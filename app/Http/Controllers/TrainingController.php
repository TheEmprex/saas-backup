<?php

namespace App\Http\Controllers;

use App\Models\TrainingModule;
use App\Models\TrainingTest;
use App\Models\UserTrainingProgress;
use App\Models\UserTestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    /**
     * Display all training modules.
     */
    public function index()
    {
        $user = Auth::user();
        
        $modules = TrainingModule::active()
            ->ordered()
            ->withCount('tests')
            ->get();

        // Get user progress for all modules
        $userProgress = UserTrainingProgress::where('user_id', $user->id)
            ->get()
            ->keyBy('training_module_id');

        // Calculate progress statistics
        $totalModulesCount = $modules->count();
        $completedModulesCount = $userProgress->where('status', 'completed')->count();
        
        // Check all requirements for talent marketplace visibility
        $requirements = [
            'email_verified' => $user->hasVerifiedEmail(),
            'kyc_completed' => $user->isKycVerified(),
            'training_completed' => $completedModulesCount === $totalModulesCount && $totalModulesCount > 0,
            'typing_test_passed' => $this->hasPassedTypingTest($user)
        ];
        
        $allRequirementsMet = array_reduce($requirements, function($carry, $requirement) {
            return $carry && $requirement;
        }, true);

        return view('training.index', compact(
            'modules', 
            'userProgress', 
            'totalModulesCount', 
            'completedModulesCount',
            'requirements',
            'allRequirementsMet'
        ));
    }

    /**
     * Show a specific training module.
     */
    public function show(TrainingModule $module)
    {
        if (!$module->is_active) {
            abort(404, 'Module not found or inactive.');
        }
        // Check if user has completed prerequisites
        if (!$this->hasCompletedPrerequisites($module)) {
            return redirect()->route('training.index')
                ->with('error', 'You need to complete prerequisite modules first.');
        }

        // Get or create user progress
        $progress = UserTrainingProgress::firstOrCreate([
            'user_id' => Auth::id(),
            'training_module_id' => $module->id,
        ]);

        // Mark as started if not already
        if ($progress->status === 'not_started') {
            $progress->markAsStarted();
        }

        // Get associated tests and user results for these tests
        $tests = $module->tests()->active()->get();
        $testResults = UserTestResult::where('user_id', Auth::id())
            ->where('testable_type', TrainingTest::class)
            ->whereIn('testable_id', $tests->pluck('id'))
            ->get()
            ->keyBy('testable_id');

        return view('training.show', compact('module', 'progress', 'tests', 'testResults'));
    }

    /**
     * Mark a module as completed.
     */
    public function complete(TrainingModule $module)
    {
        $progress = UserTrainingProgress::where([
            'user_id' => Auth::id(),
            'training_module_id' => $module->id,
        ])->first();

        if (!$progress) {
            return response()->json(['error' => 'Progress not found'], 404);
        }

        $progress->markAsCompleted();

        return response()->json([
            'success' => true,
            'message' => 'Module completed successfully!'
        ]);
    }

    /**
     * Show a training test.
     */
    public function showTest(TrainingTest $test)
    {
        // Check if user has access to this test's module
        if ($test->trainingModule && !$this->hasCompletedPrerequisites($test->trainingModule)) {
            return redirect()->route('training.index')
                ->with('error', 'You need to complete prerequisite modules first.');
        }

        return view('training.test', compact('test'));
    }

    /**
     * Submit training test results.
     */
    public function submitTest(Request $request, TrainingTest $test)
    {
        $request->validate([
            'answers' => 'required|array',
            'time_taken' => 'required|numeric|min:1'
        ]);

        // Calculate score
        $score = $test->calculateScore($request->answers);
        $passed = $test->isPassing($score);

        // Save the result
        $result = UserTestResult::create([
            'user_id' => Auth::id(),
            'testable_type' => TrainingTest::class,
            'testable_id' => $test->id,
            'score' => $score,
            'answers' => $request->answers,
            'passed' => $passed,
            'time_taken_seconds' => $request->time_taken,
            'completed_at' => now()
        ]);

        // Check if all tests for this module are now passed and auto-complete the module
        if ($passed && $test->trainingModule) {
            $this->checkAndCompleteModule($test->trainingModule, Auth::id());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'passed' => $passed,
                'score' => $score,
                'result' => $result
            ]);
        }

        $message = $passed ? 
            "Congratulations! You scored {$score}% and passed the test." : 
            "You scored {$score}% and did not pass. Try again after reviewing the material.";

        return redirect()->route('training.show', $test->trainingModule)
            ->with($passed ? 'success' : 'error', $message);
    }

    /**
     * Show user's training progress.
     */
    public function progress()
    {
        $progress = UserTrainingProgress::with(['trainingModule', 'trainingModule.tests'])
            ->where('user_id', Auth::id())
            ->get()
            ->groupBy('status');

        $testResults = UserTestResult::with('testable')
            ->where('user_id', Auth::id())
            ->whereHasMorph('testable', [TrainingTest::class])
            ->latest()
            ->paginate(10);

        return view('training.progress', compact('progress', 'testResults'));
    }

    /**
     * Check if user has completed prerequisites for a module.
     */
    private function hasCompletedPrerequisites(TrainingModule $module): bool
    {
        if (!$module->hasPrerequisites()) {
            return true;
        }

        $completedModules = UserTrainingProgress::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->pluck('training_module_id')
            ->toArray();

        $prerequisites = $module->prerequisites;
        if (!is_array($prerequisites)) {
            return true; // If prerequisites is not an array, assume no prerequisites
        }

        foreach ($prerequisites as $prerequisiteId) {
            if (!in_array($prerequisiteId, $completedModules)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if user has passed at least one typing test.
     */
    private function hasPassedTypingTest($user): bool
    {
        return $user->userTestResults()
            ->where('testable_type', 'App\Models\TypingTest')
            ->where('passed', true)
            ->exists();
    }

    /**
     * Check if all tests for a module are passed and auto-complete the module.
     */
    private function checkAndCompleteModule(TrainingModule $module, $userId): void
    {
        // Get all tests for this module
        $moduleTests = $module->tests()->active()->get();
        
        // If module has no tests, don't auto-complete
        if ($moduleTests->isEmpty()) {
            return;
        }
        
        // Check if user has passed ALL tests for this module
        $passedTestsCount = UserTestResult::where('user_id', $userId)
            ->where('testable_type', TrainingTest::class)
            ->whereIn('testable_id', $moduleTests->pluck('id'))
            ->where('passed', true)
            ->distinct('testable_id')
            ->count();
        
        // If user has passed all tests, mark module as completed
        if ($passedTestsCount === $moduleTests->count()) {
            $progress = UserTrainingProgress::where([
                'user_id' => $userId,
                'training_module_id' => $module->id,
            ])->first();
            
            if ($progress && $progress->status !== 'completed') {
                $progress->markAsCompleted();
            }
        }
    }
}

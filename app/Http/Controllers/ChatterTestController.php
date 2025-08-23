<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatterTestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get available typing test languages
        $availableLanguages = collect([
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'fr', 'name' => 'French']
        ]);
        
        // Get user's typing test results (safely)
        $typingTestResults = collect();
        $hasPassedTypingTest = false;
        
        try {
            if (class_exists('\App\Models\UserTestResult')) {
                $typingTestResults = \App\Models\UserTestResult::where('user_id', $user->id)
                    ->where('testable_type', 'App\\Models\\TypingTest')
                    ->with('testable')
                    ->latest()
                    ->take(5)
                    ->get();
                    
                $hasPassedTypingTest = $typingTestResults->where('passed', true)->isNotEmpty();
            }
        } catch (\Exception $e) {
            // Ignore model errors and use empty collections
        }
        
        // Get training modules and user progress (safely)
        $modules = collect();
        $userProgress = collect();
        $totalModulesCount = 0;
        $completedModulesCount = 0;
        $hasCompletedAllTraining = false;
        
        try {
            if (class_exists('\App\Models\TrainingModule')) {
                $modules = \App\Models\TrainingModule::where('is_active', true)
                    ->orderBy('order')
                    ->withCount('tests')
                    ->with('tests')
                    ->get();
                    
                $totalModulesCount = $modules->count();
            }
            
            if (class_exists('\App\Models\UserTrainingProgress')) {
                $userProgress = \App\Models\UserTrainingProgress::where('user_id', $user->id)
                    ->get()
                    ->keyBy('training_module_id');
                    
                $completedModulesCount = $userProgress->where('status', 'completed')->count();
                $hasCompletedAllTraining = $totalModulesCount > 0 && $completedModulesCount === $totalModulesCount;
            }
        } catch (\Exception $e) {
            // Ignore model errors and use empty collections
        }
        
        // Check overall completion status
        $allTestsCompleted = $hasPassedTypingTest && $hasCompletedAllTraining;
        
        $data = [
            'user' => $user,
            'requirements' => [
                'email_verified' => $user->hasVerifiedEmail(),
                'kyc_completed' => method_exists($user, 'isKycVerified') ? $user->isKycVerified() : false
            ],
            'availableLanguages' => $availableLanguages,
            'typingTestResults' => $typingTestResults,
            'hasPassedTypingTest' => $hasPassedTypingTest,
            'modules' => $modules,
            'userProgress' => $userProgress,
            'totalModulesCount' => $totalModulesCount,
            'completedModulesCount' => $completedModulesCount,
            'hasCompletedAllTraining' => $hasCompletedAllTraining,
            'allTestsCompleted' => $allTestsCompleted
        ];
        
        return view('chatter-tests.index', $data);
    }
    
    /**
     * Display the user's test results.
     */
    public function results()
    {
        $user = auth()->user();
        
        // Get typing test results
        $typingTestResults = collect();
        try {
            if (class_exists('\App\Models\UserTestResult')) {
                $typingTestResults = \App\Models\UserTestResult::where('user_id', $user->id)
                    ->where('testable_type', 'App\\Models\\TypingTest')
                    ->with('testable')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        } catch (\Exception $e) {
            // Ignore model errors and use empty collections
        }
        
        // Get training test results
        $trainingTestResults = collect();
        try {
            if (class_exists('\App\Models\UserTestResult')) {
                $trainingTestResults = \App\Models\UserTestResult::where('user_id', $user->id)
                    ->where('testable_type', 'App\\Models\\TrainingTest')
                    ->with(['testable', 'testable.trainingModule'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        } catch (\Exception $e) {
            // Ignore model errors and use empty collections
        }
        
        return view('chatter-tests.results', [
            'user' => $user,
            'typingTestResults' => $typingTestResults,
            'trainingTestResults' => $trainingTestResults
        ]);
    }
    
    /**
     * Unified interface for taking tests (both typing and training).
     */
    public function takeTest(Request $request)
    {
        $type = $request->get('type'); // 'typing' or 'training'
        $id = $request->get('id');
        $language = $request->get('language');
        
        if ($type === 'typing') {
            // Validate language parameter
            if (!in_array($language, ['en', 'fr'])) {
                abort(404, 'Language not supported');
            }
            
            try {
                $test = \App\Models\TypingTest::active()->forLanguage($language)->inRandomOrder()->firstOrFail();
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return redirect()->route('chatter.tests')
                    ->with('error', 'No typing tests are currently available for ' . $this->getLanguageName($language) . '. Please check back later.');
            }
            
            // Get recent result for this language
            $recentResult = null;
            if (auth()->check()) {
                try {
                    $recentResult = \App\Models\UserTestResult::where('user_id', auth()->id())
                        ->whereHasMorph('testable', [\App\Models\TypingTest::class], function ($query) use ($language) {
                            $query->where('language', $language);
                        })
                        ->latest()
                        ->first();
                } catch (\Exception $e) {
                    // Continue without recent result
                }
            }
            
            return view('chatter-tests.take-test', [
                'testType' => 'typing',
                'test' => $test,
                'recentResult' => $recentResult
            ]);
            
        } elseif ($type === 'training') {
            try {
                $test = \App\Models\TrainingTest::with('trainingModule')->findOrFail($id);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                return redirect()->route('chatter.tests')
                    ->with('error', 'Training test not found.');
            }
            
            return view('chatter-tests.take-test', [
                'testType' => 'training',
                'test' => $test
            ]);
        }
        
        return redirect()->route('chatter.tests')
            ->with('error', 'Invalid test type specified.');
    }
    
    /**
     * Get language name from code.
     */
    private function getLanguageName($code)
    {
        return match($code) {
            'en' => 'English',
            'fr' => 'French',
            default => ucfirst($code)
        };
    }
}

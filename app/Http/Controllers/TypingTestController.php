<?php

namespace App\Http\Controllers;

use App\Models\TypingTest;
use App\Models\UserTestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Exception;

class TypingTestController extends Controller
{
    /**
     * Show typing test selection page.
     */
    public function index()
    {
        try {
            $availableLanguages = TypingTest::where('is_active', true)
                ->select('language')
                ->distinct()
                ->pluck('language')
                ->filter()
                ->map(function ($lang) {
                    return [
                        'code' => $lang,
                        'name' => $this->getLanguageName($lang)
                    ];
                });

            return view('typing-tests.index', compact('availableLanguages'));
        } catch (Exception $e) {
            Log::error('TypingTest index error: ' . $e->getMessage());
            return redirect()->route('profile.show')->with('error', 'Unable to load typing tests. Please try again.');
        }
    }

    /**
     * Show typing test for a specific language.
     */
    public function show($language)
    {
        try {
            // Validate language
            if (!in_array($language, ['en', 'fr'])) {
                abort(404, 'Language not supported');
            }

            $test = null;
            try {
                // Use firstOrFail to catch the specific case of no test found
                $test = TypingTest::active()->forLanguage($language)->inRandomOrder()->firstOrFail();
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                Log::warning("No active typing test found for language: {$language}.");
                return redirect()->route('typing-tests.index')
                    ->with('error', 'No typing tests are currently available for ' . $this->getLanguageName($language) . '. Please check back later.');
            }

            $recentResult = null;
            if (Auth::check()) {
                try {
                    $recentResult = UserTestResult::where('user_id', Auth::id())
                        ->whereHasMorph('testable', [TypingTest::class], function ($query) use ($language) {
                            $query->where('language', $language);
                        })
                        ->latest()
                        ->first();
                } catch (Exception $e) {
                    Log::error("Error fetching recent test result for user " . Auth::id() . " and language {$language}. Error: " . $e->getMessage());
                    // This is not a critical error, so we can continue without the recent result.
                }
            }
            
            $language_name = $this->getLanguageName($language);

            return view('typing-tests.show', compact('test', 'language', 'recentResult', 'language_name'));

        } catch (Exception $e) {
            Log::critical('Major error in TypingTestController@show: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            return redirect()->route('profile.show')->with('error', 'A critical error occurred while loading the test. Our team has been notified.');
        }
    }

    /**
     * Submit typing test results.
     */
    public function submit(Request $request, $language)
    {
        try {
            $validated = $request->validate([
                'test_id' => 'required|exists:typing_tests,id',
                'wpm' => 'required|numeric|min:0|max:500',
                'accuracy' => 'required|numeric|min:0|max:100',
                'time_taken' => 'required|numeric|min:1',
                'user_input' => 'nullable|string'
            ]);

            $test = TypingTest::findOrFail($validated['test_id']);
            
            if ($test->language !== $language) {
                return response()->json(['error' => 'Language mismatch'], 400);
            }

            $user = Auth::user();
            if (!$user) {
                 return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Calculate if user passed
            $passed = ($validated['wpm'] >= ($test->min_wpm ?? 0)) && 
                      ($validated['accuracy'] >= ($test->min_accuracy ?? 0));

            // Save the result
            $result = UserTestResult::create([
                'user_id' => $user->id,
                'testable_type' => TypingTest::class,
                'testable_id' => $test->id,
                'wpm' => $validated['wpm'],
                'accuracy' => $validated['accuracy'],
                'passed' => $passed,
                'time_taken_seconds' => $validated['time_taken'],
                'completed_at' => now(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'passed' => $passed,
                    'result' => $result,
                    'redirect_url' => route('profile.show')
                ]);
            }

            $message = $passed ? 
                'Congratulations! You passed the typing test.' : 
                'You did not pass the typing test. Please try again.';

            return redirect()->route('profile.show')
                ->with($passed ? 'success' : 'error', $message);

        } catch (ValidationException $e) {
            Log::warning('TypingTest submission validation failed: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            Log::error('TypingTest submission error: ' . $e->getMessage() . '\n' . $e->getTraceAsString());
            if ($request->expectsJson()) {
                return response()->json(['error' => 'An unexpected error occurred while saving your result.'], 500);
            }
            return redirect()->route('profile.show')->with('error', 'An unexpected error occurred while submitting your test.');
        }
    }

    /**
     * Get typing test results for a user.
     */
    public function results(Request $request)
    {
        try {
            $results = UserTestResult::with('testable')
                ->where('user_id', Auth::id())
                ->whereHasMorph('testable', [TypingTest::class])
                ->latest()
                ->paginate(10);

            return view('typing-tests.results', compact('results'));
        } catch (Exception $e) {
            Log::error('Error fetching typing test results page: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Could not load test results.');
        }
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

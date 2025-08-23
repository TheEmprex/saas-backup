<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TrainingTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'training_module_id',
        'questions',
        'passing_score',
        'time_limit_minutes',
        'is_active'
    ];

    protected $casts = [
        'questions' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the training module that owns this test.
     */
    public function trainingModule(): BelongsTo
    {
        return $this->belongsTo(TrainingModule::class);
    }

    /**
     * Get all of the test results for the training test.
     */
    public function testResults(): MorphMany
    {
        return $this->morphMany(UserTestResult::class, 'testable');
    }

    /**
     * Scope a query to only include active tests.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the number of questions in this test.
     */
    public function getQuestionCountAttribute(): int
    {
        return count($this->questions ?? []);
    }

    /**
     * Calculate score based on correct answers.
     */
    public function calculateScore(array $userAnswers): int
    {
        $correct = 0;
        $total = count($this->questions);

        foreach ($this->questions as $index => $question) {
            $userAnswer = $userAnswers[$index] ?? null;
            $correctAnswer = $question['correct_answer'] ?? null;

            if ($userAnswer === $correctAnswer) {
                $correct++;
            }
        }

        return $total > 0 ? round(($correct / $total) * 100) : 0;
    }

    /**
     * Check if a score passes the test.
     */
    public function isPassing(int $score): bool
    {
        return $score >= $this->passing_score;
    }
}

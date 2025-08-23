<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TypingTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'language',
        'content',
        'difficulty_level',
        'time_limit_seconds',
        'min_wpm',
        'min_accuracy',
        'is_active'
    ];

    protected $casts = [
        'min_accuracy' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Get all of the test results for the typing test.
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
     * Scope a query to filter by language.
     */
    public function scopeForLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Get the word count of the content.
     */
    public function getWordCountAttribute(): int
    {
        return str_word_count(strip_tags($this->content));
    }
}

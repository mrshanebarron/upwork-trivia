<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DailyQuestion extends Model
{
    use HasFactory;
    protected $fillable = [
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'explanation',
        'prize_amount',
        'scheduled_for',
        'winner_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_for' => 'datetime',
            'is_active' => 'boolean',
            'prize_amount' => 'decimal:2',
        ];
    }

    protected $appends = ['answer_choices'];

    /**
     * Check if this is the active question
     */
    public function isActive(): bool
    {
        return $this->is_active
            && $this->scheduled_for->isPast()
            && !$this->winner()->exists();
    }

    /**
     * Get the correct answer option text
     */
    public function getCorrectAnswerText(): string
    {
        return $this->{'option_' . strtolower($this->correct_answer)};
    }

    /**
     * Get answer choices as array
     */
    protected function answerChoices(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => [
                'A' => $this->option_a,
                'B' => $this->option_b,
                'C' => $this->option_c,
                'D' => $this->option_d,
            ],
            set: fn ($value) => [
                'option_a' => $value['A'] ?? null,
                'option_b' => $value['B'] ?? null,
                'option_c' => $value['C'] ?? null,
                'option_d' => $value['D'] ?? null,
            ]
        );
    }

    /**
     * Get submission count accessor
     */
    protected function submissionCount(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->submissions()->count()
        );
    }

    /**
     * Get correct submission count accessor
     */
    protected function correctSubmissionCount(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->correctSubmissions()->count()
        );
    }

    // Relationships
    public function winnerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function winner(): HasOne
    {
        return $this->hasOne(Winner::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function correctSubmissions(): HasMany
    {
        return $this->hasMany(Submission::class)->where('is_correct', true);
    }
}

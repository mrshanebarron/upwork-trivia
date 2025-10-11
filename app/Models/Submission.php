<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasFactory;
    const UPDATED_AT = null; // No updated_at column

    protected $fillable = [
        'daily_question_id',
        'user_id',
        'selected_answer',
        'is_correct',
        'ip_address',
        'device_fingerprint',
        'latitude',
        'longitude',
        'sticker_id',
        'submitted_at',
        'random_tiebreaker',
    ];

    protected $appends = ['is_winner'];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'submitted_at' => 'datetime',
            'random_tiebreaker' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        // Auto-generate random tiebreaker on creation
        static::creating(function ($submission) {
            if (!$submission->random_tiebreaker) {
                $submission->random_tiebreaker = random_int(1, 1000000);
            }
            if (!$submission->submitted_at) {
                $submission->submitted_at = now();
            }
        });
    }

    /**
     * Determine if this submission won (check timestamps)
     */
    public function wouldWin(): bool
    {
        if (!$this->is_correct) {
            return false;
        }

        // Check if this is the earliest correct submission
        $earlierCorrect = static::where('daily_question_id', $this->daily_question_id)
            ->where('is_correct', true)
            ->where(function ($query) {
                $query->where('submitted_at', '<', $this->submitted_at)
                    ->orWhere(function ($q) {
                        $q->where('submitted_at', '=', $this->submitted_at)
                            ->where('random_tiebreaker', '<', $this->random_tiebreaker);
                    });
            })
            ->exists();

        return !$earlierCorrect;
    }

    /**
     * Get is_winner accessor - check if this submission has a winner record
     */
    public function getIsWinnerAttribute(): bool
    {
        return $this->winner()->exists();
    }

    // Relationships
    public function dailyQuestion(): BelongsTo
    {
        return $this->belongsTo(DailyQuestion::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sticker(): BelongsTo
    {
        return $this->belongsTo(Sticker::class);
    }

    public function winner()
    {
        return $this->hasOne(Winner::class);
    }
}

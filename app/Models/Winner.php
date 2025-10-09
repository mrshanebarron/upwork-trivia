<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Winner extends Model
{
    use HasFactory;
    const UPDATED_AT = null; // No updated_at column

    protected $fillable = [
        'user_id',
        'daily_question_id',
        'submission_id',
        'prize_amount',
    ];

    protected function casts(): array
    {
        return [
            'prize_amount' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dailyQuestion(): BelongsTo
    {
        return $this->belongsTo(DailyQuestion::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function giftCard(): HasOne
    {
        return $this->hasOne(GiftCard::class);
    }
}

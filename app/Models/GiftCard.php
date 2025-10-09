<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GiftCard extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'winner_id',
        'order_id',
        'reward_id',
        'amount',
        'currency',
        'status',
        'redemption_link',
        'delivery_method',
        'delivered_at',
        'redeemed_at',
        'provider',
        'provider_response',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'delivered_at' => 'datetime',
            'redeemed_at' => 'datetime',
            'provider_response' => 'array',
        ];
    }

    /**
     * Check if gift card was successfully delivered
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered' && $this->delivered_at !== null;
    }

    /**
     * Mark as delivered
     */
    public function markAsDelivered(string $redemptionLink): void
    {
        $this->update([
            'status' => 'delivered',
            'redemption_link' => $redemptionLink,
            'delivered_at' => now(),
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
        ]);
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(Winner::class);
    }

    public function deliveryLogs(): HasMany
    {
        return $this->hasMany(GiftCardDeliveryLog::class);
    }
}

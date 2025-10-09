<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrizePool extends Model
{
    protected $fillable = [
        'month',
        'budget',
        'spent',
        'sponsor_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'budget' => 'decimal:2',
            'spent' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get remaining budget
     */
    public function getRemainingAttribute(): float
    {
        return $this->budget - $this->spent;
    }

    /**
     * Check if budget is depleted
     */
    public function isDepleted(): bool
    {
        return $this->remaining <= 0;
    }

    public function transactions()
    {
        return $this->hasMany(BudgetTransaction::class);
    }
}

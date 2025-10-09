<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetTransaction extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'prize_pool_id',
        'type',
        'amount',
        'description',
        'reference_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function prizePool()
    {
        return $this->belongsTo(PrizePool::class);
    }
}

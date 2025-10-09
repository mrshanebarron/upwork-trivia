<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyAnalytic extends Model
{
    protected $fillable = [
        'date',
        'total_scans',
        'total_submissions',
        'total_winners',
        'total_spent',
        'conversion_rate',
        'avg_submissions_per_question',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'total_spent' => 'decimal:2',
            'conversion_rate' => 'decimal:2',
            'avg_submissions_per_question' => 'decimal:2',
        ];
    }
}

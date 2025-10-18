<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimpleQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'active_date',
        'display_order',
        'is_active',
        'period_type',
    ];

    protected function casts(): array
    {
        return [
            'active_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get 5 random questions for today (same 5 all day)
     */
    public static function getTodaysQuestions()
    {
        // Use today's date as seed for consistent randomization throughout the day
        $seed = (int) now()->format('Ymd');

        return self::where('is_active', true)
            ->where('period_type', 'daily')
            ->inRandomOrder($seed)
            ->limit(5)
            ->get();
    }

    /**
     * Get 5 random questions for this week (same 5 all week)
     */
    public static function getThisWeeksQuestions()
    {
        // Use week number and year as seed for consistent randomization throughout the week
        $seed = (int) (now()->format('Y') . now()->weekOfYear);

        return self::where('is_active', true)
            ->where('period_type', 'weekly')
            ->inRandomOrder($seed)
            ->limit(5)
            ->get();
    }
}

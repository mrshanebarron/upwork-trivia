<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule daily analytics processing
Schedule::job(new \App\Jobs\ProcessAnalyticsJob(now()->subDay()->toDateString()))
    ->dailyAt('00:30')
    ->name('process-daily-analytics')
    ->withoutOverlapping();

// Schedule to check for low budget alerts
Schedule::call(function () {
    $currentMonth = now()->startOfMonth()->toDateString();
    $pool = \App\Models\PrizePool::where('month', $currentMonth)->first();

    if ($pool && $pool->remaining < 100) {
        $adminEmail = config('app.admin_email', 'rick@trivia.test');
        \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)
            ->notify(new \App\Notifications\AdminAlertNotification(
                winner: null,
                alertType: 'low_budget'
            ));
    }
})->dailyAt('08:00')->name('check-budget-alerts');

<?php

namespace App\Jobs;

use App\Models\Winner;
use App\Notifications\WinnerNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWinnerNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Winner $winner
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Send email to winner
        $this->winner->user->notify(new WinnerNotification($this->winner));

        // Send admin notification to Rick
        $adminEmail = config('app.admin_email', 'rick@trivia.test');
        \Illuminate\Support\Facades\Notification::route('mail', $adminEmail)
            ->notify(new \App\Notifications\AdminAlertNotification($this->winner, 'winner'));
    }
}

<?php

namespace App\Jobs;

use App\Models\DailyAnalytic;
use App\Models\DailyQuestion;
use App\Models\Submission;
use App\Models\StickerScan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAnalyticsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $date
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $date = $this->date;

        // Get daily question for this date
        $question = DailyQuestion::whereDate('scheduled_for', $date)->first();

        if (!$question) {
            return;
        }

        // Calculate analytics
        $totalSubmissions = Submission::whereDate('submitted_at', $date)->count();
        $correctSubmissions = Submission::whereDate('submitted_at', $date)
            ->where('is_correct', true)
            ->count();
        $uniqueUsers = Submission::whereDate('submitted_at', $date)
            ->distinct('user_id')
            ->count('user_id');
        $totalScans = StickerScan::whereDate('scanned_at', $date)->count();
        $uniqueScanners = StickerScan::whereDate('scanned_at', $date)
            ->distinct('user_id')
            ->count('user_id');

        // Create or update daily analytics
        DailyAnalytic::updateOrCreate(
            ['date' => $date],
            [
                'daily_question_id' => $question->id,
                'total_submissions' => $totalSubmissions,
                'correct_submissions' => $correctSubmissions,
                'unique_users' => $uniqueUsers,
                'total_scans' => $totalScans,
                'unique_scanners' => $uniqueScanners,
                'conversion_rate' => $totalScans > 0 ? round(($totalSubmissions / $totalScans) * 100, 2) : 0,
                'accuracy_rate' => $totalSubmissions > 0 ? round(($correctSubmissions / $totalSubmissions) * 100, 2) : 0,
            ]
        );
    }
}

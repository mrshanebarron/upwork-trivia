<?php

namespace App\Jobs;

use App\Models\Winner;
use App\Services\GiftCardService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliverGiftCardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 5;

    /**
     * The maximum number of seconds the job can run.
     */
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Winner $winner
    ) {}

    /**
     * Execute the job.
     */
    public function handle(GiftCardService $giftCardService): void
    {
        $giftCardService->deliverGiftCard($this->winner);
    }

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        // Exponential backoff: 5 min, 10 min, 20 min, 40 min, 80 min
        return [300, 600, 1200, 2400, 4800];
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('Gift card delivery job failed permanently', [
            'winner_id' => $this->winner->id,
            'error' => $exception->getMessage(),
        ]);

        // Notify admin of permanent failure
        // This would trigger an admin alert email/SMS
    }
}

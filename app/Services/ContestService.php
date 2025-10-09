<?php

namespace App\Services;

use App\Models\DailyQuestion;
use App\Models\Submission;
use App\Models\Winner;
use App\Models\User;
use App\Models\PrizePool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContestService
{
    public function __construct(
        protected GiftCardService $giftCardService,
        protected AntiCheatService $antiCheatService,
        protected CacheService $cacheService
    ) {}

    /**
     * Get the currently active question (cached)
     */
    public function getActiveQuestion(): ?DailyQuestion
    {
        return $this->cacheService->getActiveQuestion();
    }

    /**
     * Submit an answer to the active question
     */
    public function submitAnswer(
        User $user,
        DailyQuestion $question,
        string $answer,
        ?int $stickerId = null,
        ?array $geolocation = null,
        ?string $deviceFingerprint = null
    ): array {
        // Anti-cheat validation
        $antiCheatCheck = $this->antiCheatService->validateSubmission($user, $question, request()->ip(), $deviceFingerprint);

        if (!$antiCheatCheck['allowed']) {
            return [
                'success' => false,
                'error' => $antiCheatCheck['reason'],
                'code' => $antiCheatCheck['code']
            ];
        }

        // Check if user can win (30-day cooldown)
        if (!$user->canWin()) {
            return [
                'success' => false,
                'error' => 'You won within the last 30 days. You can participate again after ' . $user->last_won_at->addDays(30)->format('M j, Y'),
                'code' => 'COOLDOWN_ACTIVE'
            ];
        }

        // Check if question is still active
        if ($question->winner_id) {
            return [
                'success' => false,
                'error' => 'This question already has a winner.',
                'code' => 'ALREADY_WON'
            ];
        }

        // Use transaction for atomicity
        return DB::transaction(function () use ($user, $question, $answer, $stickerId, $geolocation, $deviceFingerprint) {
            // Create submission
            $submission = Submission::create([
                'daily_question_id' => $question->id,
                'user_id' => $user->id,
                'selected_answer' => strtoupper($answer),
                'is_correct' => strtoupper($answer) === $question->correct_answer,
                'ip_address' => request()->ip(),
                'device_fingerprint' => $deviceFingerprint,
                'latitude' => $geolocation['latitude'] ?? null,
                'longitude' => $geolocation['longitude'] ?? null,
                'sticker_id' => $stickerId,
                'submitted_at' => now(),
                'random_tiebreaker' => random_int(1, 1000000),
            ]);

            // Increment submission count
            $question->increment('submission_count');
            if ($submission->is_correct) {
                $question->increment('correct_submission_count');
            }

            $result = [
                'success' => true,
                'is_correct' => $submission->is_correct,
                'submission_id' => $submission->id,
            ];

            // Check if this is a winning submission
            if ($submission->is_correct && $submission->isWinner()) {
                $winner = $this->processWinner($submission);
                $result['is_winner'] = true;
                $result['winner_id'] = $winner->id;
                $result['prize_amount'] = $winner->prize_amount;
            } else {
                $result['is_winner'] = false;
                if ($submission->is_correct) {
                    $result['message'] = 'Correct answer, but someone answered first!';
                } else {
                    $result['message'] = 'Incorrect answer. Better luck next time!';
                    $result['correct_answer'] = $question->correct_answer;
                }
            }

            return $result;
        });
    }

    /**
     * Process a winning submission
     */
    protected function processWinner(Submission $submission): Winner
    {
        $question = $submission->dailyQuestion;
        $user = $submission->user;

        // Create winner record
        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'submission_id' => $submission->id,
            'prize_amount' => $question->prize_amount,
        ]);

        // Update question with winner
        $question->update(['winner_id' => $user->id]);

        // Update user
        $user->increment('total_winnings', $question->prize_amount);
        $user->update(['last_won_at' => now()]);

        // Clear caches
        $this->cacheService->clearActiveQuestion();
        $this->cacheService->clearUserEligibility($user);
        $this->cacheService->clearUserDashboard($user);
        $this->cacheService->clearQuestionStats($question);

        // Update prize pool
        $this->deductFromPrizePool($question->prize_amount);

        // Trigger gift card delivery (async)
        \App\Jobs\DeliverGiftCardJob::dispatch($winner)->afterResponse();

        // Send winner notification
        \App\Jobs\SendWinnerNotificationJob::dispatch($winner)->afterResponse();

        // Log the win
        Log::info('Contest Winner', [
            'user_id' => $user->id,
            'question_id' => $question->id,
            'submission_id' => $submission->id,
            'prize_amount' => $question->prize_amount,
        ]);

        return $winner;
    }

    /**
     * Deduct prize from current month's prize pool
     */
    protected function deductFromPrizePool(float $amount): void
    {
        $currentMonth = now()->startOfMonth()->toDateString();

        $pool = PrizePool::firstOrCreate(
            ['month' => $currentMonth],
            ['budget' => 0, 'spent' => 0, 'is_active' => true]
        );

        $pool->increment('spent', $amount);

        // Check if budget is depleted
        if ($pool->isDepleted()) {
            Log::warning('Prize pool depleted', ['month' => $currentMonth]);
        }
    }

    /**
     * Get contest statistics (cached)
     */
    public function getStatistics(DailyQuestion $question): array
    {
        return $this->cacheService->getQuestionStats($question);
    }
}

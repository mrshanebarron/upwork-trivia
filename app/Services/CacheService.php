<?php

namespace App\Services;

use App\Models\DailyQuestion;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache duration constants
     */
    const ACTIVE_QUESTION_TTL = 300; // 5 minutes
    const USER_ELIGIBILITY_TTL = 1800; // 30 minutes
    const STATS_TTL = 600; // 10 minutes

    /**
     * Get or cache the active question
     */
    public function getActiveQuestion(): ?DailyQuestion
    {
        return Cache::remember('active_question', self::ACTIVE_QUESTION_TTL, function () {
            return DailyQuestion::where('is_active', true)
                ->where('scheduled_for', '<=', now())
                ->whereNull('winner_id')
                ->first();
        });
    }

    /**
     * Clear active question cache (when winner is selected)
     */
    public function clearActiveQuestion(): void
    {
        Cache::forget('active_question');
    }

    /**
     * Get or cache user eligibility to win
     */
    public function getUserEligibility(User $user): bool
    {
        return Cache::remember("user_eligibility:{$user->id}", self::USER_ELIGIBILITY_TTL, function () use ($user) {
            return $user->canWin();
        });
    }

    /**
     * Clear user eligibility cache (when user wins)
     */
    public function clearUserEligibility(User $user): void
    {
        Cache::forget("user_eligibility:{$user->id}");
    }

    /**
     * Get or cache question statistics
     */
    public function getQuestionStats(DailyQuestion $question): array
    {
        return Cache::remember("question_stats:{$question->id}", self::STATS_TTL, function () use ($question) {
            $totalSubmissions = $question->submission_count;
            $correctSubmissions = $question->correct_submission_count;

            return [
                'total_submissions' => $totalSubmissions,
                'correct_submissions' => $correctSubmissions,
                'incorrect_submissions' => $totalSubmissions - $correctSubmissions,
                'accuracy_rate' => $totalSubmissions > 0
                    ? round(($correctSubmissions / $totalSubmissions) * 100, 2)
                    : 0,
                'has_winner' => $question->winner_id !== null,
            ];
        });
    }

    /**
     * Clear question statistics cache
     */
    public function clearQuestionStats(DailyQuestion $question): void
    {
        Cache::forget("question_stats:{$question->id}");
    }

    /**
     * Get or cache user submission check for a question
     */
    public function hasUserSubmitted(User $user, DailyQuestion $question): bool
    {
        return Cache::remember(
            "user_submitted:{$user->id}:{$question->id}",
            self::USER_ELIGIBILITY_TTL,
            function () use ($user, $question) {
                return $user->submissions()
                    ->where('daily_question_id', $question->id)
                    ->exists();
            }
        );
    }

    /**
     * Clear user submission cache
     */
    public function clearUserSubmission(User $user, DailyQuestion $question): void
    {
        Cache::forget("user_submitted:{$user->id}:{$question->id}");
    }

    /**
     * Get or cache IP submission check for a question
     */
    public function hasIpSubmitted(string $ipAddress, DailyQuestion $question): bool
    {
        return Cache::remember(
            "ip_submitted:{$ipAddress}:{$question->id}",
            self::USER_ELIGIBILITY_TTL,
            function () use ($ipAddress, $question) {
                return \App\Models\Submission::where('ip_address', $ipAddress)
                    ->where('daily_question_id', $question->id)
                    ->exists();
            }
        );
    }

    /**
     * Clear IP submission cache
     */
    public function clearIpSubmission(string $ipAddress, DailyQuestion $question): void
    {
        Cache::forget("ip_submitted:{$ipAddress}:{$question->id}");
    }

    /**
     * Get or cache user dashboard stats
     */
    public function getUserDashboardStats(User $user): array
    {
        return Cache::remember("user_dashboard:{$user->id}", self::STATS_TTL, function () use ($user) {
            return [
                'total_winnings' => $user->total_winnings,
                'total_wins' => $user->winners()->count(),
                'total_submissions' => $user->submissions()->count(),
                'correct_submissions' => $user->submissions()->where('is_correct', true)->count(),
                'can_win' => $user->canWin(),
                'last_won_at' => $user->last_won_at,
            ];
        });
    }

    /**
     * Clear user dashboard cache
     */
    public function clearUserDashboard(User $user): void
    {
        Cache::forget("user_dashboard:{$user->id}");
    }

    /**
     * Warm up critical caches (run on deploy)
     */
    public function warmUpCaches(): void
    {
        // Warm up active question
        $this->getActiveQuestion();

        // Warm up recently active users
        $recentUsers = User::whereNotNull('last_login_at')
            ->where('last_login_at', '>', now()->subHours(24))
            ->limit(100)
            ->get();

        foreach ($recentUsers as $user) {
            $this->getUserEligibility($user);
        }
    }

    /**
     * Clear all contest-related caches
     */
    public function clearAllContestCaches(): void
    {
        Cache::tags(['contest'])->flush();
    }
}

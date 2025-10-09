<?php

namespace App\Services;

use App\Models\DailyQuestion;
use App\Models\Submission;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class AntiCheatService
{
    /**
     * Validate if a submission is allowed
     */
    public function validateSubmission(
        User $user,
        DailyQuestion $question,
        string $ipAddress,
        ?string $deviceFingerprint = null
    ): array {
        // Check if user already submitted for this question
        if ($this->hasUserSubmitted($user, $question)) {
            return [
                'allowed' => false,
                'reason' => 'You have already submitted an answer for this question.',
                'code' => 'ALREADY_SUBMITTED'
            ];
        }

        // Check IP rate limiting
        $maxIpSubmissions = Setting::getValue('max_daily_submissions_per_ip', 3);
        if ($this->hasIpExceededLimit($ipAddress, $question, $maxIpSubmissions)) {
            return [
                'allowed' => false,
                'reason' => "Maximum $maxIpSubmissions submissions per IP address reached for today.",
                'code' => 'IP_LIMIT_EXCEEDED'
            ];
        }

        // Check device fingerprint if provided
        if ($deviceFingerprint && $this->hasDeviceSubmitted($deviceFingerprint, $question)) {
            return [
                'allowed' => false,
                'reason' => 'This device has already submitted an answer for this question.',
                'code' => 'DEVICE_ALREADY_SUBMITTED'
            ];
        }

        // Check if user is of age
        if (!$user->isOfAge()) {
            return [
                'allowed' => false,
                'reason' => 'You must be 18 or older to participate.',
                'code' => 'AGE_RESTRICTED'
            ];
        }

        return ['allowed' => true];
    }

    /**
     * Check if user has already submitted for this question
     */
    protected function hasUserSubmitted(User $user, DailyQuestion $question): bool
    {
        return Submission::where('user_id', $user->id)
            ->where('daily_question_id', $question->id)
            ->exists();
    }

    /**
     * Check if IP has exceeded submission limit
     */
    protected function hasIpExceededLimit(string $ipAddress, DailyQuestion $question, int $maxSubmissions): bool
    {
        $count = Submission::where('ip_address', $ipAddress)
            ->where('daily_question_id', $question->id)
            ->count();

        return $count >= $maxSubmissions;
    }

    /**
     * Check if device has already submitted
     */
    protected function hasDeviceSubmitted(string $deviceFingerprint, DailyQuestion $question): bool
    {
        return Submission::where('device_fingerprint', $deviceFingerprint)
            ->where('daily_question_id', $question->id)
            ->exists();
    }

    /**
     * Log suspicious activity
     */
    public function logSuspiciousActivity(User $user, string $reason, array $context = []): void
    {
        \Log::warning('Suspicious activity detected', [
            'user_id' => $user->id,
            'reason' => $reason,
            'context' => $context,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Rate limit check using cache
     */
    public function checkRateLimit(string $key, int $maxAttempts, int $decayMinutes): bool
    {
        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            return false;
        }

        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));

        return true;
    }
}

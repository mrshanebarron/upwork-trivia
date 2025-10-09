<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Skip validation if reCAPTCHA is disabled (development mode)
        if (!config('recaptcha.enabled')) {
            return;
        }

        // Check if secret key is configured
        if (empty(config('recaptcha.secret_key'))) {
            Log::warning('reCAPTCHA secret key not configured');
            return;
        }

        try {
            $response = Http::asForm()->post(config('recaptcha.verify_url'), [
                'secret' => config('recaptcha.secret_key'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            if (!$response->successful()) {
                Log::error('reCAPTCHA API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                $fail('Unable to verify reCAPTCHA. Please try again.');
                return;
            }

            $result = $response->json();

            // Check if verification was successful
            if (!isset($result['success']) || $result['success'] !== true) {
                Log::warning('reCAPTCHA verification failed', [
                    'error-codes' => $result['error-codes'] ?? [],
                ]);
                $fail('reCAPTCHA verification failed. Please try again.');
                return;
            }

            // Check score threshold (v3 specific)
            if (isset($result['score'])) {
                $threshold = config('recaptcha.threshold', 0.5);

                if ($result['score'] < $threshold) {
                    Log::warning('reCAPTCHA score below threshold', [
                        'score' => $result['score'],
                        'threshold' => $threshold,
                        'action' => $result['action'] ?? 'unknown',
                    ]);
                    $fail('Your submission appears suspicious. Please try again.');
                    return;
                }
            }

            // Check action matches (optional but recommended)
            if (isset($result['action']) && $result['action'] !== 'contest_submit') {
                Log::warning('reCAPTCHA action mismatch', [
                    'expected' => 'contest_submit',
                    'received' => $result['action'],
                ]);
            }

        } catch (\Exception $e) {
            Log::error('reCAPTCHA validation exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // In production, you might want to fail the validation
            // For now, we'll log and pass to avoid blocking legitimate users
            // $fail('Unable to verify reCAPTCHA. Please try again.');
        }
    }
}

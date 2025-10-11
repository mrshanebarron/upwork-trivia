<?php

namespace App\Http\Controllers;

use App\Models\DailyQuestion;
use App\Models\Sticker;
use App\Services\ContestService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContestController extends Controller
{
    public function __construct(
        protected ContestService $contestService
    ) {}

    /**
     * Show the daily Golden Question contest page
     */
    public function show(?string $code = null): Response
    {
        $question = $this->contestService->getActiveQuestion();
        $sticker = null;

        if ($code) {
            $sticker = Sticker::where('unique_code', $code)->first();
        }

        // Check user eligibility if authenticated
        $canSubmit = false;
        $alreadySubmitted = false;
        $isAuthenticated = auth()->check();

        if ($isAuthenticated && $question) {
            $canSubmit = auth()->user()->canWin();
            $alreadySubmitted = auth()->user()->submissions()
                ->where('daily_question_id', $question->id)
                ->exists();
        }

        return Inertia::render('Contest/GoldenQuestion', [
            'question' => $question ? [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'option_a' => $question->option_a,
                'option_b' => $question->option_b,
                'option_c' => $question->option_c,
                'option_d' => $question->option_d,
                'prize_amount' => $question->prize_amount,
                'has_winner' => $question->winner_id !== null,
            ] : null,
            'sticker' => $sticker ? [
                'id' => $sticker->id,
                'location_name' => $sticker->location_name,
                'property_name' => $sticker->property_name,
            ] : null,
            'is_authenticated' => $isAuthenticated,
            'can_submit' => $canSubmit,
            'already_submitted' => $alreadySubmitted,
        ]);
    }

    /**
     * Submit an answer to the daily question (public - no auth required)
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:daily_questions,id',
            'answer' => 'required|in:A,B,C,D',
            'sticker_id' => 'nullable|exists:stickers,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'device_fingerprint' => 'nullable|string',
            'recaptcha_token' => ['nullable', new \App\Rules\RecaptchaRule()],
            // Honeypot fields - must be empty
            'website' => 'nullable|max:0',
            'email_hp' => 'nullable|max:0',
            'subscribe' => 'nullable|accepted',
        ]);

        // Honeypot check - if any honeypot field is filled, it's a bot
        if (!empty($validated['website']) || !empty($validated['email_hp']) || !empty($validated['subscribe'])) {
            \Log::warning('Honeypot triggered', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'question_id' => $validated['question_id'],
            ]);

            return back()->with('error', 'Invalid submission. Please try again.');
        }

        $question = DailyQuestion::findOrFail($validated['question_id']);

        $result = $this->contestService->submitAnswer(
            user: auth()->user(), // Can be null for guests
            question: $question,
            answer: $validated['answer'],
            stickerId: $validated['sticker_id'] ?? null,
            geolocation: isset($validated['latitude']) && isset($validated['longitude']) ? [
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ] : null,
            deviceFingerprint: $validated['device_fingerprint'] ?? null
        );

        if (!$result['success']) {
            return back()->with('error', $result['error']);
        }

        if ($result['is_winner']) {
            // Store submission ID in session and redirect to register/login
            session(['pending_win_submission' => $result['submission_id']]);

            if (!auth()->check()) {
                return redirect()->route('register')
                    ->with('success', "🎉 Congratulations! You got it right and you're the first! Register to claim your ${$result['prize_amount']} prize!");
            }

            // Already authenticated, go to claim page
            return redirect()->route('contest.claim', ['submission' => $result['submission_id']]);
        }

        return back()->with('info', $result['message']);
    }

    /**
     * Claim prize for a winning submission (requires auth)
     */
    public function claim(Request $request)
    {
        $submission = Submission::with(['dailyQuestion', 'winner', 'winner.giftCard'])
            ->findOrFail($request->route('submission'));

        // Verify this submission is a winner
        if (!$submission->is_correct || !$submission->wouldWin()) {
            abort(404);
        }

        return Inertia::render('Contest/Winner', [
            'submission' => $submission,
            'winner' => $submission->winner,
            'gift_card' => $submission->winner?->giftCard,
        ]);
    }

    /**
     * Show winner announcement page
     */
    public function winner(Request $request)
    {
        $submission = auth()->user()->submissions()
            ->with(['dailyQuestion', 'winner', 'winner.giftCard'])
            ->findOrFail($request->route('submission'));

        if (!$submission->is_winner()) {
            abort(404);
        }

        return Inertia::render('Contest/Winner', [
            'submission' => $submission,
            'winner' => $submission->winner,
            'gift_card' => $submission->winner->giftCard,
        ]);
    }

    /**
     * Show contest results page
     */
    public function results(DailyQuestion $question): Response
    {
        $totalSubmissions = $question->submissions()->count();
        $correctSubmissions = $question->submissions()->where('is_correct', true)->count();

        return Inertia::render('Contest/Results', [
            'question' => [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'option_a' => $question->option_a,
                'option_b' => $question->option_b,
                'option_c' => $question->option_c,
                'option_d' => $question->option_d,
                'correct_answer' => $question->correct_answer,
                'explanation' => $question->explanation,
            ],
            'user_submission' => auth()->check() ? auth()->user()->submissions()
                ->where('daily_question_id', $question->id)
                ->first() : null,
            'winner' => $question->winner()->with('user')->first(),
            'stats' => [
                'total_submissions' => $totalSubmissions,
                'correct_submissions' => $correctSubmissions,
                'accuracy' => $totalSubmissions > 0 ? round(($correctSubmissions / $totalSubmissions) * 100, 1) : 0,
            ],
        ]);
    }
}

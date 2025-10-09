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
            'can_submit' => auth()->check() && auth()->user()->canWin(),
            'already_submitted' => auth()->check() && auth()->user()->submissions()
                ->where('daily_question_id', $question?->id)
                ->exists(),
        ]);
    }

    /**
     * Submit an answer to the daily question
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
        ]);

        $question = DailyQuestion::findOrFail($validated['question_id']);

        $result = $this->contestService->submitAnswer(
            user: auth()->user(),
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
            return redirect()->route('contest.winner', ['submission' => $result['submission_id']])
                ->with('success', "🎉 Congratulations! You won ${$result['prize_amount']}!");
        }

        return back()->with('info', $result['message']);
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

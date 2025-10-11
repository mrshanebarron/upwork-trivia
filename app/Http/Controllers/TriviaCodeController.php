<?php

namespace App\Http\Controllers;

use App\Models\AdBox;
use App\Models\DailyQuestion;
use App\Models\TriviaCode;
use App\Services\ContestService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TriviaCodeController extends Controller
{
    public function __construct(
        protected ContestService $contestService
    ) {}

    /**
     * Show bag trivia page with Golden Question at top
     */
    public function show(Request $request): Response
    {
        $code = $request->query('code');

        if (!$code) {
            abort(404, 'Trivia code not provided');
        }

        // Find the trivia code with answers
        $triviaCode = TriviaCode::where('code', $code)
            ->where('is_active', true)
            ->with('answers')
            ->first();

        if (!$triviaCode) {
            abort(404, 'Trivia code not found or inactive');
        }

        // Track the view
        $triviaCode->views()->create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Get the active Golden Question
        $question = $this->contestService->getActiveQuestion();

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

        // Load active advertisement boxes
        $adBoxes = AdBox::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(fn($ad) => [
                'id' => $ad->id,
                'title' => $ad->title,
                'url' => $ad->url,
                'html_content' => $ad->html_content,
            ]);

        return Inertia::render('Trivia/BagTrivia', [
            'trivia_code' => [
                'code' => $triviaCode->code,
                'title' => $triviaCode->title,
                'description' => $triviaCode->description,
                'answers' => $triviaCode->answers->map(fn($answer) => [
                    'id' => $answer->id,
                    'text' => $answer->text,
                    'order' => $answer->order,
                ]),
            ],
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
            'is_authenticated' => $isAuthenticated,
            'can_submit' => $canSubmit,
            'already_submitted' => $alreadySubmitted,
            'ad_boxes' => $adBoxes,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\SimpleQuestion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SimpleQuestionController extends Controller
{
    /**
     * Show today's simple questions
     */
    public function index(Request $request)
    {
        // Get period type from query param, default to 'daily'
        $periodType = $request->query('period', 'daily');

        // Get questions based on period
        $questions = $periodType === 'weekly'
            ? SimpleQuestion::getThisWeeksQuestions()
            : SimpleQuestion::getTodaysQuestions();

        return Inertia::render('Simple/Index', [
            'questions' => $questions->map(fn($q) => [
                'id' => $q->id,
                'question' => $q->question,
                'answer' => $q->answer,
                'display_order' => $q->display_order,
            ]),
            'periodType' => $periodType,
            'date' => $periodType === 'weekly'
                ? now()->format('F j, Y') . ' - ' . now()->endOfWeek()->format('F j, Y')
                : now()->format('F j, Y'),
        ]);
    }
}

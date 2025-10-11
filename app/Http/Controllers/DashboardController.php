<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show user dashboard
     */
    public function index(): Response
    {
        $user = auth()->user();

        return Inertia::render('Dashboard', [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'total_winnings' => $user->total_winnings,
                'last_won_at' => $user->last_won_at?->format('M j, Y'),
                'can_win' => $user->canWin(),
                'next_eligible_date' => $user->last_won_at?->addDays(30)->format('M j, Y'),
            ],
            'winners' => $user->winners()
                ->with(['dailyQuestion', 'giftCard'])
                ->latest()
                ->paginate(10),
            'recent_submissions' => $user->submissions()
                ->with('dailyQuestion')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }

    /**
     * Show winnings history
     */
    public function winnings(): Response
    {
        $user = auth()->user();

        return Inertia::render('Dashboard/Winnings', [
            'winners' => $user->winners()
                ->with(['dailyQuestion', 'giftCard'])
                ->latest()
                ->paginate(20),
            'total_winnings' => $user->total_winnings,
            'total_wins' => $user->winners()->count(),
        ]);
    }

    /**
     * Show gift cards
     */
    public function giftCards(): Response
    {
        $user = auth()->user();

        return Inertia::render('Dashboard/GiftCards', [
            'gift_cards' => $user->giftCards()
                ->with('winner.dailyQuestion')
                ->latest()
                ->paginate(20),
        ]);
    }

    /**
     * Show submission history
     */
    public function submissions(): Response
    {
        $user = auth()->user();

        $totalSubmissions = $user->submissions()->count();
        $correctSubmissions = $user->submissions()->where('is_correct', true)->count();

        return Inertia::render('Dashboard/Submissions', [
            'submissions' => $user->submissions()
                ->with(['dailyQuestion', 'sticker'])
                ->latest()
                ->paginate(20),
            'stats' => [
                'total' => $totalSubmissions,
                'correct' => $correctSubmissions,
                'incorrect' => $totalSubmissions - $correctSubmissions,
                'accuracy' => $totalSubmissions > 0 ? (float) round(($correctSubmissions / $totalSubmissions) * 100, 1) : 0.0,
            ],
        ]);
    }
}

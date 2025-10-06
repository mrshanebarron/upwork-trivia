<?php

namespace App\Http\Controllers;

use App\Models\AdBox;
use App\Models\TriviaCode;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $adBoxes = AdBox::where('is_active', true)
            ->inRandomOrder()
            ->limit(2)
            ->get();

        return view('home', compact('adBoxes'));
    }

    public function lookup(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:4'
        ]);

        $triviaCode = TriviaCode::where('code', $request->code)
            ->where('is_active', true)
            ->with('answers')
            ->first();

        if (!$triviaCode) {
            return response()->json([
                'found' => false,
                'message' => 'Code not found'
            ]);
        }

        // Track view
        $triviaCode->views()->create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'found' => true,
            'title' => $triviaCode->title,
            'description' => $triviaCode->description,
            'answers' => $triviaCode->answers->sortBy('order')->map(fn($a) => $a->answer)->values()
        ]);
    }

    public function trackAdClick(AdBox $adBox)
    {
        $adBox->incrementClicks();
        return response()->json(['success' => true]);
    }
}

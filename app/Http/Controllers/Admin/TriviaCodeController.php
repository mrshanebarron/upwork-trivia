<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TriviaCode;
use Illuminate\Http\Request;

class TriviaCodeController extends Controller
{
    public function index()
    {
        $codes = TriviaCode::withCount('answers')->latest()->paginate(15);
        return view('admin.trivia-codes.index', compact('codes'));
    }

    public function create()
    {
        return view('admin.trivia-codes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:4|unique:trivia_codes',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'nullable|boolean',
            'answers' => 'required|array|min:1',
            'answers.*' => 'required|string|max:1000'
        ]);

        $triviaCode = TriviaCode::create([
            'code' => $validated['code'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('active') && $request->active == '1',
        ]);

        foreach ($validated['answers'] as $index => $answerText) {
            $triviaCode->answers()->create([
                'answer' => $answerText,
                'order' => $index + 1
            ]);
        }

        return redirect()->route('admin.trivia-codes.index')
            ->with('success', 'Trivia code created successfully');
    }

    public function edit(TriviaCode $triviaCode)
    {
        $triviaCode->load('answers');
        return view('admin.trivia-codes.edit', compact('triviaCode'));
    }

    public function update(Request $request, TriviaCode $triviaCode)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:4|unique:trivia_codes,code,' . $triviaCode->id,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'nullable|boolean',
            'answers' => 'required|array|min:1',
            'answers.*' => 'required|string|max:1000'
        ]);

        $triviaCode->update([
            'code' => $validated['code'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('active') && $request->active == '1',
        ]);

        $triviaCode->answers()->delete();

        foreach ($validated['answers'] as $index => $answerText) {
            $triviaCode->answers()->create([
                'answer' => $answerText,
                'order' => $index + 1
            ]);
        }

        return redirect()->route('admin.trivia-codes.index')
            ->with('success', 'Trivia code updated successfully');
    }

    public function destroy(TriviaCode $triviaCode)
    {
        $triviaCode->delete();
        return redirect()->route('admin.trivia-codes.index')
            ->with('success', 'Trivia code deleted successfully');
    }
}

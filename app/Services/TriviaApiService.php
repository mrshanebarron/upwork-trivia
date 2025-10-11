<?php

namespace App\Services;

use App\Models\DailyQuestion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TriviaApiService
{
    protected string $baseUrl = 'https://the-trivia-api.com/v2/questions';

    /**
     * Available categories from The Trivia API
     */
    public function getCategories(): array
    {
        return [
            'arts_and_literature' => 'Arts & Literature',
            'film_and_tv' => 'Film & TV',
            'food_and_drink' => 'Food & Drink',
            'general_knowledge' => 'General Knowledge',
            'geography' => 'Geography',
            'history' => 'History',
            'music' => 'Music',
            'science' => 'Science',
            'society_and_culture' => 'Society & Culture',
            'sport_and_leisure' => 'Sport & Leisure',
        ];
    }

    /**
     * Available difficulty levels
     */
    public function getDifficulties(): array
    {
        return [
            'easy' => 'Easy',
            'medium' => 'Medium',
            'hard' => 'Hard',
        ];
    }

    /**
     * Fetch questions from The Trivia API
     */
    public function fetchQuestions(
        int $amount = 10,
        ?string $category = null,
        ?string $difficulty = null
    ): array {
        try {
            $params = [
                'limit' => min($amount, 50), // API max is 50
            ];

            if ($category) {
                $params['categories'] = $category;
            }

            if ($difficulty) {
                $params['difficulties'] = $difficulty;
            }

            $response = Http::timeout(10)
                ->get($this->baseUrl, $params);

            if (!$response->successful()) {
                Log::error('Trivia API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Trivia API exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [];
        }
    }

    /**
     * Import questions and auto-schedule them
     */
    public function importQuestions(
        int $amount,
        ?string $category = null,
        ?string $difficulty = null,
        ?\Carbon\Carbon $startDate = null
    ): array {
        $questions = $this->fetchQuestions($amount, $category, $difficulty);

        if (empty($questions)) {
            return [
                'success' => false,
                'message' => 'Failed to fetch questions from API',
                'imported' => 0,
            ];
        }

        $startDate = $startDate ?? now()->addDay();
        $imported = 0;
        $errors = [];

        foreach ($questions as $index => $questionData) {
            try {
                // Schedule at random time on that day
                $scheduledTime = $startDate->copy()
                    ->addDays($index)
                    ->setHour(rand(8, 20))  // Between 8 AM and 8 PM
                    ->setMinute(rand(0, 59))
                    ->setSecond(0);

                // Combine correct answer with incorrect answers and shuffle
                $allAnswers = array_merge(
                    [$questionData['correctAnswer']],
                    $questionData['incorrectAnswers']
                );
                shuffle($allAnswers);

                // Map to A, B, C, D
                $answerMap = [];
                $correctAnswerLetter = null;
                $letters = ['A', 'B', 'C', 'D'];

                foreach ($allAnswers as $i => $answer) {
                    $letter = $letters[$i] ?? null;
                    if (!$letter) continue;

                    $answerMap[$letter] = $answer;

                    if ($answer === $questionData['correctAnswer']) {
                        $correctAnswerLetter = $letter;
                    }
                }

                DailyQuestion::create([
                    'question_text' => $questionData['question']['text'],
                    'option_a' => $answerMap['A'] ?? '',
                    'option_b' => $answerMap['B'] ?? '',
                    'option_c' => $answerMap['C'] ?? '',
                    'option_d' => $answerMap['D'] ?? '',
                    'correct_answer' => $correctAnswerLetter,
                    'explanation' => $this->generateExplanation($questionData),
                    'category' => $questionData['category'] ?? 'general',
                    'difficulty' => $questionData['difficulty'] ?? 'medium',
                    'prize_amount' => 10.00,
                    'scheduled_for' => $scheduledTime,
                    'is_active' => false, // Will be activated by scheduler
                    'source' => 'trivia_api',
                    'external_id' => $questionData['id'] ?? null,
                ]);

                $imported++;
            } catch (\Exception $e) {
                Log::error('Failed to import question', [
                    'question' => $questionData,
                    'error' => $e->getMessage(),
                ]);
                $errors[] = $e->getMessage();
            }
        }

        return [
            'success' => $imported > 0,
            'message' => "Successfully imported {$imported} out of " . count($questions) . " questions",
            'imported' => $imported,
            'errors' => $errors,
        ];
    }

    /**
     * Generate a simple explanation
     */
    protected function generateExplanation(array $questionData): string
    {
        $answer = $questionData['correctAnswer'];
        $category = ucfirst(str_replace('_', ' ', $questionData['category'] ?? 'trivia'));

        return "The correct answer is: {$answer}. This {$category} question was sourced from The Trivia API.";
    }
}

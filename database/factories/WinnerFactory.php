<?php

namespace Database\Factories;

use App\Models\DailyQuestion;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Winner>
 */
class WinnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $question = DailyQuestion::factory()->create();
        $user = User::factory()->create();

        // Create a correct submission for this winner
        $submission = Submission::factory()->create([
            'daily_question_id' => $question->id,
            'user_id' => $user->id,
            'selected_answer' => $question->correct_answer,
            'is_correct' => true,
        ]);

        return [
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'submission_id' => $submission->id,
            'prize_amount' => $question->prize_amount,
        ];
    }

    /**
     * Create a winner without a submission (for testing edge cases)
     */
    public function withoutSubmission(): static
    {
        return $this->state(fn (array $attributes) => [
            'submission_id' => null,
        ]);
    }
}

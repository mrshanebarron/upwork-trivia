<?php

namespace Database\Factories;

use App\Models\DailyQuestion;
use App\Models\Sticker;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
class SubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $question = DailyQuestion::factory()->create();
        $answers = ['A', 'B', 'C', 'D'];
        $selectedAnswer = $this->faker->randomElement($answers);

        return [
            'daily_question_id' => $question->id,
            'user_id' => User::factory(),
            'selected_answer' => $selectedAnswer,
            'is_correct' => $selectedAnswer === $question->correct_answer,
            'ip_address' => $this->faker->ipv4(),
            'device_fingerprint' => $this->faker->uuid(),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'sticker_id' => Sticker::factory(),
            'submitted_at' => now(),
            'random_tiebreaker' => $this->faker->numberBetween(1, 1000000),
        ];
    }

    /**
     * Indicate that the submission is correct
     */
    public function correct(): static
    {
        return $this->state(function (array $attributes) {
            $question = DailyQuestion::find($attributes['daily_question_id']);
            return [
                'selected_answer' => $question->correct_answer,
                'is_correct' => true,
            ];
        });
    }

    /**
     * Indicate that the submission is incorrect
     */
    public function incorrect(): static
    {
        return $this->state(function (array $attributes) {
            $question = DailyQuestion::find($attributes['daily_question_id']);
            $wrongAnswers = array_diff(['A', 'B', 'C', 'D'], [$question->correct_answer]);
            return [
                'selected_answer' => $this->faker->randomElement($wrongAnswers),
                'is_correct' => false,
            ];
        });
    }
}

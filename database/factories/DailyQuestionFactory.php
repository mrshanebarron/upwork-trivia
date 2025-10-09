<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyQuestion>
 */
class DailyQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $correctAnswer = $this->faker->randomElement(['A', 'B', 'C', 'D']);

        return [
            'question_text' => $this->faker->sentence() . '?',
            'option_a' => $this->faker->sentence(3),
            'option_b' => $this->faker->sentence(3),
            'option_c' => $this->faker->sentence(3),
            'option_d' => $this->faker->sentence(3),
            'correct_answer' => $correctAnswer,
            'explanation' => 'Did you know? ' . $this->faker->sentence(),
            'prize_amount' => 10.00,
            'scheduled_for' => now(),
            'is_active' => false,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Winner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GiftCard>
 */
class GiftCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $winner = Winner::factory()->create();

        return [
            'user_id' => $winner->user_id,
            'winner_id' => $winner->id,
            'order_id' => $this->faker->uuid(),
            'reward_id' => $this->faker->uuid(),
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => $this->faker->randomElement(['pending', 'delivered', 'failed']),
            'redemption_link' => null,
            'delivery_method' => 'email',
            'delivered_at' => null,
            'redeemed_at' => null,
            'provider' => 'tremendous',
            'provider_response' => null,
            'error_message' => null,
        ];
    }

    /**
     * Indicate that the gift card has been delivered
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'redemption_link' => $this->faker->url(),
            'delivered_at' => now(),
            'provider_response' => [
                'order_id' => $this->faker->uuid(),
                'reward_id' => $this->faker->uuid(),
                'status' => 'SUCCESS',
            ],
        ]);
    }

    /**
     * Indicate that the gift card delivery failed
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'error_message' => 'Failed to deliver gift card',
            'provider_response' => [
                'error' => 'API error',
            ],
        ]);
    }

    /**
     * Indicate that the gift card is pending
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'redemption_link' => null,
            'delivered_at' => null,
        ]);
    }
}

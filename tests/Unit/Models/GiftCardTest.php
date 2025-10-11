<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;

use App\Models\DailyQuestion;
use App\Models\GiftCard;
use App\Models\GiftCardDeliveryLog;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiftCardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function gift_card_can_be_created()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();
        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $giftCard = GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner->id,
            'order_id' => 'ORDER-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'delivered',
            'provider' => 'tremendous',
        ]);

        $this->assertDatabaseHas('gift_cards', [
            'user_id' => $user->id,
            'winner_id' => $winner->id,
            'order_id' => 'ORDER-123',
        ]);
    }

    #[Test]
    public function amount_is_cast_to_decimal()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();
        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $giftCard = GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner->id,
            'order_id' => 'ORDER-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'pending',
            'provider' => 'tremendous',
        ]);

        $this->assertIsString($giftCard->amount);
        $this->assertEquals('10.00', $giftCard->amount);
    }

    #[Test]
    public function delivered_at_is_cast_to_datetime()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();
        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $giftCard = GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner->id,
            'order_id' => 'ORDER-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'delivered',
            'provider' => 'tremendous',
            'delivered_at' => now(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $giftCard->delivered_at);
    }

    #[Test]
    public function provider_response_is_cast_to_array()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();
        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $response = ['order_id' => '123', 'status' => 'success'];

        $giftCard = GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner->id,
            'order_id' => 'ORDER-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'delivered',
            'provider' => 'tremendous',
            'provider_response' => $response,
        ]);

        $this->assertIsArray($giftCard->provider_response);
        $this->assertEquals($response, $giftCard->provider_response);
    }

    #[Test]
    public function is_delivered_returns_true_when_delivered()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();
        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $giftCard = GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner->id,
            'order_id' => 'ORDER-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'delivered',
            'provider' => 'tremendous',
            'delivered_at' => now(),
        ]);

        $this->assertTrue($giftCard->isDelivered());
    }

    #[Test]
    public function is_delivered_returns_false_when_pending()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();
        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $giftCard = GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner->id,
            'order_id' => 'ORDER-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'pending',
            'provider' => 'tremendous',
        ]);

        $this->assertFalse($giftCard->isDelivered());
    }

    #[Test]
    public function mark_as_delivered_updates_status()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();
        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $giftCard = GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner->id,
            'order_id' => 'ORDER-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'pending',
            'provider' => 'tremendous',
        ]);

        $giftCard->markAsDelivered('https://example.com/redeem');

        $this->assertEquals('delivered', $giftCard->status);
        $this->assertEquals('https://example.com/redeem', $giftCard->redemption_link);
        $this->assertNotNull($giftCard->delivered_at);
    }

    #[Test]
    public function mark_as_failed_updates_status_and_error()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();
        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $giftCard = GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner->id,
            'order_id' => 'ORDER-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'pending',
            'provider' => 'tremendous',
        ]);

        $giftCard->markAsFailed('API error occurred');

        $this->assertEquals('failed', $giftCard->status);
        $this->assertEquals('API error occurred', $giftCard->error_message);
    }

    #[Test]
    public function gift_card_has_user_relationship()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();
        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $giftCard = GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner->id,
            'order_id' => 'ORDER-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'delivered',
            'provider' => 'tremendous',
        ]);

        $this->assertInstanceOf(User::class, $giftCard->user);
        $this->assertEquals($user->id, $giftCard->user->id);
    }

    #[Test]
    public function gift_card_has_winner_relationship()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();
        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $giftCard = GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner->id,
            'order_id' => 'ORDER-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'delivered',
            'provider' => 'tremendous',
        ]);

        $this->assertInstanceOf(Winner::class, $giftCard->winner);
        $this->assertEquals($winner->id, $giftCard->winner->id);
    }

    #[Test]
    public function gift_card_has_delivery_logs_relationship()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();
        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $giftCard = GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner->id,
            'order_id' => 'ORDER-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'pending',
            'provider' => 'tremendous',
        ]);

        GiftCardDeliveryLog::create([
            'gift_card_id' => $giftCard->id,
            'attempt_number' => 1,
            'status' => 'success',
        ]);

        $this->assertCount(1, $giftCard->deliveryLogs);
        $this->assertInstanceOf(GiftCardDeliveryLog::class, $giftCard->deliveryLogs->first());
    }
}

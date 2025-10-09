<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\Attributes\Test;

use App\Models\GiftCard;
use App\Models\User;
use App\Models\Winner;
use App\Models\DailyQuestion;
use App\Services\GiftCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GiftCardServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GiftCardService $service;
    protected Winner $winner;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new GiftCardService();

        // Create test data
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        $this->winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
            'won_at' => now(),
        ]);
    }

    #[Test]
    public function it_delivers_gift_card_in_mock_mode()
    {
        config(['app.env' => 'local']);

        $giftCard = $this->service->deliverGiftCard($this->winner);

        $this->assertInstanceOf(GiftCard::class, $giftCard);
        $this->assertEquals('delivered', $giftCard->status);
        $this->assertEquals($this->winner->user_id, $giftCard->user_id);
        $this->assertEquals(10.00, $giftCard->amount);
        $this->assertNotNull($giftCard->redemption_link);
    }

    #[Test]
    public function it_creates_gift_card_record()
    {
        config(['app.env' => 'local']);

        $giftCard = $this->service->deliverGiftCard($this->winner);

        $this->assertDatabaseHas('gift_cards', [
            'user_id' => $this->winner->user_id,
            'winner_id' => $this->winner->id,
            'amount' => 10.00,
            'currency' => 'USD',
            'provider' => 'tremendous',
        ]);
    }

    #[Test]
    public function it_logs_successful_delivery()
    {
        config(['app.env' => 'local']);

        $giftCard = $this->service->deliverGiftCard($this->winner);

        $this->assertDatabaseHas('gift_card_delivery_logs', [
            'gift_card_id' => $giftCard->id,
            'attempt_number' => 1,
            'status' => 'success',
        ]);
    }

    #[Test]
    public function it_handles_api_failure_gracefully()
    {
        config(['app.env' => 'production']);
        config(['services.tremendous.api_key' => 'test_key']);

        Http::fake([
            '*' => Http::response(['error' => 'API Error'], 500),
        ]);

        $giftCard = $this->service->deliverGiftCard($this->winner);

        $this->assertEquals('failed', $giftCard->status);
        $this->assertNotNull($giftCard->error_message);
    }

    #[Test]
    public function it_sends_correct_data_to_tremendous_api()
    {
        config(['app.env' => 'production']);
        config(['services.tremendous.api_key' => 'test_key']);
        config(['services.tremendous.funding_source_id' => 'funding_123']);

        Http::fake([
            '*/orders' => Http::response([
                'order' => ['id' => 'order_123'],
                'reward' => [
                    'id' => 'reward_123',
                    'redemption_link' => 'https://tremendous.com/redeem/test',
                ],
            ], 200),
        ]);

        $this->service->deliverGiftCard($this->winner);

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer test_key') &&
                   $request['reward']['recipient']['email'] === $this->winner->user->email &&
                   $request['reward']['value']['denomination'] === 10.00;
        });
    }

    #[Test]
    public function it_retries_failed_delivery()
    {
        config(['app.env' => 'local']);

        // Create a failed gift card
        $giftCard = GiftCard::create([
            'user_id' => $this->winner->user_id,
            'winner_id' => $this->winner->id,
            'order_id' => 'ORDER-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'failed',
            'provider' => 'tremendous',
        ]);

        $this->service->retryDelivery($giftCard, 2);

        $giftCard->refresh();
        $this->assertEquals('delivered', $giftCard->status);
    }

    #[Test]
    public function it_stops_retrying_after_max_attempts()
    {
        config(['app.env' => 'local']);

        $giftCard = GiftCard::create([
            'user_id' => $this->winner->user_id,
            'winner_id' => $this->winner->id,
            'order_id' => 'ORDER-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'failed',
            'provider' => 'tremendous',
        ]);

        // Attempt 6 (over the max of 5)
        $this->service->retryDelivery($giftCard, 6);

        // Should not create additional delivery logs
        $this->assertEquals(0, $giftCard->deliveryLogs()->count());
    }
}

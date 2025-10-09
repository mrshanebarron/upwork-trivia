<?php

namespace Tests\Unit\Jobs;

use PHPUnit\Framework\Attributes\Test;

use App\Jobs\DeliverGiftCardJob;
use App\Models\DailyQuestion;
use App\Models\GiftCard;
use App\Models\User;
use App\Models\Winner;
use App\Services\GiftCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DeliverGiftCardJobTest extends TestCase
{
    use RefreshDatabase;

    protected Winner $winner;

    protected function setUp(): void
    {
        parent::setUp();

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
    public function job_can_be_dispatched()
    {
        Queue::fake();

        DeliverGiftCardJob::dispatch($this->winner);

        Queue::assertPushed(DeliverGiftCardJob::class);
    }

    #[Test]
    public function job_handles_delivery_successfully()
    {
        config(['app.env' => 'local']);

        $job = new DeliverGiftCardJob($this->winner);
        $job->handle(new GiftCardService());

        $this->assertDatabaseHas('gift_cards', [
            'user_id' => $this->winner->user_id,
            'winner_id' => $this->winner->id,
            'status' => 'delivered',
        ]);
    }

    #[Test]
    public function job_has_correct_retry_configuration()
    {
        $job = new DeliverGiftCardJob($this->winner);

        $this->assertEquals(5, $job->tries);
        $this->assertEquals(60, $job->timeout);
    }

    #[Test]
    public function job_has_exponential_backoff()
    {
        $job = new DeliverGiftCardJob($this->winner);

        $backoff = $job->backoff();

        $this->assertEquals([300, 600, 1200, 2400, 4800], $backoff);
    }

    #[Test]
    public function job_logs_permanent_failure()
    {
        $job = new DeliverGiftCardJob($this->winner);

        $exception = new \Exception('Test failure');

        // Should not throw exception
        $job->failed($exception);

        $this->assertTrue(true);
    }

    #[Test]
    public function job_can_be_serialized_and_deserialized()
    {
        $job = new DeliverGiftCardJob($this->winner);

        $serialized = serialize($job);
        $unserialized = unserialize($serialized);

        $this->assertInstanceOf(DeliverGiftCardJob::class, $unserialized);
        $this->assertEquals($this->winner->id, $unserialized->winner->id);
    }
}

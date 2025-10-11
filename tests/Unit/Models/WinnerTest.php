<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;

use App\Models\DailyQuestion;
use App\Models\GiftCard;
use App\Models\Submission;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WinnerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function winner_can_be_created()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        $submission = Submission::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => true,
            'ip_address' => '127.0.0.1',
        ]);

        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'submission_id' => $submission->id,
            'prize_amount' => 10.00,
        ]);

        $this->assertDatabaseHas('winners', [
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'submission_id' => $submission->id,
        ]);
    }

    #[Test]
    public function prize_amount_is_cast_to_decimal()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $this->assertIsString($winner->prize_amount);
        $this->assertEquals('10.00', $winner->prize_amount);
    }

    #[Test]
    public function created_at_is_cast_to_datetime()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $winner->created_at);
    }

    #[Test]
    public function winner_has_user_relationship()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $this->assertInstanceOf(User::class, $winner->user);
        $this->assertEquals($user->id, $winner->user->id);
    }

    #[Test]
    public function winner_has_daily_question_relationship()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $this->assertInstanceOf(DailyQuestion::class, $winner->dailyQuestion);
        $this->assertEquals($question->id, $winner->dailyQuestion->id);
    }

    #[Test]
    public function winner_has_submission_relationship()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        $submission = Submission::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => true,
            'ip_address' => '127.0.0.1',
        ]);

        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'submission_id' => $submission->id,
            'prize_amount' => 10.00,
        ]);

        $this->assertInstanceOf(Submission::class, $winner->submission);
        $this->assertEquals($submission->id, $winner->submission->id);
    }

    #[Test]
    public function winner_has_gift_card_relationship()
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

        $this->assertInstanceOf(GiftCard::class, $winner->giftCard);
        $this->assertEquals($giftCard->id, $winner->giftCard->id);
    }

    #[Test]
    public function winner_does_not_have_updated_at_column()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $this->assertNull($winner->updated_at);
    }
}

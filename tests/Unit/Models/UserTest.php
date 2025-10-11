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

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_win_when_eligible()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        $this->assertTrue($user->canWin());
    }

    #[Test]
    public function user_cannot_win_within_30_day_cooldown()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);

        $user->refresh();

        $this->assertFalse($user->canWin());
    }

    #[Test]
    public function user_can_win_after_30_day_cooldown()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        $winner = new Winner([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
        ]);
        $winner->created_at = now()->subDays(31);
        $winner->save();

        $user->refresh();

        $this->assertTrue($user->canWin());
    }

    #[Test]
    public function user_is_of_age_when_18_or_older()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(18)]);

        $this->assertTrue($user->isOfAge());
    }

    #[Test]
    public function user_is_not_of_age_when_under_18()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(17)]);

        $this->assertFalse($user->isOfAge());
    }

    #[Test]
    public function total_winnings_calculates_correctly()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question1 = DailyQuestion::factory()->create();
        $question2 = DailyQuestion::factory()->create();

        $winner1 = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question1->id,
            'prize_amount' => 10.00,
            'won_at' => now()->subDays(40),
        ]);

        GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner1->id,
            'order_id' => 'ORDER-1',
            'reward_id' => 'REWARD-1',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'delivered',
            'provider' => 'tremendous',
        ]);

        $winner2 = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question2->id,
            'prize_amount' => 10.00,
            'won_at' => now()->subDays(50),
        ]);

        GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner2->id,
            'order_id' => 'ORDER-2',
            'reward_id' => 'REWARD-2',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'delivered',
            'provider' => 'tremendous',
        ]);

        $user->refresh();

        $this->assertEquals(20.00, $user->total_winnings);
    }

    #[Test]
    public function last_won_at_returns_most_recent_win()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question1 = DailyQuestion::factory()->create();
        $question2 = DailyQuestion::factory()->create();

        $olderWin = new Winner([
            'user_id' => $user->id,
            'daily_question_id' => $question1->id,
            'prize_amount' => 10.00,
        ]);
        $olderWin->created_at = now()->subDays(40);
        $olderWin->save();

        $recentWin = new Winner([
            'user_id' => $user->id,
            'daily_question_id' => $question2->id,
            'prize_amount' => 10.00,
        ]);
        $recentWin->created_at = now()->subDays(35);
        $recentWin->save();

        $user->refresh();

        $this->assertEquals(
            $recentWin->created_at->format('Y-m-d'),
            $user->last_won_at->format('Y-m-d')
        );
    }

    #[Test]
    public function user_has_winners_relationship()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
            'won_at' => now(),
        ]);

        $this->assertCount(1, $user->winners);
    }

    #[Test]
    public function user_has_submissions_relationship()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        Submission::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => false,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertCount(1, $user->submissions);
    }

    #[Test]
    public function user_has_gift_cards_relationship()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        $winner = Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
            'won_at' => now(),
        ]);

        GiftCard::create([
            'user_id' => $user->id,
            'winner_id' => $winner->id,
            'order_id' => 'ORDER-1',
            'reward_id' => 'REWARD-1',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'delivered',
            'provider' => 'tremendous',
        ]);

        $this->assertCount(1, $user->giftCards);
    }
}

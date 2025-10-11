<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;

use App\Models\DailyQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AntiCheatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
            'correct_answer' => 'B',
        ]);
    }

    #[Test]
    public function same_ip_cannot_submit_multiple_times()
    {
        // Configure setting to allow only 1 submission per IP
        $setting = \App\Models\Setting::updateOrCreate(
            ['key' => 'max_daily_submissions_per_ip'],
            ['value' => '1', 'type' => 'integer']
        );

        $user1 = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $user2 = User::factory()->create(['birthdate' => now()->subYears(25)]);

        $ipAddress = '192.168.1.100';

        // First user submits from IP
        $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->actingAs($user1)
            ->post(route('contest.submit'), [
                'question_id' => $this->question->id,
                'answer' => 'A',
            ]);

        // Second user tries from same IP
        $response = $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->actingAs($user2)
            ->post(route('contest.submit'), [
                'question_id' => $this->question->id,
                'answer' => 'B',
            ]);

        // Second submission should be blocked
        $this->assertEquals(0, $user2->submissions()->count());
    }

    #[Test]
    public function user_cannot_win_within_30_day_cooldown()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        // Win first question
        $this->actingAs($user)->post(route('contest.submit'), [
            'question_id' => $this->question->id,
            'answer' => 'B',
        ]);

        $this->assertNotNull($user->winners()->first());

        // Travel 29 days (still in cooldown)
        $this->travel(29)->days();

        $newQuestion = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
            'correct_answer' => 'A',
        ]);

        $this->actingAs($user)->post(route('contest.submit'), [
            'question_id' => $newQuestion->id,
            'answer' => 'A',
        ]);

        // Should not be able to win
        $this->assertFalse($user->canWin());
    }

    #[Test]
    public function user_can_win_after_30_day_cooldown()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        // Win first question
        $this->actingAs($user)->post(route('contest.submit'), [
            'question_id' => $this->question->id,
            'answer' => 'B',
        ]);

        // Travel 31 days (cooldown expired)
        $this->travel(31)->days();

        $newQuestion = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
            'correct_answer' => 'A',
        ]);

        $this->actingAs($user)->post(route('contest.submit'), [
            'question_id' => $newQuestion->id,
            'answer' => 'A',
        ]);

        // Should be able to win again
        $this->assertTrue($user->canWin());
        $this->assertEquals(2, $user->winners()->count());
    }
}

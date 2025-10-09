<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;

use App\Models\DailyQuestion;
use App\Models\Submission;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyQuestionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function question_is_active_when_conditions_met()
    {
        $question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now()->subHour(),
        ]);

        $this->assertTrue($question->isActive());
    }

    #[Test]
    public function question_is_not_active_when_flagged_inactive()
    {
        $question = DailyQuestion::factory()->create([
            'is_active' => false,
            'scheduled_for' => now()->subHour(),
        ]);

        $this->assertFalse($question->isActive());
    }

    #[Test]
    public function question_is_not_active_when_scheduled_in_future()
    {
        $question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now()->addHour(),
        ]);

        $this->assertFalse($question->isActive());
    }

    #[Test]
    public function question_is_not_active_when_winner_exists()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now()->subHour(),
        ]);

        Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
            'won_at' => now(),
        ]);

        $question->refresh();

        $this->assertFalse($question->isActive());
    }

    #[Test]
    public function submission_count_accessor_works()
    {
        $question = DailyQuestion::factory()->create();
        $user1 = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $user2 = User::factory()->create(['birthdate' => now()->subYears(25)]);

        Submission::create([
            'user_id' => $user1->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => false,
            'ip_address' => '127.0.0.1',
        ]);

        Submission::create([
            'user_id' => $user2->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'B',
            'is_correct' => true,
            'ip_address' => '127.0.0.2',
        ]);

        $question->refresh();

        $this->assertEquals(2, $question->submission_count);
    }

    #[Test]
    public function correct_submission_count_accessor_works()
    {
        $question = DailyQuestion::factory()->create(['correct_answer' => 'B']);
        $user1 = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $user2 = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $user3 = User::factory()->create(['birthdate' => now()->subYears(25)]);

        Submission::create([
            'user_id' => $user1->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => false,
            'ip_address' => '127.0.0.1',
        ]);

        Submission::create([
            'user_id' => $user2->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'B',
            'is_correct' => true,
            'ip_address' => '127.0.0.2',
        ]);

        Submission::create([
            'user_id' => $user3->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'B',
            'is_correct' => true,
            'ip_address' => '127.0.0.3',
        ]);

        $question->refresh();

        $this->assertEquals(2, $question->correct_submission_count);
    }

    #[Test]
    public function question_has_submissions_relationship()
    {
        $question = DailyQuestion::factory()->create();
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        Submission::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => false,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertCount(1, $question->submissions);
    }

    #[Test]
    public function question_has_winner_relationship()
    {
        $question = DailyQuestion::factory()->create();
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
            'won_at' => now(),
        ]);

        $this->assertNotNull($question->winner);
    }

    #[Test]
    public function answer_choices_are_cast_to_array()
    {
        $question = DailyQuestion::factory()->create([
            'answer_choices' => ['A' => 'Choice A', 'B' => 'Choice B', 'C' => 'Choice C', 'D' => 'Choice D'],
        ]);

        $this->assertIsArray($question->answer_choices);
        $this->assertArrayHasKey('A', $question->answer_choices);
    }

    #[Test]
    public function scheduled_for_is_cast_to_datetime()
    {
        $question = DailyQuestion::factory()->create();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $question->scheduled_for);
    }
}

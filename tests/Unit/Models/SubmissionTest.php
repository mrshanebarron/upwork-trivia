<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;

use App\Models\DailyQuestion;
use App\Models\Sticker;
use App\Models\Submission;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmissionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function submission_can_be_created()
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

        $this->assertDatabaseHas('submissions', [
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
        ]);
    }

    #[Test]
    public function is_correct_is_cast_to_boolean()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        $submission = Submission::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => 1,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertIsBool($submission->is_correct);
        $this->assertTrue($submission->is_correct);
    }

    #[Test]
    public function random_tiebreaker_is_auto_generated()
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

        $this->assertNotNull($submission->random_tiebreaker);
        $this->assertIsInt($submission->random_tiebreaker);
        $this->assertGreaterThanOrEqual(1, $submission->random_tiebreaker);
        $this->assertLessThanOrEqual(1000000, $submission->random_tiebreaker);
    }

    #[Test]
    public function submitted_at_is_auto_generated()
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

        $this->assertNotNull($submission->submitted_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $submission->submitted_at);
    }

    #[Test]
    public function would_win_returns_true_for_first_correct_submission()
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

        $this->assertTrue($submission->wouldWin());
    }

    #[Test]
    public function would_win_returns_false_for_incorrect_submission()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        $submission = Submission::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => false,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertFalse($submission->wouldWin());
    }

    #[Test]
    public function would_win_returns_false_for_second_correct_submission()
    {
        $user1 = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $user2 = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        // First submission
        $submission1 = Submission::create([
            'user_id' => $user1->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => true,
            'ip_address' => '127.0.0.1',
            'submitted_at' => now()->subSecond(),
        ]);

        // Second submission
        $submission2 = Submission::create([
            'user_id' => $user2->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => true,
            'ip_address' => '127.0.0.2',
            'submitted_at' => now(),
        ]);

        $this->assertTrue($submission1->wouldWin());
        $this->assertFalse($submission2->wouldWin());
    }

    #[Test]
    public function is_winner_accessor_returns_true_when_winner_exists()
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

        Winner::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'submission_id' => $submission->id,
            'prize_amount' => 10.00,
        ]);

        $this->assertTrue($submission->is_winner);
    }

    #[Test]
    public function is_winner_accessor_returns_false_when_no_winner()
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

        $this->assertFalse($submission->is_winner);
    }

    #[Test]
    public function submission_has_user_relationship()
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

        $this->assertInstanceOf(User::class, $submission->user);
        $this->assertEquals($user->id, $submission->user->id);
    }

    #[Test]
    public function submission_has_daily_question_relationship()
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

        $this->assertInstanceOf(DailyQuestion::class, $submission->dailyQuestion);
        $this->assertEquals($question->id, $submission->dailyQuestion->id);
    }

    #[Test]
    public function submission_has_sticker_relationship()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();
        $sticker = Sticker::factory()->create();

        $submission = Submission::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => true,
            'ip_address' => '127.0.0.1',
            'sticker_id' => $sticker->id,
        ]);

        $this->assertInstanceOf(Sticker::class, $submission->sticker);
        $this->assertEquals($sticker->id, $submission->sticker->id);
    }

    #[Test]
    public function submission_does_not_have_updated_at_column()
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

        $this->assertNull($submission->updated_at);
    }
}

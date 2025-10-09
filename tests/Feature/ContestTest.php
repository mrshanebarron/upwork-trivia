<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;

use App\Models\DailyQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test question
        $this->question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
            'correct_answer' => 'B',
        ]);
    }

    #[Test]
    public function user_can_view_contest_page()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        $response = $this->actingAs($user)->get(route('contest.show'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Contest/GoldenQuestion')
            ->has('question')
        );
    }

    #[Test]
    public function user_can_submit_correct_answer()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        $response = $this->actingAs($user)->post(route('contest.submit'), [
            'question_id' => $this->question->id,
            'answer' => 'B', // Correct answer
        ]);

        $this->assertDatabaseHas('submissions', [
            'user_id' => $user->id,
            'daily_question_id' => $this->question->id,
            'selected_answer' => 'B',
            'is_correct' => true,
        ]);
    }

    #[Test]
    public function first_correct_answer_wins()
    {
        $user1 = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $user2 = User::factory()->create(['birthdate' => now()->subYears(25)]);

        // User 1 submits correct answer first
        $this->actingAs($user1)->post(route('contest.submit'), [
            'question_id' => $this->question->id,
            'answer' => 'B',
        ]);

        // User 2 submits correct answer second
        $this->actingAs($user2)->post(route('contest.submit'), [
            'question_id' => $this->question->id,
            'answer' => 'B',
        ]);

        // Only user 1 should be the winner
        $this->assertDatabaseHas('winners', [
            'user_id' => $user1->id,
            'daily_question_id' => $this->question->id,
        ]);

        $this->assertDatabaseMissing('winners', [
            'user_id' => $user2->id,
        ]);
    }

    #[Test]
    public function user_cannot_submit_twice_to_same_question()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        // First submission
        $this->actingAs($user)->post(route('contest.submit'), [
            'question_id' => $this->question->id,
            'answer' => 'A',
        ]);

        // Second submission should fail
        $response = $this->actingAs($user)->post(route('contest.submit'), [
            'question_id' => $this->question->id,
            'answer' => 'B',
        ]);

        // Should only have one submission
        $this->assertEquals(1, $user->submissions()->count());
    }

    #[Test]
    public function user_must_be_of_age_to_submit()
    {
        $underageUser = User::factory()->create([
            'birthdate' => now()->subYears(17), // Under 18
        ]);

        $response = $this->actingAs($underageUser)->post(route('contest.submit'), [
            'question_id' => $this->question->id,
            'answer' => 'B',
        ]);

        // Should be blocked by age_verified middleware
        $response->assertRedirect();
    }

    #[Test]
    public function recent_winner_cannot_win_again()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        // User wins first question
        $this->actingAs($user)->post(route('contest.submit'), [
            'question_id' => $this->question->id,
            'answer' => 'B',
        ]);

        // Create new question 10 days later
        $this->travel(10)->days();
        $newQuestion = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
            'correct_answer' => 'A',
        ]);

        // Try to submit to new question
        $response = $this->actingAs($user)->post(route('contest.submit'), [
            'question_id' => $newQuestion->id,
            'answer' => 'A',
        ]);

        // Should be able to submit but not win
        $submission = $user->submissions()->where('daily_question_id', $newQuestion->id)->first();
        $this->assertNotNull($submission);
        $this->assertFalse($submission->is_winner);
    }

    #[Test]
    public function winner_receives_gift_card()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        $this->actingAs($user)->post(route('contest.submit'), [
            'question_id' => $this->question->id,
            'answer' => 'B',
        ]);

        // Check gift card was created
        $this->assertDatabaseHas('gift_cards', [
            'user_id' => $user->id,
            'amount' => $this->question->prize_amount,
            'currency' => 'USD',
            'provider' => 'tremendous',
        ]);
    }
}

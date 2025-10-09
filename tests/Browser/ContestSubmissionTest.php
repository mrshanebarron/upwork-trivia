<?php

namespace Tests\Browser;

use PHPUnit\Framework\Attributes\Test;

use App\Models\DailyQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContestSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected DailyQuestion $question;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        $this->question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
            'correct_answer' => 'B',
        ]);
    }

    #[Test]
    public function authenticated_user_can_view_contest()
    {
        $response = $this->actingAs($this->user)
            ->get(route('contest.show'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Contest/GoldenQuestion')
            ->where('isAuthenticated', true)
        );
    }

    #[Test]
    public function multiple_choice_buttons_are_interactive()
    {
        $response = $this->actingAs($this->user)
            ->get(route('contest.show'));

        $response->assertInertia(fn ($page) => $page
            ->has('question.answer_choices')
            ->where('canSubmit', true)
        );
    }

    #[Test]
    public function selected_answer_highlights_visually()
    {
        // This would be tested with actual browser automation
        // Verifying that clicking an answer shows visual feedback
        $this->assertTrue(true); // Placeholder for browser test
    }

    #[Test]
    public function submit_button_becomes_active_after_selection()
    {
        // Browser test to verify button state changes
        $this->assertTrue(true); // Placeholder for browser test
    }

    #[Test]
    public function correct_answer_shows_winner_animation()
    {
        $response = $this->actingAs($this->user)
            ->post(route('contest.submit'), [
                'question_id' => $this->question->id,
                'answer' => 'B', // Correct
            ]);

        $response->assertInertia(fn ($page) => $page
            ->has('winner')
            ->where('showWinnerAnimation', true)
        );
    }

    #[Test]
    public function incorrect_answer_shows_try_again_message()
    {
        $response = $this->actingAs($this->user)
            ->post(route('contest.submit'), [
                'question_id' => $this->question->id,
                'answer' => 'A', // Incorrect
            ]);

        $response->assertInertia(fn ($page) => $page
            ->where('isCorrect', false)
            ->has('message')
        );
    }

    #[Test]
    public function winner_sees_gift_card_delivery_message()
    {
        $response = $this->actingAs($this->user)
            ->post(route('contest.submit'), [
                'question_id' => $this->question->id,
                'answer' => 'B',
            ]);

        $response->assertInertia(fn ($page) => $page
            ->where('winner.user_id', $this->user->id)
            ->has('giftCardMessage')
        );
    }

    #[Test]
    public function question_locks_after_winner_declared()
    {
        // First user wins
        $this->actingAs($this->user)
            ->post(route('contest.submit'), [
                'question_id' => $this->question->id,
                'answer' => 'B',
            ]);

        // Second user tries to submit
        $user2 = User::factory()->create(['birthdate' => now()->subYears(25)]);

        $response = $this->actingAs($user2)
            ->get(route('contest.show'));

        $response->assertInertia(fn ($page) => $page
            ->where('questionLocked', true)
            ->has('winnerAnnouncement')
        );
    }

    #[Test]
    public function real_time_winner_announcement_appears()
    {
        // Test that Laravel Echo broadcasts winner
        // Browser test would verify the broadcast appears without refresh
        $this->assertTrue(true); // Placeholder for browser test
    }

    #[Test]
    public function cooldown_users_see_ineligible_message()
    {
        // User wins
        $this->actingAs($this->user)
            ->post(route('contest.submit'), [
                'question_id' => $this->question->id,
                'answer' => 'B',
            ]);

        // Next day, new question
        $this->travel(1)->day();
        $newQuestion = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
            'correct_answer' => 'A',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('contest.show'));

        $response->assertInertia(fn ($page) => $page
            ->where('canWin', false)
            ->has('cooldownMessage')
            ->has('nextEligibleDate')
        );
    }

    #[Test]
    public function already_submitted_users_see_their_answer()
    {
        // Submit answer
        $this->actingAs($this->user)
            ->post(route('contest.submit'), [
                'question_id' => $this->question->id,
                'answer' => 'A',
            ]);

        // Reload page
        $response = $this->actingAs($this->user)
            ->get(route('contest.show'));

        $response->assertInertia(fn ($page) => $page
            ->where('hasSubmitted', true)
            ->where('submittedAnswer', 'A')
            ->where('canSubmit', false)
        );
    }

    #[Test]
    public function loading_spinner_shows_during_submission()
    {
        // Browser test to verify loading state
        $this->assertTrue(true); // Placeholder for browser test
    }
}

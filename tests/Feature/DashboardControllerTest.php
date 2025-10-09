<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;

use App\Models\DailyQuestion;
use App\Models\GiftCard;
use App\Models\Submission;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['birthdate' => now()->subYears(25)]);
    }

    #[Test]
    public function guest_cannot_access_dashboard()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function authenticated_user_can_view_dashboard()
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('user')
            ->where('user.email', $this->user->email)
        );
    }

    #[Test]
    public function dashboard_shows_user_winnings_data()
    {
        $question = DailyQuestion::factory()->create();
        $winner = Winner::create([
            'user_id' => $this->user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
            'won_at' => now(),
        ]);

        GiftCard::create([
            'user_id' => $this->user->id,
            'winner_id' => $winner->id,
            'order_id' => 'TEST-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'delivered',
            'provider' => 'tremendous',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->has('winners')
            ->where('user.total_winnings', 10.00)
        );
    }

    #[Test]
    public function dashboard_shows_recent_submissions()
    {
        $question = DailyQuestion::factory()->create();

        Submission::create([
            'user_id' => $this->user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => false,
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->has('recent_submissions', 1)
        );
    }

    #[Test]
    public function user_can_view_winnings_page()
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.winnings'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard/Winnings')
            ->has('winners')
            ->has('total_winnings')
            ->has('total_wins')
        );
    }

    #[Test]
    public function winnings_page_shows_paginated_winners()
    {
        $question = DailyQuestion::factory()->create();

        // Create multiple winners
        for ($i = 0; $i < 3; $i++) {
            $winner = Winner::create([
                'user_id' => $this->user->id,
                'daily_question_id' => $question->id,
                'prize_amount' => 10.00,
                'won_at' => now()->subDays($i),
            ]);

            GiftCard::create([
                'user_id' => $this->user->id,
                'winner_id' => $winner->id,
                'order_id' => "TEST-{$i}",
                'reward_id' => "REWARD-{$i}",
                'amount' => 10.00,
                'currency' => 'USD',
                'status' => 'delivered',
                'provider' => 'tremendous',
            ]);
        }

        $response = $this->actingAs($this->user)->get(route('dashboard.winnings'));

        $response->assertInertia(fn ($page) => $page
            ->where('total_winnings', 30.00)
            ->where('total_wins', 3)
        );
    }

    #[Test]
    public function user_can_view_gift_cards_page()
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.gift-cards'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard/GiftCards')
            ->has('gift_cards')
        );
    }

    #[Test]
    public function gift_cards_page_shows_user_gift_cards()
    {
        $question = DailyQuestion::factory()->create();
        $winner = Winner::create([
            'user_id' => $this->user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
            'won_at' => now(),
        ]);

        GiftCard::create([
            'user_id' => $this->user->id,
            'winner_id' => $winner->id,
            'order_id' => 'TEST-123',
            'reward_id' => 'REWARD-123',
            'amount' => 10.00,
            'currency' => 'USD',
            'status' => 'delivered',
            'provider' => 'tremendous',
            'redemption_link' => 'https://example.com/redeem',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard.gift-cards'));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_can_view_submissions_page()
    {
        $response = $this->actingAs($this->user)->get(route('dashboard.submissions'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard/Submissions')
            ->has('submissions')
            ->has('stats')
        );
    }

    #[Test]
    public function submissions_page_shows_accuracy_stats()
    {
        $question = DailyQuestion::factory()->create(['correct_answer' => 'B']);

        // Correct submission
        Submission::create([
            'user_id' => $this->user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'B',
            'is_correct' => true,
            'ip_address' => '127.0.0.1',
        ]);

        // Incorrect submission (different question)
        $question2 = DailyQuestion::factory()->create(['correct_answer' => 'A']);
        Submission::create([
            'user_id' => $this->user->id,
            'daily_question_id' => $question2->id,
            'selected_answer' => 'B',
            'is_correct' => false,
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard.submissions'));

        $response->assertInertia(fn ($page) => $page
            ->where('stats.total', 2)
            ->where('stats.correct', 1)
            ->where('stats.incorrect', 1)
            ->where('stats.accuracy', 50.0)
        );
    }

    #[Test]
    public function dashboard_shows_eligibility_status()
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->where('user.can_win', true)
        );
    }

    #[Test]
    public function dashboard_shows_next_eligible_date_when_on_cooldown()
    {
        $question = DailyQuestion::factory()->create();
        Winner::create([
            'user_id' => $this->user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
            'won_at' => now(),
        ]);

        $this->user->refresh();

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->where('user.can_win', false)
            ->has('user.next_eligible_date')
        );
    }
}

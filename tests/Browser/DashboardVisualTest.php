<?php

namespace Tests\Browser;

use PHPUnit\Framework\Attributes\Test;

use App\Models\DailyQuestion;
use App\Models\GiftCard;
use App\Models\Submission;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardVisualTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['birthdate' => now()->subYears(25)]);
    }

    #[Test]
    public function dashboard_index_renders_with_green_background()
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
        );
    }

    #[Test]
    public function dashboard_shows_user_profile_section()
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->where('user.name', $this->user->name)
            ->where('user.email', $this->user->email)
        );
    }

    #[Test]
    public function dashboard_displays_total_winnings_prominently()
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

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->where('user.total_winnings', 10.00)
            ->has('winners')
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

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->has('recent_submissions', 1)
        );
    }

    #[Test]
    public function dashboard_shows_eligibility_status_card()
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->where('user.can_win', true)
        );
    }

    #[Test]
    public function dashboard_navigation_has_all_tabs()
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertSee('Dashboard');
        $response->assertSee('Winnings');
        $response->assertSee('Gift Cards');
        $response->assertSee('Submissions');
    }

    #[Test]
    public function winnings_page_shows_win_history()
    {
        $question = DailyQuestion::factory()->create();
        Winner::create([
            'user_id' => $this->user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
            'won_at' => now(),
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.winnings'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard/Winnings')
            ->has('winners')
            ->where('total_winnings', 10.00)
            ->where('total_wins', 1)
        );
    }

    #[Test]
    public function gift_cards_page_shows_redemption_links()
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
            'redemption_link' => 'https://example.com/redeem/test123',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.gift-cards'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard/GiftCards')
            ->has('gift_cards', 1)
        );
    }

    #[Test]
    public function gift_cards_page_shows_customer_support_link()
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard.gift-cards'));

        $response->assertInertia(fn ($page) => $page
            ->has('supportLink')
        );
    }

    #[Test]
    public function submissions_page_shows_accuracy_stats()
    {
        $question = DailyQuestion::factory()->create(['correct_answer' => 'B']);

        Submission::create([
            'user_id' => $this->user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'B',
            'is_correct' => true,
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.submissions'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard/Submissions')
            ->has('submissions', 1)
            ->has('stats')
            ->where('stats.total', 1)
            ->where('stats.correct', 1)
            ->where('stats.accuracy', 100.0)
        );
    }

    #[Test]
    public function submissions_page_shows_question_history()
    {
        $question = DailyQuestion::factory()->create();

        Submission::create([
            'user_id' => $this->user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => false,
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('dashboard.submissions'));

        $response->assertInertia(fn ($page) => $page
            ->has('submissions', 1)
        );
    }

    #[Test]
    public function dashboard_uses_card_based_layout()
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertSee('card');
    }

    #[Test]
    public function dashboard_is_mobile_responsive()
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('viewport');
    }

    #[Test]
    public function cooldown_status_displays_countdown_timer()
    {
        $question = DailyQuestion::factory()->create();
        Winner::create([
            'user_id' => $this->user->id,
            'daily_question_id' => $question->id,
            'prize_amount' => 10.00,
            'won_at' => now(),
        ]);

        $this->user->refresh();

        $response = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $response->assertInertia(fn ($page) => $page
            ->where('user.can_win', false)
            ->has('user.next_eligible_date')
        );
    }
}

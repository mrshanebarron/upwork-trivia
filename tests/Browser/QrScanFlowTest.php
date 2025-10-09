<?php

namespace Tests\Browser;

use PHPUnit\Framework\Attributes\Test;

use App\Models\Sticker;
use App\Models\DailyQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrScanFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Sticker $sticker;
    protected DailyQuestion $question;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sticker = Sticker::create([
            'unique_code' => 'TEST123',
            'location_name' => 'Test Park',
            'property_name' => 'Test Property',
            'status' => 'active',
        ]);

        $this->question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
            'correct_answer' => 'B',
        ]);
    }

    #[Test]
    public function qr_scan_redirects_to_contest_page()
    {
        $response = $this->get(route('scan', ['code' => $this->sticker->unique_code]));

        $response->assertRedirect(route('contest.show', ['code' => $this->sticker->unique_code]));
    }

    #[Test]
    public function contest_page_loads_with_code_parameter()
    {
        $response = $this->get(route('contest.show', ['code' => $this->sticker->unique_code]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Contest/GoldenQuestion')
            ->where('stickerCode', $this->sticker->unique_code)
            ->has('question')
        );
    }

    #[Test]
    public function contest_page_shows_golden_question_prominently()
    {
        $response = $this->get(route('contest.show', ['code' => $this->sticker->unique_code]));

        $response->assertInertia(fn ($page) => $page
            ->has('question')
            ->where('question.id', $this->question->id)
            ->has('question.answer_choices')
        );
    }

    #[Test]
    public function contest_page_shows_animated_background()
    {
        $response = $this->get(route('contest.show', ['code' => $this->sticker->unique_code]));

        $response->assertInertia(fn ($page) => $page
            ->has('animations')
            ->where('animations.puppy.enabled', true)
        );
    }

    #[Test]
    public function contest_page_displays_glassmorphism_window()
    {
        $response = $this->get(route('contest.show', ['code' => $this->sticker->unique_code]));

        $response->assertSee('backdrop-blur');
    }

    #[Test]
    public function contest_page_shows_advertisement_boxes()
    {
        $response = $this->get(route('contest.show', ['code' => $this->sticker->unique_code]));

        $response->assertInertia(fn ($page) => $page
            ->has('adBoxes')
        );
    }

    #[Test]
    public function contest_page_shows_bag_trivia_questions()
    {
        $response = $this->get(route('contest.show', ['code' => $this->sticker->unique_code]));

        $response->assertInertia(fn ($page) => $page
            ->has('bagQuestions')
            ->where('stickerCode', $this->sticker->unique_code)
        );
    }

    #[Test]
    public function multiple_choice_answers_render_correctly()
    {
        $response = $this->get(route('contest.show', ['code' => $this->sticker->unique_code]));

        $response->assertInertia(fn ($page) => $page
            ->has('question.answer_choices.A')
            ->has('question.answer_choices.B')
            ->has('question.answer_choices.C')
            ->has('question.answer_choices.D')
        );
    }

    #[Test]
    public function scan_logs_geolocation_data()
    {
        $response = $this->post(route('scan', ['code' => $this->sticker->unique_code]), [
            'latitude' => 37.7749,
            'longitude' => -122.4194,
        ]);

        $this->assertDatabaseHas('sticker_scans', [
            'sticker_id' => $this->sticker->id,
            'latitude' => 37.7749,
            'longitude' => -122.4194,
        ]);
    }

    #[Test]
    public function guest_users_see_login_prompt()
    {
        $response = $this->get(route('contest.show', ['code' => $this->sticker->unique_code]));

        $response->assertInertia(fn ($page) => $page
            ->where('isAuthenticated', false)
            ->has('loginPrompt')
        );
    }

    #[Test]
    public function invalid_qr_code_shows_error()
    {
        $response = $this->get(route('scan', ['code' => 'INVALID']));

        $response->assertNotFound();
    }

    #[Test]
    public function inactive_sticker_shows_error()
    {
        $this->sticker->update(['status' => 'inactive']);

        $response = $this->get(route('scan', ['code' => $this->sticker->unique_code]));

        $response->assertNotFound();
    }
}

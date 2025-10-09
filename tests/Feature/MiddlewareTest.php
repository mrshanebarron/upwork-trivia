<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;

use App\Models\DailyQuestion;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function check_contest_active_allows_access_when_contest_is_active()
    {
        Setting::setValue('contest_active', true);

        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('contest.show'));

        $response->assertStatus(200);
    }

    #[Test]
    public function check_contest_active_redirects_when_contest_is_paused()
    {
        Setting::setValue('contest_active', false);

        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('contest.show'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('info', 'The contest is currently paused. Please check back later.');
    }

    #[Test]
    public function ensure_user_is_of_age_allows_adults()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('contest.show'));

        $response->assertStatus(200);
    }

    #[Test]
    public function ensure_user_is_of_age_blocks_minors()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(17)]);
        $question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('contest.show'));

        $response->assertRedirect();
    }

    #[Test]
    public function ensure_user_is_of_age_blocks_submission_from_minors()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(16)]);
        $question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
            'correct_answer' => 'B',
        ]);

        $response = $this->actingAs($user)->post(route('contest.submit'), [
            'question_id' => $question->id,
            'answer' => 'B',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('submissions', [
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function contest_paused_blocks_submission()
    {
        Setting::setValue('contest_active', false);

        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
            'correct_answer' => 'B',
        ]);

        $response = $this->actingAs($user)->post(route('contest.submit'), [
            'question_id' => $question->id,
            'answer' => 'B',
        ]);

        $response->assertRedirect(route('home'));
    }
}

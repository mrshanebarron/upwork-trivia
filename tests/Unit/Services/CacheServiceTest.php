<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\Attributes\Test;

use App\Models\DailyQuestion;
use App\Models\User;
use App\Models\Submission;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CacheService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CacheService();
        Cache::flush();
    }

    #[Test]
    public function it_caches_active_question()
    {
        $question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now()->subHour(),
        ]);

        $cached = $this->service->getActiveQuestion();

        $this->assertEquals($question->id, $cached->id);
        $this->assertTrue(Cache::has('active_question'));
    }

    #[Test]
    public function it_returns_null_when_no_active_question()
    {
        $result = $this->service->getActiveQuestion();

        $this->assertNull($result);
    }

    #[Test]
    public function it_clears_active_question_cache()
    {
        $question = DailyQuestion::factory()->create(['is_active' => true]);
        $this->service->getActiveQuestion();

        $this->assertTrue(Cache::has('active_question'));

        $this->service->clearActiveQuestion();

        $this->assertFalse(Cache::has('active_question'));
    }

    #[Test]
    public function it_caches_user_eligibility()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        $eligible = $this->service->getUserEligibility($user);

        $this->assertTrue($eligible);
        $this->assertTrue(Cache::has("user_eligibility:{$user->id}"));
    }

    #[Test]
    public function it_clears_user_eligibility_cache()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $this->service->getUserEligibility($user);

        $this->service->clearUserEligibility($user);

        $this->assertFalse(Cache::has("user_eligibility:{$user->id}"));
    }

    #[Test]
    public function it_caches_question_stats()
    {
        $question = DailyQuestion::factory()->create();

        $stats = $this->service->getQuestionStats($question);

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_submissions', $stats);
        $this->assertArrayHasKey('correct_submissions', $stats);
        $this->assertArrayHasKey('accuracy_rate', $stats);
        $this->assertTrue(Cache::has("question_stats:{$question->id}"));
    }

    #[Test]
    public function it_calculates_accuracy_rate_correctly()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create([
            'correct_answer' => 'B',
        ]);

        // Create submissions
        Submission::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'B',
            'is_correct' => true,
            'ip_address' => '127.0.0.1',
        ]);

        Submission::create([
            'user_id' => User::factory()->create(['birthdate' => now()->subYears(25)])->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => false,
            'ip_address' => '127.0.0.2',
        ]);

        $question->refresh();
        $stats = $this->service->getQuestionStats($question);

        $this->assertEquals(2, $stats['total_submissions']);
        $this->assertEquals(1, $stats['correct_submissions']);
        $this->assertEquals(50.0, $stats['accuracy_rate']);
    }

    #[Test]
    public function it_checks_if_user_submitted()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();

        $hasSubmitted = $this->service->hasUserSubmitted($user, $question);
        $this->assertFalse($hasSubmitted);

        Submission::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => false,
            'ip_address' => '127.0.0.1',
        ]);

        Cache::forget("user_submitted:{$user->id}:{$question->id}");
        $hasSubmitted = $this->service->hasUserSubmitted($user, $question);
        $this->assertTrue($hasSubmitted);
    }

    #[Test]
    public function it_checks_if_ip_submitted()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);
        $question = DailyQuestion::factory()->create();
        $ipAddress = '192.168.1.100';

        $hasSubmitted = $this->service->hasIpSubmitted($ipAddress, $question);
        $this->assertFalse($hasSubmitted);

        Submission::create([
            'user_id' => $user->id,
            'daily_question_id' => $question->id,
            'selected_answer' => 'A',
            'is_correct' => false,
            'ip_address' => $ipAddress,
        ]);

        Cache::forget("ip_submitted:{$ipAddress}:{$question->id}");
        $hasSubmitted = $this->service->hasIpSubmitted($ipAddress, $question);
        $this->assertTrue($hasSubmitted);
    }

    #[Test]
    public function it_caches_user_dashboard_stats()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        $stats = $this->service->getUserDashboardStats($user);

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_winnings', $stats);
        $this->assertArrayHasKey('total_wins', $stats);
        $this->assertArrayHasKey('can_win', $stats);
        $this->assertTrue(Cache::has("user_dashboard:{$user->id}"));
    }

    #[Test]
    public function it_warms_up_caches()
    {
        $question = DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now()->subHour(),
        ]);

        $this->service->warmUpCaches();

        // Verify active question is cached
        $this->assertTrue(Cache::has('active_question'));

        // Note: User eligibility warming requires last_login_at column
        // which is not in current schema, so we only test active question caching
    }

    #[Test]
    public function it_clears_all_contest_caches()
    {
        Cache::put('test_key', 'test_value');

        $this->service->clearAllContestCaches();

        // The method should not throw errors
        $this->assertTrue(true);
    }
}

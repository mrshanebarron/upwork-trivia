<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;

use App\Models\Sticker;
use App\Models\StickerScan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScanControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Sticker $sticker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sticker = Sticker::create([
            'unique_code' => 'TEST123',
            'location_name' => 'Test Location',
            'property_name' => 'Test Property',
            'status' => 'active',
        ]);
    }

    #[Test]
    public function scan_redirects_to_contest_page()
    {
        $response = $this->get(route('scan', ['code' => $this->sticker->unique_code]));

        $response->assertRedirect(route('contest.show', ['code' => $this->sticker->unique_code]));
    }

    #[Test]
    public function scan_logs_the_scan()
    {
        $this->get(route('scan', ['code' => $this->sticker->unique_code]));

        $this->assertDatabaseHas('sticker_scans', [
            'sticker_id' => $this->sticker->id,
        ]);
    }

    #[Test]
    public function scan_logs_ip_address()
    {
        $response = $this->get(route('scan', ['code' => $this->sticker->unique_code]));

        $scan = StickerScan::where('sticker_id', $this->sticker->id)->first();
        $this->assertNotNull($scan->ip_address);
    }

    #[Test]
    public function scan_logs_user_agent()
    {
        $response = $this->withHeaders([
            'User-Agent' => 'Test Browser/1.0',
        ])->get(route('scan', ['code' => $this->sticker->unique_code]));

        $scan = StickerScan::where('sticker_id', $this->sticker->id)->first();
        $this->assertEquals('Test Browser/1.0', $scan->user_agent);
    }

    #[Test]
    public function authenticated_user_scan_logs_user_id()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        $this->actingAs($user)
            ->get(route('scan', ['code' => $this->sticker->unique_code]));

        $this->assertDatabaseHas('sticker_scans', [
            'sticker_id' => $this->sticker->id,
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function guest_scan_logs_null_user_id()
    {
        $this->get(route('scan', ['code' => $this->sticker->unique_code]));

        $scan = StickerScan::where('sticker_id', $this->sticker->id)->first();
        $this->assertNull($scan->user_id);
    }

    #[Test]
    public function scan_can_log_geolocation()
    {
        $response = $this->post(route('scan', ['code' => $this->sticker->unique_code]), [
            'latitude' => 37.7749,
            'longitude' => -122.4194,
        ]);

        $scan = StickerScan::where('sticker_id', $this->sticker->id)->first();
        $this->assertEquals(37.7749, $scan->latitude);
        $this->assertEquals(-122.4194, $scan->longitude);
    }

    #[Test]
    public function scan_fails_for_inactive_sticker()
    {
        $this->sticker->update(['status' => 'inactive']);

        $response = $this->get(route('scan', ['code' => $this->sticker->unique_code]));

        $response->assertNotFound();
    }

    #[Test]
    public function scan_fails_for_nonexistent_code()
    {
        $response = $this->get(route('scan', ['code' => 'INVALID']));

        $response->assertNotFound();
    }

    #[Test]
    public function sticker_show_page_displays_sticker_details()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        $response = $this->actingAs($user)
            ->get(route('stickers.show', ['sticker' => $this->sticker->id]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Scan/Show')
            ->has('sticker')
            ->where('sticker.unique_code', $this->sticker->unique_code)
        );
    }

    #[Test]
    public function sticker_show_page_includes_scan_count()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        // Create some scans
        StickerScan::create([
            'sticker_id' => $this->sticker->id,
            'ip_address' => '127.0.0.1',
            'scanned_at' => now(),
        ]);

        StickerScan::create([
            'sticker_id' => $this->sticker->id,
            'ip_address' => '127.0.0.2',
            'scanned_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('stickers.show', ['sticker' => $this->sticker->id]));

        $response->assertInertia(fn ($page) => $page
            ->where('scans_count', 2)
        );
    }

    #[Test]
    public function sticker_show_page_includes_recent_scans()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        StickerScan::create([
            'sticker_id' => $this->sticker->id,
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'scanned_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->get(route('stickers.show', ['sticker' => $this->sticker->id]));

        $response->assertInertia(fn ($page) => $page
            ->has('recent_scans', 1)
        );
    }

    #[Test]
    public function multiple_scans_increment_count()
    {
        $this->get(route('scan', ['code' => $this->sticker->unique_code]));
        $this->get(route('scan', ['code' => $this->sticker->unique_code]));
        $this->get(route('scan', ['code' => $this->sticker->unique_code]));

        $this->assertEquals(3, StickerScan::where('sticker_id', $this->sticker->id)->count());
    }
}

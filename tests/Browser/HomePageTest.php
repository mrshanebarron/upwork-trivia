<?php

namespace Tests\Browser;

use PHPUnit\Framework\Attributes\Test;

use App\Models\DailyQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function homepage_renders_correctly()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Home')
        );
    }

    #[Test]
    public function homepage_has_animated_background_elements()
    {
        $response = $this->get('/');

        // Verify animated elements are present in component props
        $response->assertInertia(fn ($page) => $page
            ->component('Home')
            ->has('animations') // Props for puppy, plane, clouds
        );
    }

    #[Test]
    public function homepage_shows_golden_question_teaser()
    {
        DailyQuestion::factory()->create([
            'is_active' => true,
            'scheduled_for' => now(),
        ]);

        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->component('Home')
            ->where('showGoldenQuestion', true)
            ->has('questionTeaser')
        );
    }

    #[Test]
    public function homepage_displays_sponsor_ad_boxes()
    {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->component('Home')
            ->has('adBoxes')
        );
    }

    #[Test]
    public function homepage_shows_cta_to_scan_stickers()
    {
        $response = $this->get('/');

        $response->assertSee('Scan QR at dog stations to play');
        $response->assertSee('First correct answer wins $10');
    }

    #[Test]
    public function glassmorphism_styles_are_applied()
    {
        $response = $this->get('/');

        // Verify glassmorphism CSS classes are in the rendered HTML
        $response->assertSee('backdrop-blur');
        $response->assertSee('bg-white/20');
    }

    #[Test]
    public function homepage_is_mobile_responsive()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Verify responsive meta tags
        $response->assertSee('viewport');
        $response->assertSee('width=device-width');
    }

    #[Test]
    public function homepage_has_pwa_manifest()
    {
        $response = $this->get('/');

        $response->assertSee('manifest.json');
    }

    #[Test]
    public function animated_puppy_loads_correctly()
    {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->has('animations.puppy')
            ->where('animations.puppy.enabled', true)
        );
    }

    #[Test]
    public function animated_plane_loads_correctly()
    {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->has('animations.plane')
            ->where('animations.plane.enabled', true)
        );
    }

    #[Test]
    public function animated_clouds_load_correctly()
    {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->has('animations.clouds')
            ->where('animations.clouds.enabled', true)
        );
    }

    #[Test]
    public function homepage_uses_gpu_accelerated_animations()
    {
        $response = $this->get('/');

        // Verify transform and opacity usage (GPU-accelerated properties)
        $response->assertSee('transform');
    }
}

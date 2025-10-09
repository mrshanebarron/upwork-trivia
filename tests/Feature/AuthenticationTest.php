<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function registration_requires_birthdate()
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            // Missing birthdate
        ]);

        $response->assertSessionHasErrors('birthdate');
    }

    #[Test]
    public function registration_requires_user_to_be_18_or_older()
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'birthdate' => now()->subYears(17)->format('Y-m-d'), // Under 18
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('birthdate');
    }

    #[Test]
    public function user_can_register_if_18_or_older()
    {
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'birthdate' => now()->subYears(25)->format('Y-m-d'),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    #[Test]
    public function authenticated_user_can_view_dashboard()
    {
        $user = User::factory()->create(['birthdate' => now()->subYears(25)]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('user')
        );
    }
}

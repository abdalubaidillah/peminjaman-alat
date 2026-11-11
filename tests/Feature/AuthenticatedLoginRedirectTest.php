<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticatedLoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_is_redirected_to_their_dashboard_from_login_and_root(): void
    {
        $user = User::create([
            'name' => 'Peminjam',
            'email' => 'peminjam@example.com',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('peminjam.dashboard'));

        $this->get('/')
            ->assertRedirect(route('peminjam.dashboard'));
    }

    public function test_guest_can_still_open_login_page(): void
    {
        $this->get(route('login'))
            ->assertOk();
    }
}

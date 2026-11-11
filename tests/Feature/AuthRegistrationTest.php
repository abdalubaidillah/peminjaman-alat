<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_registration_page_from_login(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('register'));

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Registrasi Peminjam');
    }

    public function test_registration_creates_and_logs_in_a_borrower_only(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Peminjam Baru',
            'email' => 'peminjam@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $user = User::where('email', 'peminjam@example.com')->firstOrFail();

        $response->assertRedirect(route('peminjam.dashboard'));
        $this->assertSame('peminjam', $user->role);
        $this->assertAuthenticatedAs($user);
        $this->assertNotSame('password123', $user->password);
    }

    public function test_registration_rejects_duplicate_email_and_mismatched_password(): void
    {
        User::create([
            'name' => 'Pengguna Lama',
            'email' => 'lama@example.com',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);

        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Peminjam Baru',
                'email' => 'lama@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password456',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['email', 'password']);
    }
}

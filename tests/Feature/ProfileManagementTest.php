<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_profile_page(): void
    {
        $user = User::create([
            'name' => 'Peminjam',
            'email' => 'peminjam@example.com',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Profil Saya')
            ->assertDontSee('Ubah Password')
            ->assertDontSee('name="password"')
            ->assertSee('Unggah foto')
            ->assertDontSee('No file chosen')
            ->assertDontSee('Pilih Foto Profil');
    }

    public function test_user_can_update_own_profile_without_changing_role(): void
    {
        $user = User::create([
            'name' => 'Nama Lama',
            'email' => 'lama@example.com',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Nama Baru',
                'email' => 'baru@example.com',
                'no_hp' => '08123456789',
                'alamat' => 'Jl. Contoh',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame('baru@example.com', $user->email);
        $this->assertSame('08123456789', $user->no_hp);
        $this->assertSame('Jl. Contoh', $user->alamat);
        $this->assertSame('peminjam', $user->role);
    }

    public function test_profile_page_requires_authentication(): void
    {
        $this->get(route('profile.edit'))
            ->assertRedirect(route('login'));
    }

    public function test_user_can_upload_own_profile_photo(): void
    {
        Storage::fake('public');
        $user = User::create([
            'name' => 'Peminjam',
            'email' => 'peminjam@example.com',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);

        $this->actingAs($user)
            ->post(route('profile.photo.upload'), [
                'foto_profile' => UploadedFile::fake()->image('profile.jpg'),
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertStringStartsWith('storage/profile/', $user->foto_profile);
        Storage::disk('public')->assertExists(substr($user->foto_profile, strlen('storage/')));
    }

    public function test_user_can_delete_own_profile_photo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('profile/current.jpg', 'photo');
        $user = User::create([
            'name' => 'Peminjam',
            'email' => 'peminjam@example.com',
            'password' => 'password123',
            'role' => 'peminjam',
            'foto_profile' => 'storage/profile/current.jpg',
        ]);

        $this->actingAs($user)
            ->delete(route('profile.photo.delete'))
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertNull($user->foto_profile);
        Storage::disk('public')->assertMissing('profile/current.jpg');
    }
}

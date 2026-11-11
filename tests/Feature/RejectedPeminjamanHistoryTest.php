<?php

namespace Tests\Feature;

use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RejectedPeminjamanHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_rejection_keeps_the_request_in_borrower_history(): void
    {
        $petugas = User::create([
            'name' => 'Petugas',
            'email' => 'petugas@example.com',
            'password' => 'password123',
            'role' => 'petugas',
        ]);
        $peminjam = User::create([
            'name' => 'Peminjam',
            'email' => 'peminjam@example.com',
            'password' => 'password123',
            'role' => 'peminjam',
        ]);
        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'tgl_pinjam' => now()->toDateString(),
            'tgl_kembali_plan' => now()->addDay()->toDateString(),
            'status' => 'diajukan',
        ]);

        $this->actingAs($petugas)
            ->post(route('petugas.peminjaman.tolak', $peminjaman->id))
            ->assertRedirect();

        $this->assertDatabaseHas('peminjaman', [
            'id' => $peminjaman->id,
            'user_id' => $peminjam->id,
            'status' => 'ditolak',
        ]);

        $this->actingAs($peminjam)
            ->get(route('peminjam.riwayat'))
            ->assertOk()
            ->assertSee('Peminjaman #' . $peminjaman->id)
            ->assertSee('Ditolak')
            ->assertSee('Pengajuan ditolak oleh petugas.');
    }
}

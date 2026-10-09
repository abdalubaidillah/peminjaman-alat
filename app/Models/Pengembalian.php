<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengembalian extends Model
{
    public const DENDA_PER_HARI = 5000;

    protected $table = 'pengembalian';

    protected $fillable = [
        'peminjaman_id',
        'tgl_kembali',
        'kondisi_kembali',
        'denda',
        'petugas_id',
    ];

    protected function casts(): array
    {
        return [
            'tgl_kembali' => 'date:Y-m-d',
            'denda' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $pengembalian): void {
            if ($pengembalian->denda !== null) {
                return;
            }

            $peminjaman = Peminjaman::find($pengembalian->peminjaman_id);

            $pengembalian->denda = self::hitungDendaTerlambat(
                $peminjaman?->tgl_kembali_plan,
                $pengembalian->tgl_kembali
            );
        });
    }

    public static function hitungDendaTerlambat($tanggalRencana, $tanggalAktual): int
    {
        if (!$tanggalRencana || !$tanggalAktual) {
            return 0;
        }

        $tanggalRencana = Carbon::parse($tanggalRencana)->startOfDay();
        $tanggalAktual = Carbon::parse($tanggalAktual)->startOfDay();

        if ($tanggalAktual->lessThanOrEqualTo($tanggalRencana)) {
            return 0;
        }

        return (int) $tanggalRencana->diffInDays($tanggalAktual) * self::DENDA_PER_HARI;
    }

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(Peminjaman::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
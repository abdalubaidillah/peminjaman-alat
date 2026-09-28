<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengembalian\StorePengembalianRequest;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    public function store(StorePengembalianRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $pengembalian = DB::transaction(function () use ($validated, $request) {
            $peminjaman = Peminjaman::with('detailPinjam')
                ->lockForUpdate()
                ->findOrFail($validated['peminjaman_id']);

            if ($peminjaman->status !== 'dikembalikan' && $peminjaman->status !== 'dipinjam') {
                abort(422, 'Peminjaman tidak sedang menunggu pengembalian.');
            }

            if ($peminjaman->pengembalian()->exists()) {
                abort(422, 'Pengembalian untuk peminjaman ini sudah dibuat.');
            }

            $pengembalian = Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $validated['kondisi_kembali'],
                'denda' => $validated['denda'] ?? 0,
                'petugas_id' => $request->user()->id,
            ]);

            foreach ($peminjaman->detailPinjam as $detail) {
                Alat::whereKey($detail->alat_id)->increment('stok', $detail->jumlah);
            }

            $peminjaman->update(['status' => 'selesai']);

            return $pengembalian->load(['peminjaman.user', 'petugas']);
        });

        return response()->json([
            'message' => 'Pengembalian berhasil dibuat dan terhubung ke peminjaman.',
            'data' => $pengembalian,
        ], 201);
    }
}
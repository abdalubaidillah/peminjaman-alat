<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Peminjaman\StorePeminjamanRequest;
use App\Models\DetailPinjam;
use App\Models\Peminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Resources\PeminjamanResource;

class PeminjamanController extends Controller
{
    public function riwayat(Request $request): JsonResponse
    {
        $peminjamans = Peminjaman::with([
            'user',
            'detailPinjam.alat',
            'pengembalian.petugas',
        ])
            ->where('user_id', $request->user()->id)
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Riwayat peminjaman berhasil diambil.',
            'data' => PeminjamanResource::collection($peminjamans),
        ]);
    }

    public function approve(int $id): JsonResponse
    {
        $peminjaman = DB::transaction(function () use ($id) {
            $peminjaman = Peminjaman::with('detailPinjam.alat')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($peminjaman->status !== 'diajukan') {
                abort(422, 'Peminjaman ini sudah diproses.');
            }

            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = $detail->alat;

                if ($alat->stok < $detail->jumlah) {
                    abort(422, "Stok alat '{$alat->nama_alat}' tidak mencukupi.");
                }

                $alat->decrement('stok', $detail->jumlah);
            }

            $peminjaman->update(['status' => 'dipinjam']);

            return $peminjaman->fresh(['user', 'detailPinjam.alat']);
        });

        return response()->json([
            'message' => 'Peminjaman berhasil disetujui.',
            'data' => $peminjaman,
        ]);
    }

    public function store(StorePeminjamanRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $peminjaman = DB::transaction(function () use ($validated, $request) {
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user()->id,
                'tgl_pinjam' => now(),
                'tgl_kembali_plan' => $validated['tgl_kembali_plan'] ?? null,
                'status' => 'diajukan',
            ]);

            foreach ($validated['items'] as $item) {
                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $item['alat_id'],
                    'jumlah' => $item['jumlah'],
                ]);
            }

            return $peminjaman->load(['user', 'detailPinjam.alat']);
        });

        return response()->json([
            'message' => 'Peminjaman berhasil dibuat.',
            'data' => $peminjaman,
        ], 201);
    }
}
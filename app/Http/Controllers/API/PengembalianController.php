<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengembalian\StorePengembalianRequest;
use App\Http\Resources\PeminjamanResource;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'status' => ['nullable', 'string', 'in:diajukan,dipinjam,dikembalikan,selesai,telat'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $peminjamans = Peminjaman::with([
            'user',
            'detailPinjam.alat',
            'pengembalian.petugas',
        ])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate($request->input('per_page', 15));

        return PeminjamanResource::collection($peminjamans)
            ->additional(['message' => 'Data pengembalian berhasil diambil.'])
            ->response();
    }

    public function show(int $id): JsonResponse
    {
        $peminjaman = Peminjaman::with([
            'user',
            'detailPinjam.alat',
            'pengembalian.petugas',
        ])
            ->findOrFail($id);

        return PeminjamanResource::make($peminjaman)
            ->additional(['message' => 'Detail pengembalian berhasil diambil.'])
            ->response();
    }

    public function store(StorePengembalianRequest $request, ?int $id = null): JsonResponse
    {
        $validated = $request->validated();
        $validated['peminjaman_id'] = $id ?? $validated['peminjaman_id'];

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

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'kondisi_kembali' => ['required', 'string', 'max:255'],
            'denda' => ['required', 'integer', 'min:0'],
        ]);

        $pengembalian = Pengembalian::findOrFail($id);
        $pengembalian->update($validated);

        return response()->json([
            'message' => 'Data pengembalian berhasil diperbarui.',
            'data' => $pengembalian->load(['peminjaman.user', 'petugas']),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $pengembalian = Pengembalian::findOrFail($id);
        $pengembalian->delete();

        return response()->json([
            'message' => 'Data pengembalian berhasil dihapus.',
        ]);
    }
}
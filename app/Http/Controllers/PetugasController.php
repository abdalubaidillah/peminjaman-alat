<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'menungguPersetujuan' => Peminjaman::where('status', 'diajukan')->count(),
            'sedangDipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'totalPengembalian' => Pengembalian::count(),
            'totalDenda' => Pengembalian::sum('denda'),
        ];

        $peminjamanTerbaru = Peminjaman::with('user')
            ->latest()
            ->limit(6)
            ->get();

        return view('petugas.dashboard', compact('stats', 'peminjamanTerbaru'));
    }

    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
    }

    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $pengembalians = Pengembalian::with(['peminjaman.user', 'petugas'])
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('kondisi_kembali', 'like', "%{$search}%")
                        ->orWhereHas('peminjaman.user', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('tgl_kembali')
            ->paginate(10)
            ->withQueryString();

        $peminjamanAktif = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'dipinjam')
            ->latest()
            ->get();

        return view('petugas.pengembalian.index', compact('pengembalians', 'peminjamanAktif', 'search'));
    }

    public function formLaporan()
    {
        $peminjamans = Peminjaman::with(['detailPinjam', 'pengembalian'])->get();
        $rekapStatus = $peminjamans->groupBy('status')->map(fn ($items) => $items->count());
        $totalAlat = $peminjamans->sum(fn ($peminjaman) => $peminjaman->detailPinjam->sum('jumlah'));
        $totalDenda = $peminjamans->sum(fn ($peminjaman) => $peminjaman->pengembalian?->denda ?? 0);

        return view('petugas.laporan.index', compact('peminjamans', 'rekapStatus', 'totalAlat', 'totalDenda'));
    }

    public function cetakLaporan(Request $request)
    {
        $status = $request->input('status');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian.petugas'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($tanggalMulai, fn ($query) => $query->whereDate('tgl_pinjam', '>=', $tanggalMulai))
            ->when($tanggalSelesai, fn ($query) => $query->whereDate('tgl_pinjam', '<=', $tanggalSelesai))
            ->latest('tgl_pinjam')
            ->get();

        $rekapStatus = $peminjamans->groupBy('status')->map(fn ($items) => $items->count());
        $totalAlat = $peminjamans->sum(fn ($peminjaman) => $peminjaman->detailPinjam->sum('jumlah'));
        $totalDenda = $peminjamans->sum(fn ($peminjaman) => $peminjaman->pengembalian?->denda ?? 0);

        $pdf = Pdf::loadView('petugas.laporan.pdf', compact(
            'peminjamans',
            'status',
            'tanggalMulai',
            'tanggalSelesai',
            'rekapStatus',
            'totalAlat',
            'totalDenda'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('laporan-peminjaman-' . now()->format('Y-m-d') . '.pdf');
    }

    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPinjam.alat')->findOrFail($id);

            if ($peminjaman->status !== 'diajukan') {
                throw new \Exception('Peminjaman ini sudah diproses.');
            }

            foreach ($peminjaman->detailPinjam as $detail) {
                if ($detail->alat->stok < $detail->jumlah) {
                    throw new \Exception("Stok alat '{$detail->alat->nama_alat}' tidak mencukupi.");
                }

                $detail->alat->decrement('stok', $detail->jumlah);
            }

            $peminjaman->update(['status' => 'dipinjam']);
            DB::commit();

            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function tolakPeminjaman($id)
    {
        $peminjaman = Peminjaman::where('status', 'diajukan')->findOrFail($id);
        $peminjaman->delete();

        return redirect()->back()->with('success', 'Pengajuan peminjaman ditolak.');
    }

    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($peminjamanId);

            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $request->denda ?? 0,
                'petugas_id' => auth()->id(),
            ]);

            $peminjaman->update(['status' => 'selesai']);

            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->increment('stok', $detail->jumlah);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Pengembalian berhasil dicatat dan stok dipulihkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

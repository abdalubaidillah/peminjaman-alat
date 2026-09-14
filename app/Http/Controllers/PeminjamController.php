<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    // Beranda ringkas untuk peminjam
    public function beranda()
    {
        $userId = auth()->id();

        $stats = [
            'diajukan' => Peminjaman::where('user_id', $userId)->where('status', 'diajukan')->count(),
            'dipinjam' => Peminjaman::where('user_id', $userId)->where('status', 'dipinjam')->count(),
            'selesai'  => Peminjaman::where('user_id', $userId)->where('status', 'selesai')->count(),
        ];

        $alatPopuler = Alat::with('kategori')->tersedia()->latest()->limit(4)->get();

        $pinjamanAktif = Peminjaman::with('detailPinjam.alat')
            ->where('user_id', $userId)
            ->whereIn('status', ['diajukan', 'dipinjam', 'dikembalikan'])
            ->latest()
            ->limit(3)
            ->get();

        return view('peminjam.beranda', compact('stats', 'alatPopuler', 'pinjamanAktif'));
    }

    // Melihat daftar/katalog alat yang tersedia
    public function katalogAlat(Request $request)
    {
        $search = $request->input('search');
        $kategoriId = $request->input('kategori');

        $alats = Alat::with('kategori')
            ->tersedia()
            ->when($search, function ($query, $search) {
                $query->where('nama_alat', 'like', "%{$search}%");
            })
            ->when($kategoriId, function ($query, $kategoriId) {
                $query->where('kategori_id', $kategoriId);
            })
            ->orderBy('nama_alat')
            ->get();

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('peminjam.katalog', compact('alats', 'kategoris', 'search', 'kategoriId'));
    }

    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'tgl_kembali_plan' => 'required|date|after:today',
            'alat_id'          => 'required|array|min:1',
            'alat_id.*'        => 'required|integer|exists:alat,id',
            'jumlah'           => 'required|array',
            'jumlah.*'         => 'required|integer|min:1',
        ], [
            'alat_id.required' => 'Pilih minimal satu alat yang ingin dipinjam.',
            'alat_id.min'      => 'Pilih minimal satu alat yang ingin dipinjam.',
        ]);

        // Gabungkan jika ada alat_id yang sama dipilih lebih dari sekali
        $items = [];
        foreach ($request->alat_id as $index => $alatId) {
            $jumlah = (int) ($request->jumlah[$index] ?? 0);
            if ($jumlah < 1) {
                continue;
            }
            $items[$alatId] = ($items[$alatId] ?? 0) + $jumlah;
        }

        if (empty($items)) {
            return redirect()->back()->with('error', 'Pilih minimal satu alat dengan jumlah yang valid.');
        }

        DB::beginTransaction();

        try {
            // Kunci baris alat supaya stok tidak berubah saat validasi berjalan
            $alats = Alat::whereIn('id', array_keys($items))->lockForUpdate()->get()->keyBy('id');

            foreach ($items as $alatId => $jumlah) {
                $alat = $alats->get($alatId);

                if (!$alat) {
                    throw new \Exception('Salah satu alat tidak ditemukan.');
                }

                if ($alat->stok < $jumlah) {
                    throw new \Exception("Stok '{$alat->nama_alat}' tersisa {$alat->stok}, tidak mencukupi permintaan.");
                }
            }

            $peminjaman = Peminjaman::create([
                'user_id'          => auth()->id(),
                'tgl_pinjam'       => now(),
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status'           => 'diajukan',
            ]);

            foreach ($items as $alatId => $jumlah) {
                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id'       => $alatId,
                    'jumlah'        => $jumlah,
                ]);
            }

            LogAktivitas::create([
                'user_id'   => auth()->id(),
                'aktivitas' => 'Mengajukan peminjaman #' . $peminjaman->id,
            ]);

            DB::commit();

            return redirect()->route('peminjam.peminjaman.invoice', $peminjaman->id);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    public function invoicePeminjaman($id)
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view('peminjam.invoice', compact('peminjaman'));
    }

    // Melihat riwayat peminjaman user yang sedang login
    public function riwayatPeminjaman(Request $request)
    {
        $status = $request->input('status');

        $peminjamans = Peminjaman::with(['detailPinjam.alat', 'pengembalian'])
            ->where('user_id', auth()->id())
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->get();

        return view('peminjam.riwayat', compact('peminjamans', 'status'));
    }

    // Peminjam menandai alat sudah dikembalikan secara fisik, menunggu verifikasi petugas
    public function ajukanPengembalian($id)
    {
        $peminjaman = Peminjaman::where('user_id', auth()->id())->findOrFail($id);

        if ($peminjaman->status !== 'dipinjam') {
            return redirect()->back()->with('error', 'Peminjaman ini tidak dapat diajukan pengembaliannya.');
        }

        $peminjaman->update(['status' => 'dikembalikan']);

        LogAktivitas::create([
            'user_id'   => auth()->id(),
            'aktivitas' => 'Mengajukan pengembalian alat untuk peminjaman #' . $peminjaman->id,
        ]);

        return redirect()->back()->with('success', 'Pengembalian diajukan. Silakan serahkan alat ke petugas untuk diverifikasi.');
    }
}
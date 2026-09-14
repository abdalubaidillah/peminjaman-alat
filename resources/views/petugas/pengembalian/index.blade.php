@extends('layouts.app')

@section('title', 'Pengembalian - Dashboard Petugas')
@section('header-title', 'Pemantauan Pengembalian')

@section('content')
    @if(session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm text-gray-500">Pantau alat yang masih dipinjam dan catat pengembalian saat diterima.</p>
        </div>
    </div>

    <section class="mb-6 overflow-hidden rounded-xl border border-blue-200 bg-white shadow-sm">
        <div class="border-b border-blue-100 bg-blue-50 px-5 py-4">
            <h2 class="font-bold text-blue-950">Sedang dipinjam</h2>
            <p class="mt-1 text-xs text-blue-700">Pilih aksi catat ketika alat sudah dikembalikan.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                    <tr><th class="px-5 py-3">Peminjam</th><th class="px-5 py-3">Tanggal pinjam</th><th class="px-5 py-3">Alat</th><th class="px-5 py-3">Aksi</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($peminjamanAktif as $item)
                        <tr class="align-top">
                            <td class="px-5 py-4 font-semibold text-gray-900">{{ $item->user->name ?? 'User Dihapus' }}</td>
                            <td class="px-5 py-4">{{ $item->tgl_pinjam?->format('d-m-Y') ?? '-' }}</td>
                            <td class="px-5 py-4">
                                @foreach($item->detailPinjam as $detail)
                                    <div>{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} <span class="text-xs text-gray-500">({{ $detail->jumlah }} pcs)</span></div>
                                @endforeach
                            </td>
                            <td class="px-5 py-4">
                                <details class="w-64">
                                    <summary class="cursor-pointer rounded-lg bg-blue-600 px-3 py-2 text-center text-xs font-semibold text-white hover:bg-blue-700">Catat pengembalian</summary>
                                    <form action="{{ route('petugas.pengembalian.proses', $item->id) }}" method="POST" class="mt-3 space-y-2 rounded-lg border border-gray-200 bg-gray-50 p-3">
                                        @csrf
                                        <select name="kondisi_kembali" required class="w-full rounded border border-gray-300 px-2 py-2 text-xs">
                                            <option value="Baik">Baik</option>
                                            <option value="Rusak Ringan">Rusak Ringan</option>
                                            <option value="Rusak Berat">Rusak Berat</option>
                                        </select>
                                        <input type="number" name="denda" min="0" value="0" placeholder="Denda" class="w-full rounded border border-gray-300 px-2 py-2 text-xs">
                                        <button type="submit" onclick="return confirm('Simpan data pengembalian ini?')" class="w-full rounded bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Simpan</button>
                                    </form>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">Tidak ada alat yang sedang dipinjam.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-200 bg-gray-50 p-5 md:flex-row md:items-center md:justify-between">
            <div><h2 class="font-bold text-gray-900">Riwayat pengembalian</h2><p class="mt-1 text-xs text-gray-500">Data pengembalian yang sudah dicatat petugas.</p></div>
            <form action="{{ route('petugas.pengembalian.index') }}" method="GET" class="flex w-full md:w-80">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari peminjam atau kondisi..." class="w-full rounded-l-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="rounded-r-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Cari</button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-100 text-xs uppercase tracking-wider text-gray-500"><tr><th class="px-5 py-3">Peminjam</th><th class="px-5 py-3">Tanggal kembali</th><th class="px-5 py-3">Kondisi</th><th class="px-5 py-3">Denda</th><th class="px-5 py-3">Petugas</th></tr></thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($pengembalians as $pengembalian)
                        <tr><td class="px-5 py-3 font-medium text-gray-900">{{ $pengembalian->peminjaman->user->name ?? 'User Dihapus' }}</td><td class="px-5 py-3">{{ $pengembalian->tgl_kembali?->format('d-m-Y') ?? '-' }}</td><td class="px-5 py-3">{{ $pengembalian->kondisi_kembali }}</td><td class="px-5 py-3">Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}</td><td class="px-5 py-3">{{ $pengembalian->petugas->name ?? 'Petugas Dihapus' }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-500">Belum ada riwayat pengembalian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-200 bg-gray-50 p-4">{{ $pengembalians->links() }}</div>
    </section>
@endsection

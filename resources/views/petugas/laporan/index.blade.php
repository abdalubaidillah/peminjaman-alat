@extends('layouts.app')

@section('title', 'Cetak Laporan - Dashboard Petugas')
@section('header-title', 'Cetak Laporan')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <p class="text-sm font-semibold uppercase tracking-wider text-emerald-600">Laporan Peminjaman</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">Pilih data yang ingin dicetak</h1>
            <p class="mt-1 text-sm text-gray-500">Gunakan filter berikut untuk membuat laporan PDF sesuai kebutuhan.</p>
        </div>

        <form action="{{ route('petugas.laporan.cetak') }}" method="POST" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            @csrf
            <div class="grid gap-5 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="status" class="mb-2 block text-sm font-semibold text-gray-700">Status peminjaman</label>
                    <select id="status" name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua status</option>
                        <option value="diajukan">Diajukan</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="dipinjam">Dipinjam</option>
                        <option value="dikembalikan">Dikembalikan</option>
                        <option value="ditolak">Ditolak</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <div>
                    <label for="tanggal_mulai" class="mb-2 block text-sm font-semibold text-gray-700">Tanggal mulai</label>
                    <input id="tanggal_mulai" type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="tanggal_selesai" class="mb-2 block text-sm font-semibold text-gray-700">Tanggal selesai</label>
                    <input id="tanggal_selesai" type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                <a href="{{ route('petugas.dashboard') }}" class="rounded-lg border border-gray-300 px-4 py-2.5 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50">Kembali</a>
                <button type="submit" class="rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-gray-700">Unduh laporan PDF</button>
            </div>
        </form>

        <section class="mt-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-4">
                <h2 class="font-bold text-gray-900">Rekap laporan</h2>
                <p class="mt-1 text-sm text-gray-500">Ringkasan seluruh data peminjaman yang tersedia.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                    <p class="text-sm font-medium text-blue-700">Total transaksi</p>
                    <p class="mt-2 text-2xl font-bold text-blue-950">{{ $peminjamans->count() }}</p>
                </div>
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                    <p class="text-sm font-medium text-emerald-700">Total unit alat</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-950">{{ $totalAlat }}</p>
                </div>
                <div class="rounded-lg border border-rose-200 bg-rose-50 p-4">
                    <p class="text-sm font-medium text-rose-700">Total denda</p>
                    <p class="mt-2 text-2xl font-bold text-rose-950">Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="mt-5 overflow-hidden rounded-lg border border-gray-200">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                        <tr><th class="px-4 py-3">Status</th><th class="px-4 py-3">Jumlah transaksi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($rekapStatus as $statusNama => $jumlah)
                            <tr><td class="px-4 py-3 font-medium text-gray-900">{{ ucfirst($statusNama) }}</td><td class="px-4 py-3">{{ $jumlah }}</td></tr>
                        @empty
                            <tr><td colspan="2" class="px-4 py-4 text-center text-gray-500">Belum ada data peminjaman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
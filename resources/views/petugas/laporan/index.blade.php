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
    </div>
@endsection
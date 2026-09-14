@extends('layouts.app')

@section('title', 'Dashboard Petugas - Sistem Peminjaman')
@section('header-title', 'Dashboard Petugas')

@section('content')
    <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-emerald-600">Pusat Operasional</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">Halo, {{ auth()->user()->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola persetujuan dan pengembalian alat dari satu tempat.</p>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('petugas.peminjaman.index') }}" class="rounded-xl border border-amber-200 bg-amber-50 p-5 transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-sm font-medium text-amber-700">Menunggu persetujuan</p>
            <p class="mt-3 text-3xl font-bold text-amber-950">{{ $stats['menungguPersetujuan'] }}</p>
            <p class="mt-2 text-xs text-amber-700">Pengajuan perlu diperiksa</p>
        </a>
        <a href="{{ route('petugas.pengembalian.index') }}" class="rounded-xl border border-blue-200 bg-blue-50 p-5 transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-sm font-medium text-blue-700">Sedang dipinjam</p>
            <p class="mt-3 text-3xl font-bold text-blue-950">{{ $stats['sedangDipinjam'] }}</p>
            <p class="mt-2 text-xs text-blue-700">Menunggu dikembalikan</p>
        </a>
        <a href="{{ route('petugas.pengembalian.index') }}" class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 transition hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-sm font-medium text-emerald-700">Total pengembalian</p>
            <p class="mt-3 text-3xl font-bold text-emerald-950">{{ $stats['totalPengembalian'] }}</p>
            <p class="mt-2 text-xs text-emerald-700">Riwayat tercatat</p>
        </a>
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-5">
            <p class="text-sm font-medium text-rose-700">Total denda</p>
            <p class="mt-3 text-2xl font-bold text-rose-950">Rp {{ number_format($stats['totalDenda'], 0, ',', '.') }}</p>
            <p class="mt-2 text-xs text-rose-700">Akumulasi seluruh riwayat</p>
        </div>
    </div>

    <div class="mt-6">
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <div>
                    <h2 class="font-bold text-gray-900">Aktivitas peminjaman terbaru</h2>
                    <p class="mt-1 text-xs text-gray-500">Status terakhir dari pengajuan yang masuk.</p>
                </div>
                <a href="{{ route('petugas.peminjaman.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Lihat pengajuan</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Peminjam</th>
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($peminjamanTerbaru as $item)
                            <tr>
                                <td class="px-5 py-3 font-medium text-gray-900">{{ $item->user->name ?? 'User Dihapus' }}</td>
                                <td class="px-5 py-3">{{ $item->tgl_pinjam?->format('d-m-Y') ?? '-' }}</td>
                                <td class="px-5 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $item->status === 'diajukan' ? 'bg-amber-100 text-amber-800' : ($item->status === 'dipinjam' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800') }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-8 text-center text-gray-500">Belum ada aktivitas peminjaman.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection

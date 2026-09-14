@extends('layouts.peminjam')

@section('title', 'Beranda - PinjamAlat')
@section('header-title', 'Halo, ' . explode(' ', auth()->user()->name)[0] . ' 👋')

@section('content')

    <div class="mb-5 rounded-3xl bg-gradient-to-br from-emerald-500 to-teal-500 p-5 text-white shadow-soft">
        <p class="text-sm text-emerald-50">Butuh alat untuk kegiatanmu?</p>
        <p class="mt-1 text-lg font-bold">Cari &amp; ajukan peminjaman dalam sekali tap.</p>
        <a href="{{ route('peminjam.katalog') }}" class="mt-4 inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-emerald-700 shadow hover:bg-emerald-50">
            Lihat Katalog Alat
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <!-- STATS -->
    <div class="mb-6 grid grid-cols-3 gap-3">
        <div class="rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-gray-100">
            <p class="text-2xl font-extrabold text-amber-500">{{ $stats['diajukan'] }}</p>
            <p class="mt-1 text-[11px] font-medium text-gray-500">Diajukan</p>
        </div>
        <div class="rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-gray-100">
            <p class="text-2xl font-extrabold text-blue-500">{{ $stats['dipinjam'] }}</p>
            <p class="mt-1 text-[11px] font-medium text-gray-500">Sedang Dipinjam</p>
        </div>
        <div class="rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-gray-100">
            <p class="text-2xl font-extrabold text-emerald-500">{{ $stats['selesai'] }}</p>
            <p class="mt-1 text-[11px] font-medium text-gray-500">Selesai</p>
        </div>
    </div>

    <!-- PEMINJAMAN AKTIF -->
    <div class="mb-6">
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Peminjaman Aktif</h2>
            <a href="{{ route('peminjam.riwayat') }}" class="text-xs font-semibold text-emerald-600 hover:underline">Lihat semua</a>
        </div>

        @forelse($pinjamanAktif as $p)
            <div class="mb-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400">#{{ $p->id }} &middot; {{ $p->tgl_pinjam?->format('d M Y') }}</p>
                        <p class="mt-1 text-sm font-semibold text-gray-800">
                            {{ $p->detailPinjam->pluck('alat.nama_alat')->filter()->join(', ') ?: 'Alat tidak tersedia' }}
                        </p>
                    </div>
                    @php
                        $badge = [
                            'diajukan'     => 'bg-amber-100 text-amber-800',
                            'dipinjam'     => 'bg-blue-100 text-blue-800',
                            'dikembalikan' => 'bg-purple-100 text-purple-800',
                        ][$p->status] ?? 'bg-gray-100 text-gray-700';
                        $label = [
                            'diajukan'     => 'Menunggu Persetujuan',
                            'dipinjam'     => 'Sedang Dipinjam',
                            'dikembalikan' => 'Menunggu Verifikasi',
                        ][$p->status] ?? $p->status;
                    @endphp
                    <span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $badge }}">{{ $label }}</span>
                </div>

                @if($p->status === 'dipinjam')
                    <form action="{{ route('peminjam.pengembalian.ajukan', $p->id) }}" method="POST" class="mt-3" onsubmit="return confirm('Ajukan pengembalian untuk peminjaman ini?')">
                        @csrf
                        <button type="submit" class="w-full rounded-xl bg-emerald-600 py-2 text-xs font-semibold text-white hover:bg-emerald-700">
                            Kembalikan Alat
                        </button>
                    </form>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-6 text-center text-sm text-gray-400">
                Belum ada peminjaman aktif.
            </div>
        @endforelse
    </div>

    <!-- ALAT TERBARU -->
    <div>
        <div class="mb-3 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Alat Tersedia</h2>
            <a href="{{ route('peminjam.katalog') }}" class="text-xs font-semibold text-emerald-600 hover:underline">Lihat semua</a>
        </div>

        <div class="grid grid-cols-2 gap-3">
            @forelse($alatPopuler as $alat)
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">
                    <div class="flex h-24 items-center justify-center bg-emerald-50">
                        @if($alat->gambar)
                            <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="h-full w-full object-cover">
                        @else
                            <span class="text-3xl">🧰</span>
                        @endif
                    </div>
                    <div class="p-3">
                        <p class="truncate text-sm font-semibold text-gray-800">{{ $alat->nama_alat }}</p>
                        <p class="text-[11px] text-gray-400">{{ $alat->kategori->nama_kategori ?? '-' }}</p>
                        <span class="mt-1 inline-block rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">Stok {{ $alat->stok }}</span>
                    </div>
                </div>
            @empty
                <p class="col-span-2 text-center text-sm text-gray-400">Belum ada alat tersedia.</p>
            @endforelse
        </div>
    </div>

@endsection
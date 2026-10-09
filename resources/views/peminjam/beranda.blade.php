@extends('layouts.app')

@section('title', 'Dashboard Peminjam - Sistem Peminjaman')
@section('header-title', 'Dashboard Peminjam')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-emerald-700">Ringkasan aktivitas</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">Halo, {{ auth()->user()->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Lihat status peminjaman atau temukan alat yang tersedia.</p>
        </div>
        <a href="{{ route('peminjam.katalog') }}" class="rounded-md bg-emerald-700 px-4 py-2.5 text-center text-sm font-semibold text-white transition hover:bg-emerald-800">Jelajahi katalog</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-3">
        <a href="{{ route('peminjam.riwayat', ['status' => 'diajukan']) }}" class="rounded-lg border border-amber-200 bg-amber-50 p-5 transition hover:shadow-sm">
            <p class="text-sm font-medium text-amber-800">Menunggu persetujuan</p>
            <p class="mt-3 text-3xl font-bold text-amber-950">{{ $stats['diajukan'] }}</p>
            <p class="mt-2 text-xs text-amber-800">Pengajuan sedang diperiksa</p>
        </a>
        <a href="{{ route('peminjam.riwayat', ['status' => 'dipinjam']) }}" class="rounded-lg border border-blue-200 bg-blue-50 p-5 transition hover:shadow-sm">
            <p class="text-sm font-medium text-blue-800">Sedang dipinjam</p>
            <p class="mt-3 text-3xl font-bold text-blue-950">{{ $stats['dipinjam'] }}</p>
            <p class="mt-2 text-xs text-blue-800">Alat yang masih digunakan</p>
        </a>
        <a href="{{ route('peminjam.riwayat', ['status' => 'selesai']) }}" class="rounded-lg border border-emerald-200 bg-emerald-50 p-5 transition hover:shadow-sm">
            <p class="text-sm font-medium text-emerald-800">Selesai</p>
            <p class="mt-3 text-3xl font-bold text-emerald-950">{{ $stats['selesai'] }}</p>
            <p class="mt-2 text-xs text-emerald-800">Peminjaman yang telah tuntas</p>
        </a>
    </div>

    <section class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <div>
                <h2 class="font-bold text-gray-900">Peminjaman aktif</h2>
                <p class="mt-1 text-xs text-gray-500">Pengajuan dan alat yang sedang Anda gunakan.</p>
            </div>
            <a href="{{ route('peminjam.riwayat') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Lihat riwayat</a>
        </div>
        @forelse($pinjamanAktif as $peminjaman)
            <div class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 last:border-b-0 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="font-semibold text-gray-900">Peminjaman #{{ $peminjaman->id }}</h3>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $peminjaman->status === 'diajukan' ? 'bg-amber-100 text-amber-800' : ($peminjaman->status === 'dipinjam' ? 'bg-blue-100 text-blue-800' : 'bg-cyan-100 text-cyan-800') }}">{{ ucfirst($peminjaman->status) }}</span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">{{ $peminjaman->tgl_pinjam?->format('d-m-Y') ?? '-' }} · Kembali {{ $peminjaman->tgl_kembali_plan?->format('d-m-Y') ?? '-' }}</p>
                    <p class="mt-2 text-sm text-gray-700">{{ $peminjaman->detailPinjam->map(fn ($detail) => ($detail->alat->nama_alat ?? 'Alat Dihapus') . ' (' . $detail->jumlah . ')')->join(', ') }}</p>
                </div>
                @if($peminjaman->status === 'dipinjam')
                    <form action="{{ route('peminjam.pengembalian.ajukan', $peminjaman->id) }}" method="POST" onsubmit="return confirm('Ajukan pengembalian alat ini?')">
                        @csrf
                        <button type="submit" class="whitespace-nowrap rounded-md border border-emerald-700 px-3 py-2 text-sm font-semibold text-emerald-800 hover:bg-emerald-50">Ajukan pengembalian</button>
                    </form>
                @endif
            </div>
        @empty
            <div class="px-5 py-8 text-center">
                <p class="font-medium text-gray-800">Belum ada peminjaman aktif</p>
                <p class="mt-1 text-sm text-gray-500">Pilih alat dari katalog untuk memulai pengajuan.</p>
                <a href="{{ route('peminjam.katalog') }}" class="mt-4 inline-flex rounded-md border border-emerald-700 px-4 py-2 text-sm font-semibold text-emerald-800 hover:bg-emerald-50">Buka katalog</a>
            </div>
        @endforelse
    </section>

    <section class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <div>
                <h2 class="font-bold text-gray-900">Alat tersedia</h2>
                <p class="mt-1 text-xs text-gray-500">Pilihan alat dengan stok dan kondisi baik.</p>
            </div>
            <a href="{{ route('peminjam.katalog') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Lihat katalog</a>
        </div>
        <div class="grid gap-4 p-5 sm:grid-cols-2 xl:grid-cols-4">
            @forelse($alatPopuler as $alat)
                <article class="overflow-hidden rounded-lg border border-gray-200">
                    @if($alat->gambar)
                        <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="h-36 w-full bg-gray-100 object-cover">
                    @else
                        <div class="flex h-36 items-center justify-center bg-gray-100 text-xs font-bold tracking-widest text-gray-400">ALAT</div>
                    @endif
                    <div class="p-3">
                        <h3 class="truncate text-sm font-semibold text-gray-900">{{ $alat->nama_alat }}</h3>
                        <p class="mt-1 truncate text-xs text-gray-500">{{ $alat->kategori->nama_kategori ?? 'Tanpa kategori' }}</p>
                        <p class="mt-3 text-xs font-semibold text-emerald-700">{{ $alat->stok }} tersedia</p>
                    </div>
                </article>
            @empty
                <p class="col-span-full py-5 text-center text-sm text-gray-500">Belum ada alat yang tersedia saat ini.</p>
            @endforelse
        </div>
    </section>
@endsection
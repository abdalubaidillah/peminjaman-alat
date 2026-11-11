@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - Panel Peminjam')
@section('header-title', 'Riwayat Peminjaman')

@section('content')
    @if(session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif

    @php
        $jumlahDiajukan = $peminjamans->where('status', 'diajukan')->count();
        $jumlahDipinjam = $peminjamans->where('status', 'dipinjam')->count();
        $jumlahSelesai = $peminjamans->where('status', 'selesai')->count();
        $jumlahDitolak = $peminjamans->where('status', 'ditolak')->count();
    @endphp

    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-emerald-700">Aktivitas Anda</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">Riwayat peminjaman</h1>
            <p class="mt-1 text-sm text-gray-500">Pantau status pengajuan dan pengembalian alat.</p>
        </div>
        <a href="{{ route('peminjam.katalog') }}" class="rounded-md bg-gray-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-gray-700">Cari alat</a>
    </div>

    <div class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-5"><p class="text-sm font-medium text-amber-800">Menunggu persetujuan</p><p class="mt-2 text-3xl font-bold text-amber-950">{{ $jumlahDiajukan }}</p></div>
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-5"><p class="text-sm font-medium text-blue-800">Sedang dipinjam</p><p class="mt-2 text-3xl font-bold text-blue-950">{{ $jumlahDipinjam }}</p></div>
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-5"><p class="text-sm font-medium text-emerald-800">Selesai</p><p class="mt-2 text-3xl font-bold text-emerald-950">{{ $jumlahSelesai }}</p></div>
        <div class="rounded-lg border border-rose-200 bg-rose-50 p-5"><p class="text-sm font-medium text-rose-800">Ditolak</p><p class="mt-2 text-3xl font-bold text-rose-950">{{ $jumlahDitolak }}</p></div>
    </div>

    <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-gray-900">Daftar aktivitas</h2>
                <p class="mt-1 text-xs text-gray-500">Peminjaman terbaru ditampilkan lebih dulu.</p>
            </div>
            <form action="{{ route('peminjam.riwayat') }}" method="GET" class="flex items-center gap-2">
                <label for="status" class="text-sm font-medium text-gray-600">Status</label>
                <select id="status" name="status" onchange="this.form.submit()" class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                    <option value="">Semua status</option>
                    <option value="diajukan" @selected($status === 'diajukan')>Diajukan</option>
                    <option value="dipinjam" @selected($status === 'dipinjam')>Dipinjam</option>
                    <option value="dikembalikan" @selected($status === 'dikembalikan')>Menunggu verifikasi</option>
                    <option value="selesai" @selected($status === 'selesai')>Selesai</option>
                    <option value="telat" @selected($status === 'telat')>Telat</option>
                    <option value="ditolak" @selected($status === 'ditolak')>Ditolak</option>
                </select>
            </form>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($peminjamans as $peminjaman)
                @php
                    $statusClass = match ($peminjaman->status) {
                        'diajukan' => 'bg-amber-100 text-amber-800',
                        'dipinjam' => 'bg-blue-100 text-blue-800',
                        'dikembalikan' => 'bg-cyan-100 text-cyan-800',
                        'selesai' => 'bg-emerald-100 text-emerald-800',
                        'telat' => 'bg-rose-100 text-rose-800',
                        'ditolak' => 'bg-rose-100 text-rose-800',
                        default => 'bg-gray-100 text-gray-700',
                    };
                @endphp
                <article class="p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-bold text-gray-900">Peminjaman #{{ $peminjaman->id }}</h3>
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($peminjaman->status) }}</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Diajukan {{ $peminjaman->tgl_pinjam?->format('d-m-Y') ?? '-' }} · Rencana kembali {{ $peminjaman->tgl_kembali_plan?->format('d-m-Y') ?? '-' }}</p>
                        </div>
                        @if($peminjaman->pengembalian)
                            <p class="text-sm font-medium text-emerald-700">Dikembalikan {{ $peminjaman->pengembalian->tgl_kembali?->format('d-m-Y') ?? '-' }}</p>
                        @elseif($peminjaman->status === 'dikembalikan')
                            <p class="text-sm font-medium text-cyan-700">Menunggu verifikasi petugas</p>
                        @elseif($peminjaman->status === 'dipinjam')
                            <form action="{{ route('peminjam.pengembalian.ajukan', $peminjaman->id) }}" method="POST" onsubmit="return confirm('Ajukan pengembalian alat ini?')">
                                @csrf
                                <button type="submit" class="rounded-md border border-emerald-700 px-3 py-2 text-sm font-semibold text-emerald-800 hover:bg-emerald-50">Ajukan pengembalian</button>
                            </form>
                        @elseif($peminjaman->status === 'ditolak')
                            <p class="text-sm font-medium text-rose-700">Pengajuan ditolak oleh petugas.</p>
                        @endif
                    </div>
                    <div class="mt-4 border-t border-gray-100 pt-3">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Alat yang dipinjam</p>
                        <ul class="space-y-1 text-sm text-gray-700">
                            @foreach($peminjaman->detailPinjam as $detail)
                                <li>{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} <span class="text-gray-500">· {{ $detail->jumlah }} unit</span></li>
                            @endforeach
                        </ul>
                    </div>
                </article>
            @empty
                <div class="px-5 py-12 text-center">
                    <h3 class="font-semibold text-gray-900">Belum ada riwayat</h3>
                    <p class="mt-1 text-sm text-gray-500">Pengajuan peminjaman Anda akan muncul di sini.</p>
                    <a href="{{ route('peminjam.katalog') }}" class="mt-4 inline-flex rounded-md bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Jelajahi katalog</a>
                </div>
            @endforelse
        </div>
    </section>
@endsection

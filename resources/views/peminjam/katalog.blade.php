@extends('layouts.app')

@section('title', 'Katalog Alat - Panel Peminjam')
@section('header-title', 'Katalog Alat')

@section('content')
    @if(session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <ul class="list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-emerald-700">Temukan perlengkapan</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">Pilih alat yang Anda butuhkan</h1>
            <p class="mt-1 text-sm text-gray-500">Pilih alat dan jumlah, lalu tentukan tanggal rencana kembali.</p>
        </div>
        <a href="{{ route('peminjam.riwayat') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Lihat riwayat peminjaman</a>
    </div>

    <form action="{{ route('peminjam.katalog') }}" method="GET" class="mb-5 flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm sm:flex-row">
        <label class="flex-1">
            <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Cari alat</span>
            <input type="search" name="search" value="{{ $search }}" placeholder="Nama alat..." class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-100">
        </label>
        <label class="sm:w-56">
            <span class="mb-1 block text-xs font-semibold uppercase tracking-wide text-gray-500">Kategori</span>
            <select name="kategori" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                <option value="">Semua kategori</option>
                @foreach($kategoris as $kategori)
                    <option value="{{ $kategori->id }}" @selected((string) $kategoriId === (string) $kategori->id)>{{ $kategori->nama_kategori }}</option>
                @endforeach
            </select>
        </label>
        <div class="flex items-end gap-2">
            <button type="submit" class="rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">Cari</button>
            @if($search || $kategoriId)
                <a href="{{ route('peminjam.katalog') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Reset</a>
            @endif
        </div>
    </form>

    <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST">
        @csrf
        <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-1 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="font-bold text-gray-900">Alat tersedia</h2>
                    <p class="mt-1 text-xs text-gray-500">{{ $alats->count() }} alat ditemukan</p>
                </div>
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <span class="font-medium">Rencana kembali</span>
                    <input type="date" name="tgl_kembali_plan" value="{{ old('tgl_kembali_plan') }}" min="{{ now()->addDay()->format('Y-m-d') }}" required class="rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-100">
                </label>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="w-14 px-5 py-3">Pilih</th>
                            <th class="px-5 py-3">Nama alat</th>
                            <th class="px-5 py-3">Kategori</th>
                            <th class="px-5 py-3">Kondisi</th>
                            <th class="px-5 py-3">Stok</th>
                            <th class="w-36 px-5 py-3">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($alats as $alat)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3"><input type="checkbox" name="alat_id[]" value="{{ $alat->id }}" @checked(in_array($alat->id, old('alat_id', []))) class="h-4 w-4 rounded border-gray-300 text-emerald-700 focus:ring-emerald-600"></td>
                                <td class="px-5 py-3 font-semibold text-gray-900">
                                    <div class="flex min-w-0 items-center gap-3">
                                        @if($alat->gambar)
                                            <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="h-12 w-12 shrink-0 rounded-lg border border-gray-200 object-cover">
                                        @else
                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg border border-dashed border-gray-300 text-center text-[10px] font-medium text-gray-400">Tidak ada</div>
                                        @endif
                                        <span class="break-words">{{ $alat->nama_alat }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3">{{ $alat->kategori->nama_kategori ?? '-' }}</td>
                                <td class="px-5 py-3">{{ $alat->status_kondisi }}</td>
                                <td class="px-5 py-3"><span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800">{{ $alat->stok }} tersedia</span></td>
                                <td class="px-5 py-3"><input type="number" name="jumlah[{{ $alat->id }}]" value="{{ old('jumlah.' . $alat->id, 1) }}" min="1" max="{{ $alat->stok }}" aria-label="Jumlah {{ $alat->nama_alat }}" class="w-24 rounded-md border border-gray-300 px-2 py-1.5 text-sm focus:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-100"></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">Tidak ada alat yang cocok dengan pencarian ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($alats->isNotEmpty())
                <div class="flex justify-end border-t border-gray-200 px-5 py-4">
                    <button type="submit" class="rounded-md bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-800">Ajukan peminjaman</button>
                </div>
            @endif
        </section>
    </form>
@endsection
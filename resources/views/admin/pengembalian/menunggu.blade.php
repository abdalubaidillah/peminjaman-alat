@extends('layouts.app')

@section('title', 'Menunggu Pengembalian - Panel Admin')
@section('header-title', 'Menunggu Pengembalian Alat')

@section('content')
@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 p-4 rounded-lg shadow-sm text-sm">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
    <div class="p-5 border-b border-gray-200 bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
        <h3 class="text-lg font-bold text-gray-800">Daftar Menunggu Pengembalian</h3>
        <form action="{{ route('admin.pengembalian.menunggu') }}" method="GET" class="flex w-full md:w-96">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam / alat..."
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 text-sm font-semibold rounded-r-lg transition">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.pengembalian.menunggu') }}"
                    class="ml-2 bg-gray-300 hover:bg-gray-400 text-gray-700 px-3 py-2 text-sm rounded-lg flex items-center transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="py-3 px-4 border-b">Peminjam</th>
                    <th class="py-3 px-4 border-b">Alat yang Dipinjam</th>
                    <th class="py-3 px-4 border-b">Tanggal Pinjam</th>
                    <th class="py-3 px-4 border-b">Rencana Kembali</th>
                    <th class="py-3 px-4 border-b">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @forelse($peminjamans as $peminjaman)
                    <tr class="hover:bg-gray-50 transition align-top">
                        <td class="py-3 px-4 border-b font-medium text-gray-900">
                            {{ $peminjaman->user->name ?? 'User Dihapus' }}
                        </td>
                        <td class="py-3 px-4 border-b">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($peminjaman->detailPinjam as $detail)
                                    <li>
                                        <span class="font-semibold">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                        <span class="text-xs bg-gray-200 px-1.5 py-0.5 rounded">({{ $detail->jumlah }} pcs)</span>
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="py-3 px-4 border-b text-xs text-gray-600">
                            {{ $peminjaman->tgl_pinjam?->format('d-m-Y') ?? '-' }}
                        </td>
                        <td class="py-3 px-4 border-b text-xs font-semibold text-gray-600">
                            {{ $peminjaman->tgl_kembali_plan?->format('d-m-Y') ?? '-' }}
                        </td>
                        <td class="py-3 px-4 border-b">
                            <form action="{{ route('admin.peminjaman.updateStatus', $peminjaman->id) }}" method="POST"
                                onsubmit="return confirm('Tandai peminjaman ini sudah dikembalikan?')">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="status" value="selesai">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded text-xs font-semibold transition whitespace-nowrap">
                                    Proses Kembali
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">Tidak ada peminjaman yang menunggu pengembalian.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t border-gray-200 bg-gray-50">
        {{ $peminjamans->links() }}
    </div>
</div>
@endsection
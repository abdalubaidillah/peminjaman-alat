@extends('layouts.peminjam')

@section('title', 'Riwayat Peminjaman - PinjamAlat')
@section('header-title', 'Riwayat Peminjaman')

@section('content')

    @php
        $filters = [
            ''             => 'Semua',
            'diajukan'     => 'Diajukan',
            'dipinjam'     => 'Dipinjam',
            'dikembalikan' => 'Menunggu Verifikasi',
            'selesai'      => 'Selesai',
            'telat'        => 'Telat',
        ];
    @endphp

    <div class="mb-4 flex gap-2 overflow-x-auto pb-1">
        @foreach($filters as $value => $label)
            <a href="{{ route('peminjam.riwayat', $value ? ['status' => $value] : []) }}"
               class="whitespace-nowrap rounded-full px-4 py-1.5 text-xs font-semibold transition {{ (string)$status === (string)$value ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 ring-1 ring-gray-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="space-y-3">
        @forelse($peminjamans as $p)
            @php
                $badge = [
                    'diajukan'     => 'bg-amber-100 text-amber-800',
                    'dipinjam'     => 'bg-blue-100 text-blue-800',
                    'dikembalikan' => 'bg-purple-100 text-purple-800',
                    'selesai'      => 'bg-emerald-100 text-emerald-800',
                    'telat'        => 'bg-red-100 text-red-800',
                ][$p->status] ?? 'bg-gray-100 text-gray-700';
                $label = [
                    'diajukan'     => 'Menunggu Persetujuan',
                    'dipinjam'     => 'Sedang Dipinjam',
                    'dikembalikan' => 'Menunggu Verifikasi',
                    'selesai'      => 'Selesai',
                    'telat'        => 'Telat',
                ][$p->status] ?? $p->status;
            @endphp

            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400">Pengajuan #{{ $p->id }}</p>
                        <p class="text-xs text-gray-400">Dipinjam: {{ $p->tgl_pinjam?->format('d M Y') }} &middot; Rencana kembali: {{ $p->tgl_kembali_plan?->format('d M Y') ?? '-' }}</p>
                    </div>
                    <span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $badge }}">{{ $label }}</span>
                </div>

                <div class="mt-3 divide-y divide-gray-100 rounded-xl bg-gray-50">
                    @foreach($p->detailPinjam as $detail)
                        <div class="flex items-center justify-between px-3 py-2 text-sm">
                            <span class="text-gray-700">{{ $detail->alat->nama_alat ?? 'Alat telah dihapus' }}</span>
                            <span class="font-semibold text-gray-500">{{ $detail->jumlah }} pcs</span>
                        </div>
                    @endforeach
                </div>

                @if($p->status === 'selesai' && $p->pengembalian)
                    <div class="mt-3 rounded-xl border border-emerald-100 bg-emerald-50 px-3 py-2 text-xs text-emerald-800">
                        Dikembalikan {{ $p->pengembalian->tgl_kembali?->format('d M Y') }} &middot;
                        Kondisi: {{ $p->pengembalian->kondisi_kembali }}
                        @if($p->pengembalian->denda > 0)
                            &middot; Denda: Rp {{ number_format($p->pengembalian->denda, 0, ',', '.') }}
                        @endif
                    </div>
                @endif

                @if($p->status === 'dipinjam')
                    <form action="{{ route('peminjam.pengembalian.ajukan', $p->id) }}" method="POST" class="mt-3" onsubmit="return confirm('Ajukan pengembalian untuk peminjaman ini?')">
                        @csrf
                        <button type="submit" class="w-full rounded-xl bg-emerald-600 py-2.5 text-xs font-bold text-white hover:bg-emerald-700">
                            Kembalikan Alat
                        </button>
                    </form>
                @elseif($p->status === 'dikembalikan')
                    <p class="mt-3 rounded-xl bg-purple-50 px-3 py-2 text-center text-[11px] font-medium text-purple-700">
                        Menunggu verifikasi petugas — serahkan alat secara langsung.
                    </p>
                @elseif($p->status === 'diajukan')
                    <p class="mt-3 rounded-xl bg-amber-50 px-3 py-2 text-center text-[11px] font-medium text-amber-700">
                        Menunggu persetujuan petugas.
                    </p>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-8 text-center text-sm text-gray-400">
                Belum ada riwayat peminjaman.
            </div>
        @endforelse
    </div>

@endsection
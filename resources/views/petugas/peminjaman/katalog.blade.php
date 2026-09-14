@extends('layouts.peminjam')

@section('title', 'Katalog Alat - PinjamAlat')
@section('header-title', 'Katalog Alat')

@section('content')

    <!-- SEARCH & FILTER -->
    <form method="GET" action="{{ route('peminjam.katalog') }}" class="mb-4 space-y-3">
        <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m21 21-4.3-4.3"/></svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama alat..."
                   class="w-full rounded-2xl border border-gray-200 bg-white py-3 pl-11 pr-4 text-sm shadow-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">
        </div>

        <div class="flex gap-2 overflow-x-auto pb-1">
            <button type="submit" name="kategori" value=""
                class="whitespace-nowrap rounded-full px-4 py-1.5 text-xs font-semibold transition {{ !$kategoriId ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 ring-1 ring-gray-200' }}">
                Semua
            </button>
            @foreach($kategoris as $kategori)
                <button type="submit" name="kategori" value="{{ $kategori->id }}"
                    class="whitespace-nowrap rounded-full px-4 py-1.5 text-xs font-semibold transition {{ (string)$kategoriId === (string)$kategori->id ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 ring-1 ring-gray-200' }}">
                    {{ $kategori->nama_kategori }}
                </button>
            @endforeach
        </div>
    </form>

    <!-- ALAT GRID -->
    <div class="grid grid-cols-2 gap-3" id="alat-grid">
        @forelse($alats as $alat)
            <div class="alat-card overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 transition"
                 data-id="{{ $alat->id }}" data-nama="{{ $alat->nama_alat }}" data-stok="{{ $alat->stok }}">
                <div class="flex h-28 items-center justify-center bg-emerald-50">
                    @if($alat->gambar)
                        <img src="{{ asset($alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="h-full w-full object-cover">
                    @else
                        <span class="text-4xl">🧰</span>
                    @endif
                </div>
                <div class="p-3">
                    <p class="truncate text-sm font-semibold text-gray-800">{{ $alat->nama_alat }}</p>
                    <p class="truncate text-[11px] text-gray-400">{{ $alat->kategori->nama_kategori ?? '-' }}</p>
                    <div class="mt-1.5 flex items-center justify-between">
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">Stok {{ $alat->stok }}</span>
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-500">{{ $alat->status_kondisi }}</span>
                    </div>

                    <div class="mt-2.5">
                        <button type="button" onclick="addToCart({{ $alat->id }})" class="btn-add w-full rounded-xl bg-emerald-600 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">
                            + Pilih
                        </button>
                        <div class="stepper hidden items-center justify-between rounded-xl bg-emerald-50 px-1 py-1">
                            <button type="button" onclick="changeQty({{ $alat->id }}, -1)" class="flex h-7 w-7 items-center justify-center rounded-lg bg-white font-bold text-emerald-700 shadow-sm">−</button>
                            <span class="qty-label text-sm font-bold text-emerald-800">1</span>
                            <button type="button" onclick="changeQty({{ $alat->id }}, 1)" class="flex h-7 w-7 items-center justify-center rounded-lg bg-white font-bold text-emerald-700 shadow-sm">+</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 rounded-2xl border border-dashed border-gray-200 bg-white p-8 text-center text-sm text-gray-400">
                Tidak ada alat yang tersedia saat ini.
            </div>
        @endforelse
    </div>

    <!-- FLOATING CART BAR -->
    <div id="cart-bar" class="fixed inset-x-0 bottom-20 z-20 hidden px-5">
        <button type="button" onclick="openModal()" class="mx-auto flex max-w-2xl w-full items-center justify-between rounded-2xl bg-gray-900 px-5 py-3.5 text-white shadow-lg">
            <span class="text-sm font-semibold"><span id="cart-count">0</span> alat dipilih</span>
            <span class="flex items-center gap-1 text-sm font-bold text-emerald-400">
                Ajukan Peminjaman
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </span>
        </button>
    </div>

    <!-- CHECKOUT MODAL -->
    <div id="modal" class="fixed inset-0 z-40 hidden">
        <div class="absolute inset-0 bg-black/40" onclick="closeModal()"></div>
        <div class="absolute inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto rounded-t-3xl bg-white p-5 shadow-2xl">
            <div class="mx-auto mb-4 h-1.5 w-12 rounded-full bg-gray-200"></div>
            <h3 class="mb-3 text-lg font-bold text-gray-900">Konfirmasi Pengajuan</h3>

            <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST" id="ajukan-form">
                @csrf
                <div id="modal-items" class="space-y-2 mb-4"></div>

                <label class="mb-1 block text-xs font-semibold text-gray-600">Rencana Tanggal Kembali</label>
                <input type="date" name="tgl_kembali_plan" required min="{{ now()->addDay()->format('Y-m-d') }}"
                       class="mb-4 w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-200">

                <button type="submit" class="w-full rounded-xl bg-emerald-600 py-3 text-sm font-bold text-white hover:bg-emerald-700">
                    Kirim Pengajuan
                </button>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    const cart = {};

    function addToCart(id) {
        const card = document.querySelector(`.alat-card[data-id="${id}"]`);
        const stok = parseInt(card.dataset.stok, 10);
        if (stok < 1) return;

        cart[id] = { nama: card.dataset.nama, stok: stok, qty: 1 };

        card.querySelector('.btn-add').classList.add('hidden');
        const stepper = card.querySelector('.stepper');
        stepper.classList.remove('hidden');
        stepper.classList.add('flex');
        card.classList.add('ring-2', 'ring-emerald-400');

        updateCartBar();
    }

    function changeQty(id, delta) {
        if (!cart[id]) return;
        const card = document.querySelector(`.alat-card[data-id="${id}"]`);
        let newQty = cart[id].qty + delta;

        if (newQty < 1) {
            delete cart[id];
            card.querySelector('.btn-add').classList.remove('hidden');
            const stepper = card.querySelector('.stepper');
            stepper.classList.add('hidden');
            stepper.classList.remove('flex');
            card.classList.remove('ring-2', 'ring-emerald-400');
        } else if (newQty > cart[id].stok) {
            return; // jangan melebihi stok
        } else {
            cart[id].qty = newQty;
            card.querySelector('.qty-label').textContent = newQty;
        }

        updateCartBar();
    }

    function updateCartBar() {
        const ids = Object.keys(cart);
        const bar = document.getElementById('cart-bar');
        document.getElementById('cart-count').textContent = ids.length;
        bar.classList.toggle('hidden', ids.length === 0);
    }

    function openModal() {
        const container = document.getElementById('modal-items');
        container.innerHTML = '';

        Object.entries(cart).forEach(([id, item]) => {
            container.insertAdjacentHTML('beforeend', `
                <div class="flex items-center justify-between rounded-xl bg-gray-50 px-3 py-2">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">${item.nama}</p>
                        <p class="text-[11px] text-gray-400">Jumlah: ${item.qty}</p>
                    </div>
                    <input type="hidden" name="alat_id[]" value="${id}">
                    <input type="hidden" name="jumlah[]" value="${item.qty}">
                </div>
            `);
        });

        document.getElementById('modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }
</script>
@endpush
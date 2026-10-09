<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Peminjaman')</title>
    <!-- Memuat Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex">
            <div class="p-5 text-xl font-bold tracking-wider border-b border-gray-800">
                PANEL {{ strtoupper(auth()->user()->role) }}
            </div>
            <nav class="flex-1 p-4 space-y-2">
                @if(auth()->user()->role === 'petugas')
                    <a href="{{ route('petugas.dashboard') }}" class="block rounded-lg px-4 py-2 transition {{ request()->routeIs('petugas.dashboard') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">Dashboard</a>
                    <a href="{{ route('petugas.peminjaman.index') }}" class="block rounded-lg px-4 py-2 transition {{ request()->routeIs('petugas.peminjaman*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">Persetujuan Peminjaman</a>
                    <a href="{{ route('petugas.pengembalian.index') }}" class="block rounded-lg px-4 py-2 transition {{ request()->routeIs('petugas.pengembalian*') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">Pemantauan Pengembalian</a>
                    <a href="{{ route('petugas.laporan.form') }}" class="block rounded-lg px-4 py-2 text-gray-400 transition hover:bg-gray-800 hover:text-white">Cetak Laporan</a>
                @elseif(auth()->user()->role === 'peminjam')
                    <a href="{{ route('peminjam.dashboard') }}" class="block rounded-lg px-4 py-2 transition {{ request()->routeIs('peminjam.dashboard') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">Dashboard</a>
                    <a href="{{ route('peminjam.katalog') }}" class="block rounded-lg px-4 py-2 transition {{ request()->routeIs('peminjam.katalog') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">Katalog Alat</a>
                    <a href="{{ route('peminjam.riwayat') }}" class="block rounded-lg px-4 py-2 transition {{ request()->routeIs('peminjam.riwayat') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">Riwayat Peminjaman</a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-lg bg-gray-800 text-white font-medium">Dashboard</a>
                    <a href="{{ route('admin.user.index') }}" class="block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition">Kelola User</a>
                    <a href="{{ route('admin.kategori.index') }}" class="block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition">Kelola Kategori</a>
                    <a href="{{ route('admin.alat.index') }}" class="block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition">Kelola Alat</a>
                    <a href="{{ route('admin.peminjaman.index') }}" class="block px-4 py-2 rounded-lg transition
                    {{ request()->routeIs('admin.peminjaman*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        Kelola Peminjaman
                    </a>
                    <details class="group" {{ request()->routeIs('admin.pengembalian*') ? 'open' : '' }}>
                        <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-2 rounded-lg transition
                        {{ request()->routeIs('admin.pengembalian*') ? 'bg-gray-800 text-white font-medium shadow' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                            <span>Kelola Pengembalian</span>
                            <span class="text-xs transition group-open:rotate-180">&#9662;</span>
                        </summary>
                        <div class="mt-1 space-y-1 pl-4">
                            <a href="{{ route('admin.pengembalian.menunggu') }}" class="block px-4 py-2 rounded-lg text-sm transition {{ request()->routeIs('admin.pengembalian.menunggu') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">Menunggu Pengembalian</a>
                            <a href="{{ route('admin.pengembalian.index') }}" class="block px-4 py-2 rounded-lg text-sm transition {{ request()->routeIs('admin.pengembalian.index') ? 'bg-gray-800 text-white font-medium' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">Riwayat Pengembalian</a>
                        </div>
                    </details>
                @endif
            </nav>
            <div class="flex items-center gap-3 border-t border-gray-800 p-4 text-sm text-gray-400">
                @if(auth()->user()->foto_profile)
                    <img src="{{ asset(auth()->user()->foto_profile) }}" alt="Foto profil {{ auth()->user()->name }}" class="h-9 w-9 rounded-full border border-gray-600 object-cover">
                @else
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-500 text-sm font-bold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="min-w-0">
                    <span class="block text-xs text-gray-500">Logged in as</span>
                    <span class="block truncate font-semibold text-white">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT CONTAINER -->
        <div class="flex-1 flex flex-col overflow-y-auto">

            <!-- NAVBAR ATAS -->
            <header class="z-10 bg-white px-4 py-3 shadow-sm md:px-6 md:py-0">
                <div class="flex min-h-10 items-center justify-between">
                    <div class="text-lg font-semibold text-gray-800">
                        @yield('header-title', 'Dashboard')
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-lg bg-red-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-600">
                            Logout
                        </button>
                    </form>
                </div>
                @if(auth()->user()->role === 'peminjam')
                    <nav class="mt-3 flex gap-2 overflow-x-auto border-t border-gray-100 pt-3 md:hidden">
                        <a href="{{ route('peminjam.dashboard') }}" class="whitespace-nowrap rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('peminjam.dashboard') ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100' }}">Dashboard</a>
                        <a href="{{ route('peminjam.katalog') }}" class="whitespace-nowrap rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('peminjam.katalog') ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100' }}">Katalog</a>
                        <a href="{{ route('peminjam.riwayat') }}" class="whitespace-nowrap rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('peminjam.riwayat') ? 'bg-gray-900 text-white' : 'text-gray-600 hover:bg-gray-100' }}">Riwayat</a>
                    </nav>
                @endif
            </header>

            <!-- KONTEN UTAMA HALAMAN -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>

        </div>
    </div>

</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PinjamAlat')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Poppins', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#eefdf5', 100: '#d6faE4', 200: '#aef2c9',
                            400: '#34d399', 500: '#10b981', 600: '#059669',
                            700: '#047857', 900: '#064e3b',
                        },
                    },
                    boxShadow: {
                        soft: '0 10px 30px -12px rgba(6, 78, 59, 0.18)',
                        nav: '0 -4px 20px -6px rgba(0,0,0,0.08)',
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-thumb { background: #a7f3d0; border-radius: 10px; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-gradient-to-b from-emerald-50 via-white to-white text-gray-800 pb-24">

    <!-- TOP APP BAR -->
    <header class="sticky top-0 z-30 bg-gradient-to-r from-emerald-600 to-teal-500 text-white shadow-soft">
        <div class="mx-auto flex max-w-2xl items-center justify-between px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/20 text-lg font-bold backdrop-blur">
                    🧰
                </div>
                <div class="leading-tight">
                    <p class="text-[11px] uppercase tracking-wider text-emerald-100">PinjamAlat</p>
                    <p class="font-semibold">@yield('header-title', 'Halo, ' . auth()->user()->name . ' 👋')</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('peminjam.riwayat') }}" class="relative flex h-10 w-10 items-center justify-center rounded-full bg-white/15 hover:bg-white/25 transition" title="Riwayat">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v5h5M12 7v5l3 3"/></svg>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/15 hover:bg-white/25 transition" title="Keluar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- FLASH MESSAGES -->
    <div class="mx-auto max-w-2xl px-5">
        @if(session('success'))
            <div class="mt-4 flex items-start gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 shadow-sm">
                <span>✅</span><span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mt-4 flex items-start gap-2 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
                <span>⚠️</span><span>{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 shadow-sm">
                <p class="font-semibold mb-1">Periksa kembali data Anda:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- MAIN CONTENT -->
    <main class="mx-auto max-w-2xl px-5 py-5">
        @yield('content')
    </main>

    <!-- BOTTOM APP NAV -->
    <nav class="fixed inset-x-0 bottom-0 z-30 bg-white/95 backdrop-blur shadow-nav">
        <div class="mx-auto flex max-w-2xl items-center justify-around px-4 py-2.5">
            <a href="{{ route('peminjam.beranda') }}" class="flex flex-col items-center gap-1 rounded-xl px-4 py-1.5 text-xs font-medium transition {{ request()->routeIs('peminjam.beranda') ? 'text-emerald-600' : 'text-gray-400 hover:text-emerald-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="{{ request()->routeIs('peminjam.beranda') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 12 4l9 7.5M5 10v9a1 1 0 0 0 1 1h3v-6h6v6h3a1 1 0 0 0 1-1v-9"/></svg>
                Beranda
            </a>
            <a href="{{ route('peminjam.katalog') }}" class="flex flex-col items-center gap-1 rounded-xl px-4 py-1.5 text-xs font-medium transition {{ request()->routeIs('peminjam.katalog') ? 'text-emerald-600' : 'text-gray-400 hover:text-emerald-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="{{ request()->routeIs('peminjam.katalog') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                Katalog
            </a>
            <a href="{{ route('peminjam.riwayat') }}" class="flex flex-col items-center gap-1 rounded-xl px-4 py-1.5 text-xs font-medium transition {{ request()->routeIs('peminjam.riwayat') ? 'text-emerald-600' : 'text-gray-400 hover:text-emerald-500' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="{{ request()->routeIs('peminjam.riwayat') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 12h6M9 16h6"/></svg>
                Riwayat
            </a>
        </div>
    </nav>

    @stack('scripts')
</body>
</html>
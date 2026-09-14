<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - Peminjam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --ink: #172033;
            --muted: #718096;
            --blue: #2563eb;
            --soft-blue: #eff6ff;
        }

        body {
            color: var(--ink);
            background: #f5f7fb !important;
        }

        .topbar {
            background: linear-gradient(115deg, #172554, #2563eb) !important;
            box-shadow: 0 8px 24px rgba(37, 99, 235, .18);
        }

        .hero {
            position: relative;
            overflow: hidden;
            border-radius: 1.25rem;
            padding: 2rem;
            color: white;
            background: linear-gradient(120deg, #1d4ed8, #3b82f6 60%, #60a5fa);
            box-shadow: 0 14px 30px rgba(37, 99, 235, .18);
        }

        .hero::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            right: -70px;
            top: -100px;
            border: 28px solid rgba(255, 255, 255, .12);
            border-radius: 50%;
        }

        .hero > * { position: relative; z-index: 1; }

        .stat-card, .history-card {
            border: 0;
            border-radius: 1rem;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
        }

        .stat-card { padding: 1.1rem 1.25rem; background: white; }
        .stat-number { font-size: 1.7rem; font-weight: 800; }
        .history-card { transition: transform .2s ease, box-shadow .2s ease; }
        .history-card:hover { transform: translateY(-2px); box-shadow: 0 14px 30px rgba(15, 23, 42, .1); }
        .filter-box { background: white; border: 1px solid #e5e7eb; border-radius: .85rem; padding: .55rem .75rem; }
        .muted-label { color: var(--muted); font-size: .76rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark topbar mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('peminjam.katalog') }}">Panel Peminjam</a>
            <div class="d-flex">
                <a href="{{ route('peminjam.katalog') }}" class="btn btn-outline-light btn-sm me-2">Katalog Alat</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm text-primary">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container pb-5">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @php
            $jumlahDiajukan = $peminjamans->where('status', 'diajukan')->count();
            $jumlahDipinjam = $peminjamans->where('status', 'dipinjam')->count();
            $jumlahSelesai = $peminjamans->where('status', 'selesai')->count();
        @endphp

        <section class="hero mb-4">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <div class="small fw-semibold text-uppercase opacity-75 mb-2">Aktivitas Anda</div>
                    <h1 class="h3 fw-bold mb-2">Riwayat Peminjaman</h1>
                    <p class="mb-0 opacity-75">Pantau pengajuan, alat yang sedang digunakan, dan proses pengembalian.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('peminjam.katalog') }}" class="btn btn-light fw-semibold">Cari alat baru</a>
                </div>
            </div>
        </section>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="stat-card"><div class="muted-label">Menunggu persetujuan</div><div class="stat-number text-warning">{{ $jumlahDiajukan }}</div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="muted-label">Sedang dipinjam</div><div class="stat-number text-primary">{{ $jumlahDipinjam }}</div></div></div>
            <div class="col-md-4"><div class="stat-card"><div class="muted-label">Selesai</div><div class="stat-number text-success">{{ $jumlahSelesai }}</div></div></div>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div><h2 class="h5 fw-bold mb-1">Daftar aktivitas</h2><p class="text-muted mb-0 small">Urut dari peminjaman terbaru.</p></div>
            <form action="{{ route('peminjam.riwayat') }}" method="GET" class="filter-box d-flex align-items-center gap-2">
                <label for="status" class="small fw-semibold text-muted">Filter</label>
                <select id="status" name="status" class="form-select form-select-sm border-0" onchange="this.form.submit()">
                    <option value="">Semua status</option>
                    <option value="diajukan" @selected($status === 'diajukan')>Diajukan</option>
                    <option value="dipinjam" @selected($status === 'dipinjam')>Dipinjam</option>
                    <option value="dikembalikan" @selected($status === 'dikembalikan')>Menunggu verifikasi</option>
                    <option value="selesai" @selected($status === 'selesai')>Selesai</option>
                    <option value="telat" @selected($status === 'telat')>Telat</option>
                </select>
            </form>
        </div>

        @forelse($peminjamans as $peminjaman)
            <section class="card history-card mb-3">
                <div class="card-header bg-white border-0 pt-4 px-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <div>
                        <strong>Peminjaman #{{ $peminjaman->id }}</strong>
                        <span class="text-muted ms-md-2">{{ $peminjaman->tgl_pinjam?->format('d-m-Y') ?? '-' }}</span>
                    </div>
                    @php
                        $statusClass = match ($peminjaman->status) {
                            'diajukan' => 'bg-warning text-dark',
                            'dipinjam' => 'bg-primary',
                            'dikembalikan' => 'bg-info text-dark',
                            'selesai' => 'bg-success',
                            'telat' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ ucfirst($peminjaman->status) }}</span>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <h6 class="muted-label mb-3">Alat yang dipinjam</h6>
                            <ul class="mb-0">
                                @foreach($peminjaman->detailPinjam as $detail)
                                    <li>{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} ({{ $detail->jumlah }} pcs)</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-5">
                            <h6 class="muted-label mb-2">Rencana kembali</h6>
                            <p class="mb-2">{{ $peminjaman->tgl_kembali_plan?->format('d-m-Y') ?? '-' }}</p>
                            @if($peminjaman->pengembalian)
                                <small class="text-success">Dikembalikan pada {{ $peminjaman->pengembalian->tgl_kembali?->format('d-m-Y') ?? '-' }}</small>
                            @elseif($peminjaman->status === 'dikembalikan')
                                <small class="text-info">Menunggu verifikasi petugas.</small>
                            @elseif($peminjaman->status === 'dipinjam')
                                <form action="{{ route('peminjam.pengembalian.ajukan', $peminjaman->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary btn-sm" onclick="return confirm('Ajukan pengembalian alat ini?')">Ajukan pengembalian</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        @empty
            <div class="alert alert-secondary">Belum ada riwayat peminjaman.</div>
        @endforelse
    </main>
</body>
</html>

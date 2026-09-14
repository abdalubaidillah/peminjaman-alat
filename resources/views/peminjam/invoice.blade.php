<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Peminjaman #{{ $peminjaman->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .invoice-card { border: 0 !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="invoice-card mx-auto rounded bg-white p-4 shadow-sm" style="max-width: 800px;">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 border-bottom pb-4">
                <div>
                    <p class="mb-1 text-uppercase text-primary fw-bold">Invoice Peminjaman</p>
                    <h1 class="h3 mb-1">#{{ $peminjaman->id }}</h1>
                    <p class="mb-0 text-muted">Bukti pengajuan peminjaman alat</p>
                </div>
                <div class="text-md-end">
                    <span class="badge rounded-pill bg-warning text-dark">{{ ucfirst($peminjaman->status) }}</span>
                    <p class="mt-2 mb-0 small text-muted">Tanggal pengajuan: {{ $peminjaman->tgl_pinjam?->format('d-m-Y') ?? '-' }}</p>
                </div>
            </div>

            <div class="row g-3 border-bottom py-4">
                <div class="col-md-6">
                    <div class="small text-muted">Peminjam</div>
                    <div class="fw-semibold">{{ $peminjaman->user->name }}</div>
                    <div class="small text-muted">{{ $peminjaman->user->email }}</div>
                </div>
                <div class="col-md-6">
                    <div class="small text-muted">Rencana tanggal kembali</div>
                    <div class="fw-semibold">{{ $peminjaman->tgl_kembali_plan?->format('d-m-Y') ?? '-' }}</div>
                </div>
            </div>

            <div class="py-4">
                <h2 class="h5 mb-3">Detail alat</h2>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr><th>No</th><th>Nama alat</th><th>Kategori</th><th class="text-end">Jumlah</th></tr>
                        </thead>
                        <tbody>
                            @foreach($peminjaman->detailPinjam as $detail)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</td>
                                    <td>{{ $detail->alat->kategori->nama_kategori ?? '-' }}</td>
                                    <td class="text-end">{{ $detail->jumlah }} pcs</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><th colspan="3" class="text-end">Total unit</th><th class="text-end">{{ $peminjaman->detailPinjam->sum('jumlah') }} pcs</th></tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="no-print d-flex flex-column flex-sm-row justify-content-end gap-2 border-top pt-4">
                <a href="{{ route('peminjam.riwayat') }}" class="btn btn-outline-secondary">Kembali ke riwayat</a>
                <button type="button" onclick="window.print()" class="btn btn-primary">Cetak invoice</button>
            </div>
        </div>
    </div>
</body>
</html>
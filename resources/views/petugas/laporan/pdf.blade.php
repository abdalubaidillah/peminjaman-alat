<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 10px; }
        h1 { margin: 0 0 4px; font-size: 18px; }
        p { margin: 3px 0 12px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #111827; color: white; text-align: left; }
        th, td { border: 1px solid #d1d5db; padding: 6px; vertical-align: top; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <h1>Laporan Peminjaman Alat</h1>
    <p>Dicetak {{ now()->format('d-m-Y H:i') }} oleh {{ auth()->user()->name }}</p>
    <table>
        <thead><tr><th>No</th><th>Peminjam</th><th>Tanggal Pinjam</th><th>Rencana Kembali</th><th>Alat</th><th>Status</th><th>Pengembalian</th><th>Denda</th></tr></thead>
        <tbody>
            @forelse($peminjamans as $index => $peminjaman)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $peminjaman->user->name ?? 'User Dihapus' }}</td>
                    <td>{{ $peminjaman->tgl_pinjam?->format('d-m-Y') ?? '-' }}</td>
                    <td>{{ $peminjaman->tgl_kembali_plan?->format('d-m-Y') ?? '-' }}</td>
                    <td>@foreach($peminjaman->detailPinjam as $detail){{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} ({{ $detail->jumlah }} pcs)@if(!$loop->last), @endif @endforeach</td>
                    <td>{{ ucfirst($peminjaman->status) }}</td>
                    <td>{{ $peminjaman->pengembalian?->tgl_kembali?->format('d-m-Y') ?? '-' }}</td>
                    <td>Rp {{ number_format($peminjaman->pengembalian?->denda ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="muted">Tidak ada data sesuai filter.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

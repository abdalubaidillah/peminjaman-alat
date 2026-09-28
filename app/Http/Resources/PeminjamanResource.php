<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeminjamanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'peminjam' => $this->user?->name,
            'tgl_pinjam' => $this->tgl_pinjam?->format('Y-m-d'),
            'tgl_kembali_plan' => $this->tgl_kembali_plan?->format('Y-m-d'),
            'status' => $this->status,
            'item_dipinjam' => $this->detailPinjam->map(fn ($detail) => [
                'nama_alat' => $detail->alat?->nama_alat,
                'jumlah' => $detail->jumlah,
            ])->values(),
            'info_pengembalian' => $this->pengembalian ? [
                'tgl_kembali' => $this->pengembalian->tgl_kembali?->format('Y-m-d'),
                'kondisi_kembali' => $this->pengembalian->kondisi_kembali,
                'denda' => $this->pengembalian->denda,
                'petugas' => $this->pengembalian->petugas?->name,
            ] : null,
        ];
    }
}

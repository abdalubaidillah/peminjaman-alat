<?php

namespace App\Http\Requests\Pengembalian;

use Illuminate\Foundation\Http\FormRequest;

class StorePengembalianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'peminjaman_id' => ['required', 'integer', 'exists:peminjaman,id'],
            'kondisi_kembali' => ['required', 'string', 'max:255'],
            'denda' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
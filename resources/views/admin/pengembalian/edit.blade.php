@extends('layouts.app')

@section('title', 'Edit Pengembalian - Panel Admin')
@section('header-title', 'Edit Data Pengembalian')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow-sm border border-gray-200 p-6">
	<form action="{{ route('admin.pengembalian.update', $pengembalian->id) }}" method="POST">
		@csrf
		@method('PUT')

		<div class="mb-4">
			<label class="block text-gray-700 text-sm font-semibold mb-2">Peminjam</label>
			<input type="text" value="{{ $pengembalian->peminjaman->user->name ?? 'User Dihapus' }}" disabled
				class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
		</div>

		<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
			<div>
				<label class="block text-gray-700 text-sm font-semibold mb-2">Tanggal Kembali</label>
				<input type="date" value="{{ $pengembalian->tgl_kembali?->format('Y-m-d') }}" disabled
					class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600">
			</div>
			<div>
				<label class="block text-gray-700 text-sm font-semibold mb-2">Denda</label>
				<input type="number" name="denda" value="{{ old('denda', $pengembalian->denda) }}" min="0" required
					class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
				@error('denda')
					<span class="text-red-500 text-xs">{{ $message }}</span>
				@enderror
			</div>
		</div>

		<div class="mb-6">
			<label class="block text-gray-700 text-sm font-semibold mb-2">Kondisi Saat Dikembalikan</label>
			<input type="text" name="kondisi_kembali" value="{{ old('kondisi_kembali', $pengembalian->kondisi_kembali) }}" required
				placeholder="Contoh: Baik / Rusak Ringan"
				class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
			@error('kondisi_kembali')
				<span class="text-red-500 text-xs">{{ $message }}</span>
			@enderror
		</div>

		<div class="flex justify-end space-x-2">
			<a href="{{ route('admin.pengembalian.index') }}"
				class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold transition">Batal</a>
			<button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">Perbarui</button>
		</div>
	</form>
</div>
@endsection

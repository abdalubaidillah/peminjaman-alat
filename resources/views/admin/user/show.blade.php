@extends('layouts.app')

@section('title', 'Detail User - Panel Admin')
@section('header-title', 'Detail Pengguna')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <p class="text-sm text-gray-500">Manajemen Pengguna Sistem</p>
                <h3 class="text-2xl font-bold text-gray-800">Profil User</h3>
            </div>
            <a href="{{ route('admin.user.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
                Kembali
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gray-800 px-6 py-8 text-center">
                @if($user->foto_profile)
                    <img src="{{ asset($user->foto_profile) }}" alt="Foto profil {{ $user->name }}"
                         class="w-28 h-28 mx-auto rounded-full object-cover border-4 border-white shadow-md">
                @else
                    <div class="w-28 h-28 mx-auto rounded-full bg-blue-500 text-white flex items-center justify-center text-4xl font-bold border-4 border-white shadow-md">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <form action="{{ route('admin.user.photo.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="mt-4">
                    @csrf
                    <label for="foto_profile" class="inline-block bg-white hover:bg-gray-100 text-gray-800 px-4 py-2 rounded-lg text-sm font-semibold cursor-pointer transition">
                        Ganti Foto Profil
                    </label>
                    <input id="foto_profile" type="file" name="foto_profile" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" onchange="this.form.submit()" required>
                </form>
                <h1 class="mt-4 text-2xl font-bold text-white">{{ $user->name }}</h1>
                <span class="inline-block mt-2 px-3 py-1 text-xs font-semibold rounded-full
                    @if($user->role === 'admin') bg-purple-100 text-purple-800
                    @elseif($user->role === 'petugas') bg-blue-100 text-blue-800
                    @else bg-green-100 text-green-800 @endif">
                    {{ ucfirst($user->role) }}
                </span>
            </div>

            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi User</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nama Lengkap</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Email</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Nomor HP</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->no_hp ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Role / Hak Akses</dt>
                        <dd class="mt-1 text-gray-900">{{ ucfirst($user->role) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Terdaftar Sejak</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->created_at?->format('d F Y, H:i') ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Terakhir Diperbarui</dt>
                        <dd class="mt-1 text-gray-900">{{ $user->updated_at?->format('d F Y, H:i') ?: '-' }}</dd>
                    </div>
                </dl>

                <div class="mt-6 pt-5 border-t border-gray-200 flex justify-end">
                    <a href="{{ route('admin.user.edit', $user->id) }}"
                       class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition">
                        Edit User
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

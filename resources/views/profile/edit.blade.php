@extends('layouts.app')

@section('title', 'Profil Saya')
@section('header-title', 'Profil Saya')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
            <p class="mt-1 text-sm text-gray-600">Lihat dan perbarui informasi akun Anda.</p>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-6 flex justify-center border-b border-gray-100 pb-6">
            <details class="group relative">
                <summary class="list-none cursor-pointer rounded-full outline-none ring-blue-500 focus-visible:ring-2" aria-label="Opsi foto profil">
                    @if($user->foto_profile)
                        <img src="{{ asset($user->foto_profile) }}" alt="Foto profil {{ $user->name }}" class="h-24 w-24 rounded-full border border-gray-200 object-cover transition group-hover:opacity-80">
                    @else
                        <div class="flex h-24 w-24 items-center justify-center rounded-full bg-blue-500 text-3xl font-bold text-white transition group-hover:bg-blue-600">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="mt-2 block text-center text-xs text-gray-500">Klik foto untuk opsi</span>
                </summary>
                <div class="absolute left-1/2 z-20 mt-2 w-48 -translate-x-1/2 rounded-lg border border-gray-200 bg-white p-1 shadow-lg">
                    @if($user->foto_profile)
                        <a href="{{ asset($user->foto_profile) }}" target="_blank" rel="noopener noreferrer" class="block rounded-md px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-100">
                            Lihat foto
                        </a>
                    @endif
                    <form action="{{ route('profile.photo.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label for="foto_profile" class="block cursor-pointer rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Unggah foto
                        </label>
                        <input id="foto_profile" name="foto_profile" type="file" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden" required onchange="this.form.submit()">
                    </form>
                    @if($user->foto_profile)
                        <form action="{{ route('profile.photo.delete') }}" method="POST" onsubmit="return confirm('Hapus foto profil ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="block w-full rounded-md px-3 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                                Hapus foto
                            </button>
                        </form>
                    @endif
                </div>
            </details>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">Nama</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="name" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="email" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="no_hp" class="mb-2 block text-sm font-semibold text-gray-700">Nomor HP</label>
                    <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp', $user->no_hp) }}" maxlength="15" autocomplete="tel" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Role</label>
                    <input type="text" value="{{ ucfirst($user->role) }}" disabled class="w-full rounded-lg border border-gray-200 bg-gray-100 px-3 py-2 text-gray-600">
                </div>

                <div class="sm:col-span-2">
                    <label for="alamat" class="mb-2 block text-sm font-semibold text-gray-700">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3" autocomplete="street-address" class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('alamat', $user->alamat) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end border-t border-gray-100 pt-5">
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection

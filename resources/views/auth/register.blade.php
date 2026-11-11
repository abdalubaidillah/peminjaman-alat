<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Peminjaman Alat</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center px-4 py-8">

    <div class="w-full max-w-md rounded-lg bg-white p-8 shadow-md">
        <h3 class="mb-6 text-center text-2xl font-bold text-gray-800">
            Registrasi Peminjam
        </h3>

        @if($errors->any())
            <div class="mb-4 rounded border border-red-400 bg-red-100 p-3 text-sm text-red-700">
                <ul class="mb-0 list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">Nama</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="mb-4">
                <label for="email" class="mb-2 block text-sm font-semibold text-gray-700">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="mb-4">
                <label for="no_hp" class="mb-2 block text-sm font-semibold text-gray-700">Nomor HP <span class="font-normal text-gray-500">(opsional)</span></label>
                <input
                    id="no_hp"
                    type="tel"
                    name="no_hp"
                    value="{{ old('no_hp') }}"
                    autocomplete="tel"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="mb-4">
                <label for="alamat" class="mb-2 block text-sm font-semibold text-gray-700">Alamat <span class="font-normal text-gray-500">(opsional)</span></label>
                <textarea
                    id="alamat"
                    name="alamat"
                    rows="2"
                    autocomplete="street-address"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >{{ old('alamat') }}</textarea>
            </div>

            <div class="mb-4">
                <label for="password" class="mb-2 block text-sm font-semibold text-gray-700">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-gray-700">Konfirmasi Password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-blue-600 py-2 font-semibold text-white transition duration-200 hover:bg-blue-700"
            >
                Daftar
            </button>
        </form>

        <p class="mt-5 text-center text-sm text-gray-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">
                Masuk
            </a>
        </p>
    </div>

</body>
</html>

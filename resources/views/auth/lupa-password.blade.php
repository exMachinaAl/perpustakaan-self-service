<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center px-4">

    <div class="w-full max-w-md bg-white border-2 border-black rounded-2xl shadow-[8px_8px_0px_#000] p-8">
        <h1 class="text-2xl font-black text-slate-900 mb-2">Lupa Password</h1>
        <p class="text-sm text-slate-600 mb-6">
            Masukkan email akun kamu untuk mengganti password.
        </p>

        @if (session('success'))<div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-xl">{{ session('success') }}</div>@endif
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border-2 border-red-500 text-red-700 px-4 py-3 rounded-xl text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.check') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-bold mb-2">Email</label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       class="w-full px-4 py-3 border-2 border-black rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Masukkan email kamu">
            </div>

            <button type="submit"
                    class="w-full bg-black text-white font-bold py-3 rounded-xl hover:bg-slate-800 transition">
                Kirim tautan reset
            </button>

            <a href="{{ route('login') }}"
               class="block text-center text-sm font-semibold text-blue-600 hover:underline">
                Kembali ke Login
            </a>
        </form>
    </div>

</body>
</html>
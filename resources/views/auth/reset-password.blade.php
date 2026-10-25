<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Reset Password</title><script src="https://cdn.tailwindcss.com"></script></head>
<body class="min-h-screen flex items-center justify-center bg-slate-100 px-4"><div class="max-w-md w-full bg-white border-2 border-black rounded-2xl p-8">
<h1 class="text-2xl font-bold mb-3">Reset Password</h1><p class="text-sm mb-5">Buat password baru melalui tautan reset yang dikirim ke email.</p>
@if($errors->any())<div class="bg-red-100 text-red-800 p-3 mb-4 rounded">{{ $errors->first() }}</div>@endif
<form action="{{ route('password.update') }}" method="POST" class="space-y-4">@csrf
<input type="hidden" name="token" value="{{ $token }}"><input type="hidden" name="email" value="{{ $email }}">
<div><label class="block font-semibold">Email</label><span>{{ $email }}</span></div>
<div><label class="block font-semibold" for="password">Password baru (min. 10 karakter)</label><input id="password" type="password" autocomplete="new-password" name="password" required minlength="10" class="w-full border rounded-lg p-3"></div>
<div><label class="block font-semibold" for="password_confirmation">Konfirmasi password</label><input id="password_confirmation" type="password" autocomplete="new-password" name="password_confirmation" required minlength="10" class="w-full border rounded-lg p-3"></div>
<button class="w-full bg-black text-white p-3 rounded-lg" type="submit">Simpan password baru</button></form></div></body></html>

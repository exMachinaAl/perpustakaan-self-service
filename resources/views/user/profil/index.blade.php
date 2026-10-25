@extends(Auth::user()->role === 'admin' ? 'layouts.admin' : 'layouts.user')

@section('title', 'Profile Saya')

@section('content')

<style>
    .profile-panel {
        border: 2px solid #0f172a;
        box-shadow: 5px 5px 0 #0f172a;
    }

    .profile-btn {
        border: 2px solid #0f172a;
        box-shadow: 3px 3px 0 #0f172a;
        transition: all .18s ease;
    }

    .profile-btn:hover {
        transform: translate(-1px, -1px);
        box-shadow: 5px 5px 0 #0f172a;
    }
</style>

<div class="max-w-5xl mx-auto px-3 sm:px-4 lg:px-6 py-5 space-y-5">

    <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
        <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}"
           class="hover:text-slate-950 transition">
            Dashboard
        </a>
        <span>/</span>
        <span class="text-slate-950">Profile</span>
    </div>

    <div class="profile-panel rounded-2xl bg-gradient-to-br from-slate-50 via-white to-sky-50 px-5 py-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-950 text-white text-xs font-black tracking-wide uppercase mb-3">
                    <span class="material-symbols-outlined text-[16px]">account_circle</span>
                    Profile Akun
                </div>

                <h1 class="text-2xl md:text-3xl font-black text-slate-950">
                    Profile Saya
                </h1>

                <p class="text-sm text-slate-600 font-semibold mt-2">
                    Ubah nama, password, dan foto profile akun Anda.
                </p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="profile-panel rounded-2xl bg-emerald-100 px-4 py-3 text-sm font-bold text-slate-950">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="profile-panel rounded-2xl bg-rose-100 px-4 py-3 text-sm font-bold text-slate-950">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Foto Profile --}}
        <div class="profile-panel rounded-2xl bg-white overflow-hidden">
            <div class="px-5 py-4 bg-sky-100 border-b-2 border-slate-950">
                <h2 class="text-lg font-black text-slate-950">
                    Foto Profile
                </h2>
                <p class="text-xs text-slate-600 font-bold">
                    Foto ini akan tampil di pojok kanan atas.
                </p>
            </div>

            <div class="p-5 flex flex-col items-center text-center gap-4">
                <div class="w-32 h-32 border-[3px] border-slate-950 bg-slate-100 rounded-2xl overflow-hidden flex items-center justify-center">
                    @if(Auth::user()->foto_profile)
                        <img src="{{ asset('storage/' . Auth::user()->foto_profile) }}"
                             alt="Foto Profile"
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-5xl font-black text-slate-950">
                            {{ strtoupper(substr(Auth::user()->nama ?? Auth::user()->name ?? 'A', 0, 1)) }}
                        </span>
                    @endif
                </div>

                <div>
                    <p class="font-black text-slate-950">
                        {{ Auth::user()->nama ?? Auth::user()->name ?? 'User' }}
                    </p>
                    <p class="text-sm font-bold text-slate-500">
                        {{ Auth::user()->email }}
                    </p>
                    <p class="text-xs font-black uppercase text-slate-400 mt-1">
                        {{ Auth::user()->role }}
                    </p>
                </div>

                @if(Auth::user()->foto_profile)
                    <form action="{{ route('user.profile.foto.destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                onclick="return confirm('Hapus foto profile?')"
                                class="profile-btn bg-rose-100 text-slate-950 px-4 py-2 rounded-xl text-xs font-black">
                            Hapus Foto
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Form Update --}}
        <div class="lg:col-span-2 profile-panel rounded-2xl bg-white overflow-hidden">
            <div class="px-5 py-4 bg-amber-100 border-b-2 border-slate-950">
                <h2 class="text-lg font-black text-slate-950">
                    Edit Data Akun
                </h2>
                <p class="text-xs text-slate-600 font-bold">
                    Kosongkan password jika tidak ingin mengganti password.
                </p>
            </div>

            <form action="{{ route('user.profile.update') }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="p-5 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase mb-2">
                        Nama
                    </label>
                    <input type="text"
                           name="nama"
                           value="{{ old('nama', Auth::user()->nama ?? Auth::user()->name) }}"
                           class="w-full h-11 rounded-xl border-2 border-slate-950 bg-white px-3 text-sm font-bold text-slate-950 focus:ring-2 focus:ring-sky-300"
                           required>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase mb-2">
                        Email
                    </label>
                    <input type="email"
                           value="{{ Auth::user()->email }}"
                           class="w-full h-11 rounded-xl border-2 border-slate-300 bg-slate-100 px-3 text-sm font-bold text-slate-500"
                           readonly>
                    <p class="text-xs text-slate-500 font-semibold mt-1">
                        Email tidak diubah dari halaman ini.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase mb-2">
                        Ganti Foto Profile
                    </label>
                    <input type="file"
                           name="foto_profile"
                           accept="image/*"
                           class="w-full rounded-xl border-2 border-slate-950 bg-white px-3 py-2 text-sm font-bold text-slate-950 focus:ring-2 focus:ring-sky-300">
                    <p class="text-xs text-slate-500 font-semibold mt-1">
                        Format: JPG, PNG, WEBP. Maksimal 2MB.
                    </p>
                </div>

                <div class="border-t-2 border-slate-950 pt-5">
                    <h3 class="font-black text-slate-950 mb-3">
                        Ubah Password
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase mb-2">
                                Password Lama
                            </label>
                            <input type="password"
                                   name="current_password"
                                   class="w-full h-11 rounded-xl border-2 border-slate-950 bg-white px-3 text-sm font-bold text-slate-950 focus:ring-2 focus:ring-sky-300">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase mb-2">
                                Password Baru
                            </label>
                            <input type="password"
                                   name="new_password"
                                   class="w-full h-11 rounded-xl border-2 border-slate-950 bg-white px-3 text-sm font-bold text-slate-950 focus:ring-2 focus:ring-sky-300">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase mb-2">
                                Konfirmasi Password
                            </label>
                            <input type="password"
                                   name="new_password_confirmation"
                                   class="w-full h-11 rounded-xl border-2 border-slate-950 bg-white px-3 text-sm font-bold text-slate-950 focus:ring-2 focus:ring-sky-300">
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <button type="submit"
                            class="profile-btn bg-slate-950 text-white px-5 py-3 rounded-xl text-sm font-black inline-flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        Simpan Perubahan
                    </button>

                    <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}"
                       class="profile-btn bg-white text-slate-950 px-5 py-3 rounded-xl text-sm font-black inline-flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        Kembali
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection
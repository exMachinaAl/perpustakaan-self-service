@extends('layouts.admin')

@section('title', 'Tambah Anggota')

@section('content')

<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-[900px] mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <nav class="flex text-xs text-gray-500 gap-2 mb-2">
            <a class="hover:text-[#001e40]" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span>/</span>
            <a class="hover:text-[#001e40]" href="{{ route('admin.anggota.index') }}">Data Anggota</a>
            <span>/</span>
            <span class="text-[#001e40] font-semibold">Tambah Anggota</span>
        </nav>

        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#001e40]">Tambah Anggota</h2>
                <p class="text-sm text-gray-500 mt-1">Tambahkan akun anggota baru untuk akses perpustakaan digital.</p>
            </div>

            <a href="{{ route('admin.anggota.index') }}"
               class="inline-flex items-center justify-center gap-2 border border-gray-200 text-gray-600 px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-all">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                Kembali
            </a>
        </div>
    </div>

    {{-- Error Validation --}}
    @if ($errors->any())
        <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-100 text-red-700">
            <p class="font-semibold text-sm mb-2">Ada data yang belum sesuai:</p>
            <ul class="list-disc list-inside text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-sm font-bold text-[#001e40] uppercase tracking-wide">Form Data Anggota</h3>
            <p class="text-xs text-gray-500 mt-1">Pastikan NIM dan email belum pernah digunakan.</p>
        </div>

        <form action="{{ route('admin.anggota.store') }}" method="POST" class="p-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Nama --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">person</span>
                        <input type="text"
                               name="nama"
                               value="{{ old('nama') }}"
                               placeholder="Contoh: Ahmad Rizki"
                               class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-[#001e40] focus:border-[#001e40]"
                               required>
                    </div>
                </div>

                {{-- NIM --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        NIM <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">badge</span>
                        <input type="text"
                               name="nim"
                               value="{{ old('nim') }}"
                               placeholder="Contoh: 230602001"
                               class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-[#001e40] focus:border-[#001e40]"
                               required>
                    </div>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">mail</span>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="Contoh: anggota@umg.ac.id"
                               class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-[#001e40] focus:border-[#001e40]"
                               required>
                    </div>
                </div>

                {{-- Program Studi --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Program Studi
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">school</span>
                        <select name="prodi_id"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:ring-[#001e40] focus:border-[#001e40]">
                            <option value="">Pilih Program Studi</option>

                            @foreach ($prodiList as $prodi)
                                <option value="{{ $prodi->id }}" {{ old('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                    {{ $prodi->nama ?? $prodi->nama_prodi ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Status Akun <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">verified_user</span>
                        <select name="status"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 focus:ring-[#001e40] focus:border-[#001e40]"
                                required>
                            <option value="aktif" {{ old('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ old('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            <option value="diblokir" {{ old('status') == 'diblokir' ? 'selected' : '' }}>Diblokir</option>
                        </select>
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">lock</span>
                        <input type="password"
                               name="password"
                               id="password"
                               placeholder="Minimal 6 karakter"
                               class="w-full pl-10 pr-11 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-[#001e40] focus:border-[#001e40]"
                               required>

                        <button type="button"
                                onclick="togglePassword('password', 'iconPassword')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#001e40]">
                            <span id="iconPassword" class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">lock_reset</span>
                        <input type="password"
                               name="password_confirmation"
                               id="password_confirmation"
                               placeholder="Ulangi password"
                               class="w-full pl-10 pr-11 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-[#001e40] focus:border-[#001e40]"
                               required>

                        <button type="button"
                                onclick="togglePassword('password_confirmation', 'iconPasswordConfirm')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#001e40]">
                            <span id="iconPasswordConfirm" class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>

            </div>

            {{-- Info --}}
            <div class="mt-6 p-4 rounded-xl bg-blue-50 border border-blue-100 flex gap-3">
                <span class="material-symbols-outlined text-[#001e40] text-[22px]">info</span>
                <div>
                    <p class="text-sm font-semibold text-[#001e40]">Catatan</p>
                    <p class="text-xs text-gray-600 mt-1">
                        Anggota yang dibuat dari halaman ini otomatis memiliki role sebagai <b>user</b>.
                        Status <b>Aktif</b> berarti anggota bisa menggunakan layanan perpustakaan.
                    </p>
                </div>
            </div>

            {{-- Button --}}
            <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('admin.anggota.index') }}"
                   class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition-all">
                    Batal
                </a>

                <button type="reset"
                        class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-yellow-200 text-yellow-700 bg-yellow-50 text-sm font-semibold hover:bg-yellow-100 transition-all">
                    Reset Form
                </button>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#001e40] text-white text-sm font-semibold hover:opacity-90 transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">save</span>
                    Simpan Anggota
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }
</script>

@endsection
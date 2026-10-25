@extends('layouts.admin')

@section('title', 'Edit Program Studi')

@section('content')

<main class="p-8 space-y-8 flex-1">

    {{-- HEADER SECTION --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-primary">Edit Program Studi</h2>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                Perbarui data program studi yang sudah tersimpan
            </p>
        </div>

        <a href="{{ route('admin.program-studi.index') }}"
           class="bg-white hover:bg-surface-container text-primary px-6 py-3 rounded-lg font-label-md text-label-md flex items-center gap-2 border border-outline-variant shadow-sm transition-all active:scale-95">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali
        </a>
    </div>

    {{-- ALERT ERROR --}}
    @if ($errors->any())
        <div class="bg-error-container text-on-error-container px-4 py-3 rounded-lg font-label-md text-label-md">
            <p class="font-bold mb-1">Data belum lengkap.</p>
            <p>Periksa kembali input yang masih salah.</p>
        </div>
    @endif

    {{-- FORM CONTAINER --}}
    <div class="bg-white rounded-xl shadow-[0_10px_15px_-3px_rgba(0,30,64,0.05)] border border-outline-variant overflow-hidden max-w-3xl">

        {{-- FORM HEADER --}}
        <div class="p-6 border-b border-outline-variant flex items-center gap-4">
            <div class="w-14 h-14 bg-tertiary-fixed/30 rounded-full flex items-center justify-center">
                <span class="material-symbols-outlined text-tertiary text-[32px] fill-icon">edit</span>
            </div>

            <div>
                <h3 class="font-headline-sm text-headline-sm text-primary">
                    Form Edit Program Studi
                </h3>
                <p class="text-body-sm text-on-surface-variant mt-1">
                    Ubah informasi program studi sesuai kebutuhan
                </p>
            </div>
        </div>

        {{-- FORM BODY --}}
        <form action="{{ route('admin.program-studi.update', $prodi->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">

                {{-- Nama Program Studi --}}
                <div>
                    <label class="block text-label-md font-label-md text-on-surface-variant mb-1">
                        Nama Program Studi
                    </label>

                    <input
                        name="nama"
                        value="{{ old('nama', $prodi->nama) }}"
                        class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm focus:ring-2 focus:ring-primary-container focus:border-primary outline-none transition-all"
                        placeholder="Contoh: Teknik Informatika, Sistem Informasi, Manajemen"
                        type="text"
                        required>

                    @error('nama')
                        <p class="text-sm text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fakultas --}}
                <div>
                    <label class="block text-label-md font-label-md text-on-surface-variant mb-1">
                        Fakultas
                    </label>

                    <select
                        name="fakultas"
                        class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm focus:ring-2 focus:ring-primary-container focus:border-primary outline-none appearance-none transition-all"
                        required>

                        <option value="">Pilih Fakultas</option>

                        <option value="Fakultas Teknik" {{ old('fakultas', $prodi->fakultas) == 'Fakultas Teknik' ? 'selected' : '' }}>
                            Fakultas Teknik
                        </option>

                        <option value="Fakultas Ekonomi dan Bisnis" {{ old('fakultas', $prodi->fakultas) == 'Fakultas Ekonomi dan Bisnis' ? 'selected' : '' }}>
                            Fakultas Ekonomi dan Bisnis
                        </option>

                        <option value="Fakultas Hukum" {{ old('fakultas', $prodi->fakultas) == 'Fakultas Hukum' ? 'selected' : '' }}>
                            Fakultas Hukum
                        </option>

                        <option value="Fakultas Pertanian" {{ old('fakultas', $prodi->fakultas) == 'Fakultas Pertanian' ? 'selected' : '' }}>
                            Fakultas Pertanian
                        </option>

                        <option value="Fakultas Keguruan dan Ilmu Pendidikan" {{ old('fakultas', $prodi->fakultas) == 'Fakultas Keguruan dan Ilmu Pendidikan' ? 'selected' : '' }}>
                            Fakultas Keguruan dan Ilmu Pendidikan
                        </option>

                        <option value="Fakultas Agama Islam" {{ old('fakultas', $prodi->fakultas) == 'Fakultas Agama Islam' ? 'selected' : '' }}>
                            Fakultas Agama Islam
                        </option>

                        <option value="Fakultas Psikologi" {{ old('fakultas', $prodi->fakultas) == 'Fakultas Psikologi' ? 'selected' : '' }}>
                            Fakultas Psikologi
                        </option>

                        <option value="Fakultas Kesehatan" {{ old('fakultas', $prodi->fakultas) == 'Fakultas Kesehatan' ? 'selected' : '' }}>
                            Fakultas Kesehatan
                        </option>

                        <option value="Fakultas Kedokteran" {{ old('fakultas', $prodi->fakultas) == 'Fakultas Kedokteran' ? 'selected' : '' }}>
                            Fakultas Kedokteran
                        </option>

                        <option value="Sekolah Pascasarjana" {{ old('fakultas', $prodi->fakultas) == 'Sekolah Pascasarjana' ? 'selected' : '' }}>
                            Sekolah Pascasarjana
                        </option>
                    </select>

                    @error('fakultas')
                        <p class="text-sm text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Kode Prodi dan Status --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <label class="block text-label-md font-label-md text-on-surface-variant mb-1">
                            Kode Prodi
                        </label>

                        <input
                            name="kode_prodi"
                            value="{{ old('kode_prodi', $prodi->kode_prodi) }}"
                            class="w-full border border-outline-variant rounded-lg px-4 py-3 text-body-sm focus:ring-2 focus:ring-primary-container focus:border-primary outline-none transition-all"
                            placeholder="Contoh: TI-001"
                            type="text"
                            required>

                        @error('kode_prodi')
                            <p class="text-sm text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-label-md font-label-md text-on-surface-variant mb-1">
                            Status
                        </label>

                        <div class="flex items-center gap-5 py-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    class="text-primary focus:ring-primary-container"
                                    name="status"
                                    type="radio"
                                    value="aktif"
                                    {{ old('status', $prodi->status) == 'aktif' ? 'checked' : '' }}>
                                <span class="text-body-sm">Aktif</span>
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    class="text-primary focus:ring-primary-container"
                                    name="status"
                                    type="radio"
                                    value="nonaktif"
                                    {{ old('status', $prodi->status) == 'nonaktif' ? 'checked' : '' }}>
                                <span class="text-body-sm">Nonaktif</span>
                            </label>
                        </div>

                        @error('status')
                            <p class="text-sm text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

            </div>

            {{-- FORM FOOTER --}}
            <div class="bg-surface-container-low px-6 py-4 flex flex-col sm:flex-row-reverse gap-3 border-t border-outline-variant">
                <button
                    type="submit"
                    class="inline-flex justify-center items-center gap-2 rounded-lg bg-primary px-6 py-3 text-label-md font-label-md text-white shadow-sm hover:bg-primary-container transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Update Data
                </button>

                <a href="{{ route('admin.program-studi.index') }}"
                   class="inline-flex justify-center items-center gap-2 rounded-lg bg-white px-6 py-3 text-label-md font-label-md text-primary border border-outline-variant hover:bg-surface-container transition-all">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                    Batal
                </a>
            </div>

        </form>
    </div>

</main>

{{-- BACKGROUND ATMOSPHERIC GRADIENT --}}
<div class="fixed top-0 right-0 -z-10 w-[500px] h-[500px] bg-primary-fixed-dim/10 rounded-full blur-[120px] pointer-events-none translate-x-1/2 -translate-y-1/2 opacity-50"></div>

@endsection
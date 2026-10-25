@extends('layouts.admin')

@section('title', 'Detail Buku - UMG Digilib')

@section('content')

<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
    }

    .neo-panel {
        border: 2px solid #0f172a;
        box-shadow: 5px 5px 0 #0f172a;
    }

    .neo-card {
        border: 2px solid #0f172a;
        box-shadow: 4px 4px 0 #0f172a;
    }

    .neo-btn {
        border: 2px solid #0f172a;
        box-shadow: 3px 3px 0 #0f172a;
        transition: all .18s ease;
    }

    .neo-btn:hover {
        transform: translate(-1px, -1px);
        box-shadow: 5px 5px 0 #0f172a;
    }

    .neo-badge {
        border: 2px solid #0f172a;
        box-shadow: 2px 2px 0 #0f172a;
    }

    .detail-box {
        border: 2px solid #0f172a;
        background: #ffffff;
    }

    .cover-box {
        border: 2px dashed #0f172a;
        background: linear-gradient(to bottom right, #f8fafc, #e0f2fe);
        aspect-ratio: 3 / 4;
        min-height: 320px;
    }
</style>

<div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-5 py-4 space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-950 transition">
            Dashboard
        </a>
        <span>/</span>
        <a href="{{ route('admin.databuku.index') }}" class="hover:text-slate-950 transition">
            Data Buku
        </a>
        <span>/</span>
        <span class="text-slate-950">Detail Buku</span>
    </div>

    {{-- Header --}}
    <div class="neo-panel rounded-2xl px-5 py-5 bg-gradient-to-br from-slate-50 via-white to-sky-50">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="min-w-0">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-950 text-white text-xs font-black tracking-wide uppercase neo-badge mb-3">
                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                    Detail Katalog
                </div>

                <h2 class="text-2xl md:text-3xl font-black text-slate-950 leading-tight">
                    Detail Buku
                </h2>

                <p class="text-sm md:text-base text-slate-600 mt-2 max-w-2xl">
                    Lihat detail informasi buku yang tersimpan di katalog perpustakaan.
                </p>
            </div>

            <div class="flex flex-wrap justify-start lg:justify-end gap-3 lg:ml-auto lg:pl-10">
                <a href="{{ route('admin.databuku.index') }}"
                   class="neo-btn inline-flex items-center gap-2 bg-white text-slate-950 px-4 py-2.5 rounded-xl text-sm font-black">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Kembali
                </a>

                <a href="{{ route('admin.databuku.edit', $buku->id_buku) }}"
                   class="neo-btn inline-flex items-center gap-2 bg-slate-950 text-white px-4 py-2.5 rounded-xl text-sm font-black">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    Edit Buku
                </a>
            </div>
        </div>
    </div>

    {{-- Detail Panel --}}
    <div class="neo-panel bg-white rounded-2xl overflow-hidden">

        <div class="px-5 py-4 border-b-2 border-slate-950 bg-sky-100">
            <h3 class="text-lg font-black text-slate-950">
                Informasi Bibliografi
            </h3>
            <p class="text-xs text-slate-600 font-bold mt-1">
                Detail data buku, stok, lokasi rak, dan sinopsis.
            </p>
        </div>

        <div class="p-5 md:p-6 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                {{-- Cover --}}
                <div class="md:col-span-4 space-y-3">
                    <label class="block text-sm font-black text-slate-950">
                        Cover Buku
                    </label>

                    <div class="cover-box relative rounded-2xl flex flex-col items-center justify-center gap-4 overflow-hidden">
                        @if ($buku->cover)
                            <img class="absolute inset-0 w-full h-full object-cover"
                                 src="{{ asset('storage/' . $buku->cover) }}"
                                 alt="Cover {{ $buku->judul }}">
                        @else
                            <img class="absolute inset-0 w-full h-full object-cover opacity-15"
                                 src="https://lh3.googleusercontent.com/aida-public/AB6AXuD7W_okpqu6RUSfhyt9O7Lyr4c3p0qHn71Mf5z9zpFK6eKn25cY7r4ZIU8fXirVf_jfNyCp-Ngl3ZfpqsXDS1vkzTupg2C1KinFYRhpBdaPvTTMvE4f12zFfvBOouA_OW4qz-Lm8M2zMs1end83FxQCCfTCA-cMaplA-vQaY3k53Rp5-uPjlD8c2tHnVR5cgi1BHHI_Feo1j7f4baAwuJUiKWEKguISUDnQ8kNsQgdtjv_o7kmvEx2D7kMRjFMT3CJ9FpV7KJRTTc8P"
                                 alt="Cover Placeholder">

                            <div class="relative z-10 w-14 h-14 rounded-2xl bg-white border-2 border-slate-950 flex items-center justify-center">
                                <span class="material-symbols-outlined text-slate-950 text-[28px]">
                                    menu_book
                                </span>
                            </div>

                            <div class="text-center px-4 relative z-10">
                                <p class="text-sm font-black text-slate-950">
                                    Tidak Ada Cover
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Detail Fields --}}
                <div class="md:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div class="md:col-span-2">
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Judul Buku
                        </label>

                        <div class="detail-box w-full px-4 py-3 rounded-xl text-sm font-bold text-slate-950 break-words">
                            {{ $buku->judul }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Pengarang
                        </label>

                        <div class="detail-box w-full px-4 py-3 rounded-xl text-sm font-bold text-slate-950 break-words">
                            {{ $buku->pengarang }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Kategori
                        </label>

                        <div class="detail-box w-full px-4 py-3 rounded-xl text-sm font-bold text-slate-950">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-sky-100 text-slate-950 border-2 border-slate-950 text-xs font-black">
                                {{ $buku->kategori }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            ISBN / Kode Buku
                        </label>

                        <div class="detail-box w-full px-4 py-3 rounded-xl text-sm font-bold text-slate-950">
                            {{ $buku->kode }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Tahun Terbit
                        </label>

                        <div class="detail-box w-full px-4 py-3 rounded-xl text-sm font-bold text-slate-950">
                            {{ $buku->tahun_terbit }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Jumlah Stok
                        </label>

                        <div class="detail-box w-full px-4 py-3 rounded-xl text-sm font-bold text-slate-950">
                            @if ($buku->stock <= 3)
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-rose-200 text-slate-950 border-2 border-slate-950 text-xs font-black">
                                    {{ $buku->stock }} Eks • Stok Rendah
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-200 text-slate-950 border-2 border-slate-950 text-xs font-black">
                                    {{ $buku->stock }} Eks • Stok Aman
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Lokasi Rak
                        </label>

                        <div class="detail-box w-full px-4 py-3 rounded-xl text-sm font-bold text-slate-950">
                            {{ $buku->lokasi }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Penerbit
                        </label>

                        <div class="detail-box w-full px-4 py-3 rounded-xl text-sm font-bold text-slate-950 break-words">
                            {{ $buku->penerbit }}
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-black text-slate-950 mb-2">
                            Deskripsi / Sinopsis
                        </label>

                        <div class="detail-box w-full px-4 py-3 rounded-xl text-sm font-semibold text-slate-700 leading-relaxed min-h-[120px] whitespace-pre-line">
                            {{ $buku->deskripsi ?? 'Belum ada deskripsi untuk buku ini.' }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
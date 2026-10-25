@extends('layouts.admin')

@section('title', 'Data Buku | UMG Digilib Admin')

@section('content')

<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
    }

    .neo-card {
        border: 2px solid #0f172a;
        box-shadow: 5px 5px 0 #0f172a;
        transition: all .18s ease;
    }

    .neo-card:hover {
        transform: translate(-2px, -2px);
        box-shadow: 7px 7px 0 #0f172a;
    }

    .neo-panel {
        border: 2px solid #0f172a;
        box-shadow: 5px 5px 0 #0f172a;
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

    .neo-table th,
    .neo-table td {
        border-bottom: 2px solid #0f172a;
    }

    .book-cover-fixed {
        width: 44px;
        height: 60px;
        min-width: 44px;
        min-height: 60px;
        flex: 0 0 44px;
        overflow: hidden;
    }
</style>

<section class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-5 py-4 space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-950 transition">
            Dashboard
        </a>
        <span>/</span>
        <span class="text-slate-950">Data Buku</span>
    </div>

    {{-- Header --}}
    <div class="neo-panel rounded-2xl px-5 py-5 bg-gradient-to-br from-slate-50 via-white to-sky-50">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="min-w-0">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-950 text-white text-xs font-black tracking-wide uppercase neo-badge mb-3">
                    <span class="material-symbols-outlined text-[16px]">menu_book</span>
                    Manajemen Katalog
                </div>

                <h2 class="text-2xl md:text-3xl font-black text-slate-950 leading-tight">
                    Data Buku
                </h2>

                <p class="text-sm md:text-base text-slate-600 mt-2 max-w-2xl">
                    Kelola katalog buku, kategori, stok, penerbit, tahun terbit, dan informasi bibliografi perpustakaan.
                </p>
            </div>

            <div class="flex justify-start lg:justify-end lg:ml-auto lg:pl-10">
                <a href="{{ route('admin.databuku.create') }}"
                   class="neo-btn inline-flex items-center justify-center gap-2 bg-slate-950 text-white px-4 py-2.5 rounded-xl text-sm font-black">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Buku
                </a>
            </div>
        </div>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="neo-panel rounded-2xl bg-emerald-100 px-4 py-3 text-sm font-bold text-slate-950">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <div class="neo-card bg-white rounded-2xl px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-sky-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">menu_book</span>
                </div>

                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-wide">
                        Total Buku
                    </p>
                    <p class="text-2xl font-black text-slate-950">
                        {{ $totalBuku ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="neo-card bg-white rounded-2xl px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">category</span>
                </div>

                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-wide">
                        Kategori
                    </p>
                    <p class="text-2xl font-black text-slate-950">
                        {{ $totalKategori ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="neo-card bg-white rounded-2xl px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-amber-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">inventory_2</span>
                </div>

                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-wide">
                        Stok Rendah
                    </p>
                    <p class="text-2xl font-black text-slate-950">
                        {{ $stokRendah ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="neo-card bg-white rounded-2xl px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-rose-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">library_books</span>
                </div>

                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-wide">
                        Total Eksemplar
                    </p>
                    <p class="text-2xl font-black text-slate-950">
                        {{ $totalEksemplar ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- Table Card --}}
    <div class="neo-panel bg-white rounded-2xl overflow-hidden">

        {{-- Filter Bar --}}
        <div class="px-5 py-4 border-b-2 border-slate-950 bg-sky-100">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-3">
                <div>
                    <h3 class="text-lg font-black text-slate-950">
                        Daftar Buku
                    </h3>
                    <p class="text-xs text-slate-600 font-bold">
                        Cari dan kelola semua data buku perpustakaan.
                    </p>
                </div>

                <span class="neo-badge inline-flex items-center w-fit gap-2 bg-white text-slate-950 px-3 py-1 rounded-full text-xs font-black">
                    <span class="material-symbols-outlined text-[16px]">database</span>
                    {{ $buku->total() ?? 0 }} Data
                </span>
            </div>

            <form id="filterBukuForm"
                  action="{{ route('admin.databuku.index') }}"
                  method="GET"
                  class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 gap-3">

                <div class="relative xl:col-span-6">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-[19px]">
                        search
                    </span>

                    <input type="text"
                           id="searchBukuInput"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari judul, pengarang, atau kode buku..."
                           autocomplete="off"
                           class="w-full h-11 rounded-xl border-2 border-slate-950 bg-white pl-10 pr-4 text-sm font-semibold text-slate-950 placeholder:text-slate-400 focus:ring-2 focus:ring-sky-300 focus:border-slate-950">
                </div>

                <select name="kategori"
                        id="kategoriBukuSelect"
                        class="h-11 rounded-xl border-2 border-slate-950 bg-white px-3 text-sm font-semibold text-slate-950 xl:col-span-2 focus:ring-2 focus:ring-sky-300 focus:border-slate-950">
                    <option value="">Semua Kategori</option>
                    @foreach(($daftarKategori ?? []) as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>
                            {{ $kat }}
                        </option>
                    @endforeach
                </select>

                <select name="stok"
                        id="stokBukuSelect"
                        class="h-11 rounded-xl border-2 border-slate-950 bg-white px-3 text-sm font-semibold text-slate-950 xl:col-span-2 focus:ring-2 focus:ring-sky-300 focus:border-slate-950">
                    <option value="">Semua Stok</option>
                    <option value="rendah" {{ request('stok') == 'rendah' ? 'selected' : '' }}>
                        Stok Rendah
                    </option>
                    <option value="aman" {{ request('stok') == 'aman' ? 'selected' : '' }}>
                        Stok Aman
                    </option>
                </select>

                <a href="{{ route('admin.databuku.index') }}"
                   class="neo-btn h-11 px-4 rounded-xl bg-white text-slate-950 text-sm font-black inline-flex items-center justify-center xl:col-span-2">
                    Reset
                </a>

            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[950px] text-sm neo-table">
                <thead class="bg-slate-950 text-white">
                    <tr class="uppercase text-[11px] tracking-wide">
                        <th class="px-4 py-3 text-left font-black">No</th>
                        <th class="px-4 py-3 text-left font-black">Buku</th>
                        <th class="px-4 py-3 text-left font-black">Kode</th>
                        <th class="px-4 py-3 text-left font-black">Kategori</th>
                        <th class="px-4 py-3 text-left font-black">Stok</th>
                        <th class="px-4 py-3 text-left font-black">Penerbit</th>
                        <th class="px-4 py-3 text-left font-black">Tahun</th>
                        <th class="px-4 py-3 text-center font-black">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($buku as $index => $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-700 font-bold">
                                {{ $buku->firstItem() + $index }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3 min-w-[240px]">
                                    <div class="book-cover-fixed rounded-xl border-2 border-slate-950 bg-slate-100 flex items-center justify-center shrink-0">
                                        @if ($item->cover)
                                            <img src="{{ asset('storage/' . $item->cover) }}"
                                                 alt="Cover {{ $item->judul }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <span class="material-symbols-outlined text-slate-500 text-[22px]">
                                                menu_book
                                            </span>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <p class="font-black text-slate-950 leading-snug text-sm break-words">
                                            {{ $item->judul }}
                                        </p>
                                        <p class="text-slate-500 text-xs mt-1 font-semibold break-words">
                                            {{ $item->pengarang }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-slate-700 font-bold whitespace-nowrap">
                                {{ $item->kode }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-sky-100 text-slate-950 border-2 border-slate-950 whitespace-nowrap">
                                    {{ $item->kategori }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                @if ($item->stock <= 3)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-rose-200 text-slate-950 border-2 border-slate-950 whitespace-nowrap">
                                        {{ $item->stock }} Eks
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-emerald-200 text-slate-950 border-2 border-slate-950 whitespace-nowrap">
                                        {{ $item->stock }} Eks
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-slate-700 font-bold">
                                {{ $item->penerbit }}
                            </td>

                            <td class="px-4 py-3 text-slate-700 font-bold whitespace-nowrap">
                                {{ $item->tahun_terbit }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">

                                    <a href="{{ route('admin.databuku.show', $item->id_buku) }}"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded-xl border-2 border-slate-950 bg-sky-100 text-slate-950 hover:bg-sky-200 transition"
                                       title="Detail">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>

                                    <a href="{{ route('admin.databuku.edit', $item->id_buku) }}"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded-xl border-2 border-slate-950 bg-amber-100 text-slate-950 hover:bg-amber-200 transition"
                                       title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>

                                    <form action="{{ route('admin.databuku.destroy', $item->id_buku) }}"
                                          method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data buku ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-xl border-2 border-slate-950 bg-rose-100 text-slate-950 hover:bg-rose-200 transition"
                                                title="Hapus">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center">
                                <div class="border-2 border-dashed border-slate-950 rounded-2xl p-6 bg-slate-50 max-w-md mx-auto">
                                    <span class="material-symbols-outlined text-[34px] text-slate-950">
                                        menu_book
                                    </span>
                                    <p class="mt-2 text-sm font-black text-slate-950">
                                        Data buku belum tersedia.
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1 font-semibold">
                                        Tambahkan buku baru agar katalog perpustakaan dapat ditampilkan.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Table --}}
        <div class="px-5 py-4 border-t-2 border-slate-950 bg-slate-50 flex flex-col md:flex-row items-center justify-between gap-3">
            <p class="text-xs text-slate-600 font-bold">
                Showing {{ $buku->firstItem() ?? 0 }} to {{ $buku->lastItem() ?? 0 }} of {{ $buku->total() }} entries
            </p>

            <div class="text-sm font-bold">
                {{ $buku->links() }}
            </div>
        </div>

    </div>

</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filterBukuForm');
        const searchInput = document.getElementById('searchBukuInput');
        const kategoriSelect = document.getElementById('kategoriBukuSelect');
        const stokSelect = document.getElementById('stokBukuSelect');

        let typingTimer = null;

        function submitFilter() {
            if (form) {
                form.submit();
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(typingTimer);

                typingTimer = setTimeout(function () {
                    submitFilter();
                }, 500);
            });
        }

        if (kategoriSelect) {
            kategoriSelect.addEventListener('change', submitFilter);
        }

        if (stokSelect) {
            stokSelect.addEventListener('change', submitFilter);
        }
    });
</script>

@endsection
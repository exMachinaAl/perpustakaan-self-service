@extends('layouts.user')

@section('title', 'Data Buku - Perpustakaan')

@section('search_action', route('user.katalog'))
@section('search_placeholder', 'Cari judul buku, penulis, atau kategori...')

@section('content')

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<div class="space-y-5 sm:space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-[11px] sm:text-[12px] font-extrabold uppercase tracking-[0.12em] text-user-ink/70">
        <a href="{{ route('user.dashboard') }}"
           class="hover:text-user-gold transition">
            Dashboard
        </a>

        <span class="material-symbols-outlined text-[16px]">chevron_right</span>

        <span class="text-user-ink">
            Data Buku
        </span>
    </div>

    {{-- Header --}}
    <section class="relative overflow-hidden bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm p-4 sm:p-5 lg:p-6">
        <div class="absolute top-0 right-0 w-20 h-20 bg-user-gold border-l-[2px] border-b-[2px] border-user-ink"></div>
        <div class="absolute bottom-0 left-0 w-14 h-14 bg-user-blue-soft border-r-[2px] border-t-[2px] border-user-ink"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 bg-user-gold border-[2px] border-user-ink soft-brutal-shadow-sm px-3 py-1.5">
                    <span class="material-symbols-outlined text-[17px]" style="font-variation-settings: 'FILL' 1;">
                        menu_book
                    </span>
                    <span class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.16em]">
                        Library Catalog
                    </span>
                </div>

                <h1 class="font-display-lg text-[24px] sm:text-[32px] lg:text-[38px] leading-tight uppercase text-user-ink">
                    Jelajahi Koleksi Buku
                </h1>

                <p class="text-[12px] sm:text-[14px] text-user-ink/70 font-semibold max-w-2xl">
                    Temukan buku berdasarkan kategori, tahun terbit, status stok, atau urutkan sesuai kebutuhanmu.
                </p>
            </div>

            <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm px-4 py-3 min-w-[160px]">
                <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-user-ink/60">
                    Total Ditemukan
                </p>
                <p class="text-[26px] sm:text-[30px] font-extrabold text-user-ink leading-none mt-1">
                    {{ $buku->total() }}
                </p>
            </div>
        </div>
    </section>

    {{-- Filter + Catalog --}}
    <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-4 sm:gap-5">

        {{-- Filter Sidebar --}}
        <aside class="w-full">
            <form action="{{ route('user.katalog') }}"
                  method="GET"
                  class="bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm p-4 space-y-4">

                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <div class="flex items-center gap-2 pb-3 border-b-[2px] border-user-ink">
                    <span class="material-symbols-outlined text-[19px]">tune</span>
                    <h2 class="text-[13px] font-extrabold uppercase tracking-[0.14em] text-user-ink">
                        Filter
                    </h2>
                </div>

                {{-- Kategori --}}
                <div class="space-y-2">
                    <label class="block text-[11px] font-extrabold uppercase tracking-[0.14em] text-user-ink/70">
                        Kategori
                    </label>

                    <div class="relative">
                        <select name="kategori"
                                onchange="this.form.submit()"
                                class="w-full bg-white border-[2px] border-user-ink rounded-none px-3 py-2.5 pr-9 text-[12px] font-bold text-user-ink focus:ring-0 focus:outline-none focus:shadow-[0_0_0_3px_rgba(201,162,39,0.28)] appearance-none">
                            <option value="">Semua Kategori</option>

                            @foreach($daftarKategori as $kategoriItem)
                                <option value="{{ $kategoriItem }}" {{ request('kategori') == $kategoriItem ? 'selected' : '' }}>
                                    {{ $kategoriItem }}
                                </option>
                            @endforeach
                        </select>

                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-user-ink text-[19px]">
                            expand_more
                        </span>
                    </div>
                </div>

                {{-- Tahun --}}
                <div class="space-y-2">
                    <label class="block text-[11px] font-extrabold uppercase tracking-[0.14em] text-user-ink/70">
                        Tahun Terbit
                    </label>

                    <div class="relative">
                        <select name="tahun"
                                onchange="this.form.submit()"
                                class="w-full bg-white border-[2px] border-user-ink rounded-none px-3 py-2.5 pr-9 text-[12px] font-bold text-user-ink focus:ring-0 focus:outline-none focus:shadow-[0_0_0_3px_rgba(201,162,39,0.28)] appearance-none">
                            <option value="">Semua Tahun</option>

                            @foreach($daftarTahun as $tahunItem)
                                <option value="{{ $tahunItem }}" {{ request('tahun') == $tahunItem ? 'selected' : '' }}>
                                    {{ $tahunItem }}
                                </option>
                            @endforeach
                        </select>

                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-user-ink text-[19px]">
                            calendar_month
                        </span>
                    </div>
                </div>

                {{-- Stok --}}
                <div class="space-y-2">
                    <label class="block text-[11px] font-extrabold uppercase tracking-[0.14em] text-user-ink/70">
                        Status Stok
                    </label>

                    <label class="flex items-center gap-3 bg-white border-[2px] border-user-ink px-3 py-2.5 cursor-pointer soft-brutal-hover">
                        <input
                            class="w-4 h-4 rounded-none border-[2px] border-user-ink text-user-gold focus:ring-user-gold"
                            type="checkbox"
                            name="stok"
                            value="tersedia"
                            onchange="this.form.submit()"
                            {{ request('stok') == 'tersedia' ? 'checked' : '' }}
                        />

                        <span class="text-[12px] font-bold text-user-ink">
                            Tersedia Saja
                        </span>
                    </label>
                </div>

                {{-- Reset --}}
                <div class="pt-3 border-t-[2px] border-user-ink">
                    <a href="{{ route('user.katalog') }}"
                       class="flex items-center justify-center gap-2 w-full bg-user-gold border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover px-3 py-2.5 text-[11px] font-extrabold uppercase tracking-[0.12em] text-user-ink">
                        <span class="material-symbols-outlined text-[17px]">restart_alt</span>
                        Reset Filter
                    </a>
                </div>
            </form>
        </aside>

        {{-- Book Grid --}}
        <main class="min-w-0 space-y-4">

            {{-- Info + Sort --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm p-3 sm:p-4">
                <p class="text-[11px] sm:text-[12px] text-user-ink/70 font-extrabold uppercase tracking-[0.12em]">
                    Menampilkan
                    <span class="text-user-ink">{{ $buku->total() }}</span>
                    buku ditemukan
                </p>

                <form action="{{ route('user.katalog') }}"
                      method="GET"
                      class="flex items-center gap-2 bg-white border-[2px] border-user-ink px-3 py-2 min-w-0 sm:min-w-[230px]">

                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    @if(request('kategori'))
                        <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                    @endif

                    @if(request('tahun'))
                        <input type="hidden" name="tahun" value="{{ request('tahun') }}">
                    @endif

                    @if(request('stok'))
                        <input type="hidden" name="stok" value="{{ request('stok') }}">
                    @endif

                    <span class="text-[11px] font-extrabold uppercase tracking-[0.12em] text-user-ink/60 shrink-0">
                        Sort:
                    </span>

                    <select name="sort"
                            onchange="this.form.submit()"
                            class="w-full bg-transparent border-none focus:ring-0 font-extrabold text-user-ink pr-7 cursor-pointer text-[12px]">
                        <option value="terbaru" {{ request('sort', 'terbaru') == 'terbaru' ? 'selected' : '' }}>
                            Terbaru
                        </option>

                        <option value="terlama" {{ request('sort') == 'terlama' ? 'selected' : '' }}>
                            Terlama
                        </option>

                        <option value="judul_az" {{ request('sort') == 'judul_az' ? 'selected' : '' }}>
                            Judul A-Z
                        </option>

                        <option value="judul_za" {{ request('sort') == 'judul_za' ? 'selected' : '' }}>
                            Judul Z-A
                        </option>

                        <option value="tahun_terbaru" {{ request('sort') == 'tahun_terbaru' ? 'selected' : '' }}>
                            Tahun Terbaru
                        </option>

                        <option value="tahun_terlama" {{ request('sort') == 'tahun_terlama' ? 'selected' : '' }}>
                            Tahun Terlama
                        </option>

                        <option value="stok_terbanyak" {{ request('sort') == 'stok_terbanyak' ? 'selected' : '' }}>
                            Stok Terbanyak
                        </option>
                    </select>
                </form>
            </div>

            @if($buku->count() > 0)

                {{-- Catalog Grid --}}
                <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
                    @foreach($buku as $item)
                        <article class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm overflow-hidden soft-brutal-hover flex flex-col h-full {{ $item->stock <= 0 ? 'opacity-80' : '' }}">

                            {{-- Cover --}}
                            <div class="relative aspect-[3/4] overflow-hidden border-b-[2px] border-user-ink bg-user-paper group">
                                @if($item->stock <= 0)
                                    <div class="absolute inset-0 bg-user-ink/45 z-20 flex items-center justify-center">
                                        <span class="bg-user-paper text-user-ink border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold px-2 py-1 uppercase">
                                            Habis
                                        </span>
                                    </div>
                                @endif

                                @if($item->cover)
                                    <img
                                        class="w-full h-full object-cover transition-transform duration-300 {{ $item->stock > 0 ? 'group-hover:scale-[1.04]' : 'grayscale-[45%]' }}"
                                        src="{{ asset('storage/' . $item->cover) }}"
                                        alt="{{ $item->judul }}"
                                    />
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-user-ink/60">
                                        <span class="material-symbols-outlined text-[44px] sm:text-[60px]">
                                            menu_book
                                        </span>
                                    </div>
                                @endif

                                <div class="absolute top-2 left-2 z-30 max-w-[80%]">
                                    <span class="inline-block bg-user-gold text-user-ink border-[2px] border-user-ink text-[8px] sm:text-[9px] font-extrabold uppercase tracking-[0.1em] px-2 py-1 truncate max-w-full">
                                        {{ $item->kategori ?? 'Umum' }}
                                    </span>
                                </div>

                                <div class="absolute top-2 right-2 z-30">
                                    <span class="inline-block bg-white text-user-ink border-[2px] border-user-ink text-[8px] sm:text-[9px] font-extrabold px-2 py-1">
                                        {{ $item->tahun_terbit ?? '-' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-3 flex flex-col flex-1">
                                <h3 class="text-[12px] sm:text-[14px] font-extrabold text-user-ink leading-snug line-clamp-2 min-h-[2.25rem] sm:min-h-[2.65rem]">
                                    {{ $item->judul }}
                                </h3>

                                <p class="text-[10px] sm:text-[12px] text-user-ink/70 font-semibold mt-1.5 line-clamp-2 min-h-[2rem]">
                                    {{ $item->pengarang ?? '-' }}
                                </p>

                                <div class="mt-auto pt-3 space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        @if($item->stock > 0)
                                            <div class="flex items-center gap-1.5 min-w-0">
                                                <span class="w-2 h-2 bg-green-500 border border-user-ink shrink-0"></span>
                                                <span class="text-[9px] sm:text-[10px] font-extrabold uppercase text-green-700 truncate">
                                                    Tersedia
                                                </span>
                                            </div>

                                            <span class="text-[8px] sm:text-[9px] font-bold text-user-ink/60 truncate">
                                                {{ $item->lokasi ?? '-' }}
                                            </span>
                                        @else
                                            <div class="flex items-center gap-1.5 min-w-0">
                                                <span class="w-2 h-2 bg-red-500 border border-user-ink shrink-0"></span>
                                                <span class="text-[9px] sm:text-[10px] font-extrabold uppercase text-red-700 truncate">
                                                    Habis
                                                </span>
                                            </div>

                                            <span class="text-[8px] sm:text-[9px] font-bold text-user-ink/60">
                                                Stok 0
                                            </span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="{{ route('user.katalog.show', $item->id_buku) }}"
                                           class="flex items-center justify-center bg-user-paper text-user-ink border-[2px] border-user-ink px-2 py-2 soft-brutal-shadow-sm soft-brutal-hover text-[10px] sm:text-[11px] font-extrabold uppercase">
                                            Detail
                                        </a>

                                        @if($item->stock > 0)
                                            <a href="{{ route('user.katalog.show', $item->id_buku) }}"
                                               class="flex items-center justify-center bg-user-navy text-white border-[2px] border-user-ink px-2 py-2 soft-brutal-shadow-sm soft-brutal-hover text-[10px] sm:text-[11px] font-extrabold uppercase">
                                                Pinjam
                                            </a>
                                        @else
                                            <button disabled
                                                    class="flex items-center justify-center bg-gray-200 text-user-ink/50 border-[2px] border-user-ink px-2 py-2 text-[10px] sm:text-[11px] font-extrabold uppercase cursor-not-allowed">
                                                Antri
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if($buku->hasPages())
                    <div class="pt-3 overflow-x-auto">
                        <div class="inline-block min-w-full">
                            {{ $buku->withQueryString()->links() }}
                        </div>
                    </div>
                @endif

            @else
                <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-8 text-center">
                    <span class="material-symbols-outlined text-[48px] sm:text-[60px] text-user-ink/45 mb-3">
                        menu_book
                    </span>

                    <h3 class="font-headline-sm text-[18px] uppercase text-user-ink mb-2">
                        Buku tidak ditemukan
                    </h3>

                    <p class="text-[12px] sm:text-[14px] text-user-ink/70 font-semibold">
                        Coba gunakan kata kunci atau filter yang berbeda.
                    </p>

                    <a href="{{ route('user.katalog') }}"
                       class="inline-flex items-center justify-center mt-5 bg-user-gold text-user-ink border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover px-4 py-2.5 text-[11px] font-extrabold uppercase tracking-[0.12em]">
                        Reset Filter
                    </a>
                </div>
            @endif

        </main>
    </div>
</div>

@endsection
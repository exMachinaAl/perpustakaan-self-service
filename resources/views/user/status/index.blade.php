@extends('layouts.user')

@section('title', 'Status Peminjaman - Perpustakaan')

@section('search_action', route('user.peminjaman.cek'))
@section('search_placeholder', 'Cari judul buku atau penulis...')

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
        <a href="{{ route('user.dashboard') }}" class="hover:text-user-gold transition">
            Dashboard
        </a>

        <span class="material-symbols-outlined text-[16px]">chevron_right</span>

        <span class="text-user-ink">
            Status Peminjaman
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
                        pending_actions
                    </span>
                    <span class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.16em]">
                        Borrowing Status
                    </span>
                </div>

                <h1 class="font-display-lg text-[24px] sm:text-[32px] lg:text-[38px] leading-tight uppercase text-user-ink">
                    Daftar Status Peminjaman
                </h1>

                <p class="text-[12px] sm:text-[14px] text-user-ink/70 font-semibold max-w-2xl">
                    Pantau request peminjaman, buku yang sedang dipinjam, bukti terverifikasi, pengembalian, dan status pembatalan.
                </p>
            </div>

            <a href="{{ route('user.katalog') }}"
               class="inline-flex items-center justify-center gap-2 bg-user-navy text-white border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover px-4 py-2.5 text-[11px] sm:text-[12px] font-extrabold uppercase tracking-wide">
                <span class="material-symbols-outlined text-[18px]">menu_book</span>
                Cari Buku
            </a>
        </div>
    </section>

    {{-- Summary Cards --}}
    <section class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">

        <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-3 sm:p-4 flex items-center gap-3 soft-brutal-hover">
            <div class="w-11 h-11 sm:w-12 sm:h-12 bg-user-gold border-[2px] border-user-ink flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-user-ink">hourglass_empty</span>
            </div>

            <div class="min-w-0">
                <p class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.12em] text-user-ink/60 truncate">
                    Menunggu
                </p>
                <p class="text-[21px] sm:text-[28px] font-extrabold text-user-ink leading-none mt-1">
                    {{ str_pad($totalMenunggu ?? 0, 2, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>

        <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-3 sm:p-4 flex items-center gap-3 soft-brutal-hover">
            <div class="w-11 h-11 sm:w-12 sm:h-12 bg-user-blue-soft border-[2px] border-user-ink flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-user-ink">menu_book</span>
            </div>

            <div class="min-w-0">
                <p class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.12em] text-user-ink/60 truncate">
                    Dipinjam
                </p>
                <p class="text-[21px] sm:text-[28px] font-extrabold text-user-ink leading-none mt-1">
                    {{ str_pad($totalDipinjam ?? 0, 2, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>

        <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-3 sm:p-4 flex items-center gap-3 soft-brutal-hover">
            <div class="w-11 h-11 sm:w-12 sm:h-12 bg-green-200 border-[2px] border-user-ink flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-user-ink">assignment_turned_in</span>
            </div>

            <div class="min-w-0">
                <p class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.12em] text-user-ink/60 truncate">
                    Dikembalikan
                </p>
                <p class="text-[21px] sm:text-[28px] font-extrabold text-user-ink leading-none mt-1">
                    {{ str_pad($totalDikembalikan ?? 0, 2, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>

        <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-3 sm:p-4 flex items-center gap-3 soft-brutal-hover">
            <div class="w-11 h-11 sm:w-12 sm:h-12 bg-red-100 border-[2px] border-user-ink flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-user-ink">cancel</span>
            </div>

            <div class="min-w-0">
                <p class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.12em] text-user-ink/60 truncate">
                    Ditolak
                </p>
                <p class="text-[21px] sm:text-[28px] font-extrabold text-user-ink leading-none mt-1">
                    {{ str_pad($totalDibatalkan ?? 0, 2, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>

    </section>

    {{-- Alert --}}
    @if(session('success'))
        <div class="bg-green-100 border-[2px] border-user-ink soft-brutal-shadow-sm px-4 py-3 text-[12px] sm:text-[13px] font-bold text-user-ink">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-[2px] border-user-ink soft-brutal-shadow-sm px-4 py-3 text-[12px] sm:text-[13px] font-bold text-user-ink">
            {{ session('error') }}
        </div>
    @endif

    {{-- Data Table Container --}}
    <section class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm overflow-hidden">

        {{-- Table Header --}}
        <div class="bg-user-paper border-b-[2px] border-user-ink p-4 sm:p-5 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-user-ink">receipt_long</span>
                <h2 class="font-headline-sm text-[16px] sm:text-[19px] uppercase text-user-ink">
                    Data Peminjaman
                </h2>
            </div>

            <form id="statusFilterForm"
                  action="{{ route('user.peminjaman.cek') }}"
                  method="GET"
                  class="grid grid-cols-1 sm:grid-cols-[1fr_170px_auto] gap-2 w-full xl:w-auto">

                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-user-ink text-[19px]">
                        search
                    </span>

                    <input
                        id="searchInput"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari judul / penulis..."
                        autocomplete="off"
                        class="w-full sm:w-[230px] bg-white border-[2px] border-user-ink rounded-none pl-10 pr-4 py-2.5 text-[12px] font-bold text-user-ink focus:ring-0 focus:outline-none focus:shadow-[0_0_0_3px_rgba(201,162,39,0.28)]"
                    >
                </div>

                <select
                    id="statusSelect"
                    name="status"
                    class="w-full bg-white border-[2px] border-user-ink rounded-none px-3 py-2.5 text-[12px] font-bold text-user-ink focus:ring-0 focus:outline-none focus:shadow-[0_0_0_3px_rgba(201,162,39,0.28)]"
                >
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>

                @if(request('search') || request('status'))
                    <a href="{{ route('user.peminjaman.cek') }}"
                       class="inline-flex items-center justify-center gap-2 bg-user-gold text-user-ink border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover px-4 py-2.5 text-[11px] font-extrabold uppercase tracking-wide">
                        Reset
                    </a>
                @endif

            </form>
        </div>

        {{-- Desktop Table --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-[980px] w-full border-collapse">
                <thead>
                    <tr class="bg-user-navy text-white border-b-[2px] border-user-ink">
                        <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold w-14">No</th>
                        <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold w-24">Cover</th>
                        <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold min-w-[250px]">Judul Buku</th>
                        <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold whitespace-nowrap">Tgl Pinjam</th>
                        <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold whitespace-nowrap">Batas</th>
                        <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold whitespace-nowrap">Kembali</th>
                        <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold">Status</th>
                        <th class="px-4 py-3 text-center text-[10px] uppercase tracking-[0.12em] font-extrabold">Aksi / Bukti</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($peminjaman as $item)
                        @php
                            $tanggalPinjam = $item->tanggal_pinjam ?? $item->tanggal_peminjaman ?? null;
                            $batasPengembalian = $item->batas_pengembalian ?? null;

                            if (!$batasPengembalian && $tanggalPinjam) {
                                $batasPengembalian = \Carbon\Carbon::parse($tanggalPinjam)->addDays(7);
                            }

                            $status = strtolower($item->status ?? '-');
                        @endphp

                        <tr class="border-b-[2px] border-user-ink/10 hover:bg-user-blue-soft/35 transition">
                            <td class="px-4 py-3 text-[12px] font-extrabold text-user-ink">
                                {{ $loop->iteration + ($peminjaman->currentPage() - 1) * $peminjaman->perPage() }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="w-10 h-14 bg-user-paper border-[2px] border-user-ink overflow-hidden {{ in_array($status, ['ditolak', 'dibatalkan']) ? 'opacity-70 grayscale' : '' }}">
                                    @if($item->buku && $item->buku->cover)
                                        <img
                                            alt="Book Cover"
                                            class="w-full h-full object-cover"
                                            src="{{ asset('storage/' . $item->buku->cover) }}"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-user-ink/60">
                                            <span class="material-symbols-outlined text-[20px]">menu_book</span>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div class="{{ in_array($status, ['ditolak', 'dibatalkan']) ? 'opacity-70' : '' }}">
                                    <p class="text-[13px] font-extrabold text-user-ink truncate max-w-[260px] {{ in_array($status, ['ditolak', 'dibatalkan']) ? 'line-through' : '' }}">
                                        {{ $item->buku->judul ?? '-' }}
                                    </p>

                                    <p class="text-[11px] text-user-ink/65 font-semibold truncate max-w-[260px] mt-1">
                                        {{ $item->buku->pengarang ?? '-' }}
                                    </p>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-[12px] text-user-ink/80 font-semibold whitespace-nowrap">
                                {{ $tanggalPinjam ? \Carbon\Carbon::parse($tanggalPinjam)->format('d M Y') : '—' }}
                            </td>

                            <td class="px-4 py-3 text-[12px] font-semibold whitespace-nowrap
                                {{ $status == 'dipinjam' && $batasPengembalian && now()->gt(\Carbon\Carbon::parse($batasPengembalian)) ? 'text-red-700' : 'text-user-ink/80' }}">
                                {{ $batasPengembalian ? \Carbon\Carbon::parse($batasPengembalian)->format('d M Y') : '—' }}
                            </td>

                            <td class="px-4 py-3 text-[12px] text-user-ink/80 font-semibold whitespace-nowrap">
                                {{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->format('d M Y') : '—' }}
                            </td>

                            <td class="px-4 py-3">
                                @if($status == 'menunggu')
                                    <span class="inline-block px-2.5 py-1 bg-user-gold border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                        Menunggu
                                    </span>
                                @elseif($status == 'dipinjam')
                                    <span class="inline-block px-2.5 py-1 bg-user-blue-soft border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                        Dipinjam
                                    </span>
                                @elseif($status == 'dikembalikan')
                                    <span class="inline-block px-2.5 py-1 bg-green-200 border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                        Dikembalikan
                                    </span>
                                @elseif($status == 'ditolak')
                                    <span class="inline-block px-2.5 py-1 bg-red-100 border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                        Ditolak
                                    </span>
                                @elseif($status == 'dibatalkan')
                                    <span class="inline-block px-2.5 py-1 bg-red-100 border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                        Dibatalkan
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-1 bg-gray-100 border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                        {{ $item->status }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- Detail --}}
                                    @if($item->buku)
                                        <a
                                            href="{{ route('user.katalog.show', $item->buku->id_buku) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover text-user-ink"
                                            title="Detail"
                                        >
                                            <span class="material-symbols-outlined text-[19px]">visibility</span>
                                        </a>
                                    @endif

                                    {{-- Bukti Peminjaman - hanya muncul setelah disetujui admin --}}
                                    @if($status == 'dipinjam')
                                        <a
                                            href="{{ route('user.peminjaman.bukti', $item->peminjaman_id) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-user-gold border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover text-user-ink"
                                            title="Bukti Peminjaman"
                                        >
                                            <span class="material-symbols-outlined text-[19px]">receipt_long</span>
                                        </a>
                                    @endif
                                    
                                    {{-- Tidak Jadi Pinjam --}}
                                    @if($status == 'menunggu')
                                        <form
                                            action="{{ route('user.peminjaman.tidakJadi', $item->peminjaman_id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin tidak jadi meminjam buku ini?')"
                                        >
                                            @csrf
                                            @method('PUT')

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center w-9 h-9 bg-red-100 border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover text-user-ink"
                                                title="Tidak Jadi Pinjam"
                                            >
                                                <span class="material-symbols-outlined text-[19px]">close</span>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="material-symbols-outlined text-[44px] text-user-ink/45">
                                        inventory_2
                                    </span>

                                    <p class="font-extrabold uppercase tracking-wide text-user-ink">
                                        Belum ada data peminjaman
                                    </p>

                                    <p class="text-[12px] text-user-ink/65 font-semibold">
                                        Data buku yang kamu pinjam akan muncul di sini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="lg:hidden divide-y-[2px] divide-user-ink/10">
            @forelse($peminjaman as $item)
                @php
                    $tanggalPinjam = $item->tanggal_pinjam ?? $item->tanggal_peminjaman ?? null;
                    $batasPengembalian = $item->batas_pengembalian ?? null;

                    if (!$batasPengembalian && $tanggalPinjam) {
                        $batasPengembalian = \Carbon\Carbon::parse($tanggalPinjam)->addDays(7);
                    }

                    $status = strtolower($item->status ?? '-');
                @endphp

                <article class="p-4 bg-white">
                    <div class="flex gap-3">
                        <div class="w-16 h-[86px] bg-user-paper border-[2px] border-user-ink overflow-hidden shrink-0 {{ in_array($status, ['ditolak', 'dibatalkan']) ? 'opacity-70 grayscale' : '' }}">
                            @if($item->buku && $item->buku->cover)
                                <img
                                    alt="Book Cover"
                                    class="w-full h-full object-cover"
                                    src="{{ asset('storage/' . $item->buku->cover) }}"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center text-user-ink/60">
                                    <span class="material-symbols-outlined text-[24px]">menu_book</span>
                                </div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <h3 class="text-[13px] font-extrabold text-user-ink leading-snug line-clamp-2 {{ in_array($status, ['ditolak', 'dibatalkan']) ? 'line-through opacity-70' : '' }}">
                                        {{ $item->buku->judul ?? '-' }}
                                    </h3>

                                    <p class="text-[11px] text-user-ink/65 font-semibold truncate mt-1">
                                        {{ $item->buku->pengarang ?? '-' }}
                                    </p>
                                </div>

                                @if($status == 'menunggu')
                                    <span class="shrink-0 inline-block px-2 py-1 bg-user-gold border-[2px] border-user-ink text-[8px] font-extrabold uppercase text-user-ink">
                                        Menunggu
                                    </span>
                                @elseif($status == 'dipinjam')
                                    <span class="shrink-0 inline-block px-2 py-1 bg-user-blue-soft border-[2px] border-user-ink text-[8px] font-extrabold uppercase text-user-ink">
                                        Dipinjam
                                    </span>
                                @elseif($status == 'dikembalikan')
                                    <span class="shrink-0 inline-block px-2 py-1 bg-green-200 border-[2px] border-user-ink text-[8px] font-extrabold uppercase text-user-ink">
                                        Kembali
                                    </span>
                                @elseif($status == 'ditolak')
                                    <span class="shrink-0 inline-block px-2 py-1 bg-red-100 border-[2px] border-user-ink text-[8px] font-extrabold uppercase text-user-ink">
                                        Ditolak
                                    </span>
                                @elseif($status == 'dibatalkan')
                                    <span class="shrink-0 inline-block px-2 py-1 bg-red-100 border-[2px] border-user-ink text-[8px] font-extrabold uppercase text-user-ink">
                                        Batal
                                    </span>
                                @else
                                    <span class="shrink-0 inline-block px-2 py-1 bg-gray-100 border-[2px] border-user-ink text-[8px] font-extrabold uppercase text-user-ink">
                                        {{ $item->status }}
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-2 mt-3 text-[11px]">
                                <div class="bg-user-paper border-[2px] border-user-ink p-2">
                                    <p class="font-extrabold uppercase text-user-ink/55 text-[8px] tracking-[0.1em]">
                                        Tgl Pinjam
                                    </p>
                                    <p class="font-bold text-user-ink mt-1">
                                        {{ $tanggalPinjam ? \Carbon\Carbon::parse($tanggalPinjam)->format('d M Y') : '—' }}
                                    </p>
                                </div>

                                <div class="bg-user-paper border-[2px] border-user-ink p-2">
                                    <p class="font-extrabold uppercase text-user-ink/55 text-[8px] tracking-[0.1em]">
                                        Batas
                                    </p>
                                    <p class="font-bold mt-1 {{ $status == 'dipinjam' && $batasPengembalian && now()->gt(\Carbon\Carbon::parse($batasPengembalian)) ? 'text-red-700' : 'text-user-ink' }}">
                                        {{ $batasPengembalian ? \Carbon\Carbon::parse($batasPengembalian)->format('d M Y') : '—' }}
                                    </p>
                                </div>

                                <div class="bg-user-paper border-[2px] border-user-ink p-2">
                                    <p class="font-extrabold uppercase text-user-ink/55 text-[8px] tracking-[0.1em]">
                                        Tgl Kembali
                                    </p>
                                    <p class="font-bold text-user-ink mt-1">
                                        {{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->format('d M Y') : '—' }}
                                    </p>
                                </div>

                                <div class="bg-user-paper border-[2px] border-user-ink p-2">
                                    <p class="font-extrabold uppercase text-user-ink/55 text-[8px] tracking-[0.1em]">
                                        No
                                    </p>
                                    <p class="font-bold text-user-ink mt-1">
                                        {{ $loop->iteration + ($peminjaman->currentPage() - 1) * $peminjaman->perPage() }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex gap-2 mt-3">
                                {{-- Detail --}}
                                @if($item->buku)
                                    <a href="{{ route('user.katalog.show', $item->buku->id_buku) }}"
                                       class="flex-1 inline-flex items-center justify-center gap-1 bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm px-3 py-2 text-[10px] font-extrabold uppercase text-user-ink">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        Detail
                                    </a>
                                @endif

                                {{-- Bukti Peminjaman - hanya muncul setelah disetujui admin --}}
                                @if($status == 'dipinjam')
                                    <a href="{{ route('user.peminjaman.bukti', $item->peminjaman_id) }}"
                                       class="flex-1 inline-flex items-center justify-center gap-1 bg-user-gold border-[2px] border-user-ink soft-brutal-shadow-sm px-3 py-2 text-[10px] font-extrabold uppercase text-user-ink">
                                        <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                        Bukti
                                    </a>
                                @endif

                                {{-- Tidak Jadi Pinjam --}}
                                @if($status == 'menunggu')
                                    <form action="{{ route('user.peminjaman.tidakJadi', $item->peminjaman_id) }}"
                                          method="POST"
                                          class="flex-1"
                                          onsubmit="return confirm('Yakin tidak jadi meminjam buku ini?')">
                                        @csrf
                                        @method('PUT')

                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-1 bg-red-100 border-[2px] border-user-ink soft-brutal-shadow-sm px-3 py-2 text-[10px] font-extrabold uppercase text-user-ink">
                                            <span class="material-symbols-outlined text-[16px]">close</span>
                                            Batal
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="p-8 text-center">
                    <span class="material-symbols-outlined text-[44px] text-user-ink/45">
                        inventory_2
                    </span>

                    <p class="font-extrabold uppercase tracking-wide text-user-ink mt-2">
                        Belum ada data peminjaman
                    </p>

                    <p class="text-[12px] text-user-ink/65 font-semibold mt-1">
                        Data buku yang kamu pinjam akan muncul di sini.
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($peminjaman->total() > 0)
            <div class="bg-user-paper border-t-[2px] border-user-ink px-4 sm:px-5 py-4 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                <p class="text-[11px] sm:text-[12px] text-user-ink/65 font-extrabold uppercase tracking-[0.1em]">
                    Menampilkan
                    {{ $peminjaman->firstItem() }}
                    -
                    {{ $peminjaman->lastItem() }}
                    dari
                    {{ $peminjaman->total() }}
                    peminjaman
                </p>

                @if($peminjaman->hasPages())
                    <div class="overflow-x-auto">
                        {{ $peminjaman->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        @endif

    </section>

</div>

@endsection

@push('scripts')
<script>
    const statusFilterForm = document.getElementById('statusFilterForm');
    const searchInput = document.getElementById('searchInput');
    const statusSelect = document.getElementById('statusSelect');

    let searchTimer = null;

    if (searchInput && statusFilterForm) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);

            searchTimer = setTimeout(function () {
                statusFilterForm.submit();
            }, 500);
        });
    }

    if (statusSelect && statusFilterForm) {
        statusSelect.addEventListener('change', function () {
            statusFilterForm.submit();
        });
    }
</script>
@endpush
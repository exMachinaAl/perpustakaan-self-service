@extends('layouts.user')

@section('title', 'Riwayat Peminjaman - Perpustakaan')

@section('search_action', route('user.riwayat'))
@section('search_placeholder', 'Cari riwayat peminjaman...')

@section('content')

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

@php
    $totalDikembalikan = $totalDikembalikan ?? collect($riwayat->items())->where('status', 'dikembalikan')->count();
@endphp

<div class="space-y-5 sm:space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-[11px] sm:text-[12px] font-extrabold uppercase tracking-[0.12em] text-user-ink/70">
        <a href="{{ route('user.dashboard') }}" class="hover:text-user-gold transition">
            Dashboard
        </a>

        <span class="material-symbols-outlined text-[16px]">chevron_right</span>

        <span class="text-user-ink">
            Riwayat Peminjaman
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
                        history
                    </span>
                    <span class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.16em]">
                        Borrowing History
                    </span>
                </div>

                <h1 class="font-display-lg text-[24px] sm:text-[32px] lg:text-[38px] leading-tight uppercase text-user-ink">
                    Riwayat Peminjaman
                </h1>

                <p class="text-[12px] sm:text-[14px] text-user-ink/70 font-semibold max-w-2xl">
                    Lihat seluruh aktivitas peminjaman buku, riwayat pengembalian, keterlambatan, dan informasi denda.
                </p>
            </div>

            <a href="{{ route('user.katalog') }}"
               class="inline-flex items-center justify-center gap-2 bg-user-navy text-white border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover px-4 py-2.5 text-[11px] sm:text-[12px] font-extrabold uppercase tracking-wide">
                <span class="material-symbols-outlined text-[18px]">menu_book</span>
                Buka Katalog
            </a>
        </div>
    </section>

    {{-- Table Container --}}
    <section class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm overflow-hidden">

        {{-- Header Filter --}}
        <div class="bg-user-paper border-b-[2px] border-user-ink p-4 sm:p-5 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-user-ink text-[22px]">receipt_long</span>
                <h2 class="font-headline-sm text-[16px] sm:text-[19px] uppercase text-user-ink">
                    Data Peminjaman
                </h2>
            </div>

            <form id="riwayatFilterForm"
                  action="{{ route('user.riwayat') }}"
                  method="GET"
                  class="flex flex-col sm:flex-row gap-2 w-full xl:w-auto">

                <div class="relative w-full sm:w-[230px]">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-user-ink text-[19px]">
                        search
                    </span>

                    <input
                        id="riwayatSearchInput"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari judul / penulis..."
                        autocomplete="off"
                        class="w-full bg-white border-[2px] border-user-ink rounded-none pl-10 pr-4 py-2.5 text-[12px] font-bold text-user-ink focus:ring-0 focus:outline-none focus:shadow-[0_0_0_3px_rgba(201,162,39,0.28)]"
                    >
                </div>

                <div class="relative w-full sm:w-[170px]">
                    <select
                        id="riwayatStatusSelect"
                        name="status"
                        class="w-full bg-white border-[2px] border-user-ink rounded-none px-3 py-2.5 pr-9 text-[12px] font-bold text-user-ink focus:ring-0 focus:outline-none focus:shadow-[0_0_0_3px_rgba(201,162,39,0.28)] appearance-none"
                    >
                        <option value="">Semua</option>
                        <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                        <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </select>

                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-user-ink text-[18px]">
                        expand_more
                    </span>
                </div>
            </form>
        </div>

        @if($riwayat->count() > 0)

            {{-- Desktop Table --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-[1120px] w-full border-collapse">
                    <thead>
                        <tr class="bg-user-navy text-white border-b-[2px] border-user-ink">
                            <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold w-14">
                                No
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold w-20">
                                Cover
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold min-w-[250px]">
                                Judul Buku
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold whitespace-nowrap">
                                Tgl Pinjam
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold whitespace-nowrap">
                                Tgl Kembali
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold">
                                Status
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold min-w-[160px]">
                                Denda
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] uppercase tracking-[0.12em] font-extrabold min-w-[210px]">
                                Catatan
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($riwayat as $item)
                            @php
                                $tanggalPinjam = $item->tanggal_pinjam ?? $item->tanggal_peminjaman ?? null;

                                $batasKembali = $tanggalPinjam
                                    ? \Carbon\Carbon::parse($tanggalPinjam)->addDays(7)
                                    : null;

                                $isTerlambat = $item->status == 'dikembalikan'
                                    && $item->tanggal_kembali
                                    && $batasKembali
                                    && \Carbon\Carbon::parse($item->tanggal_kembali)->gt($batasKembali);

                                $jumlahHariTerlambat = 0;

                                if ($isTerlambat) {
                                    $jumlahHariTerlambat = $batasKembali->diffInDays(\Carbon\Carbon::parse($item->tanggal_kembali));
                                }

                                $dendaTotal = (int) ($item->denda ?? 0);
                                $dendaDibayar = (int) ($item->denda_dibayar ?? 0);
                                $sisaDenda = max($dendaTotal - $dendaDibayar, 0);
                            @endphp

                            <tr class="border-b-[2px] border-user-ink/10 hover:bg-user-blue-soft/35 transition">
                                <td class="px-4 py-3 text-[12px] font-extrabold text-user-ink">
                                    {{ str_pad($riwayat->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="w-10 h-14 bg-user-paper border-[2px] border-user-ink overflow-hidden">
                                        @if($item->buku && $item->buku->cover)
                                            <img
                                                class="w-full h-full object-cover"
                                                src="{{ asset('storage/' . $item->buku->cover) }}"
                                                alt="Cover buku"
                                            >
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-user-ink/60">
                                                <span class="material-symbols-outlined text-[20px]">menu_book</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <p class="text-[13px] font-extrabold text-user-ink truncate max-w-[280px]">
                                        {{ $item->buku->judul ?? 'Buku tidak ditemukan' }}
                                    </p>

                                    <p class="text-[11px] text-user-ink/65 font-semibold truncate max-w-[280px] mt-1">
                                        {{ $item->buku->pengarang ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-4 py-3 text-[12px] text-user-ink/80 font-semibold whitespace-nowrap">
                                    {{ $tanggalPinjam ? \Carbon\Carbon::parse($tanggalPinjam)->translatedFormat('d M Y') : '-' }}
                                </td>

                                <td class="px-4 py-3 text-[12px] text-user-ink/80 font-semibold whitespace-nowrap">
                                    {{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->translatedFormat('d M Y') : '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    @if($isTerlambat)
                                        <span class="inline-block px-2.5 py-1 bg-red-100 border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                            Terlambat {{ $jumlahHariTerlambat }} Hari
                                        </span>
                                    @elseif($item->status == 'dikembalikan')
                                        <span class="inline-block px-2.5 py-1 bg-green-200 border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                            Dikembalikan
                                        </span>
                                    @elseif($item->status == 'dipinjam')
                                        <span class="inline-block px-2.5 py-1 bg-user-blue-soft border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                            Dipinjam
                                        </span>
                                    @elseif($item->status == 'menunggu')
                                        <span class="inline-block px-2.5 py-1 bg-user-gold border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                            Menunggu
                                        </span>
                                    @elseif($item->status == 'dibatalkan')
                                        <span class="inline-block px-2.5 py-1 bg-gray-200 border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                            Dibatalkan
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-1 bg-gray-100 border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Denda --}}
                                <td class="px-4 py-3">
                                    @if($dendaTotal > 0)
                                        <div class="space-y-1 min-w-[150px]">
                                            <p class="text-[11px] font-extrabold text-user-ink">
                                                Total: Rp {{ number_format($dendaTotal, 0, ',', '.') }}
                                            </p>

                                            <p class="text-[11px] font-bold text-user-ink/70">
                                                Dibayar: Rp {{ number_format($dendaDibayar, 0, ',', '.') }}
                                            </p>

                                            <p class="text-[11px] font-extrabold {{ $sisaDenda > 0 ? 'text-red-700' : 'text-green-700' }}">
                                                Sisa: Rp {{ number_format($sisaDenda, 0, ',', '.') }}
                                            </p>

                                            @if($item->status_denda === 'lunas')
                                                <span class="inline-block mt-1 px-2 py-1 bg-green-200 border-[2px] border-user-ink text-[9px] font-extrabold uppercase text-user-ink">
                                                    Lunas
                                                </span>
                                            @else
                                                <span class="inline-block mt-1 px-2 py-1 bg-red-100 border-[2px] border-user-ink text-[9px] font-extrabold uppercase text-user-ink">
                                                    Belum Lunas
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[12px] font-bold text-user-ink/50">
                                            -
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-[12px] text-user-ink/70 font-semibold">
                                    @if($isTerlambat)
                                        Terlambat {{ $jumlahHariTerlambat }} hari.
                                        @if($dendaTotal > 0)
                                            Denda Rp {{ number_format($dendaTotal, 0, ',', '.') }}.
                                        @endif
                                    @elseif($item->status == 'dikembalikan')
                                        Kondisi buku telah dikembalikan.
                                    @elseif($item->status == 'dipinjam')
                                        Buku masih dipinjam.
                                    @elseif($item->status == 'menunggu')
                                        Menunggu verifikasi admin.
                                    @elseif($item->status == 'dibatalkan')
                                        Peminjaman dibatalkan.
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="lg:hidden divide-y-[2px] divide-user-ink/10">
                @foreach($riwayat as $item)
                    @php
                        $tanggalPinjam = $item->tanggal_pinjam ?? $item->tanggal_peminjaman ?? null;

                        $batasKembali = $tanggalPinjam
                            ? \Carbon\Carbon::parse($tanggalPinjam)->addDays(7)
                            : null;

                        $isTerlambat = $item->status == 'dikembalikan'
                            && $item->tanggal_kembali
                            && $batasKembali
                            && \Carbon\Carbon::parse($item->tanggal_kembali)->gt($batasKembali);

                        $jumlahHariTerlambat = 0;

                        if ($isTerlambat) {
                            $jumlahHariTerlambat = $batasKembali->diffInDays(\Carbon\Carbon::parse($item->tanggal_kembali));
                        }

                        $dendaTotal = (int) ($item->denda ?? 0);
                        $dendaDibayar = (int) ($item->denda_dibayar ?? 0);
                        $sisaDenda = max($dendaTotal - $dendaDibayar, 0);
                    @endphp

                    <article class="p-4 bg-white">
                        <div class="flex gap-3">
                            <div class="w-16 h-[86px] bg-user-paper border-[2px] border-user-ink overflow-hidden shrink-0">
                                @if($item->buku && $item->buku->cover)
                                    <img
                                        class="w-full h-full object-cover"
                                        src="{{ asset('storage/' . $item->buku->cover) }}"
                                        alt="Cover buku"
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
                                        <h3 class="text-[13px] font-extrabold text-user-ink leading-snug line-clamp-2">
                                            {{ $item->buku->judul ?? 'Buku tidak ditemukan' }}
                                        </h3>

                                        <p class="text-[11px] text-user-ink/65 font-semibold truncate mt-1">
                                            {{ $item->buku->pengarang ?? '-' }}
                                        </p>
                                    </div>

                                    @if($isTerlambat)
                                        <span class="shrink-0 inline-block px-2 py-1 bg-red-100 border-[2px] border-user-ink text-[8px] font-extrabold uppercase text-user-ink">
                                            Terlambat
                                        </span>
                                    @elseif($item->status == 'dikembalikan')
                                        <span class="shrink-0 inline-block px-2 py-1 bg-green-200 border-[2px] border-user-ink text-[8px] font-extrabold uppercase text-user-ink">
                                            Kembali
                                        </span>
                                    @elseif($item->status == 'dipinjam')
                                        <span class="shrink-0 inline-block px-2 py-1 bg-user-blue-soft border-[2px] border-user-ink text-[8px] font-extrabold uppercase text-user-ink">
                                            Dipinjam
                                        </span>
                                    @elseif($item->status == 'menunggu')
                                        <span class="shrink-0 inline-block px-2 py-1 bg-user-gold border-[2px] border-user-ink text-[8px] font-extrabold uppercase text-user-ink">
                                            Menunggu
                                        </span>
                                    @elseif($item->status == 'dibatalkan')
                                        <span class="shrink-0 inline-block px-2 py-1 bg-gray-200 border-[2px] border-user-ink text-[8px] font-extrabold uppercase text-user-ink">
                                            Batal
                                        </span>
                                    @else
                                        <span class="shrink-0 inline-block px-2 py-1 bg-gray-100 border-[2px] border-user-ink text-[8px] font-extrabold uppercase text-user-ink">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 gap-2 mt-3 text-[11px]">
                                    <div class="bg-user-paper border-[2px] border-user-ink p-2">
                                        <p class="font-extrabold uppercase text-user-ink/55 text-[8px] tracking-[0.1em]">
                                            Tgl Pinjam
                                        </p>
                                        <p class="font-bold text-user-ink mt-1">
                                            {{ $tanggalPinjam ? \Carbon\Carbon::parse($tanggalPinjam)->translatedFormat('d M Y') : '-' }}
                                        </p>
                                    </div>

                                    <div class="bg-user-paper border-[2px] border-user-ink p-2">
                                        <p class="font-extrabold uppercase text-user-ink/55 text-[8px] tracking-[0.1em]">
                                            Tgl Kembali
                                        </p>
                                        <p class="font-bold text-user-ink mt-1">
                                            {{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->translatedFormat('d M Y') : '-' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Mobile Denda --}}
                                <div class="bg-user-paper border-[2px] border-user-ink p-2 mt-2">
                                    <p class="font-extrabold uppercase text-user-ink/55 text-[8px] tracking-[0.1em]">
                                        Denda
                                    </p>

                                    @if($dendaTotal > 0)
                                        <div class="mt-1 space-y-1 text-[11px]">
                                            <p class="font-bold text-user-ink">
                                                Total: Rp {{ number_format($dendaTotal, 0, ',', '.') }}
                                            </p>

                                            <p class="font-bold text-user-ink/70">
                                                Dibayar: Rp {{ number_format($dendaDibayar, 0, ',', '.') }}
                                            </p>

                                            <p class="font-extrabold {{ $sisaDenda > 0 ? 'text-red-700' : 'text-green-700' }}">
                                                Sisa: Rp {{ number_format($sisaDenda, 0, ',', '.') }}
                                            </p>

                                            @if($item->status_denda === 'lunas')
                                                <span class="inline-block px-2 py-1 bg-green-200 border-[2px] border-user-ink text-[8px] font-extrabold uppercase text-user-ink">
                                                    Lunas
                                                </span>
                                            @else
                                                <span class="inline-block px-2 py-1 bg-red-100 border-[2px] border-user-ink text-[8px] font-extrabold uppercase text-user-ink">
                                                    Belum Lunas
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <p class="font-bold text-user-ink/60 mt-1 text-[11px]">
                                            Tidak ada denda
                                        </p>
                                    @endif
                                </div>

                                <div class="bg-user-paper border-[2px] border-user-ink p-2 mt-2">
                                    <p class="font-extrabold uppercase text-user-ink/55 text-[8px] tracking-[0.1em]">
                                        Catatan
                                    </p>
                                    <p class="font-bold text-user-ink/75 mt-1 text-[11px]">
                                        @if($isTerlambat)
                                            Terlambat {{ $jumlahHariTerlambat }} hari.
                                            @if($dendaTotal > 0)
                                                Denda Rp {{ number_format($dendaTotal, 0, ',', '.') }}.
                                            @endif
                                        @elseif($item->status == 'dikembalikan')
                                            Kondisi buku telah dikembalikan.
                                        @elseif($item->status == 'dipinjam')
                                            Buku masih dipinjam.
                                        @elseif($item->status == 'menunggu')
                                            Menunggu verifikasi admin.
                                        @elseif($item->status == 'dibatalkan')
                                            Peminjaman dibatalkan.
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="bg-user-paper border-t-[2px] border-user-ink px-4 sm:px-5 py-4 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                <p class="text-[11px] sm:text-[12px] text-user-ink/65 font-extrabold uppercase tracking-[0.1em]">
                    Menampilkan
                    {{ $riwayat->firstItem() }}
                    -
                    {{ $riwayat->lastItem() }}
                    dari
                    {{ $riwayat->total() }}
                    riwayat
                </p>

                @if($riwayat->hasPages())
                    <div class="overflow-x-auto">
                        {{ $riwayat->withQueryString()->links() }}
                    </div>
                @endif
            </div>

        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-14 sm:py-16 text-center px-4">
                <div class="w-24 h-24 bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm flex items-center justify-center mb-5">
                    <span class="material-symbols-outlined text-[54px] text-user-ink/60">
                        history_edu
                    </span>
                </div>

                <h2 class="font-headline-md text-[20px] sm:text-[24px] uppercase text-user-ink mb-2">
                    Belum ada riwayat
                </h2>

                <p class="text-[12px] sm:text-[14px] text-user-ink/65 font-semibold max-w-md mx-auto">
                    Anda belum pernah melakukan peminjaman buku. Silakan kunjungi katalog untuk mulai meminjam buku favorit Anda.
                </p>

                <a href="{{ route('user.katalog') }}"
                   class="mt-6 inline-flex items-center justify-center gap-2 bg-user-navy text-white border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover px-5 py-2.5 text-[11px] sm:text-[12px] font-extrabold uppercase tracking-wide">
                    <span class="material-symbols-outlined text-[18px]">menu_book</span>
                    Buka Katalog Buku
                </a>
            </div>
        @endif
    </section>

    {{-- Footer Info --}}
    <section class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-4 flex items-center gap-3 soft-brutal-hover">
            <div class="w-11 h-11 bg-user-blue-soft border-[2px] border-user-ink flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-user-ink">auto_stories</span>
            </div>

            <div>
                <p class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.12em] text-user-ink/60">
                    Total Peminjaman
                </p>
                <p class="text-[20px] sm:text-[24px] font-extrabold text-user-ink leading-none mt-1">
                    {{ $totalPeminjaman }} Buku
                </p>
            </div>
        </div>

        <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-4 flex items-center gap-3 soft-brutal-hover">
            <div class="w-11 h-11 bg-green-200 border-[2px] border-user-ink flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-user-ink">assignment_turned_in</span>
            </div>

            <div>
                <p class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.12em] text-user-ink/60">
                    Total Dikembalikan
                </p>
                <p class="text-[20px] sm:text-[24px] font-extrabold text-user-ink leading-none mt-1">
                    {{ $totalDikembalikan }} Buku
                </p>
            </div>
        </div>

        <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-4 flex items-center gap-3 soft-brutal-hover">
            <div class="w-11 h-11 bg-red-100 border-[2px] border-user-ink flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-user-ink">warning</span>
            </div>

            <div>
                <p class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.12em] text-user-ink/60">
                    Denda Tertunggak
                </p>
                <p class="text-[18px] sm:text-[22px] font-extrabold text-user-ink leading-none mt-1">
                    Rp {{ number_format($dendaTertunggak ?? 0, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </section>

</div>

@endsection

@push('scripts')
<script>
    const riwayatFilterForm = document.getElementById('riwayatFilterForm');
    const riwayatSearchInput = document.getElementById('riwayatSearchInput');
    const riwayatStatusSelect = document.getElementById('riwayatStatusSelect');

    let riwayatSearchTimer = null;

    if (riwayatSearchInput && riwayatFilterForm) {
        riwayatSearchInput.addEventListener('input', function () {
            clearTimeout(riwayatSearchTimer);

            riwayatSearchTimer = setTimeout(function () {
                riwayatFilterForm.submit();
            }, 500);
        });
    }

    if (riwayatStatusSelect && riwayatFilterForm) {
        riwayatStatusSelect.addEventListener('change', function () {
            riwayatFilterForm.submit();
        });
    }
</script>
@endpush
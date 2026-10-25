@extends('layouts.user')

@section('title', 'Bukti Peminjaman - Perpustakaan')

@section('content')

@php
    $tanggalPinjam = $peminjaman->tanggal_peminjaman ?? $peminjaman->tanggal_pinjam ?? null;

    $batasPengembalian = $peminjaman->batas_pengembalian ?? null;

    if (!$batasPengembalian && $tanggalPinjam) {
        $batasPengembalian = \Carbon\Carbon::parse($tanggalPinjam)->addDays(7);
    }

    $qrUrl = $qrPayload;
@endphp

<div class="w-full max-w-4xl mx-auto space-y-6">

    {{-- Breadcrumb --}}
    <div class="no-print flex items-center gap-2 text-[11px] sm:text-[12px] font-extrabold uppercase tracking-[0.12em] text-user-ink/70">
        <a href="{{ route('user.dashboard') }}" class="hover:text-user-gold transition">
            Dashboard
        </a>

        <span class="material-symbols-outlined text-[16px]">chevron_right</span>

        <a href="{{ route('user.peminjaman.cek') }}" class="hover:text-user-gold transition">
            Status Peminjaman
        </a>

        <span class="material-symbols-outlined text-[16px]">chevron_right</span>

        <span class="text-user-ink">
            Bukti
        </span>
    </div>

    {{-- Header --}}
    <section class="no-print relative overflow-hidden bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm p-4 sm:p-5 lg:p-6">
        <div class="absolute top-0 right-0 w-20 h-20 bg-user-gold border-l-[2px] border-b-[2px] border-user-ink"></div>
        <div class="absolute bottom-0 left-0 w-14 h-14 bg-user-blue-soft border-r-[2px] border-t-[2px] border-user-ink"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 bg-user-gold border-[2px] border-user-ink soft-brutal-shadow-sm px-3 py-1.5">
                    <span class="material-symbols-outlined text-[17px]" style="font-variation-settings: 'FILL' 1;">
                        verified
                    </span>
                    <span class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.16em]">
                        Verified Borrowing
                    </span>
                </div>

                <h1 class="font-display-lg text-[24px] sm:text-[32px] lg:text-[38px] leading-tight uppercase text-user-ink">
                    Bukti Peminjaman
                </h1>

                <p class="text-[12px] sm:text-[14px] text-user-ink/70 font-semibold max-w-2xl">
                    Tunjukkan bukti ini kepada petugas perpustakaan untuk mengambil buku yang sudah diverifikasi admin.
                </p>
            </div>

            <button onclick="window.print()"
                    class="inline-flex items-center justify-center gap-2 bg-user-navy text-white border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover px-4 py-2.5 text-[11px] sm:text-[12px] font-extrabold uppercase tracking-wide">
                <span class="material-symbols-outlined text-[18px]">print</span>
                Cetak / PDF
            </button>
        </div>
    </section>

    {{-- Bukti Print Area --}}
    <section id="printArea" class="print-area bg-user-paper border-[2px] border-user-ink soft-brutal-shadow overflow-hidden">

        {{-- Kop Bukti --}}
        <div class="relative overflow-hidden bg-user-navy text-white border-b-[2px] border-user-ink px-5 sm:px-6 py-5">
            <div class="absolute top-0 right-0 w-24 h-24 bg-user-gold border-l-[2px] border-b-[2px] border-user-ink"></div>

            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[34px] text-user-ink" style="font-variation-settings: 'FILL' 1;">
                            local_library
                        </span>
                    </div>

                    <div>
                        <h2 class="font-headline-md text-[22px] sm:text-[26px] uppercase text-white leading-tight">
                            Perpustakaan
                        </h2>
                        <p class="text-[11px] sm:text-[12px] text-white/80 font-bold uppercase tracking-[0.15em] mt-1">
                            Bukti Peminjaman Buku Terverifikasi
                        </p>
                    </div>
                </div>

                <div class="bg-green-200 text-user-ink border-[2px] border-user-ink soft-brutal-shadow-sm px-4 py-2 text-center">
                    <p class="text-[9px] font-extrabold uppercase tracking-[0.14em] text-user-ink/70">
                        Status
                    </p>
                    <p class="text-[12px] font-extrabold uppercase">
                        Disetujui Admin
                    </p>
                </div>
            </div>
        </div>

        <div class="p-5 sm:p-6">

            {{-- Nomor Peminjaman --}}
            <div class="mb-6 grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-4 items-start border-b-[2px] border-user-ink pb-5">
                <div>
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.14em] text-user-ink/60">
                        Nomor Peminjaman
                    </p>

                    <h3 class="font-display-lg text-[34px] sm:text-[42px] leading-none text-user-ink mt-2">
                        #{{ $peminjaman->peminjaman_id }}
                    </h3>
                </div>

                <div class="bg-user-gold border-[2px] border-user-ink soft-brutal-shadow-sm px-4 py-3">
                    <p class="text-[10px] font-extrabold uppercase tracking-[0.12em] text-user-ink/70">
                        Valid untuk
                    </p>
                    <p class="text-[13px] font-extrabold text-user-ink uppercase mt-1">
                        Pengambilan Buku
                    </p>
                </div>
            </div>

            {{-- QR Verifikasi --}}
            <div class="no-pdf mb-6 bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-5 flex flex-col sm:flex-row items-center justify-between gap-5">
                <div class="text-center sm:text-left">
                    <div class="inline-flex items-center gap-2 bg-user-blue-soft border-[2px] border-user-ink px-3 py-1.5 mb-3">
                        <span class="material-symbols-outlined text-[18px] text-user-ink">
                            qr_code_2
                        </span>

                        <span class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-user-ink">
                            QR Verifikasi
                        </span>
                    </div>

                    <h4 class="font-headline-sm text-[18px] sm:text-[20px] uppercase text-user-ink">
                        Scan Untuk Validasi Bukti
                    </h4>

                    <p class="text-[12px] font-semibold text-user-ink/70 mt-2 max-w-md">
                        Petugas dapat scan QR ini untuk memastikan bahwa peminjaman sudah disetujui admin dan data bukti sesuai dengan sistem.
                    </p>

                    <p class="text-[10px] font-bold text-user-ink/50 mt-3 break-all">
                        {{ $qrUrl }}
                    </p>
                </div>

                <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-3 shrink-0">
                    {!! QrCode::size(135)->margin(1)->generate($qrUrl) !!}<div class="text-xs text-center text-amber-800 mt-2">Berlaku 3 menit, satu kali scan. Muat ulang untuk QR baru.</div>
                </div>
            </div>

            {{-- Data Utama --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Data Peminjam --}}
                <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-9 h-9 bg-user-gold border-[2px] border-user-ink flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px] text-user-ink">
                                person
                            </span>
                        </div>

                        <h4 class="font-headline-sm text-[18px] uppercase text-user-ink">
                            Data Peminjam
                        </h4>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-user-ink/55">
                                Nama
                            </p>
                            <p class="font-extrabold text-user-ink mt-1">
                                {{ $peminjaman->user->nama ?? $peminjaman->user->name ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-user-ink/55">
                                Email
                            </p>
                            <p class="font-bold text-user-ink mt-1 break-all">
                                {{ $peminjaman->user->email ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-user-ink/55">
                                Program Studi
                            </p>
                            <p class="font-bold text-user-ink mt-1">
                                {{ $peminjaman->user->prodi->nama ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Data Buku --}}
                <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-5">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="w-9 h-9 bg-user-blue-soft border-[2px] border-user-ink flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px] text-user-ink">
                                menu_book
                            </span>
                        </div>

                        <h4 class="font-headline-sm text-[18px] uppercase text-user-ink">
                            Data Buku
                        </h4>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-user-ink/55">
                                Judul Buku
                            </p>
                            <p class="font-extrabold text-user-ink mt-1">
                                {{ $peminjaman->buku->judul ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-user-ink/55">
                                Kode Buku
                            </p>
                            <p class="font-bold text-user-ink mt-1">
                                {{ $peminjaman->buku->kode ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-user-ink/55">
                                Lokasi Rak
                            </p>
                            <p class="font-bold text-user-ink mt-1">
                                {{ $peminjaman->buku->lokasi ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Tanggal --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                <div class="bg-user-blue-soft border-[2px] border-user-ink soft-brutal-shadow-sm p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[20px] text-user-ink">
                            event
                        </span>
                        <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-user-ink/60">
                            Tanggal Peminjaman
                        </p>
                    </div>

                    <p class="font-extrabold text-[18px] text-user-ink">
                        {{ $tanggalPinjam ? \Carbon\Carbon::parse($tanggalPinjam)->format('d M Y') : '—' }}
                    </p>
                </div>

                <div class="bg-user-gold border-[2px] border-user-ink soft-brutal-shadow-sm p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="material-symbols-outlined text-[20px] text-user-ink">
                            timer
                        </span>
                        <p class="text-[10px] font-extrabold uppercase tracking-[0.14em] text-user-ink/70">
                            Batas Pengembalian
                        </p>
                    </div>

                    <p class="font-extrabold text-[18px] text-user-ink">
                        {{ $batasPengembalian ? \Carbon\Carbon::parse($batasPengembalian)->format('d M Y') : '—' }}
                    </p>

                    @if($tanggalPinjam)
                        <p class="text-[11px] font-bold text-user-ink/70 mt-1">
                            Dihitung dari tanggal peminjaman + 7 hari.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Catatan --}}
            <div class="mt-5 bg-green-100 border-[2px] border-user-ink soft-brutal-shadow-sm p-4">
                <div class="flex gap-3">
                    <div class="w-10 h-10 bg-green-300 border-[2px] border-user-ink flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-user-ink">
                            verified
                        </span>
                    </div>

                    <p class="text-[12px] sm:text-[13px] font-bold text-user-ink leading-relaxed">
                        <strong class="font-extrabold">Peminjaman sudah diverifikasi admin.</strong>
                        Silakan tunjukkan bukti ini kepada petugas perpustakaan untuk mengambil buku.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="mt-8 border-t-[2px] border-user-ink pt-5 grid grid-cols-1 sm:grid-cols-2 gap-6 items-end">
                <div class="text-[11px] font-bold text-user-ink/60">
                    <p class="uppercase tracking-[0.12em]">
                        Bukti ini dibuat otomatis oleh sistem perpustakaan digital.
                    </p>
                    <p class="mt-2">
                        Harap membawa bukti ini saat pengambilan buku.
                    </p>
                </div>

                <div class="text-center text-sm text-user-ink sm:justify-self-end w-full sm:w-56">
                    <p class="font-extrabold uppercase text-[12px]">
                        Petugas Perpustakaan
                    </p>

                    <div class="h-16"></div>

                    <p class="font-extrabold border-t-[2px] border-user-ink pt-2">
                        Tanda Tangan
                    </p>
                </div>
            </div>

        </div>
    </section>

    {{-- Tombol Kembali --}}
    <div class="no-print flex flex-col sm:flex-row gap-3">
        <a href="{{ route('user.peminjaman.cek') }}"
           class="inline-flex items-center justify-center gap-2 bg-white text-user-ink border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover px-4 py-3 text-[11px] sm:text-[12px] font-extrabold uppercase tracking-wide">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Kembali ke Status
        </a>

        <a href="{{ route('user.peminjaman.cek', ['status' => 'dipinjam']) }}"
           class="inline-flex items-center justify-center gap-2 bg-user-gold text-user-ink border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover px-4 py-3 text-[11px] sm:text-[12px] font-extrabold uppercase tracking-wide">
            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
            Lihat Bukti Lain
        </a>
    </div>

</div>

<style>
@media print {
    body * {
        visibility: hidden !important;
    }

    #printArea,
    #printArea * {
        visibility: visible !important;
    }

    #printArea {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        box-shadow: none !important;
        border: 2px solid #14213D !important;
        background: white !important;
    }

    .no-print,
    .no-pdf,
    aside,
    nav,
    header,
    button {
        display: none !important;
    }

    main {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }

    body {
        background: white !important;
    }
}
</style>

@endsection
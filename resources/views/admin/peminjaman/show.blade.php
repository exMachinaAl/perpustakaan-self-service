@extends('layouts.admin')

@section('title', 'Detail Peminjaman')

@section('content')

@php
    $tanggalPinjam = $peminjaman->tanggal_pinjam;

    $batasKembali = $tanggalPinjam
        ? \Carbon\Carbon::parse($tanggalPinjam)->addDays(7)
        : null;

    $tanggalAkhir = $peminjaman->tanggal_kembali
        ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali)
        : now();

    $isTerlambat = in_array($peminjaman->status, ['dipinjam', 'dikembalikan'])
        && $batasKembali
        && $tanggalAkhir->gt($batasKembali);

    $jumlahHariTerlambat = $isTerlambat
        ? $batasKembali->diffInDays($tanggalAkhir)
        : 0;

    $dendaTotal = (int) ($peminjaman->denda ?? 0);
    $dendaDibayar = (int) ($peminjaman->denda_dibayar ?? 0);
    $sisaDenda = max($dendaTotal - $dendaDibayar, 0);
@endphp

<style>
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

    .info-box {
        border: 2px solid #0f172a;
        background: #ffffff;
    }
</style>

<div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-6 py-5 space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-950 transition">
            Dashboard
        </a>
        <span>/</span>
        <a href="{{ route('admin.peminjaman.index') }}" class="hover:text-slate-950 transition">
            Riwayat Peminjaman
        </a>
        <span>/</span>
        <span class="text-slate-950">Detail</span>
    </div>

    {{-- Header --}}
    <div class="neo-panel rounded-2xl px-5 py-5 bg-gradient-to-br from-slate-50 via-white to-sky-50">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-950 text-white text-xs font-black tracking-wide uppercase neo-badge mb-3">
                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                    Detail Peminjaman
                </div>

                <h2 class="text-2xl md:text-3xl font-black text-slate-950 leading-tight">
                    Detail Transaksi Peminjaman
                </h2>

                <p class="text-sm md:text-base text-slate-600 mt-2 max-w-2xl">
                    Lihat informasi lengkap peminjaman, status pengembalian, dan pembayaran denda.
                </p>
            </div>

            <a href="{{ route('admin.peminjaman.index') }}"
               class="neo-btn inline-flex items-center gap-2 bg-white text-slate-950 px-4 py-2.5 rounded-xl text-sm font-black">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali
            </a>
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

    @if (session('error'))
        <div class="neo-panel rounded-2xl bg-rose-100 px-4 py-3 text-sm font-bold text-slate-950">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">error</span>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Detail Utama --}}
        <div class="lg:col-span-2 neo-panel rounded-2xl bg-white overflow-hidden">
            <div class="px-5 py-4 bg-sky-100 border-b-2 border-slate-950">
                <h3 class="text-lg font-black text-slate-950">
                    Informasi Peminjaman
                </h3>
                <p class="text-xs text-slate-600 font-bold">
                    Data peminjam, buku, tanggal, dan status transaksi.
                </p>
            </div>

            <div class="p-5 space-y-5">

                {{-- Peminjam --}}
                <div>
                    <h4 class="text-sm font-black text-slate-950 uppercase tracking-wide mb-3">
                        Data Peminjam
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="info-box rounded-xl p-4">
                            <p class="text-xs font-black text-slate-500 uppercase">
                                Nama Peminjam
                            </p>
                            <p class="text-base font-black text-slate-950 mt-1">
                                {{ $peminjaman->user->nama ?? $peminjaman->user->name ?? 'User tidak ditemukan' }}
                            </p>
                        </div>

                        <div class="info-box rounded-xl p-4">
                            <p class="text-xs font-black text-slate-500 uppercase">
                                Email / NIM
                            </p>
                            <p class="text-sm font-black text-slate-950 mt-1">
                                {{ $peminjaman->user->email ?? '-' }}
                            </p>
                            <p class="text-sm font-bold text-slate-600 mt-1">
                                NIM: {{ $peminjaman->user->nim ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Buku --}}
                <div>
                    <h4 class="text-sm font-black text-slate-950 uppercase tracking-wide mb-3">
                        Data Buku
                    </h4>

                    <div class="info-box rounded-xl p-4 flex gap-4">
                        <div class="w-20 h-28 border-2 border-slate-950 rounded-xl bg-slate-100 overflow-hidden flex items-center justify-center shrink-0">
                            @if($peminjaman->buku && $peminjaman->buku->cover)
                                <img src="{{ asset('storage/' . $peminjaman->buku->cover) }}"
                                     alt="Cover Buku"
                                     class="w-full h-full object-cover">
                            @else
                                <span class="material-symbols-outlined text-slate-500 text-[34px]">
                                    menu_book
                                </span>
                            @endif
                        </div>

                        <div class="min-w-0">
                            <p class="text-xs font-black text-slate-500 uppercase">
                                Judul Buku
                            </p>
                            <h3 class="text-lg font-black text-slate-950 leading-snug mt-1">
                                {{ $peminjaman->buku->judul ?? 'Buku tidak ditemukan' }}
                            </h3>

                            <p class="text-xs font-black text-slate-500 uppercase mt-4">
                                Pengarang
                            </p>
                            <p class="text-sm font-bold text-slate-700 mt-1">
                                {{ $peminjaman->buku->pengarang ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Tanggal dan Status --}}
                <div>
                    <h4 class="text-sm font-black text-slate-950 uppercase tracking-wide mb-3">
                        Tanggal & Status
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                        <div class="info-box rounded-xl p-4">
                            <p class="text-xs font-black text-slate-500 uppercase">
                                Tanggal Pinjam
                            </p>
                            <p class="text-sm font-black text-slate-950 mt-1">
                                {{ $peminjaman->tanggal_pinjam ? \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') : '-' }}
                            </p>
                        </div>

                        <div class="info-box rounded-xl p-4">
                            <p class="text-xs font-black text-slate-500 uppercase">
                                Batas Kembali
                            </p>
                            <p class="text-sm font-black {{ $isTerlambat && $peminjaman->status == 'dipinjam' ? 'text-rose-700' : 'text-slate-950' }} mt-1">
                                {{ $batasKembali ? $batasKembali->format('d/m/Y') : '-' }}
                            </p>
                        </div>

                        <div class="info-box rounded-xl p-4">
                            <p class="text-xs font-black text-slate-500 uppercase">
                                Tanggal Kembali
                            </p>
                            <p class="text-sm font-black text-slate-950 mt-1">
                                {{ $peminjaman->tanggal_kembali ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d/m/Y') : '-' }}
                            </p>
                        </div>

                        <div class="info-box rounded-xl p-4">
                            <p class="text-xs font-black text-slate-500 uppercase">
                                Status
                            </p>

                            <div class="mt-2">
                                @if($isTerlambat && $peminjaman->status == 'dipinjam')
                                    <span class="inline-flex px-3 py-1 bg-red-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase">
                                        Terlambat {{ $jumlahHariTerlambat }} Hari
                                    </span>
                                @elseif($peminjaman->status == 'dikembalikan')
                                    <span class="inline-flex px-3 py-1 bg-emerald-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase">
                                        Dikembalikan
                                    </span>
                                @elseif($peminjaman->status == 'dipinjam')
                                    <span class="inline-flex px-3 py-1 bg-amber-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase">
                                        Dipinjam
                                    </span>
                                @elseif($peminjaman->status == 'menunggu')
                                    <span class="inline-flex px-3 py-1 bg-sky-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase">
                                        Menunggu
                                    </span>
                                @elseif($peminjaman->status == 'ditolak')
                                    <span class="inline-flex px-3 py-1 bg-rose-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex px-3 py-1 bg-gray-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase">
                                        {{ ucfirst($peminjaman->status) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Panel Denda --}}
        <div class="neo-panel rounded-2xl bg-white overflow-hidden">
            <div class="px-5 py-4 bg-rose-100 border-b-2 border-slate-950">
                <h3 class="text-lg font-black text-slate-950">
                    Informasi Denda
                </h3>
                <p class="text-xs text-slate-600 font-bold">
                    Pembayaran denda peminjaman.
                </p>
            </div>

            <div class="p-5 space-y-4">

                <div class="info-box rounded-xl p-4">
                    <p class="text-xs font-black text-slate-500 uppercase">
                        Total Denda
                    </p>
                    <p class="text-2xl font-black text-slate-950 mt-1">
                        Rp {{ number_format($dendaTotal, 0, ',', '.') }}
                    </p>
                </div>

                <div class="info-box rounded-xl p-4">
                    <p class="text-xs font-black text-slate-500 uppercase">
                        Denda Dibayar
                    </p>
                    <p class="text-2xl font-black text-emerald-700 mt-1">
                        Rp {{ number_format($dendaDibayar, 0, ',', '.') }}
                    </p>
                </div>

                <div class="info-box rounded-xl p-4">
                    <p class="text-xs font-black text-slate-500 uppercase">
                        Sisa Denda
                    </p>
                    <p class="text-2xl font-black {{ $sisaDenda > 0 ? 'text-rose-700' : 'text-emerald-700' }} mt-1">
                        Rp {{ number_format($sisaDenda, 0, ',', '.') }}
                    </p>
                </div>

                <div class="info-box rounded-xl p-4">
                    <p class="text-xs font-black text-slate-500 uppercase">
                        Status Denda
                    </p>

                    <div class="mt-2">
                        @if($dendaTotal <= 0)
                            <span class="inline-flex px-3 py-1 bg-slate-100 text-slate-500 border-2 border-slate-300 rounded-full text-[10px] font-black uppercase">
                                Tidak Ada Denda
                            </span>
                        @elseif($peminjaman->status_denda === 'lunas')
                            <span class="inline-flex px-3 py-1 bg-emerald-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase">
                                Lunas
                            </span>
                        @else
                            <span class="inline-flex px-3 py-1 bg-rose-100 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase">
                                Belum Lunas
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Form Bayar Denda --}}
                @if($dendaTotal > 0 && $sisaDenda > 0 && $peminjaman->status_denda !== 'lunas')
                    <form action="{{ route('admin.peminjaman.bayar-denda', $peminjaman->peminjaman_id) }}"
                          method="POST"
                          class="info-box rounded-xl p-4 space-y-3">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase mb-2">
                                Input Pembayaran
                            </label>

                            <input type="number"
                                   name="jumlah_bayar"
                                   min="1"
                                   max="{{ $sisaDenda }}"
                                   placeholder="Masukkan nominal bayar"
                                   class="w-full h-11 rounded-xl border-2 border-slate-950 bg-white px-3 text-sm font-bold text-slate-950 focus:ring-2 focus:ring-sky-300"
                                   required>
                        </div>

                        <button type="submit"
                                onclick="return confirm('Simpan pembayaran denda ini?')"
                                class="neo-btn w-full h-11 rounded-xl bg-emerald-200 text-slate-950 text-sm font-black inline-flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">payments</span>
                            Simpan Pembayaran
                        </button>
                    </form>
                @elseif($dendaTotal > 0 && $sisaDenda <= 0)
                    <div class="info-box rounded-xl p-4 bg-emerald-50">
                        <p class="text-sm font-black text-emerald-700">
                            Denda sudah lunas.
                        </p>
                    </div>
                @else
                    <div class="info-box rounded-xl p-4 bg-slate-50">
                        <p class="text-sm font-black text-slate-500">
                            Tidak ada denda untuk peminjaman ini.
                        </p>
                    </div>
                @endif

            </div>
        </div>

    </div>

</div>

@endsection
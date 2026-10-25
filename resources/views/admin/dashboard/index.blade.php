@extends('layouts.admin')

@section('title', 'Dashboard Admin | Perpustakaan')

@section('content')

@php
    $adminName = auth()->check() ? (auth()->user()->nama ?? auth()->user()->name ?? 'Admin') : 'Admin';

    function brutalActivityColor($type) {
        return match($type) {
            'pinjam' => 'bg-amber-200 text-slate-950',
            'kembali' => 'bg-emerald-200 text-slate-950',
            'register' => 'bg-sky-200 text-slate-950',
            'terlambat' => 'bg-rose-200 text-slate-950',
            default => 'bg-slate-200 text-slate-950',
        };
    }

    function brutalActivityIcon($type) {
        return match($type) {
            'pinjam' => 'menu_book',
            'kembali' => 'assignment_return',
            'register' => 'person_add',
            'terlambat' => 'warning',
            default => 'info',
        };
    }
@endphp

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
        vertical-align: middle;
    }

    .avatar-fixed {
        width: 42px;
        height: 42px;
        min-width: 42px;
        min-height: 42px;
        flex: 0 0 42px;
        aspect-ratio: 1 / 1;
        border-radius: 9999px;
        overflow: hidden;
    }

    .activity-icon-fixed {
        width: 44px;
        height: 44px;
        min-width: 44px;
        min-height: 44px;
        flex: 0 0 44px;
        aspect-ratio: 1 / 1;
    }
</style>

<div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-5 py-4 space-y-5">

    {{-- HEADER --}}
    <section class="neo-panel rounded-2xl px-5 py-5 bg-gradient-to-br from-slate-50 via-white to-sky-50">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="min-w-0">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-950 text-white text-xs font-black tracking-wide uppercase neo-badge mb-3">
                    <span class="material-symbols-outlined text-[16px]">dashboard</span>
                    Admin Panel
                </div>

                <h2 class="text-2xl md:text-3xl font-black text-slate-950 leading-tight">
                    Dashboard Admin
                </h2>

                <p class="text-sm md:text-base text-slate-600 mt-2 max-w-2xl">
                    Pantau koleksi buku, anggota, peminjaman, dan aktivitas user secara cepat.
                </p>
            </div>

            <div class="flex flex-wrap justify-start lg:justify-end gap-3 lg:ml-auto lg:pl-10 xl:pr-2">
                <a href="{{ route('admin.peminjaman.index') }}"
                   class="neo-btn inline-flex items-center gap-2 bg-slate-950 text-white px-4 py-2.5 rounded-xl text-sm font-black">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Input Peminjaman
                </a>

                <a href="{{ route('admin.databuku.index') }}"
                   class="neo-btn inline-flex items-center gap-2 bg-sky-200 text-slate-950 px-4 py-2.5 rounded-xl text-sm font-black">
                    <span class="material-symbols-outlined text-[18px]">menu_book</span>
                    Katalog Buku
                </a>
            </div>
        </div>
    </section>

    {{-- STATISTIK --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <a href="{{ route('admin.databuku.index') }}" class="group neo-card bg-white rounded-2xl p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="w-12 h-12 rounded-xl bg-sky-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">library_books</span>
                </div>

                <span class="neo-badge text-[11px] font-black bg-sky-50 text-slate-950 px-2.5 py-1 rounded-full">
                    DATA
                </span>
            </div>

            <div class="mt-4">
                <h3 class="text-3xl font-black text-slate-950">
                    {{ $totalBuku ?? 0 }}
                </h3>
                <p class="text-sm font-bold text-slate-600 mt-1">
                    Total Buku
                </p>
            </div>
        </a>

        <a href="{{ route('admin.anggota.index') }}" class="group neo-card bg-white rounded-2xl p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="w-12 h-12 rounded-xl bg-amber-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">person_check</span>
                </div>

                <span class="neo-badge text-[11px] font-black bg-amber-50 text-slate-950 px-2.5 py-1 rounded-full">
                    AKTIF
                </span>
            </div>

            <div class="mt-4">
                <h3 class="text-3xl font-black text-slate-950">
                    {{ $anggotaAktif ?? 0 }}
                </h3>
                <p class="text-sm font-bold text-slate-600 mt-1">
                    Anggota Aktif
                </p>
            </div>
        </a>

        <a href="{{ route('admin.peminjaman.index') }}" class="group neo-card bg-white rounded-2xl p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="w-12 h-12 rounded-xl bg-rose-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">sync_alt</span>
                </div>

                <span class="neo-badge text-[11px] font-black bg-rose-50 text-slate-950 px-2.5 py-1 rounded-full">
                    DIPINJAM
                </span>
            </div>

            <div class="mt-4">
                <h3 class="text-3xl font-black text-slate-950">
                    {{ $sedangDipinjam ?? 0 }}
                </h3>
                <p class="text-sm font-bold text-slate-600 mt-1">
                    Buku Dipinjam
                </p>
            </div>
        </a>

        <a href="{{ route('admin.databuku.index') }}" class="group neo-card bg-white rounded-2xl p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">check_circle</span>
                </div>

                <span class="neo-badge text-[11px] font-black bg-emerald-50 text-slate-950 px-2.5 py-1 rounded-full">
                    TERSEDIA
                </span>
            </div>

            <div class="mt-4">
                <h3 class="text-3xl font-black text-slate-950">
                    {{ $bukuTersedia ?? 0 }}
                </h3>
                <p class="text-sm font-bold text-slate-600 mt-1">
                    Buku Tersedia
                </p>
            </div>
        </a>

    </section>

    {{-- TABEL + AKTIVITAS --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        {{-- PEMINJAMAN TERBARU --}}
        <div class="xl:col-span-2 neo-panel bg-white rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b-2 border-slate-950 flex items-center justify-between bg-sky-100">
                <div>
                    <h3 class="text-lg font-black text-slate-950">
                        Peminjaman Terbaru
                    </h3>
                    <p class="text-xs text-slate-600 font-bold">
                        Daftar transaksi terbaru pengguna
                    </p>
                </div>

                <a href="{{ route('admin.peminjaman.index') }}"
                   class="neo-btn inline-flex items-center gap-1 bg-white text-slate-950 px-3 py-1.5 rounded-xl text-xs font-black">
                    Lihat Semua
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-sm neo-table">
                    <thead class="bg-slate-950 text-white uppercase text-[11px] tracking-wide">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama Peminjam</th>
                            <th class="px-4 py-3 text-left">Judul Buku</th>
                            <th class="px-4 py-3 text-center">Tgl Pinjam</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse(($peminjamanTerbaru ?? []) as $pinjam)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3 min-w-[190px]">

                                        {{-- Foto Profile User --}}
                                        <div class="avatar-fixed bg-slate-100 text-slate-950 border-2 border-slate-950 flex items-center justify-center font-black text-xs leading-none shrink-0">
                                            @if(!empty($pinjam->user->foto_profile))
                                                <img src="{{ asset('storage/' . $pinjam->user->foto_profile) }}"
                                                     alt="Foto Profile {{ $pinjam->user->nama ?? $pinjam->user->name ?? 'User' }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <span>
                                                    {{ strtoupper(substr($pinjam->user->nama ?? $pinjam->user->name ?? 'U', 0, 1)) }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <p class="font-black text-slate-950 leading-snug break-words line-clamp-1">
                                                {{ $pinjam->user->nama ?? $pinjam->user->name ?? '-' }}
                                            </p>
                                            <p class="text-xs font-bold text-slate-500 break-words line-clamp-1 mt-1">
                                                {{ $pinjam->user->email ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-slate-700 font-bold">
                                    <span class="line-clamp-2">
                                        {{ $pinjam->buku->judul ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-center text-slate-700 font-bold whitespace-nowrap">
                                    {{ isset($pinjam->tanggal_pinjam) ? \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d M Y') : '-' }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @php
                                        $statusColor = match($pinjam->status ?? '') {
                                            'dipinjam' => 'bg-amber-200',
                                            'dikembalikan' => 'bg-emerald-200',
                                            'menunggu' => 'bg-sky-200',
                                            'ditolak' => 'bg-rose-200',
                                            'dibatalkan' => 'bg-slate-200',
                                            default => 'bg-slate-200',
                                        };
                                    @endphp

                                    <span class="inline-block {{ $statusColor }} border-2 border-slate-950 text-slate-950 text-[10px] font-black uppercase px-2.5 py-1 rounded-full whitespace-nowrap">
                                        {{ $pinjam->status ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.peminjaman.show', $pinjam->peminjaman_id) }}"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded-xl border-2 border-slate-950 bg-white hover:bg-slate-100 transition"
                                       title="Lihat Detail">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-500 font-bold">
                                    Belum ada data peminjaman terbaru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- AKTIVITAS USER --}}
        <div class="neo-panel bg-white rounded-2xl overflow-hidden flex flex-col">
            <div class="px-5 py-4 border-b-2 border-slate-950 bg-amber-100">
                <h3 class="text-lg font-black text-slate-950">
                    Aktivitas User
                </h3>
                <p class="text-xs text-slate-600 font-bold">
                    Aktivitas terbaru dari pengguna sistem
                </p>
            </div>

            <div class="p-4 space-y-4 max-h-[470px] overflow-y-auto">
                @forelse(($aktivitasUser ?? []) as $item)
                    <div class="flex gap-3 min-w-0">
                        <div class="activity-icon-fixed rounded-xl border-2 border-slate-950 flex items-center justify-center shrink-0 {{ brutalActivityColor($item['type'] ?? 'default') }}">
                            <span class="material-symbols-outlined text-[20px]">
                                {{ brutalActivityIcon($item['type'] ?? 'default') }}
                            </span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="bg-slate-50 border-2 border-slate-950 rounded-xl px-3 py-2">
                                <p class="text-sm text-slate-950 leading-relaxed break-words">
                                    <span class="font-black">
                                        {{ $item['nama'] ?? 'User' }}
                                    </span>
                                    {{ $item['aktivitas'] ?? '-' }}
                                </p>

                                @if(!empty($item['detail']))
                                    <p class="text-xs text-slate-600 mt-1 font-bold break-words">
                                        {{ $item['detail'] }}
                                    </p>
                                @endif
                            </div>

                            <p class="text-[11px] text-slate-500 mt-1 font-black">
                                {{ $item['waktu'] ?? '-' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="border-2 border-dashed border-slate-950 rounded-2xl p-6 text-center bg-slate-50">
                        <span class="material-symbols-outlined text-[30px] text-slate-950">history</span>
                        <p class="mt-2 text-sm font-black text-slate-950">
                            Belum ada aktivitas user.
                        </p>
                        <p class="text-xs text-slate-600 mt-1 font-semibold">
                            Aktivitas akan muncul ketika user mendaftar, meminjam, atau mengembalikan buku.
                        </p>
                    </div>
                @endforelse
            </div>

            <div class="p-4 border-t-2 border-slate-950 bg-slate-50">
                <a href="{{ route('admin.anggota.index') }}"
                   class="block w-full text-center neo-btn bg-white text-slate-950 py-2.5 rounded-xl text-sm font-black">
                    Lihat Data User
                </a>
            </div>
        </div>

    </section>
</div>

@endsection
@extends('layouts.admin')

@section('title', 'Riwayat Peminjaman')

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
        vertical-align: middle;
    }

    .book-cover-fixed {
        width: 42px;
        height: 56px;
        min-width: 42px;
        min-height: 56px;
        flex: 0 0 42px;
        overflow: hidden;
    }
</style>

<div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-5 space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-950 transition">
            Dashboard
        </a>
        <span>/</span>
        <span class="text-slate-950">Riwayat Peminjaman</span>
    </div>

    {{-- Header --}}
    <div class="neo-panel rounded-2xl px-5 py-5 bg-gradient-to-br from-slate-50 via-white to-sky-50">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="min-w-0">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-950 text-white text-xs font-black tracking-wide uppercase neo-badge mb-3">
                    <span class="material-symbols-outlined text-[16px]">assignment</span>
                    Manajemen Peminjaman
                </div>

                <h2 class="text-2xl md:text-3xl font-black text-slate-950 leading-tight">
                    Riwayat Peminjaman
                </h2>

                <p class="text-sm md:text-base text-slate-600 mt-2 max-w-2xl">
                    Kelola pengajuan, status peminjaman, pengembalian buku, pembayaran denda, dan riwayat transaksi anggota.
                </p>
            </div>

            <div class="flex justify-start lg:justify-end lg:ml-auto lg:pl-10">
                <a href="{{ route('admin.peminjaman.index', array_merge(request()->query(), ['export' => 'pdf'])) }}"
                   class="neo-btn inline-flex items-center gap-2 bg-slate-950 text-white px-4 py-2.5 rounded-xl text-sm font-black">
                    <span class="material-symbols-outlined text-[18px]">download</span>
                    Export
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

    @if (session('error'))
        <div class="neo-panel rounded-2xl bg-rose-100 px-4 py-3 text-sm font-bold text-slate-950">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">error</span>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <div class="neo-card bg-white rounded-2xl px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-sky-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">auto_stories</span>
                </div>

                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-wide">
                        Total Pinjam
                    </p>
                    <p class="text-2xl font-black text-slate-950">
                        {{ $totalPinjam ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="neo-card bg-white rounded-2xl px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-amber-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">schedule</span>
                </div>

                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-wide">
                        Dipinjam
                    </p>
                    <p class="text-2xl font-black text-slate-950">
                        {{ $sedangDipinjam ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="neo-card bg-white rounded-2xl px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">assignment_return</span>
                </div>

                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-wide">
                        Dikembalikan
                    </p>
                    <p class="text-2xl font-black text-slate-950">
                        {{ $dikembalikan ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="neo-card bg-white rounded-2xl px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-rose-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">cancel</span>
                </div>

                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-wide">
                        Ditolak
                    </p>
                    <p class="text-2xl font-black text-slate-950">
                        {{ $ditolak ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- Table Panel --}}
    <div class="neo-panel bg-white rounded-2xl overflow-hidden">

        {{-- Filter --}}
        <div class="px-5 py-4 border-b-2 border-slate-950 bg-sky-100">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-lg font-black text-slate-950">
                        Data Peminjaman
                    </h3>
                    <p class="text-xs text-slate-600 font-bold">
                        Cari dan kelola transaksi peminjaman buku anggota.
                    </p>
                </div>

                <span class="neo-badge inline-flex items-center w-fit gap-2 bg-white text-slate-950 px-3 py-1 rounded-full text-xs font-black">
                    <span class="material-symbols-outlined text-[16px]">database</span>
                    {{ $peminjaman->total() ?? 0 }} Data
                </span>
            </div>

            <form id="filterPeminjamanForm"
                  action="{{ route('admin.peminjaman.index') }}"
                  method="GET"
                  class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-3">

                <div class="relative xl:col-span-5">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-[19px]">
                        search
                    </span>

                    <input name="search"
                           id="searchPeminjamanInput"
                           value="{{ request('search') }}"
                           autocomplete="off"
                           class="w-full h-11 rounded-xl border-2 border-slate-950 bg-white pl-10 pr-4 text-sm font-semibold text-slate-950 placeholder:text-slate-400 focus:ring-2 focus:ring-sky-300 focus:border-slate-950"
                           placeholder="Cari buku, kode, pengarang, anggota..."
                           type="text">
                </div>

                <select name="status"
                        id="statusPeminjamanSelect"
                        class="h-11 rounded-xl border-2 border-slate-950 bg-white px-3 text-sm font-semibold text-slate-950 xl:col-span-3 focus:ring-2 focus:ring-sky-300 focus:border-slate-950">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>

                <select name="denda"
                        id="dendaPeminjamanSelect"
                        class="h-11 rounded-xl border-2 border-slate-950 bg-white px-3 text-sm font-semibold text-slate-950 xl:col-span-2 focus:ring-2 focus:ring-sky-300 focus:border-slate-950">
                    <option value="">Semua Denda</option>
                    <option value="tidak_ada" {{ request('denda') == 'tidak_ada' ? 'selected' : '' }}>Tidak Ada</option>
                    <option value="belum_lunas" {{ request('denda') == 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    <option value="lunas" {{ request('denda') == 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>

                <a href="{{ route('admin.peminjaman.index') }}"
                   class="neo-btn h-11 px-4 rounded-xl bg-white text-slate-950 text-sm font-black inline-flex items-center justify-center xl:col-span-2">
                    Reset
                </a>

            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1180px] text-sm neo-table">
                <thead class="bg-slate-950 text-white">
                    <tr class="uppercase text-[11px] tracking-wide">
                        <th class="px-4 py-3 text-left font-black w-[60px]">No</th>
                        <th class="px-4 py-3 text-left font-black min-w-[300px]">Buku</th>
                        <th class="px-4 py-3 text-left font-black min-w-[210px]">Peminjam</th>
                        <th class="px-4 py-3 text-center font-black min-w-[115px]">Tgl Pinjam</th>
                        <th class="px-4 py-3 text-center font-black min-w-[115px]">Tgl Kembali</th>
                        <th class="px-4 py-3 text-center font-black min-w-[145px]">Status</th>
                        <th class="px-4 py-3 text-left font-black min-w-[170px]">Denda</th>
                        <th class="px-4 py-3 text-center font-black min-w-[100px]">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($peminjaman as $item)
                        @php
                            $tanggalPinjam = $item->tanggal_pinjam;

                            $batasKembali = $tanggalPinjam
                                ? \Carbon\Carbon::parse($tanggalPinjam)->addDays(7)
                                : null;

                            $isTerlambat = $item->status == 'dipinjam'
                                && $batasKembali
                                && now()->gt($batasKembali);

                            $jumlahHariTerlambat = 0;

                            if ($isTerlambat) {
                                $jumlahHariTerlambat = $batasKembali->diffInDays(now());
                            }

                            $dendaTotal = (int) ($item->denda ?? 0);
                            $dendaDibayar = (int) ($item->denda_dibayar ?? 0);
                            $sisaDenda = max($dendaTotal - $dendaDibayar, 0);
                        @endphp

                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-700 font-black">
                                {{ $peminjaman->firstItem() + $loop->index }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3 min-w-[280px]">
                                    <div class="book-cover-fixed rounded-xl border-2 border-slate-950 bg-slate-100 flex items-center justify-center shrink-0">
                                        @if (!empty($item->buku->cover))
                                            <img src="{{ asset('storage/' . $item->buku->cover) }}"
                                                 alt="Cover Buku"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <span class="material-symbols-outlined text-slate-500 text-[20px]">
                                                menu_book
                                            </span>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <p class="font-black text-slate-950 leading-snug text-sm break-words line-clamp-2">
                                            {{ $item->buku->judul ?? 'Buku tidak ditemukan' }}
                                        </p>
                                        <p class="text-slate-500 text-xs mt-1 font-semibold break-words line-clamp-1">
                                            {{ $item->buku->pengarang ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3">
                                <div class="min-w-[190px]">
                                    <p class="font-black text-slate-950 leading-snug break-words line-clamp-1">
                                        {{ $item->user->nama ?? $item->user->name ?? 'User tidak ditemukan' }}
                                    </p>
                                    <p class="text-slate-500 text-xs mt-1 font-semibold break-words line-clamp-1">
                                        {{ $item->user->email ?? '-' }}
                                    </p>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-center text-slate-700 font-bold whitespace-nowrap">
                                {{ $item->tanggal_pinjam ? \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') : '-' }}
                            </td>

                            <td class="px-4 py-3 text-center text-slate-700 font-bold whitespace-nowrap">
                                {{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') : '-' }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if ($isTerlambat)
                                    <span class="inline-flex px-3 py-1 bg-red-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase whitespace-nowrap">
                                        Terlambat {{ $jumlahHariTerlambat }} Hari
                                    </span>
                                @elseif ($item->status == 'dikembalikan')
                                    <span class="inline-flex px-3 py-1 bg-emerald-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase whitespace-nowrap">
                                        Kembali
                                    </span>
                                @elseif ($item->status == 'ditolak')
                                    <span class="inline-flex px-3 py-1 bg-rose-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase whitespace-nowrap">
                                        Ditolak
                                    </span>
                                @elseif ($item->status == 'dibatalkan')
                                    <span class="inline-flex px-3 py-1 bg-gray-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase whitespace-nowrap">
                                        Dibatalkan
                                    </span>
                                @elseif ($item->status == 'dipinjam')
                                    <span class="inline-flex px-3 py-1 bg-amber-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase whitespace-nowrap">
                                        Dipinjam
                                    </span>
                                @else
                                    <span class="inline-flex px-3 py-1 bg-sky-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase whitespace-nowrap">
                                        Menunggu
                                    </span>
                                @endif
                            </td>

                            {{-- Denda Ringkas --}}
                            <td class="px-4 py-3">
                                @if($dendaTotal > 0)
                                    <div class="min-w-[150px] space-y-1">
                                        <p class="text-xs font-black text-slate-950">
                                            Rp {{ number_format($dendaTotal, 0, ',', '.') }}
                                        </p>

                                        <p class="text-[11px] font-bold {{ $sisaDenda > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                                            Sisa: Rp {{ number_format($sisaDenda, 0, ',', '.') }}
                                        </p>

                                        @if($item->status_denda === 'lunas')
                                            <span class="inline-flex px-2 py-1 bg-emerald-200 text-slate-950 border-2 border-slate-950 rounded-full text-[9px] font-black uppercase">
                                                Lunas
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 bg-rose-100 text-slate-950 border-2 border-slate-950 rounded-full text-[9px] font-black uppercase">
                                                Belum Lunas
                                            </span>
                                        @endif
                                    </div>
                                @elseif($isTerlambat)
                                    <span class="inline-flex px-2.5 py-1 bg-red-100 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase">
                                        Belum Dihitung
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 bg-slate-100 text-slate-500 border-2 border-slate-300 rounded-full text-[10px] font-black uppercase">
                                        Tidak Ada
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex justify-center items-center gap-2 min-w-[90px]">

                                    @if ($item->status == 'dipinjam')
                                        <form action="{{ route('admin.peminjaman.kembalikan', $item->peminjaman_id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <button type="submit"
                                                    onclick="return confirm('Tandai buku ini sudah dikembalikan dan hitung denda jika terlambat?')"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-xl border-2 border-slate-950 {{ $isTerlambat ? 'bg-red-100 hover:bg-red-200' : 'bg-emerald-100 hover:bg-emerald-200' }} text-slate-950 transition"
                                                    title="{{ $isTerlambat ? 'Kembalikan & Hitung Denda' : 'Kembalikan' }}">
                                                <span class="material-symbols-outlined text-[18px]">assignment_return</span>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.peminjaman.show', $item->peminjaman_id) }}"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded-xl border-2 border-slate-950 bg-sky-100 text-slate-950 hover:bg-sky-200 transition"
                                       title="Lihat Detail">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center">
                                <div class="border-2 border-dashed border-slate-950 rounded-2xl p-6 bg-slate-50 max-w-md mx-auto">
                                    <span class="material-symbols-outlined text-[38px] text-slate-950">
                                        event_busy
                                    </span>
                                    <p class="mt-2 text-sm font-black text-slate-950">
                                        Data peminjaman belum tersedia.
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1 font-semibold">
                                        Data akan muncul setelah anggota mengajukan peminjaman buku.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($peminjaman->total() > 0)
            <div class="px-5 py-4 border-t-2 border-slate-950 bg-slate-50 flex flex-col md:flex-row justify-between items-center gap-3">
                <p class="text-xs text-slate-600 font-bold">
                    Showing {{ $peminjaman->firstItem() }} to {{ $peminjaman->lastItem() }} of {{ $peminjaman->total() }} entries
                </p>

                @if ($peminjaman->lastPage() > 1)
                    <div class="flex items-center gap-1">
                        @if ($peminjaman->onFirstPage())
                            <span class="w-9 h-9 flex items-center justify-center rounded-xl border-2 border-slate-300 text-slate-300 cursor-not-allowed">
                                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                            </span>
                        @else
                            <a href="{{ $peminjaman->previousPageUrl() }}"
                               class="w-9 h-9 flex items-center justify-center rounded-xl border-2 border-slate-950 text-slate-950 bg-white hover:bg-slate-100 transition">
                                <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                            </a>
                        @endif

                        @for ($i = 1; $i <= $peminjaman->lastPage(); $i++)
                            @if ($i == $peminjaman->currentPage())
                                <span class="w-9 h-9 flex items-center justify-center rounded-xl border-2 border-slate-950 bg-slate-950 text-white text-xs font-black">
                                    {{ $i }}
                                </span>
                            @else
                                <a href="{{ $peminjaman->url($i) }}"
                                   class="w-9 h-9 flex items-center justify-center rounded-xl border-2 border-slate-950 bg-white text-slate-950 text-xs font-black hover:bg-slate-100 transition">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor

                        @if ($peminjaman->hasMorePages())
                            <a href="{{ $peminjaman->nextPageUrl() }}"
                               class="w-9 h-9 flex items-center justify-center rounded-xl border-2 border-slate-950 text-slate-950 bg-white hover:bg-slate-100 transition">
                                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                            </a>
                        @else
                            <span class="w-9 h-9 flex items-center justify-center rounded-xl border-2 border-slate-300 text-slate-300 cursor-not-allowed">
                                <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        @endif

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filterPeminjamanForm');
        const searchInput = document.getElementById('searchPeminjamanInput');
        const statusSelect = document.getElementById('statusPeminjamanSelect');
        const dendaSelect = document.getElementById('dendaPeminjamanSelect');

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

        if (statusSelect) {
            statusSelect.addEventListener('change', submitFilter);
        }

        if (dendaSelect) {
            dendaSelect.addEventListener('change', submitFilter);
        }
    });
</script>

@endsection
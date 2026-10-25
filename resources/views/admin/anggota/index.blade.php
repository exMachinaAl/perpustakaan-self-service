@extends('layouts.admin')

@section('title', 'Data Anggota')

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

    .avatar-fixed {
        width: 42px;
        height: 42px;
        min-width: 42px;
        min-height: 42px;
        flex: 0 0 42px;
        aspect-ratio: 1 / 1;
        overflow: hidden;
    }
</style>

<div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-5 py-4 space-y-5">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-950 transition">
            Dashboard
        </a>
        <span>/</span>
        <span class="text-slate-950">Data Anggota</span>
    </div>

    {{-- Header --}}
    <div class="neo-panel rounded-2xl px-5 py-5 bg-gradient-to-br from-slate-50 via-white to-sky-50">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            <div class="min-w-0">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-950 text-white text-xs font-black tracking-wide uppercase neo-badge mb-3">
                    <span class="material-symbols-outlined text-[16px]">group</span>
                    Manajemen Anggota
                </div>

                <h2 class="text-2xl md:text-3xl font-black text-slate-950 leading-tight">
                    Data Anggota
                </h2>

                <p class="text-sm md:text-base text-slate-600 mt-2 max-w-2xl">
                    Kelola akun anggota, status akses, program studi, dan riwayat peminjaman pengguna.
                </p>
            </div>

            <div class="flex flex-wrap justify-start lg:justify-end gap-3 lg:ml-auto lg:pl-10">
                <a href="{{ route('admin.anggota.create') }}"
                   class="neo-btn inline-flex items-center justify-center gap-2 bg-slate-950 text-white px-4 py-2.5 rounded-xl text-sm font-black">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    Tambah Anggota
                </a>

                <a href="{{ route('admin.anggota.export') }}"
                   class="neo-btn inline-flex items-center justify-center gap-2 bg-white text-slate-950 px-4 py-2.5 rounded-xl text-sm font-black">
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
                    <span class="material-symbols-outlined text-[24px]">group</span>
                </div>

                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-wide">
                        Total Anggota
                    </p>
                    <p class="text-2xl font-black text-slate-950">
                        {{ $totalAnggota ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="neo-card bg-white rounded-2xl px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">person_check</span>
                </div>

                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-wide">
                        Aktif
                    </p>
                    <p class="text-2xl font-black text-slate-950">
                        {{ $anggotaAktif ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="neo-card bg-white rounded-2xl px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-amber-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">new_releases</span>
                </div>

                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-wide">
                        Anggota Baru
                    </p>
                    <p class="text-2xl font-black text-slate-950">
                        {{ $anggotaBaru ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="neo-card bg-white rounded-2xl px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-rose-200 border-2 border-slate-950 flex items-center justify-center text-slate-950">
                    <span class="material-symbols-outlined text-[24px]">person_off</span>
                </div>

                <div>
                    <p class="text-xs font-black text-slate-500 uppercase tracking-wide">
                        Diblokir
                    </p>
                    <p class="text-2xl font-black text-slate-950">
                        {{ $anggotaDiblokir ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- Table Panel --}}
    <div class="neo-panel bg-white rounded-2xl overflow-hidden">

        {{-- Filter --}}
        <div class="px-5 py-4 border-b-2 border-slate-950 bg-sky-100">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-3">
                <div>
                    <h3 class="text-lg font-black text-slate-950">
                        Daftar Anggota
                    </h3>
                    <p class="text-xs text-slate-600 font-bold">
                        Cari dan kelola data anggota perpustakaan.
                    </p>
                </div>

                <span class="neo-badge inline-flex items-center w-fit gap-2 bg-white text-slate-950 px-3 py-1 rounded-full text-xs font-black">
                    <span class="material-symbols-outlined text-[16px]">database</span>
                    {{ $anggota->total() ?? 0 }} Data
                </span>
            </div>

            <form id="filterAnggotaForm"
                  action="{{ route('admin.anggota.index') }}"
                  method="GET"
                  class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-12 gap-3">

                <div class="relative xl:col-span-5">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-[19px]">
                        search
                    </span>

                    <input name="search"
                           id="searchAnggotaInput"
                           value="{{ request('search') }}"
                           autocomplete="off"
                           class="w-full h-11 rounded-xl border-2 border-slate-950 bg-white pl-10 pr-4 text-sm font-semibold text-slate-950 placeholder:text-slate-400 focus:ring-2 focus:ring-sky-300 focus:border-slate-950"
                           placeholder="Cari nama, NIM, atau email..."
                           type="text">
                </div>

                <select name="prodi_id"
                        id="prodiAnggotaSelect"
                        class="h-11 rounded-xl border-2 border-slate-950 bg-white px-3 text-sm font-semibold text-slate-950 xl:col-span-3 focus:ring-2 focus:ring-sky-300 focus:border-slate-950">
                    <option value="">Semua Prodi</option>
                    @foreach ($prodiList as $prodi)
                        <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->nama ?? $prodi->nama_prodi ?? '-' }}
                        </option>
                    @endforeach
                </select>

                <select name="status"
                        id="statusAnggotaSelect"
                        class="h-11 rounded-xl border-2 border-slate-950 bg-white px-3 text-sm font-semibold text-slate-950 xl:col-span-2 focus:ring-2 focus:ring-sky-300 focus:border-slate-950">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>
                        Aktif
                    </option>
                    <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>
                        Tidak Aktif
                    </option>
                    <option value="diblokir" {{ request('status') == 'diblokir' ? 'selected' : '' }}>
                        Diblokir
                    </option>
                </select>

                <a href="{{ route('admin.anggota.index') }}"
                   class="neo-btn h-11 px-4 rounded-xl bg-white text-slate-950 text-sm font-black inline-flex items-center justify-center xl:col-span-2">
                    Reset
                </a>

            </form>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-sm neo-table">
                <thead class="bg-slate-950 text-white">
                    <tr class="uppercase text-[11px] tracking-wide">
                        <th class="px-4 py-3 text-left font-black">No</th>
                        <th class="px-4 py-3 text-left font-black">Anggota</th>
                        <th class="px-4 py-3 text-left font-black">NIM</th>
                        <th class="px-4 py-3 text-left font-black">Prodi</th>
                        <th class="px-4 py-3 text-center font-black">Pinjam</th>
                        <th class="px-4 py-3 text-center font-black">Status</th>
                        <th class="px-4 py-3 text-center font-black">Daftar</th>
                        <th class="px-4 py-3 text-center font-black">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($anggota as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3 text-slate-700 font-bold">
                                {{ $anggota->firstItem() + $loop->index }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3 min-w-[260px]">

                                    {{-- Foto Profile Anggota --}}
                                    <div class="avatar-fixed rounded-xl bg-slate-100 text-slate-950 border-2 border-slate-950 flex items-center justify-center text-xs font-black leading-none shrink-0">
                                        @if(!empty($item->foto_profile))
                                            <img src="{{ asset('storage/' . $item->foto_profile) }}"
                                                 alt="Foto Profile {{ $item->nama ?? $item->name ?? 'Anggota' }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <span>
                                                {{ strtoupper(substr($item->nama ?? $item->name ?? 'U', 0, 1)) }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <p class="font-black text-slate-950 leading-snug text-sm break-words">
                                            {{ $item->nama ?? $item->name ?? 'Nama tidak tersedia' }}
                                        </p>
                                        <p class="text-slate-500 text-xs mt-1 font-semibold break-words">
                                            {{ $item->email ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-slate-700 font-bold whitespace-nowrap">
                                {{ $item->nim ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-slate-700 font-bold">
                                {{ $item->prodi->nama ?? $item->prodi->nama_prodi ?? '-' }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center justify-center min-w-9 px-2.5 py-1 rounded-full bg-sky-100 text-slate-950 border-2 border-slate-950 text-xs font-black">
                                    {{ $item->peminjaman_count ?? 0 }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-center">
                                @if ($item->status == 'aktif')
                                    <span class="inline-flex px-2.5 py-1 bg-emerald-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase whitespace-nowrap">
                                        Aktif
                                    </span>
                                @elseif ($item->status == 'tidak_aktif')
                                    <span class="inline-flex px-2.5 py-1 bg-slate-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase whitespace-nowrap">
                                        Nonaktif
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 bg-rose-200 text-slate-950 border-2 border-slate-950 rounded-full text-[10px] font-black uppercase whitespace-nowrap">
                                        Blokir
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center text-slate-700 font-bold whitespace-nowrap">
                                {{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex justify-center items-center gap-2">

                                    <a href="{{ route('admin.anggota.show', $item->id) }}"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded-xl border-2 border-slate-950 bg-sky-100 text-slate-950 hover:bg-sky-200 transition"
                                       title="Lihat">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>

                                    <a href="{{ route('admin.anggota.edit', $item->id) }}"
                                       class="inline-flex items-center justify-center w-9 h-9 rounded-xl border-2 border-slate-950 bg-amber-100 text-slate-950 hover:bg-amber-200 transition"
                                       title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>

                                    @if ($item->status == 'diblokir')
                                        <form action="{{ route('admin.anggota.updateStatus', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="aktif">

                                            <button type="submit"
                                                    onclick="return confirm('Aktifkan kembali anggota ini?')"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-xl border-2 border-slate-950 bg-emerald-100 text-slate-950 hover:bg-emerald-200 transition"
                                                    title="Aktifkan">
                                                <span class="material-symbols-outlined text-[18px]">lock_open</span>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.anggota.updateStatus', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="diblokir">

                                            <button type="submit"
                                                    onclick="return confirm('Blokir anggota ini?')"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-xl border-2 border-slate-950 bg-rose-100 text-slate-950 hover:bg-rose-200 transition"
                                                    title="Blokir">
                                                <span class="material-symbols-outlined text-[18px]">block</span>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.anggota.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                onclick="return confirm('Yakin ingin menghapus anggota ini?')"
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
                                    <span class="material-symbols-outlined text-[38px] text-slate-950">
                                        group_off
                                    </span>
                                    <p class="mt-2 text-sm font-black text-slate-950">
                                        Data anggota belum tersedia.
                                    </p>
                                    <p class="text-xs text-slate-500 mt-1 font-semibold">
                                        Silakan tambahkan anggota baru terlebih dahulu.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-5 py-4 border-t-2 border-slate-950 bg-slate-50 flex flex-col md:flex-row justify-between items-center gap-3">
            <p class="text-xs text-slate-600 font-bold">
                Showing {{ $anggota->firstItem() ?? 0 }} to {{ $anggota->lastItem() ?? 0 }} of {{ $anggota->total() }} entries
            </p>

            <div class="text-sm font-bold">
                {{ $anggota->links() }}
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filterAnggotaForm');
        const searchInput = document.getElementById('searchAnggotaInput');
        const prodiSelect = document.getElementById('prodiAnggotaSelect');
        const statusSelect = document.getElementById('statusAnggotaSelect');

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

        if (prodiSelect) {
            prodiSelect.addEventListener('change', submitFilter);
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', submitFilter);
        }
    });
</script>

@endsection
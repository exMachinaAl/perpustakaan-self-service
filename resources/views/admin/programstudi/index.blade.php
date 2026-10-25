@extends('layouts.admin')

@section('title', 'Program Studi')

@section('content')
<style>
    .page-smooth {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 0.25s ease, transform 0.25s ease;
    }

    .page-smooth.page-leaving {
        opacity: 0;
        transform: translateY(6px);
    }
</style>

<div id="pageContent" class="page-smooth px-4 sm:px-6 lg:px-8 py-6 max-w-[1180px] mx-auto">

    {{-- Header --}}
    <div class="mb-6">
        <nav class="flex text-xs text-gray-500 gap-2 mb-2">
            <a class="hover:text-[#001e40]" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span>/</span>
            <span class="text-[#001e40] font-semibold">Program Studi</span>
        </nav>

        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#001e40]">Program Studi</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola daftar program studi mahasiswa perpustakaan.
                </p>
            </div>

            {{-- Pakai halaman create, bukan modal --}}
            <a href="{{ route('admin.program-studi.create') }}"
               class="inline-flex items-center justify-center gap-2 bg-[#001e40] text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90 transition-all shadow-sm">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Tambah Program Studi
            </a>
        </div>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 text-green-700 text-sm font-semibold border border-green-100">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 text-red-700 text-sm font-semibold border border-red-100">
            {{ session('error') }}
        </div>
    @endif

    {{-- Statistik --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center text-[#001e40]">
                <span class="material-symbols-outlined text-[26px]">account_balance</span>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Prodi</p>
                <p class="text-xl font-bold text-[#001e40]">{{ $totalProdi }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-11 h-11 bg-yellow-50 rounded-xl flex items-center justify-center text-yellow-700">
                <span class="material-symbols-outlined text-[26px]">corporate_fare</span>
            </div>
            <div>
                <p class="text-xs text-gray-500">Fakultas</p>
                <p class="text-xl font-bold text-[#001e40]">{{ $totalFakultas }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center text-green-700">
                <span class="material-symbols-outlined text-[26px]">check_circle</span>
            </div>
            <div>
                <p class="text-xs text-gray-500">Prodi Aktif</p>
                <p class="text-xl font-bold text-[#001e40]">{{ $prodiAktif }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
            <div class="w-11 h-11 bg-red-50 rounded-xl flex items-center justify-center text-red-700">
                <span class="material-symbols-outlined text-[26px]">cancel</span>
            </div>
            <div>
                <p class="text-xs text-gray-500">Prodi Nonaktif</p>
                <p class="text-xl font-bold text-[#001e40]">{{ $prodiNonaktif }}</p>
            </div>
        </div>

    </div>

    {{-- Card Tabel --}}
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">

        {{-- Filter --}}
<form id="filterForm"
      action="{{ route('admin.program-studi.index') }}"
      method="GET"
      class="p-4 border-b border-gray-100 bg-white flex flex-col lg:flex-row gap-3 lg:items-center">

    <div class="relative w-full lg:max-w-[320px]">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[20px]">
            search
        </span>

        <input name="search"
               id="searchInput"
               value="{{ request('search') }}"
               class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-[#001e40] focus:border-[#001e40]"
               placeholder="Cari nama, kode, atau fakultas..."
               type="text"
               autocomplete="off">
    </div>

    <div class="flex flex-col sm:flex-row gap-3 lg:ml-auto">

        <select name="fakultas"
                id="fakultasSelect"
                class="bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 text-sm text-gray-600 min-w-[220px]">
            <option value="">Semua Fakultas</option>

            <option value="Fakultas Teknik" {{ request('fakultas') == 'Fakultas Teknik' ? 'selected' : '' }}>
                Fakultas Teknik
            </option>

            <option value="Fakultas Ekonomi dan Bisnis" {{ request('fakultas') == 'Fakultas Ekonomi dan Bisnis' ? 'selected' : '' }}>
                Fakultas Ekonomi dan Bisnis
            </option>

            <option value="Fakultas Hukum" {{ request('fakultas') == 'Fakultas Hukum' ? 'selected' : '' }}>
                Fakultas Hukum
            </option>

            <option value="Fakultas Pertanian" {{ request('fakultas') == 'Fakultas Pertanian' ? 'selected' : '' }}>
                Fakultas Pertanian
            </option>

            <option value="Fakultas Keguruan dan Ilmu Pendidikan" {{ request('fakultas') == 'Fakultas Keguruan dan Ilmu Pendidikan' ? 'selected' : '' }}>
                Fakultas Keguruan dan Ilmu Pendidikan
            </option>

            <option value="Fakultas Agama Islam" {{ request('fakultas') == 'Fakultas Agama Islam' ? 'selected' : '' }}>
                Fakultas Agama Islam
            </option>

            <option value="Fakultas Psikologi" {{ request('fakultas') == 'Fakultas Psikologi' ? 'selected' : '' }}>
                Fakultas Psikologi
            </option>

            <option value="Fakultas Kesehatan" {{ request('fakultas') == 'Fakultas Kesehatan' ? 'selected' : '' }}>
                Fakultas Kesehatan
            </option>

            <option value="Fakultas Kedokteran" {{ request('fakultas') == 'Fakultas Kedokteran' ? 'selected' : '' }}>
                Fakultas Kedokteran
            </option>

            <option value="Sekolah Pascasarjana" {{ request('fakultas') == 'Sekolah Pascasarjana' ? 'selected' : '' }}>
                Sekolah Pascasarjana
            </option>
        </select>

        <select name="status"
                id="statusSelect"
                class="bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-3 text-sm text-gray-600 min-w-[150px]">
            <option value="">Semua Status</option>
            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
            <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
        </select>

        <a href="{{ route('admin.program-studi.index') }}"
           class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold border border-gray-200 text-gray-600 hover:bg-gray-50 transition-all">
            Reset
        </a>
    </div>
</form>

        {{-- Tabel --}}
        <div class="w-full overflow-x-auto">
            <table class="w-full table-fixed text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="w-[46px] py-3 px-3 text-[11px] font-bold text-[#001e40] uppercase">No</th>
                        <th class="w-[30%] py-3 px-3 text-[11px] font-bold text-[#001e40] uppercase">Program Studi</th>
                        <th class="w-[14%] py-3 px-3 text-[11px] font-bold text-[#001e40] uppercase">Kode</th>
                        <th class="w-[18%] py-3 px-3 text-[11px] font-bold text-[#001e40] uppercase">Fakultas</th>
                        <th class="w-[10%] py-3 px-3 text-[11px] font-bold text-[#001e40] uppercase text-center">Anggota</th>
                        <th class="w-[12%] py-3 px-3 text-[11px] font-bold text-[#001e40] uppercase">Status</th>
                        <th class="w-[12%] py-3 px-3 text-[11px] font-bold text-[#001e40] uppercase">Dibuat</th>
                        <th class="w-[12%] py-3 px-3 text-[11px] font-bold text-[#001e40] uppercase text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($prodi as $item)
                        <tr class="hover:bg-gray-50 transition-colors">

                            <td class="py-3 px-3 text-xs text-gray-500">
                                {{ $prodi->firstItem() + $loop->index }}
                            </td>

                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-8 h-8 shrink-0 rounded-xl bg-[#001e40] text-white flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($item->nama, 0, 1)) }}
                                    </div>

                                    <div class="min-w-0">
                                        <p class="font-semibold text-sm text-gray-800 truncate">
                                            {{ $item->nama }}
                                        </p>
                                        <p class="text-[11px] text-gray-500 truncate">
                                            Program Studi
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="py-3 px-3 text-xs text-gray-600 truncate">
                                {{ $item->kode_prodi ?? '-' }}
                            </td>

                            <td class="py-3 px-3 text-xs text-gray-600 truncate">
                                {{ $item->fakultas ?? '-' }}
                            </td>

                            <td class="py-3 px-3 text-center text-xs font-bold text-[#001e40]">
                                {{ $item->jumlah_anggota ?? 0 }}
                            </td>

                            <td class="py-3 px-3">
                                @if ($item->status == 'aktif')
                                    <span class="inline-flex px-2 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-bold uppercase">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-[10px] font-bold uppercase">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-3 text-xs text-gray-600">
                                {{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}
                            </td>

                            <td class="py-3 px-3">
                                <div class="flex justify-center items-center gap-1">

                                    <a href="{{ route('admin.program-studi.show', $item->id) }}"
                                       class="w-7 h-7 flex items-center justify-center text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="Lihat">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span>
                                    </a>

                                    <a href="{{ route('admin.program-studi.edit', $item->id) }}"
                                       class="w-7 h-7 flex items-center justify-center text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors"
                                       title="Edit">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </a>

                                    <form action="{{ route('admin.program-studi.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                onclick="return confirm('Yakin ingin menghapus program studi ini?')"
                                                class="w-7 h-7 flex items-center justify-center text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Hapus">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <span class="material-symbols-outlined text-[42px] mb-2 text-gray-300">
                                        school
                                    </span>
                                    <p class="text-sm font-semibold">Data program studi belum tersedia.</p>
                                    <p class="text-xs text-gray-400">Silakan tambahkan program studi baru terlebih dahulu.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 flex flex-col md:flex-row justify-between items-center gap-3 bg-gray-50 border-t border-gray-100">
            <p class="text-xs text-gray-500">
                Showing {{ $prodi->firstItem() ?? 0 }} to {{ $prodi->lastItem() ?? 0 }} of {{ $prodi->total() }} entries
            </p>

            <div class="text-sm">
                {{ $prodi->links() }}
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filterForm');
        const searchInput = document.getElementById('searchInput');
        const fakultasSelect = document.getElementById('fakultasSelect');
        const statusSelect = document.getElementById('statusSelect');

        let typingTimer;

        function submitFilter() {
            form.submit();
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(typingTimer);

                typingTimer = setTimeout(function () {
                    submitFilter();
                }, 500);
            });
        }

        if (fakultasSelect) {
            fakultasSelect.addEventListener('change', submitFilter);
        }

        if (statusSelect) {
            statusSelect.addEventListener('change', submitFilter);
        }
    });
</script>
@endpush
@endsection
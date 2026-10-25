@extends('layouts.admin')

@section('title', 'Verifikasi Peminjaman')

@section('search_action', route('admin.verifikasi.index'))
@section('search_placeholder', 'Cari nama, NIM, judul buku...')

@section('content')

<div class="p-6 bg-surface min-h-[calc(100vh-72px)]">

    <div class="max-w-6xl mx-auto">

        {{-- Header --}}
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-primary">
                    Verifikasi Peminjaman
                </h1>
                <p class="text-sm text-on-surface-variant mt-1">
                    Kelola pengajuan peminjaman buku yang masih menunggu persetujuan admin.
                </p>
            </div>

            <div class="bg-white border border-outline-variant rounded-xl px-5 py-4 shadow-sm flex items-center gap-4">
                <div class="w-11 h-11 rounded-lg bg-tertiary-fixed/30 text-tertiary flex items-center justify-center">
                    <span class="material-symbols-outlined">hourglass_empty</span>
                </div>

                <div>
                    <p class="text-xs text-on-surface-variant font-semibold">
                        Menunggu Verifikasi
                    </p>
                    <p class="text-xl font-bold text-primary">
                        {{ $totalMenunggu ?? 0 }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Alert --}}
        @if(session('success'))
            <div class="mb-5 bg-green-50 border border-green-200 text-green-700 px-5 py-3 rounded-xl text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-xl text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter --}}
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm mb-5">
            <div class="px-5 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <h2 class="font-bold text-primary">
                    Daftar Pengajuan
                </h2>

                <form action="{{ route('admin.verifikasi.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">
                            search
                        </span>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama / judul / NIM..."
                            class="w-full sm:w-72 bg-white border border-outline-variant rounded-lg pl-10 pr-4 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary"
                        >
                    </div>

                    <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-semibold hover:opacity-90 transition-all flex items-center gap-2 justify-center">
                        <span class="material-symbols-outlined text-[18px]">filter_list</span>
                        Filter
                    </button>

                    @if(request('search'))
                        <a href="{{ route('admin.verifikasi.index') }}"
                           class="px-4 py-2 border border-primary text-primary rounded-lg text-sm font-semibold hover:bg-primary/5 transition-all text-center">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container-high text-primary text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-4 w-14">No</th>
                            <th class="px-5 py-4 min-w-[180px]">Peminjam</th>
                            <th class="px-5 py-4 min-w-[260px]">Buku</th>
                            <th class="px-5 py-4">Tgl Pengajuan</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-outline-variant/40 text-sm">
                        @forelse($peminjaman as $item)
                            <tr class="hover:bg-surface-container-low/50 transition-colors">

                                <td class="px-5 py-4 font-semibold text-on-surface-variant">
                                    {{ $loop->iteration + ($peminjaman->currentPage() - 1) * $peminjaman->perPage() }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-primary">
                                            {{ $item->user->nama ?? $item->user->name ?? '-' }}
                                        </span>
                                        <span class="text-xs text-on-surface-variant">
                                            {{ $item->user->nim ?? '-' }}
                                        </span>
                                        <span class="text-xs text-on-surface-variant">
                                            {{ $item->user->email ?? '-' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-14 rounded bg-surface-container overflow-hidden border border-outline-variant shrink-0">
                                            @if($item->buku && $item->buku->cover)
                                                <img
                                                    src="{{ asset('storage/' . $item->buku->cover) }}"
                                                    alt="{{ $item->buku->judul }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-on-surface-variant">
                                                    <span class="material-symbols-outlined">menu_book</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <p class="font-bold text-on-surface truncate">
                                                {{ $item->buku->judul ?? 'Buku tidak ditemukan' }}
                                            </p>
                                            <p class="text-xs text-on-surface-variant truncate">
                                                {{ $item->buku->pengarang ?? '-' }}
                                            </p>
                                            <p class="text-xs text-on-surface-variant truncate">
                                                Stok: {{ $item->buku->stock ?? 0 }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-on-surface-variant">
                                    {{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    <span class="px-3 py-1 bg-tertiary-fixed/30 text-tertiary rounded-full text-[12px] font-bold uppercase">
                                        Menunggu
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-center gap-2">

                                        <form action="{{ route('admin.verifikasi.update-status', $item->peminjaman_id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Setujui peminjaman ini?')">
                                            @csrf
                                            @method('PUT')

                                            <input type="hidden" name="status" value="dipinjam">

                                            <button type="submit"
                                                    class="px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-600 hover:text-white transition-all flex items-center gap-1 text-xs font-bold">
                                                <span class="material-symbols-outlined text-[18px]">check</span>
                                                Setujui
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.verifikasi.update-status', $item->peminjaman_id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Tolak peminjaman ini?')">
                                            @csrf
                                            @method('PUT')

                                            <input type="hidden" name="status" value="ditolak">

                                            <button type="submit"
                                                    class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-600 hover:text-white transition-all flex items-center gap-1 text-xs font-bold">
                                                <span class="material-symbols-outlined text-[18px]">close</span>
                                                Tolak
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-on-surface-variant">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="material-symbols-outlined text-[50px] text-on-surface-variant/40">
                                            fact_check
                                        </span>
                                        <p class="font-bold text-primary">
                                            Tidak ada pengajuan menunggu
                                        </p>
                                        <p class="text-sm">
                                            Semua pengajuan peminjaman sudah diverifikasi.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($peminjaman->total() > 0)
                <div class="px-5 py-4 border-t border-outline-variant bg-surface-container-low/30 flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                    <p class="text-xs text-on-surface-variant">
                        Menampilkan {{ $peminjaman->firstItem() }} - {{ $peminjaman->lastItem() }} dari {{ $peminjaman->total() }} pengajuan
                    </p>

                    @if($peminjaman->hasPages())
                        <div>
                            {{ $peminjaman->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
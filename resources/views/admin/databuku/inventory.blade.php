@extends('layouts.admin')

@section('title', 'Eksemplar & QR Buku')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 space-y-5">
    <div class="bg-white border-[3px] border-slate-950 shadow-[5px_5px_0_#0f172a] rounded-2xl p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <p class="text-xs font-black uppercase tracking-widest text-sky-700">Inventaris fisik</p>
            <h1 class="text-2xl font-black text-slate-950 mt-1">{{ $book->judul }}</h1>
            <p class="text-sm text-slate-600 mt-1">Kelola satu identitas unik untuk setiap buku fisik. Barcode pabrik dapat langsung dipakai di kios.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.databuku.show', $book->id_buku) }}" class="px-4 py-2.5 rounded-xl border-2 border-slate-950 font-bold bg-white">Kembali</a>
            @if($copies->where('active', true)->isNotEmpty())
                <a href="{{ route('admin.databuku.inventory.print', $book) }}" target="_blank" rel="noopener" class="px-4 py-2.5 rounded-xl border-2 border-slate-950 font-bold bg-amber-300">Cetak Semua QR</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border-2 border-emerald-700 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border-2 border-red-700 bg-red-50 px-4 py-3 text-sm font-bold text-red-800">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border-2 border-red-700 bg-red-50 px-4 py-3 text-sm font-bold text-red-800">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach(['total' => 'Total', 'available' => 'Tersedia', 'borrowed' => 'Dipinjam', 'inactive' => 'Nonaktif'] as $key => $label)
            <div class="bg-white border-2 border-slate-950 rounded-xl p-4">
                <p class="text-xs font-bold uppercase text-slate-500">{{ $label }}</p>
                <p class="text-2xl font-black text-slate-950">{{ $counts[$key] }}</p>
            </div>
        @endforeach
    </div>

    <section class="bg-sky-50 border-[3px] border-slate-950 rounded-2xl p-5">
        <h2 class="font-black text-lg">Tambah buku fisik</h2>
        <p class="text-sm text-slate-600 mb-4">Kosongkan kode inventaris untuk membuat kode otomatis. Scan atau ketik barcode yang tercetak pada buku bila tersedia.</p>
        <form method="POST" action="{{ route('admin.databuku.inventory.store', $book) }}" class="grid md:grid-cols-[1fr_1fr_auto] gap-3 items-end">
            @csrf
            <label class="text-sm font-bold">Kode inventaris (opsional)
                <input name="inventory_code" value="{{ old('inventory_code') }}" maxlength="80" placeholder="Otomatis: BK-000001-001" class="mt-1 w-full rounded-xl border-2 border-slate-950 px-3 py-2.5 font-mono uppercase">
            </label>
            <label class="text-sm font-bold">Barcode buku (opsional)
                <input name="barcode" value="{{ old('barcode') }}" maxlength="120" placeholder="Scan EAN / ISBN eksemplar" class="mt-1 w-full rounded-xl border-2 border-slate-950 px-3 py-2.5 font-mono uppercase">
            </label>
            <button class="rounded-xl border-2 border-slate-950 bg-slate-950 text-white px-5 py-2.5 font-black">Tambah + QR</button>
        </form>
    </section>

    <section class="bg-white border-[3px] border-slate-950 rounded-2xl overflow-hidden">
        <form id="print-selected" method="GET" action="{{ route('admin.databuku.inventory.print', $book) }}" target="_blank"></form>
            <div class="p-4 border-b-2 border-slate-950 flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-black text-lg">Daftar eksemplar</h2>
                    <p class="text-xs text-slate-500">Pilih label aktif, lalu cetak hanya yang dipilih.</p>
                </div>
                <button form="print-selected" class="rounded-xl border-2 border-slate-950 bg-amber-300 px-4 py-2 text-sm font-black" {{ $copies->where('active', true)->isEmpty() ? 'disabled' : '' }}>Cetak Pilihan</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-sm">
                    <thead class="bg-slate-100 text-left uppercase text-xs">
                        <tr><th class="p-3">Pilih</th><th class="p-3">Kode inventaris</th><th class="p-3">Barcode</th><th class="p-3">Status</th><th class="p-3">Aksi</th></tr>
                    </thead>
                    <tbody class="divide-y-2 divide-slate-200">
                        @forelse($copies as $copy)
                            <tr class="align-top {{ !$copy->active ? 'bg-slate-50 opacity-75' : '' }}">
                                <td class="p-3"><input form="print-selected" type="checkbox" name="copy_ids[]" value="{{ $copy->id }}" class="h-5 w-5" {{ $copy->active ? '' : 'disabled' }}></td>
                                <td class="p-3" colspan="2">
                                    <form id="copy-update-{{ $copy->id }}" method="POST" action="{{ route('admin.databuku.inventory.update', [$book, $copy->id]) }}" class="contents">
                                        @csrf @method('PATCH')
                                    </form>
                                    <div class="grid grid-cols-2 gap-3">
                                        <input form="copy-update-{{ $copy->id }}" name="inventory_code" value="{{ $copy->inventory_code }}" required maxlength="80" class="rounded-lg border-2 border-slate-800 px-2 py-2 font-mono uppercase">
                                        <div class="flex gap-2">
                                            <input form="copy-update-{{ $copy->id }}" name="barcode" value="{{ $copy->barcode }}" maxlength="120" placeholder="Belum ada" class="min-w-0 flex-1 rounded-lg border-2 border-slate-800 px-2 py-2 font-mono uppercase">
                                            <button form="copy-update-{{ $copy->id }}" class="rounded-lg border-2 border-slate-950 bg-sky-200 px-3 font-bold">Simpan</button>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <span class="inline-flex rounded-full border border-slate-400 px-2 py-1 text-xs font-black uppercase">{{ $copy->active ? $copy->status : 'inactive' }}</span>
                                </td>
                                <td class="p-3">
                                    <div class="flex flex-wrap gap-2">
                                        @if($copy->active)
                                            <form method="POST" action="{{ route('admin.databuku.inventory.reissue', [$book, $copy->id]) }}">
                                                @csrf
                                                <button onclick="return confirm('QR lama akan langsung tidak berlaku. Lanjutkan?')" class="rounded-lg border-2 border-slate-950 px-3 py-2 text-xs font-bold">QR Baru</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.databuku.inventory.toggle', [$book, $copy->id]) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="active" value="{{ $copy->active ? 0 : 1 }}">
                                            <button class="rounded-lg border-2 border-slate-950 px-3 py-2 text-xs font-bold {{ $copy->active ? 'bg-red-100' : 'bg-emerald-100' }}">{{ $copy->active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-10 text-center text-slate-500">Belum ada eksemplar. Tambahkan buku fisik pertama di atas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    </section>
</div>
@endsection

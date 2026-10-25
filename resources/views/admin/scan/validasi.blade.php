@extends('layouts.admin')

@section('title', 'Validasi Bukti Peminjaman')

@section('content')

@php
    $tanggalPinjam = $peminjaman->tanggal_peminjaman ?? $peminjaman->tanggal_pinjam ?? null;

    $batasPengembalian = $peminjaman->batas_pengembalian ?? null;

    if (!$batasPengembalian && $tanggalPinjam) {
        $batasPengembalian = \Carbon\Carbon::parse($tanggalPinjam)->addDays(7);
    }

    $status = strtolower($peminjaman->status ?? '-');
@endphp

<div class="space-y-6">

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Validasi Bukti Peminjaman
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Hasil scan QR bukti peminjaman user.
                </p>
            </div>

            <a href="{{ route('admin.scan.index') }}"
               class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition">
                Scan Lagi
            </a>
        </div>
    </div>

    @if($status == 'dipinjam')
        <div class="rounded-2xl bg-green-50 border border-green-200 p-5">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-green-700">
                    verified
                </span>

                <div>
                    <h2 class="font-bold text-green-800">
                        Bukti Valid
                    </h2>
                    <p class="text-sm text-green-700 mt-1">
                        Peminjaman ini sudah disetujui admin. User boleh mengambil buku di perpustakaan.
                    </p>
                </div>
            </div>
        </div>
    @elseif($status == 'menunggu')
        <div class="rounded-2xl bg-yellow-50 border border-yellow-200 p-5">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-yellow-700">
                    warning
                </span>

                <div>
                    <h2 class="font-bold text-yellow-800">
                        Belum Diverifikasi
                    </h2>
                    <p class="text-sm text-yellow-700 mt-1">
                        Peminjaman ini masih menunggu persetujuan admin. Buku belum boleh diberikan.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="rounded-2xl bg-red-50 border border-red-200 p-5">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-red-700">
                    cancel
                </span>

                <div>
                    <h2 class="font-bold text-red-800">
                        Bukti Tidak Aktif
                    </h2>
                    <p class="text-sm text-red-700 mt-1">
                        Status peminjaman saat ini adalah {{ ucfirst($status) }}. Buku tidak boleh diberikan.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-slate-800 text-white px-6 py-4">
            <h2 class="font-bold text-lg">
                Detail Peminjaman
            </h2>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>
                <h3 class="font-bold text-slate-700 mb-3">
                    Data Peminjam
                </h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-slate-500">Nama</p>
                        <p class="font-semibold text-slate-800">
                            {{ $peminjaman->user->nama ?? $peminjaman->user->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Email</p>
                        <p class="font-semibold text-slate-800">
                            {{ $peminjaman->user->email ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Program Studi</p>
                        <p class="font-semibold text-slate-800">
                            {{ $peminjaman->user->prodi->nama ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-slate-700 mb-3">
                    Data Buku
                </h3>

                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-slate-500">Judul Buku</p>
                        <p class="font-semibold text-slate-800">
                            {{ $peminjaman->buku->judul ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Kode Buku</p>
                        <p class="font-semibold text-slate-800">
                            {{ $peminjaman->buku->kode ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-slate-500">Lokasi Rak</p>
                        <p class="font-semibold text-slate-800">
                            {{ $peminjaman->buku->lokasi ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-sm text-slate-500">Tanggal Peminjaman</p>
                <p class="font-bold text-slate-800">
                    {{ $tanggalPinjam ? \Carbon\Carbon::parse($tanggalPinjam)->format('d M Y') : '—' }}
                </p>
            </div>

            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-sm text-slate-500">Batas Pengembalian</p>
                <p class="font-bold text-slate-800">
                    {{ $batasPengembalian ? \Carbon\Carbon::parse($batasPengembalian)->format('d M Y') : '—' }}
                </p>
            </div>

            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-sm text-slate-500">Status</p>
                <p class="font-bold text-slate-800">
                    {{ ucfirst($status) }}
                </p>
            </div>

            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-sm text-slate-500">Nomor Peminjaman</p>
                <p class="font-bold text-slate-800">
                    #{{ $peminjaman->peminjaman_id }}
                </p>
            </div>

        </div>
    </div>

</div>
@endsection
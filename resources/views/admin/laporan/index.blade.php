@extends('layouts.app')

@section('title', 'Laporan')

@section('content')

<style>
    .table-scroll {
        max-height: 300px; /* tinggi area scroll */
        overflow-y: auto;
        overflow-x: hidden;
    }
</style>

<div class="px-4">

    <div class="flex justify-end mb-4">
    <a href="{{ route('laporan.export') }}"
       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
        Export Excel
    </a>
</div>


    <h1 class="text-3xl font-bold mb-6 text-gray-800">📊 Laporan Perpustakaan</h1>

    {{-- === Laporan Peminjaman === --}}
    <div class="mb-6 bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-semibold mb-4 text-blue-700 flex items-center justify-between">
            <span class="flex items-center gap-2">
                <i class="bx bx-book-open text-2xl"></i> Laporan Peminjaman
            </span>

            <span class="text-sm font-semibold bg-blue-100 text-blue-700 px-3 py-1 rounded-full">
                Total: {{ $peminjaman->count() }}
            </span>
        </h2>

        <div class="table-scroll border rounded-lg">
            <table class="w-full">
                <thead class="bg-blue-50 border-b">
                    <tr class="text-left text-gray-600">
                        <th class="p-3">User</th>
                        <th class="p-3">Buku</th>
                        <th class="p-3">Tanggal Peminjaman</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($peminjaman as $p)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3 font-medium">{{ $p->user->nama ?? '-' }}</td>
                        <td class="p-3">{{ $p->buku->judul }}</td>
                        <td class="p-3">{{ $p->tanggal_peminjaman }}</td>
                        <td class="p-3">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($p->status === 'dipinjam') bg-yellow-200 text-yellow-700
                                @elseif($p->status === 'dikembalikan') bg-green-200 text-green-700
                                @else bg-red-200 text-red-700
                                @endif">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>



   {{-- === Laporan Buku === --}}
<div class="mb-6 bg-white rounded-xl shadow p-6">
    <h2 class="text-xl font-semibold mb-4 text-purple-700 flex items-center justify-between">
        <span class="flex items-center gap-2">
            <i class="bx bx-book text-2xl"></i> Laporan Buku
        </span>

        <span class="text-sm font-semibold bg-purple-100 text-purple-700 px-3 py-1 rounded-full">
            Total: {{ $buku->count() }}
        </span>
    </h2>

    <div class="table-scroll border rounded-lg">
        <table class="w-full">
            <thead class="bg-purple-50 border-b">
                <tr class="text-left text-gray-600">
                    <th class="p-3">Judul Buku</th>
                    <th class="p-3">Kategori</th>
                    <th class="p-3">Jumlah Peminjaman</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($buku as $b)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-medium">{{ $b->judul ?? '-' }}</td>
                    <td class="p-3"><span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">
                    {{ $b->kategori }}
                    </span></td>
                    <td class="p-3">{{ $b->peminjaman->count() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>




    {{-- === Laporan Pengembalian === --}}
<div class="mb-6 bg-white rounded-xl shadow p-6">
    <h2 class="text-xl font-semibold mb-4 text-green-700 flex items-center justify-between">
        <span class="flex items-center gap-2">
            <i class="bx bx-log-out-circle text-2xl"></i> Laporan Pengembalian
        </span>

        <span class="text-sm font-semibold bg-green-100 text-green-700 px-3 py-1 rounded-full">
            Total: {{ $peminjaman->whereNotNull('tanggal_kembali')->count() }}
        </span>
    </h2>

    <div class="table-scroll border rounded-lg">
        <table class="w-full">
            <thead class="bg-green-50 border-b">
                <tr class="text-left text-gray-600">
                    <th class="p-3">User</th>
                    <th class="p-3">Buku</th>
                    <th class="p-3">Tanggal Kembali</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($peminjaman->whereNotNull('tanggal_kembali') as $p)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-medium">{{ $p->user->nama ?? '-' }}</td>
                    <td class="p-3">{{ $p->buku->judul ?? '-' }}</td>
                    <td class="p-3">{{ $p->tanggal_kembali ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>



    {{-- === Laporan Anggota === --}}
    <div class="bg-white rounded-xl shadow p-6 mb-10">
        <h2 class="text-xl font-semibold mb-4 text-orange-700 flex items-center justify-between">
            <span class="flex items-center gap-2">
                <i class="bx bx-group text-2xl"></i> Laporan Anggota
            </span>

            <span class="text-sm font-semibold bg-orange-100 text-orange-700 px-3 py-1 rounded-full">
                Total: {{ $anggota->count() }}
            </span>
        </h2>

        <div class="table-scroll grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach ($anggota as $a)
            <div class="p-4 bg-orange-50 border rounded-lg">
                <p class="font-semibold text-gray-700">{{ $a->nama }}</p>
                <p class="text-xs text-gray-500">Prodi: {{ $a->prodi->nama ?? '-' }}</p>
            </div>
            @endforeach
        </div>
    </div>

</div>

@endsection

@extends('layouts.admin')

@section('title', 'Detail Anggota')

@section('content')

<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-[1050px] mx-auto">

    <div class="mb-6">
        <nav class="flex text-xs text-gray-500 gap-2 mb-2">
            <a class="hover:text-[#001e40]" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span>/</span>
            <a class="hover:text-[#001e40]" href="{{ route('admin.anggota.index') }}">Data Anggota</a>
            <span>/</span>
            <span class="text-[#001e40] font-semibold">Detail Anggota</span>
        </nav>

        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#001e40]">Detail Anggota</h2>
                <p class="text-sm text-gray-500 mt-1">Informasi profil dan aktivitas peminjaman anggota.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.anggota.index') }}"
                   class="inline-flex items-center justify-center gap-2 border border-gray-200 text-gray-600 px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-all">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                    Kembali
                </a>

                <a href="{{ route('admin.anggota.edit', $anggota->id) }}"
                   class="inline-flex items-center justify-center gap-2 bg-[#001e40] text-white px-4 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90 transition-all">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                    Edit
                </a>
            </div>
        </div>
    </div>

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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Profile Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="h-24 bg-[#001e40]"></div>

                <div class="px-5 pb-5 -mt-10">
                    <div class="w-20 h-20 rounded-2xl bg-white p-1 shadow-sm">
                        <div class="w-full h-full rounded-xl bg-[#001e40] text-white flex items-center justify-center text-3xl font-bold">
                            {{ strtoupper(substr($anggota->nama, 0, 1)) }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-xl font-bold text-gray-800">{{ $anggota->nama }}</h3>
                        <p class="text-sm text-gray-500">{{ $anggota->email }}</p>
                    </div>

                    <div class="mt-4">
                        @if ($anggota->status == 'aktif')
                            <span class="inline-flex px-3 py-1.5 bg-green-50 text-green-700 rounded-full text-xs font-bold uppercase">
                                Aktif
                            </span>
                        @elseif ($anggota->status == 'tidak_aktif')
                            <span class="inline-flex px-3 py-1.5 bg-gray-100 text-gray-600 rounded-full text-xs font-bold uppercase">
                                Tidak Aktif
                            </span>
                        @else
                            <span class="inline-flex px-3 py-1.5 bg-red-50 text-red-700 rounded-full text-xs font-bold uppercase">
                                Diblokir
                            </span>
                        @endif
                    </div>

                    <div class="mt-5 space-y-3">
                        <div class="flex items-center gap-3 text-sm">
                            <span class="material-symbols-outlined text-gray-400 text-[20px]">badge</span>
                            <div>
                                <p class="text-xs text-gray-400">NIM</p>
                                <p class="font-semibold text-gray-700">{{ $anggota->nim }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-sm">
                            <span class="material-symbols-outlined text-gray-400 text-[20px]">school</span>
                            <div>
                                <p class="text-xs text-gray-400">Program Studi</p>
                                <p class="font-semibold text-gray-700">
                                    {{ $anggota->prodi->nama ?? $anggota->prodi->nama_prodi ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-sm">
                            <span class="material-symbols-outlined text-gray-400 text-[20px]">calendar_month</span>
                            <div>
                                <p class="text-xs text-gray-400">Tanggal Daftar</p>
                                <p class="font-semibold text-gray-700">
                                    {{ $anggota->created_at ? $anggota->created_at->format('d M Y') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="p-3 rounded-xl bg-blue-50">
                            <p class="text-xs text-gray-500">Total Pinjaman</p>
                            <p class="text-xl font-bold text-[#001e40]">
                                {{ $anggota->peminjaman_count ?? $anggota->peminjaman->count() }}
                            </p>
                        </div>

                        <div class="p-3 rounded-xl bg-yellow-50">
                            <p class="text-xs text-gray-500">Role</p>
                            <p class="text-xl font-bold text-[#001e40]">
                                {{ ucfirst($anggota->role) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail & Riwayat --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Detail Data --}}
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-sm font-bold text-[#001e40] uppercase tracking-wide">Informasi Anggota</h3>
                    <p class="text-xs text-gray-500 mt-1">Data utama yang terhubung dengan akun pengguna.</p>
                </div>

                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <p class="text-xs text-gray-500">Nama Lengkap</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1">{{ $anggota->nama }}</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <p class="text-xs text-gray-500">Email</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1 break-all">{{ $anggota->email }}</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <p class="text-xs text-gray-500">NIM</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1">{{ $anggota->nim }}</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <p class="text-xs text-gray-500">Program Studi</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1">
                            {{ $anggota->prodi->nama ?? $anggota->prodi->nama_prodi ?? '-' }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <p class="text-xs text-gray-500">Status Akun</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1">{{ ucfirst(str_replace('_', ' ', $anggota->status)) }}</p>
                    </div>

                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-100">
                        <p class="text-xs text-gray-500">Terakhir Diperbarui</p>
                        <p class="text-sm font-semibold text-gray-800 mt-1">
                            {{ $anggota->updated_at ? $anggota->updated_at->format('d M Y H:i') : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Ubah Status Cepat --}}
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-sm font-bold text-[#001e40] uppercase tracking-wide">Aksi Cepat</h3>
                    <p class="text-xs text-gray-500 mt-1">Ubah status akun anggota tanpa masuk ke halaman edit.</p>
                </div>

                <div class="p-5 flex flex-wrap gap-3">
                    <form action="{{ route('admin.anggota.updateStatus', $anggota->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="aktif">
                        <button type="submit"
                                onclick="return confirm('Aktifkan anggota ini?')"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-green-50 text-green-700 border border-green-100 text-sm font-semibold hover:bg-green-100 transition-all">
                            <span class="material-symbols-outlined text-[20px]">lock_open</span>
                            Aktifkan
                        </button>
                    </form>

                    <form action="{{ route('admin.anggota.updateStatus', $anggota->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="tidak_aktif">
                        <button type="submit"
                                onclick="return confirm('Nonaktifkan anggota ini?')"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-50 text-gray-700 border border-gray-200 text-sm font-semibold hover:bg-gray-100 transition-all">
                            <span class="material-symbols-outlined text-[20px]">person_off</span>
                            Nonaktifkan
                        </button>
                    </form>

                    <form action="{{ route('admin.anggota.updateStatus', $anggota->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="diblokir">
                        <button type="submit"
                                onclick="return confirm('Blokir anggota ini?')"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-50 text-red-700 border border-red-100 text-sm font-semibold hover:bg-red-100 transition-all">
                            <span class="material-symbols-outlined text-[20px]">block</span>
                            Blokir
                        </button>
                    </form>
                </div>
            </div>

            {{-- Riwayat Peminjaman --}}
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-bold text-[#001e40] uppercase tracking-wide">Riwayat Peminjaman</h3>
                        <p class="text-xs text-gray-500 mt-1">Daftar peminjaman yang pernah dilakukan anggota.</p>
                    </div>

                    <span class="px-3 py-1 rounded-full bg-blue-50 text-[#001e40] text-xs font-bold">
                        {{ $anggota->peminjaman_count ?? $anggota->peminjaman->count() }} Transaksi
                    </span>
                </div>

                <div class="p-5">
                    @if ($anggota->peminjaman && $anggota->peminjaman->count() > 0)
                        <div class="overflow-hidden border border-gray-100 rounded-xl">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-gray-50 border-b border-gray-100">
                                        <th class="py-3 px-3 text-[11px] font-bold text-[#001e40] uppercase">No</th>
                                        <th class="py-3 px-3 text-[11px] font-bold text-[#001e40] uppercase">Tanggal Pinjam</th>
                                        <th class="py-3 px-3 text-[11px] font-bold text-[#001e40] uppercase">Tanggal Kembali</th>
                                        <th class="py-3 px-3 text-[11px] font-bold text-[#001e40] uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($anggota->peminjaman->take(8) as $pinjam)
                                        <tr>
                                            <td class="py-3 px-3 text-xs text-gray-600">{{ $loop->iteration }}</td>
                                            <td class="py-3 px-3 text-xs text-gray-600">
                                                {{ $pinjam->tanggal_pinjam ?? '-' }}
                                            </td>
                                            <td class="py-3 px-3 text-xs text-gray-600">
                                                {{ $pinjam->tanggal_kembali ?? '-' }}
                                            </td>
                                            <td class="py-3 px-3 text-xs text-gray-600">
                                                <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-[10px] font-bold uppercase">
                                                    {{ $pinjam->status ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($anggota->peminjaman->count() > 8)
                            <p class="mt-3 text-xs text-gray-500">
                                Menampilkan 8 riwayat terbaru dari total {{ $anggota->peminjaman->count() }} transaksi.
                            </p>
                        @endif
                    @else
                        <div class="py-10 flex flex-col items-center justify-center text-center text-gray-500">
                            <span class="material-symbols-outlined text-[46px] text-gray-300 mb-2">event_busy</span>
                            <p class="text-sm font-semibold">Belum ada riwayat peminjaman.</p>
                            <p class="text-xs text-gray-400 mt-1">Riwayat akan muncul setelah anggota melakukan peminjaman buku.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>

@endsection
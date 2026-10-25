@extends('layouts.user')

@section('title', 'Dashboard User - Perpustakaan')

@section('search_action', route('user.katalog'))
@section('search_placeholder', 'Cari judul buku, penulis, atau kategori...')

@section('content')

<div class="space-y-5 sm:space-y-6">

    {{-- HERO --}}
    <section class="relative overflow-hidden border-[2px] border-user-ink bg-user-paper soft-brutal-shadow-sm">
        <div class="absolute top-0 right-0 w-20 sm:w-28 h-20 sm:h-28 bg-user-gold border-l-[2px] border-b-[2px] border-user-ink"></div>
        <div class="absolute bottom-0 left-0 w-14 sm:w-20 h-14 sm:h-20 bg-user-blue-soft border-r-[2px] border-t-[2px] border-user-ink"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-[1.35fr_0.65fr] gap-4 p-4 sm:p-5 lg:p-6">
            <div class="space-y-3 sm:space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-user-gold text-user-ink border-[2px] border-user-ink soft-brutal-shadow-sm text-[10px] sm:text-xs font-extrabold uppercase tracking-[0.16em]">
                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">
                        local_library
                    </span>
                    Student Dashboard
                </div>

                <div class="space-y-2">
                    <h1 class="font-display-lg text-[23px] sm:text-[32px] lg:text-[40px] leading-[1.1] text-user-ink uppercase">
                        Selamat Datang,<br>
                        {{ $user->nama ?? Auth::user()->nama ?? Auth::user()->name ?? 'Mahasiswa' }}
                    </h1>

                    <p class="text-user-ink/75 text-[12px] sm:text-[14px] lg:text-[15px] leading-relaxed max-w-2xl font-semibold">
                        Kelola aktivitas peminjamanmu dengan tampilan yang rapi, cepat, dan nyaman.
                        Saat ini tersedia
                        <span class="font-extrabold text-user-navy">
                            {{ number_format($bukuTersedia ?? 0) }}
                        </span>
                        koleksi buku untuk mendukung kegiatan belajarmu.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2.5 pt-1">
                    <a href="{{ route('user.katalog') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-user-navy text-white border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover font-bold uppercase tracking-wide text-[11px] sm:text-[12px]">
                        <span class="material-symbols-outlined text-[17px]">search</span>
                        Jelajahi Katalog
                    </a>

                    <a href="{{ route('user.peminjaman.cek') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-user-ink border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover font-bold uppercase tracking-wide text-[11px] sm:text-[12px]">
                        <span class="material-symbols-outlined text-[17px]">pending_actions</span>
                        Cek Peminjaman
                    </a>
                </div>
            </div>

            {{-- Statistik kecil --}}
            <div class="grid grid-cols-2 gap-3 self-stretch">
                <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-3 flex flex-col justify-between min-h-[92px] sm:min-h-[110px]">
                    <div class="w-9 h-9 bg-user-gold border-[2px] border-user-ink flex items-center justify-center">
                        <span class="material-symbols-outlined text-user-ink text-[20px]">auto_stories</span>
                    </div>
                    <div>
                        <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-[0.12em] text-user-ink/65">
                            Dipinjam
                        </p>
                        <p class="text-[21px] sm:text-[26px] font-extrabold text-user-ink leading-none mt-1">
                            {{ $bukuDipinjam ?? 0 }}
                        </p>
                    </div>
                </div>

                <div class="bg-user-blue-soft border-[2px] border-user-ink soft-brutal-shadow-sm p-3 flex flex-col justify-between min-h-[92px] sm:min-h-[110px]">
                    <div class="w-9 h-9 bg-user-paper border-[2px] border-user-ink flex items-center justify-center">
                        <span class="material-symbols-outlined text-user-ink text-[20px]">schedule</span>
                    </div>
                    <div>
                        <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-[0.12em] text-user-ink/65">
                            Menunggu
                        </p>
                        <p class="text-[21px] sm:text-[26px] font-extrabold text-user-ink leading-none mt-1">
                            {{ $menungguKonfirmasi ?? 0 }}
                        </p>
                    </div>
                </div>

                <div class="bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm p-3 flex flex-col justify-between min-h-[92px] sm:min-h-[110px]">
                    <div class="w-9 h-9 bg-user-maroon/20 border-[2px] border-user-ink flex items-center justify-center">
                        <span class="material-symbols-outlined text-user-ink text-[20px]">history</span>
                    </div>
                    <div>
                        <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-[0.12em] text-user-ink/65">
                            Riwayat
                        </p>
                        <p class="text-[21px] sm:text-[26px] font-extrabold text-user-ink leading-none mt-1">
                            {{ $riwayatPeminjaman ?? 0 }}
                        </p>
                    </div>
                </div>

                <div class="bg-user-gold/20 border-[2px] border-user-ink soft-brutal-shadow-sm p-3 flex flex-col justify-between min-h-[92px] sm:min-h-[110px]">
                    <div class="w-9 h-9 bg-user-navy text-white border-[2px] border-user-ink flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">library_books</span>
                    </div>
                    <div>
                        <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-[0.12em] text-user-ink/65">
                            Tersedia
                        </p>
                        <p class="text-[20px] sm:text-[25px] font-extrabold text-user-ink leading-none mt-1">
                            {{ number_format($bukuTersedia ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- REKOMENDASI BUKU --}}
    <section class="space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm">
                <span class="material-symbols-outlined text-user-ink text-[19px]">star</span>
                <h2 class="font-headline-sm text-[15px] sm:text-[18px] uppercase text-user-ink">
                    Rekomendasi Buku
                </h2>
            </div>

            <a href="{{ route('user.katalog') }}"
               class="inline-flex items-center gap-1 px-3 py-2 bg-user-gold border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover text-[10px] sm:text-[11px] font-extrabold uppercase text-user-ink shrink-0">
                Lihat Semua
                <span class="material-symbols-outlined text-[17px]">east</span>
            </a>
        </div>

        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
            @forelse ($rekomendasiBuku ?? [] as $buku)
                <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm overflow-hidden soft-brutal-hover group">
                    <div class="relative h-[135px] sm:h-[205px] lg:h-[230px] overflow-hidden border-b-[2px] border-user-ink bg-user-paper">
                        @if (!empty($buku->cover))
                            <img src="{{ asset('storage/' . $buku->cover) }}"
                                 alt="{{ $buku->judul }}"
                                 class="w-full h-full object-cover group-hover:scale-[1.03] transition duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-[44px] sm:text-[60px] text-user-ink/60">
                                    menu_book
                                </span>
                            </div>
                        @endif

                        <div class="absolute top-2 left-2 px-2 py-1 bg-user-gold border-[2px] border-user-ink text-[8px] sm:text-[9px] font-extrabold uppercase tracking-[0.1em] text-user-ink max-w-[85%] truncate">
                            {{ $buku->kategori ?? 'Umum' }}
                        </div>
                    </div>

                    <div class="p-3 space-y-2">
                        <h3 class="text-[12px] sm:text-[14px] font-extrabold text-user-ink leading-snug line-clamp-2 min-h-[2.2rem] sm:min-h-[2.6rem]">
                            {{ $buku->judul }}
                        </h3>

                        <p class="text-[10px] sm:text-[12px] text-user-ink/70 font-semibold truncate">
                            {{ $buku->pengarang ?? '-' }}
                        </p>

                        <a href="{{ route('user.katalog.show', $buku->id_buku) }}"
                           class="mt-1 inline-flex items-center justify-center w-full px-3 py-2 bg-user-navy text-white border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover text-[10px] sm:text-[11px] font-extrabold uppercase tracking-wide">
                            Lihat Buku
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-2 xl:col-span-4 bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-6 text-center">
                    <p class="text-user-ink font-extrabold uppercase tracking-wide text-sm">
                        Belum ada rekomendasi buku.
                    </p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- PEMINJAMAN TERBARU --}}
    <section class="space-y-4">
        <div class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm">
            <span class="material-symbols-outlined text-user-ink text-[19px]">receipt_long</span>
            <h2 class="font-headline-sm text-[15px] sm:text-[18px] uppercase text-user-ink">
                Peminjaman Terbaru
            </h2>
        </div>

        <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-[700px] w-full border-collapse">
                    <thead>
                        <tr class="bg-user-navy text-white border-b-[2px] border-user-ink">
                            <th class="px-4 py-3 text-left text-[10px] sm:text-[11px] uppercase tracking-[0.12em] font-extrabold">
                                Judul Buku
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] sm:text-[11px] uppercase tracking-[0.12em] font-extrabold">
                                Tgl Pinjam
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] sm:text-[11px] uppercase tracking-[0.12em] font-extrabold">
                                Batas Kembali
                            </th>
                            <th class="px-4 py-3 text-left text-[10px] sm:text-[11px] uppercase tracking-[0.12em] font-extrabold">
                                Status
                            </th>
                            <th class="px-4 py-3 text-center text-[10px] sm:text-[11px] uppercase tracking-[0.12em] font-extrabold">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($peminjamanTerbaru ?? [] as $pinjam)
                            @php
                                $status = strtolower($pinjam->status ?? '-');

                                $tanggalPinjam = $pinjam->tanggal_pinjam
                                    ?? $pinjam->tanggal_peminjaman
                                    ?? null;

                                $batasKembali = $pinjam->batas_pengembalian ?? null;

                                if (!$batasKembali && $tanggalPinjam) {
                                    $batasKembali = \Carbon\Carbon::parse($tanggalPinjam)->addDays(7);
                                }
                            @endphp

                            <tr class="border-b-[2px] border-user-ink/10 hover:bg-user-blue-soft/35 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-9 h-12 bg-user-paper border-[2px] border-user-ink overflow-hidden shrink-0">
                                            @if ($pinjam->buku && $pinjam->buku->cover)
                                                <img src="{{ asset('storage/' . $pinjam->buku->cover) }}"
                                                     alt="{{ $pinjam->buku->judul }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <span class="material-symbols-outlined text-user-ink/60 text-[20px]">
                                                        menu_book
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <p class="text-[12px] sm:text-[13px] font-extrabold text-user-ink truncate">
                                                {{ $pinjam->buku->judul ?? 'Buku tidak ditemukan' }}
                                            </p>
                                            <p class="text-[10px] sm:text-[12px] text-user-ink/65 font-semibold truncate mt-1">
                                                {{ $pinjam->buku->pengarang ?? '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-[11px] sm:text-[12px] text-user-ink/80 font-semibold whitespace-nowrap">
                                    {{ $tanggalPinjam ? \Carbon\Carbon::parse($tanggalPinjam)->format('d M Y') : '-' }}
                                </td>

                                <td class="px-4 py-3 text-[11px] sm:text-[12px] text-user-ink/80 font-semibold whitespace-nowrap">
                                    {{ $batasKembali ? \Carbon\Carbon::parse($batasKembali)->format('d M Y') : '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    @if ($status === 'menunggu')
                                        <span class="inline-block px-2.5 py-1 bg-user-gold border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                            Menunggu
                                        </span>
                                    @elseif ($status === 'dipinjam')
                                        <span class="inline-block px-2.5 py-1 bg-user-blue-soft border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                            Dipinjam
                                        </span>
                                    @elseif ($status === 'terlambat')
                                        <span class="inline-block px-2.5 py-1 bg-red-200 border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                            Terlambat
                                        </span>
                                    @elseif ($status === 'dikembalikan')
                                        <span class="inline-block px-2.5 py-1 bg-green-200 border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                            Dikembalikan
                                        </span>
                                    @elseif ($status === 'ditolak')
                                        <span class="inline-block px-2.5 py-1 bg-red-100 border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                            Ditolak
                                        </span>
                                    @elseif ($status === 'dibatalkan')
                                        <span class="inline-block px-2.5 py-1 bg-gray-200 border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                            Dibatalkan
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-1 bg-gray-100 border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase text-user-ink">
                                            {{ ucfirst($status) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('user.peminjaman.cek') }}"
                                       class="inline-flex items-center justify-center w-9 h-9 bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover text-user-ink">
                                        <span class="material-symbols-outlined text-[19px]">visibility</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-7 text-center">
                                    <p class="text-user-ink font-extrabold uppercase tracking-wide text-sm">
                                        Belum ada data peminjaman.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

</div>

@endsection
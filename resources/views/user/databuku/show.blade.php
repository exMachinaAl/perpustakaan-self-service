@extends('layouts.user')

@section('title', 'Detail Buku - Perpustakaan')

@section('search_action', route('user.katalog'))
@section('search_placeholder', 'Cari judul buku, penulis, atau kategori...')

@section('content')

<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<div class="space-y-5 sm:space-y-6">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-[11px] sm:text-[12px] font-extrabold uppercase tracking-[0.12em] text-user-ink/70">
        <a href="{{ route('user.katalog') }}" class="hover:text-user-gold transition">
            Data Buku
        </a>

        <span class="material-symbols-outlined text-[16px]">chevron_right</span>

        <a href="{{ route('user.katalog', ['kategori' => $buku->kategori]) }}" class="hover:text-user-gold transition">
            {{ $buku->kategori ?? 'Umum' }}
        </a>

        <span class="material-symbols-outlined text-[16px]">chevron_right</span>

        <span class="text-user-ink">
            Detail Buku
        </span>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="bg-green-100 border-[2px] border-user-ink soft-brutal-shadow-sm px-4 py-3 text-[12px] sm:text-[13px] font-bold text-user-ink">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-[2px] border-user-ink soft-brutal-shadow-sm px-4 py-3 text-[12px] sm:text-[13px] font-bold text-user-ink">
            {{ session('error') }}
        </div>
    @endif

    {{-- Detail Book --}}
    <section class="grid grid-cols-1 lg:grid-cols-[310px_1fr] gap-5 sm:gap-6 items-start">

        {{-- Cover --}}
        <aside class="lg:sticky lg:top-[92px]">
            <div class="bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm p-3 sm:p-4">

                <div class="relative bg-white border-[2px] border-user-ink overflow-hidden">
                    @if($buku->cover)
                        <img alt="{{ $buku->judul }}"
                             class="w-full aspect-[3/4] object-cover"
                             src="{{ asset('storage/' . $buku->cover) }}">
                    @else
                        <div class="w-full aspect-[3/4] bg-user-blue-soft flex flex-col items-center justify-center text-user-ink/60">
                            <span class="material-symbols-outlined text-[62px]">menu_book</span>
                            <p class="mt-2 text-[12px] font-extrabold uppercase">
                                Tidak ada cover
                            </p>
                        </div>
                    @endif

                    <div class="absolute top-2 left-2 max-w-[80%]">
                        <span class="inline-block bg-user-gold text-user-ink border-[2px] border-user-ink text-[9px] sm:text-[10px] font-extrabold uppercase tracking-[0.1em] px-2 py-1 truncate max-w-full">
                            {{ $buku->kategori ?? 'Umum' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mt-3">
                    <div class="bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-3 text-center">
                        <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-[0.12em] text-user-ink/60">
                            Stok
                        </p>
                        <p class="text-[22px] sm:text-[26px] font-extrabold text-user-ink leading-none mt-1">
                            {{ $buku->stock ?? 0 }}
                        </p>
                    </div>

                    <div class="bg-user-gold/20 border-[2px] border-user-ink soft-brutal-shadow-sm p-3 text-center">
                        <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-[0.12em] text-user-ink/60">
                            Rak
                        </p>
                        <p class="text-[16px] sm:text-[20px] font-extrabold text-user-ink leading-none mt-1 truncate">
                            {{ $buku->lokasi ?? '-' }}
                        </p>
                    </div>
                </div>

            </div>
        </aside>

        {{-- Info --}}
        <div class="space-y-5">

            {{-- Header Info --}}
            <div class="relative overflow-hidden bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm p-4 sm:p-5 lg:p-6">
                <div class="absolute top-0 right-0 w-20 h-20 bg-user-gold border-l-[2px] border-b-[2px] border-user-ink"></div>

                <div class="relative z-10 space-y-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-user-gold border-[2px] border-user-ink soft-brutal-shadow-sm mb-3">
                                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">
                                    book_2
                                </span>
                                <span class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.16em] text-user-ink">
                                    Detail Buku
                                </span>
                            </div>

                            <h1 class="font-display-lg text-[24px] sm:text-[32px] lg:text-[40px] leading-[1.1] text-user-ink uppercase">
                                {{ $buku->judul }}
                            </h1>

                            <p class="mt-2 text-[12px] sm:text-[14px] text-user-ink/70 font-semibold">
                                Oleh
                                <span class="font-extrabold text-user-ink">
                                    {{ $buku->pengarang ?? '-' }}
                                </span>
                            </p>
                        </div>

                        <div class="hidden sm:flex gap-2 shrink-0">
                            <button type="button"
                                    class="w-10 h-10 bg-white border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover flex items-center justify-center"
                                    title="Simpan ke Favorit">
                                <span class="material-symbols-outlined text-[20px] text-user-ink">bookmark</span>
                            </button>

                            <button type="button"
                                    class="w-10 h-10 bg-white border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover flex items-center justify-center"
                                    title="Bagikan">
                                <span class="material-symbols-outlined text-[20px] text-user-ink">share</span>
                            </button>
                        </div>
                    </div>

                    {{-- Metadata --}}
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 pt-4 border-t-[2px] border-user-ink">
                        <div class="bg-white border-[2px] border-user-ink p-3">
                            <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-[0.12em] text-user-ink/55">
                                Penerbit
                            </p>
                            <p class="mt-1 text-[12px] sm:text-[13px] font-extrabold text-user-ink truncate">
                                {{ $buku->penerbit ?? '-' }}
                            </p>
                        </div>

                        <div class="bg-white border-[2px] border-user-ink p-3">
                            <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-[0.12em] text-user-ink/55">
                                Tahun
                            </p>
                            <p class="mt-1 text-[12px] sm:text-[13px] font-extrabold text-user-ink truncate">
                                {{ $buku->tahun_terbit ?? '-' }}
                            </p>
                        </div>

                        <div class="bg-white border-[2px] border-user-ink p-3">
                            <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-[0.12em] text-user-ink/55">
                                Kode Buku
                            </p>
                            <p class="mt-1 text-[12px] sm:text-[13px] font-extrabold text-user-ink truncate">
                                {{ $buku->kode ?? '-' }}
                            </p>
                        </div>

                        <div class="bg-white border-[2px] border-user-ink p-3">
                            <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-[0.12em] text-user-ink/55">
                                Status
                            </p>

                            @if(($buku->stock ?? 0) > 0)
                                <p class="mt-1 inline-flex items-center gap-2 text-[12px] sm:text-[13px] font-extrabold text-green-700 uppercase">
                                    <span class="w-2 h-2 bg-green-500 border border-user-ink"></span>
                                    Tersedia
                                </p>
                            @else
                                <p class="mt-1 inline-flex items-center gap-2 text-[12px] sm:text-[13px] font-extrabold text-red-700 uppercase">
                                    <span class="w-2 h-2 bg-red-500 border border-user-ink"></span>
                                    Habis
                                </p>
                            @endif
                        </div>

                        <div class="bg-white border-[2px] border-user-ink p-3">
                            <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-[0.12em] text-user-ink/55">
                                Stok
                            </p>
                            <p class="mt-1 text-[12px] sm:text-[13px] font-extrabold text-user-ink truncate">
                                {{ $buku->stock ?? 0 }} Eksemplar
                            </p>
                        </div>

                        <div class="bg-white border-[2px] border-user-ink p-3">
                            <p class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-[0.12em] text-user-ink/55">
                                Kategori
                            </p>
                            <p class="mt-1 text-[12px] sm:text-[13px] font-extrabold text-user-ink truncate">
                                {{ $buku->kategori ?? 'Umum' }}
                            </p>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="pt-4 border-t-[2px] border-user-ink">
                        <h2 class="font-headline-sm text-[16px] sm:text-[19px] uppercase text-user-ink mb-3">
                            Sinopsis & Deskripsi
                        </h2>

                        <div class="bg-white border-[2px] border-user-ink p-4">
                            <p class="text-[12px] sm:text-[14px] text-user-ink/75 font-semibold leading-relaxed">
                                {{ $buku->deskripsi ?: 'Belum ada deskripsi atau sinopsis untuk buku ini.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        @if(($buku->stock ?? 0) > 0)
                            <form action="{{ route('user.peminjaman.store', $buku->id_buku) }}" method="POST" class="w-full sm:w-auto">
                                @csrf

                                <input type="hidden" name="id_buku" value="{{ $buku->id_buku }}">

                                <button type="submit"
                                        onclick="return confirm('Yakin ingin meminjam buku {{ $buku->judul }}?')"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 bg-user-navy text-white border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover font-extrabold uppercase tracking-wide text-[11px] sm:text-[12px]">
                                    <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                                    Pinjam Buku
                                </button>
                            </form>
                        @else
                            <button disabled
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-200 text-user-ink/50 border-[2px] border-user-ink font-extrabold uppercase tracking-wide text-[11px] sm:text-[12px] cursor-not-allowed">
                                <span class="material-symbols-outlined text-[18px]">lock</span>
                                Stok Habis
                            </button>
                        @endif

                        <a href="{{ route('user.katalog') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 bg-user-paper text-user-ink border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover font-extrabold uppercase tracking-wide text-[11px] sm:text-[12px]">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Buku Terkait --}}
    <section class="space-y-4">
        <div class="flex items-end justify-between gap-3">
            <div class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm">
                <span class="material-symbols-outlined text-user-ink text-[19px]">recommend</span>
                <div>
                    <h2 class="font-headline-sm text-[15px] sm:text-[18px] uppercase text-user-ink">
                        Buku Terkait
                    </h2>
                    <p class="hidden sm:block text-[11px] text-user-ink/60 font-bold uppercase tracking-[0.1em] mt-0.5">
                        Kategori {{ $buku->kategori ?? 'Umum' }}
                    </p>
                </div>
            </div>

            <div class="flex gap-2 shrink-0">
                <button type="button"
                        class="w-9 h-9 bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover flex items-center justify-center"
                        id="prevBtn">
                    <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                </button>

                <button type="button"
                        class="w-9 h-9 bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover flex items-center justify-center"
                        id="nextBtn">
                    <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                </button>
            </div>
        </div>

        <div class="flex gap-3 sm:gap-4 overflow-x-auto hide-scrollbar pb-2 px-1" id="scrollContainer">
            @forelse($bukuTerkait ?? [] as $related)
                <a href="{{ route('user.katalog.show', $related->id_buku) }}"
                   class="min-w-[135px] sm:min-w-[160px] max-w-[135px] sm:max-w-[160px] bg-white border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover overflow-hidden group">

                    <div class="relative aspect-[3/4] overflow-hidden border-b-[2px] border-user-ink bg-user-paper">
                        @if($related->cover)
                            <img alt="{{ $related->judul }}"
                                 class="w-full h-full object-cover group-hover:scale-[1.04] transition duration-300"
                                 src="{{ asset('storage/' . $related->cover) }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-user-ink/60">
                                <span class="material-symbols-outlined text-[42px]">menu_book</span>
                            </div>
                        @endif

                        <div class="absolute top-2 left-2 max-w-[80%]">
                            <span class="inline-block bg-user-gold text-user-ink border-[2px] border-user-ink text-[8px] font-extrabold uppercase px-2 py-1 truncate max-w-full">
                                {{ $related->kategori ?? 'Umum' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-3">
                        <h3 class="text-[11px] sm:text-[12px] font-extrabold text-user-ink line-clamp-2 leading-snug min-h-[2rem]">
                            {{ $related->judul }}
                        </h3>

                        <p class="text-[10px] sm:text-[11px] text-user-ink/65 font-semibold line-clamp-1 mt-1">
                            {{ $related->pengarang ?? '-' }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="w-full bg-white border-[2px] border-user-ink soft-brutal-shadow-sm p-6 text-center">
                    <p class="text-user-ink font-extrabold uppercase tracking-wide text-sm">
                        Belum ada buku terkait.
                    </p>
                </div>
            @endforelse
        </div>
    </section>

</div>

@endsection

@push('scripts')
<script>
    const scrollContainer = document.getElementById('scrollContainer');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');

    if (scrollContainer && nextBtn && prevBtn) {
        nextBtn.addEventListener('click', function () {
            scrollContainer.scrollBy({
                left: 190,
                behavior: 'smooth'
            });
        });

        prevBtn.addEventListener('click', function () {
            scrollContainer.scrollBy({
                left: -190,
                behavior: 'smooth'
            });
        });
    }
</script>
@endpush
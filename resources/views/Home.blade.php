<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#111111',
                        paper: '#F7F1E5',
                        cream: '#FFF8E7',
                        navy: '#0B1F3A',
                        gold: '#D9A441',
                        clay: '#C96F4A',
                        mint: '#C8E6C9',
                        soft: '#F3E8D0'
                    },
                    fontFamily: {
                        headline: ['Montserrat', 'sans-serif'],
                        body: ['Inter', 'sans-serif']
                    },
                    boxShadow: {
                        brutal: '7px 7px 0px #111111',
                        brutalSm: '4px 4px 0px #111111'
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(217, 164, 65, .22), transparent 30%),
                radial-gradient(circle at top right, rgba(200, 230, 201, .35), transparent 28%),
                linear-gradient(135deg, #FFF8E7 0%, #F7F1E5 50%, #EFE7D3 100%);
            color: #111111;
        }

        .material-symbols-outlined {
            vertical-align: middle;
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
        }

        .nav-link.active {
            color: #111111;
            font-weight: 800;
            border-bottom: 3px solid #111111;
            padding-bottom: 5px;
        }

        .brutal-card {
            border: 3px solid #111111;
            box-shadow: 7px 7px 0px #111111;
        }

        .brutal-card-sm {
            border: 2px solid #111111;
            box-shadow: 4px 4px 0px #111111;
        }

        .brutal-btn {
            border: 2px solid #111111;
            box-shadow: 4px 4px 0px #111111;
            transition: .2s ease;
        }

        .brutal-btn:hover {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0px #111111;
        }

        .fade-up {
            opacity: 0;
            transform: translateY(28px) scale(.98);
            filter: blur(6px);
            transition:
                opacity .9s ease,
                transform .9s cubic-bezier(.16, 1, .3, 1),
                filter .9s ease;
            will-change: opacity, transform, filter;
        }

        .fade-up.show {
            opacity: 1;
            transform: translateY(0) scale(1);
            filter: blur(0);
        }

        .float-soft {
            animation: floatSoft 5s ease-in-out infinite;
        }

        .float-soft-delay {
            animation: floatSoft 6.5s ease-in-out infinite;
        }

        @keyframes floatSoft {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .hero-img {
            background-image:
                linear-gradient(to bottom, rgba(11, 31, 58, .05), rgba(11, 31, 58, .32)),
                url('https://images.unsplash.com/photo-1568667256549-094345857637?auto=format&fit=crop&w=1200&q=85');
            background-size: cover;
            background-position: center;
        }

        .mini-img-1 {
            background-image:
                linear-gradient(to bottom, rgba(11, 31, 58, .05), rgba(11, 31, 58, .28)),
                url('https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=900&q=85');
            background-size: cover;
            background-position: center;
        }

        .mini-img-2 {
            background-image:
                linear-gradient(to bottom, rgba(11, 31, 58, .05), rgba(11, 31, 58, .28)),
                url('https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&w=900&q=85');
            background-size: cover;
            background-position: center;
        }

        .mini-img-3 {
            background-image:
                linear-gradient(to bottom, rgba(11, 31, 58, .05), rgba(11, 31, 58, .28)),
                url('https://images.unsplash.com/photo-1495446815901-a7297e633e8d?auto=format&fit=crop&w=900&q=85');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body class="overflow-x-hidden">

<header class="fixed top-0 left-0 right-0 z-50 bg-cream/95 backdrop-blur border-b-[3px] border-ink">
    <div class="max-w-6xl mx-auto px-5 lg:px-8 h-[72px] flex items-center justify-between">
        <a href="#home" class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gold border-[3px] border-ink rounded-xl flex items-center justify-center shadow-brutalSm">
                <span class="material-symbols-outlined text-ink">menu_book</span>
            </div>
            <div>
                <h1 class="font-headline font-black text-base leading-none text-ink">Perpustakaan</h1>
                <p class="text-[10px] font-bold uppercase tracking-widest text-navy">Sistem Digital</p>
            </div>
        </a>

        <nav class="hidden md:flex items-center gap-7">
            <a href="#home" class="nav-link text-sm font-semibold text-ink/70 hover:text-ink transition">Home</a>
            <a href="#kategori" class="nav-link text-sm font-semibold text-ink/70 hover:text-ink transition">Kategori</a>
            <a href="#galeri" class="nav-link text-sm font-semibold text-ink/70 hover:text-ink transition">Galeri</a>
            <a href="#layanan" class="nav-link text-sm font-semibold text-ink/70 hover:text-ink transition">Layanan</a>
            <a href="#kontak" class="nav-link text-sm font-semibold text-ink/70 hover:text-ink transition">Kontak</a>
        </nav>

        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}"
               class="hidden sm:inline-flex px-4 py-2 rounded-xl bg-white text-ink text-sm font-bold brutal-btn">
                Login
            </a>

            <a href="{{ route('register') }}"
               class="inline-flex px-4 py-2 rounded-xl bg-navy text-white text-sm font-bold brutal-btn">
                Register
            </a>
        </div>
    </div>
</header>

<main class="pt-[72px]">

    <section id="home" class="relative py-12 lg:py-16 overflow-hidden">
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-gold/30 rounded-full blur-3xl"></div>
        <div class="absolute top-48 -left-20 w-72 h-72 bg-mint/45 rounded-full blur-3xl"></div>

        <div class="max-w-6xl mx-auto px-5 lg:px-8 grid lg:grid-cols-[1fr_.92fr] gap-10 items-center">
            <div class="fade-up">
                <div class="inline-flex items-center gap-2 bg-mint border-[2px] border-ink rounded-full px-4 py-2 shadow-brutalSm mb-5">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span class="text-[11px] font-black uppercase tracking-widest">Sistem Perpustakaan</span>
                </div>

                <h2 class="font-headline text-3xl sm:text-4xl lg:text-5xl font-black leading-[1.08] text-ink">
                    Temukan Buku dan Kelola Peminjaman dengan Lebih Mudah.
                </h2>

                <p class="mt-5 text-base text-ink/75 leading-relaxed max-w-xl">
                    Akses koleksi buku, cari referensi, dan ajukan peminjaman melalui sistem
                    perpustakaan yang sederhana, cepat, dan nyaman digunakan.
                </p>

                <form action="{{ route('user.katalog') }}" method="GET"
                      class="mt-7 max-w-xl brutal-card-sm rounded-2xl bg-white p-2 flex gap-2">
                    <div class="flex-1 flex items-center gap-3 px-3">
                        <span class="material-symbols-outlined text-ink/60">search</span>
                        <input
                            name="search"
                            type="text"
                            class="w-full border-0 focus:ring-0 bg-transparent text-sm text-ink placeholder:text-ink/45"
                            placeholder="Cari judul buku, pengarang, atau kategori..."
                        >
                    </div>

                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-gold text-ink text-sm font-black brutal-btn">
                        Cari
                    </button>
                </form>

                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ route('user.katalog') }}"
                       class="px-6 py-3 rounded-2xl bg-navy text-white text-sm font-black brutal-btn inline-flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">explore</span>
                        Jelajahi Katalog
                    </a>

                    <a href="{{ route('register') }}"
                       class="px-6 py-3 rounded-2xl bg-white text-ink text-sm font-black brutal-btn inline-flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">person_add</span>
                        Daftar Anggota
                    </a>
                </div>

                <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-white brutal-card-sm rounded-2xl p-4">
                        <h3 class="font-headline text-xl font-black">{{ number_format($totalBuku ?? 0, 0, ',', '.') }}</h3>
                        <p class="text-xs font-bold text-ink/60">Total Buku</p>
                    </div>

                    <div class="bg-gold brutal-card-sm rounded-2xl p-4">
                        <h3 class="font-headline text-xl font-black">{{ number_format($anggotaAktif ?? 0, 0, ',', '.') }}</h3>
                        <p class="text-xs font-bold text-ink/70">Anggota</p>
                    </div>

                    <div class="bg-white brutal-card-sm rounded-2xl p-4">
                        <h3 class="font-headline text-xl font-black">{{ number_format($bukuTersedia ?? 0, 0, ',', '.') }}</h3>
                        <p class="text-xs font-bold text-ink/60">Tersedia</p>
                    </div>

                    <div class="bg-mint brutal-card-sm rounded-2xl p-4">
                        <h3 class="font-headline text-xl font-black">{{ number_format($bukuDipinjam ?? 0, 0, ',', '.') }}</h3>
                        <p class="text-xs font-bold text-ink/70">Dipinjam</p>
                    </div>
                </div>
            </div>

            <div class="relative fade-up">
                <div class="absolute -top-5 -left-4 bg-gold brutal-card-sm rounded-2xl px-4 py-3 z-20 float-soft">
                    <p class="text-[10px] font-black uppercase tracking-widest">Cepat</p>
                    <p class="text-sm font-bold">Cari buku online</p>
                </div>

                <div class="absolute -bottom-5 -right-3 bg-mint brutal-card-sm rounded-2xl px-4 py-3 z-20 float-soft-delay">
                    <p class="text-[10px] font-black uppercase tracking-widest">Rapi</p>
                    <p class="text-sm font-bold">Riwayat tersimpan</p>
                </div>

                <div class="brutal-card rounded-[28px] bg-white p-4 rotate-1">
                    <div class="hero-img h-[350px] lg:h-[410px] rounded-[22px] border-[3px] border-ink relative overflow-hidden">
                        <div class="absolute inset-x-4 bottom-4 bg-white/92 backdrop-blur brutal-card-sm rounded-2xl p-4">
                            <p class="text-xs font-black uppercase tracking-widest text-clay">Perpustakaan</p>
                            <h3 class="font-headline text-xl font-black mt-1">
                                Pengalaman mencari buku yang lebih nyaman.
                            </h3>

                            <div class="mt-4 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-navy text-white border-[2px] border-ink flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[20px]">auto_stories</span>
                                </div>
                                <div>
                                    <p class="text-sm font-black">Peminjaman lebih praktis</p>
                                    <p class="text-xs text-ink/60 font-semibold">Cek status dan riwayat dengan mudah</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <section id="kategori" class="py-14">
        <div class="max-w-6xl mx-auto px-5 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5 mb-9 fade-up">
                <div>
                    <p class="font-black uppercase tracking-[.25em] text-clay text-xs">Kategori</p>
                    <h2 class="font-headline text-3xl lg:text-4xl font-black mt-2">Kategori Buku Pilihan</h2>
                    <p class="text-sm text-ink/65 mt-2">
                        Pilih kategori buku sesuai kebutuhan belajar, tugas, dan referensi akademikmu.
                    </p>
                </div>

                <a href="{{ route('user.katalog') }}"
                   class="w-fit px-5 py-3 rounded-xl bg-white text-sm font-black brutal-btn inline-flex items-center gap-2">
                    Lihat Katalog
                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                </a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                <a href="{{ route('user.katalog', ['kategori' => 'Teknologi']) }}"
                   class="group brutal-card-sm rounded-2xl bg-white p-5 hover:bg-navy hover:text-white transition fade-up">
                    <div class="w-11 h-11 rounded-xl border-[2px] border-ink bg-gold flex items-center justify-center mb-4 group-hover:bg-white">
                        <span class="material-symbols-outlined text-ink">memory</span>
                    </div>
                    <h3 class="font-headline text-lg font-black">Teknologi</h3>
                    <p class="mt-2 text-sm font-bold text-ink/55 group-hover:text-white/70">Buku pemrograman dan sistem</p>
                </a>

                <a href="{{ route('user.katalog', ['kategori' => 'Pendidikan']) }}"
                   class="group brutal-card-sm rounded-2xl bg-gold p-5 hover:bg-navy hover:text-white transition fade-up">
                    <div class="w-11 h-11 rounded-xl border-[2px] border-ink bg-white flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-ink">school</span>
                    </div>
                    <h3 class="font-headline text-lg font-black">Pendidikan</h3>
                    <p class="mt-2 text-sm font-bold text-ink/60 group-hover:text-white/70">Referensi akademik</p>
                </a>

                <a href="{{ route('user.katalog', ['kategori' => 'Ekonomi']) }}"
                   class="group brutal-card-sm rounded-2xl bg-white p-5 hover:bg-navy hover:text-white transition fade-up">
                    <div class="w-11 h-11 rounded-xl border-[2px] border-ink bg-mint flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-ink">monitoring</span>
                    </div>
                    <h3 class="font-headline text-lg font-black">Ekonomi</h3>
                    <p class="mt-2 text-sm font-bold text-ink/55 group-hover:text-white/70">Manajemen dan bisnis</p>
                </a>

                <a href="{{ route('user.katalog', ['kategori' => 'Umum']) }}"
                   class="group brutal-card-sm rounded-2xl bg-mint p-5 hover:bg-navy hover:text-white transition fade-up">
                    <div class="w-11 h-11 rounded-xl border-[2px] border-ink bg-white flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-ink">public</span>
                    </div>
                    <h3 class="font-headline text-lg font-black">Umum</h3>
                    <p class="mt-2 text-sm font-bold text-ink/60 group-hover:text-white/70">Koleksi bacaan umum</p>
                </a>
            </div>
        </div>
    </section>

    <section id="galeri" class="py-14 bg-ink text-white border-y-[3px] border-ink">
        <div class="max-w-6xl mx-auto px-5 lg:px-8">
            <div class="grid lg:grid-cols-[.8fr_1.2fr] gap-8 items-center">
                <div class="fade-up">
                    <p class="font-black uppercase tracking-[.25em] text-gold text-xs">Galeri</p>
                    <h2 class="font-headline text-3xl lg:text-4xl font-black mt-2">
                        Ruang Baca yang Nyaman dan Modern.
                    </h2>
                    <p class="text-sm text-white/65 mt-4 leading-relaxed">
                        Nikmati pengalaman mencari buku dengan tampilan yang bersih, visual yang nyaman,
                        dan akses informasi perpustakaan yang lebih mudah.
                    </p>
                </div>

                <div class="grid sm:grid-cols-3 gap-5">
                    <div class="mini-img-1 h-64 sm:h-80 rounded-[24px] brutal-card-sm fade-up relative overflow-hidden">
                        <div class="absolute bottom-3 left-3 right-3 bg-white/90 text-ink rounded-2xl border-[2px] border-ink p-3">
                            <p class="text-xs font-black">Ruang Baca</p>
                        </div>
                    </div>

                    <div class="mini-img-2 h-64 sm:h-80 rounded-[24px] brutal-card-sm fade-up relative overflow-hidden sm:mt-8">
                        <div class="absolute bottom-3 left-3 right-3 bg-gold text-ink rounded-2xl border-[2px] border-ink p-3">
                            <p class="text-xs font-black">Kegiatan Belajar</p>
                        </div>
                    </div>

                    <div class="mini-img-3 h-64 sm:h-80 rounded-[24px] brutal-card-sm fade-up relative overflow-hidden">
                        <div class="absolute bottom-3 left-3 right-3 bg-mint text-ink rounded-2xl border-[2px] border-ink p-3">
                            <p class="text-xs font-black">Koleksi Buku</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="layanan" class="py-14">
        <div class="max-w-6xl mx-auto px-5 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-10 fade-up">
                <p class="font-black uppercase tracking-[.25em] text-clay text-xs">Layanan</p>
                <h2 class="font-headline text-3xl lg:text-4xl font-black mt-2">Fitur Utama Sistem</h2>
                <p class="text-sm text-ink/65 mt-2">
                    Fitur dirancang agar peminjaman dan pengelolaan buku terasa lebih mudah.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="brutal-card-sm rounded-2xl bg-white p-5 fade-up">
                    <span class="material-symbols-outlined text-3xl">manage_search</span>
                    <h3 class="font-headline text-lg font-black mt-4">Pencarian Buku</h3>
                    <p class="text-sm text-ink/65 mt-2">Cari buku berdasarkan judul, pengarang, atau kategori.</p>
                </div>

                <div class="brutal-card-sm rounded-2xl bg-gold p-5 fade-up">
                    <span class="material-symbols-outlined text-3xl">book_online</span>
                    <h3 class="font-headline text-lg font-black mt-4">Peminjaman</h3>
                    <p class="text-sm text-ink/70 mt-2">Ajukan peminjaman buku secara online.</p>
                </div>

                <div class="brutal-card-sm rounded-2xl bg-white p-5 fade-up">
                    <span class="material-symbols-outlined text-3xl">history</span>
                    <h3 class="font-headline text-lg font-black mt-4">Riwayat</h3>
                    <p class="text-sm text-ink/65 mt-2">Pantau daftar peminjaman dan pengembalian.</p>
                </div>

                <div class="brutal-card-sm rounded-2xl bg-mint p-5 fade-up">
                    <span class="material-symbols-outlined text-3xl">qr_code_scanner</span>
                    <h3 class="font-headline text-lg font-black mt-4">Bukti Pinjam</h3>
                    <p class="text-sm text-ink/70 mt-2">Cetak bukti peminjaman untuk ditunjukkan ke petugas.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12">
        <div class="max-w-6xl mx-auto px-5 lg:px-8">
            <div class="brutal-card rounded-[28px] bg-navy text-white p-7 lg:p-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-7 fade-up">
                <div>
                    <h2 class="font-headline text-2xl lg:text-3xl font-black">Mulai gunakan sistem perpustakaan.</h2>
                    <p class="text-sm text-white/70 mt-3 max-w-2xl">
                        Daftar sebagai anggota untuk mengakses fitur peminjaman, bukti pinjam, dan riwayat buku.
                    </p>
                </div>

                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('register') }}" class="px-6 py-3 rounded-2xl bg-gold text-ink text-sm font-black brutal-btn">
                        Daftar Sekarang
                    </a>
                    <a href="{{ route('login') }}" class="px-6 py-3 rounded-2xl bg-white text-ink text-sm font-black brutal-btn">
                        Masuk Akun
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<footer id="kontak" class="bg-ink text-white border-t-[3px] border-ink">
    <div class="max-w-6xl mx-auto px-5 lg:px-8 py-10 grid md:grid-cols-4 gap-8">
        <div>
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-gold border-[2px] border-white rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-ink">menu_book</span>
                </div>
                <h3 class="font-headline font-black text-lg">Perpustakaan</h3>
            </div>
            <p class="text-white/60 text-sm leading-relaxed">
                Sistem manajemen perpustakaan berbasis web untuk mendukung aktivitas akademik.
            </p>
        </div>

        <div>
            <h4 class="font-black mb-4 uppercase tracking-widest text-sm">Menu</h4>
            <ul class="space-y-3 text-white/65 text-sm">
                <li><a href="#home" class="hover:text-gold">Home</a></li>
                <li><a href="#kategori" class="hover:text-gold">Kategori</a></li>
                <li><a href="#galeri" class="hover:text-gold">Galeri</a></li>
                <li><a href="#layanan" class="hover:text-gold">Layanan</a></li>
            </ul>
        </div>

        <div>
            <h4 class="font-black mb-4 uppercase tracking-widest text-sm">Akses</h4>
            <ul class="space-y-3 text-white/65 text-sm">
                <li><a href="{{ route('user.katalog') }}" class="hover:text-gold">Katalog Buku</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-gold">Login</a></li>
                <li><a href="{{ route('register') }}" class="hover:text-gold">Register</a></li>
            </ul>
        </div>

        <div>
            <h4 class="font-black mb-4 uppercase tracking-widest text-sm">Kontak</h4>
            <div class="space-y-3 text-white/65 text-sm">
                <p class="flex gap-2">
                    <span class="material-symbols-outlined text-gold text-[18px]">location_on</span>
                    Jl. Sumatera No.101, GKB, Gresik
                </p>
                <p class="flex gap-2">
                    <span class="material-symbols-outlined text-gold text-[18px]">call</span>
                    (031) 3951414
                </p>
                <p class="flex gap-2">
                    <span class="material-symbols-outlined text-gold text-[18px]">mail</span>
                    perpustakaan@example.com
                </p>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10 py-5 text-center text-white/45 text-sm">
        © {{ date('Y') }} Perpustakaan. All rights reserved.
    </div>
</footer>

<script>
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('section[id], footer[id]');

    function setActiveLink(id) {
        navLinks.forEach(link => {
            link.classList.toggle('active', link.getAttribute('href') === '#' + id);
        });
    }

    navLinks.forEach(link => {
        link.addEventListener('click', function () {
            const id = this.getAttribute('href').replace('#', '');
            setActiveLink(id);
        });
    });

    window.addEventListener('scroll', () => {
        let current = 'home';

        sections.forEach(section => {
            const sectionTop = section.offsetTop - 120;
            if (window.scrollY >= sectionTop) {
                current = section.getAttribute('id');
            }
        });

        setActiveLink(current);
    });

    setActiveLink('home');

    const fadeItems = document.querySelectorAll('.fade-up');

    const fadeObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('show');
                }, index * 90);
            }
        });
    }, {
        threshold: 0.18,
        rootMargin: '0px 0px -40px 0px'
    });

    fadeItems.forEach(item => fadeObserver.observe(item));
</script>

</body>
</html>
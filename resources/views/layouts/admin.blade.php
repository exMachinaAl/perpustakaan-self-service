<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>@yield('title', 'Perpustakaan Admin')</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <style>
        html,
        body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden !important;
            background-color: #F6F3EE;
        }

        * {
            box-sizing: border-box;
        }

        img,
        table,
        section,
        div,
        main,
        header,
        aside {
            max-width: 100%;
        }

        html,
body {
    width: 100%;
    max-width: 100%;
    overflow-x: hidden !important;
    background-color: #F6F3EE;

    scrollbar-width: none;
    -ms-overflow-style: none;
}

html::-webkit-scrollbar,
body::-webkit-scrollbar,
main::-webkit-scrollbar,
section::-webkit-scrollbar,
div::-webkit-scrollbar,
aside::-webkit-scrollbar,
nav::-webkit-scrollbar {
    display: none;
    width: 0;
    height: 0;
}

aside,
nav,
main,
section,
div {
    scrollbar-width: none;
    -ms-overflow-style: none;
}

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
        }

        .brutal-shadow {
            box-shadow: 6px 6px 0 #1B1B1F;
        }

        .brutal-shadow-sm {
            box-shadow: 4px 4px 0 #1B1B1F;
        }

        .brutal-hover {
            transition: all 0.15s ease;
        }

        .brutal-hover:hover {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0 #1B1B1F;
        }

    </style>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "brutal-bg": "#F6F3EE",
                        "brutal-cream": "#F6F3EE",
                        "brutal-gold": "#C9A227",
                        "brutal-navy": "#1E3A5F",
                        "brutal-green": "#4F6F52",
                        "brutal-rose": "#A65F6A",
                        "brutal-black": "#1B1B1F",
                        "brutal-white": "#FFFFFF",

                        "error-container": "#F8D7DA",
                        "error": "#B42318",

                        "on-surface": "#1B1B1F",
                        "on-surface-variant": "#4B5563",
                        "background": "#F6F3EE",
                        "surface": "#F6F3EE",
                        "surface-container-low": "#FFFFFF",
                        "surface-container-lowest": "#FFFFFF",
                        "outline": "#1B1B1F",
                        "outline-variant": "#1B1B1F",

                        "primary": "#1B1B1F",
                        "primary-container": "#1E3A5F",
                        "primary-fixed": "#C9A227",
                        "primary-fixed-dim": "#B89120",
                        "on-primary": "#FFFFFF",
                        "on-primary-container": "#FFFFFF",

                        "tertiary": "#C9A227",
                        "tertiary-fixed": "#C9A227",
                        "tertiary-fixed-dim": "#B89120",

                        "secondary": "#4B5563"
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px"
                    },
                    spacing: {
                        header_height: "72px",
                        base_unit: "8px",
                        container_padding: "1.25rem",
                        gutter: "1rem",
                        sidebar_width: "250px"
                    },
                    fontFamily: {
                        "body-md": ["Inter"],
                        "label-md": ["Inter"],
                        "body-sm": ["Inter"],
                        "label-sm": ["Inter"],
                        "body-lg": ["Inter"],
                        "headline-lg": ["Montserrat"],
                        "headline-md": ["Montserrat"],
                        "display-lg": ["Montserrat"],
                        "headline-sm": ["Montserrat"]
                    },
                    fontSize: {
                        "body-md": ["16px", {"lineHeight": "1.5", "fontWeight": "500"}],
                        "label-md": ["14px", {"lineHeight": "1", "letterSpacing": "0.01em", "fontWeight": "700"}],
                        "body-sm": ["14px", {"lineHeight": "1.5", "fontWeight": "500"}],
                        "label-sm": ["12px", {"lineHeight": "1", "fontWeight": "700"}],
                        "body-lg": ["18px", {"lineHeight": "1.6", "fontWeight": "500"}],
                        "headline-lg": ["32px", {"lineHeight": "1.3", "fontWeight": "800"}],
                        "headline-md": ["24px", {"lineHeight": "1.4", "fontWeight": "800"}],
                        "display-lg": ["48px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "800"}],
                        "headline-sm": ["20px", {"lineHeight": "1.4", "fontWeight": "800"}]
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>

<body class="bg-brutal-bg font-body-md text-brutal-black min-h-screen">

<div id="sidebarOverlay"
     onclick="toggleSidebar()"
     class="hidden fixed inset-0 bg-black/40 z-40 lg:hidden"></div>

<aside id="mobileSidebar"
       class="fixed left-0 top-0 h-full w-sidebar_width flex flex-col z-50 bg-brutal-navy border-r-[3px] border-brutal-black transform -translate-x-full lg:translate-x-0 transition-transform duration-300">

    <div class="relative p-gutter flex flex-col items-center border-b-[3px] border-brutal-black bg-brutal-navy">
        <button onclick="toggleSidebar()"
                class="absolute top-4 right-4 lg:hidden w-9 h-9 bg-white border-[3px] border-brutal-black brutal-shadow-sm brutal-hover flex items-center justify-center">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>

        <div class="w-16 h-16 bg-white border-[3px] border-brutal-black brutal-shadow-sm flex items-center justify-center mb-4">
            <span class="material-symbols-outlined text-4xl text-brutal-navy" style="font-variation-settings: 'FILL' 1;">auto_stories</span>
        </div>

        <h1 class="font-headline-sm text-headline-sm font-extrabold text-white uppercase tracking-tight">
            Perpustakaan
        </h1>

        <p class="font-label-sm text-label-sm text-white/70 tracking-wider mt-1">
            ADMIN PANEL
        </p>
    </div>

    <nav class="mt-5 flex-1 space-y-2 px-3 overflow-y-auto">

        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-4 transition-all duration-150 py-3 px-4 border-[3px] border-brutal-black brutal-hover
           {{ request()->routeIs('admin.dashboard')
                ? 'bg-brutal-gold text-brutal-black brutal-shadow-sm'
                : 'bg-white text-brutal-black hover:bg-brutal-cream brutal-shadow-sm' }}">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="font-label-md text-label-md">Dashboard</span>
        </a>

        <a href="{{ route('admin.databuku.index') }}"
           class="flex items-center gap-4 transition-all duration-150 py-3 px-4 border-[3px] border-brutal-black brutal-hover
           {{ request()->routeIs('admin.databuku.*')
                ? 'bg-brutal-gold text-brutal-black brutal-shadow-sm'
                : 'bg-white text-brutal-black hover:bg-brutal-cream brutal-shadow-sm' }}">
            <span class="material-symbols-outlined">menu_book</span>
            <span class="font-label-md text-label-md">Data Buku</span>
        </a>

        <a href="{{ route('admin.peminjaman.index') }}"
           class="flex items-center gap-4 transition-all duration-150 py-3 px-4 border-[3px] border-brutal-black brutal-hover
           {{ request()->routeIs('admin.peminjaman.*')
                ? 'bg-brutal-gold text-brutal-black brutal-shadow-sm'
                : 'bg-white text-brutal-black hover:bg-brutal-cream brutal-shadow-sm' }}">
            <span class="material-symbols-outlined">calendar_today</span>
            <span class="font-label-md text-label-md">Peminjaman</span>
        </a>

        <a href="{{ route('admin.verifikasi.index') }}"
           class="flex items-center gap-4 transition-all duration-150 py-3 px-4 border-[3px] border-brutal-black brutal-hover
           {{ request()->routeIs('admin.verifikasi.*')
                ? 'bg-brutal-gold text-brutal-black brutal-shadow-sm'
                : 'bg-white text-brutal-black hover:bg-brutal-cream brutal-shadow-sm' }}">
            <span class="material-symbols-outlined">fact_check</span>
            <span class="font-label-md text-label-md">Verifikasi</span>
        </a>

        <a href="{{ route('admin.scan.index') }}"
             class="flex items-center gap-4 transition-all duration-150 py-3 px-4 border-[3px] border-brutal-black brutal-hover
            {{ request()->routeIs('admin.scan.*')
                    ? 'bg-brutal-gold text-brutal-black brutal-shadow-sm'
                    : 'bg-white text-brutal-black hover:bg-brutal-cream brutal-shadow-sm' }}">
            <span class="material-symbols-outlined">qr_code_scanner</span>
            <span class="font-label-md text-label-md">Scan QR</span>
        </a>

        <a href="{{ route('admin.kiosk.hub') }}"
           class="flex items-center gap-4 transition-all duration-150 py-3 px-4 border-[3px] border-brutal-black brutal-hover {{ request()->routeIs('admin.kiosk.*') ? 'bg-brutal-gold text-brutal-black brutal-shadow-sm' : 'bg-white text-brutal-black hover:bg-brutal-cream brutal-shadow-sm' }}">
            <span class="material-symbols-outlined">qr_code_scanner</span>
            <span class="font-label-md text-label-md">Kiosk QR LAB</span>
        </a>

        <a href="{{ route('admin.rfid.index') }}"
           class="flex items-center gap-4 transition-all duration-150 py-3 px-4 border-[3px] border-brutal-black brutal-hover
           {{ request()->routeIs('admin.rfid.*') ? 'bg-brutal-gold text-brutal-black brutal-shadow-sm' : 'bg-white text-brutal-black hover:bg-brutal-cream brutal-shadow-sm' }}">
            <span class="material-symbols-outlined">contactless</span>
            <span class="font-label-md text-label-md">Kartu RFID</span>
        </a>
        <a href="{{ route('admin.rfid.desk') }}" class="flex items-center gap-3 px-4 py-3 border-[3px] border-brutal-black mb-3 bg-white text-brutal-black hover:bg-brutal-cream brutal-shadow-sm">
            <span class="material-symbols-outlined">sensors</span>
            <span class="font-label-md text-label-md">Monitor RFID</span>
        </a>

        <a href="{{ route('admin.anggota.index') }}"
           class="flex items-center gap-4 transition-all duration-150 py-3 px-4 border-[3px] border-brutal-black brutal-hover
           {{ request()->routeIs('admin.anggota.*')
                ? 'bg-brutal-gold text-brutal-black brutal-shadow-sm'
                : 'bg-white text-brutal-black hover:bg-brutal-cream brutal-shadow-sm' }}">
            <span class="material-symbols-outlined">group</span>
            <span class="font-label-md text-label-md">Anggota</span>
        </a>

        <a href="{{ route('admin.program-studi.index') }}"
           class="flex items-center gap-4 transition-all duration-150 py-3 px-4 border-[3px] border-brutal-black brutal-hover
           {{ request()->routeIs('admin.program-studi.*')
                ? 'bg-brutal-gold text-brutal-black brutal-shadow-sm'
                : 'bg-white text-brutal-black hover:bg-brutal-cream brutal-shadow-sm' }}">
            <span class="material-symbols-outlined">school</span>
            <span class="font-label-md text-label-md">Program Studi</span>
        </a>

    </nav>

    <div class="mt-auto p-4 border-t-[3px] border-brutal-black bg-brutal-navy">
        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit"
                    class="flex items-center gap-4 bg-white text-brutal-black hover:bg-error-container px-4 py-3 border-[3px] border-brutal-black brutal-shadow-sm brutal-hover transition-all duration-150 w-full font-bold">
                <span class="material-symbols-outlined">logout</span>
                <span class="font-label-md text-label-md">Logout</span>
            </button>
        </form>
    </div>

</aside>

<main class="ml-0 lg:ml-sidebar_width min-h-screen flex flex-col">

    <header class="flex justify-between items-center w-full px-4 lg:px-container_padding h-header_height bg-brutal-bg border-b-[3px] border-brutal-black z-30 sticky top-0">

        <div class="flex items-center gap-3 w-full min-w-0">
            <button onclick="toggleSidebar()"
                    class="lg:hidden w-11 h-11 bg-white border-[3px] border-brutal-black brutal-shadow-sm brutal-hover flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">menu</span>
            </button>

            <form action="@yield('search_action', '#')" method="GET" class="relative w-full max-w-md hidden sm:block">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-brutal-black">
                    search
                </span>

                <input
                    class="w-full bg-white border-[3px] border-brutal-black rounded-none pl-10 pr-4 py-2 text-body-sm font-semibold focus:ring-0 focus:outline-none brutal-shadow-sm"
                    placeholder="@yield('search_placeholder', 'Cari data perpustakaan...')"
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                />
            </form>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <button class="relative bg-white text-brutal-black border-[3px] border-brutal-black w-11 h-11 brutal-shadow-sm brutal-hover transition-all flex items-center justify-center">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute -top-1 -right-1 w-4 h-4 bg-error rounded-full border-[3px] border-brutal-black"></span>
            </button>

            <div class="flex items-center gap-3 bg-white border-[3px] border-brutal-black brutal-shadow-sm px-3 py-2">
                <div class="text-right hidden md:block">
                    <p class="font-label-md text-label-md text-brutal-black uppercase">
                        {{ Auth::user()->nama ?? Auth::user()->name ?? 'Admin' }}
                    </p>
                    <p class="font-label-sm text-label-sm text-brutal-black/60 uppercase">
                        {{ Auth::user()->role ?? 'Admin' }}
                    </p>
                </div>

                <div class="w-10 h-10 border-[3px] border-brutal-black bg-brutal-gold text-brutal-black flex items-center justify-center font-extrabold text-lg">
                    {{ strtoupper(substr(Auth::user()->nama ?? Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
            </div>
        </div>

    </header>

    <div class="sm:hidden bg-brutal-bg border-b-[3px] border-brutal-black px-4 py-3">
        <form action="@yield('search_action', '#')" method="GET" class="relative w-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-brutal-black">
                search
            </span>

            <input
                class="w-full bg-white border-[3px] border-brutal-black rounded-none pl-10 pr-4 py-2 text-body-sm font-semibold focus:ring-0 focus:outline-none brutal-shadow-sm"
                placeholder="@yield('search_placeholder', 'Cari data...')"
                type="text"
                name="search"
                value="{{ request('search') }}"
            />
        </form>
    </div>

    <div class="flex-1 px-4 lg:px-6 py-5">
        <div class="max-w-6xl mx-auto w-full">
            @yield('content')
        </div>
    </div>

    <footer class="mt-auto w-full py-5 px-4 lg:px-container_padding flex flex-col md:flex-row justify-between items-center gap-4 bg-white border-t-[3px] border-brutal-black">
        <p class="font-body-sm text-body-sm text-brutal-black font-bold">
            © 2024 Perpustakaan. All rights reserved.
        </p>

        <div class="flex flex-wrap justify-center gap-3 md:gap-5">
            <a class="font-label-sm text-label-sm text-white bg-brutal-navy border-[3px] border-brutal-black px-3 py-2 brutal-shadow-sm brutal-hover transition-all" href="#">
                Kontak
            </a>
            <a class="font-label-sm text-label-sm text-white bg-brutal-navy border-[3px] border-brutal-black px-3 py-2 brutal-shadow-sm brutal-hover transition-all" href="#">
                Layanan
            </a>
            <a class="font-label-sm text-label-sm text-white bg-brutal-navy border-[3px] border-brutal-black px-3 py-2 brutal-shadow-sm brutal-hover transition-all" href="#">
                Privasi
            </a>
        </div>
    </footer>

</main>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('mobileSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

     const scrollKey = 'userScrollPosition';

    // Hilangkan animasi scroll bawaan agar tidak terlihat loncat saat reload
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    document.documentElement.style.scrollBehavior = 'auto';

    // Sembunyikan halaman sebentar saat load agar posisi scroll tidak terlihat loncat
    document.body.classList.add('page-loading');

    // Simpan posisi scroll sebelum reload / pindah halaman
    window.addEventListener('beforeunload', function () {
        sessionStorage.setItem(scrollKey, window.scrollY);
    });

    // Kembalikan posisi scroll setelah halaman siap
    window.addEventListener('DOMContentLoaded', function () {
        const scrollPosition = sessionStorage.getItem(scrollKey);

        if (scrollPosition !== null) {
            window.scrollTo(0, parseInt(scrollPosition));
        }

        requestAnimationFrame(function () {
            document.body.classList.remove('page-loading');
        });
    });
</script>

@stack('scripts')

</body>
</html>
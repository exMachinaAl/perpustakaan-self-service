<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>

    <title>@yield('title', 'Perpustakaan - Student Portal')</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "user-bg": "#F5F1E8",
                        "user-paper": "#FFFAF0",
                        "user-white": "#FFFFFF",

                        "user-ink": "#14213D",
                        "user-navy": "#13233A",
                        "user-navy-2": "#1E3A5F",
                        "user-blue": "#27496D",
                        "user-blue-soft": "#DCE7F3",

                        "user-gold": "#C9A227",
                        "user-gold-dark": "#A8841E",
                        "user-bone": "#EDE3CF",
                        "user-maroon": "#A65F6A",

                        "primary-container": "#1E3A5F",
                        "primary": "#14213D",
                        "tertiary-fixed": "#C9A227",
                        "tertiary-fixed-dim": "#A8841E",
                        "surface": "#F5F1E8",
                        "surface-container-low": "#FFFAF0",
                        "surface-container-lowest": "#FFFFFF",
                        "surface-container": "#FFFAF0",
                        "outline-variant": "#14213D",
                        "on-primary": "#FFFFFF",
                        "on-surface": "#14213D",
                        "on-surface-variant": "#4B5563",
                        "on-tertiary-fixed": "#14213D",
                        "background": "#F5F1E8",
                        "error": "#B42318",
                        "primary-fixed": "#C9A227",
                        "primary-fixed-dim": "#A8841E",
                        "secondary-fixed": "#FFFFFF",
                        "secondary": "#4B5563",
                        "tertiary": "#C9A227",
                        "on-primary-container": "#FFFFFF",
                        "error-container": "#F8D7DA"
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px"
                    },
                    spacing: {
                        container_padding: "1.25rem",
                        gutter: "1rem",
                        header_height: "72px"
                    },
                    fontFamily: {
                        "headline-lg": ["Montserrat"],
                        "label-sm": ["Inter"],
                        "body-sm": ["Inter"],
                        "body-md": ["Inter"],
                        "headline-md": ["Montserrat"],
                        "headline-sm": ["Montserrat"],
                        "label-md": ["Inter"],
                        "body-lg": ["Inter"],
                        "display-lg": ["Montserrat"]
                    },
                    fontSize: {
                        "headline-lg": ["32px", {"lineHeight": "1.3", "fontWeight": "800"}],
                        "label-sm": ["12px", {"lineHeight": "1", "fontWeight": "700"}],
                        "body-sm": ["14px", {"lineHeight": "1.5", "fontWeight": "500"}],
                        "body-md": ["16px", {"lineHeight": "1.5", "fontWeight": "500"}],
                        "headline-md": ["24px", {"lineHeight": "1.4", "fontWeight": "800"}],
                        "headline-sm": ["20px", {"lineHeight": "1.4", "fontWeight": "800"}],
                        "label-md": ["14px", {"lineHeight": "1", "letterSpacing": "0.01em", "fontWeight": "700"}],
                        "body-lg": ["18px", {"lineHeight": "1.6", "fontWeight": "500"}],
                        "display-lg": ["48px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "800"}]
                    }
                },
            },
        }
    </script>

    <style>
        html,
        body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden !important;
            background:
                radial-gradient(circle at top left, rgba(201, 162, 39, 0.12), transparent 28%),
                radial-gradient(circle at bottom right, rgba(30, 58, 95, 0.12), transparent 32%),
                #F5F1E8;

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

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
        }

        .user-sidebar-pattern {
            background:
                linear-gradient(135deg, rgba(201, 162, 39, 0.13), transparent 28%),
                linear-gradient(145deg, #13233A 0%, #1E3A5F 55%, #27496D 100%);
            position: relative;
        }

        .user-sidebar-pattern::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.045) 1px, transparent 1px);
            background-size: 34px 34px;
            opacity: 0.6;
            pointer-events: none;
        }

        .user-sidebar-pattern > * {
            position: relative;
            z-index: 1;
        }

        .soft-brutal-shadow {
            box-shadow: 4px 4px 0 #14213D;
        }

        .soft-brutal-shadow-sm {
            box-shadow: 3px 3px 0 #14213D;
        }

        .soft-brutal-hover {
            transition:
                transform .18s ease,
                box-shadow .18s ease,
                background-color .18s ease,
                color .18s ease;
        }

        .soft-brutal-hover:hover {
            transform: translate(1px, 1px);
            box-shadow: 2px 2px 0 #14213D;
        }

        aside,
        nav,
        main,
        section,
        div {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .page-shell {
            animation: contentFade .22s ease-out both;
        }

        @keyframes contentFade {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    @stack('styles')
</head>

<body class="font-body-md text-user-ink bg-user-bg min-h-screen overflow-x-hidden">

    {{-- Overlay Mobile --}}
    <div id="sidebarOverlay"
         onclick="toggleSidebar()"
         class="hidden fixed inset-0 bg-black/40 z-40 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside id="mobileSidebar"
           class="fixed left-0 top-0 z-50 w-[246px] h-screen user-sidebar-pattern border-r-[2px] border-user-ink flex flex-col overflow-hidden transform -translate-x-full lg:translate-x-0 transition-transform duration-300">

        {{-- Logo --}}
        <div class="relative px-5 py-7 mb-4 border-b-[2px] border-user-gold/70 flex flex-col items-center text-center">

            <div class="w-16 h-16 bg-user-paper border-[2px] border-user-gold soft-brutal-shadow-sm flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[38px] text-user-ink" style="font-variation-settings: 'FILL' 1;">
                    local_library
                </span>
            </div>

            <h1 class="font-headline-md text-[22px] font-extrabold text-white leading-tight uppercase text-center">
                Perpustakaan
            </h1>

            <p class="font-label-md text-[11px] text-user-gold mt-2 uppercase tracking-[0.18em] text-center">
                Student Portal
            </p>

            <button onclick="toggleSidebar()"
                    class="absolute top-4 right-4 lg:hidden w-9 h-9 bg-user-paper text-user-ink border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover flex items-center justify-center">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 space-y-2 overflow-y-auto px-3">

            <a href="{{ route('user.dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 font-label-md border-[2px] border-user-ink soft-brutal-hover transition-all
               {{ request()->routeIs('user.dashboard')
                    ? 'bg-user-gold text-user-ink soft-brutal-shadow-sm'
                    : 'bg-user-paper text-user-ink hover:bg-user-blue-soft soft-brutal-shadow-sm' }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('user.katalog') }}"
               class="flex items-center gap-3 px-4 py-3 font-label-md border-[2px] border-user-ink soft-brutal-hover transition-all
               {{ request()->routeIs('user.katalog*')
                    ? 'bg-user-gold text-user-ink soft-brutal-shadow-sm'
                    : 'bg-user-paper text-user-ink hover:bg-user-blue-soft soft-brutal-shadow-sm' }}">
                <span class="material-symbols-outlined">menu_book</span>
                <span>Data Buku</span>
            </a>

            <a href="{{ route('user.kiosk.pass') }}"
               class="flex items-center gap-3 px-4 py-3 font-label-md border-[2px] border-user-ink soft-brutal-hover transition-all duration-150 bg-user-paper text-user-ink hover:bg-user-blue-soft soft-brutal-shadow-sm">
                <span class="material-symbols-outlined">qr_code_2</span>
                <span>QR Anggota</span>
            </a>

            <a href="{{ route('user.peminjaman.cek') }}"
               class="flex items-center gap-3 px-4 py-3 font-label-md border-[2px] border-user-ink soft-brutal-hover transition-all
               {{ request()->routeIs('user.peminjaman.cek') || request()->routeIs('peminjaman.cek')
                    ? 'bg-user-gold text-user-ink soft-brutal-shadow-sm'
                    : 'bg-user-paper text-user-ink hover:bg-user-blue-soft soft-brutal-shadow-sm' }}">
                <span class="material-symbols-outlined">pending_actions</span>
                <span>Status Peminjaman</span>
            </a>

            <a href="{{ route('user.riwayat') }}"
               class="flex items-center gap-3 px-4 py-3 font-label-md border-[2px] border-user-ink soft-brutal-hover transition-all
               {{ request()->routeIs('user.riwayat*')
                    ? 'bg-user-gold text-user-ink soft-brutal-shadow-sm'
                    : 'bg-user-paper text-user-ink hover:bg-user-blue-soft soft-brutal-shadow-sm' }}">
                <span class="material-symbols-outlined">history</span>
                <span>Riwayat Peminjaman</span>
            </a>

            <a href="{{ route('user.profile.edit') }}"
               class="flex items-center gap-3 px-4 py-3 font-label-md border-[2px] border-user-ink soft-brutal-hover transition-all
               {{ request()->routeIs('profile.*')
                    ? 'bg-user-gold text-user-ink soft-brutal-shadow-sm'
                    : 'bg-user-paper text-user-ink hover:bg-user-blue-soft soft-brutal-shadow-sm' }}">
                <span class="material-symbols-outlined">account_circle</span>
                <span>Profile</span>
            </a>

        </nav>

        {{-- Logout --}}
        <div class="px-4 py-4 mt-auto border-t-[2px] border-user-gold/70">
            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit"
                        class="flex items-center gap-3 px-4 py-3 w-full bg-user-paper text-user-ink border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover font-label-md hover:bg-red-100 transition-all">
                    <span class="material-symbols-outlined">logout</span>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Topbar --}}
    <header class="fixed top-0 left-0 lg:left-[246px] right-0 z-30 h-[72px] bg-user-bg/95 backdrop-blur border-b-[2px] border-user-ink overflow-hidden">
        <div class="h-full px-4 lg:px-container_padding flex items-center justify-between gap-3 overflow-hidden">

            <button onclick="toggleSidebar()"
                    class="lg:hidden w-11 h-11 bg-user-paper text-user-ink border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined">menu</span>
            </button>

            <div class="hidden sm:flex items-center w-full max-w-xl min-w-0">
                <form action="@yield('search_action', route('user.katalog'))" method="GET" class="relative w-full">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-user-ink">
                        search
                    </span>

                    <input
                        class="w-full bg-white border-[2px] border-user-ink rounded-none pl-10 pr-4 py-2 text-body-sm font-semibold focus:ring-0 focus:outline-none focus:shadow-[0_0_0_3px_rgba(201,162,39,0.28)] soft-brutal-shadow-sm"
                        placeholder="@yield('search_placeholder', 'Cari judul buku, penulis, atau ISBN...')"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                    />
                </form>
            </div>

            <div class="flex items-center gap-2 sm:gap-4 shrink-0 ml-auto">

                <button class="relative w-11 h-11 bg-user-paper text-user-ink border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover flex items-center justify-center transition-all">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-user-maroon rounded-full border-[2px] border-user-ink"></span>
                </button>

                <a href="{{ route('user.profile.edit') }}"
                   class="hidden sm:flex w-11 h-11 bg-user-paper text-user-ink border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover items-center justify-center transition-all"
                   title="Pengaturan Profile">
                    <span class="material-symbols-outlined">settings</span>
                </a>

                <a href="{{ route('user.profile.edit') }}"
                   class="hidden md:flex items-center gap-3 bg-user-paper border-[2px] border-user-ink soft-brutal-shadow-sm soft-brutal-hover px-3 py-2 transition-all">

                    <div class="text-right">
                        <p class="font-label-md text-user-ink leading-none whitespace-nowrap uppercase">
                            {{ Auth::user()->nama ?? Auth::user()->name ?? 'Mahasiswa' }}
                        </p>

                        <p class="text-[12px] text-user-ink/60 mt-1 whitespace-nowrap uppercase font-bold">
                            {{ Auth::user()->prodi->nama ?? Auth::user()->role ?? 'Mahasiswa' }}
                        </p>
                    </div>

                    <div class="w-10 h-10 border-[2px] border-user-ink bg-user-gold text-user-ink flex items-center justify-center font-extrabold shrink-0 overflow-hidden">
                        @if(Auth::user()->foto_profile)
                            <img src="{{ asset('storage/' . Auth::user()->foto_profile) }}"
                                 alt="Foto Profile"
                                 class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr(Auth::user()->nama ?? Auth::user()->name ?? 'U', 0, 1)) }}
                        @endif
                    </div>
                </a>

                <a href="{{ route('user.profile.edit') }}"
                   class="md:hidden w-10 h-10 border-[2px] border-user-ink bg-user-gold text-user-ink flex items-center justify-center font-extrabold shrink-0 soft-brutal-shadow-sm overflow-hidden"
                   title="Profile">
                    @if(Auth::user()->foto_profile)
                        <img src="{{ asset('storage/' . Auth::user()->foto_profile) }}"
                             alt="Foto Profile"
                             class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(Auth::user()->nama ?? Auth::user()->name ?? 'U', 0, 1)) }}
                    @endif
                </a>

            </div>
        </div>
    </header>

    {{-- Search Mobile --}}
    <div class="sm:hidden fixed top-[72px] left-0 right-0 z-20 bg-user-bg border-b-[2px] border-user-ink px-4 py-3">
        <form action="@yield('search_action', route('user.katalog'))" method="GET" class="relative w-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-user-ink">
                search
            </span>

            <input
                class="w-full bg-white border-[2px] border-user-ink rounded-none pl-10 pr-4 py-2 text-body-sm font-semibold focus:ring-0 focus:outline-none focus:shadow-[0_0_0_3px_rgba(201,162,39,0.28)] soft-brutal-shadow-sm"
                placeholder="@yield('search_placeholder', 'Cari buku...')"
                type="text"
                name="search"
                value="{{ request('search') }}"
            />
        </form>
    </div>

    {{-- Main Content --}}
    <main class="page-shell ml-0 lg:ml-[246px] w-full lg:w-[calc(100vw-246px)] max-w-full min-h-screen pt-[132px] sm:pt-[72px] overflow-x-hidden">
        <div class="w-full max-w-6xl mx-auto px-4 lg:px-6 py-5 overflow-x-hidden">
            @yield('content')
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const overlay = document.getElementById('sidebarOverlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        const scrollKey = 'userScrollPosition';

        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }

        window.addEventListener('beforeunload', function () {
            sessionStorage.setItem(scrollKey, window.scrollY);
        });

        window.addEventListener('DOMContentLoaded', function () {
            const scrollPosition = sessionStorage.getItem(scrollKey);

            if (scrollPosition !== null) {
                window.scrollTo(0, parseInt(scrollPosition));
            }
        });
    </script>

    @stack('scripts')

</body>
</html>
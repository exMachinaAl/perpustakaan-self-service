<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Register - Perpustakaan</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Montserrat:wght@600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: "#14213D",
                        navy: "#13233A",
                        navy2: "#1E3A5F",
                        blue: "#27496D",
                        blueSoft: "#DCE7F3",
                        cream: "#F5F1E8",
                        paper: "#FFFAF0",
                        bone: "#EDE3CF",
                        gold: "#C9A227",
                        goldDark: "#A8841E",
                        slate: "#263247",
                        maroon: "#A65F6A",
                        white: "#FFFFFF",
                    },
                    fontFamily: {
                        inter: ["Inter", "sans-serif"],
                        montserrat: ["Montserrat", "sans-serif"],
                    },
                    boxShadow: {
                        brutal: "6px 6px 0px #14213D",
                        brutalSm: "4px 4px 0px #14213D",
                        soft: "0 24px 60px -30px rgba(20, 33, 61, 0.55)",
                    }
                }
            }
        }
    </script>

    <style>
        html,
        body,
        main,
        section,
        div {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar,
        main::-webkit-scrollbar,
        section::-webkit-scrollbar,
        div::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(201, 162, 39, 0.14), transparent 28%),
                radial-gradient(circle at bottom right, rgba(30, 58, 95, 0.14), transparent 32%),
                #F5F1E8;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
        }

        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }

        input[type="password"]::-webkit-credentials-auto-fill-button,
        input[type="password"]::-webkit-caps-lock-indicator {
            visibility: hidden;
            display: none !important;
            pointer-events: none;
        }

        .library-panel {
            background:
                linear-gradient(135deg, rgba(201, 162, 39, 0.14), transparent 30%),
                linear-gradient(145deg, #13233A 0%, #1E3A5F 55%, #27496D 100%);
            position: relative;
        }

        .library-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.045) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.045) 1px, transparent 1px);
            background-size: 36px 36px;
            opacity: 0.65;
        }

        .library-panel::after {
            content: "";
            position: absolute;
            inset: 18px;
            border: 1.5px solid rgba(201, 162, 39, 0.34);
            pointer-events: none;
        }

        .brutal-card {
            border: 2px solid #14213D;
            box-shadow: 5px 5px 0px #14213D;
        }

        .brutal-card-soft {
            border: 2px solid #14213D;
            box-shadow: 0 24px 60px -30px rgba(20, 33, 61, 0.55);
        }

        .input-brutal:focus {
            outline: none;
            border-color: #14213D;
            box-shadow: 0 0 0 3px rgba(201, 162, 39, 0.28);
        }

        .btn-brutal:hover {
            transform: translateY(-1px);
            box-shadow: 5px 5px 0px #14213D;
        }

        .btn-brutal:active {
            transform: translateY(1px);
            box-shadow: 2px 2px 0px #14213D;
        }

        .book-spine {
            border: 2px solid rgba(255, 250, 240, 0.9);
            box-shadow: 4px 4px 0px rgba(201, 162, 39, 0.42);
            animation: bookEnter .8s cubic-bezier(.16, 1, .3, 1) forwards;
            opacity: 0;
            transform: translateY(18px);
        }

        .book-spine:nth-child(1) { animation-delay: .15s; }
        .book-spine:nth-child(2) { animation-delay: .25s; }
        .book-spine:nth-child(3) { animation-delay: .35s; }
        .book-spine:nth-child(4) { animation-delay: .45s; }
        .book-spine:nth-child(5) { animation-delay: .55s; }
        .book-spine:nth-child(6) { animation-delay: .65s; }

        .fade-up {
            opacity: 0;
            transform: translateY(24px) scale(.98);
            filter: blur(6px);
            animation: fadeUp .85s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        .fade-left {
            opacity: 0;
            transform: translateX(-28px);
            filter: blur(6px);
            animation: fadeLeft .9s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        .fade-right {
            opacity: 0;
            transform: translateX(28px);
            filter: blur(6px);
            animation: fadeRight .9s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        .float-soft {
            animation: floatSoft 5s ease-in-out infinite;
        }

        .float-soft-delay {
            animation: floatSoft 6.5s ease-in-out infinite;
        }

        .pulse-soft {
            animation: pulseSoft 3s ease-in-out infinite;
        }

        .input-animate {
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .input-animate:focus-within {
            transform: translateY(-2px);
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }

        @keyframes fadeLeft {
            to {
                opacity: 1;
                transform: translateX(0);
                filter: blur(0);
            }
        }

        @keyframes fadeRight {
            to {
                opacity: 1;
                transform: translateX(0);
                filter: blur(0);
            }
        }

        @keyframes floatSoft {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-10px) rotate(1deg);
            }
        }

        @keyframes pulseSoft {
            0%, 100% {
                transform: scale(1);
                opacity: .9;
            }
            50% {
                transform: scale(1.06);
                opacity: .75;
            }
        }

        @keyframes bookEnter {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="text-ink antialiased">

<main class="min-h-screen flex w-full">

    {{-- Left Column --}}
    <section class="hidden lg:flex w-5/12 library-panel relative flex-col justify-between p-8 text-white overflow-hidden min-h-screen fade-left">

        <div class="absolute -top-24 -right-24 w-64 h-64 bg-gold/90 border-[2px] border-paper rounded-full z-[1] pulse-soft"></div>
        <div class="absolute bottom-28 -left-12 w-28 h-28 bg-bone border-[2px] border-gold rotate-12 z-[1] float-soft-delay"></div>

        {{-- Header --}}
        <div class="relative z-10">
            <div class="flex items-center gap-4 fade-up" style="animation-delay: .08s;">
                <div class="w-[60px] h-[60px] bg-paper text-ink border-[2px] border-gold flex items-center justify-center shadow-[5px_5px_0px_rgba(20,33,61,0.45)]">
                    <span class="material-symbols-outlined text-[36px]">
                        local_library
                    </span>
                </div>

                <div>
                    <h1 class="font-montserrat text-[28px] font-black tracking-tight uppercase">
                        Perpustakaan
                    </h1>
                    <p class="text-sm text-white/70 font-semibold">
                        Sistem Peminjaman Buku Kampus
                    </p>
                </div>
            </div>

            {{-- Hero Text --}}
            <div class="mt-12 fade-up" style="animation-delay: .18s;">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-paper text-ink border-[2px] border-gold text-xs font-black uppercase shadow-[4px_4px_0px_rgba(20,33,61,0.45)]">
                    <span class="material-symbols-outlined text-[18px]">
                        shelves
                    </span>
                    Sistem Perpustakaan
                </span>

                <h2 class="mt-7 font-montserrat text-[39px] leading-[1.08] font-black max-w-md uppercase">
                    Daftar untuk mulai meminjam buku
                </h2>

                <p class="mt-5 text-white/75 text-[14px] leading-relaxed max-w-md font-medium">
                    Akses katalog buku, ajukan peminjaman, pantau batas pengembalian,
                    dan lihat riwayat peminjaman melalui satu sistem perpustakaan.
                </p>
            </div>
        </div>

        {{-- Library Illustration --}}
        <div class="relative z-10">
            <div class="bg-paper text-ink brutal-card p-5 mb-5 float-soft">
                <div class="flex items-end gap-2 h-24 mb-4">
                    <div class="book-spine w-9 h-20 bg-gold"></div>
                    <div class="book-spine w-9 h-16 bg-blueSoft"></div>
                    <div class="book-spine w-9 h-24 bg-maroon"></div>
                    <div class="book-spine w-9 h-18 bg-slate"></div>
                    <div class="book-spine w-9 h-14 bg-gold"></div>
                    <div class="book-spine w-9 h-22 bg-blue"></div>
                </div>

                <div class="border-t-[2px] border-ink pt-3">
                    <p class="font-black uppercase text-lg">
                        Rak Buku Kampus
                    </p>
                    <p class="text-xs text-gray-700 font-semibold mt-1">
                        Katalog, peminjaman, dan pengembalian buku dalam satu sistem.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3 fade-up" style="animation-delay: .25s;">
                <div class="bg-bone text-ink border-[2px] border-ink p-3 shadow-[3px_3px_0px_#14213D]">
                    <span class="material-symbols-outlined text-[24px] mb-2">
                        menu_book
                    </span>
                    <p class="text-xs font-black uppercase">Katalog</p>
                </div>

                <div class="bg-gold text-ink border-[2px] border-ink p-3 shadow-[3px_3px_0px_#14213D]">
                    <span class="material-symbols-outlined text-[24px] mb-2">
                        assignment_add
                    </span>
                    <p class="text-xs font-black uppercase">Pinjam</p>
                </div>

                <div class="bg-bone text-ink border-[2px] border-ink p-3 shadow-[3px_3px_0px_#14213D]">
                    <span class="material-symbols-outlined text-[24px] mb-2">
                        assignment_return
                    </span>
                    <p class="text-xs font-black uppercase">Kembali</p>
                </div>
            </div>
        </div>

    </section>

    {{-- Right Column --}}
    <section class="w-full lg:w-7/12 flex items-center justify-center px-5 py-6 lg:px-10 bg-cream min-h-screen">

        <div class="w-full max-w-xl bg-paper brutal-card-soft rounded-sm px-7 py-6 lg:px-9 lg:py-7 fade-right">

            <div class="mb-6 text-center lg:text-left fade-up" style="animation-delay: .12s;">

                <div class="lg:hidden flex items-center justify-center gap-3 mb-6 text-ink">
                    <div class="w-12 h-12 bg-gold border-[2px] border-ink flex items-center justify-center shadow-brutalSm">
                        <span class="material-symbols-outlined text-[28px]">local_library</span>
                    </div>
                    <h1 class="font-montserrat text-xl font-black uppercase">Perpustakaan</h1>
                </div>

                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gold border-[2px] border-ink text-xs font-black uppercase mb-4">
                    <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
                    Form Pendaftaran
                </div>

                <h3 class="font-montserrat text-[30px] leading-tight font-black text-ink uppercase mb-2">
                    Buat Akun Baru
                </h3>

                <p class="text-sm text-gray-600 font-medium">
                    Lengkapi data untuk mengakses layanan peminjaman buku.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-5 bg-red-100 text-red-700 px-4 py-3 text-sm border-[2px] border-ink shadow-brutalSm font-semibold fade-up" style="animation-delay: .18s;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register.post') }}" class="space-y-4" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="space-y-1 fade-up input-animate" style="animation-delay: .22s;">
                        <label class="text-sm font-black text-ink uppercase tracking-wide flex items-center gap-2" for="nama">
                            <span class="material-symbols-outlined text-[18px]">person</span>
                            Nama Lengkap
                        </label>

                        <input
                            class="w-full px-3 py-2.5 bg-white border-[2px] border-ink font-semibold text-sm transition-all input-brutal"
                            id="nama"
                            name="nama"
                            placeholder="Masukkan nama lengkap"
                            required
                            type="text"
                            value="{{ old('nama') }}"
                        />
                    </div>

                    <div class="space-y-1 fade-up input-animate" style="animation-delay: .28s;">
                        <label class="text-sm font-black text-ink uppercase tracking-wide flex items-center gap-2" for="nim">
                            <span class="material-symbols-outlined text-[18px]">badge</span>
                            NIM
                        </label>

                        <input
                            class="w-full px-3 py-2.5 bg-white border-[2px] border-ink font-semibold text-sm transition-all input-brutal"
                            id="nim"
                            name="nim"
                            placeholder="Nomor Induk Mahasiswa"
                            required
                            type="text"
                            value="{{ old('nim') }}"
                        />
                    </div>

                    <div class="space-y-1 md:col-span-2 fade-up input-animate" style="animation-delay: .34s;">
                        <label class="text-sm font-black text-ink uppercase tracking-wide flex items-center gap-2" for="prodi_id">
                            <span class="material-symbols-outlined text-[18px]">school</span>
                            Program Studi
                        </label>

                        <div class="relative">
                            <select
                                class="w-full px-3 py-2.5 bg-white border-[2px] border-ink font-semibold text-sm transition-all input-brutal appearance-none"
                                id="prodi_id"
                                name="prodi_id"
                                required
                            >
                                <option disabled {{ old('prodi_id') ? '' : 'selected' }} value="">
                                    Pilih Program Studi
                                </option>

                                @foreach ($prodi as $item)
                                    <option value="{{ $item->id }}" {{ old('prodi_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama }}
                                    </option>
                                @endforeach
                            </select>

                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-ink">
                                expand_more
                            </span>
                        </div>
                    </div>

                    <div class="space-y-1 md:col-span-2 fade-up input-animate" style="animation-delay: .40s;">
                        <label class="text-sm font-black text-ink uppercase tracking-wide flex items-center gap-2" for="email">
                            <span class="material-symbols-outlined text-[18px]">mail</span>
                            Email
                        </label>

                        <input
                            class="w-full px-3 py-2.5 bg-white border-[2px] border-ink font-semibold text-sm transition-all input-brutal"
                            id="email"
                            name="email"
                            placeholder="contoh@mahasiswa.umg.ac.id"
                            required
                            type="email"
                            value="{{ old('email') }}"
                        />
                    </div>

                    <div class="space-y-1 fade-up input-animate" style="animation-delay: .46s;">
                        <label class="text-sm font-black text-ink uppercase tracking-wide flex items-center gap-2" for="password">
                            <span class="material-symbols-outlined text-[18px]">lock</span>
                            Password
                        </label>

                        <div class="relative">
                            <input
                                class="w-full px-3 py-2.5 pr-10 bg-white border-[2px] border-ink font-semibold text-sm transition-all input-brutal"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                required
                                type="password"
                            />

                            <button
                                class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-ink hover:text-gray-600 transition-colors"
                                type="button"
                                id="togglePassword"
                            >
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-1 fade-up input-animate" style="animation-delay: .52s;">
                        <label class="text-sm font-black text-ink uppercase tracking-wide flex items-center gap-2" for="password_confirmation">
                            <span class="material-symbols-outlined text-[18px]">lock_clock</span>
                            Konfirmasi
                        </label>

                        <div class="relative">
                            <input
                                class="w-full px-3 py-2.5 pr-10 bg-white border-[2px] border-ink font-semibold text-sm transition-all input-brutal"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="••••••••"
                                required
                                type="password"
                            />

                            <button
                                class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-ink hover:text-gray-600 transition-colors"
                                type="button"
                                id="toggleConfirmPassword"
                            >
                                <span class="material-symbols-outlined text-[20px]">visibility</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-2 pt-1 fade-up" style="animation-delay: .58s;">
                    <input
                        class="w-4 h-4 mt-0.5 border-[2px] border-ink text-ink focus:ring-ink"
                        id="terms"
                        name="terms"
                        required
                        type="checkbox"
                    />

                    <label class="text-xs text-gray-700 font-semibold leading-relaxed" for="terms">
                        Saya setuju dengan
                        <a class="text-ink font-black underline decoration-[2px] underline-offset-4 hover:bg-gold px-1 transition-colors" href="#">
                            Syarat & Ketentuan
                        </a>
                        yang berlaku.
                    </label>
                </div>

                <button
                    class="btn-brutal w-full py-3.5 bg-ink text-white font-black text-sm uppercase tracking-wide border-[2px] border-ink transition-all flex items-center justify-center gap-2 shadow-brutalSm fade-up"
                    style="animation-delay: .66s;"
                    type="submit"
                >
                    Daftar Sekarang
                    <span class="material-symbols-outlined text-[20px]">
                        arrow_forward
                    </span>
                </button>
            </form>

            <div class="mt-6 pt-5 border-t-[2px] border-ink text-center fade-up" style="animation-delay: .74s;">
                <p class="text-sm text-gray-700 font-semibold">
                    Sudah punya akun?

                    <a class="text-ink font-black underline decoration-[2px] underline-offset-4 hover:bg-gold px-1 transition-colors" href="{{ route('login') }}">
                        Login
                    </a>
                </p>
            </div>

        </div>
    </section>

</main>

<script>
    function setupPasswordToggle(inputId, buttonId) {
        const input = document.getElementById(inputId);
        const button = document.getElementById(buttonId);
        const icon = button?.querySelector('span');

        if (!input || !button || !icon) return;

        input.addEventListener('input', () => {
            if (input.value.length > 0) {
                button.classList.remove('hidden');
            } else {
                button.classList.add('hidden');
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        });

        button.addEventListener('click', () => {
            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';
            icon.textContent = isPassword ? 'visibility_off' : 'visibility';
        });
    }

    setupPasswordToggle('password', 'togglePassword');
    setupPasswordToggle('password_confirmation', 'toggleConfirmPassword');

    const registerForm = document.querySelector("form[action='{{ route('register.post') }}']");
    const submitButton = registerForm?.querySelector('button[type="submit"]');

    if (registerForm && submitButton) {
        registerForm.addEventListener('submit', () => {
            submitButton.disabled = true;
            submitButton.classList.add('opacity-80', 'cursor-not-allowed');
            submitButton.innerHTML = `
                <span class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span>
                Memproses...
            `;
        });
    }

    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>

</body>
</html>
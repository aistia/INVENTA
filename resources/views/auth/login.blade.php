
@extends('layouts.guest')

@section('title', 'Login')

@section('content')

@php
    try {
        $contohAlat = \App\Models\Alat::with('kategori')
            ->where('arsip', false)
            ->where('kondisi', 'baik')
            ->where('jumlah_stok', '>', 0)
            ->orderByDesc('jumlah_stok')
            ->take(2)
            ->get();
    } catch (\Throwable $e) {
        $contohAlat = collect();
    }
@endphp

<style>
    /* ==============================
       ANIMASI
    ============================== */

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(14px);
        }
        to {
            opacity: 1;
            transform: none;
        }
    }

    @keyframes popIn {
        from {
            opacity: 0;
            transform: translateY(18px) scale(.96);
        }
        to {
            opacity: 1;
            transform: none;
        }
    }

    @keyframes shimmer {
        from {
            background-position: 0% 50%;
        }
        to {
            background-position: 200% 50%;
        }
    }

    @keyframes pulseRing {
        0% {
            box-shadow: 0 0 0 0 rgba(124,58,237,.35);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(124,58,237,0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(124,58,237,0);
        }
    }

    @keyframes logoGlow {
        0%, 100% {
            filter: drop-shadow(0 0 3px rgba(167,139,250,.2));
        }
        50% {
            filter: drop-shadow(0 0 12px rgba(167,139,250,.8));
        }
    }

    @keyframes logoFloat {
        0%, 100% {
            translate: 0 0;
        }
        50% {
            translate: 0 -7px;
        }
    }

    @keyframes shake {
        10%, 90% {
            transform: translateX(-1px);
        }
        20%, 80% {
            transform: translateX(2px);
        }
        30%, 50%, 70% {
            transform: translateX(-4px);
        }
        40%, 60% {
            transform: translateX(4px);
        }
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* ==============================
       ANIMASI ELEMEN
    ============================== */

    .anim-up {
        animation: fadeUp .6s cubic-bezier(.22,1,.36,1) both;
    }

    .anim-pop {
        animation: popIn .7s cubic-bezier(.22,1,.36,1) .1s both;
    }

    .d1 { animation-delay: .05s; }
    .d2 { animation-delay: .15s; }
    .d3 { animation-delay: .25s; }
    .d4 { animation-delay: .35s; }
    .d5 { animation-delay: .45s; }
    .d6 { animation-delay: .55s; }

    .shimmer-text {
        background-size: 200% 100%;
        animation: shimmer 5s linear infinite;
    }

    .lock-pulse {
        animation: pulseRing 2.4s ease-out infinite;
    }

    .anim-shake {
        animation: shake .5s ease-in-out;
    }

    .card-hover {
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .card-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px -8px rgba(124,58,237,.25);
    }

    .spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin .7s linear infinite;
    }

    /* ==============================
       ANIMASI LOGO
    ============================== */

    .logo-follow {
        transition: transform .12s ease-out, box-shadow .2s ease-out;
        will-change: transform;
        transform-style: preserve-3d;
        animation: logoFloat 3s ease-in-out infinite;
    }

    .logo-animated {
        animation:
            logoGlow 3s ease-in-out infinite,
            logoFloat 3s ease-in-out infinite;
    }

    /* ==============================
       TEMA UNGU
    ============================== */

    .text-violet-600 {
        color: #7c3aed !important;
    }

    .text-violet-700 {
        color: #6d28d9 !important;
    }

    .bg-violet-50 {
        background-color: #f5f3ff !important;
    }

    .bg-violet-100 {
        background-color: #ede9fe !important;
    }

    .bg-violet-600 {
        background-color: #7c3aed !important;
    }

    .border-violet-500 {
        border-color: #8b5cf6 !important;
    }

    .focus\:ring-violet-500:focus {
        --tw-ring-color: #8b5cf6 !important;
    }

    .accent-violet-600 {
        accent-color: #7c3aed !important;
    }

    /* ==============================
       DESAIN HP
    ============================== */

    @media (max-width: 1023px) {

        .login-layout {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: min(100%, 440px);
            max-width: none;
            min-height: 0;
            max-height: 100%;
            margin: 0 auto;
            padding: 0 22px 18px;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 18px 45px rgba(76,29,149,.16);
        }

        .mobile-login-hero {
            display: flex;
            min-height: 190px;
            margin: 0 -22px 16px;
            padding: 20px 22px 30px;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;

            background: linear-gradient(
                145deg,
                #a78bfa 0%,
                #7c3aed 52%,
                #5b21b6 100%
            );

            border-radius: 0 0 48% 13% / 0 0 16% 8%;
            box-shadow: 0 8px 18px rgba(91,33,182,.18);
        }

        .mobile-login-hero img {
            width: 66px;
            height: 66px;
            object-fit: contain;
            padding: 11px;
            margin-bottom: 12px;
            border-radius: 20px;
            background: rgba(255,255,255,.16);
        }

        .mobile-brand {
            font-size: 23px;
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: .08em;
        }

        .mobile-tagline {
            margin-top: 6px;
            font-size: 12px;
            color: rgba(255,255,255,.88);
        }

        .login-card .login-heading {
            margin-bottom: 14px;
        }

        .login-card #formLogin {
            gap: 12px;
        }

        .login-card #formLogin input:not([type="checkbox"]) {
            height: 46px;
            border-radius: 12px;
            background: #fff;
        }

        .login-card #btnMasuk {
            height: 47px;
            margin-top: 5px;
            border-radius: 999px;
            background: linear-gradient(90deg, #7c3aed, #5b21b6);
        }

        .login-card > .border-t {
            margin-top: 14px;
            padding-top: 10px;
        }
    }

    /* HP KECIL */

    @media (max-width: 380px) {
        .login-card {
            padding-right: 17px;
            padding-left: 17px;
        }

        .mobile-login-hero {
            margin-right: -17px;
            margin-left: -17px;
        }
    }

    /* LAYAR PENDEK */

    @media (max-height: 700px) and (max-width: 1023px) {

        .mobile-login-hero {
            min-height: 145px;
            padding-top: 12px;
            padding-bottom: 20px;
            margin-bottom: 12px;
        }

        .mobile-login-hero img {
            width: 52px;
            height: 52px;
            margin-bottom: 7px;
        }

        .login-card {
            padding-top: 0;
            padding-bottom: 12px;
        }

        .login-card .login-heading {
            margin-bottom: 10px;
        }

        .login-card #formLogin {
            gap: 9px;
        }
    }

    /* AKSESIBILITAS */

    @media (prefers-reduced-motion: reduce) {
        .anim-up,
        .anim-pop,
        .shimmer-text,
        .lock-pulse,
        .anim-shake,
        .spinner,
        .logo-animated,
        .logo-follow {
            animation: none !important;
        }

        .card-hover,
        .logo-follow {
            transition: none;
        }

        .logo-follow {
            transform: none !important;
            translate: none !important;
            box-shadow: none !important;
        }
    }
</style>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center login-layout">

    {{-- BAGIAN KIRI: INFORMASI LAPTOP --}}

    <div class="hidden lg:block">

        <div class="flex items-center gap-2 mb-3 anim-up d1">

            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white rounded-full text-[11px] font-semibold text-emerald-600 shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                SISTEM INVENTARIS LAB
            </span>

            <span class="inline-flex items-center px-2.5 py-1 bg-white rounded-full text-[11px] font-semibold text-violet-600 shadow-sm">
                SMK RPL
            </span>

        </div>


        <div class="flex items-center gap-3 mb-3 anim-up d2">

            <img
                id="logoInventa"
                src="{{ asset('images/inventa-icon-v3.svg') }}"
                alt="Inventa"
                class="logo-follow logo-animated w-12 h-12 rounded-xl cursor-pointer"
            >

            <div>

                <div class="flex items-center gap-2">

                    <span class="font-display font-extrabold text-lg text-slate-900">
                        INVENTA
                    </span>

                    <span class="px-1.5 py-0.5 bg-violet-100 text-violet-700 text-[10px] font-bold rounded">
                        LAB RPL
                    </span>

                </div>

                <span class="text-[11px] text-slate-500">
                    Sistem Manajemen &amp; Peminjaman Alat Lab
                </span>

            </div>

        </div>


        <h1 class="font-display font-extrabold text-3xl leading-tight text-slate-900 mb-2.5 anim-up d3">

            Pusat Manajemen Inventaris &amp;

            <span class="bg-gradient-to-r from-violet-600 via-purple-500 to-indigo-600 bg-clip-text text-transparent shimmer-text">
                Peminjaman Alat Lab RPL
            </span>

        </h1>


        <p class="text-sm text-slate-500 mb-4 max-w-lg anim-up d4">

            Cek ketersediaan alat praktikum, ajukan peminjaman, pantau statusnya, dan laporkan
            kerusakan perangkat. Semua pengajuan diverifikasi oleh petugas lab.

        </p>


        <div class="flex flex-wrap gap-1.5 mb-4 anim-up d4">

            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white rounded-full text-[11px] font-medium text-slate-600 shadow-sm">
                <span class="material-symbols-outlined text-[14px] text-violet-600">inventory_2</span>
                Katalog &amp; Cek Stok Alat
            </span>

            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white rounded-full text-[11px] font-medium text-slate-600 shadow-sm">
                <span class="material-symbols-outlined text-[14px] text-violet-600">add_task</span>
                Pengajuan Peminjaman Online
            </span>

            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white rounded-full text-[11px] font-medium text-slate-600 shadow-sm">
                <span class="material-symbols-outlined text-[14px] text-violet-600">report</span>
                Lapor Kerusakan Alat
            </span>

        </div>


        {{-- CONTOH ALAT --}}

        @if ($contohAlat->isNotEmpty())

            <div class="grid grid-cols-2 gap-3 mb-3 anim-up d5">

                @foreach ($contohAlat as $alat)

                    <div class="bg-white rounded-xl p-3 shadow-sm card-hover">

                        <div class="flex items-start justify-between mb-2">

                            <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center text-violet-600 overflow-hidden">

                                @if ($alat->foto_url)

                                    <img
                                        src="{{ $alat->foto_url }}"
                                        alt=""
                                        class="w-full h-full object-cover"
                                    >

                                @else

                                    <span class="material-symbols-outlined text-[16px]">
                                        memory
                                    </span>

                                @endif

                            </div>

                            <span class="text-[10px] font-semibold text-emerald-600">
                                &bull; {{ $alat->jumlah_stok }} Unit Tersedia
                            </span>

                        </div>


                        <div class="font-semibold text-[13px] text-slate-900">
                            {{ $alat->nama_alat }}
                        </div>


                        <p class="text-[11px] text-slate-500 mt-0.5 mb-1.5 leading-snug line-clamp-2">
                            {{ $alat->deskripsi ?: 'Alat praktikum lab RPL.' }}
                        </p>


                        <span class="px-1.5 py-0.5 bg-violet-50 text-violet-600 text-[9px] font-medium rounded">
                            {{ $alat->kategori->nama_kategori ?? 'Alat Lab' }}
                        </span>

                    </div>

                @endforeach

            </div>

        @endif


        {{-- ALUR PEMINJAMAN --}}

        <div class="bg-white rounded-xl p-3 shadow-sm anim-up d6">

            <p class="text-[11px] font-bold text-slate-400 tracking-wide mb-2">
                ALUR PEMINJAMAN
            </p>

            <ol class="grid grid-cols-4 gap-2 text-center">

                <li>

                    <div class="w-7 h-7 mx-auto rounded-lg bg-violet-600 text-white text-[11px] font-bold flex items-center justify-center mb-1">
                        1
                    </div>

                    <p class="text-[10.5px] text-slate-600 leading-tight">
                        Cek stok alat
                    </p>

                </li>


                <li>

                    <div class="w-7 h-7 mx-auto rounded-lg bg-violet-100 text-violet-600 text-[11px] font-bold flex items-center justify-center mb-1">
                        2
                    </div>

                    <p class="text-[10.5px] text-slate-600 leading-tight">
                        Ajukan peminjaman
                    </p>

                </li>


                <li>

                    <div class="w-7 h-7 mx-auto rounded-lg bg-violet-100 text-violet-600 text-[11px] font-bold flex items-center justify-center mb-1">
                        3
                    </div>

                    <p class="text-[10.5px] text-slate-600 leading-tight">
                        Diverifikasi petugas
                    </p>

                </li>


                <li>

                    <div class="w-7 h-7 mx-auto rounded-lg bg-violet-100 text-violet-600 text-[11px] font-bold flex items-center justify-center mb-1">
                        4
                    </div>

                    <p class="text-[10.5px] text-slate-600 leading-tight">
                        Ambil &amp; kembalikan
                    </p>

                </li>

            </ol>

        </div>

    </div>


    {{-- BAGIAN KANAN: FORM LOGIN --}}

    <div class="w-full max-w-md mx-auto bg-white rounded-2xl shadow-xl p-6 anim-pop login-card">


        {{-- HEADER KHUSUS HP --}}

        <div class="mobile-login-hero lg:hidden">

            <img
                id="logoInventaMobile"
                src="{{ asset('images/inventa-icon-v3.svg') }}"
                alt="Logo Inventa"
                class="logo-follow logo-animated"
            >

            <div class="mobile-brand">
                INVENTA
            </div>

            <p class="mobile-tagline">
                Sistem Manajemen &amp; Peminjaman Alat Lab
            </p>

        </div>


        {{-- JUDUL LOGIN --}}

        <div class="flex flex-col items-center text-center mb-4 login-heading">

            <div class="w-11 h-11 rounded-2xl bg-violet-100 flex items-center justify-center text-violet-600 mb-2.5 lock-pulse">

                <span class="material-symbols-outlined text-[22px]">
                    lock
                </span>

            </div>


            <h1 class="font-display font-bold text-xl text-slate-900">
                Login
            </h1>


            <p class="text-xs text-slate-500 mt-0.5">
                Masuk ke akun inventaris lab Anda
            </p>

        </div>


        {{-- NOTIFIKASI BERHASIL --}}

        @if (session('success'))

            <div class="mb-3 p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs">
                {{ session('success') }}
            </div>

        @endif


        {{-- NOTIFIKASI ERROR --}}

        @if ($errors->any())

            <div class="mb-3 p-2.5 rounded-lg bg-red-50 border border-red-200 text-red-700 text-xs anim-shake">
                {{ $errors->first() }}
            </div>

        @endif


        {{-- FORM LOGIN --}}

        <form
            id="formLogin"
            method="POST"
            action="{{ route('login.attempt') }}"
            class="space-y-3"
        >

            @csrf


            {{-- USERNAME --}}

            <div>

                <label
                    for="identifier"
                    class="block text-[11px] font-bold text-slate-500 tracking-wide mb-1"
                >
                    USERNAME / EMAIL
                </label>


                <div class="relative flex items-center">

                    <span class="material-symbols-outlined absolute left-3 text-slate-400 pointer-events-none text-[18px]">
                        person
                    </span>


                    <input
                        type="text"
                        id="identifier"
                        name="identifier"
                        value="{{ old('identifier') }}"
                        placeholder="Masukkan username atau email"
                        required
                        autofocus
                        class="w-full h-10 pl-10 pr-3 bg-slate-50 border border-slate-200 text-slate-900 placeholder:text-slate-400 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                    >

                </div>


                @error('identifier')

                    <p class="text-[11px] text-red-600 mt-1">
                        {{ $message }}
                    </p>

                @enderror

            </div>


            {{-- PASSWORD --}}

            <div>

                <label
                    for="password"
                    class="block text-[11px] font-bold text-slate-500 tracking-wide mb-1"
                >
                    KATA SANDI
                </label>


                <div class="relative flex items-center">

                    <span class="material-symbols-outlined absolute left-3 text-slate-400 pointer-events-none text-[18px]">
                        lock
                    </span>


                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••••"
                        required
                        class="w-full h-10 pl-10 pr-10 bg-slate-50 border border-slate-200 text-slate-900 placeholder:text-slate-400 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all"
                    >


                    <button
                        type="button"
                        onclick="togglePassword('password', this)"
                        class="absolute right-3 text-slate-400 hover:text-violet-600"
                    >

                        <span class="material-symbols-outlined text-[18px]">
                            visibility
                        </span>

                    </button>

                </div>

            </div>


            {{-- INGAT SAYA --}}

            <div class="flex items-center justify-between">

                <label class="flex items-center gap-2 cursor-pointer select-none">

                    <input
                        type="checkbox"
                        name="remember"
                        class="w-3.5 h-3.5 rounded accent-violet-600"
                    >

                    <span class="text-xs text-slate-600">
                        Ingat saya
                    </span>

                </label>

            </div>


            {{-- TOMBOL LOGIN --}}

            <button
                id="btnMasuk"
                type="submit"
                class="w-full h-11 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-semibold flex items-center justify-center gap-2 shadow-md hover:shadow-lg hover:opacity-95 active:scale-[.98] transition-all text-sm"
            >

                <span class="material-symbols-outlined text-[18px] btn-icon">
                    login
                </span>

                <span class="spinner btn-spinner hidden"></span>

                <span class="btn-text">
                    Login ke Sistem
                </span>

            </button>

        </form>


        {{-- DAFTAR --}}

        <div class="border-t border-slate-100 mt-4 pt-3 text-center">

            <p class="text-xs text-slate-500">

                Belum punya akun?

                <a
                    href="{{ route('register') }}"
                    class="text-violet-600 font-semibold hover:underline"
                >
                    Daftar di sini
                </a>

            </p>

        </div>

    </div>

</div>


<script>
    /* ==================================
       ANIMASI LOGO MENGIKUTI KURSOR
    ================================== */

    (function () {

        const logos = document.querySelectorAll('.logo-follow');

        if (
            !logos.length ||
            window.matchMedia('(prefers-reduced-motion: reduce)').matches
        ) {
            return;
        }

        const RADIUS = 260;

        let mx = 0;
        let my = 0;
        let raf = null;


        function reset() {

            logos.forEach(logo => {
                logo.style.transform = '';
                logo.style.boxShadow = '';
            });

        }


        function update() {

            raf = null;

            logos.forEach(logo => {

                const r = logo.getBoundingClientRect();

                const dx = mx - (r.left + r.width / 2);
                const dy = my - (r.top + r.height / 2);

                const dist = Math.hypot(dx, dy);
                const near = Math.max(0, 1 - dist / RADIUS);

                if (near === 0) {

                    logo.style.transform = '';
                    logo.style.boxShadow = '';

                    return;

                }

                const ux = dx / (dist || 1);
                const uy = dy / (dist || 1);

                logo.style.transform =
                    'perspective(300px) rotateX(' +
                    (-uy * 24 * near) +
                    'deg) rotateY(' +
                    (ux * 24 * near) +
                    'deg) scale(' +
                    (1 + 0.2 * near) +
                    ')';

                logo.style.boxShadow =
                    '0 0 ' +
                    (26 * near) +
                    'px rgba(124,58,237,' +
                    (0.6 * near) +
                    ')';

            });

        }


        window.addEventListener('pointermove', function (e) {

            if (e.pointerType && e.pointerType !== 'mouse') {
                return;
            }

            mx = e.clientX;
            my = e.clientY;

            if (!raf) {
                raf = requestAnimationFrame(update);
            }

        });


        document.addEventListener('mouseleave', reset);

    })();


    /* ==================================
       RESET ANIMASI SIDEBAR
    ================================== */

    try {
        sessionStorage.removeItem('inventaNavIntro');
    } catch (e) {}


    /* ==================================
       ANIMASI TOMBOL LOGIN
    ================================== */

    const formLogin = document.getElementById('formLogin');

    if (formLogin) {

        formLogin.addEventListener('submit', function () {

            const btn = document.getElementById('btnMasuk');

            btn.disabled = true;

            btn.classList.add(
                'opacity-80',
                'cursor-wait'
            );

            btn.querySelector('.btn-icon').classList.add('hidden');

            btn.querySelector('.btn-spinner').classList.remove('hidden');

            btn.querySelector('.btn-text').textContent = 'Memproses...';

        });

    }


    /* ==================================
       RESET TOMBOL SAAT KEMBALI
    ================================== */

    window.addEventListener('pageshow', function (e) {

        if (e.persisted) {
            location.reload();
        }

    });


    /* ==================================
       TAMPIL / SEMBUNYIKAN PASSWORD
    ================================== */

    function togglePassword(id, btn) {

        const input = document.getElementById(id);

        const icon = btn.querySelector('.material-symbols-outlined');

        if (input.type === 'password') {

            input.type = 'text';

            icon.textContent = 'visibility_off';

        } else {

            input.type = 'password';

            icon.textContent = 'visibility';

        }

    }

</script>

@endsection

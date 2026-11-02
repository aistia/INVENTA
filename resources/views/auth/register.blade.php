
@extends('layouts.guest')

@section('title', 'Daftar Akun')

@section('content')
<style>
    /* ==============================
       ANIMASI
    ============================== */

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to { opacity: 1; transform: none; }
    }

    @keyframes popIn {
        from { opacity: 0; transform: translateY(18px) scale(.96); }
        to { opacity: 1; transform: none; }
    }

    @keyframes shimmer {
        from { background-position: 0% 50%; }
        to { background-position: 200% 50%; }
    }

    @keyframes pulseRing {
        0% { box-shadow: 0 0 0 0 rgba(124,58,237,.35); }
        70% { box-shadow: 0 0 0 8px rgba(124,58,237,0); }
        100% { box-shadow: 0 0 0 0 rgba(124,58,237,0); }
    }

    @keyframes shake {
        10%, 90% { transform: translateX(-1px); }
        20%, 80% { transform: translateX(2px); }
        30%, 50%, 70% { transform: translateX(-4px); }
        40%, 60% { transform: translateX(4px); }
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @keyframes logoFloat {
        0%, 100% { translate: 0 0; }
        50% { translate: 0 -7px; }
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

    .badge-pulse {
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

    .logo-follow {
        transition: transform .12s ease-out, filter .2s ease-out;
        will-change: transform;
        transform-style: preserve-3d;
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

    .accent-violet-600 {
        accent-color: #7c3aed !important;
    }

    /* ==============================
       DESAIN MOBILE REGISTER
    ============================== */

    .mobile-register-hero {
        display: none;
    }

    @media (max-width: 1023px) {

        .register-layout {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-card {
            width: min(100%, 440px);
            max-width: none;
            max-height: 100%;
            margin: 0 auto;
            padding: 0 20px 16px;
            border-radius: 24px;
            overflow-y: auto;
            overflow-x: hidden;
            overscroll-behavior: contain;
            box-shadow: 0 18px 45px rgba(76,29,149,.16);
            scrollbar-width: none;
        }

        .register-card::-webkit-scrollbar {
            display: none;
        }

        /* HEADER UNGU */

        .mobile-register-hero {
            display: flex;
            min-height: 155px;
            margin: 0 -20px 15px;
            padding: 18px 20px 27px;
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

        .mobile-register-hero img {
            width: 58px;
            height: 58px;
            object-fit: contain;
            padding: 9px;
            margin-bottom: 9px;
            border-radius: 18px;
            background: rgba(255,255,255,.16);
            animation: logoFloat 3s ease-in-out infinite;
        }

        .mobile-register-brand {
            font-size: 23px;
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: .08em;
        }

        .mobile-register-tagline {
            margin-top: 5px;
            font-size: 11px;
            color: rgba(255,255,255,.88);
        }

        /* JUDUL */

        .register-card .register-heading {
            margin-bottom: 12px;
        }

        /* JARAK FORM */

        .register-card #formRegister {
            gap: 10px;
        }

        /* INPUT */

        .register-card #formRegister input:not([type="checkbox"]),
        .register-card #formRegister select {
            height: 42px;
            border-radius: 11px;
        }

        /* TOMBOL */

        .register-card #btnDaftar {
            min-height: 44px;
            border-radius: 999px;
            background: linear-gradient(90deg, #7c3aed, #5b21b6);
        }
    }

    /* ==============================
       HP KECIL
    ============================== */

    @media (max-width: 480px) {

        .register-card {
            padding-right: 17px;
            padding-left: 17px;
        }

        .mobile-register-hero {
            margin-right: -17px;
            margin-left: -17px;
        }

        .register-card #formRegister > .grid.grid-cols-2 {
            grid-template-columns: minmax(0,1fr);
        }

        .register-card #formRegister > .grid.grid-cols-2 > .col-span-2 {
            grid-column: span 1 / span 1;
        }
    }

    /* ==============================
       LAYAR PENDEK
    ============================== */

    @media (max-height: 700px) and (max-width: 1023px) {

        .mobile-register-hero {
            min-height: 120px;
            padding-top: 10px;
            padding-bottom: 17px;
            margin-bottom: 11px;
        }

        .mobile-register-hero img {
            width: 45px;
            height: 45px;
            margin-bottom: 5px;
        }

        .register-card {
            padding-top: 0;
            padding-bottom: 12px;
        }
    }

    /* ==============================
       AKSESIBILITAS
    ============================== */

    @media (prefers-reduced-motion: reduce) {

        .anim-up,
        .anim-pop,
        .shimmer-text,
        .badge-pulse,
        .anim-shake,
        .spinner,
        .mobile-register-hero img {
            animation: none !important;
        }

        .card-hover,
        .logo-follow {
            transition: none;
        }

        .logo-follow {
            transform: none !important;
            filter: none !important;
        }
    }
</style>


<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center register-layout">

    {{-- ==============================
         KOLOM KIRI: INFORMASI
    ============================== --}}

    <div class="hidden lg:block">

        <div class="flex items-center justify-between mb-3 anim-up d1">

            <img
                id="logoInventa"
                src="{{ asset('images/inventa-logo-v3.svg') }}"
                alt="Inventa"
                class="logo-follow h-12 w-auto cursor-pointer"
            >

        </div>


        <h1 class="font-display font-extrabold text-2xl leading-tight text-slate-900 mb-2.5 anim-up d2">

            Bergabung dengan

            <span class="bg-gradient-to-r from-violet-600 via-indigo-500 to-violet-600 bg-clip-text text-transparent shimmer-text">
                Portal Inventaris
            </span>

            Lab RPL

        </h1>


        <p class="text-xs text-slate-500 mb-4 max-w-lg leading-relaxed anim-up d3">

            Daftarkan akun siswa untuk kemudahan peminjaman alat praktik,
            komponen IoT, dan pelaporan perangkat lab secara terintegrasi
            dan akuntabel.

        </p>


        <span class="text-[11px] font-bold text-slate-400 tracking-wider anim-up d4 inline-block">
            ALUR REGISTRASI AKUN
        </span>


        {{-- ALUR REGISTRASI --}}

        <div class="space-y-2 mt-2 mb-3 anim-up d4">

            <div class="bg-white rounded-xl p-2.5 shadow-sm flex items-start gap-2.5 card-hover">

                <div class="w-6 h-6 rounded-lg bg-violet-600 text-white flex items-center justify-center font-bold text-[11px] shrink-0">
                    1
                </div>

                <div>
                    <div class="font-semibold text-[12.5px] text-slate-900 leading-none mb-0.5">
                        Pilih Peran &amp; Identitas
                    </div>

                    <p class="text-[11px] text-slate-500 leading-snug">
                        Tentukan akun sebagai Siswa kejuruan aktif atau Guru / Pembina Lab.
                    </p>
                </div>

            </div>


            <div class="bg-white rounded-xl p-2.5 shadow-sm flex items-start gap-2.5 card-hover">

                <div class="w-6 h-6 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center font-bold text-[11px] shrink-0">
                    2
                </div>

                <div>
                    <div class="font-semibold text-[12.5px] text-slate-900 leading-none mb-0.5">
                        Verifikasi Data Sekolah
                    </div>

                    <p class="text-[11px] text-slate-500 leading-snug">
                        Lengkapi NIS/NIP resmi dan pilih rombel kelas RPL yang terdaftar.
                    </p>
                </div>

            </div>


            <div class="bg-white rounded-xl p-2.5 shadow-sm flex items-start gap-2.5 card-hover">

                <div class="w-6 h-6 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-[11px] shrink-0">
                    3
                </div>

                <div>
                    <div class="font-semibold text-[12.5px] text-slate-900 leading-none mb-0.5">
                        Akses Penuh Inventaris
                    </div>

                    <p class="text-[11px] text-slate-500 leading-snug">
                        Pinjam alat praktikum, kit mikrokontroler, dan ajukan tiket perawatan lab.
                    </p>
                </div>

            </div>

        </div>


        {{-- INFORMASI SEKOLAH --}}

        <div class="anim-up d5">

            <div class="bg-violet-50 border border-violet-100 rounded-xl p-2.5 flex items-center gap-2.5 card-hover">

                <span class="material-symbols-outlined text-violet-600 text-[20px]">
                    shield
                </span>

                <div>
                    <div class="font-semibold text-[12.5px] text-slate-900 leading-none mb-0.5">
                        Sistem Terintegrasi SMK PK
                    </div>

                    <p class="text-[11px] text-slate-500">
                        Program Keahlian Rekayasa Perangkat Lunak Terakreditasi.
                    </p>
                </div>

            </div>

        </div>

    </div>


    {{-- ==============================
         KOLOM KANAN: FORM REGISTER
    ============================== --}}

    <div class="w-full max-w-lg mx-auto bg-white rounded-2xl shadow-xl p-5 anim-pop register-card">


        {{-- HEADER KHUSUS HP --}}

        <div class="mobile-register-hero lg:hidden">

            <img
                src="{{ asset('images/inventa-icon-v3.svg') }}"
                alt="Logo Inventa"
                class="logo-follow"
            >

            <div class="mobile-register-brand">
                INVENTA
            </div>

            <p class="mobile-register-tagline">
                Sistem Manajemen &amp; Peminjaman Alat Lab
            </p>

        </div>


        {{-- JUDUL REGISTER --}}

        <div class="flex items-start justify-between mb-0.5 register-heading">

            <h1 class="font-display font-bold text-lg text-slate-900">
                Registrasi Akun Baru
            </h1>

        </div>


        <p class="text-[11px] text-slate-500 mb-3">
            Lengkapi data diri untuk memulai akses inventaris lab RPL
        </p>


        {{-- NOTIFIKASI ERROR --}}

        @if ($errors->any())

            <div class="mb-3 p-2 rounded-lg bg-red-50 border border-red-200 text-red-700 text-[11px] space-y-0.5 anim-shake">

                @foreach ($errors->all() as $error)

                    <p>{{ $error }}</p>

                @endforeach

            </div>

        @endif


        {{-- FORM REGISTER --}}

        <form
            id="formRegister"
            method="POST"
            action="{{ route('register.attempt') }}"
            class="space-y-2.5"
        >

            @csrf


            {{-- JENIS PENGGUNA --}}

            <div>

                <label class="block text-[10.5px] font-bold text-slate-500 tracking-wide mb-1">
                    JENIS PENGGUNA *
                </label>

                <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-100 rounded-xl" id="tipeContainer">

                    <button
                        type="button"
                        onclick="pilihTipe('siswa')"
                        id="btnSiswa"
                        class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg font-semibold text-xs transition-all"
                    >

                        <span class="material-symbols-outlined text-[15px]">
                            school
                        </span>

                        <span>Siswa</span>

                    </button>


                    <button
                        type="button"
                        onclick="pilihTipe('guru')"
                        id="btnGuru"
                        class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg font-semibold text-xs transition-all"
                    >

                        <span class="material-symbols-outlined text-[15px]">
                            apartment
                        </span>

                        <span>Guru / Pembina Lab</span>

                    </button>

                </div>

                <input
                    type="hidden"
                    name="tipe_user"
                    id="tipe_user"
                    value="{{ old('tipe_user', 'siswa') }}"
                >

            </div>


            {{-- GRID DATA PENGGUNA --}}

            <div class="grid grid-cols-2 gap-2.5">


                {{-- USERNAME --}}

                <div>

                    <label for="username" class="block text-[10.5px] font-bold text-slate-500 tracking-wide mb-1">
                        USERNAME *
                    </label>

                    <div class="relative flex items-center">

                        <span class="material-symbols-outlined absolute left-3 text-slate-400 pointer-events-none text-[16px]">
                            alternate_email
                        </span>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="aryaputra"
                            required
                            class="w-full h-9 pl-9 pr-2 bg-slate-50 border border-slate-200 text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 transition-all"
                        >

                    </div>

                    @error('username')
                        <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                    @enderror

                </div>


                {{-- NAMA LENGKAP --}}

                <div>

                    <label for="nama" class="block text-[10.5px] font-bold text-slate-500 tracking-wide mb-1">
                        NAMA LENGKAP *
                    </label>

                    <div class="relative flex items-center">

                        <span class="material-symbols-outlined absolute left-3 text-slate-400 pointer-events-none text-[16px]">
                            badge
                        </span>

                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            value="{{ old('nama') }}"
                            placeholder="Arya Pratama Putra"
                            required
                            class="w-full h-9 pl-9 pr-2 bg-slate-50 border border-slate-200 text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 transition-all"
                        >

                    </div>

                    @error('nama')
                        <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                    @enderror

                </div>


                {{-- FIELD KHUSUS SISWA: NIS --}}

                <div id="fieldNis">

                    <label for="nis" class="block text-[10.5px] font-bold text-slate-500 tracking-wide mb-1">
                        NIS *
                    </label>

                    <div class="relative flex items-center">

                        <span class="material-symbols-outlined absolute left-3 text-slate-400 pointer-events-none text-[16px]">
                            id_card
                        </span>

                        <input
                            type="text"
                            id="nis"
                            name="nis"
                            value="{{ old('nis') }}"
                            placeholder="212210452"
                            class="w-full h-9 pl-9 pr-2 bg-slate-50 border border-slate-200 text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 transition-all"
                        >

                    </div>

                    @error('nis')
                        <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                    @enderror

                </div>


                {{-- FIELD KHUSUS SISWA: KELAS --}}

                <div id="fieldKelas">

                    <label for="kelas" class="block text-[10.5px] font-bold text-slate-500 tracking-wide mb-1">
                        KELAS *
                    </label>

                    <select
                        id="kelas"
                        name="kelas"
                        class="w-full h-9 px-2.5 bg-slate-50 border border-slate-200 text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 text-slate-700 transition-all"
                    >

                        <option value="" disabled {{ old('kelas') ? '' : 'selected' }}>
                            Pilih Rombel
                        </option>

                        @foreach (['X PPLG A', 'X PPLG B', 'X PPLG C', 'XI RPL A', 'XI RPL B', 'XI RPL C', 'XII RPL A', 'XII RPL B', 'XII RPL C'] as $rombel)

                            <option
                                value="{{ $rombel }}"
                                {{ old('kelas') === $rombel ? 'selected' : '' }}
                            >
                                {{ $rombel }}
                            </option>

                        @endforeach

                    </select>

                    @error('kelas')
                        <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                    @enderror

                </div>


                {{-- FIELD KHUSUS GURU: NIP --}}

                <div id="fieldNip" class="hidden col-span-2">

                    <label for="nip" class="block text-[10.5px] font-bold text-slate-500 tracking-wide mb-1">
                        NIP *
                    </label>

                    <div class="relative flex items-center">

                        <span class="material-symbols-outlined absolute left-3 text-slate-400 pointer-events-none text-[16px]">
                            id_card
                        </span>

                        <input
                            type="text"
                            id="nip"
                            name="nip"
                            value="{{ old('nip') }}"
                            placeholder="198501012010012001"
                            class="w-full h-9 pl-9 pr-2 bg-slate-50 border border-slate-200 text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 transition-all"
                        >

                    </div>

                    @error('nip')
                        <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                    @enderror

                </div>


                {{-- EMAIL --}}

                <div class="col-span-2">

                    <label for="email" class="block text-[10.5px] font-bold text-slate-500 tracking-wide mb-1">
                        EMAIL SEKOLAH / PRIBADI *
                    </label>

                    <div class="relative flex items-center">

                        <span class="material-symbols-outlined absolute left-3 text-slate-400 pointer-events-none text-[16px]">
                            mail
                        </span>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="arya.putra@smkn.sch.id"
                            required
                            class="w-full h-9 pl-9 pr-2 bg-slate-50 border border-slate-200 text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 transition-all"
                        >

                    </div>

                    @error('email')
                        <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                    @enderror

                </div>


                {{-- PASSWORD --}}

                <div>

                    <label for="password" class="block text-[10.5px] font-bold text-slate-500 tracking-wide mb-1">
                        PASSWORD *
                    </label>

                    <div class="relative flex items-center">

                        <span class="material-symbols-outlined absolute left-3 text-slate-400 pointer-events-none text-[16px]">
                            lock
                        </span>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Min. 8 karakter"
                            required
                            class="w-full h-9 pl-9 pr-8 bg-slate-50 border border-slate-200 text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 transition-all"
                        >

                        <button
                            type="button"
                            onclick="togglePassword('password', this)"
                            class="absolute right-2.5 text-slate-400 hover:text-slate-600"
                        >

                            <span class="material-symbols-outlined text-[16px]">
                                visibility
                            </span>

                        </button>

                    </div>

                    @error('password')
                        <p class="text-[10px] text-red-600 mt-0.5">{{ $message }}</p>
                    @enderror

                </div>


                {{-- KONFIRMASI PASSWORD --}}

                <div>

                    <label for="password_confirmation" class="block text-[10.5px] font-bold text-slate-500 tracking-wide mb-1">
                        KONFIRMASI *
                    </label>

                    <div class="relative flex items-center">

                        <span class="material-symbols-outlined absolute left-3 text-slate-400 pointer-events-none text-[16px]">
                            lock_reset
                        </span>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ulangi sandi"
                            required
                            class="w-full h-9 pl-9 pr-8 bg-slate-50 border border-slate-200 text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 transition-all"
                        >

                        <button
                            type="button"
                            onclick="togglePassword('password_confirmation', this)"
                            class="absolute right-2.5 text-slate-400 hover:text-slate-600"
                        >

                            <span class="material-symbols-outlined text-[16px]">
                                visibility
                            </span>

                        </button>

                    </div>

                </div>

            </div>


            {{-- PERSETUJUAN TATA TERTIB --}}

            <label class="flex items-start gap-2 cursor-pointer select-none">

                <input
                    type="checkbox"
                    name="terms"
                    required
                    class="w-3.5 h-3.5 mt-0.5 rounded accent-violet-600 shrink-0"
                >

                <span class="text-[10.5px] text-slate-500 leading-snug">

                    Saya menyetujui

                    <a href="#" class="text-violet-600 font-medium hover:underline">
                        SOP &amp; Tata Tertib Peminjaman Alat Lab RPL
                    </a>

                    dan bersedia menjaga seluruh fasilitas lab dengan penuh tanggung jawab.

                </span>

            </label>

            @error('terms')
                <p class="text-[10px] text-red-600">{{ $message }}</p>
            @enderror


            {{-- TOMBOL DAFTAR --}}

            <button
                id="btnDaftar"
                type="submit"
                class="w-full h-10 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-semibold flex items-center justify-center gap-2 shadow-md hover:shadow-lg hover:opacity-95 active:scale-[.98] transition-all text-sm"
            >

                <span class="spinner btn-spinner hidden"></span>

                <span class="btn-text">
                    Daftar Akun Sekarang
                </span>

                <span class="material-symbols-outlined text-[18px] btn-icon">
                    arrow_forward
                </span>

            </button>

        </form>


        {{-- LINK LOGIN --}}

        <p class="text-center text-[11px] text-slate-500 mt-2.5">

            Sudah memiliki akun?

            <a
                href="{{ route('login') }}"
                class="text-violet-600 font-semibold hover:underline"
            >
                Masuk di sini
            </a>

        </p>

    </div>

</div>


<script>
    /* ==================================
       ANIMASI LOGO MENGIKUTI KURSOR
       KHUSUS MOUSE / LAPTOP
    ================================== */

    (function () {

        const logo = document.getElementById('logoInventa');

        if (
            !logo ||
            window.matchMedia('(prefers-reduced-motion: reduce)').matches
        ) {
            return;
        }

        const RADIUS = 260;

        let mx = 0;
        let my = 0;
        let raf = null;


        function reset() {
            logo.style.transform = '';
            logo.style.filter = '';
        }


        function update() {

            raf = null;

            const r = logo.getBoundingClientRect();

            const dx = mx - (r.left + r.width / 2);
            const dy = my - (r.top + r.height / 2);

            const dist = Math.hypot(dx, dy);
            const near = Math.max(0, 1 - dist / RADIUS);

            if (near === 0) {
                reset();
                return;
            }

            const ux = dx / (dist || 1);
            const uy = dy / (dist || 1);

            logo.style.transform =
                'perspective(300px) rotateX(' +
                (-uy * 14 * near) +
                'deg) rotateY(' +
                (ux * 14 * near) +
                'deg) scale(' +
                (1 + 0.1 * near) +
                ')';

            logo.style.filter =
                'drop-shadow(0 0 ' +
                (14 * near) +
                'px rgba(124, 58, 237, ' +
                (0.55 * near) +
                '))';

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
       ANIMASI TOMBOL REGISTER
    ================================== */

    const formRegister = document.getElementById('formRegister');

    if (formRegister) {

        formRegister.addEventListener('submit', function () {

            const btn = document.getElementById('btnDaftar');

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


    /* ==================================
       PILIH JENIS PENGGUNA
    ================================== */

    function pilihTipe(tipe) {

        document.getElementById('tipe_user').value = tipe;

        const active =
            "flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg font-semibold text-xs transition-all bg-white text-violet-600 shadow-sm";

        const inactive =
            "flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg font-semibold text-xs transition-all text-slate-500 hover:text-slate-700";


        document.getElementById('btnSiswa').className =
            tipe === 'siswa' ? active : inactive;

        document.getElementById('btnGuru').className =
            tipe === 'guru' ? active : inactive;


        document.getElementById('fieldNis').classList.toggle(
            'hidden',
            tipe !== 'siswa'
        );

        document.getElementById('fieldKelas').classList.toggle(
            'hidden',
            tipe !== 'siswa'
        );

        document.getElementById('fieldNip').classList.toggle(
            'hidden',
            tipe !== 'guru'
        );


        document.getElementById('nis').required = tipe === 'siswa';

        document.getElementById('kelas').required = tipe === 'siswa';

        document.getElementById('nip').required = tipe === 'guru';

    }


    /* ==================================
       INISIALISASI JENIS PENGGUNA
    ================================== */

    pilihTipe(
        document.getElementById('tipe_user').value || 'siswa'
    );

</script>

@endsection

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#7c3aed">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard Siswa') — INVENTA</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/inventa-icon-v3.svg') }}">

    <script>
        try {
            if (!sessionStorage.getItem('inventaNavIntro')) {
                document.documentElement.classList.add('nav-intro');
                sessionStorage.setItem('inventaNavIntro', '1');
            }
        } catch (e) {}
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        display: ['"Plus Jakarta Sans"', 'sans-serif'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif']
                    },
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                        }
                    }
                }
            }
        };
    </script>

    <style>
        /* ===== BASE & SCROLLBAR ===== */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
            background-color: #f8f7fc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(124, 58, 237, 0.03) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(99, 102, 241, 0.03) 0px, transparent 50%);
            background-attachment: fixed;
        }

        /* Custom Scrollbar yang Premium */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c4b5fd; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #8b5cf6; }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            font-size: 20px;
            vertical-align: middle;
        }

        /* ===== ANIMASI ===== */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        @keyframes fadeDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: none; } }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-14px); } to { opacity: 1; transform: none; } }
        @keyframes pulseDot {
            0% { box-shadow: 0 0 0 0 rgba(16,185,129,.5); }
            70% { box-shadow: 0 0 0 6px rgba(16,185,129,0); }
            100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes toastIn { from { opacity: 0; transform: translateY(-10px) scale(.98); } to { opacity: 1; transform: none; } }

        header { animation: fadeDown .5s cubic-bezier(.22,1,.36,1) backwards; }
        main > * { animation: fadeUp .55s cubic-bezier(.22,1,.36,1) backwards; }
        main > *:nth-child(1) { animation-delay: .05s; }
        main > *:nth-child(2) { animation-delay: .12s; }
        main > *:nth-child(3) { animation-delay: .19s; }
        main > *:nth-child(4) { animation-delay: .26s; }
        main > *:nth-child(5) { animation-delay: .33s; }
        main > *:nth-child(6) { animation-delay: .40s; }
        main > *:nth-child(n+7) { animation-delay: .46s; }

        html.nav-intro #sidebar nav a { animation: slideIn .45s cubic-bezier(.22,1,.36,1) backwards; }
        html.nav-intro #sidebar nav a:nth-of-type(1) { animation-delay: .10s; }
        html.nav-intro #sidebar nav a:nth-of-type(2) { animation-delay: .16s; }
        html.nav-intro #sidebar nav a:nth-of-type(3) { animation-delay: .22s; }
        html.nav-intro #sidebar nav a:nth-of-type(4) { animation-delay: .28s; }
        html.nav-intro #sidebar nav a:nth-of-type(5) { animation-delay: .34s; }
        html.nav-intro #sidebar nav a:nth-of-type(6) { animation-delay: .40s; }
        html.nav-intro #sidebar nav a:nth-of-type(n+7) { animation-delay: .46s; }
        
        /* ===== NAVBAR SIDEBAR ===== */
        .nav-section-title {
            margin: 24px 0 8px;
            padding: 0 12px;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .nav-section-title:first-child { margin-top: 12px; }

        .nav-link {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 44px;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            color: #64748b;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 3px solid transparent;
        }

        .nav-link:not(.is-active):hover { 
            background: rgba(124, 58, 237, 0.06); 
            color: #6d28d9; 
            transform: translateX(2px);
        }

        .nav-link.is-active {
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.1) 0%, rgba(99, 102, 241, 0.05) 100%);
            color: #6d28d9;
            border-left: 3px solid #7c3aed;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.08);
        }

        .nav-link .nav-item-icon { flex-shrink: 0; font-size: 21px; transition: transform 0.2s; }
        .nav-link:hover .nav-item-icon { transform: scale(1.1); }

        /* ===== DROPDOWN & SIDEBAR DESKTOP ===== */
        #userProfileMenu { transform-origin: top right; animation: fadeDown .18s ease-out; }
        #userProfileMenu.hidden { display: none; }

        @media (min-width: 1024px) {
            #sidebar {
                width: 260px;
                min-width: 260px;
                flex-basis: 260px;
                overflow-x: visible;
                transition: width .3s cubic-bezier(0.4, 0, 0.2, 1), min-width .3s cubic-bezier(0.4, 0, 0.2, 1), flex-basis .3s cubic-bezier(0.4, 0, 0.2, 1);
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(12px);
                border-right: 1px solid #e2e8f0;
            }
            #sidebar.sidebar-collapsed { width: 88px; min-width: 88px; flex-basis: 88px; }
            #sidebar .sidebar-brand { padding: 20px 16px; }
            #sidebar .sidebar-brand-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; min-width: 0; }
            #sidebar .sidebar-logo-link { display: flex; align-items: center; justify-content: flex-start; min-width: 0; flex: 1; }
            
            #sidebar .sidebar-toggle {
                display: flex; flex: 0 0 32px; align-items: center; justify-content: center;
                width: 32px; height: 32px; padding: 0; border: 1px solid #e2e8f0; border-radius: 10px;
                background: white; color: #7c3aed; cursor: pointer;
                box-shadow: 0 2px 4px rgba(0,0,0,0.02);
                transition: all 0.2s;
            }
            #sidebar .sidebar-toggle:hover { background: #f5f3ff; color: #6d28d9; border-color: #c4b5fd; transform: scale(1.05); }

            #sidebar .sidebar-logo-icon { display: none; width: 38px; height: 38px; object-fit: contain; }

            #sidebar.sidebar-collapsed .sidebar-brand { padding: 18px 12px; }
            #sidebar.sidebar-collapsed .sidebar-brand-row { flex-direction: column; justify-content: center; gap: 12px; }
            #sidebar.sidebar-collapsed .sidebar-logo-link { flex: 0 0 auto; justify-content: center; width: 100%; }
            #sidebar.sidebar-collapsed #logoInventa { display: none; }
            #sidebar.sidebar-collapsed .sidebar-logo-icon { display: block; }
            #sidebar.sidebar-collapsed .sidebar-toggle { align-self: center; }
            #sidebar.sidebar-collapsed .sidebar-account { display: none; }
            #sidebar.sidebar-collapsed .nav-section-title { display: none; }

            #sidebar.sidebar-collapsed nav a { justify-content: center; gap: 0; padding-left: 0; padding-right: 0; position: relative; overflow: visible; border-left: none; }
            #sidebar.sidebar-collapsed nav a > .nav-label { display: none; }
            #sidebar.sidebar-collapsed nav a > .nav-item-icon { margin: 0; }
            
            #sidebar.sidebar-collapsed nav a[title]:hover::after {
                content: attr(title); position: absolute; left: calc(100% + 12px); top: 50%; transform: translateY(-50%);
                z-index: 100; padding: 8px 12px; border-radius: 8px; background: #1e1b2e; color: white;
                font-size: 11px; font-weight: 600; white-space: nowrap; pointer-events: none;
                box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            }
            #sidebar.sidebar-collapsed .sidebar-footer-status { justify-content: center; padding-left: 0; padding-right: 0; }
            #sidebar.sidebar-collapsed .status-label { display: none; }
        }

        /* ===== UTILITIES ===== */
        .dot-live { animation: pulseDot 2s ease-out infinite; }
        main button { transition: transform .12s ease, box-shadow .12s ease; }
        main button:not(:disabled):active { transform: scale(.97); }
        .btn-spinner-inline {
            display: inline-block; width: 14px; height: 14px; margin-right: 6px; vertical-align: middle;
            border: 2px solid currentColor; border-right-color: transparent; border-radius: 50%;
            animation: spin .7s linear infinite;
        }
        .logo-follow { display: block; transition: transform .15s ease-out, filter .2s ease-out; will-change: transform; }
        .toast-item { animation: toastIn .3s ease-out; transition: opacity .25s, transform .25s; }

        @media (max-width: 640px) { main .flex.items-center.gap-2 { flex-wrap: wrap; } }
        @media (max-width: 1023px) {
            input:not([type="file"]):not([type="checkbox"]):not([type="radio"]), select, textarea { font-size: 16px !important; }
            main input[type="file"] { flex: 1 1 0%; min-width: 0; max-width: 100%; font-size: 12px; }
            body { overflow-x: hidden; }
            main img, main video, main canvas { max-width: 100%; height: auto; }
        }

        @media (prefers-reduced-motion: reduce) {
            header, main > *, #sidebar nav a, .dot-live, .btn-spinner-inline, .toast-item { animation: none !important; }
            #sidebar nav a:not(.is-active):hover { transform: none; }
            .logo-follow { transform: none !important; filter: none !important; transition: none; }
            #sidebar, #sidebar .sidebar-toggle { transition: none !important; }
        }

        @media print {
            aside, header, nav, .no-print, [data-mobile-nav] { display: none !important; }
            main { padding: 0 !important; }
            body { background: white !important; }
            .bg-white { box-shadow: none !important; }
            *, *::before, *::after { animation: none !important; }
        }

        /* ===== PEMILIH ALAT DESKTOP ===== */
        @media (min-width: 1024px) {
            #sheetSelect {
                position: fixed !important;
                left: 50% !important;
                top: 50% !important;
                right: auto !important;
                bottom: auto !important;
                width: min(520px, calc(100vw - 48px));
                max-height: min(680px, 85vh);
                border-radius: 24px !important;
                padding-bottom: 16px !important;
                padding-top: 16px !important;
                transform: translate(-50%, 16px) scale(.98);
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
                transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
            }
            #sheetSelect.translate-y-0 {
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
                visibility: visible;
                pointer-events: auto;
            }
            #sheetSelectDaftar { min-height: 100px; max-height: 420px; }
            #sheetSelectPegangan { display: none !important; }
            #sheetSelectBackdrop { display: block !important; }
        }
    </style>

    @stack('styles')
</head>

<body class="text-slate-800 antialiased">

@php
    $user = auth()->user();
    $namaUser = $user->nama ?? $user->name ?? 'Pengguna';
    $tipeUser = $user->tipe_user === 'guru' ? 'Guru' : 'Siswa';
    $jabatanUser = $user->kelas ?? $user->jabatan ?? 'Anggota Lab RPL';

    $menuGroups = [
    [
        'title' => 'SISTEM',
        'items' => [
            ['route' => 'siswa.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
            ['route' => 'siswa.barang.index', 'icon' => 'inventory_2', 'label' => 'Cek Stok Alat'],
        ],
    ],
    [
        'title' => 'PEMINJAMAN',
        'items' => [
            ['route' => 'siswa.peminjaman.create', 'icon' => 'add_task', 'label' => 'Ajukan Peminjaman'],
            ['route' => 'siswa.peminjaman.index', 'icon' => 'history', 'label' => 'Riwayat Peminjaman'],
        ],
    ],
    [
        'title' => 'KERUSAKAN',
        'items' => [
            ['route' => 'siswa.laporan.create', 'icon' => 'report', 'label' => 'Lapor Kerusakan'],
            ['route' => 'siswa.laporan.index', 'icon' => 'history_edu', 'label' => 'Riwayat Kerusakan'],
        ],
    ],
];

    $aktifDashboard = request()->routeIs('siswa.dashboard');
    $aktifKatalog = request()->routeIs('siswa.barang.*');
    $aktifAjukan = request()->routeIs('siswa.peminjaman.create');
    $aktifRiwayat = request()->routeIs('siswa.peminjaman.*') && ! request()->routeIs('siswa.peminjaman.create');
    $aktifLaporan = request()->routeIs('siswa.laporan.*');
    $aktifProfil = request()->routeIs('siswa.profil.*');
    $aktifMenu = $aktifRiwayat || $aktifProfil;

    $ikonIsi = "font-variation-settings:'FILL' 1,'wght' 500,'GRAD' 0,'opsz' 24;";
@endphp

<div class="flex min-h-screen">

    {{-- ====================== SIDEBAR (Desktop) ====================== --}}
    <aside
        id="sidebar"
        class="hidden lg:flex lg:sticky top-0 shrink-0 flex-col justify-between h-screen overflow-y-auto"
        style="height:100dvh"
    >
        <div>
            {{-- LOGO --}}
            <div class="sidebar-brand px-5 py-5 border-b border-slate-100">
                <div class="sidebar-brand-row">
                    <a href="{{ route('siswa.dashboard') }}" class="sidebar-logo-link" aria-label="Dashboard INVENTA">
                        <img id="logoInventa" src="{{ asset('images/inventa-logo-v3.svg') }}" alt="INVENTA" class="logo-follow h-9 w-auto">
                        <img id="logoInventaIcon" src="{{ asset('images/inventa-icon-v3.svg') }}" alt="INVENTA" class="sidebar-logo-icon logo-follow">
                    </a>
                    <button type="button" id="sidebarToggle" class="sidebar-toggle" aria-label="Kecilkan sidebar" aria-expanded="true" title="Kecilkan sidebar">
                        <span id="sidebarToggleIcon" class="material-symbols-outlined text-[20px]">chevron_left</span>
                    </button>
                </div>
            </div>

            {{-- INFO AKUN (Original - Jangan Diubah) --}}
            <div class="sidebar-account flex items-center justify-between px-5 py-3 text-[11px]">
                <span class="text-slate-400">Akun</span>
                <span class="px-2 py-0.5 bg-violet-100 text-violet-700 font-bold rounded">{{ $tipeUser }}</span>
            </div>

            {{-- NAVBAR BERKATEGORI --}}
            <nav class="px-3 space-y-0.5 mt-2">
                @foreach ($menuGroups as $group)
                    <p class="nav-section-title">{{ $group['title'] }}</p>
                    @foreach ($group['items'] as $item)
                        @php
                            $routeName = $item['route'];
                            $routeExists = \Illuminate\Support\Facades\Route::has($routeName);
                            $active = false;

                            if ($routeExists) {
                                $active = request()->routeIs($routeName);
                                if ($routeName === 'siswa.barang.index') {
                                    $active = $active || request()->routeIs('siswa.barang.*');
                                } elseif ($routeName === 'siswa.peminjaman.index') {
                                    $active = $active || (request()->routeIs('siswa.peminjaman.*') && ! request()->routeIs('siswa.peminjaman.create'));
                                } elseif ($routeName === 'siswa.laporan.index') {
                                    $active = $active || (request()->routeIs('siswa.laporan.*') && ! request()->routeIs('siswa.laporan.create'));
                                }
                            }
                        @endphp

                        @if ($routeExists)
                            <a href="{{ route($routeName) }}" title="{{ $item['label'] }}"
                               class="nav-link {{ $active ? 'is-active' : '' }}">
                                <span class="material-symbols-outlined nav-item-icon">{{ $item['icon'] }}</span>
                                <span class="nav-label flex-1">{{ $item['label'] }}</span>
                            </a>
                        @endif
                    @endforeach
                @endforeach
            </nav>
        </div>

        {{-- FOOTER SIDEBAR --}}
        <div class="p-3">
            <div class="sidebar-footer-status flex items-center justify-between px-3 py-2.5 bg-slate-50/80 rounded-xl border border-slate-100">
                <span class="status-label flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <span class="status-dot w-1.5 h-1.5 rounded-full bg-emerald-500 dot-live"></span>
                    Sistem Aktif
                </span>
            </div>
        </div>
    </aside>

    {{-- ====================== AREA UTAMA ====================== --}}
    <div class="flex-1 min-w-0 flex flex-col">

        {{-- HEADER DESKTOP --}}
        <header class="hidden lg:flex items-center justify-between gap-3 px-6 py-3 bg-white/80 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-20">
            <form method="GET" action="{{ route('siswa.barang.index') }}" class="relative flex-1 min-w-0 max-w-sm">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input type="text" name="cari" placeholder="Cari alat atau komponen..."
                    class="w-full h-9 pl-9 pr-3 bg-slate-50/80 border border-slate-200 text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500 focus:bg-white transition-all">
            </form>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('siswa.laporan.index') }}"
                   class="relative w-10 h-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 hover:text-violet-600 hover:bg-violet-50 hover:border-violet-100 transition-all"
                   aria-label="Notifikasi laporan">
                    <span class="material-symbols-outlined text-[20px]">notifications</span>
                </a>

                {{-- PROFIL DAN DROPDOWN --}}
                <div class="relative" id="userProfileDropdown">
                    <button type="button" id="userProfileButton" onclick="toggleUserProfile()"
                        class="flex items-center gap-3 pl-3 pr-1 py-1 border-l border-slate-200 hover:bg-slate-50 rounded-full transition-all"
                        aria-expanded="false" aria-controls="userProfileMenu">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-violet-100 to-indigo-100 flex items-center justify-center text-violet-700 font-bold text-xs overflow-hidden shrink-0 ring-2 ring-white shadow-sm">
                            @if ($user->foto)
                                <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto profil" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($namaUser, 0, 1)) }}
                            @endif
                        </div>
                        <div class="leading-tight text-left pr-1">
                            <div class="text-xs font-bold text-slate-800">{{ $namaUser }}</div>
                            <div class="text-[10px] text-slate-500 font-medium">{{ $jabatanUser }}</div>
                        </div>
                        <span class="material-symbols-outlined text-slate-400 text-[18px]">keyboard_arrow_down</span>
                    </button>

                    <div id="userProfileMenu" class="hidden absolute right-0 top-full mt-3 w-64 rounded-2xl border border-slate-100 bg-white p-2 shadow-xl shadow-slate-200/50 z-50">
                        <div class="px-3 py-3 border-b border-slate-100 mb-1">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-xl overflow-hidden bg-gradient-to-br from-violet-100 to-indigo-100 flex items-center justify-center text-violet-700 font-bold shrink-0 ring-2 ring-white shadow-sm">
                                    @if ($user->foto)
                                        <img src="{{ asset('storage/' . $user->foto) }}" alt="Foto profil" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($namaUser, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ $namaUser }}</p>
                                    <p class="text-[11px] text-violet-600 font-semibold mt-0.5">{{ $tipeUser }} · {{ $jabatanUser }}</p>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('siswa.profil.edit') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-violet-50 hover:text-violet-700 transition">
                            <span class="material-symbols-outlined text-[18px]">person</span>
                            Profil Saya
                        </a>

                        <div class="my-1 border-t border-slate-100"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" data-no-spinner
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs font-semibold text-red-500 hover:bg-red-50 transition">
                                <span class="material-symbols-outlined text-[18px]">logout</span>
                                Keluar dari Akun
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- HEADER RINGKAS (HP) --}}
        <header data-mobile-nav class="lg:hidden sticky top-0 z-40 bg-white/85 backdrop-blur-xl border-b border-slate-200/60 pt-[env(safe-area-inset-top)]">
            <div class="flex items-center justify-between px-4 h-14">
                <a href="{{ route('siswa.dashboard') }}" aria-label="Dashboard INVENTA" class="flex items-center">
                    <img src="{{ asset('images/inventa-logo-v3.svg') }}" alt="INVENTA" class="h-8 w-auto">
                </a>
                <div class="flex items-center gap-2">
                    <a href="{{ route('siswa.laporan.index') }}"
                       class="w-9 h-9 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 active:scale-90 transition"
                       aria-label="Notifikasi laporan">
                        <span class="material-symbols-outlined text-[19px]">notifications</span>
                    </a>
                    <a href="{{ route('siswa.profil.edit') }}"
                       class="w-9 h-9 rounded-full bg-gradient-to-br from-violet-100 to-indigo-100 flex items-center justify-center text-violet-700 font-bold text-xs overflow-hidden ring-2 ring-white shadow-sm active:scale-90 transition"
                       aria-label="Profil saya">
                        @if ($user->foto)
                            <img src="{{ asset('storage/' . $user->foto) }}" alt="" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($namaUser, 0, 1)) }}
                        @endif
                    </a>
                </div>
            </div>
        </header>

        {{-- KONTEN --}}
        <main class="p-4 sm:p-6 pb-28 lg:pb-6">
            {{-- STATUS VERIFIKASI AKUN --}}
            @php
                $linkVerifikasi = route('siswa.profil.edit') . '#verifikasi';
                $dataLengkap = filled($user->foto) && filled($user->no_hp);
            @endphp

            @if (! request()->routeIs('siswa.profil.*'))
                @if ($user->status_akun === 'menunggu')
                    <a href="{{ $linkVerifikasi }}"
                       class="mb-4 flex items-start gap-2.5 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm hover:bg-amber-100 transition">
                        <span class="material-symbols-outlined text-[20px] shrink-0">verified_user</span>
                        <span>
                            <b>Akun belum diverifikasi.</b>
                            {{ $dataLengkap
                                ? 'Data sudah lengkap dan terkirim, tunggu petugas lab memverifikasi.'
                                : 'Lengkapi data (No. HP dan foto profil) lewat Edit Profil supaya petugas lab bisa memverifikasi.' }}
                            Selama itu kamu belum bisa meminjam alat.
                        </span>
                    </a>
                @elseif ($user->status_akun === 'ditolak')
                    <a href="{{ $linkVerifikasi }}"
                       class="mb-4 flex items-start gap-2.5 p-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm hover:bg-red-100 transition">
                        <span class="material-symbols-outlined text-[20px] shrink-0">gpp_bad</span>
                        <span>
                            <b>Verifikasi akun ditolak.</b>
                            {{ $user->catatan_verifikasi ?: 'Data akun belum sesuai.' }}
                            Klik untuk memperbaiki di Profil Saya.
                        </span>
                    </a>
                @endif
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- ====================== NOTIFIKASI (toast) ====================== --}}
<div class="fixed inset-x-3 top-3 z-[80] space-y-2 pointer-events-none lg:inset-x-auto lg:right-6 lg:w-96 pt-[env(safe-area-inset-top)] no-print">
    @if (session('success'))
        <div data-toast class="toast-item pointer-events-auto flex items-start gap-3 p-4 rounded-2xl bg-white border border-emerald-100 shadow-lg shadow-emerald-500/10">
            <span class="material-symbols-outlined text-emerald-500 text-[22px] mt-0.5">check_circle</span>
            <p class="flex-1 text-sm text-slate-700 leading-snug font-medium">{{ session('success') }}</p>
            <button type="button" data-toast-close class="text-slate-300 hover:text-slate-500 transition" aria-label="Tutup">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div data-toast class="toast-item pointer-events-auto flex items-start gap-3 p-4 rounded-2xl bg-white border border-red-100 shadow-lg shadow-red-500/10">
            <span class="material-symbols-outlined text-red-500 text-[22px] mt-0.5">error</span>
            <p class="flex-1 text-sm text-slate-700 leading-snug font-medium">{{ session('error') }}</p>
            <button type="button" data-toast-close class="text-slate-300 hover:text-slate-500 transition" aria-label="Tutup">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
    @endif
</div>

{{-- ====================== TAB BAR BAWAH (HP) ====================== --}}
<nav data-mobile-nav aria-label="Navigasi utama"
     class="lg:hidden fixed inset-x-0 bottom-0 z-50 bg-white/90 backdrop-blur-xl border-t border-slate-200/60 pb-[env(safe-area-inset-bottom)] shadow-[0_-4px_20px_rgba(15,23,42,0.04)]">
    <div class="grid grid-cols-5 items-end px-2 pt-2 pb-1">
        <a href="{{ route('siswa.dashboard') }}" data-haptic
           class="relative flex flex-col items-center gap-0.5 py-1.5 transition active:scale-90 {{ $aktifDashboard ? 'text-violet-600' : 'text-slate-400' }}">
            <span class="absolute -top-1.5 h-[3px] rounded-full bg-violet-600 transition-all duration-300 {{ $aktifDashboard ? 'w-6 opacity-100' : 'w-2 opacity-0' }}"></span>
            <span class="material-symbols-outlined" style="font-size:24px; {{ $aktifDashboard ? $ikonIsi : '' }}">dashboard</span>
            <span class="text-[10px] font-semibold">Dashboard</span>
        </a>

        <a href="{{ route('siswa.barang.index') }}" data-haptic
           class="relative flex flex-col items-center gap-0.5 py-1.5 transition active:scale-90 {{ $aktifKatalog ? 'text-violet-600' : 'text-slate-400' }}">
            <span class="absolute -top-1.5 h-[3px] rounded-full bg-violet-600 transition-all duration-300 {{ $aktifKatalog ? 'w-6 opacity-100' : 'w-2 opacity-0' }}"></span>
            <span class="material-symbols-outlined" style="font-size:24px; {{ $aktifKatalog ? $ikonIsi : '' }}">inventory_2</span>
            <span class="text-[10px] font-semibold">Katalog</span>
        </a>

        <a href="{{ route('siswa.peminjaman.create') }}" data-haptic
           class="relative -mt-8 flex flex-col items-center gap-1 transition active:scale-90 {{ $aktifAjukan ? 'text-violet-600' : 'text-slate-400' }}">
            <span class="relative flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-violet-600 to-indigo-600 text-white shadow-lg shadow-violet-500/40 ring-4 ring-[#f8f7fc]">
                <span class="material-symbols-outlined" style="font-size:28px;">add_task</span>
            </span>
            <span class="text-[10px] font-bold">Ajukan</span>
        </a>

        <a href="{{ route('siswa.laporan.create') }}" data-haptic
           class="relative flex flex-col items-center gap-0.5 py-1.5 transition active:scale-90 {{ $aktifLaporan ? 'text-violet-600' : 'text-slate-400' }}">
            <span class="absolute -top-1.5 h-[3px] rounded-full bg-violet-600 transition-all duration-300 {{ $aktifLaporan ? 'w-6 opacity-100' : 'w-2 opacity-0' }}"></span>
            <span class="material-symbols-outlined" style="font-size:24px; {{ $aktifLaporan ? $ikonIsi : '' }}">report</span>
            <span class="text-[10px] font-semibold">Lapor</span>
        </a>

        <button type="button" id="btnMenu" data-haptic aria-haspopup="dialog" aria-controls="drawerMenu"
                class="relative flex flex-col items-center gap-0.5 py-1.5 transition active:scale-90 {{ $aktifMenu ? 'text-violet-600' : 'text-slate-400' }}">
            <span class="absolute -top-1.5 h-[3px] rounded-full bg-violet-600 transition-all duration-300 {{ $aktifMenu ? 'w-6 opacity-100' : 'w-2 opacity-0' }}"></span>
            <span class="material-symbols-outlined" style="font-size:24px;">apps</span>
            <span class="text-[10px] font-semibold">Menu</span>
        </button>
    </div>
</nav>

{{-- ====================== LACI MENU (HP) ====================== --}}
<div id="drawerBackdrop" data-mobile-nav
     class="lg:hidden fixed inset-0 z-[60] bg-slate-900/40 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300"></div>

<div id="drawerMenu" data-mobile-nav role="dialog" aria-modal="true" aria-label="Menu lainnya"
     class="lg:hidden fixed inset-x-0 bottom-0 z-[70] translate-y-full transition-transform duration-300 ease-out
            rounded-t-3xl bg-white shadow-2xl shadow-slate-900/20 pb-[max(1rem,env(safe-area-inset-bottom))]">
    
    <div id="drawerHandle" class="pt-3 pb-2 flex justify-center cursor-grab">
        <span class="h-1.5 w-12 rounded-full bg-slate-200"></span>
    </div>

    <div class="px-5 pb-2">
        <a href="{{ route('siswa.profil.edit') }}" aria-label="Buka Profil Saya"
           class="flex items-center gap-3 p-3 rounded-2xl border transition active:scale-[.98] {{ $aktifProfil ? 'bg-violet-50 border-violet-200' : 'bg-gradient-to-br from-violet-50 to-indigo-50 border-violet-100' }}">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-violet-100 to-indigo-100 flex items-center justify-center text-violet-700 font-bold overflow-hidden ring-2 ring-white shadow-sm shrink-0">
                @if ($user->foto)
                    <img src="{{ asset('storage/' . $user->foto) }}" alt="" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($namaUser, 0, 1)) }}
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-slate-900 truncate">{{ $namaUser }}</p>
                <p class="text-xs text-slate-500 truncate">{{ $jabatanUser }}</p>
                @if ($user->status_akun === 'terverifikasi')
                    <span class="inline-flex items-center gap-0.5 mt-1 text-[10px] font-semibold text-emerald-600">
                        <span class="material-symbols-outlined" style="font-size:13px;">verified</span> Terverifikasi
                    </span>
                @endif
            </div>
            <span class="px-2.5 py-1 bg-violet-600 text-white text-[10px] font-bold rounded-full shadow-sm shadow-violet-200">{{ $tipeUser }}</span>
            <span class="material-symbols-outlined text-violet-400 text-[20px]">chevron_right</span>
        </a>

        <div class="mt-4 space-y-1">
            <a href="{{ route('siswa.peminjaman.index') }}"
               class="flex items-center gap-3 px-3 py-3 rounded-2xl transition active:scale-[.98] {{ $aktifRiwayat ? 'bg-violet-50 text-violet-700' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <span class="material-symbols-outlined">history</span>
                </span>
                <span class="flex-1 text-sm font-semibold">Riwayat Peminjaman</span>
                <span class="material-symbols-outlined text-slate-300 text-[20px]">chevron_right</span>
            </a>

            <a href="{{ route('siswa.laporan.index') }}"
               class="flex items-center gap-3 px-3 py-3 rounded-2xl transition active:scale-[.98] {{ request()->routeIs('siswa.laporan.index') ? 'bg-violet-50 text-violet-700' : 'text-slate-700 hover:bg-slate-50' }}">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <span class="material-symbols-outlined">assignment_late</span>
                </span>
                <span class="flex-1 text-sm font-semibold">Riwayat Laporan</span>
                <span class="material-symbols-outlined text-slate-300 text-[20px]">chevron_right</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" data-no-spinner
                        class="w-full flex items-center gap-3 px-3 py-3 rounded-2xl text-red-600 hover:bg-red-50 transition mt-2">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-50">
                        <span class="material-symbols-outlined">logout</span>
                    </span>
                    <span class="flex-1 text-left text-sm font-semibold">Keluar Sistem</span>
                </button>
            </form>
        </div>

        <button type="button" data-close-drawer
                class="mt-4 w-full py-3.5 rounded-2xl bg-slate-100 text-slate-600 text-sm font-bold hover:bg-slate-200 transition">
            Tutup
        </button>
    </div>
</div>

<script>
    /* ===== SIDEBAR DESKTOP: KECILKAN / BESARKAN ===== */
    (function () {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebarToggle');
        const toggleIcon = document.getElementById('sidebarToggleIcon');
        const desktop = window.matchMedia('(min-width: 1024px)');
        const storageKey = 'inventaSidebarCollapsed';

        if (!sidebar || !toggle || !toggleIcon) return;

        function setCollapsed(collapsed) {
            if (!desktop.matches) { sidebar.classList.remove('sidebar-collapsed'); return; }
            sidebar.classList.toggle('sidebar-collapsed', collapsed);
            toggleIcon.textContent = collapsed ? 'chevron_right' : 'chevron_left';
            toggle.setAttribute('aria-label', collapsed ? 'Besarkan sidebar' : 'Kecilkan sidebar');
            toggle.setAttribute('title', collapsed ? 'Besarkan sidebar' : 'Kecilkan sidebar');
            toggle.setAttribute('aria-expanded', String(!collapsed));
            try { localStorage.setItem(storageKey, String(collapsed)); } catch (e) {}
        }

        function restoreSidebar() {
            if (!desktop.matches) { sidebar.classList.remove('sidebar-collapsed'); return; }
            let collapsed = false;
            try { collapsed = localStorage.getItem(storageKey) === 'true'; } catch (e) {}
            setCollapsed(collapsed);
        }

        toggle.addEventListener('click', function () {
            if (!desktop.matches) return;
            setCollapsed(!sidebar.classList.contains('sidebar-collapsed'));
        });

        if (desktop.addEventListener) { desktop.addEventListener('change', restoreSidebar); } 
        else { desktop.addListener(restoreSidebar); }
        restoreSidebar();
    })();

    /* ===== DROPDOWN PROFIL (DESKTOP) ===== */
    function toggleUserProfile() {
        const menu = document.getElementById('userProfileMenu');
        const button = document.getElementById('userProfileButton');
        if (!menu || !button) return;
        const akanDibuka = menu.classList.contains('hidden');
        menu.classList.toggle('hidden', !akanDibuka);
        button.setAttribute('aria-expanded', String(akanDibuka));
    }

    document.addEventListener('click', function (event) {
        const container = document.getElementById('userProfileDropdown');
        const menu = document.getElementById('userProfileMenu');
        const button = document.getElementById('userProfileButton');
        if (container && !container.contains(event.target)) {
            if (menu) menu.classList.add('hidden');
            if (button) button.setAttribute('aria-expanded', 'false');
        }
    });

    /* ===== ANIMASI LOGO MENGIKUTI KURSOR (DESKTOP) ===== */
    (function () {
        const sidebar = document.getElementById('sidebar');
        const logoFull = document.getElementById('logoInventa');
        const logoIcon = document.getElementById('logoInventaIcon');
        if (!sidebar || !logoFull || !logoIcon || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        const RADIUS = 220;
        let mx = 0, my = 0, raf = null;

        function getVisibleLogo() {
            const iconVisible = window.matchMedia('(min-width: 1024px)').matches && sidebar.classList.contains('sidebar-collapsed');
            return iconVisible ? logoIcon : logoFull;
        }

        function resetLogo(logo) { if (!logo) return; logo.style.transform = ''; logo.style.filter = ''; }

        function update() {
            raf = null;
            const logo = getVisibleLogo();
            if (!logo || !logo.getClientRects().length) { resetLogo(logoFull); resetLogo(logoIcon); return; }

            const rect = logo.getBoundingClientRect();
            const dx = mx - (rect.left + rect.width / 2);
            const dy = my - (rect.top + rect.height / 2);
            const dist = Math.hypot(dx, dy);
            const near = Math.max(0, 1 - dist / RADIUS);

            if (near === 0) { resetLogo(logo); return; }

            const ux = dx / (dist || 1);
            const uy = dy / (dist || 1);

            logo.style.transform = 'perspective(300px) rotateX(' + (-uy * 12 * near) + 'deg) rotateY(' + (ux * 12 * near) + 'deg) scale(' + (1 + 0.08 * near) + ')';
            logo.style.filter = 'drop-shadow(0 0 ' + (12 * near) + 'px rgba(124, 58, 237, ' + (0.5 * near) + '))';
        }

        window.addEventListener('pointermove', function (e) {
            if (e.pointerType && e.pointerType !== 'mouse') return;
            mx = e.clientX; my = e.clientY;
            if (!raf) raf = requestAnimationFrame(update);
        });
        document.addEventListener('mouseleave', function () { resetLogo(logoFull); resetLogo(logoIcon); });
    })();

    /* ===== SPINNER FORM ===== */
    document.addEventListener('submit', function (e) {
        const form = e.target;
        const btn = e.submitter;
        if (e.defaultPrevented || !btn || btn.dataset.noSpinner !== undefined) return;
        if (form.target === '_blank') return;

        const salinan = btn.cloneNode(true);
        salinan.querySelectorAll('.material-symbols-outlined').forEach(function (el) { el.remove(); });
        if (salinan.textContent.trim() === '') return;

        setTimeout(function () {
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-wait');
            const spin = document.createElement('span');
            spin.className = 'btn-spinner-inline';
            btn.prepend(spin);
        }, 0);
    });

    /* ===== PEMULIHAN HALAMAN DARI CACHE ===== */
    window.addEventListener('pageshow', function (e) { if (e.persisted) location.reload(); });

    /* ===== HP: LACI MENU, GETARAN, TOAST ===== */
    (function () {
        const drawer = document.getElementById('drawerMenu');
        const backdrop = document.getElementById('drawerBackdrop');
        const tombolMenu = document.getElementById('btnMenu');
        const pegangan = document.getElementById('drawerHandle');
        let laciTerbuka = false;

        function bukaLaci() {
            if (!drawer) return;
            laciTerbuka = true;
            drawer.classList.remove('translate-y-full');
            drawer.classList.add('translate-y-0');
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            backdrop.classList.add('opacity-100');
            document.body.classList.add('overflow-hidden');
        }

        function tutupLaci() {
            if (!drawer || !laciTerbuka) return;
            laciTerbuka = false;
            drawer.classList.add('translate-y-full');
            drawer.classList.remove('translate-y-0');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            backdrop.classList.remove('opacity-100');
            document.body.classList.remove('overflow-hidden');
        }

        if (tombolMenu) tombolMenu.addEventListener('click', bukaLaci);
        if (backdrop) backdrop.addEventListener('click', tutupLaci);
        document.querySelectorAll('[data-close-drawer]').forEach(function (el) { el.addEventListener('click', tutupLaci); });

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'Escape') return;
            tutupLaci();
            const menu = document.getElementById('userProfileMenu');
            const button = document.getElementById('userProfileButton');
            if (menu) menu.classList.add('hidden');
            if (button) button.setAttribute('aria-expanded', 'false');
        });

        let awalY = null;
        if (pegangan && drawer) {
            pegangan.addEventListener('touchstart', function (e) { awalY = e.touches[0].clientY; }, { passive: true });
            pegangan.addEventListener('touchmove', function (e) {
                if (awalY === null) return;
                const selisih = e.touches[0].clientY - awalY;
                if (selisih > 0) { drawer.style.transition = 'none'; drawer.style.transform = 'translateY(' + selisih + 'px)'; }
            }, { passive: true });
            pegangan.addEventListener('touchend', function (e) {
                if (awalY === null) return;
                const selisih = e.changedTouches[0].clientY - awalY;
                drawer.style.transition = ''; drawer.style.transform = ''; awalY = null;
                if (selisih > 70) tutupLaci();
            });
        }

        document.querySelectorAll('[data-haptic]').forEach(function (el) {
            el.addEventListener('click', function () { if (navigator.vibrate) navigator.vibrate(8); });
        });

        function tutupToast(t) {
            t.style.opacity = '0'; t.style.transform = 'translateY(-8px)';
            setTimeout(function () { t.remove(); }, 250);
        }
        document.querySelectorAll('[data-toast]').forEach(function (t) {
            setTimeout(function () { tutupToast(t); }, 5000);
            const tombol = t.querySelector('[data-toast-close]');
            if (tombol) tombol.addEventListener('click', function () { tutupToast(t); });
        });
    })();
</script>

{{-- ====================== PEMILIH ALAT HP & DESKTOP ====================== --}}
<script>
(function () {
    const daftarSelect = document.querySelectorAll('select[data-sheet-select]');
    if (!daftarSelect.length) return;

    const mobile = window.matchMedia('(max-width: 1023px)');

    function esc(teks) {
        const d = document.createElement('div');
        d.textContent = teks;
        return d.innerHTML;
    }

    const wadah = document.createElement('div');
    wadah.innerHTML =
        '<div id="sheetSelectBackdrop" class="fixed inset-0 z-[100] bg-slate-900/40 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300"></div>' +
        '<div id="sheetSelect" role="dialog" aria-modal="true" aria-labelledby="sheetSelectJudul" ' +
            'class="fixed inset-x-0 bottom-0 z-[110] translate-y-full transition-transform duration-300 ease-out flex flex-col max-h-[80dvh] rounded-t-3xl bg-white shadow-2xl pb-[max(1rem,env(safe-area-inset-bottom))]">' +
            '<div id="sheetSelectPegangan" class="pt-3 pb-2 flex justify-center cursor-grab shrink-0">' +
                '<span class="h-1.5 w-12 rounded-full bg-slate-200"></span>' +
            '</div>' +
            '<div class="px-5 pb-3 flex items-center gap-2 shrink-0">' +
                '<button type="button" id="sheetSelectKembali" aria-label="Kembali ke daftar kategori" class="hidden w-9 h-9 shrink-0 rounded-full bg-slate-100 items-center justify-center text-slate-600">' +
                    '<span class="material-symbols-outlined">arrow_back</span>' +
                '</button>' +
                '<div class="min-w-0 flex-1">' +
                    '<h3 id="sheetSelectJudul" class="font-display font-bold text-base text-slate-900 truncate">Pilih</h3>' +
                    '<p id="sheetSelectSub" class="text-xs text-slate-500 truncate"></p>' +
                '</div>' +
                '<button type="button" data-sheet-tutup aria-label="Tutup" class="w-9 h-9 shrink-0 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">' +
                    '<span class="material-symbols-outlined">close</span>' +
                '</button>' +
            '</div>' +
            '<div id="sheetSelectCariWrap" class="px-5 pb-3 shrink-0 hidden">' +
                '<input id="sheetSelectCari" type="search" placeholder="Cari..." autocomplete="off" class="w-full h-11 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500">' +
            '</div>' +
            '<div id="sheetSelectDaftar" class="overflow-y-auto overscroll-contain px-3 pb-2 space-y-1"></div>' +
        '</div>';

    document.body.appendChild(wadah);

    const backdrop = document.getElementById('sheetSelectBackdrop');
    const sheet = document.getElementById('sheetSelect');
    const judul = document.getElementById('sheetSelectJudul');
    const sub = document.getElementById('sheetSelectSub');
    const tombolKembali = document.getElementById('sheetSelectKembali');
    const cariWrap = document.getElementById('sheetSelectCariWrap');
    const cari = document.getElementById('sheetSelectCari');
    const daftar = document.getElementById('sheetSelectDaftar');
    const pegangan = document.getElementById('sheetSelectPegangan');

    let aktif = null;
    let data = null;
    let langkah = 'kategori';
    let kelompokAktif = null;
    let terbuka = false;

    function bacaData(select) {
        const kelompok = [];
        const lepas = [];
        Array.from(select.children).forEach(function (anak) {
            if (anak.tagName === 'OPTGROUP') {
                const opsi = Array.from(anak.children).filter(function (o) {
                    return o.tagName === 'OPTION' && !o.disabled && o.value !== '';
                });
                if (opsi.length) {
                    kelompok.push({ label: anak.label, opsi: opsi });
                }
            } else if (anak.tagName === 'OPTION' && !anak.disabled && anak.value !== '') {
                lepas.push(anak);
            }
        });

        const berkelompok = kelompok.length > 0;
        if (berkelompok && lepas.length) {
            kelompok.push({ label: 'Lainnya', opsi: lepas });
        }

        const semua = berkelompok ? kelompok.reduce(function (acc, k) { return acc.concat(k.opsi); }, []) : lepas;

        return { berkelompok: berkelompok, kelompok: kelompok, semua: semua };
    }

    function teksOpsi(opt) { return opt.textContent.replace(/\s+/g, ' ').trim(); }
    function labelKelompokDari(opt) {
        return opt.parentElement && opt.parentElement.tagName === 'OPTGROUP' ? opt.parentElement.label : '';
    }
    function sembunyikanKembali() { tombolKembali.classList.add('hidden'); tombolKembali.classList.remove('flex'); }
    function tampilkanKembali() { tombolKembali.classList.remove('hidden'); tombolKembali.classList.add('flex'); }

    function barisOpsi(opt, tampilKategori) {
        const teks = teksOpsi(opt);
        const bagian = teks.split(' — ');
        const nama = bagian.shift();
        let keterangan = bagian.join(' — ');
        if (tampilKategori) {
            const kat = labelKelompokDari(opt);
            if (kat) keterangan = kat + (keterangan ? ' • ' + keterangan : '');
        }
        const terpilih = opt.value === aktif.value;
        const tombol = document.createElement('button');
        tombol.type = 'button';
        tombol.className = 'w-full flex items-center gap-3 px-3 py-3 rounded-2xl text-left transition active:scale-[.98] ' + (terpilih ? 'bg-violet-50' : 'hover:bg-slate-50');
        tombol.innerHTML =
            '<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl ' + (terpilih ? 'bg-violet-600 text-white' : 'bg-slate-100 text-slate-500') + '">' +
                '<span class="material-symbols-outlined">' + (terpilih ? 'check' : 'devices') + '</span>' +
            '</span>' +
            '<span class="min-w-0 flex-1">' +
                '<span class="block text-sm font-semibold text-slate-800 leading-snug">' + esc(nama) + '</span>' +
                (keterangan ? '<span class="block text-xs text-slate-500 mt-0.5">' + esc(keterangan) + '</span>' : '') +
            '</span>';
        tombol.addEventListener('click', function () {
            aktif.value = opt.value;
            aktif.dispatchEvent(new Event('change', { bubbles: true }));
            if (navigator.vibrate) navigator.vibrate(8);
            tutup();
        });
        return tombol;
    }

    function barisKategori(kelompok) {
        const dipilih = kelompok.opsi.find(function (o) { return o.value === aktif.value; });
        let keterangan = kelompok.opsi.length + ' alat';
        if (dipilih) keterangan += ' • Dipilih: ' + teksOpsi(dipilih).split(' — ')[0];
        
        const tombol = document.createElement('button');
        tombol.type = 'button';
        tombol.className = 'w-full flex items-center gap-3 px-3 py-3 rounded-2xl text-left transition active:scale-[.98] ' + (dipilih ? 'bg-violet-50' : 'hover:bg-slate-50');
        tombol.innerHTML =
            '<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl ' + (dipilih ? 'bg-violet-600 text-white' : 'bg-violet-100 text-violet-600') + '">' +
                '<span class="material-symbols-outlined">category</span>' +
            '</span>' +
            '<span class="min-w-0 flex-1">' +
                '<span class="block text-sm font-semibold text-slate-800 leading-snug">' + esc(kelompok.label) + '</span>' +
                '<span class="block text-xs text-slate-500 mt-0.5">' + esc(keterangan) + '</span>' +
            '</span>' +
            '<span class="material-symbols-outlined text-slate-300 text-[20px]">chevron_right</span>';
        tombol.addEventListener('click', function () {
            langkah = 'alat';
            kelompokAktif = kelompok;
            cari.value = '';
            render();
            daftar.scrollTop = 0;
        });
        return tombol;
    }

    function render() {
        const q = cari.value.trim().toLowerCase();
        const judulAwal = aktif.dataset.sheetTitle || 'Pilih';
        daftar.innerHTML = '';
        let butir = [];
        let tampilKategori = false;

        if (!data.berkelompok) {
            sembunyikanKembali();
            judul.textContent = judulAwal;
            sub.textContent = '';
            butir = data.semua;
        } else if (langkah === 'kategori' && !q) {
            sembunyikanKembali();
            judul.textContent = judulAwal;
            sub.textContent = 'Pilih kategori dulu';
            data.kelompok.forEach(function (k) { daftar.appendChild(barisKategori(k)); });
            return;
        } else if (langkah === 'kategori' && q) {
            sembunyikanKembali();
            judul.textContent = judulAwal;
            sub.textContent = 'Hasil pencarian di semua kategori';
            butir = data.semua;
            tampilKategori = true;
        } else {
            tampilkanKembali();
            judul.textContent = kelompokAktif.label;
            sub.textContent = kelompokAktif.opsi.length + ' alat';
            butir = kelompokAktif.opsi;
        }

        let jumlah = 0;
        butir.forEach(function (opt) {
            if (q && teksOpsi(opt).toLowerCase().indexOf(q) === -1) return;
            daftar.appendChild(barisOpsi(opt, tampilKategori));
            jumlah++;
        });

        if (jumlah === 0) {
            daftar.innerHTML = '<p class="px-3 py-8 text-center text-sm text-slate-400">Tidak ada yang cocok.</p>';
        }
    }

    function buka(select) {
        aktif = select;
        data = bacaData(select);
        langkah = 'kategori';
        kelompokAktif = null;
        cariWrap.classList.toggle('hidden', data.semua.length <= 6);
        cari.value = '';
        cari.placeholder = data.berkelompok ? 'Cari alat di semua kategori...' : 'Cari...';
        render();
        terbuka = true;
        sheet.classList.remove('translate-y-full');
        sheet.classList.add('translate-y-0');
        backdrop.classList.remove('opacity-0', 'pointer-events-none');
        backdrop.classList.add('opacity-100');
        document.body.classList.add('overflow-hidden');
    }

    function tutup() {
        if (!terbuka) return;
        terbuka = false;
        sheet.classList.add('translate-y-full');
        sheet.classList.remove('translate-y-0');
        backdrop.classList.add('opacity-0', 'pointer-events-none');
        backdrop.classList.remove('opacity-100');
        document.body.classList.remove('overflow-hidden');
    }

    backdrop.addEventListener('click', tutup);
    sheet.querySelector('[data-sheet-tutup]').addEventListener('click', tutup);
    cari.addEventListener('input', render);
    tombolKembali.addEventListener('click', function () {
        langkah = 'kategori';
        kelompokAktif = null;
        cari.value = '';
        render();
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') tutup(); });

    const onUbah = function () { if (!mobile.matches) tutup(); };
    if (mobile.addEventListener) mobile.addEventListener('change', onUbah); else mobile.addListener(onUbah);

    let awalY = null;
    pegangan.addEventListener('touchstart', function (e) { awalY = e.touches[0].clientY; }, { passive: true });
    pegangan.addEventListener('touchmove', function (e) {
        if (awalY === null) return;
        const selisih = e.touches[0].clientY - awalY;
        if (selisih > 0) { sheet.style.transition = 'none'; sheet.style.transform = 'translateY(' + selisih + 'px)'; }
    }, { passive: true });
    pegangan.addEventListener('touchend', function (e) {
        if (awalY === null) return;
        const selisih = e.changedTouches[0].clientY - awalY;
        sheet.style.transition = ''; sheet.style.transform = ''; awalY = null;
        if (selisih > 70) tutup();
    });

    daftarSelect.forEach(function (select) {
        const pembungkus = document.createElement('div');
        pembungkus.className = 'relative';
        select.parentNode.insertBefore(pembungkus, select);
        pembungkus.appendChild(select);
        const lapis = document.createElement('button');
        lapis.type = 'button';
        lapis.className = 'absolute inset-0 z-10 w-full rounded-xl cursor-pointer';
        lapis.setAttribute('aria-haspopup', 'dialog');
        lapis.setAttribute('aria-label', select.dataset.sheetTitle || 'Buka pilihan');
        lapis.addEventListener('click', function () { buka(select); });
        pembungkus.appendChild(lapis);
    });
})();
</script>

@include('layouts._zoom')
@stack('scripts')
</body>
</html>
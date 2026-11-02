@extends('layouts.siswa')

@section('title', 'Cek Stok Alat')

@section('content')

{{-- Header --}}
<div class="mb-6">
    <p class="text-[11px] font-semibold text-violet-600 tracking-wide mb-1">
        INVENTA // KATALOG LAB RPL
    </p>

    <h1 class="font-display font-bold text-2xl text-slate-900">
        Katalog & Cek Stok Alat
    </h1>

    <p class="text-sm text-slate-500 mt-1">
        Lihat ketersediaan alat praktikum sebelum mengajukan peminjaman.
    </p>
</div>

{{-- Statistik --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    {{-- Total jenis --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-400 tracking-wide">
                JENIS ALAT TERDATA
            </span>

            <div class="w-10 h-10 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600">
                <span class="material-symbols-outlined">category</span>
            </div>
        </div>

        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-bold text-slate-900">
                {{ $stats['total_jenis'] }}
            </span>
            <span class="text-sm text-slate-400">Jenis</span>
        </div>
    </div>

    {{-- Total unit --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-400 tracking-wide">
                TOTAL UNIT
            </span>

            <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined">inventory_2</span>
            </div>
        </div>

        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-bold text-slate-900">
                {{ $stats['total_unit'] }}
            </span>
            <span class="text-sm text-slate-400">Unit</span>
        </div>
    </div>

    {{-- Siap dipinjam --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-400 tracking-wide">
                SIAP DIPINJAM
            </span>

            <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
        </div>

        <div class="flex items-baseline gap-2">
            <span class="text-3xl font-bold text-slate-900">
                {{ $stats['tersedia'] }}
            </span>
            <span class="text-sm text-slate-400">Jenis</span>
        </div>
    </div>

</div>

{{-- Form pencarian dan filter --}}
<form method="GET"
      action="{{ route('siswa.barang.index') }}"
      class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 mb-6">

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-12 gap-3">

        {{-- Pencarian --}}
        <div class="relative sm:col-span-2 xl:col-span-5">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                search
            </span>

            <input
                type="text"
                name="cari"
                value="{{ request('cari') }}"
                placeholder="Cari nama atau kode alat..."
                class="w-full h-12 pl-10 pr-4 bg-slate-50 border border-slate-200 rounded-xl text-sm
                       focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent"
            >
        </div>

        {{-- Filter kategori --}}
        <div class="xl:col-span-3">
            <select
                name="kategori"
                class="w-full h-12 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm
                       focus:outline-none focus:ring-2 focus:ring-violet-500"
            >
                <option value="">Semua Kategori</option>

                @foreach ($kategoriList as $k)
                    <option
                        value="{{ $k->id_kategori }}"
                        {{ (string) request('kategori') === (string) $k->id_kategori ? 'selected' : '' }}
                    >
                        {{ $k->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filter kondisi --}}
        <div class="xl:col-span-2">
            <select
                name="kondisi"
                class="w-full h-12 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm
                       focus:outline-none focus:ring-2 focus:ring-violet-500"
            >
                <option value="">Semua Kondisi</option>

                <option value="baik" {{ request('kondisi') === 'baik' ? 'selected' : '' }}>
                    Baik
                </option>

                <option value="perlu_servis" {{ request('kondisi') === 'perlu_servis' ? 'selected' : '' }}>
                    Perlu Servis
                </option>

                <option value="rusak" {{ request('kondisi') === 'rusak' ? 'selected' : '' }}>
                    Rusak
                </option>
            </select>
        </div>

        {{-- Tombol --}}
        <div class="flex gap-2 xl:col-span-2">
            <button
                type="submit"
                class="flex-1 h-12 px-4 bg-slate-900 text-white text-sm font-semibold rounded-xl
                       hover:bg-violet-600 transition-colors"
            >
                Cari
            </button>

            @if (request('cari') || request('kategori') || request('kondisi'))
                <a
                    href="{{ route('siswa.barang.index') }}"
                    class="h-12 px-3 flex items-center justify-center text-sm font-medium text-slate-500
                           rounded-xl hover:bg-slate-100 transition-colors"
                >
                    Reset
                </a>
            @endif
        </div>

    </div>
</form>

{{-- Katalog alat --}}
@if ($alat->isEmpty())

    <div class="bg-white rounded-2xl p-10 shadow-sm border border-slate-100 text-center">
        <div class="w-16 h-16 rounded-2xl bg-violet-50 text-violet-500 flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-3xl">inventory_2</span>
        </div>

        <h3 class="font-semibold text-slate-800 mb-1">
            Alat tidak ditemukan
        </h3>

        <p class="text-sm text-slate-500">
            Tidak ada alat yang cocok dengan pencarian kamu.
        </p>

        <a
            href="{{ route('siswa.barang.index') }}"
            class="inline-flex mt-4 px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700"
        >
            Lihat Semua Alat
        </a>
    </div>

@else

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

        @foreach ($alat as $item)

            @php
                // Ketersediaan HANYA ditentukan oleh stok tersedia.
                // Unit servis/rusak sudah tidak masuk jumlah_stok, jadi cukup
                // ditampilkan sebagai keterangan dan tidak memblokir tombol pinjam.
                $stok = (int) $item->jumlah_stok;
                $servis = (int) ($item->jumlah_servis ?? 0);
                $rusak = (int) ($item->jumlah_rusak ?? 0);

                $bisaPinjam = $stok > 0 && ! $item->arsip;

                $badgeStatus = $stok > 0
                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-100'
                    : 'bg-red-50 text-red-700 ring-red-100';

                $labelStatus = $stok > 0 ? 'Tersedia' : 'Stok Habis';
            @endphp

            <article
                class="group bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden
                       flex flex-col transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
            >

                {{-- FOTO ALAT: GAMBAR TIDAK DIPOTONG --}}
                <div class="relative h-56 sm:h-60 bg-white border-b border-slate-100
                            flex items-center justify-center p-4 overflow-hidden">

                    @if ($item->foto_url)

                        <img
                            src="{{ $item->foto_url }}"
                            alt="Foto {{ $item->nama_alat }}"
                            loading="lazy"
                            class="w-full h-full object-contain transition-transform duration-300
                                   group-hover:scale-105"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >

                        {{-- Placeholder jika gambar gagal dimuat --}}
                        <div
                            class="absolute inset-0 items-center justify-center flex-col bg-slate-50 text-slate-300"
                            style="display: none;"
                        >
                            <span class="material-symbols-outlined text-5xl">image_not_supported</span>
                            <span class="text-xs mt-2">Foto tidak tersedia</span>
                        </div>

                    @else

                        <div class="flex flex-col items-center justify-center text-slate-300">
                            <span class="material-symbols-outlined text-6xl">devices</span>
                            <span class="text-xs mt-2">Foto belum tersedia</span>
                        </div>

                    @endif

                </div>

                {{-- INFORMASI ALAT --}}
                <div class="p-5 flex flex-col flex-1">

                    {{-- Kode dan status ketersediaan --}}
                    <div class="flex items-center justify-between gap-2 mb-3">

                        <span class="text-[11px] font-mono font-medium text-slate-400">
                            {{ $item->kode_alat }}
                        </span>

                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                     text-[10px] font-semibold ring-1 ring-inset whitespace-nowrap
                                     {{ $badgeStatus }}">

                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                            {{ $labelStatus }}

                        </span>

                    </div>

                    {{-- Nama alat --}}
                    <h3 class="font-semibold text-base text-slate-900 leading-snug mb-2
                               group-hover:text-violet-700 transition-colors">
                        {{ $item->nama_alat }}
                    </h3>

                    {{-- Deskripsi --}}
                    <p class="text-sm text-slate-500 leading-relaxed mb-3 flex-1">
                        {{ $item->deskripsi ?: ($item->kategori->nama_kategori ?? 'Alat praktikum laboratorium') }}
                    </p>

                    {{-- Keterangan unit servis / rusak (tidak memblokir peminjaman) --}}
                    @if ($servis > 0 || $rusak > 0)
                        <div class="mb-4 flex flex-wrap gap-1.5">
                            @if ($servis > 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 text-[10px] font-semibold">
                                    <span class="material-symbols-outlined text-[13px]">build</span>
                                    {{ $servis }} unit sedang servis
                                </span>
                            @endif

                            @if ($rusak > 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-red-50 text-red-700 text-[10px] font-semibold">
                                    <span class="material-symbols-outlined text-[13px]">report</span>
                                    {{ $rusak }} unit rusak
                                </span>
                            @endif
                        </div>
                    @else
                        <div class="mb-4"></div>
                    @endif

                    {{-- Stok dan tombol pinjam --}}
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">

                        <div>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-2xl font-bold {{ $stok > 0 ? 'text-slate-900' : 'text-red-500' }}">
                                    {{ $stok }}
                                </span>

                                <span class="text-xs text-slate-400">unit</span>
                            </div>

                            <span class="text-[11px] text-slate-400">
                                {{ $stok > 0 ? 'Stok tersedia' : 'Stok habis' }}
                            </span>
                        </div>

                        @if ($bisaPinjam)

                            <a
                                href="{{ route('siswa.peminjaman.create') }}?alat={{ $item->id_alat }}"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5
                                       bg-gradient-to-r from-violet-600 to-indigo-600 text-white
                                       text-xs font-semibold rounded-xl shadow-sm
                                       hover:shadow-md hover:opacity-95 transition-all"
                            >
                                <span class="material-symbols-outlined text-base">add_task</span>
                                Pinjam
                            </a>

                        @else

                            <span
                                class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5
                                       bg-slate-100 text-slate-400 text-xs font-semibold rounded-xl
                                       cursor-not-allowed"
                            >
                                <span class="material-symbols-outlined text-base">
                                    block
                                </span>
                                Tidak Tersedia
                            </span>

                        @endif

                    </div>

                </div>

            </article>

        @endforeach

    </div>

    {{-- Pagination --}}
    @if ($alat->hasPages())
        <div class="mt-6 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
            {{ $alat->withQueryString()->links() }}
        </div>
    @endif

@endif

@endsection
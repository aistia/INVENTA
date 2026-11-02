
@extends('layouts.siswa')

@section('title', 'Riwayat Laporan Kerusakan')

@section('content')

<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <p class="text-[11px] font-semibold text-violet-600 tracking-wide mb-0.5">
            INVENTA // RIWAYAT LAPORAN
        </p>
        <h1 class="font-display font-bold text-2xl text-slate-900">
            Riwayat Laporan Kerusakan
        </h1>
        <p class="text-sm text-slate-500 mt-0.5">
            Semua laporan kerusakan yang pernah kamu kirim.
        </p>
    </div>

    <a href="{{ route('siswa.laporan.create') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:opacity-95 shrink-0">
        <span class="material-symbols-outlined text-[17px]">report</span>
        Lapor Baru
    </a>
</div>

{{-- Notifikasi --}}
@if (session('success'))
    <div class="mb-4 rounded-xl border border-emerald-100 bg-emerald-50 p-3 text-sm text-emerald-700">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 rounded-xl border border-red-100 bg-red-50 p-3 text-sm text-red-700">
        {{ session('error') }}
    </div>
@endif

{{-- Tabs --}}
<div class="flex flex-wrap items-center gap-2 mb-5">
    @php
        $tabs = [
            'semua' => 'Semua ('.$stats['semua'].')',
            'diproses' => 'Diproses ('.$stats['diproses'].')',
            'selesai' => 'Selesai ('.$stats['selesai'].')',
        ];
    @endphp

    @foreach ($tabs as $key => $label)
        <a href="{{ route('siswa.laporan.index', ['tab' => $key]) }}"
           class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all {{ $tab === $key ? 'bg-violet-600 text-white shadow-sm' : 'bg-white text-slate-500 hover:text-slate-800' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

@if ($laporan->isEmpty())
    <div class="bg-white rounded-2xl p-10 shadow-sm text-center">
        <span class="material-symbols-outlined text-slate-300 text-[36px] mb-2 block">
            inbox
        </span>
        <p class="text-sm text-slate-400">
            Belum ada laporan kerusakan di kategori ini.
        </p>
    </div>
@else
    <div class="space-y-3">
        @foreach ($laporan as $lap)
            @php
                $catatanAsli = $lap->catatan_tindak_lanjut ?? '';

                $sudahTukar = str_contains(
                    $catatanAsli,
                    '[TUKAR_BARANG]'
                );

                $catatanSiswa = trim(str_replace(
                    '[TUKAR_BARANG]',
                    '',
                    $catatanAsli
                ));
            @endphp

            <div class="bg-white rounded-2xl p-4 shadow-sm">
                @if ($sudahTukar)
                    <div class="mb-3 flex items-start gap-2.5 rounded-xl border border-violet-100 bg-violet-50 p-3">
                        <span class="material-symbols-outlined text-violet-600 text-[21px]">
                            inventory_2
                        </span>

                        <div>
                            <p class="text-sm font-semibold text-violet-800">
                                Laporan diterima untuk penukaran barang
                            </p>
                            <p class="text-xs text-violet-700 mt-0.5">
                                Silakan tukarkan barang melalui petugas laboratorium RPL.
                            </p>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col sm:flex-row gap-4">
                    @if ($lap->foto_bukti)
                        <img src="{{ asset('storage/'.$lap->foto_bukti) }}"
                             alt="Foto kerusakan"
                             class="w-full sm:w-24 h-24 object-cover rounded-xl shrink-0">
                    @else
                        <div class="w-full sm:w-24 h-24 rounded-xl bg-slate-100 flex items-center justify-center text-slate-300 shrink-0">
                            <span class="material-symbols-outlined text-[28px]">
                                broken_image
                            </span>
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-start justify-between gap-2 mb-1">
                            <div>
                                <span class="font-mono text-xs font-semibold text-violet-600">
                                    {{ $lap->kodeLaporanLabel() }}
                                </span>

                                <h3 class="font-semibold text-sm text-slate-900">
                                    {{ $lap->alat->nama_alat ?? '-' }}
                                </h3>
                            </div>

                            <span class="px-2.5 py-1 {{ $lap->status === 'selesai' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} text-[11px] font-semibold rounded-full whitespace-nowrap">
                                {{ $lap->status === 'selesai' ? 'Selesai' : 'Diproses' }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-500 mb-1">
                            {{ $lap->created_at->translatedFormat('d M Y, H:i') }}
                        </p>

                        <p class="text-sm text-slate-600">
                            {{ $lap->deskripsi }}
                        </p>

                        @if ($catatanSiswa !== '')
                            <div class="mt-2 p-2.5 bg-slate-50 rounded-lg">
                                <p class="text-[11px] font-semibold text-slate-500 mb-0.5">
                                    CATATAN PETUGAS LAB
                                </p>

                                <p class="text-xs text-slate-600">
                                    {{ $catatanSiswa }}
                                </p>

                                @if ($lap->admin)
                                    <p class="text-[10.5px] text-slate-400 mt-1">
                                        oleh {{ $lap->admin->nama }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($laporan->hasPages())
        <div class="mt-5">
            {{ $laporan->links() }}
        </div>
    @endif
@endif

@endsection

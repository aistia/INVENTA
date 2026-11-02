
@extends('layouts.admin')

@section('title', 'Tindak Lanjut Kerusakan')

@section('content')

{{-- Header --}}
<div class="mb-5 sm:mb-6">
    <p class="text-[10px] sm:text-[11px] font-semibold text-violet-600 tracking-wide mb-1">
        MONITORING LAB &bull; SERVIS ALAT
    </p>
    <h1 class="font-display font-bold text-xl sm:text-2xl text-slate-900 leading-tight">
        Tindak Lanjut &amp; Penanganan Kerusakan Alat
    </h1>
    <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
        Kelola dan tindak lanjuti laporan kerusakan dari siswa &amp; guru.
        Perbarui catatan servis hingga selesai.
    </p>
</div>

{{-- Statistik --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-5 sm:mb-6">

    <div class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-4 shadow-sm">
        <div class="flex items-center justify-between gap-2 mb-2">
            <span class="text-[9px] sm:text-[11px] font-semibold text-slate-400 tracking-wide">
                TIKET AKTIF
            </span>
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                <span class="material-symbols-outlined text-[16px] sm:text-[17px]">build_circle</span>
            </div>
        </div>
        <div class="text-xl sm:text-2xl font-bold text-slate-900">
            {{ $stats['diproses'] }}
        </div>
    </div>

    <div class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-4 shadow-sm">
        <div class="flex items-center justify-between gap-2 mb-2">
            <span class="text-[9px] sm:text-[11px] font-semibold text-slate-400 tracking-wide">
                PERLU SEGERA
            </span>
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                <span class="material-symbols-outlined text-[16px] sm:text-[17px]">priority_high</span>
            </div>
        </div>
        <div class="text-xl sm:text-2xl font-bold text-slate-900">
            {{ $stats['segera'] }}
        </div>
        <p class="text-[9px] sm:text-[11px] text-slate-400 mt-1">
            Sudah &gt;2 hari belum ditangani
        </p>
    </div>

    <div class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-4 shadow-sm">
        <div class="flex items-center justify-between gap-2 mb-2">
            <span class="text-[9px] sm:text-[11px] font-semibold text-slate-400 tracking-wide">
                SELESAI BULAN INI
            </span>
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                <span class="material-symbols-outlined text-[16px] sm:text-[17px]">task_alt</span>
            </div>
        </div>
        <div class="text-xl sm:text-2xl font-bold text-slate-900">
            {{ $stats['selesai_bulan_ini'] }}
        </div>
    </div>

    <div class="bg-white rounded-xl sm:rounded-2xl p-3.5 sm:p-4 shadow-sm">
        <div class="flex items-center justify-between gap-2 mb-2">
            <span class="text-[9px] sm:text-[11px] font-semibold text-slate-400 tracking-wide">
                TOTAL LAPORAN
            </span>
            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                <span class="material-symbols-outlined text-[16px] sm:text-[17px]">summarize</span>
            </div>
        </div>
        <div class="text-xl sm:text-2xl font-bold text-slate-900">
            {{ $stats['total'] }}
        </div>
    </div>

</div>

{{-- Notifikasi --}}
@if (session('success'))
    <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-xs sm:text-sm">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-100 text-red-700 text-xs sm:text-sm">
        {{ session('error') }}
    </div>
@endif

{{-- Validasi --}}
@if ($errors->any())
    <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-100 text-red-700 text-xs sm:text-sm">
        <ul class="list-disc pl-4 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Tab dan Pencarian --}}
<div class="mb-4 space-y-3">

    <div class="overflow-x-auto -mx-1 px-1 pb-1">
        @php
            $tabs = [
                'semua' => 'Semua',
                'diproses' => 'Diproses',
                'selesai' => 'Selesai',
                'arsip' => 'Diarsipkan ('.$stats['arsip'].')'
            ];
        @endphp

        <div class="flex items-center gap-2 w-max">
            @foreach ($tabs as $key => $label)
                <a href="{{ route('admin.laporan.index', array_filter(['tab' => $key, 'cari' => request('cari')])) }}"
                   class="px-3.5 py-2 rounded-full text-[11px] sm:text-xs font-semibold transition-all whitespace-nowrap
                   {{ $tab === $key
                        ? 'bg-violet-600 text-white shadow-sm'
                        : 'bg-white text-slate-500 hover:text-slate-800' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Pencarian dan unduh --}}
    <div class="flex flex-col sm:flex-row gap-2">

        <form method="GET"
              action="{{ route('admin.laporan.index') }}"
              class="flex-1 min-w-0">

            <input type="hidden" name="tab" value="{{ $tab }}">

            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">
                    search
                </span>

                <input type="text"
                       name="cari"
                       value="{{ request('cari') }}"
                       placeholder="Cari nama pelapor / alat..."
                       class="w-full h-10 pl-10 pr-3 bg-white shadow-sm border border-slate-200 text-xs sm:text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
        </form>

        @if ($tab === 'arsip')
            <a href="{{ route('admin.laporan.export-arsip') }}"
               class="inline-flex items-center justify-center gap-1.5 h-10 px-4 bg-white border border-slate-200 text-slate-700 text-xs sm:text-sm font-semibold rounded-xl shadow-sm hover:bg-slate-50 whitespace-nowrap">
                <span class="material-symbols-outlined text-[17px]">download</span>
                Unduh Arsip
            </a>
        @endif

    </div>
</div>

{{-- Konten utama --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-start">

    {{-- Daftar laporan --}}
    <div class="lg:col-span-2 space-y-3 sm:space-y-4">

        @forelse ($laporan as $item)

            @php
                $catatanAsli = $item->catatan_tindak_lanjut ?? '';

                $sudahTukar = str_contains(
                    $catatanAsli,
                    '[TUKAR_BARANG]'
                );

                $catatanTampil = trim(str_replace(
                    [
                        '[TUKAR_BARANG]',
                        '[TINDAKAN: HAPUS BARANG]',
                        '[TINDAKAN: GANTI BARANG BARU]',
                    ],
                    '',
                    $catatanAsli
                ));
            @endphp

            <div class="bg-white rounded-xl sm:rounded-2xl shadow-sm p-3.5 sm:p-5">

                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">

                    {{-- Foto kerusakan --}}
                    <div class="shrink-0">
                        @if ($item->foto_bukti)
                            <a href="{{ asset('storage/'.$item->foto_bukti) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="block">
                                <img src="{{ asset('storage/'.$item->foto_bukti) }}"
                                     alt="Foto kerusakan"
                                     loading="lazy"
                                     class="w-full h-40 sm:w-28 sm:h-28 md:w-32 md:h-32 object-cover rounded-xl border border-slate-100">
                            </a>
                        @else
                            <div class="w-full h-28 sm:w-28 sm:h-28 md:w-32 md:h-32 rounded-xl bg-slate-100 flex items-center justify-center text-slate-300">
                                <span class="material-symbols-outlined text-[32px]">broken_image</span>
                            </div>
                        @endif
                    </div>

                    {{-- Informasi laporan --}}
                    <div class="flex-1 min-w-0">

                        {{-- Nama alat dan status --}}
                        <div class="flex flex-col gap-2 mb-2">

                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    <h3 class="font-semibold text-sm sm:text-base text-slate-900">
                                        {{ $item->alat->nama_alat ?? 'Alat tidak ditemukan' }}
                                    </h3>

                                    <span class="font-mono text-[10px] sm:text-[11px] text-violet-600 font-semibold">
                                        {{ $item->kodeLaporanLabel() }}
                                    </span>
                                </div>

                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Kode alat: {{ $item->alat->kode_alat ?? '-' }}
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="px-2.5 py-1
                                    {{ $item->status === 'selesai'
                                        ? 'bg-emerald-50 text-emerald-600'
                                        : 'bg-amber-50 text-amber-600' }}
                                    text-[10px] sm:text-[11px] font-semibold rounded-full">
                                    {{ $item->status === 'selesai' ? 'Selesai' : 'Diproses' }}
                                </span>

                                @if ($sudahTukar)
                                    <span class="px-2.5 py-1 bg-violet-50 text-violet-700 text-[10px] sm:text-[11px] font-semibold rounded-full">
                                        Penukaran Diajukan
                                    </span>
                                @endif

                                @if ($item->arsip)
                                    <span class="px-2 py-1 bg-slate-100 text-slate-500 text-[10px] font-semibold rounded-full">
                                        Diarsipkan
                                    </span>
                                @endif
                            </div>

                        </div>

                        {{-- Pelapor dan tanggal --}}
                        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-1.5 sm:gap-x-3 sm:gap-y-1 text-[10px] sm:text-[11px] text-slate-400 mb-3">

                            <span class="flex items-center gap-1 min-w-0">
                                <span class="material-symbols-outlined text-[13px]">person</span>
                                <span class="truncate">
                                    {{ $item->user->nama ?? '-' }}
                                    @if ($item->user?->kelas)
                                        &bull; {{ $item->user->kelas }}
                                    @endif
                                </span>
                            </span>

                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">calendar_today</span>
                                {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                            </span>

                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-3">
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                {{ $item->deskripsi }}
                            </p>
                        </div>

                        {{-- Catatan tindak lanjut --}}
                        @if ($catatanTampil !== '')
                            <div class="bg-slate-50 rounded-xl p-3 mb-3">
                                <p class="text-[10px] sm:text-[11px] font-semibold text-slate-500 mb-1">
                                    CATATAN SERVIS TERAKHIR
                                </p>

                                <p class="text-xs text-slate-600 leading-relaxed">
                                    {{ $catatanTampil }}
                                </p>

                                @if ($item->admin)
                                    <p class="text-[10px] text-slate-400 mt-1.5">
                                        oleh {{ $item->admin->nama }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        {{-- Aksi laporan --}}
                        @if ($item->status === 'diproses')

                            <div class="space-y-2">

                                {{-- Update catatan --}}
                                <form method="POST"
                                      action="{{ route('admin.laporan.catatan', $item) }}"
                                      class="flex flex-col sm:flex-row gap-2">
                                    @csrf

                                    <input type="text"
                                           name="catatan_tindak_lanjut"
                                           placeholder="Update catatan servis..."
                                           required
                                           class="flex-1 min-w-0 h-10 sm:h-9 px-3 bg-slate-50 border border-slate-200 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500">

                                    <button type="submit"
                                            class="w-full sm:w-auto px-3 h-10 sm:h-9 bg-white border border-slate-200 text-slate-600 text-xs font-semibold rounded-lg hover:bg-slate-50">
                                        Simpan Catatan
                                    </button>
                                </form>

                                {{-- Tombol Tukar Barang --}}
                                @if ($sudahTukar)
                                    <div class="flex items-center gap-2 px-3 py-2.5 bg-violet-50 border border-violet-100 rounded-lg">
                                        <span class="material-symbols-outlined text-violet-600 text-[18px]">
                                            check_circle
                                        </span>
                                        <span class="text-xs font-semibold text-violet-700">
                                            Penukaran barang sudah diajukan
                                        </span>
                                    </div>
                                @else
                                    <form method="POST"
                                          action="{{ route('admin.laporan.tukar', $item) }}"
                                          onsubmit="return confirm('Yakin ingin mengajukan penukaran barang? Stok barang pengganti akan langsung dikurangi.');">
                                        @csrf

                                        <button type="submit"
                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-2.5 bg-violet-600 text-white text-xs font-semibold rounded-lg hover:bg-violet-700 transition">
                                            <span class="material-symbols-outlined text-[17px]">
                                                swap_horiz
                                            </span>
                                            Tukar Barang
                                        </button>
                                    </form>
                                @endif

                                {{-- Tombol tandai selesai --}}
                                <button type="button"
                                        onclick="document.getElementById('selesai-{{ $item->id_laporan }}').classList.toggle('hidden')"
                                        class="w-full sm:w-auto px-3 py-2.5 sm:py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700">
                                    Tandai Selesai
                                </button>

                                {{-- Form penyelesaian --}}
                                <form id="selesai-{{ $item->id_laporan }}"
                                      method="POST"
                                      action="{{ route('admin.laporan.selesai', $item) }}"
                                      class="hidden mt-2 p-3 bg-emerald-50 border border-emerald-100 rounded-xl space-y-3">
                                    @csrf

                                    <p class="text-[10px] font-bold text-emerald-700">
                                        KONFIRMASI PENYELESAIAN
                                    </p>

                                    @if ($item->jenis_penanganan === 'servis')
                                        <p class="text-xs text-emerald-700">
                                            Setelah servis selesai, {{ $item->jumlah_unit }} unit akan dikembalikan ke stok tersedia.
                                        </p>

                                    @elseif ($item->jenis_penanganan === 'rusak')

                                        @if ($sudahTukar)
                                            <div class="p-3 bg-violet-50 border border-violet-100 rounded-lg">
                                                <p class="text-xs font-semibold text-violet-700">
                                                    Penukaran barang sudah diajukan.
                                                </p>
                                                <p class="text-[11px] text-violet-600 mt-1">
                                                    Stok barang pengganti sudah dikurangi saat pengajuan. Tidak perlu memilih Hapus atau Ganti lagi.
                                                </p>
                                            </div>
                                        @else
                                            <div class="space-y-2">
                                                <p class="text-xs font-semibold text-slate-700">
                                                    Pilih tindakan untuk unit rusak:
                                                </p>

                                                <label class="flex items-start gap-2 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-violet-300">
                                                    <input type="radio"
                                                           name="tindakan_rusak"
                                                           value="hapus"
                                                           required
                                                           class="mt-0.5 text-violet-600 focus:ring-violet-500">

                                                    <span>
                                                        <span class="block text-xs font-semibold text-slate-700">
                                                            Hapus
                                                        </span>
                                                        <span class="block text-[11px] text-slate-400 mt-0.5">
                                                            Unit rusak dihapus dari inventaris.
                                                        </span>
                                                    </span>
                                                </label>

                                                <label class="flex items-start gap-2 p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-violet-300">
                                                    <input type="radio"
                                                           name="tindakan_rusak"
                                                           value="ganti"
                                                           required
                                                           class="mt-0.5 text-violet-600 focus:ring-violet-500">

                                                    <span>
                                                        <span class="block text-xs font-semibold text-slate-700">
                                                            Ganti
                                                        </span>
                                                        <span class="block text-[11px] text-slate-400 mt-0.5">
                                                            Unit rusak diganti dengan barang baru dan stok tersedia bertambah.
                                                        </span>
                                                    </span>
                                                </label>

                                                <p class="text-[10px] text-slate-400">
                                                    Jika ingin stok pengganti langsung dikurangi saat pengajuan, gunakan tombol Tukar Barang di atas.
                                                </p>
                                            </div>
                                        @endif
                                    @endif

                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                                            Catatan Penyelesaian
                                        </label>

                                        <textarea name="catatan_tindak_lanjut"
                                                  rows="3"
                                                  required
                                                  placeholder="Tuliskan hasil pemeriksaan atau tindakan..."
                                                  class="w-full text-xs p-3 bg-white border border-emerald-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400"></textarea>
                                    </div>

                                    <button type="submit"
                                            class="w-full px-3 py-2.5 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700">
                                        Konfirmasi Penyelesaian
                                    </button>
                                </form>

                            </div>

                        @elseif ($item->status === 'selesai')

                            <form method="POST" action="{{ route('admin.laporan.arsip', $item) }}">
                                @csrf

                                <button type="submit"
                                        class="text-[11px] font-semibold text-slate-400 hover:text-amber-600 hover:underline py-1">
                                    {{ $item->arsip ? 'Batalkan Arsip' : 'Arsipkan Laporan Ini' }}
                                </button>
                            </form>

                        @endif

                    </div>
                </div>
            </div>

        @empty

            <div class="bg-white rounded-2xl p-8 sm:p-10 shadow-sm text-center">
                <span class="material-symbols-outlined text-4xl text-slate-300">inbox</span>
                <p class="text-xs sm:text-sm text-slate-400 mt-2">
                    Tidak ada laporan kerusakan pada kategori ini.
                </p>
            </div>

        @endforelse

        {{-- Pagination --}}
        @if ($laporan->hasPages())
            <div class="mt-4 overflow-x-auto">
                {{ $laporan->links() }}
            </div>
        @endif

    </div>

    {{-- Sidebar ringkasan --}}
    <div class="space-y-3 sm:space-y-4">

        {{-- Ringkasan penanganan --}}
        <div class="bg-white rounded-xl sm:rounded-2xl p-4 sm:p-5 shadow-sm">

            <h2 class="font-semibold text-sm text-slate-900 mb-1">
                Ringkasan Penanganan
            </h2>

            <p class="text-[11px] sm:text-xs text-slate-400 mb-4">
                Alat yang paling sering dilaporkan rusak
            </p>

            @if ($topAlatBermasalah->isEmpty())

                <p class="text-xs text-slate-400 text-center py-6">
                    Belum ada data laporan.
                </p>

            @else

                <div class="space-y-3">

                    @foreach ($topAlatBermasalah as $row)

                        <div class="flex items-center justify-between gap-3">

                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-500 shrink-0">
                                    <span class="material-symbols-outlined text-[16px]">build</span>
                                </div>

                                <span class="text-xs text-slate-600 truncate">
                                    {{ $row->alat->nama_alat ?? 'Alat dihapus' }}
                                </span>
                            </div>

                            <span class="text-xs font-bold text-slate-900 shrink-0">
                                {{ $row->total }}x
                            </span>

                        </div>

                    @endforeach

                </div>

            @endif
        </div>

        {{-- SOP --}}
        <div class="bg-violet-50 border border-violet-100 rounded-xl sm:rounded-2xl p-4 sm:p-5">

            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-violet-600 text-[18px]">policy</span>
                <h2 class="font-semibold text-sm text-slate-900">
                    SOP Penanganan Kerusakan
                </h2>
            </div>

            <p class="text-xs text-slate-500 leading-relaxed mb-3">
                Pastikan unit telah diperiksa sebelum laporan diselesaikan.
                Untuk barang rusak, pilih Hapus atau Ganti sesuai tindakan
                petugas. Gunakan Tukar Barang jika siswa perlu mendapatkan
                unit pengganti dan stok pengganti harus langsung dikurangi.
                Untuk barang servis, stok akan kembali tersedia setelah
                laporan servis diselesaikan.
            </p>

            <a href="{{ route('admin.barang.index') }}"
               class="inline-flex items-center gap-1 text-xs font-semibold text-violet-600 hover:underline">
                Lihat Kelola Stok Barang
                <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
            </a>

        </div>
    </div>

</div>

@endsection

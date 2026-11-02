@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')

@section('content')

{{-- Banner selamat datang --}}
<div class="bg-gradient-to-r from-violet-600 to-indigo-600 rounded-2xl p-5 mb-6 text-white">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/20 text-[11px] font-semibold rounded-full mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span> Tahun Ajaran {{ now()->month >= 7 ? now()->year.'/'.(now()->year + 1) : (now()->year - 1).'/'.now()->year }}
            </span>
            <h1 class="font-display font-bold text-xl">Selamat Datang, {{ auth()->user()->nama }}!</h1>
            <p class="text-sm text-white/80 mt-0.5">
                @if (auth()->user()->tipe_user === 'siswa')
                    Kelas {{ auth()->user()->kelas ?? '-' }} &bull; Jurusan Rekayasa Perangkat Lunak
                @else
                    {{ auth()->user()->jabatan ?: 'Guru Pembina' }} &bull; Jurusan Rekayasa Perangkat Lunak
                @endif
            </p>
        </div>
        <div class="bg-white/10 rounded-xl p-3 flex items-center gap-2.5">
            <span class="material-symbols-outlined text-[22px]">science</span>
            <div>
                <p class="text-[11px] font-semibold">LAB RPL OPERASIONAL</p>
                <p class="text-[11px] text-white/80">Lab RPL siap digunakan sesuai jadwal</p>
            </div>
        </div>
    </div>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 tracking-wide">PEMINJAMAN BERJALAN</span>
            <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center text-violet-600">
                <span class="material-symbols-outlined text-[17px]">inventory_2</span>
            </div>
        </div>
        <div class="text-2xl font-bold text-slate-900">{{ $stats['aktif'] }} <span class="text-sm font-medium text-slate-400">Alat</span></div>
        @if ($stats['jatuh_tempo_hari_ini'] > 0)
            <span class="inline-flex items-center gap-1 mt-2 text-[11px] font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">
                {{ $stats['jatuh_tempo_hari_ini'] }} jatuh tempo hari ini
            </span>
        @else
            <span class="inline-flex items-center gap-1 mt-2 text-[11px] font-medium text-slate-500 bg-slate-50 px-2 py-0.5 rounded-full">Tidak ada jatuh tempo hari ini</span>
        @endif
    </div>

    <div class="bg-white rounded-2xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 tracking-wide">MENUNGGU VERIFIKASI</span>
            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-[17px]">hourglass_top</span>
            </div>
        </div>
        <div class="text-2xl font-bold text-slate-900">{{ $stats['menunggu'] }} <span class="text-sm font-medium text-slate-400">Pengajuan</span></div>
        <span class="inline-flex items-center gap-1 mt-2 text-[11px] font-medium text-slate-500 bg-slate-50 px-2 py-0.5 rounded-full">Menunggu persetujuan petugas lab</span>
    </div>

    <div class="bg-white rounded-2xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 tracking-wide">TOTAL RIWAYAT PEMINJAMAN</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-[17px]">task_alt</span>
            </div>
        </div>
        <div class="text-2xl font-bold text-slate-900">{{ $stats['total_riwayat'] }} <span class="text-sm font-medium text-slate-400">Kali</span></div>
        <span class="inline-flex items-center gap-1 mt-2 text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">
            {{ $stats['persen_tepat_waktu'] }}% dikembalikan tepat waktu
        </span>
    </div>
</div>

{{-- Aksi cepat --}}
<div class="bg-white rounded-2xl p-4 shadow-sm flex flex-wrap items-center justify-between gap-3 mb-6">
    <div class="flex items-center gap-2.5">
        <span class="material-symbols-outlined text-violet-600 text-[20px]">bolt</span>
        <div>
            <p class="text-sm font-semibold text-slate-900">Aksi Cepat Inventaris</p>
            <p class="text-xs text-slate-500">Lakukan reservasi hardware atau laporkan kendala operasional.</p>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('siswa.peminjaman.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:opacity-95">
            <span class="material-symbols-outlined text-[17px]">add_task</span> Ajukan Peminjaman
        </a>
        <a href="{{ route('siswa.laporan.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl shadow-sm hover:bg-slate-50">
            <span class="material-symbols-outlined text-[17px]">report</span> Lapor Kerusakan
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    {{-- Peminjaman aktif --}}
    <div class="lg:col-span-2 bg-white rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-1">
            <h2 class="font-semibold text-sm text-slate-900 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[17px] text-violet-600">schedule</span>
                Peminjaman Berjalan (Aktif)
            </h2>
            <span class="text-xs text-slate-400">{{ $stats['aktif'] }} Perangkat Sedang Digunakan</span>
        </div>

        @if ($peminjamanAktif->isEmpty())
            <p class="text-sm text-slate-400 text-center py-10">Belum ada peminjaman yang sedang berjalan.</p>
        @else
            <div class="space-y-3 mt-3">
                @foreach ($peminjamanAktif as $p)
                    @php $telatOrJatuhTempo = $p->isJatuhTempoHariIni() || $p->isTerlambat(); @endphp
                    <div class="border border-slate-100 rounded-xl p-3">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                            <span class="font-mono text-[11px] text-violet-600 font-semibold">REQ-{{ str_pad($p->id_peminjaman, 4, '0', STR_PAD_LEFT) }}</span>
                            @if ($p->diajukan_kembali_pada)
                                <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-semibold rounded-full">Menunggu Konfirmasi Pengembalian</span>
                            @elseif ($p->isTerlambat())
                                <span class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-semibold rounded-full">Terlambat Dikembalikan</span>
                            @elseif ($p->isJatuhTempoHariIni())
                                <span class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-semibold rounded-full">Harus Dikembalikan Hari Ini</span>
                            @else
                                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[10px] font-semibold rounded-full">Aktif</span>
                            @endif
                        </div>

                        @foreach ($p->detail as $d)
                            <p class="text-sm font-semibold text-slate-800">{{ $d->alat->nama_alat ?? '-' }} <span class="text-slate-400 font-normal">&times;{{ $d->jumlah }}</span></p>
                        @endforeach

                        <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-[11px] text-slate-500">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">event</span>
                                Dipinjam: {{ $p->tanggal_pinjam?->translatedFormat('d M Y') ?? '-' }}
                            </span>
                            <span class="flex items-center gap-1 {{ $telatOrJatuhTempo ? 'text-red-500 font-semibold' : '' }}">
                                <span class="material-symbols-outlined text-[13px]">event_upcoming</span>
                                Jatuh tempo: {{ $p->tanggal_kembali_rencana?->translatedFormat('d M Y') ?? '-' }}
                            </span>
                            @if ($p->admin)
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">person</span>
                                    PJ: {{ $p->admin->nama }}
                                </span>
                            @endif
                        </div>
                        @unless ($p->diajukan_kembali_pada)
                            <a href="{{ route('siswa.peminjaman.index', ['tab' => 'disetujui']) }}#p-{{ $p->id_peminjaman }}"
                               class="inline-flex items-center gap-1 mt-2.5 text-[11px] font-semibold text-violet-600 hover:underline">
                                <span class="material-symbols-outlined text-[14px]">assignment_return</span> Ajukan Pengembalian
                            </a>
                        @endunless
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Sidebar: laporan kerusakan terbaru + info --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <h2 class="font-semibold text-sm text-slate-900 mb-3">Laporan Kerusakan Terbaru</h2>

            @if ($laporanTerbaru->isEmpty())
                <p class="text-xs text-slate-400 text-center py-6">Belum pernah lapor kerusakan.</p>
            @else
                <div class="space-y-3">
                    @foreach ($laporanTerbaru as $lap)
                        <div class="flex items-start gap-2.5">
                            <div class="w-7 h-7 rounded-lg {{ $lap->status === 'selesai' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[15px]">{{ $lap->status === 'selesai' ? 'check' : 'build' }}</span>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-slate-700 truncate">{{ $lap->alat->nama_alat ?? '-' }}</p>
                                <p class="text-[10.5px] text-slate-400">{{ $lap->status === 'selesai' ? 'Selesai ditangani' : 'Sedang diproses' }} &bull; {{ $lap->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-violet-50 border border-violet-100 rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-violet-600 text-[18px]">policy</span>
                <h2 class="font-semibold text-sm text-slate-900">Aturan Pengembalian</h2>
            </div>
            <ul class="text-xs text-slate-500 space-y-1.5 leading-relaxed">
                <li class="flex gap-1.5"><span class="material-symbols-outlined text-[14px] text-violet-600 shrink-0">check</span> Pastikan kondisi alat sama seperti saat dipinjam.</li>
                <li class="flex gap-1.5"><span class="material-symbols-outlined text-[14px] text-violet-600 shrink-0">check</span> Kembalikan sebelum/tepat jatuh tempo untuk menjaga skor peminjaman.</li>
                <li class="flex gap-1.5"><span class="material-symbols-outlined text-[14px] text-violet-600 shrink-0">check</span> Lapor ke petugas lab kalau alat rusak, jangan ditahan sendiri.</li>
            </ul>
        </div>
    </div>
</div>

@endsection
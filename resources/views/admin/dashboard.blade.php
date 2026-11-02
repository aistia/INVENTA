
@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-[11px] font-semibold tracking-wide text-violet-600">
                INVENTA // RPL-HUB-01 &bull; Sistem Sinkron 100%
            </p>
            <h1 class="font-display text-2xl font-bold text-slate-900">
                Dashboard Petugas Lab RPL
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Monitoring sirkulasi aset dan status operasional laboratorium.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.dashboard.export') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                <span class="material-symbols-outlined text-lg">download</span>
                Export Laporan
            </a>

            <a href="{{ route('admin.barang.index') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90">
                <span class="material-symbols-outlined text-lg">add</span>
                Tambah Barang
            </a>
        </div>
    </div>

    {{-- STATISTIK UTAMA --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- TOTAL ASET --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <p class="text-xs font-semibold tracking-wide text-slate-400">
                    TOTAL ASET TERDATA
                </p>
                <span class="material-symbols-outlined rounded-xl bg-violet-100 p-2 text-violet-600">
                    inventory_2
                </span>
            </div>
            <p class="text-3xl font-bold text-slate-900">
                {{ number_format($stats['total_aset']) }}
                <span class="text-sm font-medium text-slate-400">Unit</span>
            </p>
            <p class="mt-2 text-xs text-slate-500">
                Total aset inventaris laboratorium
            </p>
        </div>

        {{-- SEDANG DIPINJAM --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <p class="text-xs font-semibold tracking-wide text-slate-400">
                    SEDANG DIPINJAM
                </p>
                <span class="material-symbols-outlined rounded-xl bg-indigo-100 p-2 text-indigo-600">
                    sync_alt
                </span>
            </div>
            <p class="text-3xl font-bold text-slate-900">
                {{ number_format($stats['sedang_dipinjam']) }}
                <span class="text-sm font-medium text-slate-400">Unit</span>
            </p>
            <p class="mt-2 text-xs text-slate-500">
                Unit dalam peminjaman aktif
            </p>
        </div>

        {{-- STOK SIAP --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <p class="text-xs font-semibold tracking-wide text-slate-400">
                    STOK SIAP DI LAB
                </p>
                <span class="material-symbols-outlined rounded-xl bg-emerald-100 p-2 text-emerald-600">
                    check_circle
                </span>
            </div>
            <p class="text-3xl font-bold text-slate-900">
                {{ number_format($stats['stok_siap']) }}
                <span class="text-sm font-medium text-slate-400">Unit</span>
            </p>
            <p class="mt-2 text-xs font-medium text-emerald-600">
                {{ $stats['persen_prima'] }}% stok dalam kondisi baik
            </p>
        </div>

        {{-- LAPORAN KERUSAKAN --}}
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <p class="text-xs font-semibold tracking-wide text-slate-400">
                    LAPORAN KERUSAKAN
                </p>
                <span class="material-symbols-outlined rounded-xl bg-red-100 p-2 text-red-600">
                    warning
                </span>
            </div>
            <p class="text-3xl font-bold text-slate-900">
                {{ number_format($stats['laporan_baru']) }}
                <span class="text-sm font-medium text-slate-400">Tiket</span>
            </p>
            <p class="mt-2 text-xs font-medium text-red-600">
                {{ $stats['laporan_segera'] }} perlu penanganan cepat
            </p>
        </div>
    </div>

    {{-- KONTEN UTAMA --}}
    <div class="grid grid-cols-1 items-stretch gap-5 xl:grid-cols-3">

        {{-- KOLOM KIRI --}}
        <div class="space-y-5 xl:col-span-2">

            {{-- GRAFIK TREN --}}
            <section class="rounded-2xl bg-white p-5 shadow-sm">
                <div class="mb-1 flex items-center gap-2">
                    <span class="material-symbols-outlined text-violet-600">
                        show_chart
                    </span>
                    <h2 class="font-semibold text-slate-900">
                        Tren Sirkulasi &amp; Peminjaman Mingguan
                    </h2>
                </div>

                <p class="mb-5 text-sm text-slate-400">
                    Aktivitas pengajuan peminjaman selama 7 hari terakhir.
                </p>

                <div class="relative h-64 w-full">
                    <canvas id="trenChart"></canvas>
                </div>
            </section>

            {{-- PEMINJAMAN MENUNGGU PERSETUJUAN --}}
            <section class="rounded-2xl bg-white p-5 shadow-sm">
                <div class="mb-1 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="flex items-center gap-2 font-semibold text-slate-900">
                        <span class="h-2 w-2 rounded-full bg-violet-600"></span>
                        Peminjaman Menunggu Persetujuan
                    </h2>

                    <a href="{{ route('admin.peminjaman.index') }}"
                       class="text-sm font-semibold text-violet-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>

                <p class="mb-4 text-sm text-slate-400">
                    {{ $totalMenunggu }} permohonan menunggu verifikasi.
                </p>

                @if ($menungguVerifikasi->isEmpty())
                    <div class="rounded-xl bg-slate-50 py-10 text-center">
                        <span class="material-symbols-outlined text-3xl text-slate-300">
                            task_alt
                        </span>
                        <p class="mt-2 text-sm text-slate-500">
                            Tidak ada pengajuan yang menunggu verifikasi.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-left text-xs text-slate-400">
                                    <th class="whitespace-nowrap px-3 py-3 font-medium">
                                        SISWA
                                    </th>
                                    <th class="px-3 py-3 font-medium">
                                        ALAT &amp; JUMLAH
                                    </th>
                                    <th class="whitespace-nowrap px-3 py-3 font-medium">
                                        TANGGAL
                                    </th>
                                    <th class="whitespace-nowrap px-3 py-3 text-right font-medium">
                                        AKSI
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($menungguVerifikasi as $p)
                                    <tr class="border-b border-slate-50 last:border-0">
                                        <td class="px-3 py-4">
                                            <p class="font-semibold text-slate-800">
                                                {{ $p->user->nama ?? '-' }}
                                            </p>
                                            <p class="mt-1 text-xs text-slate-400">
                                                {{ $p->user->kelas ?? $p->user->nis ?? $p->user->nip ?? '-' }}
                                            </p>
                                        </td>

                                        <td class="px-3 py-4 text-slate-600">
                                            @foreach ($p->detail as $d)
                                                <p class="mb-1 last:mb-0">
                                                    {{ $d->alat->nama_alat ?? '-' }}
                                                    <span class="text-slate-400">
                                                        &times;{{ $d->jumlah }}
                                                    </span>
                                                </p>
                                            @endforeach
                                        </td>

                                        <td class="whitespace-nowrap px-3 py-4 text-xs text-slate-500">
                                            {{ $p->created_at->translatedFormat('d M Y, H:i') }}
                                        </td>

                                        <td class="px-3 py-4">
                                            <div class="flex justify-end gap-2">
                                                <form method="POST"
                                                      action="{{ route('admin.peminjaman.approve', $p) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            class="rounded-lg bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">
                                                        Setuju
                                                    </button>
                                                </form>

                                                <form method="POST"
                                                      action="{{ route('admin.peminjaman.reject', $p) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-100">
                                                        Tolak
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        {{-- KOLOM KANAN: DISTRIBUSI KATEGORI --}}
        <section class="flex flex-col rounded-2xl bg-white p-5 shadow-sm xl:self-stretch">
            <h2 class="font-semibold text-slate-900">
                Distribusi Kategori
            </h2>

            <p class="mb-3 mt-1 text-sm text-slate-400">
                Komposisi {{ number_format($stats['total_aset']) }} aset di lab
            </p>

            {{-- PIE CHART --}}
            <div class="relative mx-auto h-56 w-full max-w-xs shrink-0">
                <canvas id="kategoriChart"></canvas>

                <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-bold text-slate-900">
                        {{ number_format($stats['total_aset']) }}
                    </span>
                    <span class="text-[10px] text-slate-400">
                        TOTAL UNIT
                    </span>
                </div>
            </div>

            {{-- DAFTAR KATEGORI --}}
            <div class="mt-4 flex-1 space-y-3">
                @forelse ($distribusi as $i => $kategori)
                    <div class="flex items-start justify-between gap-3 text-sm">
                        <span class="flex min-w-0 items-start gap-2 text-slate-600">
                            <span class="mt-1.5 h-2.5 w-2.5 shrink-0 rounded-full"
                                  style="background-color: {{ ['#7c3aed','#059669','#6366f1','#f59e0b','#ec4899','#0ea5e9'][$i % 6] }}">
                            </span>
                            <span>{{ $kategori->nama_kategori }}</span>
                        </span>

                        <span class="shrink-0 text-right font-medium text-slate-500">
                            {{ $kategori->total_stok }}
                            <span class="text-xs">
                                ({{ $stats['total_aset'] > 0 ? round($kategori->total_stok / $stats['total_aset'] * 100) : 0 }}%)
                            </span>
                        </span>
                    </div>
                @empty
                    <p class="py-4 text-center text-sm text-slate-400">
                        Belum ada data kategori.
                    </p>
                @endforelse
            </div>
        </section>
    </div>

    {{-- AKTIVITAS TERKINI --}}
    <section class="rounded-2xl bg-white p-5 shadow-sm">
        <div class="mb-4">
            <h2 class="font-semibold text-slate-900">
                Aktivitas &amp; Alert Terkini
            </h2>
            <p class="mt-1 text-sm text-slate-400">
                Riwayat perubahan stok dan laporan kerusakan terbaru
            </p>
        </div>

        @if ($aktivitas->isEmpty())
            <div class="rounded-xl bg-slate-50 py-8 text-center text-sm text-slate-400">
                Belum ada aktivitas.
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($aktivitas as $item)
                    @php $d = $item['data']; @endphp

                    <div class="flex items-start gap-3 rounded-xl border border-slate-100 p-4">
                        @if ($item['type'] === 'stok')
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                                <span class="material-symbols-outlined">
                                    {{ $d->jenis === 'masuk' ? 'move_to_inbox' : 'outbox' }}
                                </span>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm leading-relaxed text-slate-700">
                                    Stok
                                    <span class="font-semibold">
                                        {{ $d->alat->nama_alat ?? '-' }}
                                    </span>
                                    {{ $d->jenis === 'masuk' ? 'bertambah' : 'berkurang' }}
                                    {{ $d->jumlah }} unit
                                </p>
                                <p class="mt-2 text-xs text-slate-400">
                                    {{ $d->created_at->diffForHumans() }}
                                </p>
                            </div>
                        @else
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                                <span class="material-symbols-outlined">
                                    report
                                </span>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm leading-relaxed text-slate-700">
                                    Laporan kerusakan
                                    <span class="font-semibold">
                                        {{ $d->alat->nama_alat ?? '-' }}
                                    </span>
                                    oleh {{ $d->user->nama ?? '-' }}
                                </p>
                                <p class="mt-2 text-xs {{ $d->status === 'selesai' ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $d->status === 'selesai' ? 'Selesai ditangani' : 'Perlu tindak lanjut' }}
                                    &bull; {{ $d->created_at->diffForHumans() }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>

</div>
@endsection

@push('scripts')
<script>
    const trenCanvas = document.getElementById('trenChart');

    if (trenCanvas) {
        new Chart(trenCanvas, {
            type: 'line',
            data: {
                labels: @json($labelHari),
                datasets: [{
                    label: 'Pengajuan Peminjaman',
                    data: @json($dataTren),
                    borderColor: '#7c3aed',
                    backgroundColor: 'rgba(124, 58, 237, 0.10)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#7c3aed',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    const kategoriCanvas = document.getElementById('kategoriChart');

    if (kategoriCanvas) {
        new Chart(kategoriCanvas, {
            type: 'doughnut',
            data: {
                labels: @json($distribusi->pluck('nama_kategori')->values()),
                datasets: [{
                    data: @json($distribusi->pluck('total_stok')->values()),
                    backgroundColor: [
                        '#7c3aed',
                        '#059669',
                        '#6366f1',
                        '#f59e0b',
                        '#ec4899',
                        '#0ea5e9'
                    ],
                    borderWidth: 0,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
</script>
@endpush

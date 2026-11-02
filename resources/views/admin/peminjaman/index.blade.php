
@extends('layouts.admin')

@section('title', 'Verifikasi Peminjaman')

@section('content')

{{-- HEADER --}}
<div class="flex flex-col sm:flex-row sm:flex-wrap items-start sm:justify-between gap-3 sm:gap-4 mb-5 sm:mb-6">
    <div class="min-w-0">
        <p class="text-[11px] font-semibold text-violet-600 tracking-wide mb-0.5">
            OTORISASI LAB &bull; DISPOSISI
        </p>
        <h1 class="font-display font-bold text-xl sm:text-2xl text-slate-900">
            Verifikasi Pengajuan Peminjaman
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Tinjau kelayakan pengajuan peminjaman perangkat praktikum oleh user &amp; guru RPL.
        </p>
    </div>

    <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto shrink-0 no-print">
        @if ($tab === 'arsip')
            <a href="{{ route('admin.peminjaman.export-arsip') }}"
               class="inline-flex flex-1 sm:flex-none items-center justify-center gap-1.5 px-3 sm:px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl shadow-sm hover:bg-slate-50">
                <span class="material-symbols-outlined text-[17px]">download</span>
                Unduh Arsip
            </a>
        @else
            
            <button type="submit" form="formBulkApprove"
                    class="inline-flex flex-1 sm:flex-none items-center justify-center gap-1.5 px-3 sm:px-4 py-2 bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:opacity-95">
                <span class="material-symbols-outlined text-[17px]">done_all</span>
                Setujui Cepat
            </button>
        @endif
    </div>
</div>

{{-- ERROR --}}
@if ($errors->any())
    <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm space-y-1 no-print">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

{{-- STATISTIK --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-5 sm:mb-6 no-print">
    @php
        $statCards = [
            ['label' => 'MENUNGGU VERIFIKASI', 'value' => $stats['menunggu'], 'icon' => 'hourglass_top', 'color' => 'amber'],
            ['label' => 'SEDANG DIPINJAM', 'value' => $stats['disetujui'], 'icon' => 'sync_alt', 'color' => 'indigo'],
            ['label' => 'DITOLAK', 'value' => $stats['ditolak'], 'icon' => 'block', 'color' => 'red'],
            ['label' => 'SUDAH DIKEMBALIKAN', 'value' => $stats['dikembalikan'], 'icon' => 'task_alt', 'color' => 'emerald'],
        ];
    @endphp

    @foreach ($statCards as $card)
        <div class="bg-white rounded-2xl p-3 sm:p-4 shadow-sm">
            <div class="flex items-center justify-between gap-2 mb-2">
                <span class="text-[10px] sm:text-[11px] font-semibold text-slate-400 tracking-wide">
                    {{ $card['label'] }}
                </span>
                <div class="w-8 h-8 shrink-0 rounded-lg bg-{{ $card['color'] }}-100 flex items-center justify-center text-{{ $card['color'] }}-600">
                    <span class="material-symbols-outlined text-[17px]">{{ $card['icon'] }}</span>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-bold text-slate-900">
                {{ $card['value'] }}
            </div>
        </div>
    @endforeach
</div>

{{-- TAB STATUS --}}
<div class="overflow-x-auto mb-4 no-print">
    <div class="flex items-center gap-2 w-max pb-1">
        @php
            $tabs = [
                'semua' => 'Semua ('.$totalSemua.')',
                'menunggu' => 'Menunggu ('.$stats['menunggu'].')',
                'disetujui' => 'Sedang Dipinjam ('.$stats['disetujui'].')',
                'ditolak' => 'Ditolak ('.$stats['ditolak'].')',
                'dikembalikan' => 'Dikembalikan ('.$stats['dikembalikan'].')',
                'arsip' => 'Diarsipkan ('.$stats['arsip'].')',
            ];
        @endphp

        @foreach ($tabs as $key => $label)
            <a href="{{ route('admin.peminjaman.index', array_filter(['tab' => $key, 'cari' => request('cari')])) }}"
               class="px-3.5 py-2 rounded-full text-xs font-semibold whitespace-nowrap transition-all {{ $tab === $key ? 'bg-violet-600 text-white shadow-sm' : 'bg-white text-slate-500 hover:text-slate-800' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</div>

{{-- PENCARIAN --}}
<form method="GET" action="{{ route('admin.peminjaman.index') }}"
      class="bg-white rounded-2xl p-3 shadow-sm flex flex-col sm:flex-row gap-2 mb-4 no-print">

    <input type="hidden" name="tab" value="{{ $tab }}">

    <div class="relative w-full sm:flex-1 min-w-0">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">
            search
        </span>
        <input type="text" name="cari" value="{{ request('cari') }}"
               placeholder="Cari nama User, NIS/NIP, atau nama perangkat..."
               class="w-full h-10 pl-10 pr-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
    </div>

    <button type="submit"
            class="h-10 w-full sm:w-auto px-5 bg-slate-900 text-white text-sm font-semibold rounded-xl hover:bg-slate-800">
        Cari
    </button>
</form>

{{-- FORM SETUJUI CEPAT --}}
<form id="formBulkApprove" method="POST"
      action="{{ route('admin.peminjaman.bulk-approve') }}"
      onsubmit="return siapkanBulkApprove(event);">
    @csrf
</form>

{{-- TABEL PEMINJAMAN --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden peminjaman-responsive">
    <div class="table-container">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="font-semibold py-3 px-2 w-8 no-print"></th>
                    <th class="font-semibold py-3 px-2">ID PENGAJUAN</th>
                    <th class="font-semibold py-3 px-2">DATA PEMINJAM</th>
                    <th class="font-semibold py-3 px-2">PERANGKAT &amp; JUMLAH</th>
                    <th class="font-semibold py-3 px-2">KEPERLUAN</th>
                    <th class="font-semibold py-3 px-2">JADWAL PINJAM</th>
                    <th class="font-semibold py-3 px-2">PENANGGUNG JAWAB</th>
                    <th class="font-semibold py-3 px-2 no-print">KEPUTUSAN</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($peminjaman as $p)
                    <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/50 align-top">

                        {{-- CHECKBOX --}}
                        <td class="py-3 px-2 no-print" data-label="Pilih">
                            @if ($p->status === 'menunggu')
                                <input type="checkbox" class="checkbox-bulk w-4 h-4 rounded accent-violet-600 mt-1"
                                       value="{{ $p->id_peminjaman }}">
                            @endif
                        </td>

                        {{-- ID --}}
                        <td class="py-3 px-2" data-label="ID Pengajuan">
                            <span class="font-mono text-xs font-semibold text-violet-600">
                                REQ-{{ str_pad($p->id_peminjaman, 4, '0', STR_PAD_LEFT) }}
                            </span>
                            <div class="text-[10.5px] text-slate-400 mt-0.5">
                                Diajukan {{ $p->created_at->translatedFormat('d M, H:i') }}
                            </div>
                        </td>

                        {{-- PEMINJAM --}}
                        <td class="py-3 px-2" data-label="Data Peminjam">
                            <div class="font-semibold text-slate-800">
                                {{ $p->user->nama ?? '-' }}
                            </div>
                            <div class="text-xs text-slate-400">
                                {{ $p->user->nis ?? $p->user->nip ?? '-' }}
                                @if ($p->user->kelas)
                                    &bull; {{ $p->user->kelas }}
                                @endif
                                @if ($p->user->tipe_user === 'guru')
                                    &bull; Guru
                                @endif
                            </div>
                        </td>

                        {{-- PERANGKAT --}}
                        <td class="py-3 px-2 text-slate-600" data-label="Perangkat & Jumlah">
                            @forelse ($p->detail as $d)
                                <div class="mb-1">
                                    {{ $d->alat->nama_alat ?? '-' }}
                                    <span class="text-slate-400">&times;{{ $d->jumlah }}</span>

                                    @if ($p->status === 'dikembalikan' && ($d->jumlah_servis_kembali || $d->jumlah_rusak_kembali))
                                        <span class="block text-[10px] text-red-500">
                                            @if ($d->jumlah_servis_kembali)
                                                servis {{ $d->jumlah_servis_kembali }}
                                            @endif
                                            @if ($d->jumlah_rusak_kembali)
                                                rusak {{ $d->jumlah_rusak_kembali }}
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            @empty
                                <span class="text-slate-400">-</span>
                            @endforelse
                        </td>

                        {{-- KEPERLUAN --}}
                        <td class="py-3 px-2 text-slate-600" data-label="Keperluan">
                            <p class="line-clamp-2">{{ $p->deskripsi ?: '-' }}</p>
                        </td>

                        {{-- JADWAL --}}
                        <td class="py-3 px-2 text-xs text-slate-600" data-label="Jadwal Pinjam">
                            @if ($p->tanggal_pinjam)
                                <div>{{ $p->tanggal_pinjam->translatedFormat('d M Y') }}</div>
                                <div class="text-slate-400">
                                    s/d {{ $p->tanggal_kembali_rencana?->translatedFormat('d M Y') ?? '-' }}
                                </div>
                                @if ($p->isTerlambat())
                                    <span class="inline-block mt-1 px-1.5 py-0.5 bg-red-50 text-red-600 text-[10px] font-semibold rounded">
                                        Terlambat
                                    </span>
                                @endif
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        {{-- ADMIN --}}
                        <td class="py-3 px-2" data-label="Penanggung Jawab">
                            @if ($p->admin)
                                <div class="text-xs font-semibold text-slate-800">
                                    {{ $p->admin->nama }}
                                </div>
                                <div class="text-[10.5px] text-slate-400">
                                    {{ $p->status === 'ditolak' ? 'Menolak pengajuan' : 'Menyetujui pengajuan' }}
                                </div>
                            @else
                                <span class="text-xs text-slate-400">Belum diproses</span>
                            @endif
                        </td>

                        {{-- KEPUTUSAN --}}
                        <td class="py-3 px-2 no-print keputusan-cell" data-label="Keputusan">

                            @if ($p->status === 'menunggu')
                                <div class="keputusan-actions flex flex-wrap items-center gap-1.5">
                                    <form method="POST" action="{{ route('admin.peminjaman.approve', $p) }}">
                                        @csrf
                                        <button type="submit"
                                                class="px-2.5 py-1 bg-emerald-50 text-emerald-600 font-semibold text-xs rounded-lg hover:bg-emerald-100">
                                            Setuju
                                        </button>
                                    </form>

                                    <button type="button"
                                            onclick="document.getElementById('tolak-{{ $p->id_peminjaman }}').classList.toggle('hidden')"
                                            class="px-2.5 py-1 bg-red-50 text-red-600 font-semibold text-xs rounded-lg hover:bg-red-100">
                                        Tolak
                                    </button>
                                </div>

                                <form id="tolak-{{ $p->id_peminjaman }}"
                                      method="POST"
                                      action="{{ route('admin.peminjaman.reject', $p) }}"
                                      class="hidden w-full mt-2">
                                    @csrf
                                    <textarea name="alasan" rows="2" placeholder="Alasan penolakan..."
                                              required
                                              class="w-full text-xs p-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400"></textarea>
                                    <button type="submit"
                                            class="mt-1 w-full py-1 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700">
                                        Kirim Penolakan
                                    </button>
                                </form>

                            @else
                                @php
                                    $badge = [
                                        'disetujui' => 'bg-indigo-50 text-indigo-600',
                                        'ditolak' => 'bg-red-50 text-red-600',
                                        'dikembalikan' => 'bg-emerald-50 text-emerald-600',
                                    ][$p->status] ?? 'bg-slate-100 text-slate-600';

                                    $label = [
                                        'disetujui' => 'Sedang Dipinjam',
                                        'ditolak' => 'Ditolak',
                                        'dikembalikan' => 'Dikembalikan',
                                    ][$p->status] ?? ucfirst($p->status);
                                @endphp

                                <div class="keputusan-content">

                                    {{-- STATUS --}}
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="px-2 py-1 {{ $badge }} text-[11px] font-semibold rounded-full whitespace-nowrap">
                                            {{ $label }}
                                        </span>

                                        @if ($p->arsip)
                                            <span class="px-2 py-1 bg-slate-100 text-slate-500 text-[10px] font-semibold rounded-full whitespace-nowrap">
                                                Diarsipkan
                                            </span>
                                        @endif
                                    </div>

                                    {{-- FOTO BUKTI SISWA --}}
                                    @if ($p->foto_pengembalian)
                                        <div class="mobile-proof">
                                            <a href="{{ asset('storage/'.$p->foto_pengembalian) }}"
                                               target="_blank" rel="noopener noreferrer" class="proof-link">
                                                <img src="{{ asset('storage/'.$p->foto_pengembalian) }}"
                                                     alt="Bukti pengembalian dari user" loading="lazy"
                                                     class="w-16 h-16 rounded-lg object-cover border border-slate-200">
                                                <span class="block text-[9px] text-slate-400 mt-0.5">User</span>
                                            </a>
                                        </div>
                                    @endif

                                    {{-- FOTO BUKTI ADMIN --}}
                                    @if ($p->foto_bukti_admin ?? false)
                                        <div class="mobile-proof">
                                            <a href="{{ asset('storage/'.$p->foto_bukti_admin) }}"
                                               target="_blank" rel="noopener noreferrer" class="proof-link">
                                                <img src="{{ asset('storage/'.$p->foto_bukti_admin) }}"
                                                     alt="Bukti penerimaan admin" loading="lazy"
                                                     class="w-16 h-16 rounded-lg object-cover border border-slate-200">
                                                <span class="block text-[9px] text-emerald-600 mt-0.5">Admin</span>
                                            </a>
                                        </div>
                                    @endif

                                    {{-- PENGEMBALIAN --}}
                                    @if ($p->status === 'disetujui')

                                        <button type="button"
                                                onclick="togglePengembalian('terima-{{ $p->id_peminjaman }}', this)"
                                                class="text-[11px] font-semibold text-violet-600 hover:underline">
                                            Tandai Dikembalikan
                                        </button>

                                        <form id="terima-{{ $p->id_peminjaman }}"
                                              method="POST"
                                              action="{{ route('admin.peminjaman.returned', $p) }}"
                                              onsubmit="return cekKondisi(this);"
                                              class="hidden w-full p-3 bg-slate-50 rounded-xl space-y-3 text-left form-pengembalian">
                                            @csrf

                                            <input type="hidden" name="id_peminjaman"
                                                   value="{{ $p->id_peminjaman }}">

                                            <div class="form-heading">
                                                <p class="text-[10px] font-bold text-slate-600 tracking-wide">
                                                    KONDISI BARANG SAAT KEMBALI
                                                </p>
                                                <p class="text-[10px] text-slate-400 mt-0.5">
                                                    Atur jumlah unit sesuai kondisi barang.
                                                </p>
                                            </div>

                                            @foreach ($p->detail as $d)
                                                <div class="kondisi-item p-3 bg-white rounded-xl border border-slate-200"
                                                     data-jumlah="{{ $d->jumlah }}">

                                                    <p class="text-xs font-semibold text-slate-700 mb-3">
                                                        {{ $d->alat->nama_alat ?? '-' }}
                                                        <span class="text-slate-400 font-normal">&times;{{ $d->jumlah }}</span>
                                                    </p>

                                                    <div class="kondisi-grid">
                                                        <label class="kondisi-label kondisi-baik">
                                                            <span>Baik</span>
                                                            <input type="number" data-jenis="baik" readonly tabindex="-1"
                                                                   name="items[{{ $d->id_detail }}][baik]"
                                                                   value="{{ $d->jumlah }}">
                                                        </label>

                                                        <label class="kondisi-label kondisi-servis">
                                                            <span>Servis</span>
                                                            <input type="number" data-jenis="servis" min="0"
                                                                   max="{{ $d->jumlah }}"
                                                                   name="items[{{ $d->id_detail }}][servis]"
                                                                   value="0" required>
                                                        </label>

                                                        <label class="kondisi-label kondisi-rusak">
                                                            <span>Rusak</span>
                                                            <input type="number" data-jenis="rusak" min="0"
                                                                   max="{{ $d->jumlah }}"
                                                                   name="items[{{ $d->id_detail }}][rusak]"
                                                                   value="0" required>
                                                        </label>
                                                    </div>

                                                    <p class="kondisi-pesan hidden mt-2 text-[10px] text-red-600"></p>
                                                </div>
                                            @endforeach

                                            <p class="text-[10px] text-slate-500 leading-relaxed">
                                                <strong>Catatan:</strong> Baik terisi otomatis dari sisa unit setelah jumlah servis dan rusak ditentukan. Hanya unit baik yang kembali ke stok tersedia.
                                            </p>

                                            @if ($p->catatan_pengembalian)
                                                <div class="p-3 bg-white border border-slate-200 rounded-xl">
                                                    <p class="text-[10px] font-bold text-slate-500 mb-1">CATATAN USER</p>
                                                    <p class="text-xs text-slate-700 whitespace-pre-line">
                                                        {{ $p->catatan_pengembalian }}
                                                    </p>
                                                </div>
                                            @endif

                                            <button type="submit" @disabled(!$p->foto_pengembalian)
                                                    class="w-full py-2.5 text-xs font-semibold rounded-xl transition
                                                    {{ $p->foto_pengembalian
                                                        ? 'bg-emerald-600 text-white hover:bg-emerald-700'
                                                        : 'bg-slate-300 text-slate-500 cursor-not-allowed' }}">
                                                {{ $p->foto_pengembalian ? 'Konfirmasi Diterima' : 'Menunggu Bukti Foto' }}
                                            </button>
                                        </form>

                                    @else
                                        <form method="POST" action="{{ route('admin.peminjaman.arsip', $p) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="text-[11px] font-semibold text-slate-400 hover:text-amber-600 hover:underline">
                                                {{ $p->arsip ? 'Batalkan Arsip' : 'Arsipkan' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif

                            {{-- DETAIL MOBILE --}}
                            <button type="button" onclick="toggleDetail(this)"
                                    class="mobile-detail-button mt-2 w-full py-2 rounded-lg border border-violet-200 text-violet-600 text-xs font-semibold hover:bg-violet-50">
                                <span class="detail-label">Lihat Detail</span>
                                <span class="material-symbols-outlined text-sm align-middle detail-icon">expand_more</span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-10 text-center text-sm text-slate-400">
                            Tidak ada pengajuan peminjaman pada kategori ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if ($peminjaman->hasPages())
        <div class="px-4 py-3 border-t border-slate-100 no-print">
            {{ $peminjaman->links() }}
        </div>
    @endif
</div>

{{-- CSS --}}
<style>
    .mobile-detail-button {
        display: none;
    }

    /* DESKTOP & LAPTOP */
    @media (min-width: 768px) {
        .peminjaman-responsive {
            width: 100%;
            max-width: 100%;
            overflow: hidden;
        }

        .peminjaman-responsive .table-container {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            overflow-y: visible;
        }

        .peminjaman-responsive table {
            display: table;
            width: 100%;
            min-width: 0;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .peminjaman-responsive th,
        .peminjaman-responsive td {
            padding: 12px 10px;
            vertical-align: top;
            overflow-wrap: anywhere;
            word-break: normal;
        }

        .peminjaman-responsive th {
            font-size: 10px;
            white-space: normal;
        }

        /* LEBAR KOLOM DESKTOP */
        .peminjaman-responsive th:nth-child(1),
        .peminjaman-responsive td:nth-child(1) { width: 3%; }

        .peminjaman-responsive th:nth-child(2),
        .peminjaman-responsive td:nth-child(2) { width: 11%; }

        .peminjaman-responsive th:nth-child(3),
        .peminjaman-responsive td:nth-child(3) { width: 15%; }

        .peminjaman-responsive th:nth-child(4),
        .peminjaman-responsive td:nth-child(4) { width: 16%; }

        .peminjaman-responsive th:nth-child(5),
        .peminjaman-responsive td:nth-child(5) { width: 10%; }

        .peminjaman-responsive th:nth-child(6),
        .peminjaman-responsive td:nth-child(6) { width: 13%; }

        .peminjaman-responsive th:nth-child(7),
        .peminjaman-responsive td:nth-child(7) { width: 15%; }

        .peminjaman-responsive th:nth-child(8),
        .peminjaman-responsive td:nth-child(8) { width: 17%; }

        /* KEPUTUSAN */
        .keputusan-cell {
            text-align: left;
            min-width: 0;
        }

        .keputusan-content {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            width: 100%;
            min-width: 0;
            gap: 8px;
        }

        .keputusan-actions {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        /* FORM PENGEMBALIAN DESKTOP */
        .peminjaman-responsive .form-pengembalian {
            --form-expand: clamp(40px, 5vw, 80px);

            display: block;
            width: calc(100% + var(--form-expand));
            max-width: none;
            min-width: 0;

            /* Form melebar ke kiri, sisi kanan tetap */
            margin-left: calc(var(--form-expand) * -1);
            margin-right: 0;

            padding: 13px;
            box-sizing: border-box;
            text-align: left;
            overflow: visible;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }

        .peminjaman-responsive .form-pengembalian.hidden {
            display: none;
        }

        .form-heading {
            padding-bottom: 11px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .kondisi-item {
            width: 100%;
            padding: 14px;
            box-sizing: border-box;
            text-align: left;
        }

        .kondisi-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 8px;
            width: 100%;
        }

        .kondisi-label {
            display: block;
            min-width: 0;
            font-size: 10px;
            font-weight: 700;
        }

        .kondisi-label input {
            display: block;
            width: 100%;
            height: 39px;
            padding: 0 5px;
            margin-top: 4px;
            box-sizing: border-box;
            font-size: 13px;
            border-radius: 7px;
            border: 1px solid #e2e8f0;
        }

        .kondisi-baik { color: #059669; }
        .kondisi-servis { color: #d97706; }
        .kondisi-rusak { color: #dc2626; }

        .kondisi-baik input {
            color: #047857;
            background: #ecfdf5;
            border-color: #a7f3d0;
            cursor: not-allowed;
        }

        .kondisi-servis input {
            color: #92400e;
            background: #fffbeb;
            border-color: #fde68a;
        }

        .kondisi-rusak input {
            color: #b91c1c;
            background: #fef2f2;
            border-color: #fecaca;
        }

        .kondisi-label input:focus {
            outline: 2px solid #c4b5fd;
            outline-offset: 1px;
        }

        .mobile-detail-button {
            display: none !important;
        }
    }

    /* MOBILE */
    @media (max-width: 767px) {
        .mobile-detail-button {
            display: block;
        }

        .peminjaman-responsive {
            overflow: hidden;
        }

        .peminjaman-responsive .table-container {
            overflow: visible;
        }

        .peminjaman-responsive table {
            display: block;
            width: 100%;
            min-width: 0;
        }

        .peminjaman-responsive thead {
            display: none;
        }

        .peminjaman-responsive tbody {
            display: block;
            padding: 10px;
        }

        .peminjaman-responsive tbody tr {
            display: block;
            margin-bottom: 12px;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: white;
            box-shadow: 0 1px 3px rgba(15, 23, 42, .04);
        }

        .peminjaman-responsive tbody tr:last-child {
            margin-bottom: 0;
        }

        .peminjaman-responsive tbody td {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
            padding: 9px 0;
            border-bottom: 1px solid #f1f5f9;
            text-align: right;
            overflow-wrap: anywhere;
            box-sizing: border-box;
        }

        .peminjaman-responsive tbody td::before {
            content: attr(data-label);
            flex: 0 0 105px;
            text-align: left;
            color: #94a3b8;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .03em;
        }

        .peminjaman-responsive tbody td.no-print:first-child {
            justify-content: flex-start;
            padding-bottom: 5px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .peminjaman-responsive tbody td.no-print:first-child::before {
            content: "PILIH PENGAJUAN";
            flex-basis: 105px;
        }

        .peminjaman-responsive tbody tr:not(.show-detail) td:nth-child(5),
        .peminjaman-responsive tbody tr:not(.show-detail) td:nth-child(6),
        .peminjaman-responsive tbody tr:not(.show-detail) td:nth-child(7) {
            display: none;
        }

        .peminjaman-responsive tbody tr.show-detail td:nth-child(5),
        .peminjaman-responsive tbody tr.show-detail td:nth-child(6),
        .peminjaman-responsive tbody tr.show-detail td:nth-child(7) {
            display: flex;
        }

        .peminjaman-responsive tbody td:last-child {
            display: block;
            text-align: left;
            padding-top: 12px;
            border-bottom: 0;
        }

        .peminjaman-responsive tbody td:last-child::before {
            display: block;
            margin-bottom: 9px;
        }

        .peminjaman-responsive tbody td:last-child > div {
            align-items: stretch;
            width: 100%;
        }

        .peminjaman-responsive tbody td:last-child form {
            max-width: 100%;
        }

        .peminjaman-responsive tbody td:last-child button {
            min-height: 36px;
        }

        .peminjaman-responsive .form-pengembalian {
            width: 100%;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
            padding: 11px;
        }

        .peminjaman-responsive .form-pengembalian.hidden {
            display: none;
        }

        .kondisi-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 6px;
            width: 100%;
        }

        .kondisi-label {
            display: block;
            min-width: 0;
            font-size: 10px;
            font-weight: 700;
        }

        .kondisi-label input {
            display: block;
            width: 100%;
            height: 36px;
            margin-top: 5px;
            padding: 0 5px;
            box-sizing: border-box;
            border-radius: 7px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
            outline: none;
        }

        .kondisi-baik { color: #059669; }
        .kondisi-servis { color: #d97706; }
        .kondisi-rusak { color: #dc2626; }

        .kondisi-baik input {
            color: #047857;
            background: #ecfdf5;
            border-color: #a7f3d0;
        }

        .kondisi-servis input {
            color: #92400e;
            background: #fffbeb;
            border-color: #fde68a;
        }

        .kondisi-rusak input {
            color: #b91c1c;
            background: #fef2f2;
            border-color: #fecaca;
        }

        .peminjaman-responsive tbody td[colspan] {
            display: block;
            text-align: center;
        }

        .peminjaman-responsive tbody td[colspan]::before {
            display: none;
        }
    }

    /* PRINT */
    @media print {
        .no-print,
        .mobile-detail-button {
            display: none !important;
        }

        .peminjaman-responsive {
            overflow: visible !important;
            box-shadow: none !important;
        }

        .peminjaman-responsive .table-container {
            overflow: visible !important;
        }

        .peminjaman-responsive table {
            display: table !important;
            width: 100% !important;
            min-width: 0 !important;
            table-layout: auto !important;
        }

        .peminjaman-responsive thead {
            display: table-header-group !important;
        }

        .peminjaman-responsive tbody {
            display: table-row-group !important;
            padding: 0 !important;
        }

        .peminjaman-responsive tbody tr {
            display: table-row !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .peminjaman-responsive tbody td,
        .peminjaman-responsive tbody tr:not(.show-detail) td:nth-child(5),
        .peminjaman-responsive tbody tr:not(.show-detail) td:nth-child(6),
        .peminjaman-responsive tbody tr:not(.show-detail) td:nth-child(7) {
            display: table-cell !important;
            text-align: left !important;
            padding: 8px !important;
        }

        .peminjaman-responsive tbody td::before {
            display: none !important;
        }
    }
</style>

{{-- JAVASCRIPT --}}
<script>
    // Detail mobile
    function toggleDetail(button) {
        const row = button.closest('tr');
        const isOpen = row.classList.toggle('show-detail');

        button.querySelector('.detail-label').textContent =
            isOpen ? 'Tutup Detail' : 'Lihat Detail';

        button.querySelector('.detail-icon').textContent =
            isOpen ? 'expand_less' : 'expand_more';
    }

    // Buka/tutup pemeriksaan pengembalian
    function togglePengembalian(id, button) {
        const form = document.getElementById(id);

        if (!form) return;

        const isHidden = form.classList.contains('hidden');

        form.classList.toggle('hidden');

        button.textContent = isHidden
            ? 'Tutup Pemeriksaan'
            : 'Tandai Dikembalikan';
    }

    // Hitung unit baik otomatis
    document.addEventListener('input', function (e) {
        const el = e.target;
        const kotak = el.closest ? el.closest('.kondisi-item') : null;

        if (!kotak || !el.dataset.jenis || el.dataset.jenis === 'baik') {
            return;
        }

        const total = parseInt(kotak.dataset.jumlah, 10);
        const servis = kotak.querySelector('[data-jenis="servis"]');
        const rusak = kotak.querySelector('[data-jenis="rusak"]');
        const baik = kotak.querySelector('[data-jenis="baik"]');
        const lainnya = el === servis ? rusak : servis;

        const mentah = parseInt(el.value, 10);
        const nilaiLain = parseInt(lainnya.value || '0', 10) || 0;

        const nilai = isNaN(mentah)
            ? 0
            : Math.max(0, Math.min(mentah, total - nilaiLain));

        if (!isNaN(mentah) && nilai !== mentah) {
            el.value = nilai;
        }

        baik.value = total
            - (parseInt(servis.value || '0', 10) || 0)
            - (parseInt(rusak.value || '0', 10) || 0);

        kotak.querySelector('.kondisi-pesan').classList.add('hidden');
    });

    // Validasi kondisi barang
    function cekKondisi(form) {
        let aman = true;

        form.querySelectorAll('.kondisi-item').forEach(function (kotak) {
            const target = parseInt(kotak.dataset.jumlah, 10);
            let total = 0;

            kotak.querySelectorAll('input[data-jenis]').forEach(function (input) {
                total += parseInt(input.value || '0', 10);
            });

            const pesan = kotak.querySelector('.kondisi-pesan');

            if (total !== target) {
                pesan.textContent = 'Total harus ' + target + ' unit (sekarang ' + total + ').';
                pesan.classList.remove('hidden');
                aman = false;
            } else {
                pesan.classList.add('hidden');
            }
        });

        return aman;
    }

    // Setujui cepat
    function siapkanBulkApprove(event) {
        const form = document.getElementById('formBulkApprove');
        const checked = document.querySelectorAll('.checkbox-bulk:checked');

        form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());

        if (checked.length === 0) {
            alert('Centang minimal satu pengajuan dulu ya.');
            event.preventDefault();
            return false;
        }

        checked.forEach(cb => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'ids[]';
            hidden.value = cb.value;
            form.appendChild(hidden);
        });

        return confirm('Setujui ' + checked.length + ' pengajuan yang dicentang?');
    }
</script>

@endsection

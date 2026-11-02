@extends('layouts.admin')

@section('title', 'Kelola Stok Barang')

@section('content')

<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <p class="text-[11px] font-semibold text-violet-600 tracking-wide mb-0.5">INVENTA // RPL-HUB-01</p>
        <h1 class="font-display font-bold text-2xl text-slate-900">Manajemen &amp; Kelola Stok Alat Lab</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kelola inventaris manual, pantau kondisi barang, dan riwayat perubahan stok secara real-time.</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        @if ($tab === 'arsip')
            <a href="{{ route('admin.barang.export-arsip') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl shadow-sm hover:bg-slate-50">
                <span class="material-symbols-outlined text-[17px]">download</span> Unduh Arsip
            </a>
        @endif
        <a href="{{ route('admin.barang.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:opacity-95">
            <span class="material-symbols-outlined text-[17px]">add</span> Tambah Alat Baru
        </a>
    </div>
</div>

{{-- Tab Aktif / Arsip --}}
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('admin.barang.index', ['tab' => 'aktif']) }}"
       class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all {{ $tab === 'aktif' ? 'bg-violet-600 text-white shadow-sm' : 'bg-white text-slate-500 hover:text-slate-800' }}">
        Aktif
    </a>
    <a href="{{ route('admin.barang.index', ['tab' => 'arsip']) }}"
       class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all {{ $tab === 'arsip' ? 'bg-violet-600 text-white shadow-sm' : 'bg-white text-slate-500 hover:text-slate-800' }}">
        Diarsipkan ({{ $stats['total_arsip'] }})
    </a>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 tracking-wide">TOTAL PERANGKAT</span>
            <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center text-violet-600">
                <span class="material-symbols-outlined text-[17px]">inventory_2</span>
            </div>
        </div>
        <div class="text-2xl font-bold text-slate-900">{{ number_format($stats['total_perangkat']) }} <span class="text-sm font-medium text-slate-400">Unit</span></div>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 tracking-wide">TOTAL KATEGORI</span>
            <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                <span class="material-symbols-outlined text-[17px]">category</span>
            </div>
        </div>
        <div class="text-2xl font-bold text-slate-900">{{ $stats['total_kategori'] }} <span class="text-sm font-medium text-slate-400">Kategori</span></div>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 tracking-wide">KONDISI BAIK</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-[17px]">check_circle</span>
            </div>
        </div>
        <div class="text-2xl font-bold text-slate-900">{{ number_format($stats['kondisi_baik']) }} <span class="text-sm font-medium text-slate-400">Unit</span></div>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 tracking-wide">PERLU PERHATIAN</span>
            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-[17px]">build</span>
            </div>
        </div>
        <div class="text-2xl font-bold text-slate-900">{{ $stats['perlu_perhatian'] }} <span class="text-sm font-medium text-slate-400">Jenis Alat</span></div>
    </div>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('admin.barang.index') }}" class="bg-white rounded-2xl p-3 shadow-sm flex flex-wrap gap-2 mb-4">
    <input type="hidden" name="tab" value="{{ $tab }}">
    <div class="relative flex-1 min-w-[200px]">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama atau kode alat..."
            class="w-full h-10 pl-10 pr-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
    </div>
    <select name="kategori" onchange="this.form.submit()" class="h-10 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
        <option value="">Semua Kategori</option>
        @foreach ($kategoriList as $k)
            <option value="{{ $k->id_kategori }}" {{ (string) request('kategori') === (string) $k->id_kategori ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
        @endforeach
    </select>
    <button type="submit" class="h-10 px-4 bg-slate-900 text-white text-sm font-semibold rounded-xl hover:bg-slate-800">Cari</button>
    @if (request('cari') || request('kategori'))
        <a href="{{ route('admin.barang.index') }}" class="h-10 px-4 flex items-center text-sm font-medium text-slate-500 hover:text-slate-700">Reset</a>
    @endif
</form>

{{-- Tabel alat --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="font-semibold py-3 px-4">KODE &amp; NAMA ALAT</th>
                    <th class="font-semibold py-3 px-4">KATEGORI</th>
                    <th class="font-semibold py-3 px-4">STOK</th>
                    <th class="font-semibold py-3 px-4">KONDISI</th>
                    <th class="font-semibold py-3 px-4">SESUAIKAN STOK</th>
                    <th class="font-semibold py-3 px-4 text-right">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($alat as $item)
                    <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/50">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center overflow-hidden shrink-0">
                                    @if ($item->foto_url)
                                        <img src="{{ $item->foto_url }}" alt="{{ $item->nama_alat }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="material-symbols-outlined text-slate-300 text-[20px]">memory</span>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-800">{{ $item->nama_alat }}</div>
                                    <div class="text-xs text-slate-400 font-mono">{{ $item->kode_alat }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-slate-600">{{ $item->kategori->nama_kategori ?? '-' }}</td>
                        <td class="py-3 px-4">
                            <span class="font-bold text-slate-900">{{ $item->jumlah_stok }}</span>
                            <span class="text-xs text-slate-400">unit</span>
                        </td>
                        <td class="py-3 px-4">
                            @php
                                $badge = [
                                    'baik' => 'bg-emerald-50 text-emerald-600',
                                    'perlu_servis' => 'bg-amber-50 text-amber-600',
                                    'rusak' => 'bg-red-50 text-red-600',
                                ][$item->kondisi];
                                $label = ['baik' => 'Baik', 'perlu_servis' => 'Perlu Servis', 'rusak' => 'Rusak'][$item->kondisi];
                            @endphp
                            <span class="px-2 py-0.5 {{ $badge }} text-[11px] font-semibold rounded-full">{{ $label }}</span>
                            @if ($item->arsip)
                                <span class="block mt-1 px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-semibold rounded-full w-fit">Diarsipkan</span>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <form method="POST" action="{{ route('admin.barang.stok', $item) }}" class="flex items-center gap-1">
                                @csrf
                                <input type="number" name="jumlah" min="1" value="1" required
                                    class="w-14 h-8 px-2 bg-slate-50 border border-slate-200 text-xs rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500">
                                <button type="submit" name="jenis" value="masuk" title="Tambah stok"
                                    class="w-8 h-8 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                </button>
                                <button type="submit" name="jenis" value="keluar" title="Kurangi stok"
                                    class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-600 rounded-lg hover:bg-red-100">
                                    <span class="material-symbols-outlined text-[16px]">remove</span>
                                </button>
                            </form>
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.barang.edit', $item) }}" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-violet-600 hover:bg-violet-50 rounded-lg" title="Ubah">
                                    <span class="material-symbols-outlined text-[17px]">edit</span>
                                </a>
                                <form method="POST" action="{{ route('admin.barang.arsip', $item) }}">
                                    @csrf
                                    <button type="submit" title="{{ $item->arsip ? 'Batalkan arsip' : 'Arsipkan' }}"
                                        class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg">
                                        <span class="material-symbols-outlined text-[17px]">{{ $item->arsip ? 'unarchive' : 'archive' }}</span>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.barang.destroy', $item) }}" onsubmit="return confirm('Hapus barang ini secara permanen? Tindakan tidak bisa dibatalkan.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Hapus permanen" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                        <span class="material-symbols-outlined text-[17px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-sm text-slate-400">Belum ada data alat. Klik "Tambah Alat Baru" untuk mulai.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($alat->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $alat->links() }}
        </div>
    @endif
</div>

{{-- Log perubahan stok terakhir --}}
<div class="bg-white rounded-2xl p-5 shadow-sm">
    <h2 class="font-semibold text-sm text-slate-900 mb-3">Log Perubahan Stok Terakhir</h2>
    @if ($logTerakhir->isEmpty())
        <p class="text-sm text-slate-400 text-center py-6">Belum ada riwayat perubahan stok.</p>
    @else
        <div class="space-y-3">
            @foreach ($logTerakhir as $log)
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg {{ $log->jenis === 'masuk' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[15px]">{{ $log->jenis === 'masuk' ? 'move_to_inbox' : 'outbox' }}</span>
                        </div>
                        <div>
                            <p class="text-slate-700">
                                <span class="font-semibold">{{ $log->alat->nama_alat ?? '-' }}</span>
                                {{ $log->jenis === 'masuk' ? 'bertambah' : 'berkurang' }} {{ $log->jumlah }} unit
                            </p>
                            <p class="text-[10.5px] text-slate-400">{{ $log->keterangan }} &bull; oleh {{ $log->admin->nama ?? '-' }}</p>
                        </div>
                    </div>
                    <span class="text-slate-400 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
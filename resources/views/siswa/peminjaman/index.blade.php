@extends('layouts.siswa')

@section('title', 'Riwayat Peminjaman')

@section('content')

<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <p class="text-[11px] font-semibold text-violet-600 tracking-wide mb-0.5">INVENTA // RIWAYAT SAYA</p>
        <h1 class="font-display font-bold text-2xl text-slate-900">Riwayat &amp; Status Peminjaman</h1>
        <p class="text-sm text-slate-500 mt-0.5">Pantau status semua pengajuan peminjaman kamu di sini.</p>
    </div>
    <a href="{{ route('siswa.peminjaman.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:opacity-95 shrink-0">
        <span class="material-symbols-outlined text-[17px]">add_task</span> Ajukan Baru
    </a>
</div>

{{-- Tabs --}}
<div class="flex flex-wrap items-center gap-2 mb-5">
    @php
        $tabs = [
            'semua' => 'Semua ('.$stats['semua'].')',
            'menunggu' => 'Menunggu ('.$stats['menunggu'].')',
            'disetujui' => 'Sedang Dipinjam ('.$stats['disetujui'].')',
            'ditolak' => 'Ditolak ('.$stats['ditolak'].')',
            'dikembalikan' => 'Selesai ('.$stats['dikembalikan'].')',
        ];
    @endphp
    @foreach ($tabs as $key => $label)
        <a href="{{ route('siswa.peminjaman.index', ['tab' => $key]) }}"
           class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all {{ $tab === $key ? 'bg-violet-600 text-white shadow-sm' : 'bg-white text-slate-500 hover:text-slate-800' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

@if ($errors->any())
    <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

@if ($peminjaman->isEmpty())
    <div class="bg-white rounded-2xl p-10 shadow-sm text-center">
        <span class="material-symbols-outlined text-slate-300 text-[36px] mb-2 block">inbox</span>
        <p class="text-sm text-slate-400">Belum ada pengajuan peminjaman di kategori ini.</p>
    </div>
@else
    <div class="space-y-3">
        @foreach ($peminjaman as $p)
            @php
                $badge = [
                    'menunggu' => 'bg-amber-50 text-amber-600',
                    'disetujui' => 'bg-indigo-50 text-indigo-600',
                    'ditolak' => 'bg-red-50 text-red-600',
                    'dikembalikan' => 'bg-emerald-50 text-emerald-600',
                ][$p->status];
                $label = [
                    'menunggu' => 'Menunggu Verifikasi',
                    'disetujui' => 'Sedang Dipinjam',
                    'ditolak' => 'Ditolak',
                    'dikembalikan' => 'Selesai Dikembalikan',
                ][$p->status];
            @endphp
            <div class="bg-white rounded-2xl p-4 shadow-sm" id="p-{{ $p->id_peminjaman }}">
                <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                    <span class="font-mono text-xs font-semibold text-violet-600">REQ-{{ str_pad($p->id_peminjaman, 4, '0', STR_PAD_LEFT) }}</span>
                    <div class="flex items-center gap-1.5">
                        @if ($p->isTerlambat())
                            <span class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-semibold rounded-full">Terlambat</span>
                        @elseif ($p->status === 'disetujui' && $p->isJatuhTempoHariIni())
                            <span class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-semibold rounded-full">Jatuh Tempo Hari Ini</span>
                        @endif
                        <span class="px-2 py-0.5 {{ $badge }} text-[11px] font-semibold rounded-full">{{ $label }}</span>
                    </div>
                </div>

                @foreach ($p->detail as $d)
                    <p class="text-sm font-semibold text-slate-800">{{ $d->alat->nama_alat ?? '-' }} <span class="text-slate-400 font-normal">&times;{{ $d->jumlah }}</span></p>
                @endforeach

                <p class="text-xs text-slate-500 mt-1">{{ $p->deskripsi }}</p>

                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-3 pt-3 border-t border-slate-50 text-[11px] text-slate-500">
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px]">event</span>
                        Pinjam: {{ $p->tanggal_pinjam?->translatedFormat('d M Y') ?? '-' }}
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-[13px]">event_upcoming</span>
                        Rencana Kembali: {{ $p->tanggal_kembali_rencana?->translatedFormat('d M Y') ?? '-' }}
                    </span>
                    @if ($p->tanggal_kembali_aktual)
                        <span class="flex items-center gap-1 text-emerald-600">
                            <span class="material-symbols-outlined text-[13px]">task_alt</span>
                            Dikembalikan: {{ $p->tanggal_kembali_aktual->translatedFormat('d M Y') }}
                        </span>
                    @endif
                    @if ($p->admin)
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">person</span>
                            Diverifikasi oleh: {{ $p->admin->nama }}
                        </span>
                    @endif
                </div>

                @if ($p->status === 'ditolak' && $p->deskripsi && str_contains($p->deskripsi, 'Ditolak:'))
                    <div class="mt-2 p-2.5 bg-red-50 rounded-lg text-xs text-red-600">
                        {{ \Illuminate\Support\Str::after($p->deskripsi, 'Ditolak:') }}
                    </div>
                @endif

                {{-- ===== Pengembalian ===== --}}
                @if ($p->status === 'disetujui')
                    <div class="mt-3 pt-3 border-t border-slate-50">
                        @if ($p->diajukan_kembali_pada)
                            <div class="flex flex-wrap items-start gap-3 bg-amber-50 border border-amber-100 rounded-xl p-3">
                                <a href="{{ asset('storage/'.$p->foto_pengembalian) }}" target="_blank" class="shrink-0">
                                    <img src="{{ asset('storage/'.$p->foto_pengembalian) }}" alt="Bukti pengembalian" class="w-16 h-16 object-cover rounded-lg">
                                </a>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-amber-700">Menunggu konfirmasi pengembalian</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        Diajukan {{ $p->diajukan_kembali_pada->translatedFormat('d M Y, H:i') }}.
                                        Serahkan alat ke petugas lab untuk dicek &amp; dikonfirmasi.
                                    </p>
                                    @if ($p->catatan_pengembalian)
                                        <p class="text-[11px] text-slate-600 mt-1">Catatan: {{ $p->catatan_pengembalian }}</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <button type="button" onclick="toggleKembali({{ $p->id_peminjaman }})"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-violet-50 text-violet-700 text-xs font-semibold rounded-xl hover:bg-violet-100">
                                <span class="material-symbols-outlined text-[16px]">assignment_return</span> Ajukan Pengembalian
                            </button>

                            <form id="kembali-{{ $p->id_peminjaman }}" method="POST" enctype="multipart/form-data"
                                  action="{{ route('siswa.peminjaman.kembalikan', $p) }}"
                                  class="{{ old('kembali_id') == $p->id_peminjaman ? '' : 'hidden' }} mt-3 space-y-3 bg-slate-50 rounded-xl p-3">
                                @csrf
                                <input type="hidden" name="kembali_id" value="{{ $p->id_peminjaman }}">

                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 tracking-wide mb-1.5">FOTO KONDISI ALAT SAAT DIKEMBALIKAN *</label>
                                    <div class="flex items-center gap-3">
                                        <div class="w-16 h-16 rounded-xl bg-white border border-dashed border-slate-300 flex items-center justify-center overflow-hidden shrink-0">
                                            <img id="prev-{{ $p->id_peminjaman }}" src="" alt="" class="hidden w-full h-full object-cover">
                                            <span id="ph-{{ $p->id_peminjaman }}" class="material-symbols-outlined text-slate-300 text-[24px]">add_a_photo</span>
                                        </div>
                                        <input type="file" name="foto_pengembalian" accept="image/*" required
                                            onchange="siapkanFoto(this, {{ $p->id_peminjaman }})"
                                            class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-violet-50 file:text-violet-600 file:text-xs file:font-semibold hover:file:bg-violet-100">
                                    </div>
                                    <p class="text-[10.5px] text-slate-400 mt-1">Foto semua bagian alat beserta kelengkapannya. Foto besar dikecilkan otomatis.</p>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 tracking-wide mb-1.5">CATATAN (OPSIONAL)</label>
                                    <textarea name="catatan_pengembalian" rows="2" placeholder="Mis. semua lengkap, ada goresan kecil di bodi..."
                                        class="w-full text-xs p-2.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-500">{{ old('kembali_id') == $p->id_peminjaman ? old('catatan_pengembalian') : '' }}</textarea>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="submit" class="px-4 py-2 bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-xs font-semibold rounded-lg shadow-sm hover:opacity-95">
                                        Kirim Pengajuan Pengembalian
                                    </button>
                                    <button type="button" onclick="toggleKembali({{ $p->id_peminjaman }})" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-700">Batal</button>
                                </div>
                            </form>
                        @endif
                    </div>
                @elseif ($p->status === 'dikembalikan' && $p->foto_pengembalian)
                    <div class="mt-3 pt-3 border-t border-slate-50 flex items-center gap-3">
                        <a href="{{ asset('storage/'.$p->foto_pengembalian) }}" target="_blank" class="shrink-0">
                            <img src="{{ asset('storage/'.$p->foto_pengembalian) }}" alt="Bukti pengembalian" class="w-12 h-12 object-cover rounded-lg">
                        </a>
                        <p class="text-[11px] text-slate-500">Bukti kondisi alat saat dikembalikan (diunggah {{ $p->diajukan_kembali_pada?->translatedFormat('d M Y') }}).</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if ($peminjaman->hasPages())
        <div class="mt-5">
            {{ $peminjaman->links() }}
        </div>
    @endif
@endif

<script>
    function toggleKembali(id) {
        document.getElementById('kembali-' + id).classList.toggle('hidden');
    }

    // Pratinjau foto + kecilkan otomatis kalau ukurannya besar (foto HP biasanya 3-8 MB)
    async function siapkanFoto(input, id) {
        const file = input.files && input.files[0];
        if (!file || !file.type.startsWith('image/')) { return; }

        let hasil = file;

        if (file.size > 1.5 * 1024 * 1024) {
            try {
                const bitmap = await createImageBitmap(file);
                const skala = Math.min(1, 1600 / Math.max(bitmap.width, bitmap.height));
                const canvas = document.createElement('canvas');
                canvas.width = Math.round(bitmap.width * skala);
                canvas.height = Math.round(bitmap.height * skala);
                canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);

                const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.8));
                if (blob) {
                    hasil = new File([blob], file.name.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg' });
                    const dt = new DataTransfer();
                    dt.items.add(hasil);
                    input.files = dt.files;
                }
            } catch (e) {
                // Kalau gagal dikecilkan, kirim foto aslinya saja.
            }
        }

        const reader = new FileReader();
        reader.onload = e => {
            const prev = document.getElementById('prev-' + id);
            prev.src = e.target.result;
            prev.classList.remove('hidden');
            document.getElementById('ph-' + id).classList.add('hidden');
        };
        reader.readAsDataURL(hasil);
    }
</script>

@endsection
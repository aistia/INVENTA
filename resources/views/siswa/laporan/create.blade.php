@extends('layouts.siswa')

@section('title', 'Lapor Kerusakan')

@section('content')

<div class="mb-6">
    <p class="text-[11px] font-semibold text-violet-600 tracking-wide mb-0.5">INVENTA // LAPORAN KERUSAKAN</p>
    <h1 class="font-display font-bold text-2xl text-slate-900">Lapor Kerusakan Alat</h1>
    <p class="text-sm text-slate-500 mt-0.5">Temukan alat rusak atau bermasalah? Laporkan ke petugas lab di sini.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm">
        @if ($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('siswa.laporan.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">ALAT YANG BERMASALAH *</label>
                {{-- data-sheet-select: di HP dibuka sebagai lembar "pilih kategori dulu, lalu alatnya".
                     Alat dikelompokkan per kategori lewat <optgroup>. Di desktop tetap dropdown biasa. --}}
                <select name="alat_id" required
                    data-sheet-select data-sheet-title="Alat yang Bermasalah"
                    class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
                    <option value="" disabled {{ $alatTerpilih ? '' : 'selected' }}>-- Pilih alat --</option>

                    @foreach ($alatList->groupBy(fn ($a) => $a->kategori->nama_kategori ?? 'Lainnya')->sortKeys() as $namaKategori => $items)
                        <optgroup label="{{ $namaKategori }}">
                            @foreach ($items as $a)
                                <option value="{{ $a->id_alat }}" {{ old('alat_id', $alatTerpilih?->id_alat) == $a->id_alat ? 'selected' : '' }}>
                                    {{ $a->nama_alat }} &mdash; {{ $a->kode_alat }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">DESKRIPSI KERUSAKAN *</label>
                <textarea name="deskripsi" rows="4" required placeholder="Jelaskan kerusakan/kendala yang kamu temukan sedetail mungkin..."
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">{{ old('deskripsi') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">FOTO BUKTI *</label>
                <div class="flex items-center gap-3">
                    <div class="w-20 h-20 rounded-xl bg-slate-50 border border-dashed border-slate-300 flex items-center justify-center overflow-hidden shrink-0">
                        <img id="fotoPreview" src="" alt="" class="hidden w-full h-full object-cover">
                        <span id="fotoPlaceholder" class="material-symbols-outlined text-slate-300 text-[28px]">add_a_photo</span>
                    </div>
                    <input type="file" name="foto_bukti" accept="image/*" required
                        onchange="previewFoto(this)"
                        class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-violet-50 file:text-violet-600 file:text-xs file:font-semibold hover:file:bg-violet-100">
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:opacity-95">
                    Kirim Laporan
                </button>
                <a href="{{ route('siswa.barang.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-500 hover:text-slate-700">Batal</a>
            </div>
        </form>
    </div>

    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5 h-fit">
        <div class="flex items-center gap-2 mb-2">
            <span class="material-symbols-outlined text-amber-600 text-[18px]">warning</span>
            <h2 class="font-semibold text-sm text-slate-900">Catatan Penting</h2>
        </div>
        <ul class="text-xs text-slate-600 space-y-1.5 leading-relaxed">
            <li>&bull; Jangan tahan/sembunyikan alat rusak, segera laporkan.</li>
            <li>&bull; Sertakan foto bukti, mempercepat diagnosis petugas.</li>
            <li>&bull; Status alat otomatis berubah "Perlu Servis" sampai laporan ditangani.</li>
        </ul>
    </div>
</div>

{{-- Riwayat laporan terbaru --}}
<div class="bg-white rounded-2xl p-5 shadow-sm">
    <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-sm text-slate-900">Laporan Terbaru Saya</h2>
        <a href="{{ route('siswa.laporan.index') }}" class="text-xs font-semibold text-violet-600 hover:underline">Lihat Semua Riwayat</a>
    </div>

    @if ($laporanTerbaru->isEmpty())
        <p class="text-sm text-slate-400 text-center py-8">Belum pernah lapor kerusakan.</p>
    @else
        <div class="space-y-3">
            @foreach ($laporanTerbaru as $lap)
                <div class="flex items-center justify-between gap-3 py-2 border-b border-slate-50 last:border-0">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg {{ $lap->status === 'selesai' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[16px]">{{ $lap->status === 'selesai' ? 'check' : 'build' }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-700 truncate">{{ $lap->alat->nama_alat ?? '-' }}</p>
                            <p class="text-[10.5px] text-slate-400">{{ $lap->created_at->translatedFormat('d M Y') }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 {{ $lap->status === 'selesai' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} text-[10px] font-semibold rounded-full whitespace-nowrap">
                        {{ $lap->status === 'selesai' ? 'Selesai' : 'Diproses' }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    function previewFoto(input) {
        const preview = document.getElementById('fotoPreview');
        const placeholder = document.getElementById('fotoPlaceholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
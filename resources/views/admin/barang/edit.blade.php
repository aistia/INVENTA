@extends('layouts.admin')

@section('title', 'Ubah Alat')

@section('content')

<div class="mb-6">
    <p class="text-[11px] font-semibold text-violet-600 tracking-wide mb-0.5">INVENTA // RPL-HUB-01</p>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.barang.index') }}" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-700 bg-white rounded-lg shadow-sm">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        </a>
        <h1 class="font-display font-bold text-2xl text-slate-900">Ubah Data Alat</h1>
    </div>
</div>

<div class="bg-white rounded-2xl p-6 shadow-sm max-w-2xl">
    @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="flex items-center gap-2 mb-5 p-3 bg-slate-50 rounded-xl">
        <span class="material-symbols-outlined text-slate-400 text-[18px]">info</span>
        <p class="text-xs text-slate-500">
            Stok saat ini: <span class="font-bold text-slate-800">{{ $alat->jumlah_stok }} unit</span>.
            Untuk menambah/mengurangi stok, gunakan tombol +/- di halaman daftar barang (supaya tercatat di log).
        </p>
    </div>

    <form method="POST" action="{{ route('admin.barang.update', $alat) }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">FOTO ALAT</label>
            <div class="flex items-center gap-3">
                <div class="w-20 h-20 rounded-xl bg-slate-50 border border-dashed border-slate-300 flex items-center justify-center overflow-hidden shrink-0">
                    <img id="fotoPreview" src="{{ $alat->foto_url }}" alt="" class="{{ $alat->foto_url ? '' : 'hidden' }} w-full h-full object-cover">
                    <span id="fotoPlaceholder" class="material-symbols-outlined text-slate-300 text-[28px] {{ $alat->foto_url ? 'hidden' : '' }}">image</span>
                </div>
                <input type="file" name="foto" id="foto" accept="image/*" onchange="previewFoto(this)"
                    class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-violet-50 file:text-violet-600 file:text-xs file:font-semibold hover:file:bg-violet-100">
            </div>
            @error('foto') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">KODE ALAT</label>
                <input type="text" name="kode_alat" value="{{ old('kode_alat', $alat->kode_alat) }}"
                    class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">NAMA ALAT *</label>
                <input type="text" name="nama_alat" value="{{ old('nama_alat', $alat->nama_alat) }}" required
                    class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">KATEGORI *</label>
                <select name="id_kategori" id="id_kategori" class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
                    @foreach ($kategoriList as $k)
                        <option value="{{ $k->id_kategori }}" {{ old('id_kategori', $alat->id_kategori) == $k->id_kategori ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                    @endforeach
                    <option value="__baru__">+ Kategori baru...</option>
                </select>
                <input type="text" name="kategori_baru" id="kategori_baru" value="{{ old('kategori_baru') }}" placeholder="Nama kategori baru"
                    class="hidden w-full h-11 mt-2 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">KONDISI *</label>
                <select name="kondisi" required class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
                    <option value="baik" {{ old('kondisi', $alat->kondisi) === 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="perlu_servis" {{ old('kondisi', $alat->kondisi) === 'perlu_servis' ? 'selected' : '' }}>Perlu Servis</option>
                    <option value="rusak" {{ old('kondisi', $alat->kondisi) === 'rusak' ? 'selected' : '' }}>Rusak</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">DESKRIPSI</label>
                <textarea name="deskripsi" rows="3"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">{{ old('deskripsi', $alat->deskripsi) }}</textarea>
            </div>
        </div>

        <div class="flex items-center gap-2 pt-2">
            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:opacity-95">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.barang.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-500 hover:text-slate-700">Batal</a>
        </div>
    </form>
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

    const selectKategori = document.getElementById('id_kategori');
    const inputBaru = document.getElementById('kategori_baru');
    selectKategori.addEventListener('change', function () {
        const isBaru = this.value === '__baru__';
        inputBaru.classList.toggle('hidden', !isBaru);
        if (isBaru) { this.name = ''; inputBaru.focus(); } else { this.name = 'id_kategori'; }
    });
</script>
@endsection
@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')

@php
    // Kalau submit gagal (validasi error), otomatis buka mode Edit lagi
    // supaya pesan error kelihatan, bukan ketutup di mode Lihat.
    $bukaEdit = $errors->any();
@endphp

<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <p class="text-[11px] font-semibold text-violet-600 tracking-wide mb-0.5">PORTAL OTORITAS LABORATORIUM RPL</p>
        <h1 class="font-display font-bold text-2xl text-slate-900">Profil Petugas Lab</h1>
        <p class="text-sm text-slate-500 mt-0.5">Lihat data akun kamu, atau ubah kalau ada yang perlu diperbarui.</p>
    </div>
    <div id="btnLihatWrapper">
        <button type="button" onclick="toggleMode('edit')"
            class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:opacity-95">
            <span class="material-symbols-outlined text-[17px]">edit</span> Edit Profil
        </button>
    </div>
    <div id="btnEditWrapper" style="display:none">
        <button type="button" onclick="toggleMode('lihat')"
            class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl shadow-sm hover:bg-slate-50">
            <span class="material-symbols-outlined text-[17px]">close</span> Batal, Kembali Lihat Profil
        </button>
    </div>
</div>

{{-- Kartu ringkasan profil (selalu tampil di kedua mode) --}}
<div class="bg-gradient-to-r from-violet-600 to-indigo-600 rounded-2xl p-5 mb-6 text-white">
    <div class="flex flex-wrap items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center overflow-hidden shrink-0">
            @if ($user->foto)
                <img src="{{ asset('storage/'.$user->foto) }}" alt="{{ $user->nama }}" class="w-full h-full object-cover">
            @else
                <span class="text-2xl font-bold">{{ strtoupper(substr($user->nama, 0, 1)) }}</span>
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <span class="inline-flex items-center px-2 py-0.5 bg-white/20 text-[11px] font-semibold rounded-full mb-1">
                Hak Akses: {{ ucfirst($user->role->nama_role ?? 'Admin') }}
            </span>
            <h2 class="font-display font-bold text-lg">{{ $user->nama }}</h2>
            <p class="text-sm text-white/80">{{ $user->jabatan ?: 'Petugas Laboratorium RPL' }}</p>
        </div>
        <div class="text-right shrink-0">
            <p class="text-[11px] text-white/70">NIP / Username</p>
            <p class="text-sm font-semibold">{{ $user->nip ?: $user->username }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">

        {{-- ================= MODE LIHAT ================= --}}
        <div id="modeLihat" class="bg-white rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-sm text-slate-900">Data Identitas Petugas</h2>
                <span class="px-2 py-0.5 bg-violet-50 text-violet-600 text-[10px] font-bold rounded">{{ $user->username }}</span>
            </div>

            <dl class="divide-y divide-slate-100">
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <dt class="text-xs text-slate-400">Nama Lengkap &amp; Gelar</dt>
                    <dd class="text-sm font-medium text-slate-800 text-right">{{ $user->nama }}</dd>
                </div>
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <dt class="text-xs text-slate-400">Jabatan</dt>
                    <dd class="text-sm font-medium text-slate-800 text-right">{{ $user->jabatan ?: '-' }}</dd>
                </div>
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <dt class="text-xs text-slate-400">Email Dinas / Institusi</dt>
                    <dd class="text-sm font-medium text-slate-800 text-right">{{ $user->email }}</dd>
                </div>
                <div class="py-2.5 flex items-center justify-between gap-4">
                    <dt class="text-xs text-slate-400">No. WhatsApp</dt>
                    <dd class="text-sm font-medium text-slate-800 text-right">{{ $user->no_hp ?: '-' }}</dd>
                </div>
            </dl>
        </div>

        {{-- ================= MODE EDIT ================= --}}
        <div id="modeEdit" class="bg-white rounded-2xl p-5 shadow-sm" style="display:none">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-sm text-slate-900">Ubah Data Identitas</h2>
                <span class="px-2 py-0.5 bg-violet-50 text-violet-600 text-[10px] font-bold rounded">{{ $user->username }}</span>
            </div>

            @if ($errors->has('nama') || $errors->has('email') || $errors->has('jabatan') || $errors->has('no_hp') || $errors->has('foto'))
                <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">FOTO PROFIL</label>
                    <div class="flex items-center gap-3">
                        <div class="w-16 h-16 rounded-xl bg-slate-50 border border-dashed border-slate-300 flex items-center justify-center overflow-hidden shrink-0">
                            <img id="fotoPreview" data-zoom src="{{ $user->foto ? asset('storage/'.$user->foto) : '' }}" alt=""
                                class="{{ $user->foto ? '' : 'hidden' }} w-full h-full object-cover">
                            <span id="fotoPlaceholder" class="material-symbols-outlined text-slate-300 text-[24px] {{ $user->foto ? 'hidden' : '' }}">person</span>
                        </div>
                        <input type="file" name="foto" accept="image/*" onchange="previewFoto(this)"
                            class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-violet-50 file:text-violet-600 file:text-xs file:font-semibold hover:file:bg-violet-100">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">Foto yang besar dikecilkan otomatis. Maksimal 5 MB.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">NAMA LENGKAP &amp; GELAR *</label>
                    <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required
                        class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">JABATAN STRUKTURAL &amp; OPERASIONAL LAB</label>
                    <input type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}" placeholder="mis. Kepala Laboratorium RPL"
                        class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">EMAIL DINAS / INSTITUSI *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">NO. WHATSAPP (BANTUAN SISWA)</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" placeholder="08xxxxxxxxxx"
                            class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:opacity-95">
                        Simpan Perubahan
                    </button>
                    <button type="button" onclick="toggleMode('lihat')" class="px-5 py-2.5 text-sm font-semibold text-slate-500 hover:text-slate-700">
                        Batal
                    </button>
                </div>
            </form>

            {{-- Ganti password, taruh di dalam mode Edit juga --}}
            <div class="border-t border-slate-100 mt-6 pt-6">
                <h2 class="font-semibold text-sm text-slate-900 mb-4">Ganti Password</h2>

                @if ($errors->has('current_password') || $errors->has('password'))
                    <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.profil.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">PASSWORD SAAT INI *</label>
                        <input type="password" name="current_password" required
                            class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">PASSWORD BARU *</label>
                            <input type="password" name="password" required
                                class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">KONFIRMASI PASSWORD BARU *</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-3">
                        <p class="text-[11px] font-semibold text-slate-500 mb-1.5">STANDAR KEKUATAN PASSWORD</p>
                        <ul class="text-[11px] text-slate-500 space-y-0.5">
                            <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[13px] text-emerald-500">check</span> Minimal 8 karakter</li>
                            <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[13px] text-emerald-500">check</span> Kombinasi huruf besar &amp; kecil</li>
                            <li class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[13px] text-emerald-500">check</span> Minimal 1 angka</li>
                        </ul>
                    </div>

                    <button type="submit" class="px-5 py-2.5 bg-slate-900 text-white text-sm font-semibold rounded-xl hover:bg-slate-800">
                        Ganti Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Kolom kanan: Statistik (selalu tampil) --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl p-5 shadow-sm">
            <h2 class="font-semibold text-sm text-slate-900 mb-3">Statistik Tugas Aktif</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Log Stok Ditangani</span>
                    <span class="text-sm font-bold text-slate-900">{{ $stats['alat_ditangani'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Verifikasi Bulan Ini</span>
                    <span class="text-sm font-bold text-slate-900">{{ $stats['verifikasi_bulan_ini'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500">Bergabung Sejak</span>
                    <span class="text-sm font-bold text-slate-900">{{ $stats['bergabung_sejak']->translatedFormat('M Y') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-violet-50 border border-violet-100 rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-violet-600 text-[18px]">info</span>
                <h2 class="font-semibold text-sm text-slate-900">Catatan</h2>
            </div>
            <p class="text-xs text-slate-500 leading-relaxed">
                Perubahan email &amp; password akan langsung berlaku untuk login berikutnya.
                Pastikan menyimpan password baru di tempat aman.
            </p>
        </div>
    </div>
</div>

<script>
    function toggleMode(mode) {
        const lihatPanel = document.getElementById('modeLihat');
        const editPanel = document.getElementById('modeEdit');
        const btnLihatWrapper = document.getElementById('btnLihatWrapper');
        const btnEditWrapper = document.getElementById('btnEditWrapper');

        if (mode === 'edit') {
            lihatPanel.style.display = 'none';
            editPanel.style.display = 'block';
            btnLihatWrapper.style.display = 'none';
            btnEditWrapper.style.display = 'block';
        } else {
            lihatPanel.style.display = 'block';
            editPanel.style.display = 'none';
            btnLihatWrapper.style.display = 'block';
            btnEditWrapper.style.display = 'none';
        }
    }

    // Foto profil: dikecilkan otomatis kalau besar (maks. sisi 1200 px), lalu tampilkan preview
    async function previewFoto(input) {
        const preview = document.getElementById('fotoPreview');
        const placeholder = document.getElementById('fotoPlaceholder');
        const file = input.files && input.files[0];
        if (!file || !file.type.startsWith('image/')) { return; }

        let hasil = file;

        if (file.size > 1.5 * 1024 * 1024) {
            try {
                const bitmap = await createImageBitmap(file);
                const skala = Math.min(1, 1200 / Math.max(bitmap.width, bitmap.height));
                const canvas = document.createElement('canvas');
                canvas.width = Math.round(bitmap.width * skala);
                canvas.height = Math.round(bitmap.height * skala);
                canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);

                const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.85));
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
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(hasil);
    }

    @if ($bukaEdit)
        // Ada error validasi dari submit sebelumnya — langsung buka mode Edit.
        toggleMode('edit');
    @endif
</script>
@endsection
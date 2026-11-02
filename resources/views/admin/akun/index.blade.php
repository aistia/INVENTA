@extends('layouts.admin')

@section('title', 'Kelola Admin')

@section('content')

@php $bukaForm = $errors->any(); @endphp

<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
        <p class="text-[11px] font-semibold text-violet-600 tracking-wide mb-0.5">PORTAL OTORITAS LABORATORIUM RPL</p>
        <h1 class="font-display font-bold text-2xl text-slate-900">Kelola Akun Admin</h1>
        <p class="text-sm text-slate-500 mt-0.5">Tambah petugas lab baru supaya setiap verifikasi &amp; perbaikan tercatat atas nama petugas yang menangani.</p>
    </div>
    <button type="button" id="btnTambah" onclick="toggleForm(true)"
        class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:opacity-95 shrink-0">
        <span class="material-symbols-outlined text-[17px]">person_add</span> Tambah Admin Baru
    </button>
</div>

{{-- Form tambah admin --}}
<div id="formTambah" class="bg-white rounded-2xl p-5 shadow-sm mb-6" style="display:none">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-sm text-slate-900">Akun Admin Baru</h2>
        <button type="button" onclick="toggleForm(false)" class="text-slate-400 hover:text-slate-700" aria-label="Tutup form">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.akun.store') }}" class="space-y-4">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">NAMA LENGKAP &amp; GELAR *</label>
                <input type="text" name="nama" value="{{ old('nama') }}" required
                    class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">USERNAME *</label>
                <input type="text" name="username" value="{{ old('username') }}" required placeholder="mis. admin.budi"
                    class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">EMAIL *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">JABATAN</label>
                <input type="text" name="jabatan" value="{{ old('jabatan') }}" placeholder="mis. Teknisi Laboratorium RPL"
                    class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">NO. WHATSAPP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx"
                    class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div class="hidden sm:block"></div>
            <div>
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">PASSWORD *</label>
                <input type="password" name="password" required
                    class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">KONFIRMASI PASSWORD *</label>
                <input type="password" name="password_confirmation" required
                    class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
            </div>
        </div>

        <p class="text-[11px] text-slate-500">Password minimal 8 karakter, kombinasi huruf besar &amp; kecil, dan minimal 1 angka. Admin baru bisa langsung login dan menyetujui pengajuan.</p>

        <div class="flex items-center gap-2 pt-1">
            <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:opacity-95">
                Simpan Akun Admin
            </button>
            <button type="button" onclick="toggleForm(false)" class="px-5 py-2.5 text-sm font-semibold text-slate-500 hover:text-slate-700">Batal</button>
        </div>
    </form>
</div>

{{-- Daftar admin --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="font-semibold text-sm text-slate-900">Daftar Admin / Petugas Lab</h2>
        <span class="text-xs text-slate-400">{{ $admins->count() }} akun</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[11px] text-slate-400 border-b border-slate-100 bg-slate-50/60">
                    <th class="font-semibold py-3 px-4">ADMIN</th>
                    <th class="font-semibold py-3 px-4">USERNAME</th>
                    <th class="font-semibold py-3 px-4">EMAIL &amp; KONTAK</th>
                    <th class="font-semibold py-3 px-4">JABATAN</th>
                    <th class="font-semibold py-3 px-4 text-center">VERIFIKASI</th>
                    <th class="font-semibold py-3 px-4 text-center">PERBAIKAN</th>
                    <th class="font-semibold py-3 px-4">BERGABUNG</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($admins as $a)
                    <tr class="border-b border-slate-50 last:border-0 hover:bg-slate-50/50">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-full bg-violet-100 flex items-center justify-center text-violet-600 font-bold text-xs overflow-hidden shrink-0">
                                    @if ($a->foto)
                                        <img src="{{ asset('storage/'.$a->foto) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($a->nama, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-800">{{ $a->nama }}</div>
                                    @if ($a->id_user === auth()->id())
                                        <span class="px-1.5 py-0.5 bg-violet-50 text-violet-600 text-[10px] font-bold rounded">Anda</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 font-mono text-xs text-slate-600">{{ $a->username }}</td>
                        <td class="py-3 px-4 text-slate-600">
                            <div class="text-xs">{{ $a->email }}</div>
                            <div class="text-[11px] text-slate-400">{{ $a->no_hp ?: '-' }}</div>
                        </td>
                        <td class="py-3 px-4 text-slate-600 text-xs">{{ $a->jabatan ?: '-' }}</td>
                        <td class="py-3 px-4 text-center font-bold text-slate-900">{{ $totalVerifikasi[$a->id_user] ?? 0 }}</td>
                        <td class="py-3 px-4 text-center font-bold text-slate-900">{{ $totalPerbaikan[$a->id_user] ?? 0 }}</td>
                        <td class="py-3 px-4 text-xs text-slate-500 whitespace-nowrap">{{ $a->created_at->translatedFormat('d M Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<p class="text-[11px] text-slate-400 mt-3">
    Kolom <b>Verifikasi</b> = jumlah pengajuan peminjaman yang diproses admin tersebut, <b>Perbaikan</b> = jumlah laporan kerusakan yang ditangani.
    Akun admin tidak bisa dihapus dari sini supaya riwayat pekerjaannya tetap utuh.
</p>

<script>
    function toggleForm(buka) {
        document.getElementById('formTambah').style.display = buka ? 'block' : 'none';
        document.getElementById('btnTambah').style.display = buka ? 'none' : 'inline-flex';
        if (buka) {
            document.getElementById('formTambah').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    @if ($bukaForm)
        toggleForm(true);
    @endif
</script>
@endsection
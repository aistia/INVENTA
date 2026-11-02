{{-- Status verifikasi akun di halaman Profil Saya (siswa/guru).
     Data yang dibutuhkan (No. HP + foto profil) dilengkapi lewat mode "Edit Profil".
     Cara pakai di view profil:  @include('siswa.verifikasi._kartu', ['user' => $user]) --}}
@php
    $statusAkun = $user->status_akun;
    $punyaHp = filled($user->no_hp);
    $punyaFoto = filled($user->foto);
    $dataLengkap = $punyaHp && $punyaFoto;
@endphp

<div id="verifikasi" class="scroll-mt-20 mb-6">

    @if ($statusAkun === 'terverifikasi')
        <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-4 flex items-start gap-3">
            <span class="material-symbols-outlined text-emerald-600 text-[26px]">verified_user</span>
            <div>
                <h2 class="font-semibold text-sm text-slate-900">Akun kamu sudah terverifikasi</h2>
                <p class="text-xs text-slate-600 mt-0.5">
                    Diverifikasi oleh {{ $user->verifikator->nama ?? 'petugas lab' }}
                    @if ($user->diverifikasi_pada) pada {{ $user->diverifikasi_pada->translatedFormat('d M Y') }} @endif.
                    Kamu bisa mengajukan peminjaman alat.
                </p>
            </div>
        </div>
    @else
        @php
            if ($statusAkun === 'ditolak') {
                $kotak = 'bg-red-50 border-red-100';
                $ikon = 'gpp_bad';
                $warnaIkon = 'text-red-500';
                $judul = 'Verifikasi ditolak';
                $isi = ($user->catatan_verifikasi ?: 'Data akun belum sesuai.').' Perbaiki data di Edit Profil, lalu simpan untuk dikirim ulang.';
            } elseif ($dataLengkap) {
                $kotak = 'bg-amber-50 border-amber-100';
                $ikon = 'hourglass_top';
                $warnaIkon = 'text-amber-600';
                $judul = 'Menunggu verifikasi petugas lab';
                $isi = 'Data kamu sudah lengkap dan terkirim. Kamu belum bisa meminjam alat sampai petugas memverifikasi akunmu.';
            } else {
                $kotak = 'bg-amber-50 border-amber-100';
                $ikon = 'badge';
                $warnaIkon = 'text-amber-600';
                $judul = 'Lengkapi data verifikasi akun';
                $isi = 'Lengkapi data di Edit Profil supaya petugas lab bisa memverifikasi akunmu dan kamu bisa meminjam alat.';
            }
        @endphp

        <div class="{{ $kotak }} border rounded-2xl p-4">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined {{ $warnaIkon }} text-[26px]">{{ $ikon }}</span>
                <div class="flex-1 min-w-0">
                    <h2 class="font-semibold text-sm text-slate-900">{{ $judul }}</h2>
                    <p class="text-xs text-slate-600 mt-0.5">{{ $isi }}</p>

                    {{-- Checklist data yang dibutuhkan --}}
                    <div class="flex flex-wrap gap-1.5 mt-3">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $punyaHp ? 'bg-emerald-100 text-emerald-700' : 'bg-white text-slate-500' }}">
                            <span class="material-symbols-outlined text-[14px]">{{ $punyaHp ? 'check_circle' : 'radio_button_unchecked' }}</span> No. HP
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $punyaFoto ? 'bg-emerald-100 text-emerald-700' : 'bg-white text-slate-500' }}">
                            <span class="material-symbols-outlined text-[14px]">{{ $punyaFoto ? 'check_circle' : 'radio_button_unchecked' }}</span> Foto profil
                        </span>
                    </div>

                    <button type="button" onclick="toggleMode('edit'); window.scrollTo({ top: 0, behavior: 'smooth' });"
                        class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl shadow-sm hover:bg-slate-50">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                        {{ $dataLengkap && $statusAkun !== 'ditolak' ? 'Perbarui Data' : 'Lengkapi Data di Edit Profil' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
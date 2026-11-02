@extends('layouts.admin')

@section('title', 'Verifikasi Akun')

@section('content')

<div class="mb-6">
    <p class="text-[11px] font-semibold text-violet-600 tracking-wide mb-0.5">KEAMANAN INVENTARIS LAB</p>
    <h1 class="font-display font-bold text-2xl text-slate-900">Verifikasi Akun Peminjam</h1>
    <p class="text-sm text-slate-500 mt-0.5">Cocokkan data pendaftar (nama, NIS/NIP, kelas, No. HP) dengan foto profilnya. Klik <b>Lihat Detail</b> untuk melihat seluruh data. Hanya akun terverifikasi yang bisa meminjam alat.</p>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 tracking-wide">MENUNGGU VERIFIKASI</span>
            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-[17px]">hourglass_top</span>
            </div>
        </div>
        <div class="text-2xl font-bold text-slate-900">{{ $stats['menunggu'] }}</div>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 tracking-wide">TERVERIFIKASI</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-[17px]">verified_user</span>
            </div>
        </div>
        <div class="text-2xl font-bold text-slate-900">{{ $stats['terverifikasi'] }}</div>
    </div>
    <div class="bg-white rounded-2xl p-4 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-semibold text-slate-400 tracking-wide">DITOLAK / DICABUT</span>
            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center text-red-600">
                <span class="material-symbols-outlined text-[17px]">gpp_bad</span>
            </div>
        </div>
        <div class="text-2xl font-bold text-slate-900">{{ $stats['ditolak'] }}</div>
    </div>
</div>

{{-- Tabs & search --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
    <div class="flex flex-wrap items-center gap-2">
        @php
            $tabs = [
                'menunggu' => 'Menunggu ('.$stats['menunggu'].')',
                'terverifikasi' => 'Terverifikasi ('.$stats['terverifikasi'].')',
                'ditolak' => 'Ditolak ('.$stats['ditolak'].')',
                'semua' => 'Semua ('.$stats['semua'].')',
            ];
        @endphp
        @foreach ($tabs as $key => $label)
            <a href="{{ route('admin.verifikasi-akun.index', array_filter(['tab' => $key, 'cari' => request('cari')])) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all {{ $tab === $key ? 'bg-violet-600 text-white shadow-sm' : 'bg-white text-slate-500 hover:text-slate-800' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
    <form method="GET" action="{{ route('admin.verifikasi-akun.index') }}" class="flex-1 max-w-xs">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
            <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Cari nama, NIS/NIP, email..."
                class="w-full h-10 pl-10 pr-3 bg-white shadow-sm border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
        </div>
    </form>
</div>

@if ($akun->isEmpty())
    <div class="bg-white rounded-2xl p-10 shadow-sm text-center">
        <span class="material-symbols-outlined text-slate-300 text-[36px] mb-2 block">how_to_reg</span>
        <p class="text-sm text-slate-400">Tidak ada akun pada kategori ini.</p>
    </div>
@else
    <div class="space-y-3">
        @foreach ($akun as $u)
            @php
                $badge = [
                    'menunggu' => 'bg-amber-50 text-amber-600',
                    'terverifikasi' => 'bg-emerald-50 text-emerald-600',
                    'ditolak' => 'bg-red-50 text-red-600',
                ][$u->status_akun];
                $label = ['menunggu' => 'Menunggu', 'terverifikasi' => 'Terverifikasi', 'ditolak' => 'Ditolak / Dicabut'][$u->status_akun];

                // Kelengkapan data verifikasi: No. HP + foto profil
                $punyaHp = filled($u->no_hp);
                $punyaFoto = filled($u->foto);
                $lengkap = $punyaHp && $punyaFoto;
                $kurang = array_values(array_filter([
                    $punyaHp ? null : 'No. HP',
                    $punyaFoto ? null : 'foto profil',
                ]));

                // Data untuk panel detail (dibaca JavaScript lewat atribut data-detail)
                $detail = [
                    'nama' => $u->nama,
                    'username' => $u->username,
                    'tipe' => $u->tipe_user === 'guru' ? 'Guru' : 'Siswa',
                    'idLabel' => $u->tipe_user === 'guru' ? 'NIP' : 'NIS',
                    'idNomor' => $u->nis ?? $u->nip ?? null,
                    'kelas' => $u->kelas ?: null,
                    'jabatan' => $u->jabatan ?: null,
                    'email' => $u->email,
                    'noHp' => $u->no_hp ?: null,
                    'daftar' => $u->created_at->translatedFormat('d M Y, H:i'),
                    'status' => $u->status_akun,
                    'label' => $label,
                    'catatan' => $u->catatan_verifikasi,
                    'verifikator' => ($u->status_akun !== 'menunggu' && $u->verifikator)
                        ? $u->verifikator->nama.' pada '.$u->diverifikasi_pada?->translatedFormat('d M Y, H:i')
                        : null,
                    'foto' => $punyaFoto ? asset('storage/'.$u->foto) : null,
                    'lengkap' => $lengkap,
                    'kurang' => $kurang,
                    'approveUrl' => route('admin.verifikasi-akun.approve', $u),
                    'rejectUrl' => route('admin.verifikasi-akun.reject', $u),
                    'bisaApprove' => $u->status_akun !== 'terverifikasi',
                    'bisaTolak' => $u->status_akun !== 'ditolak',
                ];
            @endphp
            <div class="bg-white rounded-2xl shadow-sm p-4">
                <div class="flex flex-col md:flex-row gap-4">

                    {{-- Foto profil pendaftar --}}
                    <div class="shrink-0">
                        @if ($u->foto)
                            <a href="{{ asset('storage/'.$u->foto) }}" target="_blank" title="Klik untuk memperbesar">
                                <img src="{{ asset('storage/'.$u->foto) }}" alt="Foto profil {{ $u->nama }}" loading="lazy"
                                    class="w-full md:w-40 h-28 object-cover rounded-xl border border-slate-100">
                            </a>
                        @else
                            <div class="w-full md:w-40 h-28 rounded-xl bg-slate-100 flex flex-col items-center justify-center text-slate-400 text-center px-2">
                                <span class="material-symbols-outlined text-[26px]">no_photography</span>
                                <span class="text-[10.5px] mt-1">Belum unggah foto profil</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-start justify-between gap-2 mb-1.5">
                            <div>
                                <button type="button" onclick="bukaDetail(this)" data-detail="{{ json_encode($detail, JSON_UNESCAPED_UNICODE) }}"
                                    class="font-semibold text-slate-900 hover:text-violet-600 text-left">{{ $u->nama }}</button>
                                <p class="text-xs text-slate-400">
                                    {{ $u->tipe_user === 'guru' ? 'Guru' : 'Siswa' }} &bull; {{ '@'.$u->username }} &bull; daftar {{ $u->created_at->translatedFormat('d M Y') }}
                                </p>
                            </div>
                            <span class="px-2.5 py-1 {{ $badge }} text-[11px] font-semibold rounded-full whitespace-nowrap">{{ $label }}</span>
                        </div>

                        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-2 text-xs mb-3">
                            <div>
                                <dt class="text-slate-400">{{ $u->tipe_user === 'guru' ? 'NIP' : 'NIS' }}</dt>
                                <dd class="font-semibold text-slate-800">{{ $u->nis ?? $u->nip ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400">Kelas</dt>
                                <dd class="font-semibold text-slate-800">{{ $u->kelas ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400">No. HP</dt>
                                <dd class="font-semibold {{ $punyaHp ? 'text-slate-800' : 'text-red-500' }}">{{ $u->no_hp ?: 'Belum diisi' }}</dd>
                            </div>
                            <div class="col-span-2 sm:col-span-1 min-w-0">
                                <dt class="text-slate-400">Email</dt>
                                <dd class="font-semibold text-slate-800 truncate">{{ $u->email }}</dd>
                            </div>
                        </dl>

                        @if ($u->status_akun === 'ditolak' && $u->catatan_verifikasi)
                            <div class="mb-3 p-2.5 bg-red-50 rounded-lg text-xs text-red-600">
                                <span class="font-semibold">Alasan:</span> {{ $u->catatan_verifikasi }}
                            </div>
                        @endif

                        @if ($u->status_akun !== 'menunggu' && $u->verifikator)
                            <p class="text-[11px] text-slate-400 mb-2">
                                {{ $u->status_akun === 'terverifikasi' ? 'Diverifikasi' : 'Ditolak/dicabut' }} oleh
                                <span class="font-semibold text-violet-600">{{ $u->verifikator->nama }}</span>
                                pada {{ $u->diverifikasi_pada?->translatedFormat('d M Y, H:i') }}
                            </p>
                        @endif

                        {{-- Aksi --}}
                        <div class="flex flex-wrap items-start gap-2">
                            <button type="button" onclick="bukaDetail(this)" data-detail="{{ json_encode($detail, JSON_UNESCAPED_UNICODE) }}"
                                class="inline-flex items-center gap-1 px-3.5 py-1.5 bg-violet-50 text-violet-600 text-xs font-semibold rounded-lg hover:bg-violet-100">
                                <span class="material-symbols-outlined text-[15px]">visibility</span> Lihat Detail
                            </button>

                            @if ($u->status_akun !== 'terverifikasi')
                                @if ($lengkap)
                                    <form method="POST" action="{{ route('admin.verifikasi-akun.approve', $u) }}">
                                        @csrf
                                        <button class="px-3.5 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700">
                                            {{ $u->status_akun === 'ditolak' ? 'Verifikasi Ulang' : 'Verifikasi Akun' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="px-3 py-1.5 bg-slate-100 text-slate-400 text-xs font-semibold rounded-lg">
                                        Belum bisa diverifikasi (belum lengkap: {{ implode(', ', $kurang) }})
                                    </span>
                                @endif
                            @endif

                            @if ($u->status_akun !== 'ditolak')
                                <button type="button" onclick="document.getElementById('tolak-{{ $u->id_user }}').classList.toggle('hidden')"
                                    class="px-3.5 py-1.5 bg-red-50 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-100">
                                    {{ $u->status_akun === 'terverifikasi' ? 'Cabut Verifikasi' : 'Tolak' }}
                                </button>
                            @endif
                        </div>

                        <form id="tolak-{{ $u->id_user }}" method="POST" action="{{ route('admin.verifikasi-akun.reject', $u) }}" class="hidden mt-2 max-w-md">
                            @csrf
                            <textarea name="alasan" rows="2" required placeholder="Alasan (mis. foto buram, data tidak cocok, bukan siswa/guru RPL)..."
                                class="w-full text-xs p-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400"></textarea>
                            <button type="submit" class="mt-1 px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700">
                                Simpan Keputusan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($akun->hasPages())
        <div class="mt-4">
            {{ $akun->links() }}
        </div>
    @endif
@endif

{{-- ================= PANEL DETAIL AKUN (dipakai bersama oleh semua kartu) ================= --}}
<div id="detailModal" class="hidden fixed inset-0 z-50 items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="dtNama">
    <div class="absolute inset-0 bg-slate-900/50" onclick="tutupDetail()"></div>

    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-start justify-between gap-3 p-5 border-b border-slate-100">
            <div class="min-w-0">
                <h2 id="dtNama" class="font-display font-bold text-lg text-slate-900 truncate"></h2>
                <p id="dtMeta" class="text-xs text-slate-400 mt-0.5"></p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span id="dtStatus" class="px-2.5 py-1 text-[11px] font-semibold rounded-full whitespace-nowrap"></span>
                <button type="button" onclick="tutupDetail()" class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-500 hover:text-slate-800" aria-label="Tutup">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </div>

        <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Foto profil besar --}}
            <div>
                <p class="text-[11px] font-bold text-slate-400 tracking-wide mb-2">FOTO PROFIL</p>
                <a id="dtFotoLink" href="#" target="_blank" class="hidden block" title="Klik untuk membuka ukuran penuh">
                    <img id="dtFoto" src="" alt="Foto profil" class="w-full max-h-72 object-contain bg-slate-50 rounded-xl border border-slate-100">
                </a>
                <div id="dtFotoKosong" class="hidden w-full h-44 rounded-xl bg-slate-100 flex flex-col items-center justify-center text-slate-400 text-center px-2">
                    <span class="material-symbols-outlined text-[30px]">no_photography</span>
                    <span class="text-xs mt-1">Belum unggah foto profil</span>
                </div>
            </div>

            {{-- Data akun --}}
            <div>
                <p class="text-[11px] font-bold text-slate-400 tracking-wide mb-2">DATA PENDAFTAR</p>
                <dl id="dtData" class="divide-y divide-slate-100 text-xs"></dl>

                <p class="text-[11px] font-bold text-slate-400 tracking-wide mt-4 mb-2">KELENGKAPAN DATA</p>
                <div id="dtCek" class="flex flex-wrap gap-1.5"></div>
            </div>
        </div>

        <div id="dtCatatanBox" class="hidden mx-5 mb-3 p-3 bg-red-50 rounded-lg text-xs text-red-600">
            <span class="font-semibold">Alasan:</span> <span id="dtCatatan"></span>
        </div>
        <p id="dtVerifikator" class="hidden mx-5 mb-3 text-[11px] text-slate-400"></p>

        {{-- Aksi di dalam panel --}}
        <div class="p-5 border-t border-slate-100 space-y-3">
            <p id="dtBelumLengkap" class="hidden text-xs text-slate-500 bg-slate-50 rounded-lg p-2.5"></p>

            <div class="flex flex-wrap items-start gap-2">
                <form id="dtFormApprove" method="POST" action="#" class="hidden">
                    @csrf
                    <button id="dtBtnApprove" type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700"></button>
                </form>
                <button id="dtBtnTolak" type="button" onclick="document.getElementById('dtFormTolak').classList.toggle('hidden')"
                    class="hidden px-4 py-2 bg-red-50 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-100"></button>
                <button type="button" onclick="tutupDetail()" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-700">Tutup</button>
            </div>

            <form id="dtFormTolak" method="POST" action="#" class="hidden max-w-md">
                @csrf
                <textarea id="dtAlasan" name="alasan" rows="2" required placeholder="Alasan (mis. foto buram, data tidak cocok, bukan siswa/guru RPL)..."
                    class="w-full text-xs p-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400"></textarea>
                <button type="submit" class="mt-1 px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700">Simpan Keputusan</button>
            </form>
        </div>
    </div>
</div>

<script>
    const dtModal = document.getElementById('detailModal');

    function dtTeks(id, nilai) {
        document.getElementById(id).textContent = nilai;
    }

    function bukaDetail(tombol) {
        const d = JSON.parse(tombol.dataset.detail);

        // Header
        dtTeks('dtNama', d.nama);
        dtTeks('dtMeta', d.tipe + ' \u2022 @' + d.username + ' \u2022 daftar ' + d.daftar);

        const status = document.getElementById('dtStatus');
        status.textContent = d.label;
        status.className = 'px-2.5 py-1 text-[11px] font-semibold rounded-full whitespace-nowrap ' + ({
            menunggu: 'bg-amber-50 text-amber-600',
            terverifikasi: 'bg-emerald-50 text-emerald-600',
            ditolak: 'bg-red-50 text-red-600'
        }[d.status] || 'bg-slate-100 text-slate-500');

        // Foto profil
        const link = document.getElementById('dtFotoLink');
        const kosong = document.getElementById('dtFotoKosong');
        if (d.foto) {
            document.getElementById('dtFoto').src = d.foto;
            link.href = d.foto;
            link.classList.remove('hidden');
            kosong.classList.add('hidden');
        } else {
            document.getElementById('dtFoto').src = '';
            link.classList.add('hidden');
            kosong.classList.remove('hidden');
            kosong.classList.add('flex');
        }

        // Data pendaftar
        const dl = document.getElementById('dtData');
        dl.innerHTML = '';
        [
            [d.idLabel, d.idNomor],
            ['Kelas', d.kelas],
            ['Jabatan', d.jabatan],
            ['No. HP', d.noHp, 'Belum diisi'],
            ['Email', d.email],
            ['Username', '@' + d.username],
            ['Jenis akun', d.tipe]
        ].forEach(function (baris) {
            if (baris[0] === 'Jabatan' && !baris[1]) { return; }
            if (baris[0] === 'Kelas' && !baris[1] && d.tipe === 'Guru') { return; }

            const kosongTeks = baris[2] || '-';
            const ada = !!baris[1];

            const row = document.createElement('div');
            row.className = 'py-2 flex justify-between gap-3';

            const dt = document.createElement('dt');
            dt.className = 'text-slate-400';
            dt.textContent = baris[0];

            const dd = document.createElement('dd');
            dd.className = 'font-semibold text-right break-all ' + (ada ? 'text-slate-800' : (baris[2] ? 'text-red-500' : 'text-slate-400'));
            dd.textContent = ada ? baris[1] : kosongTeks;

            row.appendChild(dt);
            row.appendChild(dd);
            dl.appendChild(row);
        });

        // Checklist kelengkapan
        const cek = document.getElementById('dtCek');
        cek.innerHTML = '';
        [['No. HP', !!d.noHp], ['Foto profil', !!d.foto]].forEach(function (item) {
            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold ' +
                (item[1] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-50 text-red-600');
            const ikon = document.createElement('span');
            ikon.className = 'material-symbols-outlined text-[14px]';
            ikon.textContent = item[1] ? 'check_circle' : 'cancel';
            chip.appendChild(ikon);
            chip.appendChild(document.createTextNode(' ' + item[0]));
            cek.appendChild(chip);
        });

        // Catatan penolakan & petugas
        const catatanBox = document.getElementById('dtCatatanBox');
        if (d.status === 'ditolak' && d.catatan) {
            dtTeks('dtCatatan', d.catatan);
            catatanBox.classList.remove('hidden');
        } else {
            catatanBox.classList.add('hidden');
        }

        const verif = document.getElementById('dtVerifikator');
        if (d.verifikator) {
            verif.textContent = (d.status === 'terverifikasi' ? 'Diverifikasi oleh ' : 'Ditolak/dicabut oleh ') + d.verifikator;
            verif.classList.remove('hidden');
        } else {
            verif.classList.add('hidden');
        }

        // Aksi: verifikasi / tolak / cabut
        const formApprove = document.getElementById('dtFormApprove');
        const btnApprove = document.getElementById('dtBtnApprove');
        const info = document.getElementById('dtBelumLengkap');
        if (d.bisaApprove && d.lengkap) {
            formApprove.action = d.approveUrl;
            btnApprove.textContent = d.status === 'ditolak' ? 'Verifikasi Ulang' : 'Verifikasi Akun';
            formApprove.classList.remove('hidden');
            info.classList.add('hidden');
        } else {
            formApprove.classList.add('hidden');
            if (d.bisaApprove && !d.lengkap) {
                info.textContent = 'Belum bisa diverifikasi, data belum lengkap: ' + d.kurang.join(', ') + '.';
                info.classList.remove('hidden');
            } else {
                info.classList.add('hidden');
            }
        }

        const btnTolak = document.getElementById('dtBtnTolak');
        const formTolak = document.getElementById('dtFormTolak');
        formTolak.classList.add('hidden');
        document.getElementById('dtAlasan').value = '';
        if (d.bisaTolak) {
            formTolak.action = d.rejectUrl;
            btnTolak.textContent = d.status === 'terverifikasi' ? 'Cabut Verifikasi' : 'Tolak';
            btnTolak.classList.remove('hidden');
        } else {
            btnTolak.classList.add('hidden');
        }

        dtModal.classList.remove('hidden');
        dtModal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function tutupDetail() {
        dtModal.classList.add('hidden');
        dtModal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        document.getElementById('dtFoto').src = '';
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !dtModal.classList.contains('hidden')) { tutupDetail(); }
    });
</script>

@endsection
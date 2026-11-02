
@extends('layouts.siswa')

@section('title', 'Ajukan Peminjaman')

@section('content')

<div class="mb-6">
    <p class="text-[11px] font-semibold text-violet-600 tracking-wide mb-0.5">
        INVENTA // RESERVASI ALAT
    </p>
    <h1 class="font-display font-bold text-2xl text-slate-900">
        Ajukan Peminjaman Alat
    </h1>
    <p class="text-sm text-slate-500 mt-0.5">
        Isi form di bawah, pengajuan akan diverifikasi petugas lab sebelum disetujui.
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm">

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if ($alatList->isEmpty())
            <div class="text-center py-10">
                <span class="material-symbols-outlined text-slate-300 text-[36px] mb-2 block">
                    inventory_2
                </span>
                <p class="text-sm text-slate-400">
                    Belum ada alat yang tersedia untuk dipinjam saat ini.
                </p>
            </div>
        @else

            <form method="POST"
                  action="{{ route('siswa.peminjaman.store') }}"
                  class="space-y-4"
                  id="formPeminjaman">

                @csrf

                {{-- PILIH ALAT --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">
                        PILIH ALAT *
                    </label>

                    <select
                        name="id_alat"
                        id="id_alat"
                        required
                        onchange="updateInfoAlat()"
                        data-sheet-select
                        data-sheet-title="Pilih Alat"
                        class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">

                        <option value="" disabled
                            {{ old('id_alat', $alatTerpilih?->id_alat) ? '' : 'selected' }}>
                            -- Pilih alat yang mau dipinjam --
                        </option>

                        @foreach ($alatList->groupBy(fn ($a) => $a->kategori->nama_kategori ?? 'Lainnya')->sortKeys() as $namaKategori => $items)
                            <optgroup label="{{ $namaKategori }}">
                                @foreach ($items as $a)
                                    <option
                                        value="{{ $a->id_alat }}"
                                        data-stok="{{ $a->jumlah_stok }}"
                                        data-kode="{{ $a->kode_alat }}"
                                        @selected(old('id_alat', $alatTerpilih?->id_alat) == $a->id_alat)>
                                        {{ $a->nama_alat }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach

                    </select>

                    <p id="infoStok" class="text-xs text-violet-600 mt-1.5"></p>
                </div>

                {{-- JUMLAH --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">
                        JUMLAH *
                    </label>

                    <input
                        type="number"
                        name="jumlah"
                        id="jumlah"
                        min="1"
                        step="1"
                        value="{{ old('jumlah', 1) }}"
                        required
                        class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">

                    <p class="text-[11px] text-slate-400 mt-1">
                        Masukkan jumlah unit yang ingin dipinjam.
                    </p>
                </div>

                {{-- TANGGAL --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">
                            TANGGAL PINJAM *
                        </label>

                        <input
                            type="date"
                            name="tanggal_pinjam"
                            id="tanggal_pinjam"
                            value="{{ old('tanggal_pinjam', now()->format('Y-m-d')) }}"
                            min="{{ now()->format('Y-m-d') }}"
                            required
                            class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">
                            RENCANA KEMBALI *
                        </label>

                        <input
                            type="date"
                            name="tanggal_kembali_rencana"
                            id="tanggal_kembali"
                            value="{{ old('tanggal_kembali_rencana', now()->format('Y-m-d')) }}"
                            min="{{ now()->format('Y-m-d') }}"
                            required
                            class="w-full h-11 px-3 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">

                        <p class="text-[11px] text-slate-400 mt-1">
                            Pinjam dan kembali di hari yang sama? Isi dengan tanggal yang sama.
                        </p>
                    </div>

                </div>

                {{-- ATURAN SATU HARI --}}
                <div id="aturanSatuHari"
                     class="hidden p-3 rounded-xl bg-amber-50 border border-amber-200">

                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-amber-600 text-[18px] mt-0.5">
                            schedule
                        </span>

                        <div>
                            <p class="text-xs font-bold text-amber-800">
                                Peminjaman 1 hari
                            </p>

                            <p class="text-xs text-amber-700 leading-relaxed mt-0.5">
                                Alat wajib dikembalikan
                                <strong>
                                    sebelum pukul {{ \App\Models\Peminjaman::jamBatasTampil() }} WIB
                                </strong>
                                pada hari yang sama. Pengembalian setelah jam tersebut dihitung terlambat.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- KEPERLUAN --}}
                <div>
                    <label class="block text-xs font-bold text-slate-500 tracking-wide mb-1.5">
                        KEPERLUAN / KETERANGAN *
                    </label>

                    <textarea
                        name="deskripsi"
                        rows="3"
                        required
                        placeholder="Contoh: Praktikum administrasi jaringan, modul minggu 5..."
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 text-sm rounded-xl focus:outline-none focus:ring-2 focus:ring-violet-500">{{ old('deskripsi') }}</textarea>
                </div>

                {{-- TOMBOL --}}
                <div class="flex items-center gap-2 pt-2">

                    <button
                        type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:opacity-95">
                        Kirim Pengajuan
                    </button>

                    <a
                        href="{{ route('siswa.barang.index') }}"
                        class="px-5 py-2.5 text-sm font-semibold text-slate-500 hover:text-slate-700">
                        Batal
                    </a>

                </div>

            </form>

        @endif
    </div>

    {{-- SIDEBAR INFORMASI --}}
    <div class="space-y-4">

        <div class="bg-violet-50 border border-violet-100 rounded-2xl p-5">

            <div class="flex items-center gap-2 mb-2">
                <span class="material-symbols-outlined text-violet-600 text-[18px]">
                    info
                </span>

                <h2 class="font-semibold text-sm text-slate-900">
                    Alur Pengajuan
                </h2>
            </div>

            <ol class="text-xs text-slate-500 space-y-2 leading-relaxed list-decimal list-inside">
                <li>Isi form di samping dan kirim pengajuan.</li>
                <li>Petugas lab akan meninjau dan memverifikasi (menyetujui/menolak).</li>
                <li>Kalau disetujui, ambil alat ke petugas lab sesuai tanggal pinjam.</li>
                <li>Kembalikan tepat waktu untuk menjaga skor peminjaman kamu.</li>
            </ol>

        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm">

            <h2 class="font-semibold text-sm text-slate-900 mb-2">
                Butuh Cek Alat Lain?
            </h2>

            <p class="text-xs text-slate-500 mb-3">
                Lihat katalog lengkap dan kondisi alat sebelum mengajukan.
            </p>

            <a
                href="{{ route('siswa.barang.index') }}"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-violet-600 hover:underline">
                Buka Katalog Alat
                <span class="material-symbols-outlined text-[14px]">
                    arrow_forward
                </span>
            </a>

        </div>

    </div>

</div>

<script>
    // INFORMASI STOK ALAT
    function updateInfoAlat() {
        const select = document.getElementById('id_alat');
        const jumlahInput = document.getElementById('jumlah');
        const info = document.getElementById('infoStok');

        if (!select || !jumlahInput || !info) return;

        const opt = select.options[select.selectedIndex];

        if (!opt || !opt.dataset.stok) {
            info.textContent = '';
            jumlahInput.removeAttribute('max');
            return;
        }

        const stok = parseInt(opt.dataset.stok, 10);

        info.textContent = `Stok tersedia: ${stok} unit (kode: ${opt.dataset.kode})`;

        jumlahInput.max = stok;

        // Jangan mengubah jumlah menjadi 1 secara otomatis.
        if (parseInt(jumlahInput.value, 10) > stok) {
            jumlahInput.value = stok;
        }
    }

    // TANGGAL PINJAM DAN KEMBALI
    function sinkronTanggal() {
        const pinjam = document.getElementById('tanggal_pinjam');
        const kembali = document.getElementById('tanggal_kembali');

        if (!pinjam || !kembali || !pinjam.value) return;

        kembali.min = pinjam.value;

        if (kembali.value && kembali.value < pinjam.value) {
            kembali.value = pinjam.value;
        }

        tampilkanAturanSatuHari();
    }

    // ATURAN PEMINJAMAN SATU HARI
    function tampilkanAturanSatuHari() {
        const pinjam = document.getElementById('tanggal_pinjam');
        const kembali = document.getElementById('tanggal_kembali');
        const kotak = document.getElementById('aturanSatuHari');

        if (!pinjam || !kembali || !kotak) return;

        const satuHari =
            pinjam.value !== '' &&
            pinjam.value === kembali.value;

        kotak.classList.toggle('hidden', !satuHari);
    }

    // VALIDASI FORM
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('id_alat');
        const jumlahInput = document.getElementById('jumlah');
        const form = document.getElementById('formPeminjaman');
        const pinjam = document.getElementById('tanggal_pinjam');
        const kembali = document.getElementById('tanggal_kembali');

        updateInfoAlat();
        sinkronTanggal();

        if (pinjam) {
            pinjam.addEventListener('change', sinkronTanggal);
        }

        if (kembali) {
            kembali.addEventListener('change', tampilkanAturanSatuHari);
        }

        if (select) {
            select.addEventListener('change', updateInfoAlat);
        }

        if (jumlahInput) {
            jumlahInput.addEventListener('input', function () {
                this.setCustomValidity('');

                if (this.value !== '' && Number(this.value) < 1) {
                    this.setCustomValidity('Jumlah minimal 1 unit.');
                }

                if (
                    this.value !== '' &&
                    this.max &&
                    Number(this.value) > Number(this.max)
                ) {
                    this.setCustomValidity(
                        'Jumlah melebihi stok alat yang tersedia.'
                    );
                }
            });
        }

        if (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    form.reportValidity();
                }
            });
        }
    });
</script>

@endsection

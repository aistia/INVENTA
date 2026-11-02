<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\StokLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class AlatSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereHas('role', fn ($q) => $q->where('nama_role', 'admin'))->first();

        if (! $admin) {
            $this->command->error('Akun admin belum ada. Jalankan dulu: php artisan db:seed');

            return;
        }

        // Format tiap item: [kode_alat, nama_alat, deskripsi, stok awal]
        $data = [
            'Router & Managed Switch' => [
                ['RPL-NET-01', 'MikroTik RouterBOARD RB951Ui-2HnD', 'Router untuk praktikum administrasi jaringan, routing, dan firewall', 8],
                ['RPL-NET-02', 'TP-Link TL-SG1024 Switch 24 Port', 'Switch 24 port gigabit untuk praktikum topologi & VLAN', 4],
                ['RPL-NET-03', 'MikroTik hAP lite RB941', 'Router mini untuk praktikum konfigurasi dasar MikroTik', 6],
            ],
            'Kit IoT & Mikrokontroler' => [
                ['RPL-IOT-01', 'ESP32 DevKit NodeMCU V4', 'Mikrokontroler WiFi & Bluetooth untuk proyek IoT', 20],
                ['RPL-IOT-02', 'Arduino Uno R3 Starter Kit', 'Paket dasar mikrokontroler Arduino beserta kabel USB', 15],
                ['RPL-IOT-03', 'NodeMCU ESP8266 V3', 'Board WiFi murah untuk praktikum IoT dasar', 20],
                ['RPL-IOT-06', 'ESP32-CAM Module', 'Modul ESP32 dengan kamera untuk proyek monitoring', 8],
            ],
            'Komponen Elektronik & Sensor' => [
                ['RPL-SNS-01', 'Sensor Ultrasonik HC-SR04', 'Sensor jarak untuk proyek robotika & IoT', 25],
                ['RPL-SNS-02', 'Sensor Suhu & Kelembapan DHT11', 'Sensor suhu dan kelembapan udara', 20],
                ['RPL-SNS-03', 'Servo Motor SG90', 'Motor servo mini untuk gerak presisi', 15],
                ['RPL-SNS-04', 'Relay Module 4 Channel', 'Modul relay untuk kontrol perangkat listrik via mikrokontroler', 10],
                ['RPL-SNS-06', 'Breadboard 830 Titik + Kabel Jumper', 'Papan rangkaian prototipe beserta set kabel jumper', 20],
            ],
            'Kabel & Tools Jaringan' => [
                ['RPL-TL-01', 'Crimping Tool RJ-45', 'Tang crimping untuk memasang konektor RJ-45', 6],
                ['RPL-TL-02', 'Kabel UTP Cat6 (Roll 305m)', 'Kabel UTP Cat6 untuk praktikum instalasi jaringan', 2],
                ['RPL-TL-03', 'LAN Tester RJ-45/RJ-11', 'Alat tes kontinuitas kabel jaringan', 8],
                ['RPL-TL-04', 'Konektor RJ-45 Cat6 (Box 100 pcs)', 'Konektor RJ-45 untuk praktik crimping kabel', 5],
            ],
            
            'Peripheral & Aksesoris Komputer' => [
                ['RPL-PRF-01', 'Mouse Logitech B100', 'Mouse kabel standar untuk PC lab', 15],
                ['RPL-PRF-03', 'Headphone/Headset Sades', 'Headset untuk praktik multimedia & meeting', 10],
                ['RPL-PRF-04', 'Proyektor Epson EB-X05', 'Proyektor untuk presentasi & demo kelas', 3],
                ['RPL-PRF-05', 'Kabel HDMI 1.5 Meter', 'Kabel HDMI untuk proyektor & monitor', 10],
            ],
            'Tools Perakitan & Perbaikan' => [
                ['RPL-ALT-01', 'Multimeter Digital', 'Alat ukur tegangan, arus, dan hambatan', 8],
                ['RPL-ALT-04', 'USB to TTL Converter CP2102', 'Konverter untuk upload program & komunikasi serial', 10],
            ],
        ];

        $baru = 0;
        $dilewati = 0;

        foreach ($data as $namaKategori => $items) {
            $kategori = KategoriAlat::firstOrCreate(['nama_kategori' => $namaKategori]);

            foreach ($items as [$kode, $nama, $deskripsi, $stok]) {
                if (Alat::where('kode_alat', $kode)->exists()) {
                    $dilewati++;

                    continue;
                }

                $alat = Alat::create([
                    'id_kategori' => $kategori->id_kategori,
                    'kode_alat' => $kode,
                    'nama_alat' => $nama,
                    'deskripsi' => $deskripsi,
                    'jumlah_stok' => 0,
                    'kondisi' => 'baik',
                    'arsip' => false,
                ]);

                // Stok awal dicatat lewat stok_log (trigger MySQL yang menambah jumlah_stok)
                StokLog::create([
                    'id_alat' => $alat->id_alat,
                    'admin_id' => $admin->id_user,
                    'jenis' => 'masuk',
                    'jumlah' => $stok,
                    'keterangan' => 'Stok awal (data alat lab RPL)',
                ]);

                // Cadangan kalau trigger tidak aktif di database kamu
                $alat->refresh();
                if ($alat->jumlah_stok < $stok) {
                    $alat->update(['jumlah_stok' => $stok]);
                }

                $baru++;
            }
        }

        // Kalau sebelumnya sempat menjalankan versi 41 alat, 16 alat yang tidak
        // masuk daftar inti ini diarsipkan (disembunyikan, tidak dihapus).
        $tidakDipakai = [
            'RPL-NET-04', 'RPL-NET-05', 'RPL-NET-06',
            'RPL-IOT-04', 'RPL-IOT-05',
            'RPL-SNS-05', 'RPL-SNS-07',
            'RPL-TL-05', 'RPL-TL-06',
            'RPL-SRV-02',
            'RPL-PRF-02', 'RPL-PRF-06', 'RPL-PRF-07',
            'RPL-ALT-02', 'RPL-ALT-03', 'RPL-ALT-05',
        ];
        $diarsipkan = Alat::whereIn('kode_alat', $tidakDipakai)->where('arsip', false)->update(['arsip' => true]);

        $this->command->info("Selesai: {$baru} alat baru ditambahkan, {$dilewati} dilewati (kode sudah ada), {$diarsipkan} alat lama diarsipkan.");
    }
}
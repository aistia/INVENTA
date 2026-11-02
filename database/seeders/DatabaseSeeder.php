<?php

namespace Database\Seeders;

use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roleAdmin = Role::firstOrCreate(['nama_role' => 'admin']);
        $roleUser = Role::firstOrCreate(['nama_role' => 'user']);

        // Akun demo — admin / petugas lab
        User::firstOrCreate(
            ['username' => 'admin.lab'],
            [
                'id_role' => $roleAdmin->id_role,
                'nama' => 'Pak Hendra Petugas Lab',
                'email' => 'admin@inventa.smk.sch.id',
                'password' => Hash::make('password'),
                'tipe_user' => null,
                'jabatan' => 'Kepala Laboratorium RPL',
                'no_hp' => '081234567890',
            ]
        );

        // Akun demo — siswa
        User::firstOrCreate(
            ['username' => 'siswa.demo'],
            [
                'id_role' => $roleUser->id_role,
                'nama' => 'Andi Saputra',
                'email' => 'andi.saputra@siswa.smk.sch.id',
                'password' => Hash::make('password'),
                'tipe_user' => 'siswa',
                'nis' => '2122049',
                'kelas' => 'XII RPL 1',
                'no_hp' => '081298765432',
            ]
        );

        // Akun demo — guru
        User::firstOrCreate(
            ['username' => 'guru.demo'],
            [
                'id_role' => $roleUser->id_role,
                'nama' => 'Bu Siti Rahayu',
                'email' => 'siti.rahayu@guru.smk.sch.id',
                'password' => Hash::make('password'),
                'tipe_user' => 'guru',
                'nip' => '198501012010012001',
                'jabatan' => 'Guru Produktif RPL',
                'no_hp' => '081211122233',
            ]
        );

        // Kategori & data alat  (jaringan, IoT, server, tools)
        $kategoris = [
            'Router & Managed Switch',
            'Kit IoT & Mikrokontroler',
            'Kabel & Tools Jaringan',
            
        ];

        $kategoriIds = [];
        foreach ($kategoris as $nama) {
            $kategoriIds[$nama] = KategoriAlat::firstOrCreate(['nama_kategori' => $nama])->id_kategori;
        }

        $alats = [
            ['Router & Managed Switch', 'RPL-NET-01', 'MikroTik RouterBOARD RB951Ui', 'Praktikum administrasi jaringan', 8, 'baik'],
            ['Router & Managed Switch', 'RPL-NET-02', 'TP-Link TL-SG1024 Switch 24 Port', 'Praktikum topologi & VLAN jaringan', 4, 'baik'],
            ['Kit IoT & Mikrokontroler', 'RPL-IOT-01', 'ESP32 DevKit NodeMCU V4', 'Praktikum sensor & IoT', 20, 'baik'],
            ['Kit IoT & Mikrokontroler', 'RPL-IOT-02', 'Arduino Uno R3 Starter Kit', 'Praktikum dasar mikrokontroler', 15, 'baik'],
            ['Kabel & Tools Jaringan', 'RPL-TL-01', 'Crimping Tool + Tester RJ-45', 'Praktikum instalasi kabel jaringan', 6, 'baik'],
            
        ];

        foreach ($alats as [$kategori, $kode, $nama, $deskripsi, $stok, $kondisi]) {
            Alat::firstOrCreate(
                ['kode_alat' => $kode],
                [
                    'id_kategori' => $kategoriIds[$kategori],
                    'nama_alat' => $nama,
                    'deskripsi' => $deskripsi,
                    'jumlah_stok' => $stok,
                    'kondisi' => $kondisi,
                ]
            );
        }
    }
}
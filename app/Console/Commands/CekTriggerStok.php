<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CekTriggerStok extends Command
{
    protected $signature = 'app:cek-trigger-stok';
    protected $description = 'Melihat definisi trigger stok INVENTA';

    public function handle(): int
    {
        foreach (DB::select('SHOW TRIGGERS') as $trigger) {
            $nama = $trigger->Trigger;

            if (
                str_contains($nama, 'peminjaman') ||
                str_contains($nama, 'stok_log') ||
                str_contains($nama, 'laporan_kerusakan')
            ) {
                $hasil = DB::select(
                    'SHOW CREATE TRIGGER `'.$nama.'`'
                );

                $this->newLine();
                $this->info('=== '.$nama.' ===');
                $this->line($hasil[0]->{'SQL Original Statement'});
            }
        }

        return self::SUCCESS;
    }
}
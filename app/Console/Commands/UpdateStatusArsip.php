<?php

namespace App\Console\Commands;

use App\Models\surat_keluar;
use App\Models\surat_masuk;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateStatusArsip extends Command
{
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'arsip:update-status';

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Update status arsip surat keluar dan surat masuk berdasarkan tanggal retensi';

        /**
         * Execute the console command.
         */
        public function handle()
        {
                $today = Carbon::today();

                // Update status arsip untuk surat keluar
                $suratKeluarUpdated = surat_keluar::whereNull('status_arsip')
                        ->whereNotNull('retensi')
                        ->whereDate('retensi', '<', $today)
                        ->update(['status_arsip' => 'inaktif']);

                // Update status arsip untuk surat masuk
                $suratMasukUpdated = surat_masuk::whereNull('status_arsip')
                        ->whereNotNull('retensi')
                        ->whereDate('retensi', '<', $today)
                        ->update(['status_arsip' => 'inaktif']);

                $totalUpdated = $suratKeluarUpdated + $suratMasukUpdated;

                $this->info("Status arsip berhasil diupdate:");
                $this->info("- Surat Keluar: {$suratKeluarUpdated} record");
                $this->info("- Surat Masuk: {$suratMasukUpdated} record");
                $this->info("Total: {$totalUpdated} record");

                // Log activity
                Log::info("Update status arsip completed", [
                        'surat_keluar_updated' => $suratKeluarUpdated,
                        'surat_masuk_updated' => $suratMasukUpdated,
                        'total_updated' => $totalUpdated,
                        'date' => $today->toDateString()
                ]);

                return Command::SUCCESS;
        }
}

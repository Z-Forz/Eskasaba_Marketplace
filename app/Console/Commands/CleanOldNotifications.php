<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class CleanOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:clean {--days=7 : Menentukan berapa hari umur notifikasi yang akan dihapus}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus notifikasi yang sudah berumur lebih dari 1 minggu (7 hari)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);

        $deletedCount = Notification::where('created_at', '<', $cutoff)->delete();

        $this->info("✅ Berhasil menghapus {$deletedCount} notifikasi yang lebih tua dari {$days} hari (sebelum {$cutoff->toDateTimeString()}).");

        return Command::SUCCESS;
    }
}

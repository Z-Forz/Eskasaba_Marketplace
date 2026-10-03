<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\Seller;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyGraduatingSellersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sellers:notify-graduating {--force : Jalankan tanpa mengecek bulan April}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi peringatan WhatsApp ke Seller Kelas 12 pada bulan April untuk menyelesaikan pesanan & menonaktifkan produk sebelum lulus.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $currentMonth = (int) date('m');
        $force = $this->option('force');

        if ($currentMonth !== 4 && ! $force) {
            $this->info('Saat ini bukan bulan April. Gunakan opsi --force untuk menjalankan secara manual.');

            return self::SUCCESS;
        }

        $this->info('Mencari Seller aktif dari Kelas 12 (XII)...');

        $graduatingSellers = Seller::with('user')
            ->where('status', 'approved')
            ->whereHas('user', function ($query) {
                $query->where(function ($q) {
                    $q->where('class_room', 'like', '%XII%')
                        ->orWhere('class_room', 'like', '%12%')
                        ->orWhere('class_room', 'like', '%xii%');
                });
            })
            ->get();

        if ($graduatingSellers->isEmpty()) {
            $this->info('Tidak ada Seller aktif dari Kelas 12 ditemukan.');

            return self::SUCCESS;
        }

        $count = 0;
        foreach ($graduatingSellers as $seller) {
            if (! $seller->user) {
                continue;
            }

            // 1. Kirim Notifikasi WA ke Seller Kelas 12
            WhatsAppService::sendGraduatingSellerWarningNotification($seller);

            // 2. Buat Notifikasi Aplikasi (In-App)
            Notification::create([
                'user_id' => $seller->user_id,
                'title' => '🎓 Persiapan Kelulusan - Selesaikan Pesanan & Nonaktifkan Produk',
                'message' => 'Mendekati masa akhir sekolah di bulan April, mohon selesaikan pesanan berjalan dan nonaktifkan produk jualan Anda sebelum akun dialihkan menjadi Alumni.',
                'type' => 'seller_warning',
                'link' => route('seller.products.index'),
                'is_read' => false,
            ]);

            $count++;
            $this->info("Notifikasi peringatan kelulusan terkirim ke: {$seller->user->username} ({$seller->user->class_room})");
        }

        Log::info("NotifyGraduatingSellersCommand: Sent graduation warning notifications to {$count} Grade 12 sellers.");
        $this->info("Berhasil mengirim notifikasi ke {$count} seller Kelas 12.");

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Models\User;
use App\Services\SchoolApiService;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncSiPintuUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sipintu:sync {--manual : Tandai jika dijalankan secara manual dari CLI}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi otomatis data pengguna sekolah (Siswa & Guru) dari SiPintu Gateway dan kirim notifikasi WhatsApp ke Admin.';

    /**
     * Execute the console command.
     */
    public function handle(SchoolApiService $schoolApi): int
    {
        $this->info('Memulai sinkronisasi data pengguna SiPintu Gateway...');
        Log::info('SyncSiPintuUsersCommand: Starting automated daily user sync at 00:00 WIB...');

        try {
            $isManual = $this->option('manual');
            $syncedCount = $schoolApi->syncAllUsers();

            $this->info("Sinkronisasi selesai. Total {$syncedCount} pengguna berhasil disinkronkan.");
            Log::info("SyncSiPintuUsersCommand: Successfully synced {$syncedCount} users from SiPintu Gateway.");

            // 1. Kirim pesan notifikasi WhatsApp otomatis ke Admin
            WhatsAppService::sendAutoSyncNotification($syncedCount, $isManual);

            // 2. Buat notifikasi aplikasi (in-app notification) untuk semua admin
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title'   => 'Sinkronisasi Otomatis Berhasil 🔄',
                    'message' => "Sistem telah secara otomatis men-sinkronkan {$syncedCount} data pengguna sekolah dari SiPintu Gateway pada pukul 00:00 WIB.",
                    'type'    => 'system_sync',
                    'is_read' => false,
                    'link'    => route('admin.users.index'),
                ]);
            }

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Gagal melakukan sinkronisasi: ' . $e->getMessage());
            Log::error('SyncSiPintuUsersCommand error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Command::FAILURE;
        }
    }
}

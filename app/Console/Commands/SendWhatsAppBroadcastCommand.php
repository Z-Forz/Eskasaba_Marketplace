<?php

namespace App\Console\Commands;

use App\Models\WhatsAppBroadcast;
use App\Models\WhatsAppBroadcastLog;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWhatsAppBroadcastCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:send-broadcast {broadcastId : ID Broadcast Campaign}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pesan broadcast WhatsApp secara bertahap (anti-ban rate limiting)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $broadcastId = (int) $this->argument('broadcastId');
        $broadcast = WhatsAppBroadcast::find($broadcastId);

        if (!$broadcast) {
            $this->error("Campaign Broadcast dengan ID #{$broadcastId} tidak ditemukan.");
            return self::FAILURE;
        }

        if ($broadcast->status === 'cancelled') {
            $this->warn("Campaign Broadcast #{$broadcastId} telah dibatalkan.");
            return self::SUCCESS;
        }

        // Tandai status broadcast sebagai processing
        $broadcast->update([
            'status'     => 'processing',
            'started_at' => now(),
        ]);

        $this->info("Memulai pengiriman Broadcast #{$broadcast->id} [Target: {$broadcast->target_label}]...");
        $this->info("Total Penerima: {$broadcast->total_recipients} | Jeda: {$broadcast->delay_seconds} detik");

        $pendingLogs = WhatsAppBroadcastLog::where('whatsapp_broadcast_id', $broadcast->id)
            ->where('status', 'pending')
            ->get();

        foreach ($pendingLogs as $log) {
            // Re-check status campaign di database jika admin menekan 'Batal' di UI
            $freshBroadcast = WhatsAppBroadcast::find($broadcast->id);
            if ($freshBroadcast && $freshBroadcast->status === 'cancelled') {
                $this->warn("Pengiriman broadcast #{$broadcast->id} dibatalkan oleh Admin.");
                Log::info("WhatsApp Broadcast #{$broadcast->id} cancelled by admin during processing.");
                return self::SUCCESS;
            }

            // Subtitusi variabel/placeholder kustom dalam template pesan
            $personalizedMessage = $this->replacePlaceholders($broadcast->message, $log);

            $this->output->write("Mengirim ke {$log->user_name} ({$log->phone})... ");

            $sent = WhatsAppService::send($log->phone, $personalizedMessage);

            if ($sent) {
                $log->update([
                    'status'  => 'sent',
                    'sent_at' => now(),
                ]);

                $broadcast->increment('sent_count');
                $this->info("✅ SUKSES");
            } else {
                $log->update([
                    'status'        => 'failed',
                    'error_message' => 'Gagal terhubung atau ditolak oleh WhatsApp Gateway',
                ]);

                $broadcast->increment('failed_count');
                $this->error("❌ GAGAL");
            }

            // Jeda bertahap antar pesan (Anti-Ban delay)
            $delay = max(1, (int) $broadcast->delay_seconds);
            sleep($delay);
        }

        // Selesaikan campaign jika tidak dibatalkan
        $freshBroadcast = WhatsAppBroadcast::find($broadcast->id);
        if ($freshBroadcast && $freshBroadcast->status !== 'cancelled') {
            $freshBroadcast->update([
                'status'       => 'completed',
                'completed_at' => now(),
            ]);

            $this->info("🎉 Campaign Broadcast #{$broadcast->id} SELESAI!");
        }

        return self::SUCCESS;
    }

    /**
     * Replace template variables like {name}, {kelas}, {role}, {nis_nip}
     */
    protected function replacePlaceholders(string $message, WhatsAppBroadcastLog $log): string
    {
        $userName  = $log->user_name ?: 'Pengguna';
        $userGroup = $log->recipient_group ?: '-';

        $replacements = [
            '{name}'    => $userName,
            '{nama}'    => $userName,
            '{kelas}'   => $userGroup,
            '{group}'   => $userGroup,
        ];

        return strtr($message, $replacements);
    }
}

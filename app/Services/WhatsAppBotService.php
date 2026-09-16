<?php

namespace App\Services;

use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppBotService
{
    /**
     * URL dasar Baileys Node Bot (contoh: http://localhost:3000)
     */
    protected static function getBaseUrl(): string
    {
        $gatewayUrl = config('services.whatsapp.url', 'http://localhost:3000/send-message');
        $parsed = parse_url($gatewayUrl);
        $scheme = $parsed['scheme'] ?? 'http';
        $host   = $parsed['host'] ?? 'localhost';
        $port   = isset($parsed['port']) ? ':' . $parsed['port'] : ':3000';

        return "{$scheme}://{$host}{$port}";
    }

    /**
     * Ambil status realtime bot WhatsApp dari Node.js service.
     */
    public static function getStatus(): array
    {
        $baseUrl = self::getBaseUrl();

        try {
            $response = Http::withoutVerifying()
                ->timeout(2)
                ->get("{$baseUrl}/status");

            if ($response->successful()) {
                $data = $response->json();
                
                // Pastikan sync dengan WebsiteSetting
                $enabledSetting = WebsiteSetting::get('wa_bot_enabled', '1');
                $data['setting_enabled'] = ($enabledSetting === '1' || $enabledSetting === 1 || $enabledSetting === true);

                return $data;
            }
        } catch (\Exception $e) {
            // Node server mati/offline
        }

        $enabledSetting = WebsiteSetting::get('wa_bot_enabled', '0');
        $isBotEnabled = ($enabledSetting === '1' || $enabledSetting === 1 || $enabledSetting === true);

        return [
            'status'               => 'nonaktif',
            'bot_enabled'          => false,
            'setting_enabled'      => $isBotEnabled,
            'is_connected'         => false,
            'qr_code'              => null,
            'connected_number'     => null,
            'connected_name'       => null,
            'last_connected_at'    => null,
            'last_disconnected_at' => null,
            'last_error'           => 'Service Baileys Node.js tidak berjalan',
        ];
    }

    /**
     * Mengaktifkan Bot WhatsApp & menjalankan proses Node.js jika belum aktif.
     */
    public static function start(): array
    {
        WebsiteSetting::set('wa_bot_enabled', '1');

        $baseUrl = self::getBaseUrl();
        $isServiceUp = false;

        try {
            $response = Http::withoutVerifying()->timeout(2)->get("{$baseUrl}/status");
            if ($response->successful()) {
                $isServiceUp = true;
            }
        } catch (\Exception $e) {
            $isServiceUp = false;
        }

        if (! $isServiceUp) {
            self::spawnNodeProcess();
            usleep(1500000); // Tunggu 1.5 detik agar Express boot up
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(5)
                ->post("{$baseUrl}/start");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Bot WhatsApp berhasil diaktifkan.',
                    'data'    => $response->json()
                ];
            }
        } catch (\Exception $e) {
            Log::error("WhatsAppBotService start error: " . $e->getMessage());
        }

        return [
            'success' => true,
            'message' => 'Proses Bot WhatsApp dijalankan.',
            'data'    => self::getStatus()
        ];
    }

    /**
     * Menonaktifkan Bot WhatsApp & menghentikan koneksi.
     */
    public static function stop(): array
    {
        WebsiteSetting::set('wa_bot_enabled', '0');

        $baseUrl = self::getBaseUrl();

        try {
            Http::withoutVerifying()
                ->timeout(4)
                ->post("{$baseUrl}/stop");
        } catch (\Exception $e) {
            // Abaikan error jika server sudah mati
        }

        // Hentikan proses jika perlu
        self::killNodeProcess();

        return [
            'success' => true,
            'message' => 'Bot WhatsApp berhasil dinonaktifkan.',
            'data'    => self::getStatus()
        ];
    }

    /**
     * Memutuskan sesi koneksi WhatsApp (Logout).
     */
    public static function disconnect(): array
    {
        $baseUrl = self::getBaseUrl();

        try {
            $response = Http::withoutVerifying()
                ->timeout(6)
                ->post("{$baseUrl}/disconnect");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Koneksi WhatsApp berhasil diputuskan.',
                    'data'    => $response->json()
                ];
            }
        } catch (\Exception $e) {
            Log::error("WhatsAppBotService disconnect error: " . $e->getMessage());
        }

        return [
            'success' => false,
            'message' => 'Gagal terhubung ke service Bot WhatsApp untuk memutuskan koneksi.',
            'data'    => self::getStatus()
        ];
    }

    /**
     * Reset Sesi WhatsApp (Hapus folder Auth & Siapkan QR baru).
     */
    public static function resetSession(): array
    {
        WebsiteSetting::set('wa_bot_enabled', '1');
        $baseUrl = self::getBaseUrl();

        try {
            $response = Http::withoutVerifying()
                ->timeout(6)
                ->post("{$baseUrl}/reset-session");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Sesi WhatsApp berhasil di-reset. QR Code baru disiapkan.',
                    'data'    => $response->json()
                ];
            }
        } catch (\Exception $e) {
            Log::error("WhatsAppBotService resetSession HTTP error: " . $e->getMessage());
        }

        // Fallback jika HTTP tidak dapat dijangkau: Hapus folder auth_info_baileys secara manual
        $authDir = base_path('whatsapp-bot/auth_info_baileys');
        if (File::exists($authDir)) {
            File::deleteDirectory($authDir);
        }

        // Restart bot process
        self::spawnNodeProcess();

        return [
            'success' => true,
            'message' => 'Sesi WhatsApp berhasil di-reset secara manual. Menjalankan ulang bot...',
            'data'    => self::getStatus()
        ];
    }

    /**
     * Jalankan proses `node whatsapp-bot/index.js` di background.
     */
    protected static function spawnNodeProcess(): void
    {
        $botDir  = base_path('whatsapp-bot');
        $logFile = storage_path('logs/whatsapp-bot.log');
        $pidFile = storage_path('app/whatsapp-bot.pid');

        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            pclose(popen("start /B node {$botDir}/index.js > {$logFile} 2>&1", "r"));
        } else {
            $command = "cd " . escapeshellarg($botDir) . " && nohup node index.js > " . escapeshellarg($logFile) . " 2>&1 & echo $! > " . escapeshellarg($pidFile);
            exec($command);
        }
    }

    /**
     * Hentikan proses node yang tercatat di PID file / pkill.
     */
    protected static function killNodeProcess(): void
    {
        $pidFile = storage_path('app/whatsapp-bot.pid');

        if (File::exists($pidFile)) {
            $pid = trim(File::get($pidFile));
            if (!empty($pid) && is_numeric($pid)) {
                if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
                    exec("taskkill /F /PID {$pid}");
                } else {
                    exec("kill -9 {$pid}");
                }
            }
            File::delete($pidFile);
        }

        // Pkill fallback di Linux
        if (strncasecmp(PHP_OS, 'WIN', 3) !== 0) {
            exec("pkill -f 'node.*whatsapp-bot/index.js'");
        }
    }
}

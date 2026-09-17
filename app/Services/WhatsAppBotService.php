<?php

namespace App\Services;

use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppBotService
{
    /**
     * URL dasar Baileys Node Bot (contoh: http://127.0.0.1:3000)
     */
    protected static function getBaseUrl(): string
    {
        $gatewayUrl = config('services.whatsapp.url', 'http://127.0.0.1:3000/send-message');
        $parsed = parse_url($gatewayUrl);
        $scheme = $parsed['scheme'] ?? 'http';
        $host   = $parsed['host'] ?? '127.0.0.1';
        $port   = isset($parsed['port']) ? ':' . $parsed['port'] : ':3000';

        if ($host !== 'localhost' && $host !== '127.0.0.1' && !filter_var($host, FILTER_VALIDATE_IP)) {
            $host = '127.0.0.1';
        }

        return "{$scheme}://{$host}{$port}";
    }

    /**
     * Periksa apakah proses node whatsapp-bot sedang berjalan di OS & merespons HTTP.
     */
    public static function isNodeProcessRunning(): bool
    {
        $baseUrl = self::getBaseUrl();

        // 1. Direct HTTP health check to Express
        try {
            $response = Http::withoutVerifying()->timeout(1)->get("{$baseUrl}/status");
            if ($response->successful()) {
                return true;
            }
        } catch (\Exception $e) {
            // Node server HTTP offline
        }

        // 2. PID File check
        $pidFile = storage_path('app/whatsapp-bot.pid');
        if (File::exists($pidFile)) {
            $pid = trim((string) File::get($pidFile));
            if (!empty($pid) && is_numeric($pid)) {
                if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
                    $output = [];
                    exec("tasklist /FI \"PID eq {$pid}\"", $output);
                    if (count($output) > 1 && str_contains(implode("\n", $output), (string) $pid)) {
                        return true;
                    }
                } else {
                    if (function_exists('posix_kill')) {
                        if (@posix_kill((int) $pid, 0)) {
                            return true;
                        }
                    } else {
                        $execOut = shell_exec("kill -0 {$pid} 2>&1");
                        if (empty($execOut)) {
                            return true;
                        }
                    }
                }
            }
            // Clean up stale PID file if process is dead
            File::delete($pidFile);
        }

        return false;
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
                
                // Sync dengan WebsiteSetting DB
                $enabledSetting = WebsiteSetting::get('wa_bot_enabled', '1');
                $data['setting_enabled'] = ($enabledSetting === '1' || $enabledSetting === 1 || $enabledSetting === true);

                return $data;
            }
        } catch (\Exception $e) {
            // Node server offline
        }

        $enabledSetting = WebsiteSetting::get('wa_bot_enabled', '0');
        $isBotEnabled = ($enabledSetting === '1' || $enabledSetting === 1 || $enabledSetting === true);

        // Jika setting aktif tapi service mati, coba spawn secara otomatis
        if ($isBotEnabled && !self::isNodeProcessRunning()) {
            self::spawnNodeProcess();
        }

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
            usleep(1500000); // Jeda 1.5 detik agar Express server boot up
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
            // Abaikan jika server sudah mati
        }

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

        // Fallback jika HTTP tidak dapat dijangkau: Hapus folder auth_info_baileys
        $authDir = base_path('whatsapp-bot/auth_info_baileys');
        if (File::exists($authDir)) {
            File::deleteDirectory($authDir);
        }

        self::killNodeProcess();
        self::spawnNodeProcess();

        return [
            'success' => true,
            'message' => 'Sesi WhatsApp berhasil di-reset secara manual. Menjalankan ulang bot...',
            'data'    => self::getStatus()
        ];
    }

    /**
     * Cari lokasi binary node di OS (termasuk NVM).
     */
    protected static function getNodeBinary(): string
    {
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            return 'node';
        }

        $which = trim((string) shell_exec('which node 2>/dev/null'));
        if (!empty($which) && File::exists($which)) {
            return escapeshellarg($which);
        }

        $commonPaths = [
            '/home/muhammad/.nvm/versions/node/v22.13.1/bin/node',
            '/usr/local/bin/node',
            '/usr/bin/node',
        ];

        foreach ($commonPaths as $path) {
            if (File::exists($path)) {
                return escapeshellarg($path);
            }
        }

        return 'node';
    }

    /**
     * Jalankan proses `node whatsapp-bot/index.js` di background jika belum berjalan.
     */
    protected static function spawnNodeProcess(): void
    {
        if (self::isNodeProcessRunning()) {
            return;
        }

        $nodeBin = self::getNodeBinary();
        $botDir  = base_path('whatsapp-bot');
        $logFile = storage_path('logs/whatsapp-bot.log');
        $pidFile = storage_path('app/whatsapp-bot.pid');

        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            pclose(popen("start /B {$nodeBin} {$botDir}/index.js > {$logFile} 2>&1", "r"));
        } else {
            $command = "cd " . escapeshellarg($botDir) . " && (nohup {$nodeBin} index.js > " . escapeshellarg($logFile) . " 2>&1 < /dev/null &) && pgrep -f 'node.*whatsapp-bot/index.js' > " . escapeshellarg($pidFile);
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
            $pid = trim((string) File::get($pidFile));
            if (!empty($pid) && is_numeric($pid)) {
                if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
                    exec("taskkill /F /PID {$pid}");
                } else {
                    exec("kill -9 {$pid} 2>/dev/null");
                }
            }
            File::delete($pidFile);
        }

        // Pkill fallback di Linux
        if (strncasecmp(PHP_OS, 'WIN', 3) !== 0) {
            exec("pkill -f 'node.*whatsapp-bot/index.js' 2>/dev/null");
        }
    }
}

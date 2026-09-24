<?php

namespace App\Services;

use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppBotService
{
    /**
     * URL dasar Baileys Node Bot (contoh: http://127.0.0.1:4545)
     */
    protected static function getBaseUrl(): string
    {
        $gatewayUrl = config('services.whatsapp.url', 'http://127.0.0.1:4545/send-message');
        $parsed = parse_url($gatewayUrl);
        $scheme = $parsed['scheme'] ?? 'http';
        $host   = $parsed['host'] ?? '127.0.0.1';
        $port   = isset($parsed['port']) ? ':' . $parsed['port'] : ':4545';

        if ($host === 'localhost' || !filter_var($host, FILTER_VALIDATE_IP)) {
            $host = '127.0.0.1';
        }

        return "{$scheme}://{$host}{$port}";
    }

    /**
     * Micro cURL request untuk fetch status lokal dalam hitungan milidetik.
     */
    protected static function fastHttpGet(string $url, float $timeoutSeconds = 1.5): ?array
    {
        if (!function_exists('curl_init')) {
            return null;
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT_MS => 800,
            CURLOPT_TIMEOUT_MS     => (int) ($timeoutSeconds * 1000),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT      => 'EskasabaFastCurl/1.0',
        ]);
        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($output !== false && $httpCode === 200) {
            $data = json_decode($output, true);
            return is_array($data) ? $data : null;
        }

        return null;
    }

    /**
     * Periksa PID process tanpa melakukan HTTP request ganda.
     */
    public static function isPidRunningOnly(): bool
    {
        $pidFile = storage_path('app/whatsapp-bot.pid');
        if (File::exists($pidFile)) {
            $pid = trim((string) File::get($pidFile));
            if (!empty($pid) && is_numeric($pid)) {
                try {
                    if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
                        if (function_exists('exec')) {
                            $output = [];
                            @exec("tasklist /FI \"PID eq {$pid}\"", $output);
                            if (count($output) > 1 && str_contains(implode("\n", $output), (string) $pid)) {
                                return true;
                            }
                        }
                    } else {
                        if (function_exists('posix_kill')) {
                            if (@posix_kill((int) $pid, 0)) {
                                return true;
                            }
                        }
                    }
                } catch (\Throwable $e) {}
            }
            File::delete($pidFile);
        }

        return false;
    }

    /**
     * Periksa apakah proses node whatsapp-bot sedang berjalan di OS & merespons HTTP.
     */
    public static function isNodeProcessRunning(): bool
    {
        $baseUrl = self::getBaseUrl();
        if (self::fastHttpGet("{$baseUrl}/status", 1.0) !== null) {
            return true;
        }

        return self::isPidRunningOnly();
    }

    /**
     * Ambil status realtime bot WhatsApp dari Node.js service.
     */
    public static function getStatus(): array
    {
        return Cache::remember('wa_bot_status_cache', 1, function () {
            $baseUrl = self::getBaseUrl();

            // Fast cURL check (< 10ms jika service online)
            $data = self::fastHttpGet("{$baseUrl}/status", 1.5);

            if ($data !== null) {
                $enabledSetting = WebsiteSetting::get('wa_bot_enabled', '1');
                $data['setting_enabled'] = ($enabledSetting === '1' || $enabledSetting === 1 || $enabledSetting === true);

                return $data;
            }

            $httpError = 'Gagal terhubung ke ' . $baseUrl . '/status';
            $enabledSetting = WebsiteSetting::get('wa_bot_enabled', '0');
            $isBotEnabled = ($enabledSetting === '1' || $enabledSetting === 1 || $enabledSetting === true);

            // Jika setting aktif tapi service mati, spawn secara cepat tanpa HTTP retry ganda
            if ($isBotEnabled && !self::isPidRunningOnly()) {
                self::spawnNodeProcess();
            }

            return [
                'status'               => $isBotEnabled ? 'menjalankan' : 'nonaktif',
                'bot_enabled'          => $isBotEnabled,
                'setting_enabled'      => $isBotEnabled,
                'is_connected'         => false,
                'qr_code'              => null,
                'connected_number'     => null,
                'connected_name'       => null,
                'last_connected_at'    => null,
                'last_disconnected_at' => null,
                'last_error'           => $isBotEnabled ? 'Memulai service Baileys Node.js...' : ($httpError ?? 'Service Baileys Node.js tidak berjalan'),
            ];
        });
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
        } catch (\Throwable $e) {
            $isServiceUp = false;
        }

        if (! $isServiceUp) {
            self::spawnNodeProcess();
            // Retry ping hingga 4 kali (total 1.2 detik) menunggu Express boot up
            for ($i = 0; $i < 4; $i++) {
                usleep(300000);
                try {
                    $ping = Http::withoutVerifying()->timeout(1)->get("{$baseUrl}/status");
                    if ($ping->successful()) {
                        $isServiceUp = true;
                        break;
                    }
                } catch (\Throwable $e) {}
            }
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(5)
                ->post("{$baseUrl}/start");

            if ($response->successful()) {
                usleep(300000);
                return [
                    'success' => true,
                    'message' => 'Bot WhatsApp berhasil diaktifkan.',
                    'data'    => self::getStatus()
                ];
            }
        } catch (\Throwable $e) {
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
        $httpSuccess = false;

        try {
            $response = Http::withoutVerifying()
                ->timeout(4)
                ->post("{$baseUrl}/stop");
            if ($response->successful()) {
                $httpSuccess = true;
            }
        } catch (\Exception $e) {
            // Server HTTP offline
        }

        // Hanya kill jika Node HTTP service tidak merespons
        if (! $httpSuccess) {
            self::killNodeProcess();
        }

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
                usleep(500000);
                return [
                    'success' => true,
                    'message' => 'Koneksi WhatsApp berhasil diputuskan.',
                    'data'    => self::getStatus()
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
                usleep(1000000);
                return [
                    'success' => true,
                    'message' => 'Sesi WhatsApp berhasil di-reset. QR Code baru disiapkan.',
                    'data'    => self::getStatus()
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
        usleep(1000000);

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

        if (function_exists('shell_exec')) {
            try {
                $which = trim((string) @shell_exec('which node 2>/dev/null'));
                if (!empty($which) && File::exists($which)) {
                    return escapeshellarg($which);
                }
            } catch (\Throwable $e) {}
        }

        $homeDir = $_SERVER['HOME'] ?? getenv('HOME') ?: '/root';
        $globPaths = array_merge(
            glob('/usr/local/nvm/versions/node/*/bin/node') ?: [],
            glob('/home/*/.nvm/versions/node/*/bin/node') ?: [],
            glob($homeDir . '/.nvm/versions/node/*/bin/node') ?: []
        );

        foreach ($globPaths as $path) {
            if (File::exists($path)) {
                return escapeshellarg($path);
            }
        }

        $commonPaths = [
            '/usr/local/bin/node',
            '/usr/bin/node',
            '/bin/node',
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

        try {
            $nodeBin = self::getNodeBinary();
            $botDir  = base_path('whatsapp-bot');
            $logFile = storage_path('logs/whatsapp-bot.log');
            $pidFile = storage_path('app/whatsapp-bot.pid');

            if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
                if (function_exists('popen') && function_exists('pclose')) {
                    @pclose(@popen("start /B {$nodeBin} {$botDir}/index.js > {$logFile} 2>&1", "r"));
                }
            } else {
                if (function_exists('exec')) {
                    $command = "cd " . escapeshellarg($botDir) . " && (nohup {$nodeBin} index.js > " . escapeshellarg($logFile) . " 2>&1 < /dev/null &) && pgrep -f 'node.*whatsapp-bot/index.js' > " . escapeshellarg($pidFile);
                    @exec($command);
                }
            }
        } catch (\Throwable $e) {
            Log::warning("WhatsAppBotService spawnNodeProcess error: " . $e->getMessage());
        }
    }

    /**
     * Hentikan proses node yang tercatat di PID file / pkill.
     */
    protected static function killNodeProcess(): void
    {
        $pidFile = storage_path('app/whatsapp-bot.pid');

        try {
            if (File::exists($pidFile)) {
                $pid = trim((string) File::get($pidFile));
                if (!empty($pid) && is_numeric($pid) && function_exists('exec')) {
                    if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
                        @exec("taskkill /F /PID {$pid}");
                    } else {
                        @exec("kill -9 {$pid} 2>/dev/null");
                    }
                }
                File::delete($pidFile);
            }

            if (strncasecmp(PHP_OS, 'WIN', 3) !== 0 && function_exists('exec')) {
                @exec("pkill -f 'node.*whatsapp-bot/index.js' 2>/dev/null");
            }
        } catch (\Throwable $e) {}
    }
}

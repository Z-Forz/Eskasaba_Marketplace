@php
    $statusKey = $status['status'] ?? 'nonaktif';
    $isBotEnabled = !empty($status['bot_enabled']);
    $isConnected = !empty($status['is_connected']);
    $qrCode = $status['qr_code'] ?? null;
    $connectedNumber = $status['connected_number'] ?? null;
    $connectedName = $status['connected_name'] ?? 'Eskasaba Bot';
    $lastError = $status['last_error'] ?? null;

    $configs = [
        'nonaktif' => [
            'badge' => 'NONAKTIF',
            'bgClass' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
            'dotClass' => 'bg-slate-500',
            'pulseClass' => 'bg-slate-400',
            'desc' => 'Bot WhatsApp dihentikan dan tidak berjalan.'
        ],
        'menjalankan' => [
            'badge' => 'MENJALANKAN...',
            'bgClass' => 'bg-sky-100 text-sky-800 dark:bg-sky-950/80 dark:text-sky-300',
            'dotClass' => 'bg-sky-500',
            'pulseClass' => 'bg-sky-400',
            'desc' => 'Memulai service Baileys...'
        ],
        'menunggu_qr' => [
            'badge' => 'MENUNGGU QR SCAN',
            'bgClass' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300',
            'dotClass' => 'bg-amber-500',
            'pulseClass' => 'bg-amber-400',
            'desc' => 'Silakan pindai QR Code dengan aplikasi WhatsApp di HP.'
        ],
        'menghubungkan' => [
            'badge' => 'MENGHUBUNGKAN...',
            'bgClass' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300',
            'dotClass' => 'bg-blue-500',
            'pulseClass' => 'bg-blue-400',
            'desc' => 'Proses sinkronisasi dengan WhatsApp server...'
        ],
        'terhubung' => [
            'badge' => 'TERHUBUNG',
            'bgClass' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300',
            'dotClass' => 'bg-emerald-500',
            'pulseClass' => 'bg-emerald-400',
            'desc' => 'Bot WhatsApp aktif dan siap mengirim pesan.'
        ],
        'terputus' => [
            'badge' => 'TERPUTUS',
            'bgClass' => 'bg-orange-100 text-orange-800 dark:bg-orange-950/80 dark:text-orange-300',
            'dotClass' => 'bg-orange-500',
            'pulseClass' => 'bg-orange-400',
            'desc' => 'Koneksi terputus. Sistem mencoba reconnect jika bot aktif.'
        ],
        'error' => [
            'badge' => 'ERROR',
            'bgClass' => 'bg-red-100 text-red-800 dark:bg-red-950/80 dark:text-red-300',
            'dotClass' => 'bg-red-500',
            'pulseClass' => 'bg-red-400',
            'desc' => 'Terjadi kendala pada proses WhatsApp Bot.'
        ]
    ];

    $cfg = $configs[$statusKey] ?? $configs['nonaktif'];

    $showQrPanel = ($statusKey === 'menunggu_qr' || (!empty($qrCode) && !$isConnected));
    $showConnPanel = ($statusKey === 'terhubung' || $isConnected);
    $showInactivePanel = ($statusKey === 'nonaktif' || (!$isBotEnabled && empty($status['setting_enabled'])));
    $showConnectingPanel = (!$showQrPanel && !$showConnPanel && !$showInactivePanel);
@endphp

<x-layouts.admin title="Kelola WhatsApp Bot - Admin Panel">

    <div
        class="space-y-8"
        id="whatsapp-admin-config"
        data-status-url="{{ route('admin.whatsapp.status', [], false) }}"
        data-start-url="{{ route('admin.whatsapp.start', [], false) }}"
        data-stop-url="{{ route('admin.whatsapp.stop', [], false) }}"
        data-disconnect-url="{{ route('admin.whatsapp.disconnect', [], false) }}"
        data-reset-url="{{ route('admin.whatsapp.reset-session', [], false) }}"
        data-csrf="{{ csrf_token() }}"
        data-initial='@json($status)'
    >

        {{-- Page Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                    <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp Bot Gateway
                </p>
                <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl flex items-center gap-2">
                    Pengelolaan WhatsApp Bot
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kontrol service Baileys, pantau status koneksi WhatsApp, dan pindai QR Code pairing secara langsung.
                </p>
            </div>

            <div class="flex items-center gap-2.5">
                <div class="inline-flex items-center gap-2 rounded-2xl border border-emerald-200/80 bg-emerald-50/80 px-4 py-2.5 text-xs font-bold text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300 shadow-xs">
                    <span class="relative flex h-2 w-2">
                        <span id="sync-ping" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>Realtime Auto-Sync</span>
                </div>

                <div class="hidden sm:inline-flex items-center gap-1.5 rounded-2xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400 shadow-xs" title="Engine: Baileys Multi-Device">
                    <i class="fa-solid fa-server text-slate-400 text-xs"></i>
                    <span>Baileys Gateway</span>
                </div>
            </div>
        </div>

        {{-- Flash Notification --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-lg shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300 flex items-center gap-3">
                <i class="fa-solid fa-circle-exclamation text-lg shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Top Summary Grid --}}
        <div class="grid gap-4 md:grid-cols-3">

            {{-- Card 1: Status Bot --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Status Bot Service
                    </p>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                        <i class="fa-solid fa-robot text-lg"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-3">
                    <span id="pulse-indicator" class="relative flex h-3.5 w-3.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $cfg['pulseClass'] }} opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3.5 w-3.5 {{ $cfg['dotClass'] }}"></span>
                    </span>
                    <span id="badge-bot-status" class="inline-flex items-center rounded-full px-3 py-1 text-xs font-black uppercase tracking-wider {{ $cfg['bgClass'] }}">
                        {{ $cfg['badge'] }}
                    </span>
                </div>
                <p id="desc-bot-status" class="mt-3 text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    {{ $cfg['desc'] }}
                </p>
            </div>

            {{-- Card 2: Koneksi WhatsApp --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Koneksi WhatsApp
                    </p>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300">
                        <i class="fa-brands fa-whatsapp text-xl"></i>
                    </div>
                </div>
                <p id="text-connected-number" class="mt-4 text-xl font-black text-slate-900 dark:text-white truncate">
                    @if($isConnected && $connectedNumber)
                        +{{ $connectedNumber }}
                    @else
                        {{ $isBotEnabled ? 'Belum Terhubung' : 'Nonaktif' }}
                    @endif
                </p>
                <p id="text-connected-name" class="mt-1 text-xs font-bold text-slate-500 dark:text-slate-400">
                    @if($isConnected && $connectedNumber)
                        {{ $connectedName }}
                    @else
                        {{ $isBotEnabled ? 'Menunggu autentikasi...' : 'Bot tidak berjalan' }}
                    @endif
                </p>
            </div>

            {{-- Card 3: Informasi Log Aktivitas --}}
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Aktivitas Terakhir
                    </p>
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-700 dark:bg-blue-950/50 dark:text-blue-300">
                        <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                    </div>
                </div>
                <div class="mt-3 space-y-1.5 text-xs text-slate-600 dark:text-slate-300">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">Terhubung:</span>
                        <span id="text-last-connected" class="font-bold">{{ $status['last_connected_at'] ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 font-medium">Terputus:</span>
                        <span id="text-last-disconnected" class="font-bold">{{ $status['last_disconnected_at'] ?? '-' }}</span>
                    </div>
                </div>
                <div id="wrapper-last-error" class="mt-2.5 {{ $lastError ? '' : 'hidden' }} rounded-xl bg-red-50 p-2 text-[11px] text-red-700 dark:bg-red-950/40 dark:text-red-300 font-medium truncate">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i> <span id="text-last-error">{{ $lastError ?? '-' }}</span>
                </div>
            </div>

        </div>

        {{-- Dynamic Panel: QR Code / Connected Info / Stopped State --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 md:p-8 shadow-xs dark:border-slate-800 dark:bg-slate-900 space-y-6">

            {{-- PANEL 1: QR CODE DISPLAY --}}
            <div id="panel-qr-code" class="{{ $showQrPanel ? 'flex' : 'hidden' }} flex-col items-center justify-center py-6 text-center space-y-4">
                <div class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-1.5 text-xs font-bold text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                    <i class="fa-solid fa-qrcode text-amber-600"></i> Pindai QR Code untuk Menghubungkan
                </div>

                <div class="relative mx-auto rounded-3xl border-4 border-slate-100 bg-white p-4 shadow-md dark:border-slate-800 dark:bg-white">
                    <img id="qr-code-img" src="{{ $qrCode ?? '' }}" alt="WhatsApp QR Code Baileys" class="h-64 w-64 object-contain mx-auto transition-all">
                </div>

                <div class="max-w-md space-y-1">
                    <p class="text-sm font-bold text-slate-800 dark:text-white">
                        Langkah Menghubungkan WhatsApp:
                    </p>
                    <ol class="text-xs text-slate-500 dark:text-slate-400 list-decimal list-inside space-y-1 text-left inline-block">
                        <li>Buka aplikasi <strong>WhatsApp</strong> di HP Anda.</li>
                        <li>Ketuk menu <strong>Pengaturan</strong> atau <strong>Titik Tiga (⋮)</strong> di sudut atas.</li>
                        <li>Pilih <strong>Perangkat Tertaut</strong>, lalu ketuk <strong>Tautkan Perangkat</strong>.</li>
                        <li>Arahkan kamera HP Anda ke QR Code di atas.</li>
                    </ol>
                </div>

                <p class="text-[11px] text-slate-400 font-medium animate-pulse">
                    <i class="fa-solid fa-arrows-rotate text-emerald-600 mr-1"></i> QR Code akan otomatis diperbarui dan hilang setelah terhubung.
                </p>
            </div>

            {{-- PANEL 2: CONNECTED DEVICE STATE --}}
            <div id="panel-connected" class="{{ $showConnPanel ? 'flex' : 'hidden' }} flex-col items-center justify-center py-8 text-center space-y-4">
                <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 shadow-xs">
                    <i class="fa-brands fa-whatsapp text-4xl"></i>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3.5 py-1 text-xs font-black uppercase tracking-wider text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                        <i class="fa-solid fa-circle text-[8px] text-emerald-600"></i> WhatsApp Terhubung
                    </span>
                    <h3 id="connected-phone-display" class="mt-3 text-2xl font-black text-slate-900 dark:text-white">
                        +{{ $connectedNumber ?? '' }}
                    </h3>
                    <p id="connected-name-display" class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $connectedName }}
                    </p>
                </div>
                <p class="max-w-md text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    WhatsApp Gateway aktif & siap mengirim notifikasi otomatis untuk pesanan baru, status transaksi, dan verifikasi seller.
                </p>
            </div>

            {{-- PANEL 3: BOT INACTIVE / STOPPED STATE --}}
            <div id="panel-inactive" class="{{ $showInactivePanel ? 'flex' : 'hidden' }} flex-col items-center justify-center py-8 text-center space-y-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-slate-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500">
                    <i class="fa-solid fa-power-off text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">
                        Bot WhatsApp Saat Ini Nonaktif
                    </h3>
                    <p class="mt-1 max-w-md text-xs text-slate-500 dark:text-slate-400">
                        Bot tidak berjalan di background dan tidak melakukan auto-reconnect. Klik tombol <strong>Aktifkan Bot</strong> untuk menjalankan service Baileys.
                    </p>
                </div>
            </div>

            {{-- PANEL 4: CONNECTING / LOADING STATE --}}
            <div id="panel-connecting" class="{{ $showConnectingPanel ? 'flex' : 'hidden' }} flex-col items-center justify-center py-8 text-center space-y-4">
                <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-blue-50 text-blue-600 dark:bg-blue-950/80 dark:text-blue-400">
                    <i class="fa-solid fa-spinner fa-spin text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">
                        Menghubungkan ke WhatsApp...
                    </h3>
                    <p class="mt-1 max-w-md text-xs text-slate-500 dark:text-slate-400">
                        Proses Baileys sedang memvalidasi sesi atau menyiapkan QR Code. Mohon tunggu sebentar.
                    </p>
                </div>
            </div>

            {{-- CONTROLS & ACTION BUTTONS --}}
            <div class="border-t border-slate-100 pt-6 dark:border-slate-800">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">
                    Kontrol & Aksi Bot
                </p>

                <div class="flex flex-wrap items-center gap-3">

                    {{-- Tombol Aktifkan Bot --}}
                    <button
                        type="button"
                        id="btn-start-bot"
                        onclick="executeAction('start', this, 'Mengaktifkan...')"
                        class="{{ ($statusKey === 'nonaktif' || !$isBotEnabled) ? 'inline-flex' : 'hidden' }} items-center gap-2 rounded-2xl bg-emerald-800 px-5 py-3 text-xs font-bold text-white shadow-xs transition hover:bg-emerald-900 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <i class="fa-solid fa-play"></i>
                        <span>Aktifkan Bot</span>
                    </button>

                    {{-- Tombol Minta / Generasi QR Code Baru --}}
                    <button
                        type="button"
                        id="btn-refresh-qr"
                        onclick="executeAction('start', this, 'Menyiapkan QR...')"
                        class="{{ ($isBotEnabled && !$isConnected) ? 'inline-flex' : 'hidden' }} items-center gap-2 rounded-2xl bg-amber-600 px-5 py-3 text-xs font-bold text-white shadow-xs transition hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <i class="fa-solid fa-qrcode"></i>
                        <span>Minta QR Code Baru</span>
                    </button>

                    {{-- Tombol Menonaktifkan Bot --}}
                    <button
                        type="button"
                        id="btn-stop-bot"
                        onclick="executeAction('stop', this, 'Menonaktifkan...')"
                        class="{{ ($statusKey !== 'nonaktif' && $isBotEnabled) ? 'inline-flex' : 'hidden' }} items-center gap-2 rounded-2xl bg-slate-800 px-5 py-3 text-xs font-bold text-white shadow-xs transition hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <i class="fa-solid fa-stop"></i>
                        <span>Nonaktifkan Bot</span>
                    </button>

                    {{-- Tombol Memutuskan Koneksi WA --}}
                    <button
                        type="button"
                        id="btn-disconnect-bot"
                        onclick="executeAction('disconnect', this, 'Memutuskan...')"
                        class="{{ ($isConnected || $statusKey === 'terhubung') ? 'inline-flex' : 'hidden' }} items-center gap-2 rounded-2xl bg-orange-600 px-5 py-3 text-xs font-bold text-white shadow-xs transition hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Memutuskan Koneksi</span>
                    </button>

                    {{-- Tombol Reset Session (Buka Modal) --}}
                    <button
                        type="button"
                        id="btn-reset-session-trigger"
                        onclick="openResetModal()"
                        class="inline-flex items-center gap-2 rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-xs font-bold text-red-700 transition hover:bg-red-100 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-900/60 shadow-xs cursor-pointer ml-auto"
                    >
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Reset Session</span>
                    </button>

                </div>
            </div>

        </div>

    </div>

    {{-- MODAL KONFIRMASI RESET SESSION --}}
    <div id="modal-reset-session" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-4">
            <div class="flex items-center gap-3 text-red-600 dark:text-red-400">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-950/80">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">Reset Session WhatsApp?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Tindakan ini membutuhkan pemindaian ulang</p>
                </div>
            </div>

            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                Menghapus folder sesi auth Baileys lama. Semua koneksi aktif akan diputuskan dan bot akan menghasilkan <strong>QR Code baru</strong> untuk pairing ulang.
            </p>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button
                    type="button"
                    onclick="closeResetModal()"
                    class="rounded-xl px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 transition cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="button"
                    id="btn-confirm-reset"
                    onclick="confirmResetSession(this)"
                    class="rounded-xl bg-red-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-red-700 shadow-xs transition disabled:opacity-50 cursor-pointer"
                >
                    Ya, Reset Session
                </button>
            </div>
        </div>
    </div>

    {{-- SCRIPT MANAGEMENT & POLLING --}}
    <script>
        (function () {
            const configEl = document.getElementById('whatsapp-admin-config');
            if (!configEl) return;

            const STATUS_URL = configEl.dataset.statusUrl;
            const ACTIONS = {
                start: configEl.dataset.startUrl,
                stop: configEl.dataset.stopUrl,
                disconnect: configEl.dataset.disconnectUrl,
                reset: configEl.dataset.resetUrl
            };
            const CSRF_TOKEN = configEl.dataset.csrf;
            let INITIAL_STATUS = {};
            try {
                INITIAL_STATUS = JSON.parse(configEl.dataset.initial || '{}');
            } catch (e) {
                INITIAL_STATUS = {};
            }

            let isExecuting = false;

            // Mapping tampilan status
            const STATUS_CONFIG = {
                nonaktif: {
                    badge: 'NONAKTIF',
                    bgClass: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                    dotClass: 'bg-slate-400',
                    pulseClass: 'bg-slate-400',
                    desc: 'Bot WhatsApp dihentikan dan tidak berjalan.'
                },
                menjalankan: {
                    badge: 'MENJALANKAN...',
                    bgClass: 'bg-sky-100 text-sky-800 dark:bg-sky-950/80 dark:text-sky-300',
                    dotClass: 'bg-sky-500',
                    pulseClass: 'bg-sky-400',
                    desc: 'Memulai service Baileys...'
                },
                menunggu_qr: {
                    badge: 'MENUNGGU QR SCAN',
                    bgClass: 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300',
                    dotClass: 'bg-amber-500',
                    pulseClass: 'bg-amber-400',
                    desc: 'Silakan pindai QR Code dengan aplikasi WhatsApp di HP.'
                },
                menghubungkan: {
                    badge: 'MENGHUBUNGKAN...',
                    bgClass: 'bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300',
                    dotClass: 'bg-blue-500',
                    pulseClass: 'bg-blue-400',
                    desc: 'Proses sinkronisasi dengan WhatsApp server...'
                },
                terhubung: {
                    badge: 'TERHUBUNG',
                    bgClass: 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300',
                    dotClass: 'bg-emerald-500',
                    pulseClass: 'bg-emerald-400',
                    desc: 'Bot WhatsApp aktif dan siap mengirim pesan.'
                },
                terputus: {
                    badge: 'TERPUTUS',
                    bgClass: 'bg-orange-100 text-orange-800 dark:bg-orange-950/80 dark:text-orange-300',
                    dotClass: 'bg-orange-500',
                    pulseClass: 'bg-orange-400',
                    desc: 'Koneksi terputus. Sistem mencoba reconnect jika bot aktif.'
                },
                error: {
                    badge: 'ERROR',
                    bgClass: 'bg-red-100 text-red-800 dark:bg-red-950/80 dark:text-red-300',
                    dotClass: 'bg-red-500',
                    pulseClass: 'bg-red-400',
                    desc: 'Terjadi kendala pada proses WhatsApp Bot.'
                }
            };

            window.fetchBotStatus = async function () {
                const syncPing = document.getElementById('sync-ping');
                if (syncPing) syncPing.classList.add('opacity-100');

                try {
                    const response = await fetch(STATUS_URL, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!response.ok) throw new Error('Network error');
                    const data = await response.json();
                    renderUI(data);
                } catch (err) {
                    console.warn('Status poll fetch failed:', err);
                }
            };

            function renderUI(data) {
                const statusKey = data.status || 'nonaktif';
                const config = STATUS_CONFIG[statusKey] || STATUS_CONFIG['nonaktif'];

                // Update Status Card
                const badgeEl = document.getElementById('badge-bot-status');
                if (badgeEl) {
                    badgeEl.textContent = config.badge;
                    badgeEl.className = `inline-flex items-center rounded-full px-3 py-1 text-xs font-black uppercase tracking-wider ${config.bgClass}`;
                }

                const descEl = document.getElementById('desc-bot-status');
                if (descEl) descEl.textContent = config.desc;

                const pulseEl = document.getElementById('pulse-indicator');
                if (pulseEl) {
                    pulseEl.querySelector('.animate-ping').className = `animate-ping absolute inline-flex h-full w-full rounded-full ${config.pulseClass} opacity-75`;
                    pulseEl.querySelector('.relative').className = `relative inline-flex rounded-full h-3.5 w-3.5 ${config.dotClass}`;
                }

                // Update Connection Info
                const numberEl = document.getElementById('text-connected-number');
                const nameEl = document.getElementById('text-connected-name');
                if (data.is_connected && data.connected_number) {
                    if (numberEl) numberEl.textContent = '+' + data.connected_number;
                    if (nameEl) nameEl.textContent = data.connected_name || 'Eskasaba Bot';
                } else {
                    if (numberEl) numberEl.textContent = data.bot_enabled ? 'Belum Terhubung' : 'Nonaktif';
                    if (nameEl) nameEl.textContent = data.bot_enabled ? 'Menunggu autentikasi...' : 'Bot tidak berjalan';
                }

                // Timestamps
                const lastConnEl = document.getElementById('text-last-connected');
                const lastDiscEl = document.getElementById('text-last-disconnected');
                if (lastConnEl) lastConnEl.textContent = data.last_connected_at || '-';
                if (lastDiscEl) lastDiscEl.textContent = data.last_disconnected_at || '-';

                // Last Error
                const errWrapper = document.getElementById('wrapper-last-error');
                const errEl = document.getElementById('text-last-error');
                if (data.last_error && errWrapper && errEl) {
                    errEl.textContent = data.last_error;
                    errWrapper.classList.remove('hidden');
                } else if (errWrapper) {
                    errWrapper.classList.add('hidden');
                }

                // Panels visibility
                const panelQr = document.getElementById('panel-qr-code');
                const panelConn = document.getElementById('panel-connected');
                const panelInactive = document.getElementById('panel-inactive');
                const panelConnecting = document.getElementById('panel-connecting');
                const qrImg = document.getElementById('qr-code-img');

                if (panelQr) panelQr.classList.add('hidden');
                if (panelConn) panelConn.classList.add('hidden');
                if (panelInactive) panelInactive.classList.add('hidden');
                if (panelConnecting) panelConnecting.classList.add('hidden');

                if ((statusKey === 'menunggu_qr' || data.qr_code) && !data.is_connected && data.qr_code) {
                    if (qrImg) qrImg.src = data.qr_code;
                    if (panelQr) panelQr.classList.remove('hidden');
                    if (panelQr) panelQr.classList.add('flex');
                } else if (statusKey === 'terhubung' || data.is_connected) {
                    const connPhoneDisp = document.getElementById('connected-phone-display');
                    const connNameDisp = document.getElementById('connected-name-display');
                    if (connPhoneDisp) connPhoneDisp.textContent = '+' + (data.connected_number || '');
                    if (connNameDisp) connNameDisp.textContent = data.connected_name || 'Eskasaba Bot';
                    if (panelConn) panelConn.classList.remove('hidden');
                    if (panelConn) panelConn.classList.add('flex');
                } else if (statusKey === 'nonaktif' || (!data.bot_enabled && !data.setting_enabled)) {
                    if (panelInactive) panelInactive.classList.remove('hidden');
                    if (panelInactive) panelInactive.classList.add('flex');
                } else {
                    if (panelConnecting) panelConnecting.classList.remove('hidden');
                    if (panelConnecting) panelConnecting.classList.add('flex');
                }

                // Action Buttons State
                const btnStart = document.getElementById('btn-start-bot');
                const btnStop = document.getElementById('btn-stop-bot');
                const btnDisconnect = document.getElementById('btn-disconnect-bot');
                const btnRefreshQr = document.getElementById('btn-refresh-qr');

                function toggleBtnVisibility(el, visible) {
                    if (!el) return;
                    if (visible) {
                        el.classList.remove('hidden');
                        el.classList.add('inline-flex');
                    } else {
                        el.classList.add('hidden');
                        el.classList.remove('inline-flex');
                    }
                }

                toggleBtnVisibility(btnStart, (statusKey === 'nonaktif' || !data.bot_enabled));
                toggleBtnVisibility(btnStop, (statusKey !== 'nonaktif' && data.bot_enabled));
                toggleBtnVisibility(btnDisconnect, (data.is_connected || statusKey === 'terhubung'));
                toggleBtnVisibility(btnRefreshQr, (data.bot_enabled && !data.is_connected));
            }

            window.executeAction = async function (actionType, btnElement, loadingText) {
                if (isExecuting) return;
                isExecuting = true;

                const allActionBtns = document.querySelectorAll('#btn-start-bot, #btn-stop-bot, #btn-disconnect-bot, #btn-refresh-qr, #btn-reset-session-trigger');
                allActionBtns.forEach(b => b.disabled = true);

                const originalHTML = btnElement.innerHTML;
                btnElement.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-1"></i> ${loadingText}`;

                try {
                    const response = await fetch(ACTIONS[actionType], {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const result = await response.json();
                    if (result.data) {
                        renderUI(result.data);
                    }
                    setTimeout(fetchBotStatus, 1000);
                } catch (err) {
                    alert('Gagal memproses aksi: ' + err.message);
                } finally {
                    btnElement.innerHTML = originalHTML;
                    allActionBtns.forEach(b => b.disabled = false);
                    isExecuting = false;
                }
            };

            // Modal Handlers
            window.openResetModal = function () {
                const modal = document.getElementById('modal-reset-session');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            };

            window.closeResetModal = function () {
                const modal = document.getElementById('modal-reset-session');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            };

            window.confirmResetSession = async function (btnElement) {
                closeResetModal();
                const triggerBtn = document.getElementById('btn-reset-session-trigger');
                if (triggerBtn) {
                    await executeAction('reset', triggerBtn, 'Mereset...');
                }
            };

            // Sync initial state instantly on load
            renderUI(INITIAL_STATUS);

            // Polling Interval (every 3 seconds)
            setInterval(fetchBotStatus, 3000);
        })();
    </script>

</x-layouts.admin>

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
        'membuat_qr' => [
            'badge' => 'MEMBUAT QR CODE...',
            'bgClass' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/80 dark:text-purple-300',
            'dotClass' => 'bg-purple-500',
            'pulseClass' => 'bg-purple-400',
            'desc' => 'Sedang memuat & menghasilkan QR Code pairing dari WhatsApp server...'
        ],
        'menunggu_qr' => [
            'badge' => 'MENUNGGU QR SCAN',
            'bgClass' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300',
            'dotClass' => 'bg-amber-500',
            'pulseClass' => 'bg-amber-400',
            'desc' => 'Silakan pindai QR Code dengan aplikasi WhatsApp di HP.'
        ],
        'menghubungkan' => [
            'badge' => 'MEMUAT...',
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

    $showQrPanel = (in_array($statusKey, ['menunggu_qr', 'membuat_qr']) || (!empty($qrCode) && !$isConnected));
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
        data-recipient-count-url="{{ route('admin.whatsapp.recipient-count', [], false) }}"
        data-csrf="{{ csrf_token() }}"
        data-initial='@json($status)'
    >

        {{-- Page Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 flex items-center gap-1.5">
                    <i class="fa-brands fa-whatsapp text-sm"></i> WhatsApp Bot Gateway & Broadcast System
                </p>
                <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 dark:text-white sm:text-3xl flex items-center gap-2">
                    Pengelolaan WhatsApp Bot & Broadcast
                </h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Kontrol service Baileys, pantau status koneksi, dan kirim pesan kustom bertahap ke Guru & Siswa.
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
                        <i class="fa-solid fa-plus"></i>{{ $connectedNumber }}
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
                    <div class="flex items-center justify-between pt-1 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400 font-medium">Sumber Pemutus:</span>
                        <span id="badge-disconnect-source" class="font-bold text-[11px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                            {{ $status['last_disconnect_source'] ?? 'Belum ada record' }}
                        </span>
                    </div>
                </div>
                <div id="wrapper-last-error" class="mt-2.5 {{ $lastError ? '' : 'hidden' }} rounded-xl bg-amber-50 p-2.5 text-[11px] text-amber-800 dark:bg-amber-950/40 dark:text-amber-300 font-medium break-words">
                    <i class="fa-solid fa-circle-info mr-1"></i> <span id="text-last-error">{{ $lastError ?? '-' }}</span>
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
                    WhatsApp Gateway aktif & siap mengirim notifikasi transaksi otomatis serta pesan broadcast custom.
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
                        Memuat ...
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
                        class="{{ ($statusKey === 'nonaktif' || !$isBotEnabled) ? 'inline-flex' : 'hidden' }} items-center gap-2 rounded-2xl bg-emerald-800 px-5 py-3 text-xs font-bold text-white shadow-xs transition-colors duration-150 hover:bg-emerald-900 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <i class="fa-solid fa-play"></i>
                        <span>Aktifkan Bot</span>
                    </button>

                    {{-- Tombol Minta / Generasi QR Code Baru --}}
                    <button
                        type="button"
                        id="btn-refresh-qr"
                        onclick="executeAction('start', this, 'Menyiapkan QR...')"
                        class="{{ ($isBotEnabled && !$isConnected && ($statusKey === 'menunggu_qr' || !empty($qrCode))) ? 'inline-flex' : 'hidden' }} items-center gap-2 rounded-2xl bg-amber-600 px-5 py-3 text-xs font-bold text-white shadow-xs transition-colors duration-150 hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <i class="fa-solid fa-qrcode"></i>
                        <span>Minta QR Code Baru</span>
                    </button>

                    {{-- Tombol Menonaktifkan Bot --}}
                    <button
                        type="button"
                        id="btn-stop-bot"
                        onclick="executeAction('stop', this, 'Menonaktifkan...')"
                        class="{{ ($statusKey !== 'nonaktif' && $isBotEnabled) ? 'inline-flex' : 'hidden' }} items-center gap-2 rounded-2xl bg-slate-800 px-5 py-3 text-xs font-bold text-white shadow-xs transition-colors duration-150 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <i class="fa-solid fa-stop"></i>
                        <span>Nonaktifkan Bot</span>
                    </button>

                    {{-- Tombol Memutuskan Koneksi WA --}}
                    <button
                        type="button"
                        id="btn-disconnect-bot"
                        onclick="executeAction('disconnect', this, 'Memutuskan...')"
                        class="{{ ($isConnected || $statusKey === 'terhubung') ? 'inline-flex' : 'hidden' }} items-center gap-2 rounded-2xl bg-orange-600 px-5 py-3 text-xs font-bold text-white shadow-xs transition-colors duration-150 hover:bg-orange-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Memutuskan Koneksi</span>
                    </button>

                    {{-- Tombol Reset Session (Buka Modal) --}}
                    <button
                        type="button"
                        id="btn-reset-session-trigger"
                        onclick="openResetModal()"
                        class="inline-flex items-center gap-2 rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-xs font-bold text-red-700 transition-colors duration-150 hover:bg-red-100 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-900/60 shadow-xs cursor-pointer ml-auto"
                    >
                        <i class="fa-solid fa-trash-can"></i>
                        <span>Reset Session</span>
                    </button>

                </div>
            </div>

        </div>

        {{-- ================================================================= --}}
        {{-- SECTION PESAN BROADCAST CUSTOM (ANTI-BAN QUEUE) --}}
        {{-- ================================================================= --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 md:p-8 shadow-xs dark:border-slate-800 dark:bg-slate-900 space-y-6">

            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between border-b border-slate-100 pb-5 dark:border-slate-800">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-black uppercase tracking-wider text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 mb-1">
                        <i class="fa-solid fa-paper-plane text-xs"></i> Fitur Pesan Custom
                    </span>
                    <h2 class="text-xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                        Kirim Pesan Broadcast Kustom
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Kirim pesan WhatsApp otomatis ke target pengguna (Guru, Siswa Kelas 10/11/12, atau Semua) secara bertahap dengan fitur <strong>Anti-Ban Rate Limiting</strong>.
                    </p>
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <div class="rounded-2xl bg-amber-50 border border-amber-200 px-3.5 py-2 text-amber-800 dark:bg-amber-950/40 dark:border-amber-900/50 dark:text-amber-300 font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-amber-600"></i>
                        <span>Proteksi Anti-Ban Active</span>
                    </div>
                </div>
            </div>

            {{-- Connection Status Banner for Broadcast --}}
            <div id="wrapper-broadcast-conn-status">
                <div id="broadcast-conn-warning-banner" class="{{ $isConnected ? 'hidden' : 'flex' }} rounded-2xl border border-amber-300 bg-amber-50 p-4 text-xs font-semibold text-amber-900 dark:border-amber-900/50 dark:bg-amber-950/40 dark:text-amber-300 flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300">
                            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                        </div>
                        <div>
                            <p class="font-black text-sm text-amber-900 dark:text-amber-200">Bot WhatsApp Belum Terhubung</p>
                            <p class="text-xs text-amber-700 dark:text-amber-300 font-medium">
                                Bot WhatsApp saat ini nonaktif atau belum ditautkan (QR Code belum di-scan). Pindai QR Code di atas terlebih dahulu agar pesan broadcast dapat terkirim.
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        onclick="document.getElementById('whatsapp-admin-config').scrollIntoView({ behavior: 'smooth' })"
                        class="shrink-0 rounded-xl bg-amber-600 px-3.5 py-2 text-xs font-bold text-white hover:bg-amber-700 transition cursor-pointer shadow-xs"
                    >
                        <i class="fa-solid fa-qrcode mr-1"></i> Scan QR Code
                    </button>
                </div>

                <div id="broadcast-conn-success-banner" class="{{ $isConnected ? 'flex' : 'hidden' }} rounded-2xl border border-emerald-200 bg-emerald-50/80 p-3.5 text-xs font-semibold text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300 items-center gap-3 shadow-xs">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300">
                        <i class="fa-solid fa-circle-check text-base"></i>
                    </div>
                    <div>
                        <span class="font-black">WhatsApp Bot Terhubung</span> (<span id="banner-connected-phone">+{{ $connectedNumber ?? '' }}</span>) — Siap mengirim pesan broadcast kustom.
                    </div>
                </div>
            </div>

            {{-- Form Kirim Broadcast --}}
            <form action="{{ route('admin.whatsapp.broadcast.send') }}" method="POST" id="form-send-broadcast" class="space-y-6">
                @csrf

                <div class="grid gap-6 md:grid-cols-2">

                    {{-- Kolom Kiri: Target & Pengaturan --}}
                    <div class="space-y-5">

                        {{-- Judul Broadcast --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Judul Campaign Broadcast <span class="text-slate-400 font-normal">(Opsional)</span>
                            </label>
                            <input
                                type="text"
                                name="title"
                                placeholder="Contoh: Pengumuman Ujian Semester / Informasi Kegiatan Sekolah"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-xs font-medium text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-950/50 dark:text-white dark:focus:border-emerald-500"
                            >
                        </div>

                        {{-- Target Penerima --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Target Penerima Pesan <span class="text-red-500">*</span>
                                </label>
                                <span id="badge-recipient-count" class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 dark:bg-emerald-950/60 dark:border-emerald-900/50 dark:text-emerald-300">
                                    <i class="fa-solid fa-user-check text-[10px]"></i> <span id="count-number">{{ $recipientStats['all'] }}</span> WA Terisi
                                </span>
                            </div>

                            <x-custom-select
                                name="target_type"
                                id="select-target-type"
                                icon="fa-solid fa-users text-emerald-600"
                                :options="[
                                    'all'        => 'Semua Users (Guru & Siswa) — ' . $recipientStats['all'] . ' No. WA',
                                    'teacher'    => 'Dewan Guru & Staf — ' . $recipientStats['teacher'] . ' No. WA',
                                    'student_10' => 'Siswa Kelas 10 (X) — ' . $recipientStats['student_10'] . ' No. WA',
                                    'student_11' => 'Siswa Kelas 11 (XI) — ' . $recipientStats['student_11'] . ' No. WA',
                                    'student_12' => 'Siswa Kelas 12 (XII) — ' . $recipientStats['student_12'] . ' No. WA',
                                ]"
                                selected="all"
                                placeholder=""
                            />

                            <p class="mt-1.5 text-[11px] text-slate-400">
                                *Hanya mengirim ke akun pengguna yang pernah login dan mengisi nomor WhatsApp.
                            </p>
                        </div>

                        {{-- Jeda Pengiriman / Rate Limit (Anti-Ban) --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Jeda Pengiriman per Pesan (Anti-Ban Rate Limit) <span class="text-red-500">*</span>
                            </label>

                            <x-custom-select
                                name="delay_seconds"
                                id="select-delay-seconds"
                                icon="fa-solid fa-clock text-amber-500"
                                :options="[
                                    '2'  => '2 Detik per pesan (Cepat)',
                                    '3'  => '3 Detik per pesan (Direkomendasikan — Safe Anti-Ban)',
                                    '5'  => '5 Detik per pesan (Ekstra Aman)',
                                    '10' => '10 Detik per pesan (Sangat Aman / Pesan Banyak)',
                                ]"
                                selected="3"
                                placeholder=""
                            />

                            <p class="mt-1.5 text-[11px] text-slate-400">
                                Pemberian jeda bertahap mencegah nomor WA terdeteksi sebagai spammer oleh sistem WhatsApp.
                            </p>
                        </div>

                    </div>

                    {{-- Kolom Kanan: Editor Pesan & Placeholder --}}
                    <div class="space-y-4">

                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                    Isi Pesan Custom WhatsApp <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-1.5 text-[11px]">
                                    <span class="text-slate-400">Variabel:</span>
                                    <button type="button" onclick="insertPlaceholder('{name}')" class="rounded-lg bg-slate-100 px-2 py-0.5 text-xs font-mono font-bold text-slate-700 hover:bg-emerald-100 hover:text-emerald-800 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-emerald-950 dark:hover:text-emerald-300 transition cursor-pointer" title="Sisipkan Nama User">{name}</button>
                                    <button type="button" onclick="insertPlaceholder('{kelas}')" class="rounded-lg bg-slate-100 px-2 py-0.5 text-xs font-mono font-bold text-slate-700 hover:bg-emerald-100 hover:text-emerald-800 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-emerald-950 dark:hover:text-emerald-300 transition cursor-pointer" title="Sisipkan Kelas User">{kelas}</button>
                                </div>
                            </div>

                            <textarea
                                name="message"
                                id="broadcast-message-textarea"
                                rows="6"
                                placeholder="Tulis pesan custom WhatsApp di sini...
Contoh:
Halo {name},
Diberitahukan kepada seluruh siswa kelas {kelas}, bahwa ada update terbaru di Eskasaba Marketplace.

Terima kasih,
_Admin Eskasaba Marketplace_"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50/50 p-4 text-xs font-medium text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none dark:border-slate-800 dark:bg-slate-950/50 dark:text-white dark:focus:border-emerald-500 leading-relaxed"
                                oninput="updateMessagePreview(this.value)"
                                required
                            ></textarea>
                        </div>

                        {{-- Preview Chat Bubble WhatsApp --}}
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4 dark:border-emerald-900/40 dark:bg-emerald-950/30">
                            <p class="text-[11px] font-bold text-emerald-800 dark:text-emerald-300 mb-2 flex items-center gap-1.5">
                                <i class="fa-brands fa-whatsapp text-emerald-600"></i> Preview Chat Bubble WhatsApp:
                            </p>
                            <div class="rounded-2xl bg-white p-3.5 shadow-xs border border-emerald-100 dark:bg-slate-800 dark:border-slate-700 max-w-sm">
                                <p id="whatsapp-preview-text" class="text-xs text-slate-800 dark:text-slate-200 whitespace-pre-wrap font-sans leading-relaxed italic text-slate-400">
                                    Pesan preview akan muncul di sini saat Anda mengetik...
                                </p>
                                <div class="mt-2 text-[10px] text-slate-400 text-right flex items-center justify-end gap-1">
                                    <span>08:00</span>
                                    <i class="fa-solid fa-check-double text-sky-500"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                {{-- Action Submit --}}
                <div class="flex items-center justify-end border-t border-slate-100 pt-5 dark:border-slate-800 gap-3">
                    <button
                        type="button"
                        onclick="openBroadcastModal()"
                        class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-6 py-3.5 text-xs font-black text-white shadow-md transition hover:bg-emerald-700 cursor-pointer"
                    >
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Kirim Broadcast Custom</span>
                    </button>
                </div>
            </form>

        </div>

        {{-- ================================================================= --}}
        {{-- SECTION ACTIVE BROADCAST REALTIME PROGRESS MONITOR --}}
        {{-- ================================================================= --}}
        @if($activeBroadcast)
            <div id="active-broadcast-card" data-broadcast-id="{{ $activeBroadcast->id }}" data-status-url="{{ route('admin.whatsapp.broadcast.status', $activeBroadcast->id) }}" class="rounded-3xl border border-emerald-200 bg-emerald-50/70 p-6 shadow-xs dark:border-emerald-900/50 dark:bg-emerald-950/30 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-xs shrink-0">
                            <i class="fa-solid fa-paper-plane text-xl animate-pulse"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-200 px-2.5 py-0.5 text-[10px] font-black uppercase text-emerald-900 dark:bg-emerald-900 dark:text-emerald-200">
                                    <i class="fa-solid fa-circle-notch fa-spin"></i> BROADCAST BERJALAN
                                </span>
                                <span class="text-xs font-bold text-slate-500">{{ $activeBroadcast->id }}</span>
                            </div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white mt-1">
                                {{ $activeBroadcast->title }}
                            </h3>
                        </div>
                    </div>

                    <form action="{{ route('admin.whatsapp.broadcast.cancel', $activeBroadcast->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengiriman broadcast ini?');">
                        @csrf
                        <button
                            type="submit"
                            class="rounded-xl border border-red-200 bg-white px-4 py-2.5 text-xs font-bold text-red-600 hover:bg-red-50 dark:bg-slate-900 dark:border-red-900/50 dark:text-red-400 transition cursor-pointer shadow-xs"
                        >
                            <i class="fa-solid fa-stop mr-1"></i> Batalkan Broadcast
                        </button>
                    </form>
                </div>

                {{-- Progress Bar --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300">
                        <span>Progress Pengiriman: <span id="active-sent-count">{{ $activeBroadcast->sent_count }}</span> / <span id="active-total-count">{{ $activeBroadcast->total_recipients }}</span> User</span>
                        <span id="active-percent-text" class="text-emerald-700 dark:text-emerald-400">{{ $activeBroadcast->progress_percentage }}%</span>
                    </div>
                    <div class="h-3.5 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-800">
                        <div id="active-progress-bar" class="h-full bg-emerald-500 transition-all duration-300" style="width: 0%;" :style="{ width: '{{ $activeBroadcast->progress_percentage }}%' }"></div>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-3 gap-3 text-center text-xs font-bold pt-1">
                    <div class="rounded-2xl bg-white p-3 dark:bg-slate-900 shadow-xs border border-emerald-100 dark:border-slate-800">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Target Group</span>
                        <span class="text-emerald-700 dark:text-emerald-400 truncate block mt-0.5">{{ $activeBroadcast->target_label }}</span>
                    </div>
                    <div class="rounded-2xl bg-white p-3 dark:bg-slate-900 shadow-xs border border-emerald-100 dark:border-slate-800">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Terkirim (Berhasil)</span>
                        <span id="stat-sent-count" class="text-emerald-600 dark:text-emerald-400 text-sm mt-0.5 block">{{ $activeBroadcast->sent_count }}</span>
                    </div>
                    <div class="rounded-2xl bg-white p-3 dark:bg-slate-900 shadow-xs border border-emerald-100 dark:border-slate-800">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Gagal</span>
                        <span id="stat-failed-count" class="text-red-500 text-sm mt-0.5 block">{{ $activeBroadcast->failed_count }}</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- ================================================================= --}}
        {{-- SECTION RIWAYAT CAMPAIGN BROADCAST --}}
        {{-- ================================================================= --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-emerald-600"></i> Riwayat Campaign Broadcast WA
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Daftar campaign pesan custom yang telah atau sedang dikirimkan.
                    </p>
                </div>
                <span class="text-[11px] font-bold text-slate-400">{{ count($broadcasts) }} Record Terbaru</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:bg-slate-800/60">
                        <tr>
                            <th class="px-4 py-3 rounded-l-xl">ID / Campaign</th>
                            <th class="px-4 py-3">Target</th>
                            <th class="px-4 py-3 text-center">Terkirim / Total</th>
                            <th class="px-4 py-3 text-center">Jeda</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 rounded-r-xl text-right">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                        @forelse($broadcasts as $bc)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="px-4 py-3.5">
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        #{{ $bc->id }} - {{ $bc->title ?: 'Broadcast Pesan Custom' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 truncate max-w-xs mt-0.5">
                                        "{{ Str::limit($bc->message, 50) }}"
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        {{ $bc->target_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center font-bold">
                                    <span class="text-emerald-600">{{ $bc->sent_count }}</span> / <span class="text-slate-800 dark:text-white">{{ $bc->total_recipients }}</span>
                                    @if($bc->failed_count > 0)
                                        <span class="text-[10px] text-red-500 font-normal ml-1">({{ $bc->failed_count }} gagal)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="text-slate-500 font-mono">{{ $bc->delay_seconds }}s</span>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($bc->status === 'completed')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-black uppercase text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                                            <i class="fa-solid fa-check text-[9px]"></i> Selesai
                                        </span>
                                    @elseif($bc->status === 'processing')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-black uppercase text-blue-800 dark:bg-blue-950/80 dark:text-blue-300">
                                            <i class="fa-solid fa-spinner fa-spin text-[9px]"></i> Berjalan
                                        </span>
                                    @elseif($bc->status === 'pending')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-black uppercase text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">
                                            Pending
                                        </span>
                                    @elseif($bc->status === 'cancelled')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-black uppercase text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                            Dibatalkan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2.5 py-0.5 text-[10px] font-black uppercase text-red-800 dark:bg-red-950/80 dark:text-red-300">
                                            Gagal
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right text-[11px] text-slate-400">
                                    {{ $bc->created_at?->format('d M Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-400 italic">
                                    Belum ada riwayat campaign broadcast tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- SECTION DIAGNOSA KONEKSI TERPUTUS --}}
        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-emerald-600"></i> Riwayat Diagnosa Disconnect
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Memantau apakah koneksi terputus dari pihak WhatsApp (HP/Logout), Admin Panel, atau Gangguan Jaringan.
                    </p>
                </div>
                <span class="text-[11px] font-bold text-slate-400">5 Record Terakhir</span>
            </div>

            <div id="disconnect-log-list" class="space-y-2 text-xs">
                <p class="text-slate-400 italic text-center py-2">Belum ada riwayat disconnect tercatat.</p>
            </div>
        </div>

    </div>

    {{-- MODAL KONFIRMASI RESET SESSION --}}
    <div id="modal-reset-session" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
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

    {{-- MODAL KONFIRMASI BROADCAST SUBMIT --}}
    <div id="modal-confirm-broadcast" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-4">
            <div class="flex items-center gap-3 text-emerald-600 dark:text-emerald-400">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-emerald-100 dark:bg-emerald-950/80">
                    <i class="fa-solid fa-paper-plane text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white">Konfirmasi Pengiriman Broadcast</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pesan akan dikirim secara bertahap</p>
                </div>
            </div>

            <div class="text-xs text-slate-600 dark:text-slate-300 space-y-2 bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-2xl border border-slate-100 dark:border-slate-800">
                <div class="flex justify-between">
                    <span class="text-slate-400">Target Penerima:</span>
                    <span id="modal-target-label" class="font-bold text-emerald-700 dark:text-emerald-400">Semua Users</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Total User Terisi WA:</span>
                    <span id="modal-recipient-count" class="font-bold">0 User</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Proteksi Anti-Ban Delay:</span>
                    <span id="modal-delay-seconds" class="font-bold">3 Detik / Pesan</span>
                </div>
            </div>

            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Pengiriman akan berjalan di background server tanpa mengganggu koneksi Anda. Anda dapat memantau statusnya di halaman ini.
            </p>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button
                    type="button"
                    onclick="closeBroadcastModal()"
                    class="rounded-xl px-4 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 transition cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="button"
                    onclick="submitBroadcastForm()"
                    class="rounded-xl bg-emerald-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-emerald-700 shadow-md transition cursor-pointer"
                >
                    Ya, Kirim Sekarang
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL WARNING PERINGATAN (Subtitusi alert bawaan browser) --}}
    <div id="modal-validation-warning" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
        <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-4">
            <div class="flex items-center gap-3 text-amber-600 dark:text-amber-400">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-100 dark:bg-amber-950/80">
                    <i class="fa-solid fa-circle-exclamation text-xl"></i>
                </div>
                <div>
                    <h3 id="modal-warning-title" class="text-base font-black text-slate-900 dark:text-white">Perhatian</h3>
                    <p id="modal-warning-subtitle" class="text-xs text-slate-500 dark:text-slate-400">Peringatan Pengiriman Broadcast</p>
                </div>
            </div>

            <p id="modal-warning-message" class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed bg-amber-50/70 dark:bg-amber-950/30 p-3.5 rounded-2xl border border-amber-100 dark:border-amber-900/40">
                Mohon isi pesan kustom WhatsApp terlebih dahulu sebelum membuat campaign broadcast.
            </p>

            <div class="flex items-center justify-end pt-2">
                <button
                    type="button"
                    onclick="closeWarningModal()"
                    class="rounded-xl bg-slate-900 px-5 py-2.5 text-xs font-bold text-white hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 shadow-xs transition cursor-pointer"
                >
                    Mengerti
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
            const RECIPIENT_COUNT_URL = configEl.dataset.recipientCountUrl;
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
                    badge: 'MEMUAT...',
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

            let isFetchingStatus = false;

            window.fetchBotStatus = async function () {
                if (isFetchingStatus || document.hidden) return;
                isFetchingStatus = true;

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
                } finally {
                    isFetchingStatus = false;
                    if (syncPing) {
                        setTimeout(() => syncPing.classList.remove('opacity-100'), 500);
                    }
                }
            };

            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) {
                    fetchBotStatus();
                }
            });

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

                // Track WhatsApp Connection State for Broadcast Form Validation
                window.isWhatsAppConnected = !!(data.is_connected && data.connected_number);

                const broadcastWarnBanner = document.getElementById('broadcast-conn-warning-banner');
                const broadcastSuccBanner = document.getElementById('broadcast-conn-success-banner');
                const bannerPhone = document.getElementById('banner-connected-phone');

                if (window.isWhatsAppConnected) {
                    if (broadcastWarnBanner) {
                        broadcastWarnBanner.classList.add('hidden');
                        broadcastWarnBanner.classList.remove('flex');
                    }
                    if (broadcastSuccBanner) {
                        broadcastSuccBanner.classList.remove('hidden');
                        broadcastSuccBanner.classList.add('flex');
                    }
                    if (bannerPhone) bannerPhone.textContent = '+' + data.connected_number;
                } else {
                    if (broadcastSuccBanner) {
                        broadcastSuccBanner.classList.add('hidden');
                        broadcastSuccBanner.classList.remove('flex');
                    }
                    if (broadcastWarnBanner) {
                        broadcastWarnBanner.classList.remove('hidden');
                        broadcastWarnBanner.classList.add('flex');
                    }
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

                // Disconnect Source Badge & History Mapping
                const SOURCE_MAP = {
                    'WHATSAPP_APP': { label: 'Di-logout dari HP WhatsApp', class: 'bg-red-100 text-red-800 dark:bg-red-950/80 dark:text-red-300' },
                    'ADMIN_PANEL': { label: 'Diputuskan oleh Admin', class: 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300' },
                    'NETWORK_TEMPORARY': { label: 'Gangguan Jaringan (Auto-Reconnect)', class: 'bg-blue-100 text-blue-800 dark:bg-blue-950/80 dark:text-blue-300' },
                    'SESSION_CONFLICT': { label: 'Konflik Sesi Perangkat Lain', class: 'bg-red-100 text-red-800 dark:bg-red-950/80 dark:text-red-300' },
                    'QR_TIMEOUT': { label: 'Waktu Scan QR Expired', class: 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300' },
                    'BAILEYS_RESTART': { label: 'Sinkronisasi Baileys (Status 515)', class: 'bg-sky-100 text-sky-800 dark:bg-sky-950/80 dark:text-sky-300' }
                };

                const badgeSource = document.getElementById('badge-disconnect-source');
                if (badgeSource) {
                    const srcObj = SOURCE_MAP[data.last_disconnect_source] || { label: data.last_disconnect_source || 'Belum ada record', class: 'bg-slate-100 text-slate-700' };
                    badgeSource.textContent = srcObj.label;
                    badgeSource.className = `font-bold text-[11px] px-2.5 py-0.5 rounded-full ${srcObj.class}`;
                }

                // Render Disconnect History List
                const logListContainer = document.getElementById('disconnect-log-list');
                if (logListContainer && data.disconnect_logs && data.disconnect_logs.length > 0) {
                    logListContainer.innerHTML = data.disconnect_logs.map(log => {
                        const srcObj = SOURCE_MAP[log.source] || { label: log.source || 'N/A', class: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300' };
                        return `
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 gap-2">
                                <div class="flex items-center gap-2 overflow-hidden">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold shrink-0 ${srcObj.class}">${srcObj.label}</span>
                                    <span class="font-medium text-slate-700 dark:text-slate-300 truncate">${log.reason}</span>
                                </div>
                                <span class="text-[11px] text-slate-400 shrink-0 font-semibold">${log.time}</span>
                            </div>
                        `;
                    }).join('');
                } else if (logListContainer) {
                    logListContainer.innerHTML = `<p class="text-slate-400 italic text-center py-2">Belum ada riwayat disconnect tercatat.</p>`;
                }

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
                toggleBtnVisibility(btnRefreshQr, (data.bot_enabled && !data.is_connected && (statusKey === 'menunggu_qr' || !!data.qr_code)));
            }

            window.executeAction = async function (actionType, btnElement, loadingText) {
                if (isExecuting) return;
                isExecuting = true;

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

                if (actionType === 'start') {
                    toggleBtnVisibility(btnStart, false);
                    toggleBtnVisibility(btnStop, true);
                } else if (actionType === 'stop') {
                    toggleBtnVisibility(btnStop, false);
                    toggleBtnVisibility(btnStart, true);
                    toggleBtnVisibility(btnDisconnect, false);
                    toggleBtnVisibility(btnRefreshQr, false);
                }

                const allActionBtns = document.querySelectorAll('#btn-start-bot, #btn-stop-bot, #btn-disconnect-bot, #btn-refresh-qr, #btn-reset-session-trigger');
                allActionBtns.forEach(b => b.disabled = true);

                const targetActiveBtn = (actionType === 'start') ? btnStop : (actionType === 'stop' ? btnStart : btnElement);
                const originalHTML = targetActiveBtn.innerHTML;
                targetActiveBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin mr-1"></i> ${loadingText}`;

                let responseData = null;

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
                    if (response.ok) {
                        const result = await response.json();
                        if (result.data) {
                            responseData = result.data;
                        }
                    }
                } catch (err) {
                    console.warn('Execute action error:', err);
                } finally {
                    targetActiveBtn.innerHTML = originalHTML;
                    allActionBtns.forEach(b => b.disabled = false);
                    isExecuting = false;
                    if (responseData) {
                        renderUI(responseData);
                    } else {
                        await fetchBotStatus();
                    }
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

            // Dynamic Recipient Counter Update
            window.updateRecipientCount = async function (targetValue) {
                const countNumEl = document.getElementById('count-number');
                if (!countNumEl) return;

                try {
                    const url = `${RECIPIENT_COUNT_URL}?target=${targetValue}`;
                    const res = await fetch(url);
                    const data = await res.json();
                    if (data && typeof data.count !== 'undefined') {
                        countNumEl.textContent = data.count;
                    }
                } catch (e) {
                    console.warn('Failed to update recipient count:', e);
                }
            };

            // Textarea Placeholder Helper
            window.insertPlaceholder = function (tag) {
                const textarea = document.getElementById('broadcast-message-textarea');
                if (!textarea) return;

                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const text = textarea.value;

                textarea.value = text.substring(0, start) + tag + text.substring(end);
                textarea.selectionStart = textarea.selectionEnd = start + tag.length;
                textarea.focus();

                updateMessagePreview(textarea.value);
            };

            // WhatsApp Chat Bubble Live Preview Generator
            window.updateMessagePreview = function (text) {
                const previewEl = document.getElementById('whatsapp-preview-text');
                if (!previewEl) return;

                if (!text || text.trim() === '') {
                    previewEl.innerHTML = '<span class="italic text-slate-400">Pesan preview akan muncul di sini saat Anda mengetik...</span>';
                    return;
                }

                // Formatter sederhana untuk WhatsApp (*bold*, _italic_, ~strike~)
                let formatted = text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/\*(.*?)\*/g, "<strong>$1</strong>")
                    .replace(/_(.*?)_/g, "<em>$1</em>")
                    .replace(/~(.*?)~/g, "<del>$1</del>")
                    .replace(/\{name\}/g, '<span class="font-bold text-emerald-600 dark:text-emerald-400">[Nama User]</span>')
                    .replace(/\{kelas\}/g, '<span class="font-bold text-emerald-600 dark:text-emerald-400">[Kelas/Grup]</span>');

                previewEl.innerHTML = formatted;
            };

            const TARGET_LABELS = {
                'all': 'Semua Users (Guru & Siswa)',
                'teacher': 'Dewan Guru & Staf',
                'student_10': 'Siswa Kelas 10 (X)',
                'student_11': 'Siswa Kelas 11 (XI)',
                'student_12': 'Siswa Kelas 12 (XII)'
            };

            const DELAY_LABELS = {
                '2': '2 Detik per pesan (Cepat)',
                '3': '3 Detik per pesan (Direkomendasikan — Safe Anti-Ban)',
                '5': '5 Detik per pesan (Ekstra Aman)',
                '10': '10 Detik per pesan (Sangat Aman / Pesan Banyak)'
            };

            // Event listener untuk x-custom-select target_type
            document.addEventListener('DOMContentLoaded', function() {
                const targetInput = document.getElementById('select-target-type');
                if (targetInput) {
                    targetInput.addEventListener('change', function(e) {
                        if (typeof window.updateRecipientCount === 'function') {
                            window.updateRecipientCount(e.target.value);
                        }
                    });
                }
            });

            // Custom Warning Modal Handler (Menggantikan alert bawaan browser)
            window.showWarningModal = function(title, message, subtitle = 'Peringatan Pengiriman Broadcast') {
                const titleEl = document.getElementById('modal-warning-title');
                const subtitleEl = document.getElementById('modal-warning-subtitle');
                const messageEl = document.getElementById('modal-warning-message');

                if (titleEl) titleEl.textContent = title;
                if (subtitleEl) subtitleEl.textContent = subtitle;
                if (messageEl) messageEl.textContent = message;

                const modal = document.getElementById('modal-validation-warning');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            };

            window.closeWarningModal = function() {
                const modal = document.getElementById('modal-validation-warning');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            };

            // Broadcast Submit Modal Handlers
            window.openBroadcastModal = function () {
                const form = document.getElementById('form-send-broadcast');
                if (!form) return;

                // 1. Validasi Status Koneksi WA Bot
                if (!window.isWhatsAppConnected) {
                    showWarningModal(
                        'WhatsApp Belum Terhubung',
                        'Bot WhatsApp saat ini belum aktif atau belum terhubung (QR Code belum di-scan). Silakan aktifkan bot dan pindai QR Code terlebih dahulu agar pesan broadcast dapat terkirim.',
                        'Koneksi Bot Dibutuhkan'
                    );
                    return;
                }

                // 2. Validasi Isi Pesan Custom
                const textarea = document.getElementById('broadcast-message-textarea');
                if (!textarea || !textarea.value.trim()) {
                    if (textarea) {
                        textarea.classList.add('border-red-500', 'ring-2', 'ring-red-100');
                        textarea.focus();
                    }
                    showWarningModal(
                        'Isi Pesan Kosong',
                        'Mohon isi pesan custom WhatsApp terlebih dahulu sebelum membuat campaign broadcast.',
                        'Form Tidak Lengkap'
                    );
                    return;
                } else {
                    textarea.classList.remove('border-red-500', 'ring-2', 'ring-red-100');
                }

                const targetVal = document.getElementById('select-target-type')?.value || 'all';
                const delayVal = document.getElementById('select-delay-seconds')?.value || '3';

                const targetText = TARGET_LABELS[targetVal] || targetVal;
                const countText = document.getElementById('count-number')?.textContent || '0';
                const delayText = DELAY_LABELS[delayVal] || `${delayVal} Detik`;

                document.getElementById('modal-target-label').textContent = targetText;
                document.getElementById('modal-recipient-count').textContent = `${countText} User`;
                document.getElementById('modal-delay-seconds').textContent = delayText;

                const modal = document.getElementById('modal-confirm-broadcast');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            };

            window.closeBroadcastModal = function () {
                const modal = document.getElementById('modal-confirm-broadcast');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            };

            window.submitBroadcastForm = function () {
                closeBroadcastModal();
                const form = document.getElementById('form-send-broadcast');
                if (form) {
                    form.submit();
                }
            };

            // Polling Active Broadcast Progress (If active broadcast exists)
            const activeCard = document.getElementById('active-broadcast-card');
            if (activeCard) {
                const broadcastStatusUrl = activeCard.dataset.statusUrl;

                async function pollBroadcastProgress() {
                    try {
                        const res = await fetch(broadcastStatusUrl, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (!res.ok) return;

                        const data = await res.json();
                        if (data) {
                            const sentCount = document.getElementById('active-sent-count');
                            const totalCount = document.getElementById('active-total-count');
                            const percentText = document.getElementById('active-percent-text');
                            const progressBar = document.getElementById('active-progress-bar');
                            const statSent = document.getElementById('stat-sent-count');
                            const statFailed = document.getElementById('stat-failed-count');

                            if (sentCount) sentCount.textContent = data.sent_count;
                            if (totalCount) totalCount.textContent = data.total_recipients;
                            if (percentText) percentText.textContent = `${data.progress_percent}%`;
                            if (progressBar) progressBar.style.width = `${data.progress_percent}%`;
                            if (statSent) statSent.textContent = data.sent_count;
                            if (statFailed) statFailed.textContent = data.failed_count;

                            if (data.status === 'completed' || data.status === 'cancelled' || data.status === 'failed') {
                                setTimeout(() => window.location.reload(), 1500);
                            }
                        }
                    } catch (e) {
                        console.warn('Broadcast status poll error:', e);
                    }
                }

                setInterval(pollBroadcastProgress, 2500);
            }

            // Sync initial state instantly on load
            renderUI(INITIAL_STATUS);

            // Polling Interval (every 3 seconds)
            setInterval(fetchBotStatus, 3000);
        })();
    </script>

</x-layouts.admin>

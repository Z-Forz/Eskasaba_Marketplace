<x-layouts.admin title="Kelola WhatsApp Bot">

    {{-- Meta config data untuk JavaScript polling --}}
    <div id="whatsapp-admin-config" class="hidden"
        data-status-url="{{ route('admin.whatsapp.status') }}"
        data-start-url="{{ route('admin.whatsapp.start') }}"
        data-stop-url="{{ route('admin.whatsapp.stop') }}"
        data-disconnect-url="{{ route('admin.whatsapp.disconnect') }}"
        data-reset-url="{{ route('admin.whatsapp.reset-session') }}"
        data-csrf="{{ csrf_token() }}"
        data-initial="{{ json_encode($status) }}"
    ></div>

    <div class="space-y-8">

        {{-- Header Section --}}
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-emerald-600 transition">Dashboard</a>
                    <i class="fa-solid fa-chevron-right text-[9px]"></i>
                    <span class="text-slate-600 dark:text-slate-300">Pengaturan</span>
                </div>
                <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
                    <i class="fa-brands fa-whatsapp text-emerald-500"></i> Kelola WhatsApp Bot
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Atur status koneksi, pindai QR Code, dan pantau log diagnostik bot WhatsApp.
                </p>
            </div>
        </div>

        {{-- Flash Notification Alerts --}}
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-bold text-emerald-800 dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-300 flex items-center gap-3 shadow-xs">
                <i class="fa-solid fa-circle-check text-base text-emerald-600"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-xs font-bold text-red-800 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300 flex items-center gap-3 shadow-xs">
                <i class="fa-solid fa-circle-exclamation text-base text-red-600"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- ================================================================= --}}
        {{-- SECTION UTAMA: CONTROL PANEL & STATUS REALTIME WHATSAPP BOT --}}
        {{-- ================================================================= --}}
        @php
            $statusKey = $status['status'] ?? 'nonaktif';
            $isConnected = $status['is_connected'] ?? false;
            $botEnabled = $status['bot_enabled'] ?? false;
            $qrCode = $status['qr_code'] ?? null;
            $connectedNumber = $status['connected_number'] ?? null;
            $connectedName = $status['connected_name'] ?? null;
            $lastError = $status['last_error'] ?? null;
        @endphp

        <div class="rounded-3xl border border-slate-200/80 bg-white p-6 md:p-8 shadow-xs dark:border-slate-800 dark:bg-slate-900 space-y-6">

            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between border-b border-slate-100 pb-5 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 dark:bg-emerald-950/60 dark:text-emerald-400 shrink-0">
                        <i class="fa-brands fa-whatsapp text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-black tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                            Status Service Bot WhatsApp
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Status koneksi gateway Baileys Node.js dengan nomor resmi WhatsApp sekolah.
                        </p>
                    </div>
                </div>

                {{-- Status Badge Realtime --}}
                <div id="status-badge-container" class="flex items-center gap-2">
                    <span id="bot-status-badge" class="inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-xs font-black uppercase tracking-wider transition-all duration-300 {{ $isConnected ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300' : ($botEnabled ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300') }}">
                        <span id="bot-status-dot-pulse" class="relative flex h-2.5 w-2.5">
                            <span id="bot-status-pulse" class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $isConnected ? 'bg-emerald-400' : 'bg-slate-400' }} opacity-75"></span>
                            <span id="bot-status-dot" class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $isConnected ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                        </span>
                        <span id="bot-status-text">{{ $isConnected ? 'TERHUBUNG' : ($botEnabled ? 'MENUNGGU QR SCAN' : 'NONAKTIF') }}</span>
                    </span>
                </div>
            </div>

            {{-- Dynamic Content Area --}}
            <div class="grid gap-6 md:grid-cols-12 items-center">

                {{-- Kolom Kiri: Visual Status / QR Code Card --}}
                <div class="md:col-span-5 flex justify-center">

                    {{-- Card QR Code --}}
                    <div id="panel-qr-code" class="{{ ($qrCode && !$isConnected) ? 'flex' : 'hidden' }} flex-col items-center justify-center rounded-3xl border border-amber-200 bg-amber-50/50 p-6 text-center dark:border-amber-900/40 dark:bg-amber-950/30 w-full max-w-sm space-y-4">
                        <div class="relative rounded-2xl bg-white p-3 shadow-md dark:bg-slate-800 border border-amber-100 dark:border-slate-700">
                            <img id="qr-code-img" src="{{ $qrCode ?? '' }}" alt="QR Code WhatsApp" class="h-48 w-48 object-contain rounded-xl">
                        </div>
                        <div>
                            <p class="text-xs font-black text-amber-900 dark:text-amber-200">Pindai QR Code Ini</p>
                            <p class="text-[11px] text-amber-700 dark:text-amber-300 font-medium mt-1 leading-relaxed">
                                Buka WhatsApp di HP Anda > Perangkat Tertaut > Tautkan Perangkat. QR Code akan diperbarui secara otomatis.
                            </p>
                        </div>
                    </div>

                    {{-- Card Terhubung --}}
                    <div id="panel-connected" class="{{ $isConnected ? 'flex' : 'hidden' }} flex-col items-center justify-center rounded-3xl border border-emerald-200 bg-emerald-50/50 p-6 text-center dark:border-emerald-900/40 dark:bg-emerald-950/30 w-full max-w-sm space-y-3">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 dark:bg-emerald-900/60 dark:text-emerald-300 shadow-inner">
                            <i class="fa-solid fa-circle-check text-3xl"></i>
                        </div>
                        <div>
                            <h3 id="connected-name-display" class="text-sm font-black text-slate-900 dark:text-white">
                                {{ $connectedName ?? 'Eskasaba Bot' }}
                            </h3>
                            <p id="connected-phone-display" class="text-xs font-mono font-bold text-emerald-700 dark:text-emerald-400 mt-0.5">
                                +{{ $connectedNumber ?? '' }}
                            </p>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-200 px-3 py-1 text-[10px] font-black uppercase text-emerald-900 dark:bg-emerald-900 dark:text-emerald-200 mt-2">
                                <i class="fa-solid fa-signal text-[9px]"></i> Online & Ready
                            </span>
                        </div>
                    </div>

                    {{-- Card Nonaktif --}}
                    <div id="panel-inactive" class="{{ (!$botEnabled && !$isConnected) ? 'flex' : 'hidden' }} flex-col items-center justify-center rounded-3xl border border-slate-200 bg-slate-50 p-6 text-center dark:border-slate-800 dark:bg-slate-950/50 w-full max-w-sm space-y-3">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                            <i class="fa-solid fa-power-off text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-800 dark:text-slate-200">Bot WhatsApp Nonaktif</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                                Service WhatsApp Baileys tidak berjalan. Klik tombol <strong>Aktifkan Bot</strong> untuk memulai service.
                            </p>
                        </div>
                    </div>

                    {{-- Card Connecting / Loading --}}
                    <div id="panel-connecting" class="{{ ($botEnabled && !$isConnected && !$qrCode) ? 'flex' : 'hidden' }} flex-col items-center justify-center rounded-3xl border border-blue-200 bg-blue-50/50 p-6 text-center dark:border-blue-900/40 dark:bg-blue-950/30 w-full max-w-sm space-y-3">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/60 dark:text-blue-300">
                            <i class="fa-solid fa-circle-notch fa-spin text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white">Memulai Service Bot...</h3>
                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-1 leading-relaxed">
                                Menyiapkan engine Baileys Node.js dan menyiapkan QR Code. Mohon tunggu beberapa detik.
                            </p>
                        </div>
                    </div>

                </div>

                {{-- Kolom Kanan: Detail Informasi & Metadata Koneksi --}}
                <div class="md:col-span-7 space-y-4">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-950/40 space-y-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nomor WhatsApp Terhubung</span>
                            <p id="text-connected-number" class="font-bold text-slate-800 dark:text-slate-200 text-sm">
                                {{ $isConnected ? ('+' . $connectedNumber) : 'Belum Terhubung' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-950/40 space-y-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama Perangkat / Bot</span>
                            <p id="text-connected-name" class="font-bold text-slate-800 dark:text-slate-200 text-sm">
                                {{ $connectedName ?? 'Eskasaba Bot' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-950/40 space-y-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Terakhir Terhubung</span>
                            <p id="text-last-connected" class="font-medium text-slate-700 dark:text-slate-300">
                                {{ $status['last_connected_at'] ?? '-' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4 dark:border-slate-800 dark:bg-slate-950/40 space-y-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Penyebab Disconnect Terakhir</span>
                            <div class="mt-0.5">
                                <span id="badge-disconnect-source" class="font-bold text-[11px] px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $status['last_disconnect_source'] ?? 'Belum ada record' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Pesan Status / Last Error --}}
                    <div id="wrapper-last-error" class="{{ $lastError ? 'block' : 'hidden' }} rounded-2xl border border-red-200 bg-red-50/60 p-4 text-xs dark:border-red-900/40 dark:bg-red-950/30">
                        <div class="flex items-start gap-2.5 text-red-800 dark:text-red-300">
                            <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5"></i>
                            <div>
                                <span class="font-bold">Info Catatan Service:</span>
                                <p id="text-last-error" class="text-[11px] mt-0.5 font-mono text-red-700 dark:text-red-300">
                                    {{ $lastError }}
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            {{-- Actions Control Toolbar --}}
            <div class="border-t border-slate-100 pt-5 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs text-slate-400 flex items-center gap-2">
                    <i class="fa-solid fa-rotate fa-spin text-emerald-500 text-[10px]"></i>
                    <span>Polling status otomatis aktif setiap 3 detik.</span>
                </div>

                <div class="flex flex-wrap items-center gap-3">

                    {{-- Tombol Aktifkan Bot --}}
                    <button
                        type="button"
                        id="btn-start-bot"
                        onclick="executeAction('start', this, 'Mengaktifkan...')"
                        class="{{ (!$botEnabled) ? 'inline-flex' : 'hidden' }} items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-xs font-bold text-white shadow-xs transition-colors duration-150 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <i class="fa-solid fa-play"></i>
                        <span>Aktifkan Bot</span>
                    </button>

                    {{-- Tombol Refresh QR --}}
                    <button
                        type="button"
                        id="btn-refresh-qr"
                        onclick="fetchBotStatus()"
                        class="{{ ($botEnabled && !$isConnected && $qrCode) ? 'inline-flex' : 'hidden' }} items-center gap-2 rounded-2xl bg-amber-600 px-5 py-3 text-xs font-bold text-white shadow-xs transition-colors duration-150 hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <i class="fa-solid fa-arrows-rotate"></i>
                        <span>Refresh QR</span>
                    </button>

                    {{-- Tombol Nonaktifkan Bot --}}
                    <button
                        type="button"
                        id="btn-stop-bot"
                        onclick="executeAction('stop', this, 'Menonaktifkan...')"
                        class="{{ ($botEnabled) ? 'inline-flex' : 'hidden' }} items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-xs font-bold text-slate-700 shadow-xs transition-colors duration-150 hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <i class="fa-solid fa-stop"></i>
                        <span>Nonaktifkan Service</span>
                    </button>

                    {{-- Tombol Disconnect --}}
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
                menhubungkan: {
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

                try {
                    const response = await fetch(STATUS_URL, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        renderUI(data);
                    }
                } catch (err) {
                    console.warn('Fetch bot status error:', err);
                } finally {
                    isFetchingStatus = false;
                }
            };

            function renderUI(data) {
                if (!data) return;

                const statusKey = data.status || 'nonaktif';
                const statusMeta = STATUS_CONFIG[statusKey] || STATUS_CONFIG['nonaktif'];

                // Update Status Badge & Dot
                const badgeEl = document.getElementById('bot-status-badge');
                const badgeTextEl = document.getElementById('bot-status-text');
                const dotEl = document.getElementById('bot-status-dot');
                const pulseEl = document.getElementById('bot-status-pulse');

                if (badgeEl) {
                    badgeEl.className = `inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-xs font-black uppercase tracking-wider transition-all duration-300 ${statusMeta.bgClass}`;
                }
                if (badgeTextEl) badgeTextEl.textContent = statusMeta.badge;
                if (dotEl) dotEl.className = `relative inline-flex rounded-full h-2.5 w-2.5 ${statusMeta.dotClass}`;
                if (pulseEl) pulseEl.className = `animate-ping absolute inline-flex h-full w-full rounded-full ${statusMeta.pulseClass} opacity-75`;

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

            // Sync initial state instantly on load
            renderUI(INITIAL_STATUS);

            // Polling Interval (every 3 seconds)
            setInterval(fetchBotStatus, 3000);
        })();
    </script>

</x-layouts.admin>

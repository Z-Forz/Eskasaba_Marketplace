let makeWASocket, useMultiFileAuthState, DisconnectReason, jidNormalizedUser;

async function loadBaileys() {
    if (!makeWASocket) {
        const baileys = await import('@whiskeysockets/baileys');
        makeWASocket = baileys.default || baileys.makeWASocket || baileys;
        useMultiFileAuthState = baileys.useMultiFileAuthState;
        DisconnectReason = baileys.DisconnectReason;
        jidNormalizedUser = baileys.jidNormalizedUser;
    }
}

const express = require('express');
const qrcodeTerminal = require('qrcode-terminal');
const QRCode = require('qrcode');
const cors = require('cors');
const fs = require('fs');
const path = require('path');

const app = express();
app.use(express.json());
app.use(cors());

let sock = null;
let isConnected = false;
let isConnecting = false;
let botEnabled = true;

let botStatus = 'menjalankan'; // 'nonaktif', 'menjalankan', 'menunggu_qr', 'menghubungkan', 'terhubung', 'terputus', 'error'
let qrCodeDataUrl = null;
let connectedNumber = null;
let connectedName = null;
let lastConnectedAt = null;
let lastDisconnectedAt = null;
let lastError = null;
let lastDisconnectReason = null;
let lastDisconnectSource = null; // 'WHATSAPP_APP', 'ADMIN_PANEL', 'NETWORK_TEMPORARY', 'SESSION_CONFLICT', 'QR_TIMEOUT', 'BAD_SESSION'
let lastDisconnectCode = null;

let disconnectHistory = [];

const AUTH_DIR = path.join(__dirname, 'auth_info_baileys');

function hasSavedSession() {
    try {
        const credsPath = path.join(AUTH_DIR, 'creds.json');
        return fs.existsSync(credsPath);
    } catch (e) {
        return false;
    }
}

function addDisconnectLog(source, code, reason) {
    const entry = {
        time: new Date().toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' }),
        source: source,
        code: code || 'N/A',
        reason: reason
    };
    disconnectHistory.unshift(entry);
    if (disconnectHistory.length > 20) {
        disconnectHistory.pop();
    }
    
    // Write log entry to file for debugging
    try {
        const logDir = path.join(__dirname, 'logs');
        if (!fs.existsSync(logDir)) {
            fs.mkdirSync(logDir, { recursive: true });
        }
        const logLine = `[${entry.time}] [${entry.source}] (Code ${entry.code}) ${entry.reason}\n`;
        fs.appendFileSync(path.join(logDir, 'disconnect_history.log'), logLine);
    } catch (e) {}
}

function clearAuthFolder() {
    try {
        if (fs.existsSync(AUTH_DIR)) {
            fs.rmSync(AUTH_DIR, { recursive: true, force: true });
            console.log('🧹 Session WhatsApp (auth_info_baileys) berhasil dibersihkan.');
        }
    } catch (err) {
        console.error('Gagal menghapus folder session:', err.message);
    }
}

async function connectToWhatsApp() {
    await loadBaileys();
    if (!botEnabled) {
        botStatus = 'nonaktif';
        isConnecting = false;
        return;
    }

    if (isConnecting) return;
    isConnecting = true;

    if (sock) {
        try {
            sock.ev.removeAllListeners();
            sock.end(undefined);
        } catch (e) {}
        sock = null;
    }

    if (botStatus !== 'menunggu_qr') {
        botStatus = 'menghubungkan';
    }

    try {
        const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);

        sock = makeWASocket({
            auth: state,
            printQRInTerminal: false,
            browser: ['Eskasaba Marketplace', 'Chrome', '1.0.0'],
            connectTimeoutMs: 60000,
            defaultQueryTimeoutMs: 60000,
            keepAliveIntervalMs: 15000,
            syncFullHistory: false,
            markOnlineOnConnect: true,
        });

        sock.ev.on('creds.update', saveCreds);

        sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                isConnecting = false;
                botStatus = 'menunggu_qr';
                try {
                    qrCodeDataUrl = await QRCode.toDataURL(qr, { margin: 2, scale: 6 });
                } catch (e) {
                    qrCodeDataUrl = null;
                }
                console.log('\n======================================================');
                console.log('📱 SCAN QR CODE DI BAWAH INI DENGAN WHATSAPP ANDA');
                console.log('======================================================\n');
                qrcodeTerminal.generate(qr, { small: true });
            }

            if (connection === 'connecting') {
                if (botStatus !== 'menunggu_qr') {
                    botStatus = 'menghubungkan';
                }
            }

            if (connection === 'close') {
                const wasConnected = isConnected;
                const sessionExists = hasSavedSession();
                isConnected = false;
                isConnecting = false;
                qrCodeDataUrl = null;

                const statusCode = lastDisconnect?.error?.output?.statusCode;
                const errMsg = lastDisconnect?.error?.message || lastDisconnect?.error?.output?.payload?.message || '';
                
                const isLoggedOut = statusCode === DisconnectReason.loggedOut || statusCode === 401;
                const isReplaced = statusCode === DisconnectReason.connectionReplaced || statusCode === 440;
                const isRestartRequired = statusCode === DisconnectReason.restartRequired || statusCode === 515;
                const isBadSession = statusCode === DisconnectReason.badSession || statusCode === 500;
                
                // QR Timeout hanya berlaku jika bot sedang menunggu QR dan belum pernah terhubung/belum punya sesi
                const isQrTimeout = (!wasConnected && !sessionExists) && (errMsg.includes('QR refs attempts ended') || statusCode === 408);

                lastDisconnectedAt = new Date().toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' });
                lastDisconnectCode = statusCode || 'UNKNOWN';

                console.log(`⚠️ Connection Closed (Status: ${statusCode || 'Unknown'}, Reason: ${errMsg}, WasConnected: ${wasConnected}, SessionExists: ${sessionExists}).`);

                if (!botEnabled) {
                    botStatus = 'nonaktif';
                    lastDisconnectSource = 'ADMIN_PANEL';
                    lastDisconnectReason = 'Bot dinonaktifkan dari Admin Panel.';
                    addDisconnectLog('ADMIN_PANEL', statusCode, lastDisconnectReason);
                    return;
                }

                if (isRestartRequired) {
                    lastDisconnectSource = 'BAILEYS_RESTART';
                    lastDisconnectReason = 'Sinkronisasi awal / Restart Baileys (Status 515). Reconnecting otomatis...';
                    console.log(`🔄 ${lastDisconnectReason}`);
                    addDisconnectLog('BAILEYS_RESTART', 515, lastDisconnectReason);
                    botStatus = 'menghubungkan';
                    setTimeout(() => {
                        if (botEnabled && !isConnected) {
                            isConnecting = false;
                            connectToWhatsApp();
                        }
                    }, 500);
                } else if (isLoggedOut) {
                    // DI-LOGOUT DARI HP WHATSAPP - Pihak WhatsApp/HP yang memutuskan!
                    lastDisconnectSource = 'WHATSAPP_APP';
                    lastDisconnectReason = 'Sesi WhatsApp di-logout dari aplikasi WhatsApp di HP (Code 401). Menyiapkan QR Code baru...';
                    lastError = lastDisconnectReason;
                    console.log(`🔒 ${lastDisconnectReason}`);
                    addDisconnectLog('WHATSAPP_APP', 401, lastDisconnectReason);
                    botStatus = 'menghubungkan';
                    connectedNumber = null;
                    connectedName = null;
                    clearAuthFolder();

                    setTimeout(() => {
                        if (botEnabled) {
                            isConnecting = false;
                            connectToWhatsApp();
                        }
                    }, 1000);
                } else if (isQrTimeout) {
                    lastDisconnectSource = 'QR_TIMEOUT';
                    lastDisconnectReason = 'Waktu scan QR Code habis (Expired Code 408). Menyiapkan QR Code baru...';
                    lastError = lastDisconnectReason;
                    console.log(`⏳ ${lastDisconnectReason}`);
                    addDisconnectLog('QR_TIMEOUT', 408, lastDisconnectReason);
                    botStatus = 'menghubungkan';
                    connectedNumber = null;
                    connectedName = null;
                    clearAuthFolder();

                    setTimeout(() => {
                        if (botEnabled) {
                            isConnecting = false;
                            connectToWhatsApp();
                        }
                    }, 1000);
                } else if (isReplaced) {
                    lastDisconnectSource = 'SESSION_CONFLICT';
                    lastDisconnectReason = 'Sesi WhatsApp aktif di perangkat/proses lain (Conflict Code 440).';
                    console.log(`⛔ ${lastDisconnectReason}`);
                    addDisconnectLog('SESSION_CONFLICT', 440, lastDisconnectReason);
                    botStatus = 'error';
                    lastError = lastDisconnectReason;
                } else if (isBadSession && !sessionExists) {
                    lastDisconnectSource = 'BAD_SESSION';
                    lastDisconnectReason = 'File sesi terkorupsi (Code 500). Menyiapkan QR Code baru...';
                    console.log(`❌ ${lastDisconnectReason}`);
                    addDisconnectLog('BAD_SESSION', 500, lastDisconnectReason);
                    botStatus = 'menghubungkan';
                    connectedNumber = null;
                    connectedName = null;
                    clearAuthFolder();

                    setTimeout(() => {
                        if (botEnabled) {
                            isConnecting = false;
                            connectToWhatsApp();
                        }
                    }, 1000);
                } else {
                    // GANGGUAN JARINGAN / SOCKET SEMENTARA - JANGAN HAPUS FOLDER AUTH!
                    lastDisconnectSource = 'NETWORK_TEMPORARY';
                    const codeText = statusCode ? ` (Code ${statusCode})` : '';
                    lastDisconnectReason = `Jaringan / Socket WhatsApp terputus sementara${codeText}: ${errMsg || 'Koneksi terputus'}. Reconnecting otomatis...`;
                    lastError = `Koneksi terputus sementara${codeText}. Menghubungkan kembali...`;
                    console.log(`📡 ${lastDisconnectReason}`);
                    addDisconnectLog('NETWORK_TEMPORARY', statusCode || 'NET_ERR', lastDisconnectReason);

                    botStatus = 'menghubungkan';

                    setTimeout(() => {
                        if (botEnabled && !isConnected) {
                            isConnecting = false;
                            connectToWhatsApp();
                        }
                    }, 3000);
                }
            } else if (connection === 'open') {
                isConnected = true;
                isConnecting = false;
                botStatus = 'terhubung';
                qrCodeDataUrl = null;
                lastConnectedAt = new Date().toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' });
                lastError = null;

                if (sock && sock.user && sock.user.id) {
                    try {
                        const userJid = jidNormalizedUser(sock.user.id);
                        connectedNumber = userJid.split('@')[0].replace(/[^0-9]/g, '');
                        connectedName = sock.user.name || sock.user.notify || 'Eskasaba Bot';
                    } catch (e) {
                        const rawJid = sock.user.id.split(':')[0] || sock.user.id.split('@')[0];
                        connectedNumber = rawJid.replace(/[^0-9]/g, '');
                        connectedName = 'Eskasaba Bot';
                    }
                }

                console.log(`✅ Bot WhatsApp Baileys Berhasil Terhubung (+${connectedNumber}) & Siap Digunakan!`);
            }
        });
    } catch (err) {
        isConnecting = false;
        botStatus = 'error';
        lastError = err.message;
        console.error('Error saat connectToWhatsApp:', err.message);
    }
}

// GET /status - Endpoint Polling untuk Admin Panel
app.get('/status', (req, res) => {
    return res.json({
        status: botStatus,
        bot_enabled: botEnabled,
        is_connected: isConnected,
        qr_code: qrCodeDataUrl,
        connected_number: connectedNumber,
        connected_name: connectedName,
        last_connected_at: lastConnectedAt,
        last_disconnected_at: lastDisconnectedAt,
        last_error: lastError,
        last_disconnect_reason: lastDisconnectReason,
        last_disconnect_source: lastDisconnectSource,
        last_disconnect_code: lastDisconnectCode,
        disconnect_logs: disconnectHistory.slice(0, 5)
    });
});

// POST /start - Aktifkan Bot
app.post('/start', async (req, res) => {
    botEnabled = true;
    if (!isConnected) {
        isConnecting = false;
        botStatus = 'menghubungkan';
        connectToWhatsApp();
    }
    return res.json({
        status: true,
        message: 'Bot WhatsApp diaktifkan',
        bot_status: botStatus
    });
});

// POST /stop - Menonaktifkan Bot
app.post('/stop', async (req, res) => {
    botEnabled = false;
    botStatus = 'nonaktif';
    isConnecting = false;
    isConnected = false;
    qrCodeDataUrl = null;
    lastDisconnectSource = 'ADMIN_PANEL';
    lastDisconnectReason = 'Bot dinonaktifkan oleh Admin dari Admin Panel.';
    addDisconnectLog('ADMIN_PANEL', 'OFF', lastDisconnectReason);
    if (sock) {
        try {
            sock.ev.removeAllListeners();
            sock.end(undefined);
        } catch (e) {}
        sock = null;
    }
    return res.json({
        status: true,
        message: 'Bot WhatsApp dinonaktifkan',
        bot_status: botStatus
    });
});

// POST /disconnect - Memutuskan koneksi WA (Logout & Siapkan QR Baru)
app.post('/disconnect', async (req, res) => {
    botEnabled = true;
    lastDisconnectSource = 'ADMIN_PANEL';
    lastDisconnectReason = 'Koneksi WhatsApp diputuskan secara manual oleh Admin dari Admin Panel.';
    addDisconnectLog('ADMIN_PANEL', 'MANUAL_DISCONNECT', lastDisconnectReason);

    if (sock) {
        try {
            sock.ev.removeAllListeners();
            await Promise.race([
                sock.logout().catch(() => {}),
                new Promise(r => setTimeout(r, 1000))
            ]);
        } catch (e) {}
        try {
            sock.end(undefined);
        } catch (err) {}
        sock = null;
    }
    clearAuthFolder();
    isConnected = false;
    isConnecting = false;
    botStatus = 'menghubungkan';
    qrCodeDataUrl = null;
    connectedNumber = null;
    connectedName = null;
    lastDisconnectedAt = new Date().toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' });

    setTimeout(() => {
        if (botEnabled) {
            isConnecting = false;
            connectToWhatsApp();
        }
    }, 500);

    return res.json({
        status: true,
        message: 'Koneksi WhatsApp diputuskan dan menyiapkan QR Code baru',
        bot_status: botStatus
    });
});

// POST /reset-session - Reset Sesi & Hapus folder Auth
app.post('/reset-session', async (req, res) => {
    botEnabled = true;
    isConnecting = false;
    isConnected = false;
    qrCodeDataUrl = null;
    connectedNumber = null;
    connectedName = null;
    if (sock) {
        try {
            sock.ev.removeAllListeners();
            sock.end(undefined);
        } catch (e) {}
        sock = null;
    }
    clearAuthFolder();

    botStatus = 'menghubungkan';
    lastDisconnectedAt = new Date().toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' });

    setTimeout(() => {
        if (botEnabled) {
            isConnecting = false;
            connectToWhatsApp();
        }
    }, 500);

    return res.json({
        status: true,
        message: 'Sesi WhatsApp berhasil di-reset. Menyiapkan QR Code baru...',
        bot_status: botStatus
    });
});

// POST /send-message - Endpoint HTTP POST dipanggil oleh Laravel WhatsAppService
app.post('/send-message', async (req, res) => {
    const { target, number, phone, message } = req.body;
    const recipient = target || number || phone;

    if (!recipient || !message) {
        return res.status(400).json({ status: false, message: 'Nomor tujuan dan pesan wajib diisi.' });
    }

    if (!botEnabled) {
        return res.status(503).json({
            status: false,
            message: 'Bot WhatsApp sedang dinonaktifkan dari Admin Panel.'
        });
    }

    // Tunggu toleransi hingga 2.5 detik jika koneksi sedang re-sync sebentar
    if (!sock || !isConnected) {
        let attempts = 0;
        while ((!sock || !isConnected) && attempts < 25) {
            await new Promise(r => setTimeout(r, 100));
            attempts++;
        }
    }

    if (!sock || !isConnected) {
        return res.status(503).json({
            status: false,
            message: 'Bot WhatsApp belum terhubung/online. Silakan hubungkan & scan QR Code di Admin Panel.'
        });
    }

    try {
        let formattedNumber = recipient.replace(/[^0-9]/g, '');
        if (formattedNumber.startsWith('0')) {
            formattedNumber = '62' + formattedNumber.substring(1);
        }

        let jid = `${formattedNumber}@s.whatsapp.net`;
        let isRegistered = false;

        if (sock && sock.onWhatsApp) {
            try {
                const onWaPromise = sock.onWhatsApp(formattedNumber);
                const timeoutPromise = new Promise((resolve) => setTimeout(() => resolve(null), 1500));
                const resArray = await Promise.race([onWaPromise, timeoutPromise]);
                if (resArray && Array.isArray(resArray) && resArray[0] && resArray[0].exists) {
                    jid = resArray[0].jid;
                    isRegistered = true;
                }
            } catch (err) {
                console.warn('[WA BOT ON_WHATSAPP WARN]', err.message);
            }
        }

        await sock.sendMessage(jid, { text: message });
        console.log(`[WA BOT SUCCESS] Pesan terkirim ke ${jid} (Target HP: ${formattedNumber}, Terdaftar: ${isRegistered ? 'YA' : 'DEFAULT'})`);

        return res.json({
            status: true,
            message: 'Pesan berhasil terkirim via Baileys Bot',
            target: jid,
            registered: isRegistered
        });
    } catch (error) {
        console.error('[WA BOT ERROR]', error);
        return res.status(500).json({ status: false, error: error.message });
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Server WA Bot jalan di http://localhost:${PORT}`);
    connectToWhatsApp();
});

process.on('uncaughtException', (err) => {
    console.error('[WA BOT UNCAUGHT EXCEPTION]', err.message);
});

process.on('unhandledRejection', (reason) => {
    console.error('[WA BOT UNHANDLED REJECTION]', reason);
});

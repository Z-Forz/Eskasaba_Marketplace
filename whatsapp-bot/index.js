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

const AUTH_DIR = path.join(__dirname, 'auth_info_baileys');

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
            keepAliveIntervalMs: 10000,
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
                isConnected = false;
                isConnecting = false;
                qrCodeDataUrl = null;

                const statusCode = lastDisconnect?.error?.output?.statusCode;
                const errMsg = lastDisconnect?.error?.message || '';
                const isLoggedOut = statusCode === DisconnectReason.loggedOut || statusCode === 401;
                const isReplaced = statusCode === DisconnectReason.connectionReplaced || statusCode === 440;
                const isQrTimeout = errMsg.includes('QR refs attempts ended') || statusCode === 408;

                lastDisconnectedAt = new Date().toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' });
                lastError = errMsg || `Koneksi terputus (Status ${statusCode || 'Unknown'})`;

                console.log(`⚠️ Koneksi WA terputus (Status: ${statusCode || 'Unknown'}, Reason: ${errMsg}).`);

                if (!botEnabled) {
                    botStatus = 'nonaktif';
                    return;
                }

                if (isLoggedOut) {
                    console.log('🔒 Sesi WhatsApp terdeteksi Logged Out. Membersihkan sesi...');
                    botStatus = 'terputus';
                    lastError = 'Sesi WhatsApp telah di-logout dari HP atau expired. Silakan klik Hubungkan / Refresh QR.';
                    connectedNumber = null;
                    connectedName = null;
                    clearAuthFolder();
                } else if (isQrTimeout) {
                    console.log('⏳ QR Code expired tanpa dipindai. Menyiapkan sesi ulang...');
                    botStatus = 'terputus';
                    lastError = 'Waktu scan QR Code habis (Expired). Silakan klik Refresh QR Code.';
                    clearAuthFolder();
                } else if (isReplaced) {
                    console.log('⛔ Sesi WhatsApp terdeteksi aktif di perangkat/proses lain.');
                    botStatus = 'error';
                    lastError = 'Sesi WhatsApp aktif di perangkat/proses lain (Conflict 440).';
                } else {
                    botStatus = 'menghubungkan';
                    setTimeout(() => {
                        if (botEnabled && !isConnected) {
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
        last_error: lastError
    });
});

// POST /start - Aktifkan Bot
app.post('/start', async (req, res) => {
    botEnabled = true;
    if (!isConnected && !isConnecting) {
        botStatus = 'menjalankan';
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

// POST /disconnect - Memutuskan koneksi WA (Logout)
app.post('/disconnect', async (req, res) => {
    if (sock) {
        try {
            await sock.logout();
        } catch (e) {
            try {
                sock.end(undefined);
            } catch (err) {}
        }
        sock = null;
    }
    clearAuthFolder();
    isConnected = false;
    isConnecting = false;
    botStatus = 'terputus';
    qrCodeDataUrl = null;
    connectedNumber = null;
    connectedName = null;
    lastDisconnectedAt = new Date().toLocaleString('id-ID', { timeZone: 'Asia/Jakarta' });
    return res.json({
        status: true,
        message: 'Koneksi WhatsApp diputuskan dan sesi dibersihkan',
        bot_status: botStatus
    });
});

// POST /reset-session - Reset Sesi & Hapus folder Auth
app.post('/reset-session', async (req, res) => {
    botEnabled = false;
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

    botEnabled = true;
    botStatus = 'menjalankan';
    setTimeout(() => {
        connectToWhatsApp();
    }, 1000);

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

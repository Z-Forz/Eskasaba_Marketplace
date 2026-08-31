const { default: makeWASocket, useMultiFileAuthState, DisconnectReason } = require('@whiskeysockets/baileys');
const express = require('express');
const qrcode = require('qrcode-terminal');
const cors = require('cors');
const fs = require('fs');
const path = require('path');

const app = express();
app.use(express.json());
app.use(cors());

let sock = null;
let isConnected = false;

const AUTH_DIR = path.join(__dirname, 'auth_info_baileys');

function clearAuthFolder() {
    try {
        if (fs.existsSync(AUTH_DIR)) {
            fs.rmSync(AUTH_DIR, { recursive: true, force: true });
            console.log('🧹 Session WhatsApp lama/expired berhasil dibersihkan.');
        }
    } catch (err) {
        console.error('Gagal menghapus folder session:', err.message);
    }
}

async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);

    sock = makeWASocket({
        auth: state,
        printQRInTerminal: false,
        browser: ['Eskasaba Marketplace', 'Chrome', '1.0.0']
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect, qr } = update;

        if (qr) {
            console.log('\n======================================================');
            console.log('📱 SCAN QR CODE DI BAWAH INI DENGAN WHATSAPP ANDA');
            console.log('======================================================\n');
            qrcode.generate(qr, { small: true });
        }

        if (connection === 'close') {
            isConnected = false;
            const statusCode = lastDisconnect?.error?.output?.statusCode;
            const isLoggedOut = statusCode === DisconnectReason.loggedOut;

            console.log(`⚠️ Koneksi WA terputus (Status Code: ${statusCode || 'Unknown'}). Reconnecting...`);

            if (isLoggedOut) {
                console.log('🔒 Session WhatsApp telah Keluar / Expired. Menyiapkan QR Code baru...');
                clearAuthFolder();
                setTimeout(() => connectToWhatsApp(), 3000);
            } else {
                // Untuk status code 428 / 515 / 408 / network loss: auto reconnect setelah 3 detik
                setTimeout(() => connectToWhatsApp(), 3000);
            }
        } else if (connection === 'open') {
            isConnected = true;
            console.log('✅ Bot WhatsApp Baileys Berhasil Terhubung & Siap Digunakan!');
        }
    });
}

// Endpoint HTTP POST dipanggil oleh Laravel WhatsAppService
app.post('/send-message', async (req, res) => {
    const { target, number, phone, message } = req.body;
    const recipient = target || number || phone;

    if (!recipient || !message) {
        return res.status(400).json({ status: false, message: 'Nomor tujuan dan pesan wajib diisi.' });
    }

    if (!sock || !isConnected) {
        return res.status(503).json({
            status: false,
            message: 'Bot WhatsApp belum terhubung/online. Silakan scan QR Code di terminal terlebih dahulu.'
        });
    }

    try {
        let formattedNumber = recipient.replace(/[^0-9]/g, '');
        if (formattedNumber.startsWith('0')) {
            formattedNumber = '62' + formattedNumber.substring(1);
        }

        let jid = `${formattedNumber}@s.whatsapp.net`;
        let isRegistered = false;

        // Cek validasi keberadaan nomor di WhatsApp via onWhatsApp()
        if (sock && sock.onWhatsApp) {
            try {
                const [onWaResult] = await sock.onWhatsApp(formattedNumber);
                if (onWaResult && onWaResult.exists) {
                    jid = onWaResult.jid;
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

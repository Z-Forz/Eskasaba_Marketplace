const { default: makeWASocket, useMultiFileAuthState, DisconnectReason } = require('@whiskeysockets/baileys');
const express = require('express');
const qrcode = require('qrcode-terminal');
const cors = require('cors');

const app = express();
app.use(express.json());
app.use(cors());

let sock;

async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('auth_info_baileys');

    sock = makeWASocket({
        auth: state,
        printQRInTerminal: false
    });

    sock.ev.on('creds.update', saveCreds);

    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect, qr } = update;
        
        if (qr) {
            console.log('\n=== SCAN QR CODE DI BAWAH INI DENGAN WHATSAPP ANDA ===\n');
            qrcode.generate(qr, { small: true });
        }

        if (connection === 'close') {
            const shouldReconnect = (lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut);
            console.log('Koneksi terputus. Reconnecting:', shouldReconnect);
            if (shouldReconnect) {
                connectToWhatsApp();
            }
        } else if (connection === 'open') {
            console.log('✅ Bot WhatsApp Baileys Berhasil Terhubung!');
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

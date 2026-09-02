<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Seller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Kirim pesan WhatsApp ke nomor tujuan via Gateway / Baileys Bot HTTP API.
     *
     * @param  string  $to       Nomor HP (Contoh: 081234567890 atau 6281234567890)
     * @param  string  $message  Teks pesan
     * @return bool              Status keberhasilan pengiriman
     */
    public static function send(string $to, string $message): bool
    {
        $enabled = config('services.whatsapp.enabled', true);
        if (! $enabled) {
            return false;
        }

        $url   = config('services.whatsapp.url', 'http://localhost:3000/send-message');
        $token = config('services.whatsapp.token', '');

        // Format nomor ke standar internasional (62xxxx)
        $formattedTo = self::formatPhoneNumber($to);

        if (empty($formattedTo)) {
            Log::warning("WhatsAppService: Nomor tujuan '{$to}' tidak valid.");
            return false;
        }

        try {
            // Beri jeda 300ms untuk memastikan pengiriman beruntun berjalan mulus
            usleep(300000);

            // Support baik Baileys Node Bot API lokal maupun Fonnte / Gateway lain
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => $token,
                    'Content-Type'  => 'application/json',
                ])
                ->post($url, [
                    'target'  => $formattedTo,
                    'number'  => $formattedTo,
                    'phone'   => $formattedTo,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info("WhatsApp message sent to {$formattedTo}");
                return true;
            }

            Log::error("WhatsAppService failed: HTTP {$response->status()} - {$response->body()}");
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsAppService error sending to {$formattedTo}: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Format nomor handphone Indonesia ke format 62xxx.
     */
    public static function formatPhoneNumber(string $number): string
    {
        // Hapus karakter non-digit
        $clean = preg_replace('/[^0-9]/', '', $number);

        if (empty($clean)) {
            return '';
        }

        if (str_starts_with($clean, '0')) {
            return '62' . substr($clean, 1);
        }

        if (str_starts_with($clean, '62')) {
            return $clean;
        }

        return '62' . $clean;
    }

    /**
     * Kirim notifikasi pesanan baru ke Penjual & Pembeli.
     */
    public static function sendNewOrderNotification(Order $order): void
    {
        $order->loadMissing(['seller.user', 'user', 'items.product']);

        $itemsList = "";
        foreach ($order->items as $item) {
            $pName = $item->product_name ?: $item->product?->name ?: 'Produk';
            $optionInfo = "";
            $opt = $item->variant_name ?: $item->note;
            if (!empty($opt)) {
                $optionInfo = " [Pilihan: {$opt}]";
            }
            $itemsList .= "• {$pName}{$optionInfo} (x{$item->quantity})\n";
        }

        $pickupLoc = $order->pickup_location ?: 'COD Sekolah';

        $sellerPhone = $order->seller?->whatsapp_number ?: $order->seller?->user?->phone;
        $buyerPhone = $order->user?->phone;

        // 1. Notifikasi ke Penjual
        if ($sellerPhone) {
            $buyerPhoneText = $buyerPhone ?: '-';
            $sellerMsg = "🛍️ *PESANAN BARU MASUK!*\n\n"
                . "Halo *{$order->seller->user->username}*,\n"
                . "Anda mendapatkan pesanan baru di Eskasaba Marketplace!\n\n"
                . "📄 *Invoice:* #{$order->invoice_number}\n"
                . "👤 *Pembeli:* {$order->user->username}\n"
                . "📱 *No. HP/WA Pembeli:* {$buyerPhoneText}\n"
                . "📍 *Lokasi & Waktu Pengambilan:* {$pickupLoc}\n"
                . "💰 *Total:* Rp " . number_format($order->total_price, 0, ',', '.') . "\n\n"
                . "📋 *Item Pesanan:*\n{$itemsList}\n"
                . "Silakan periksa panel Seller Anda untuk memproses pesanan ini.\n\n"
                . "🌐 *Akses Website:* http://eskamart.smkn1bangsri.sch.id/";

            self::send($sellerPhone, $sellerMsg);
        }

        // 2. Notifikasi ke Pembeli
        if ($buyerPhone) {
            $sellerPhoneText = $sellerPhone ?: '-';
            $buyerMsg = "✅ *PESANAN BERHASIL DIBUAT!*\n\n"
                . "Halo *{$order->user->username}*,\n"
                . "Pesanan Anda dengan Invoice *#{$order->invoice_number}* telah berhasil dibuat.\n\n"
                . "🏪 *Toko Penjual:* {$order->seller->user->username}\n"
                . "📱 *No. HP/WA Penjual:* {$sellerPhoneText}\n"
                . "💰 *Total Pembayaran:* Rp " . number_format($order->total_price, 0, ',', '.') . "\n"
                . "📍 *Lokasi & Waktu Pengambilan:* {$pickupLoc}\n\n"
                . "📋 *Item Pesanan:*\n{$itemsList}\n"
                . "Silakan selesaikan pembayaran dan koordinasi pengambilan pesanan dengan penjual.\n\n"
                . "Terima kasih telah berbelanja di Eskasaba Marketplace!\n"
                . "🌐 *Akses Website:* http://eskamart.smkn1bangsri.sch.id/";

            self::send($buyerPhone, $buyerMsg);
        }
    }

    /**
     * Kirim notifikasi pembaruan status pesanan ke Pembeli.
     */
    public static function sendOrderStatusNotification(Order $order): void
    {
        $order->loadMissing(['user', 'seller.user', 'items.product']);

        $buyerPhone = $order->user?->phone;
        if (! $buyerPhone) {
            Log::warning("WhatsAppService: Nomor HP Pembeli '{$order->user?->username}' tidak diisi untuk pesanan #{$order->invoice_number}.");
            return;
        }

        $sellerPhoneText = $order->seller?->whatsapp_number ?: ($order->seller?->user?->phone ?: '-');

        $statusLabel = match ($order->status) {
            'pending'          => 'Menunggu Konfirmasi ⏳',
            'confirmed'        => 'Dikonfirmasi & Diterima Penjual ✅',
            'processing'       => 'Sedang Diproses oleh Penjual 👨‍🍳',
            'ready_for_pickup', 'ready' => 'Siap Diambil di Kantin/Toko 🎒',
            'completed'        => 'Selesai & Diserahterimakan 🎉',
            'cancelled'        => 'Dibatalkan / Ditolak ❌',
            default            => ucfirst(str_replace('_', ' ', (string) $order->status)),
        };

        $pickupLoc = $order->pickup_location ?: 'Kantin Sekolah';

        $itemsList = "";
        if ($order->items && $order->items->isNotEmpty()) {
            foreach ($order->items as $item) {
                $pName = $item->product_name ?: $item->product?->name ?: 'Produk';
                $optionInfo = "";
                $opt = $item->variant_name ?: $item->note;
                if (!empty($opt)) {
                    $optionInfo = " [Pilihan: {$opt}]";
                }
                $itemsList .= "• {$pName}{$optionInfo} (x{$item->quantity})\n";
            }
        }

        $msg = "📦 *UPDATE STATUS PESANAN*\n\n"
            . "Halo *{$order->user->username}*,\n"
            . "Status pesanan *#{$order->invoice_number}* Anda telah diperbarui menjadi:\n"
            . "👉 *{$statusLabel}*\n\n"
            . "🏪 *Toko Penjual:* {$order->seller->user->username}\n"
            . "📱 *No. HP/WA Penjual:* {$sellerPhoneText}\n"
            . "📍 *Titik Pengambilan:* {$pickupLoc}\n"
            . (!empty($itemsList) ? "📋 *Item:*\n{$itemsList}\n" : "")
            . "\nTerima kasih telah berbelanja di Eskasaba Marketplace!\n"
            . "🌐 *Akses Website:* http://eskamart.smkn1bangsri.sch.id/";

        self::send($buyerPhone, $msg);
    }

    /**
     * Alias untuk sendOrderStatusNotification.
     */
    public static function sendStatusUpdateNotification(Order $order): void
    {
        self::sendOrderStatusNotification($order);
    }

    /**
     * Kirim notifikasi pengajuan akun Seller ke User & Admin.
     */
    public static function sendSellerApplicationNotification(Seller $seller): void
    {
        $seller->loadMissing('user');

        $userPhone  = $seller->user?->phone ?: $seller->whatsapp_number;
        $adminPhone = config('services.whatsapp.admin_number');

        // 1. Notifikasi ke Pendaftar
        if ($userPhone) {
            $userMsg = "📝 *PENGAJUAN SELLER DITERIMA*\n\n"
                . "Halo *{$seller->user->username}*,\n"
                . "Pengajuan toko Anda di Eskasaba Marketplace telah kami terima.\n"
                . "Tim Admin Sekolah akan melakukan verifikasi dalam 1×24 jam.\n\n"
                . "Status pengajuan Anda saat ini: *PENDING VERIFIKASI*.";

            self::send($userPhone, $userMsg);
        }

        // 2. Notifikasi ke Admin Sekolah jika WA Admin dikonfigurasi
        if ($adminPhone) {
            $adminMsg = "📢 *PENGAJUAN SELLER BARU!*\n\n"
                . "Pengguna *{$seller->user->username}* (" . ($seller->user?->nis_nip ?? '-') . ") mengajukan pendaftaran sebagai Penjual baru.\n"
                . "Alasan: {$seller->reason}\n"
                . "Nomor WA Pendaftar: " . ($userPhone ?? '-') . "\n\n"
                . "Silakan buka panel Admin untuk menyetujui atau menolak pengajuan ini.";

            self::send($adminPhone, $adminMsg);
        }
    }

    /**
     * Kirim notifikasi hasil verifikasi Seller oleh Admin ke Pendaftar & Admin.
     */
    public static function sendSellerVerificationResultNotification(Seller $seller): void
    {
        $seller->loadMissing('user');

        $userPhone  = $seller->user?->phone ?: $seller->whatsapp_number;
        $adminPhone = config('services.whatsapp.admin_number');

        if (! $userPhone && ! $adminPhone) {
            return;
        }

        $username = $seller->user?->username ?? 'User';
        $nisNip   = $seller->user?->nis_nip ?? '-';

        if ($seller->isApproved()) {
            $userMsg = "🎉 *PENGAJUAN SELLER DISETUJUI!*\n\n"
                . "Selamat *{$username}*!\n"
                . "Pengajuan toko Anda di Eskasaba Marketplace telah *DISETUJUI* oleh Admin.\n"
                . "Anda sekarang dapat mulai menambah produk dan berjualan online melalui Dashboard Seller Anda.\n\n"
                . "Selamat berjualan!";

            $adminMsg = "✅ *VERIFIKASI SELLER: DISETUJUI*\n\n"
                . "Pengajuan toko *{$username}* ({$nisNip}) telah *DISETUJUI* oleh Admin.\n"
                . "Nomor WA Seller: " . ($userPhone ?? '-');

        } elseif ($seller->needsRevision()) {
            $userMsg = "⚠️ *PENGAJUAN SELLER MEMERLUKAN REVISI*\n\n"
                . "Halo *{$username}*,\n"
                . "Pengajuan toko Anda memerlukan perbaikan dari Admin.\n"
                . "Catatan Admin: _\"{$seller->rejection_note}\"_\n\n"
                . "Silakan login ke akun Anda untuk memperbarui pengajuan.";

            $adminMsg = "⚠️ *VERIFIKASI SELLER: MEMERLUKAN REVISI*\n\n"
                . "Pengajuan toko *{$username}* ({$nisNip}) diubah ke status: *PERLU REVISI*.\n"
                . "Catatan Revisi: _\"{$seller->rejection_note}\"_";

        } elseif ($seller->isRejected()) {
            $userMsg = "❌ *PENGAJUAN SELLER DITOLAK*\n\n"
                . "Halo *{$username}*,\n"
                . "Mohon maaf, pengajuan toko Anda saat ini belum dapat disetujui.\n"
                . "Alasan: _\"{$seller->rejection_note}\"_";

            $adminMsg = "❌ *VERIFIKASI SELLER: DITOLAK*\n\n"
                . "Pengajuan toko *{$username}* ({$nisNip}) telah *DITOLAK* oleh Admin.\n"
                . "Alasan: _\"{$seller->rejection_note}\"_";

        } else {
            return;
        }

        // 1. Kirim Notifikasi ke Pendaftar
        if ($userPhone) {
            self::send($userPhone, $userMsg);
        }

        // 2. Kirim Notifikasi ke Admin Sekolah
        if ($adminPhone) {
            self::send($adminPhone, $adminMsg);
        }
    }

    /**
     * Kirim notifikasi pencabutan / penghapusan status Seller.
     */
    public static function sendSellerRevokedNotification(Seller $seller): void
    {
        $seller->loadMissing('user');

        $userPhone  = $seller->user?->phone ?: $seller->whatsapp_number;
        $adminPhone = config('services.whatsapp.admin_number');

        $username = $seller->user?->username ?? 'User';

        if ($userPhone) {
            $userMsg = "🚫 *STATUS SELLER DICABUT*\n\n"
                . "Halo *{$username}*,\n"
                . "Status kepemilikan toko / seller Anda di Eskasaba Marketplace telah dicabut oleh Admin.\n"
                . "Akun Anda kembali menjadi akun pembeli biasa.";

            self::send($userPhone, $userMsg);
        }

        if ($adminPhone) {
            $adminMsg = "🚫 *STATUS SELLER DICABUT*\n\n"
                . "Status toko untuk pengguna *{$username}* telah dicabut / dihapus oleh Admin.";

            self::send($adminPhone, $adminMsg);
        }
    }
}

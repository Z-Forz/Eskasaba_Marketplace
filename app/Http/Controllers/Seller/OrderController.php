<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Seller;
use App\Services\ImageCompressor;
use App\Services\WhatsAppService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request): View
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();

        $query = Order::with([
            'user',
            'items.product',
            'payment',
        ])
        ->where('seller_id', $seller->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $orders = $query->latest()->paginate(10);

        return view('seller.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($order->seller_id === $seller->id, 403);

        $order->load([
            'user',
            'items.product',
            'payment',
            'pickupSchedule',
        ]);

        return view('seller.orders.show', compact('order'));
    }

    /**
     * Helper to send in-app notification & WhatsApp notification on status update.
     */
    private function notifyOrderStatusUpdate(Order $order): void
    {
        $statusIndo = match($order->status) {
            'pending'                           => 'Menunggu Konfirmasi',
            'confirmed'                         => 'Dikonfirmasi',
            'processing'                        => 'Sedang Diproses',
            'ready_for_pickup'                  => 'Siap Diambil',
            'completed'                         => 'Selesai',
            'cancel_requested'                  => 'Pengajuan Pembatalan Pembeli',
            'return_requested'                  => 'Pengajuan Return / Pengembalian Barang',
            'refund_pending_buyer_confirmation' => 'Bukti Refund Diunggah (Menunggu Konfirmasi Pembeli)',
            'cancelled'                         => 'Dibatalkan',
            'refunded', 'returned'              => 'Pengembalian Dana & Return Selesai',
            default                             => ucfirst(str_replace('_', ' ', (string) $order->status)),
        };

        \App\Models\Notification::create([
            'user_id' => $order->user_id,
            'title'   => 'Status Pesanan Diperbarui 📦',
            'message' => 'Status pesanan #' . ($order->invoice_number ?? $order->id) . ' telah diperbarui oleh penjual menjadi: ' . $statusIndo,
            'type'    => 'order_status_updated',
            'link'    => route('buyer.orders.show', $order),
        ]);

        // Kirim notifikasi WA ke pembeli
        WhatsAppService::sendOrderStatusNotification($order);
    }

    /**
     * Update status, payment verification, and pickup location of the order.
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($order->seller_id === $seller->id, 403);

        if (in_array($order->status, ['cancelled', 'refunded', 'returned', 'refund_pending_buyer_confirmation', 'cancel_requested', 'return_requested'])) {
            return back()->with('error', 'Pesanan yang telah dibatalkan, dikembalikan (return), atau sedang dalam alur pengajuan refund/pembatalan tidak dapat diubah statusnya lagi.');
        }

        $data = $request->validate([
            'status'          => ['nullable', 'in:pending,confirmed,processing,ready_for_pickup,completed,cancelled'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'payment_status'  => ['nullable', 'in:pending,verified,rejected,paid'],
        ]);

        $statusChanged = !empty($data['status']) && $data['status'] !== $order->status;

        if (!empty($data['status']) && $data['status'] === 'cancelled' && in_array($order->status, ['ready_for_pickup', 'completed'])) {
            return back()->with('error', 'Pesanan yang sudah siap diambil atau selesai tidak dapat dibatalkan secara langsung.');
        }

        $order->update(array_filter([
            'status'          => $data['status'] ?? null,
            'pickup_location' => $data['pickup_location'] ?? null,
        ], fn ($v) => ! is_null($v)));

        // Verification for QRIS / COD payment through order page
        if (!empty($data['payment_status']) && $order->payment) {
            $order->payment->update([
                'status'      => $data['payment_status'],
                'verified_at' => in_array($data['payment_status'], ['verified', 'paid']) ? now() : null,
            ]);

            // Auto-advance order status from 'pending' to 'confirmed' if payment is verified
            if (in_array($data['payment_status'], ['verified', 'paid']) && $order->status === 'pending') {
                $order->update(['status' => 'confirmed']);
                $statusChanged = true;
            }
        } elseif (isset($data['status']) && in_array($data['status'], ['confirmed', 'processing', 'ready_for_pickup', 'completed']) && $order->payment && $order->payment->status === 'pending') {
            // Auto-verify QRIS/payment when order is confirmed or processed
            $order->payment->update([
                'status'      => 'verified',
                'verified_at' => now(),
            ]);
        }

        if ($statusChanged || isset($data['status'])) {
            $this->notifyOrderStatusUpdate($order);
        }

        return back()->with(
            'success',
            'Status pesanan & konfirmasi pembayaran berhasil diperbarui.'
        );
    }

    /**
     * Cancel an order directly by seller.
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($order->seller_id === $seller->id, 403);

        if (in_array($order->status, ['completed', 'cancelled', 'refunded', 'returned', 'refund_pending_buyer_confirmation', 'cancel_requested', 'return_requested'])) {
            return back()->with('error', 'Pesanan yang sudah selesai, dibatalkan, atau dalam alur pengajuan refund/return tidak dapat dibatalkan ulang.');
        }

        $request->validate([
            'reason'       => ['required', 'string', 'max:500'],
            'refund_proof' => ['nullable', 'image', 'max:10240'],
            'refund_notes' => ['nullable', 'string', 'max:500'],
        ], [
            'reason.required'    => 'Alasan pembatalan pesanan wajib diisi.',
            'refund_proof.image' => 'Bukti refund harus berupa gambar (JPG, PNG, WEBP).',
            'refund_proof.max'   => 'Ukuran foto bukti refund maksimal 10MB.',
        ]);

        $order->loadMissing(['payment', 'user']);

        $refundPath = null;
        if ($request->hasFile('refund_proof')) {
            $refundPath = ImageCompressor::compressAndStore($request->file('refund_proof'), 'refund_proofs');
        }

        if ($order->payment) {
            $order->payment->update([
                'refund_proof' => $refundPath ?? $order->payment->refund_proof,
                'refund_notes' => $request->refund_notes ?? $order->payment->refund_notes,
                'refunded_at'  => $refundPath ? now() : $order->payment->refunded_at,
            ]);
        } elseif ($refundPath) {
            \App\Models\Payment::create([
                'order_id'     => $order->id,
                'method'       => 'qris',
                'amount'       => $order->total_price,
                'status'       => 'rejected',
                'refund_proof' => $refundPath,
                'refund_notes' => $request->refund_notes,
                'refunded_at'  => now(),
            ]);
        }

        if ($refundPath) {
            // Require buyer confirmation if seller attached refund proof
            $order->update([
                'status'              => 'refund_pending_buyer_confirmation',
                'cancelled_by'        => 'seller',
                'cancellation_reason' => $request->reason,
                'cancellation_status' => 'pending',
            ]);

            \App\Models\Notification::create([
                'user_id' => $order->user_id,
                'title'   => 'Bukti Refund Diunggah Penjual 💳',
                'message' => 'Penjual toko ' . Auth::user()->username . ' membatalkan pesanan #' . $order->invoice_number . ' dan telah mengunggah bukti pengembalian dana (refund) Rp ' . number_format((float) ($order->total_price ?? 0), 0, ',', '.') . '. Silakan periksa dan lakukan konfirmasi penerimaan dana.',
                'type'    => 'refund_uploaded',
                'link'    => route('buyer.orders.show', $order),
            ]);

            WhatsAppService::sendRefundUploadedNotification($order);

            return redirect()->route('seller.orders.show', $order)->with(
                'success',
                'Pembatalan berhasil diajukan dan bukti refund telah diunggah. Menunggu konfirmasi penerimaan dari pembeli.'
            );
        } else {
            // Unpaid / Direct cancellation
            $order->update([
                'status'              => 'cancelled',
                'cancelled_by'        => 'seller',
                'cancellation_reason' => $request->reason,
                'cancellation_status' => 'approved',
            ]);

            $order->restoreStock();

            \App\Models\Notification::create([
                'user_id' => $order->user_id,
                'title'   => 'Pesanan Dibatalkan Penjual ❌',
                'message' => 'Pesanan #' . $order->invoice_number . ' telah dibatalkan oleh penjual. Alasan: ' . $request->reason,
                'type'    => 'order_cancelled',
                'link'    => route('buyer.orders.show', $order),
            ]);

            WhatsAppService::sendCancellationConfirmedNotification($order);

            return redirect()->route('seller.orders.show', $order)->with(
                'success',
                'Pesanan berhasil dibatalkan dan stok produk telah dikembalikan.'
            );
        }
    }

    /**
     * Confirm buyer's cancellation or return request & upload refund proof.
     */
    public function confirmCancellation(Request $request, Order $order): RedirectResponse
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($order->seller_id === $seller->id, 403);

        if (!in_array($order->status, ['cancel_requested', 'return_requested'])) {
            return back()->with('error', 'Hanya pengajuan pembatalan atau return aktif dari pembeli yang dapat disetujui.');
        }

        $request->validate([
            'refund_proof' => ['nullable', 'image', 'max:10240'],
            'refund_notes' => ['nullable', 'string', 'max:500'],
        ], [
            'refund_proof.image' => 'Bukti refund harus berupa gambar (JPG, PNG, WEBP).',
            'refund_proof.max'   => 'Ukuran foto bukti refund maksimal 10MB.',
        ]);

        $order->loadMissing(['payment', 'user']);

        $refundPath = null;
        if ($request->hasFile('refund_proof')) {
            $refundPath = ImageCompressor::compressAndStore($request->file('refund_proof'), 'refund_proofs');
        }

        if ($order->payment) {
            $order->payment->update([
                'refund_proof' => $refundPath ?? $order->payment->refund_proof,
                'refund_notes' => $request->refund_notes ?? $order->payment->refund_notes,
                'refunded_at'  => $refundPath ? now() : ($order->payment->refunded_at ?? now()),
            ]);
        } elseif ($refundPath) {
            \App\Models\Payment::create([
                'order_id'     => $order->id,
                'method'       => 'qris',
                'amount'       => $order->total_price,
                'status'       => 'rejected',
                'refund_proof' => $refundPath,
                'refund_notes' => $request->refund_notes,
                'refunded_at'  => now(),
            ]);
        }

        if ($refundPath || $order->payment?->refund_proof) {
            // Require buyer confirmation when refund proof is uploaded
            $order->update([
                'status'              => 'refund_pending_buyer_confirmation',
                'cancellation_status' => 'pending',
            ]);

            \App\Models\Notification::create([
                'user_id' => $order->user_id,
                'title'   => 'Bukti Refund Diunggah Penjual 💳',
                'message' => 'Penjual telah menyetujui pengajuan pembatalan/return pesanan #' . $order->invoice_number . ' dan mengunggah foto bukti pengembalian dana (refund) Rp ' . number_format((float) ($order->total_price ?? 0), 0, ',', '.') . '. Silakan periksa rekening/e-wallet Anda dan lakukan konfirmasi penerimaan.',
                'type'    => 'refund_uploaded',
                'link'    => route('buyer.orders.show', $order),
            ]);

            WhatsAppService::sendRefundUploadedNotification($order);

            return redirect()->route('seller.orders.show', $order)->with(
                'success',
                'Pengajuan pembatalan/return disetujui. Foto bukti pengembalian dana telah dikirimkan ke pembeli untuk dikonfirmasi penerimaannya.'
            );
        } else {
            // Unpaid COD or direct approval without money transfer
            $finalStatus = $order->status === 'return_requested' ? 'returned' : 'cancelled';

            $order->update([
                'status'              => $finalStatus,
                'cancellation_status' => 'approved',
            ]);

            $order->restoreStock();

            \App\Models\Notification::create([
                'user_id' => $order->user_id,
                'title'   => 'Pengajuan Pembatalan / Return Disetujui ✅',
                'message' => 'Pengajuan pembatalan/return pesanan #' . $order->invoice_number . ' telah disetujui oleh penjual.',
                'type'    => 'order_cancellation_approved',
                'link'    => route('buyer.orders.show', $order),
            ]);

            WhatsAppService::sendCancellationConfirmedNotification($order);

            return redirect()->route('seller.orders.show', $order)->with(
                'success',
                'Pengajuan pembatalan/return berhasil disetujui dan stok produk telah dikembalikan.'
            );
        }
    }

    /**
     * Reject buyer's cancellation or return request.
     */
    public function rejectCancellation(Request $request, Order $order): RedirectResponse
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($order->seller_id === $seller->id, 403);

        if (!in_array($order->status, ['cancel_requested', 'return_requested'])) {
            return back()->with('error', 'Hanya pengajuan pembatalan atau return aktif dari pembeli yang dapat ditolak.');
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ], [
            'rejection_reason.required' => 'Catatan alasan penolakan wajib diisi.',
            'rejection_reason.max'      => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        $order->loadMissing(['user']);

        // Restore to processing / confirmed / completed based on context
        $revertedStatus = match($order->status) {
            'return_requested' => 'completed',
            default            => 'processing',
        };

        $order->update([
            'status'              => $revertedStatus,
            'cancellation_status' => 'rejected',
        ]);

        \App\Models\Notification::create([
            'user_id' => $order->user_id,
            'title'   => 'Pengajuan Pembatalan / Return Ditolak ❌',
            'message' => 'Pengajuan pembatalan/return pesanan #' . $order->invoice_number . ' ditolak oleh penjual. Alasan Penolakan: ' . $request->rejection_reason,
            'type'    => 'order_cancellation_rejected',
            'link'    => route('buyer.orders.show', $order),
        ]);

        $buyerPhone = $order->user?->phone;
        if ($buyerPhone) {
            $msg = "❌ *PENGAJUAN PEMBATALAN / RETURN DITOLAK*\n\n"
                . "Halo *{$order->user->username}*,\n"
                . "Pengajuan pembatalan/return pesanan *#{$order->invoice_number}* Anda belum dapat disetujui oleh penjual.\n\n"
                . "📝 *Alasan Penolakan Penjual:*\n_\"{$request->rejection_reason}\"_\n\n"
                . "Status pesanan Anda dikembalikan ke: *" . strtoupper(str_replace('_', ' ', $revertedStatus)) . "*.\n"
                . "🌐 *Detail Pesanan:* https://eskamart.smkn1bangsri.sch.id/buyer/orders/{$order->id}";

            WhatsAppService::send($buyerPhone, $msg);
        }

        return redirect()->route('seller.orders.show', $order)->with(
            'success',
            'Pengajuan pembatalan/return telah ditolak dan pembeli telah dinotifikasi.'
        );
    }
}
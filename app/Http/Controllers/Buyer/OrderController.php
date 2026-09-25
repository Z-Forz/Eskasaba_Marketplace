<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ImageCompressor;
use App\Services\WhatsAppService;
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
        $query = Order::with([
            'seller.user',
            'items.product',
            'payment',
            'pickupSchedule',
        ])
        ->where('user_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(10);

        return view('buyer.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $order->load([
            'seller.user',
            'items.product',
            'payment',
            'pickupSchedule',
        ]);

        return view('buyer.orders.show', compact('order'));
    }

    /**
     * Upload proof of payment for QRIS / transfer order.
     */
    public function uploadProof(Request $request, Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        $request->validate([
            'proof' => ['required', 'image', 'max:10240'],
        ], [
            'proof.required' => 'File gambar bukti pembayaran wajib dipilih.',
            'proof.image'    => 'Bukti pembayaran harus berupa gambar (JPG, PNG, WEBP).',
            'proof.max'      => 'Ukuran foto bukti pembayaran maksimal 10MB.',
        ]);

        $proofPath = ImageCompressor::compressAndStore($request->file('proof'), 'payment_proofs');

        // Check if payment record exists or create new
        if ($order->payment) {
            $order->payment->update([
                'proof'  => $proofPath,
                'status' => 'pending',
            ]);
        } else {
            \App\Models\Payment::create([
                'order_id' => $order->id,
                'method'   => 'qris',
                'amount'   => $order->total_price,
                'proof'    => $proofPath,
                'status'   => 'pending',
            ]);
        }

        // Notify seller via in-app notification
        \App\Models\Notification::create([
            'user_id' => $order->seller->user_id,
            'title'   => 'Bukti Pembayaran Diunggah 🧾',
            'message' => 'Pembeli ' . Auth::user()->username . ' telah mengunggah bukti pembayaran untuk pesanan #' . $order->invoice_number . '. Silakan verifikasi dana masuk.',
            'type'    => 'payment_proof_uploaded',
            'link'    => route('seller.orders.show', $order),
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah! Penjual akan memverifikasi mutasi pembayaran Anda.');
    }

    /**
     * Cancel or request cancellation of an order by buyer.
     */
    public function cancel(Request $request, Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        if (!in_array($order->status, ['pending', 'confirmed', 'processing'])) {
            return back()->with('error', 'Pesanan yang sudah siap diambil, selesai, atau dalam proses pembatalan/return tidak dapat dibatalkan.');
        }

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'Alasan pembatalan pesanan wajib diisi.',
            'reason.max'      => 'Alasan pembatalan maksimal 500 karakter.',
        ]);

        $order->loadMissing(['payment', 'seller.user']);

        $isQrisPaid = strtolower($order->payment?->method ?? '') === 'qris' &&
            ($order->payment?->proof || in_array($order->payment?->status, ['verified', 'paid']));

        // Require seller approval for any order in 'confirmed' or 'processing' state, or if QRIS payment was uploaded/paid
        $requiresApproval = $order->status !== 'pending' || $isQrisPaid;

        if ($requiresApproval) {
            $order->update([
                'status'              => 'cancel_requested',
                'previous_status'     => $order->status,
                'cancelled_by'        => 'buyer',
                'cancellation_reason' => $request->reason,
                'cancellation_status' => 'pending',
            ]);

            \App\Models\Notification::create([
                'user_id' => $order->seller->user_id,
                'title'   => 'Pengajuan Pembatalan Pesanan ⚠️',
                'message' => 'Pembeli ' . Auth::user()->username . ' mengajukan pembatalan pesanan #' . $order->invoice_number . '. Alasan: ' . $request->reason,
                'type'    => 'order_cancellation_requested',
                'link'    => route('seller.orders.show', $order),
            ]);

            WhatsAppService::sendCancellationRequestNotification($order);

            return back()->with('success', 'Pengajuan pembatalan berhasil dikirim ke penjual. Penjual akan memproses pengembalian dana (refund) jika pembayaran sudah diterima.');
        } else {
            // Unpaid COD / Pending order -> cancel immediately & restore stock
            $order->update([
                'status'              => 'cancelled',
                'previous_status'     => $order->status,
                'cancelled_by'        => 'buyer',
                'cancellation_reason' => $request->reason,
                'cancellation_status' => 'approved',
            ]);

            $order->restoreStock();

            \App\Models\Notification::create([
                'user_id' => $order->seller->user_id,
                'title'   => 'Pesanan Dibatalkan Pembeli ❌',
                'message' => 'Pembeli ' . Auth::user()->username . ' membatalkan pesanan #' . $order->invoice_number . ' sebelum dikonfirmasi penjual. Alasan: ' . $request->reason,
                'type'    => 'order_cancelled',
                'link'    => route('seller.orders.show', $order),
            ]);

            WhatsAppService::sendCancellationConfirmedNotification($order);

            return back()->with('success', 'Pesanan berhasil dibatalkan.');
        }
    }

    /**
     * Request return/pengembalian of a completed/ready order by buyer.
     */
    public function requestReturn(Request $request, Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        if (in_array($order->status, ['ready_for_pickup', 'completed'])) {
            return back()->with('error', 'Pesanan yang sudah siap diambil atau selesai tidak dapat dibatalkan ataupun di-return.');
        }

        $request->validate([
            'reason'       => ['required', 'string', 'max:500'],
            'return_proof' => ['required', 'image', 'max:10240'],
        ], [
            'reason.required'       => 'Alasan pengajuan return / pengembalian barang wajib diisi.',
            'reason.max'            => 'Alasan return maksimal 500 karakter.',
            'return_proof.required' => 'Foto bukti kondisi barang (rusak/salah/cacat) wajib diunggah.',
            'return_proof.image'    => 'Foto bukti harus berupa file gambar (JPG, PNG, WEBP).',
            'return_proof.max'      => 'Ukuran foto bukti barang maksimal 10MB.',
        ]);

        $order->loadMissing(['seller.user']);

        $proofPath = ImageCompressor::compressAndStore($request->file('return_proof'), 'return_proofs');

        $order->update([
            'status'              => 'return_requested',
            'previous_status'     => $order->status,
            'cancelled_by'        => 'buyer',
            'cancellation_reason' => $request->reason,
            'return_proof_image'  => $proofPath,
            'cancellation_status' => 'pending',
        ]);

        \App\Models\Notification::create([
            'user_id' => $order->seller->user_id,
            'title'   => 'Pengajuan Return / Pengembalian Barang 🔄',
            'message' => 'Pembeli ' . Auth::user()->username . ' mengajukan return untuk pesanan #' . $order->invoice_number . ' beserta foto bukti kondisi barang. Alasan: ' . $request->reason,
            'type'    => 'order_return_requested',
            'link'    => route('seller.orders.show', $order),
        ]);

        WhatsAppService::sendReturnRequestNotification($order);

        return back()->with('success', 'Pengajuan return barang & refund dana berhasil dikirim ke penjual bersama foto bukti. Penjual akan memeriksa dan memproses pengembalian Anda.');
    }

    /**
     * Confirm receipt of refunded money by buyer.
     */
    public function confirmRefund(Request $request, Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);

        if (!in_array($order->status, ['refund_pending_buyer_confirmation', 'cancel_requested', 'return_requested']) && !$order->payment?->refund_proof) {
            return back()->with('error', 'Status pesanan tidak memerlukan konfirmasi penerimaan refund saat ini.');
        }

        $order->loadMissing(['seller.user', 'payment']);

        // Update payment buyer confirmation flag
        if ($order->payment) {
            $order->payment->update([
                'buyer_confirmed_refund' => true,
            ]);
        }

        $finalStatus = $order->status === 'return_requested' ? 'returned' : 'cancelled';

        $order->update([
            'status'              => $finalStatus,
            'cancellation_status' => 'approved',
            'refund_confirmed_at' => now(),
        ]);

        // Restore product stock
        $order->restoreStock();

        // Notify seller that buyer has confirmed receiving refund
        \App\Models\Notification::create([
            'user_id' => $order->seller->user_id,
            'title'   => 'Pengembalian Dana Dikonfirmasi Pembeli ✅',
            'message' => 'Pembeli ' . Auth::user()->username . ' telah mengonfirmasi bahwa dana refund sebesar Rp ' . number_format((float) ($order->total_price ?? 0), 0, ',', '.') . ' untuk pesanan #' . $order->invoice_number . ' telah diterima dengan lunas.',
            'type'    => 'refund_confirmed',
            'link'    => route('seller.orders.show', $order),
        ]);

        WhatsAppService::sendRefundConfirmedByBuyerNotification($order);

        return back()->with('success', 'Terima kasih! Anda telah mengonfirmasi penerimaan pengembalian dana. Transaksi pembatalan/return telah selesai secara resmi.');
    }
}
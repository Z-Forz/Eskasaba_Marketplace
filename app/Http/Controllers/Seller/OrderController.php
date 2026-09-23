<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Seller;
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
        \App\Models\Notification::create([
            'user_id' => $order->user_id,
            'title'   => 'Status Pesanan Diperbarui 📦',
            'message' => 'Status pesanan ' . ($order->invoice_number ?? '#' . $order->id) . ' telah diubah menjadi: ' . ucfirst(str_replace('_', ' ', (string) $order->status)),
            'type'    => 'order_status_updated',
            'link'    => route('buyer.orders.show', $order),
        ]);

        // Kirim notifikasi WA ke pembeli
        \App\Services\WhatsAppService::sendOrderStatusNotification($order);
    }

    /**
     * Update status, payment verification, and pickup location of the order.
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($order->seller_id === $seller->id, 403);

        $data = $request->validate([
            'status'          => ['nullable', 'in:pending,confirmed,processing,ready_for_pickup,completed,cancelled'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'payment_status'  => ['nullable', 'in:pending,verified,rejected,paid'],
        ]);

        $statusChanged = !empty($data['status']) && $data['status'] !== $order->status;

        if (!empty($data['status']) && $data['status'] === 'cancelled' && in_array($order->status, ['ready_for_pickup', 'completed'])) {
            return back()->with('error', 'Pesanan yang sudah siap diambil atau selesai tidak dapat dibatalkan.');
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
     * Accept the order.
     */
    public function accept(Order $order): RedirectResponse
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($order->seller_id === $seller->id, 403);

        $order->update([
            'status' => 'confirmed',
        ]);

        if ($order->payment && $order->payment->status === 'pending') {
            $order->payment->update([
                'status'      => 'verified',
                'verified_at' => now(),
            ]);
        }

        $this->notifyOrderStatusUpdate($order);

        return back()->with(
            'success',
            'Pesanan & pembayaran QRIS berhasil dikonfirmasi.'
        );
    }

    /**
     * Reject the order.
     */
    public function reject(Order $order): RedirectResponse
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($order->seller_id === $seller->id, 403);

        $order->update([
            'status' => 'cancelled',
        ]);

        if ($order->payment) {
            $order->payment->update([
                'status' => 'rejected',
            ]);
        }

        $this->notifyOrderStatusUpdate($order);

        return back()->with(
            'success',
            'Pesanan berhasil ditolak.'
        );
    }

    /**
     * Mark order as ready to pickup.
     */
    public function ready(Order $order): RedirectResponse
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($order->seller_id === $seller->id, 403);

        $order->update([
            'status' => 'ready_for_pickup',
        ]);

        $this->notifyOrderStatusUpdate($order);

        return back()->with(
            'success',
            'Barang siap diambil.'
        );
    }

    /**
     * Complete the order.
     */
    public function complete(Order $order): RedirectResponse
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($order->seller_id === $seller->id, 403);

        $order->update([
            'status' => 'completed',
        ]);

        if ($order->payment && $order->payment->status !== 'verified') {
            $order->payment->update([
                'status'      => 'verified',
                'verified_at' => now(),
            ]);
        }

        $this->notifyOrderStatusUpdate($order);

        return back()->with(
            'success',
            'Pesanan selesai & diserahterimakan.'
        );
    }

    /**
     * Cancel an order directly by seller.
     */
    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($order->seller_id === $seller->id, 403);

        if (!in_array($order->status, ['pending', 'confirmed', 'processing'])) {
            return back()->with('error', 'Pesanan yang sudah siap diambil, selesai, atau dalam proses pembatalan tidak dapat dibatalkan.');
        }

        $request->validate([
            'reason'       => ['required', 'string', 'max:500'],
            'refund_proof' => ['nullable', 'image', 'max:10240'],
            'refund_notes' => ['nullable', 'string', 'max:500'],
        ], [
            'reason.required'  => 'Alasan pembatalan pesanan wajib diisi.',
            'refund_proof.image' => 'Bukti refund harus berupa gambar (JPG, PNG, WEBP).',
            'refund_proof.max'   => 'Ukuran foto bukti refund maksimal 10MB.',
        ]);

        $order->loadMissing(['payment', 'user']);

        $refundPath = null;
        if ($request->hasFile('refund_proof')) {
            $refundPath = \App\Services\ImageCompressor::compressAndStore($request->file('refund_proof'), 'refund_proofs');
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
            'message' => 'Pesanan #' . $order->invoice_number . ' telah dibatalkan oleh penjual. Alasan: ' . $request->reason . ($refundPath ? ' (Bukti refund telah diunggah).' : ''),
            'type'    => 'order_cancelled',
            'link'    => route('buyer.orders.show', $order),
        ]);

        \App\Services\WhatsAppService::sendCancellationConfirmedNotification($order);

        return redirect()->route('seller.orders.show', $order)->with(
            'success',
            'Pesanan berhasil dibatalkan dan stok produk telah dikembalikan.'
        );
    }

    /**
     * Confirm buyer's cancellation request and upload refund proof.
     */
    public function confirmCancellation(Request $request, Order $order): RedirectResponse
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($order->seller_id === $seller->id, 403);

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
            $refundPath = \App\Services\ImageCompressor::compressAndStore($request->file('refund_proof'), 'refund_proofs');
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

        $order->update([
            'status'              => 'cancelled',
            'cancellation_status' => 'approved',
        ]);

        $order->restoreStock();

        \App\Models\Notification::create([
            'user_id' => $order->user_id,
            'title'   => 'Pengajuan Pembatalan Disetujui ✅',
            'message' => 'Pengajuan pembatalan pesanan #' . $order->invoice_number . ' telah disetujui oleh penjual.' . ($refundPath ? ' Bukti refund pengembalian dana telah diunggah.' : ''),
            'type'    => 'order_cancellation_approved',
            'link'    => route('buyer.orders.show', $order),
        ]);

        \App\Services\WhatsAppService::sendCancellationConfirmedNotification($order);

        return redirect()->route('seller.orders.show', $order)->with(
            'success',
            'Pengajuan pembatalan berhasil disetujui. Bukti pengembalian dana telah tersimpan.'
        );
    }
}
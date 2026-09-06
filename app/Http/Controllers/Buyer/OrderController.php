<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

        $proofPath = \App\Services\ImageCompressor::compressAndStore($request->file('proof'), 'payment_proofs');

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

        // Send WhatsApp notification to Buyer & Seller
        \App\Services\WhatsAppService::sendPaymentProofUploadedNotification($order);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah! Penjual akan memverifikasi mutasi pembayaran Anda.');
    }
}
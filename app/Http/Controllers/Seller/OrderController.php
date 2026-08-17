<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Seller;
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
        } elseif (isset($data['status']) && in_array($data['status'], ['confirmed', 'processing', 'ready_for_pickup', 'completed']) && $order->payment && $order->payment->status === 'pending') {
            // Auto-verify QRIS/payment when order is confirmed or processed
            $order->payment->update([
                'status'      => 'verified',
                'verified_at' => now(),
            ]);
        }

        if (isset($data['status'])) {
            \App\Models\Notification::create([
                'user_id' => $order->user_id,
                'title'   => 'Status Pesanan Diperbarui 📦',
                'message' => 'Status pesanan ' . ($order->invoice_number ?? '#' . $order->id) . ' telah diubah menjadi: ' . ucfirst(str_replace('_', ' ', $order->status)),
                'type'    => 'order_status_updated',
                'link'    => route('buyer.orders.show', $order),
            ]);
        }

        return back()->with(
            'success',
            'Status pesanan, konfirmasi pembayaran & lokasi pengambilan berhasil diperbarui.'
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

        return back()->with(
            'success',
            'Pesanan berhasil diselesaikan.'
        );
    }
}
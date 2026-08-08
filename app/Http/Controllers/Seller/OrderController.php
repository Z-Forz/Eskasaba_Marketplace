<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(): View
    {
        $seller = Seller::where(
            'user_id',
            Auth::id()
        )->firstOrFail();

        $orders = Order::with([
            'user',
            'items.product',
            'payment',
        ])
        ->where('seller_id', $seller->id)
        ->latest()
        ->paginate(10);

        return view('seller.orders.index', compact(
            'orders'
        ));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $order->load([
            'user',
            'items.product',
            'payment',
            'pickupSchedule',
        ]);

        return view('seller.orders.show', compact(
            'order'
        ));
    }

    /**
     * Accept the order.
     */
    public function accept(Order $order): RedirectResponse
    {
        $order->update([
            'status' => 'accepted',
        ]);

        return back()->with(
            'success',
            'Pesanan berhasil diterima.'
        );
    }

    /**
     * Reject the order.
     */
    public function reject(Order $order): RedirectResponse
    {
        $order->update([
            'status' => 'cancelled',
        ]);

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
        $order->update([
            'status' => 'ready_to_pickup',
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
        $order->update([
            'status' => 'completed',
        ]);

        return back()->with(
            'success',
            'Pesanan berhasil diselesaikan.'
        );
    }
}
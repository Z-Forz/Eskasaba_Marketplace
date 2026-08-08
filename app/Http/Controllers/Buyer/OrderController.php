<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(): View
    {
        $orders = Order::with([
            'seller.user',
            'items.product',
            'payment',
            'pickupSchedule',
        ])
        ->where('user_id', Auth::id())
        ->latest()
        ->paginate(10);

        return view('buyer.orders.index', compact(
            'orders'
        ));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $order->load([
            'seller.user',
            'items.product',
            'payment',
            'pickupSchedule',
        ]);

        return view('buyer.orders.show', compact(
            'order'
        ));
    }
}
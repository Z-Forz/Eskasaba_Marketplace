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
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status', 'all');

        $query = Order::with([
            'user',
            'seller.user',
            'items.product',
        ]);

        if ($status === 'processing') {
            $query->whereIn('status', ['confirmed', 'processing']);
        } elseif ($status === 'cancelled') {
            $query->whereIn('status', ['cancelled', 'cancel_requested', 'return_requested', 'refund_pending_buyer_confirmation', 'returned']);
        } elseif ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'all'              => Order::count(),
            'pending'          => Order::where('status', 'pending')->count(),
            'processing'       => Order::whereIn('status', ['confirmed', 'processing'])->count(),
            'ready_for_pickup' => Order::where('status', 'ready_for_pickup')->count(),
            'completed'        => Order::where('status', 'completed')->count(),
            'cancelled'        => Order::whereIn('status', ['cancelled', 'cancel_requested', 'return_requested', 'refund_pending_buyer_confirmation', 'returned'])->count(),
        ];

        return view('admin.orders.index', compact('orders', 'status', 'counts'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $order->load([
            'user',
            'seller.user',
            'items.product.images',
            'payment',
            'pickupSchedule',
        ]);

        return view('admin.orders.show', compact('order'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index(): View
    {
        $orders = Order::latest()->paginate(15);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        return view('admin.orders.show', compact('order'));
    }
}

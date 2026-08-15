<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $totalOrders = Order::where('user_id', $user->id)->count();
        $pendingOrders = Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $completedOrders = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $cartCount = Cart::where('user_id', $user->id)
            ->withCount('items')
            ->first()?->items_count ?? 0;

        $recentOrders = Order::where('user_id', $user->id)
            ->latest()
            ->limit(8)
            ->get();

        return view('buyer.dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'cartCount',
            'recentOrders'
        ));
    }
}

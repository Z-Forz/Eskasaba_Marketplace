<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the seller dashboard.
     */
    public function index(): View
    {
        $seller = Seller::where(
            'user_id',
            Auth::id()
        )->firstOrFail();

        $totalProducts = Product::where(
            'seller_id',
            $seller->id
        )->count();

        $totalOrders = Order::where(
            'seller_id',
            $seller->id
        )->count();

        $pendingOrders = Order::where(
            'seller_id',
            $seller->id
        )->where(
            'status',
            'pending'
        )->count();

        $completedOrders = Order::where(
            'seller_id',
            $seller->id
        )->where(
            'status',
            'completed'
        )->count();

        return view('seller.dashboard', compact(
            'seller',
            'totalProducts',
            'totalOrders',
            'pendingOrders',
            'completedOrders'
        ));
    }
}
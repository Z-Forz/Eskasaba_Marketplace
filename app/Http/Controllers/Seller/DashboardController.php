<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the seller dashboard with comprehensive Sales Report.
     */
    public function index(): View
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();

        // 1. Basic Stats
        $totalProducts = Product::where('seller_id', $seller->id)->count();
        $totalOrders = Order::where('seller_id', $seller->id)->count();
        $pendingOrders = Order::where('seller_id', $seller->id)->where('status', 'pending')->count();
        $completedOrders = Order::where('seller_id', $seller->id)->where('status', 'completed')->count();

        // 2. Sales Report Analytics (Valid statuses: confirmed, processing, ready_for_pickup, completed)
        $validStatuses = ['confirmed', 'processing', 'ready_for_pickup', 'completed'];

        $totalRevenue = Order::where('seller_id', $seller->id)
            ->whereIn('status', $validStatuses)
            ->sum('total_price');

        $revenueThisMonth = Order::where('seller_id', $seller->id)
            ->whereIn('status', $validStatuses)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_price');

        $revenueToday = Order::where('seller_id', $seller->id)
            ->whereIn('status', $validStatuses)
            ->whereDate('created_at', now()->today())
            ->sum('total_price');

        $totalItemsSold = OrderItem::whereHas('order', function ($q) use ($seller, $validStatuses) {
            $q->where('seller_id', $seller->id)->whereIn('status', $validStatuses);
        })->sum('quantity');

        // 3. Top Selling Products
        $topProducts = OrderItem::whereHas('order', function ($q) use ($seller, $validStatuses) {
            $q->where('seller_id', $seller->id)->whereIn('status', $validStatuses);
        })
        ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(quantity * price) as total_revenue'))
        ->groupBy('product_id', 'product_name')
        ->with('product.images')
        ->orderByDesc('total_sold')
        ->take(5)
        ->get();

        // 4. Recent Orders
        $recentOrders = Order::with(['user', 'items.product', 'payment'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->take(5)
            ->get();

        // 5. Recent Products
        $recentProducts = Product::with(['images', 'category', 'seller.user'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->take(4)
            ->get();

        return view('seller.dashboard', compact(
            'seller',
            'totalProducts',
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'totalRevenue',
            'revenueThisMonth',
            'revenueToday',
            'totalItemsSold',
            'topProducts',
            'recentOrders',
            'recentProducts'
        ));
    }
}
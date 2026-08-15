<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $totalUsers = User::count();

        $totalCategories = Category::count();

        $totalSellers = Seller::count();

        $totalProducts = Product::count();

        $totalOrders = Order::count();

        // Recent orders for display
        $recentOrders = Order::with('buyer')->latest()->limit(8)->get();

        // Pending sellers collection for the dashboard list
        $pendingSellers = Seller::with('user')->where('status', 'pending')->latest()->limit(8)->get();

        $pendingOrders = Order::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalCategories',
            'totalSellers',
            'totalProducts',
            'totalOrders',
            'pendingSellers',
            'pendingOrders',
            'recentOrders'
        ));
    }
}
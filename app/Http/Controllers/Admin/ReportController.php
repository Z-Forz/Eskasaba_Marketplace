<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Tampilkan Laporan Produk Marketplace.
     */
    public function products(Request $request): View
    {
        $totalProducts = Product::count();
        $outOfStockCount = Product::where('stock', '<=', 0)->count();

        $categories = Category::withCount('products')->get();

        $products = Product::with(['category', 'seller.user'])
            ->withCount('orderItems')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.reports.products', compact(
            'totalProducts',
            'outOfStockCount',
            'categories',
            'products'
        ));
    }

    /**
     * Tampilkan Laporan Penjualan Marketplace.
     */
    public function sales(Request $request): View
    {
        $totalOrders = Order::count();
        $completedOrdersCount = Order::where('status', 'completed')->count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');

        $sellers = Seller::with('user')
            ->withCount(['orders', 'products'])
            ->get();

        $recentSales = Order::with(['buyer', 'seller.user', 'items.product'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.sales', compact(
            'totalOrders',
            'completedOrdersCount',
            'totalRevenue',
            'sellers',
            'recentSales'
        ));
    }
}

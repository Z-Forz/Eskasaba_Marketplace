<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Seller;
use Carbon\Carbon;
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
        $selectedMonth = (int) $request->input('month', date('n'));
        $selectedYear = (int) $request->input('year', date('Y'));

        // Statistik Keseluruhan (All Time)
        $totalOrders = Order::count();
        $completedOrdersCount = Order::where('status', 'completed')->count();
        $totalRevenue = Order::where('status', 'completed')->sum('total_price');

        // Statistik Bulan Terpilih
        $monthlyQuery = Order::whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth);

        $monthlyTotalOrders = (clone $monthlyQuery)->count();
        $monthlyCompletedOrders = (clone $monthlyQuery)->where('status', 'completed')->count();
        $monthlyRevenue = (clone $monthlyQuery)->where('status', 'completed')->sum('total_price');

        // Data Grafik Batang 6 Bulan Terakhir
        $chartLabels = [];
        $chartRevenues = [];
        $chartOrderCounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $dt = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->subMonths($i);
            $m = $dt->month;
            $y = $dt->year;

            $rev = Order::whereYear('created_at', $y)
                ->whereMonth('created_at', $m)
                ->where('status', 'completed')
                ->sum('total_price');

            $count = Order::whereYear('created_at', $y)
                ->whereMonth('created_at', $m)
                ->where('status', 'completed')
                ->count();

            $chartLabels[] = $dt->translatedFormat('M Y');
            $chartRevenues[] = (float) $rev;
            $chartOrderCounts[] = (int) $count;
        }

        $sellers = Seller::with('user')
            ->where('status', 'approved')
            ->withCount(['orders', 'products'])
            ->get();

        $recentSales = Order::with(['buyer', 'seller.user', 'items.product'])
            ->when($request->filled('month'), function ($q) use ($selectedMonth, $selectedYear) {
                $q->whereYear('created_at', $selectedYear)
                  ->whereMonth('created_at', $selectedMonth);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.sales', compact(
            'totalOrders',
            'completedOrdersCount',
            'totalRevenue',
            'monthlyTotalOrders',
            'monthlyCompletedOrders',
            'monthlyRevenue',
            'selectedMonth',
            'selectedYear',
            'chartLabels',
            'chartRevenues',
            'chartOrderCounts',
            'sellers',
            'recentSales'
        ));
    }
}

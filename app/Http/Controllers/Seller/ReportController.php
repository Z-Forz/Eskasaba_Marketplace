<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Seller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Tampilkan Laporan Penjualan Toko Seller.
     */
    public function sales(Request $request): View
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();

        $selectedMonth = (int) $request->input('month', date('n'));
        $selectedYear = (int) $request->input('year', date('Y'));

        // Status pesanan yang valid untuk omzet & penjualan
        $validStatuses = ['confirmed', 'processing', 'ready_for_pickup', 'completed'];

        // 1. Statistik Keseluruhan Toko (All Time)
        $totalOrders = Order::where('seller_id', $seller->id)->count();
        $completedOrdersCount = Order::where('seller_id', $seller->id)->where('status', 'completed')->count();
        $totalRevenue = Order::where('seller_id', $seller->id)
            ->whereIn('status', $validStatuses)
            ->sum('total_price');

        $totalItemsSoldAllTime = OrderItem::whereHas('order', function ($q) use ($seller, $validStatuses) {
            $q->where('seller_id', $seller->id)->whereIn('status', $validStatuses);
        })->sum('quantity');

        // 2. Statistik Bulan Terpilih
        $monthlyQuery = Order::where('seller_id', $seller->id)
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth);

        $monthlyTotalOrders = (clone $monthlyQuery)->count();
        $monthlyCompletedOrders = (clone $monthlyQuery)->where('status', 'completed')->count();
        $monthlyRevenue = (clone $monthlyQuery)
            ->whereIn('status', $validStatuses)
            ->sum('total_price');

        $monthlyItemsSold = OrderItem::whereHas('order', function ($q) use ($seller, $validStatuses, $selectedYear, $selectedMonth) {
            $q->where('seller_id', $seller->id)
              ->whereIn('status', $validStatuses)
              ->whereYear('created_at', $selectedYear)
              ->whereMonth('created_at', $selectedMonth);
        })->sum('quantity');

        // 3. Data Grafik Batang 6 Bulan Terakhir (Seller)
        $chartLabels = [];
        $chartRevenues = [];
        $chartOrderCounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $dt = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->subMonths($i);
            $m = $dt->month;
            $y = $dt->year;

            $rev = Order::where('seller_id', $seller->id)
                ->whereYear('created_at', $y)
                ->whereMonth('created_at', $m)
                ->whereIn('status', $validStatuses)
                ->sum('total_price');

            $count = Order::where('seller_id', $seller->id)
                ->whereYear('created_at', $y)
                ->whereMonth('created_at', $m)
                ->whereIn('status', $validStatuses)
                ->count();

            $chartLabels[] = $dt->translatedFormat('M Y');
            $chartRevenues[] = (float) $rev;
            $chartOrderCounts[] = (int) $count;
        }

        // 4. Produk Terlaris Toko (Filtered Month & Year)
        $topProducts = OrderItem::whereHas('order', function ($q) use ($seller, $validStatuses, $selectedYear, $selectedMonth) {
            $q->where('seller_id', $seller->id)
              ->whereIn('status', $validStatuses)
              ->whereYear('created_at', $selectedYear)
              ->whereMonth('created_at', $selectedMonth);
        })
            ->select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(quantity * price) as total_revenue'))
            ->groupBy('product_id', 'product_name')
            ->with('product.images')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 5. Riwayat Transaksi Penjualan
        $recentSales = Order::with(['user', 'items.product', 'payment', 'pickupSchedule'])
            ->where('seller_id', $seller->id)
            ->when($request->filled('month'), function ($q) use ($selectedMonth, $selectedYear) {
                $q->whereYear('created_at', $selectedYear)
                  ->whereMonth('created_at', $selectedMonth);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('seller.reports.sales', compact(
            'seller',
            'totalOrders',
            'completedOrdersCount',
            'totalRevenue',
            'totalItemsSoldAllTime',
            'monthlyTotalOrders',
            'monthlyCompletedOrders',
            'monthlyRevenue',
            'monthlyItemsSold',
            'selectedMonth',
            'selectedYear',
            'chartLabels',
            'chartRevenues',
            'chartOrderCounts',
            'topProducts',
            'recentSales'
        ));
    }
}

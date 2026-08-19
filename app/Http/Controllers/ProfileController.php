<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Statistik pesanan & keranjang untuk Dashboard & Profil terpadu
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
            ->with('items.product')
            ->latest()
            ->limit(5)
            ->get();

        // Riwayat login & aktivitas terkini
        $recentActivityLogs = $user->activityLogs()
            ->take(5)
            ->get();

        return view('profile.index', compact(
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'cartCount',
            'recentOrders',
            'recentActivityLogs'
        ));
    }

    /**
     * Tampilkan halaman lengkap Riwayat Login & Aktivitas Akun.
     */
    public function activityLogs(): View
    {
        $user = Auth::user();
        $logs = $user->activityLogs()->paginate(15);

        return view('profile.activity-logs', compact('logs'));
    }

    public function edit(): View
    {
        return view('profile.edit');
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user->update($data);

        ActivityLog::record(
            $user->id,
            'profile_updated',
            'Informasi profil akun (email/telepon) diperbarui',
            $request
        );

        return redirect()->route('profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}

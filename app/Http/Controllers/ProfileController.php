<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Services\ImageCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

        return view('profile.index', compact(
            'totalOrders',
            'pendingOrders',
            'completedOrders',
            'cartCount',
            'recentOrders'
        ));
    }

    public function edit(): View
    {
        return view('profile.edit');
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'email'  => ['nullable', 'email', 'max:255'],
            'phone'  => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'max:10240'],
        ]);

        if ($request->hasFile('avatar')) {
            $path = ImageCompressor::compressAndStore($request->file('avatar'), 'avatars');

            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $path;
        }

        $user->update($data);

        return redirect()->route('profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}

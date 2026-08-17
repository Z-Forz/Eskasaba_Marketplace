<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PickupScheduleController extends Controller
{
    /**
     * Display a listing of pickup schedules for active (non-completed) orders.
     */
    public function index(Request $request): View
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        $status = $request->input('status', 'active');

        $query = Order::with([
            'user',
            'items.product',
            'payment',
            'pickupSchedule',
        ])
        ->where('seller_id', $seller->id)
        ->whereNotNull('pickup_location');

        if ($status === 'active') {
            // Default view: exclude completed & cancelled orders so completed orders will clear out automatically
            $query->whereNotIn('status', ['completed', 'cancelled']);
        } elseif (in_array($status, ['pending', 'confirmed', 'processing', 'ready_for_pickup', 'completed', 'cancelled'])) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        $activeCount = Order::where('seller_id', $seller->id)
            ->whereNotNull('pickup_location')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        return view('seller.pickup-schedules.index', compact('orders', 'status', 'activeCount'));
    }

    /**
     * Display the specified order pickup schedule.
     */
    public function show(Order $order): View
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($order->seller_id === $seller->id, 403);

        $order->load([
            'user',
            'items.product',
            'payment',
            'pickupSchedule',
        ]);

        return view('seller.pickup-schedules.show', compact('order'));
    }

    /**
     * Update pickup location and status directly.
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $seller = Seller::where('user_id', Auth::id())->firstOrFail();
        abort_unless($order->seller_id === $seller->id, 403);

        $data = $request->validate([
            'pickup_location' => ['required', 'string', 'max:255'],
            'status'          => ['nullable', 'in:pending,confirmed,processing,ready_for_pickup,completed,cancelled'],
        ]);

        $order->update(array_filter($data, fn ($v) => ! is_null($v)));

        return back()->with('success', 'Lokasi & status pengambilan pesanan berhasil diperbarui.');
    }
}
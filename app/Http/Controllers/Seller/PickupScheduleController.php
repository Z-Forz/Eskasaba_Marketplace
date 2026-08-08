<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\PickupScheduleRequest;
use App\Models\Order;
use App\Models\PickupSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PickupScheduleController extends Controller
{
    /**
     * Display a listing of pickup schedules.
     */
    public function index(): View
    {
        $pickupSchedules = PickupSchedule::with([
            'order',
            'order.user',
        ])
        ->latest()
        ->paginate(10);

        return view('seller.pickups.index', compact(
            'pickupSchedules'
        ));
    }

    /**
     * Show the form for creating a pickup schedule.
     */
    public function create(Order $order): View
    {
        return view('seller.pickups.create', compact(
            'order'
        ));
    }

    /**
     * Store a newly created pickup schedule.
     */
    public function store(
        PickupScheduleRequest $request
    ): RedirectResponse {

        PickupSchedule::create(
            $request->validated()
        );

        return redirect()
            ->route('seller.pickups.index')
            ->with('success', 'Jadwal pengambilan berhasil dibuat.');
    }

    /**
     * Display the specified pickup schedule.
     */
    public function show(
        PickupSchedule $pickupSchedule
    ): View {

        return view('seller.pickups.show', compact(
            'pickupSchedule'
        ));
    }

    /**
     * Show the form for editing the pickup schedule.
     */
    public function edit(
        PickupSchedule $pickupSchedule
    ): View {

        return view('seller.pickups.edit', compact(
            'pickupSchedule'
        ));
    }

    /**
     * Update the specified pickup schedule.
     */
    public function update(
        PickupScheduleRequest $request,
        PickupSchedule $pickupSchedule
    ): RedirectResponse {

        $pickupSchedule->update(
            $request->validated()
        );

        return redirect()
            ->route('seller.pickups.index')
            ->with('success', 'Jadwal pengambilan berhasil diperbarui.');
    }

    /**
     * Mark pickup as completed.
     */
    public function complete(
        PickupSchedule $pickupSchedule
    ): RedirectResponse {

        $pickupSchedule->update([
            'is_picked_up' => true,
            'picked_up_at' => now(),
        ]);

        return redirect()
            ->route('seller.pickups.index')
            ->with('success', 'Barang berhasil diserahkan.');
    }
}
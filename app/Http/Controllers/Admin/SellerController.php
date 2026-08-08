<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SellerController extends Controller
{
    /**
     * Display a listing of sellers.
     */
    public function index(): View
    {
        $sellers = Seller::with('user')
            ->latest()
            ->paginate(10);

        return view('admin.sellers.index', compact(
            'sellers'
        ));
    }

    /**
     * Display the specified seller.
     */
    public function show(Seller $seller): View
    {
        $seller->load('user');

        return view('admin.sellers.show', compact(
            'seller'
        ));
    }

    /**
     * Approve seller registration.
     */
    public function approve(Seller $seller): RedirectResponse
    {
        $seller->update([
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('admin.sellers.index')
            ->with('success', 'Seller berhasil disetujui.');
    }

    /**
     * Reject seller registration.
     */
    public function reject(Seller $seller): RedirectResponse
    {
        $seller->update([
            'status' => 'rejected',
            'approved_at' => null,
        ]);

        return redirect()
            ->route('admin.sellers.index')
            ->with('success', 'Seller berhasil ditolak.');
    }
}
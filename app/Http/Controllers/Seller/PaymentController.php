<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments.
     */
    public function index(): View
    {
        $seller = Seller::where(
            'user_id',
            Auth::id()
        )->firstOrFail();

        $payments = Payment::with([
            'order.user',
        ])
        ->whereHas('order', function ($query) use ($seller) {
            $query->where(
                'seller_id',
                $seller->id
            );
        })
        ->latest()
        ->paginate(10);

        return view('seller.payments.index', compact(
            'payments'
        ));
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment): View
    {
        $payment->load([
            'order',
            'order.user',
        ]);

        return view('seller.payments.show', compact(
            'payment'
        ));
    }

    /**
     * Verify QRIS payment.
     */
    public function verify(Payment $payment): RedirectResponse
    {
        if ($payment->method === 'qris') {

            $payment->update([
                'status'      => 'verified',
                'verified_at' => now(),
            ]);

        }

        return redirect()
            ->route('seller.payments.index')
            ->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    /**
     * Reject QRIS payment.
     */
    public function reject(Payment $payment): RedirectResponse
    {
        if ($payment->method === 'qris') {

            $payment->update([
                'status' => 'rejected',
            ]);

        }

        return redirect()
            ->route('seller.payments.index')
            ->with('success', 'Pembayaran berhasil ditolak.');
    }
}
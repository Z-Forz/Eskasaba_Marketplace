<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments.
     */
    public function index(): View
    {
        $payments = Payment::latest()->paginate(15);
        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment): View
    {
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Update the specified payment (e.g., status).
     */
    public function update(Payment $payment): RedirectResponse
    {
        // For now, simply toggle status or mark as verified. Adjust as needed.
        $payment->update(['status' => 'verified']);
        return redirect()->route('admin.payments.index')
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }
}

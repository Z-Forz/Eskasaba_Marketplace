<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments with status filter tabs.
     */
    public function index(Request $request): View
    {
        $status = $request->input('status', 'all');

        $query = Payment::with(['order.user', 'order.seller.user'])->latest();

        if ($status === 'verified') {
            $query->whereIn('status', ['verified', 'paid']);
        } elseif (in_array($status, ['pending', 'rejected'])) {
            $query->where('status', $status);
        }

        $payments = $query->paginate(15)->withQueryString();

        $counts = [
            'all'      => Payment::count(),
            'pending'  => Payment::where('status', 'pending')->count(),
            'verified' => Payment::whereIn('status', ['verified', 'paid'])->count(),
            'rejected' => Payment::where('status', 'rejected')->count(),
        ];

        return view('admin.payments.index', compact('payments', 'status', 'counts'));
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment): View
    {
        $payment->load(['order.user', 'order.seller.user', 'order.items.product']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Update the specified payment (e.g., status).
     */
    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,verified,paid,rejected'],
        ]);

        $newStatus = $request->input('status');
        $payment->update(['status' => $newStatus]);

        if (in_array($newStatus, ['verified', 'paid']) && $payment->order) {
            if ($payment->order->status === 'pending') {
                $payment->order->update(['status' => 'processing']);
            }
        }

        return redirect()->back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}

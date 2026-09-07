<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    /**
     * Handle incoming payment gateway callback/webhook.
     * Support formats: Midtrans, Tripay, QRIS, Xendit, Bank Webhooks, or custom JSON payloads.
     */
    public function handle(Request $request): JsonResponse
    {
        Log::info('PaymentCallback received payload:', $request->all());

        $invoiceNumber = $request->input('invoice_number')
            ?? $request->input('order_id')
            ?? $request->input('merchant_order_id')
            ?? $request->input('external_id')
            ?? $request->input('invoice');

        $statusPayload = strtolower(
            (string) ($request->input('status')
            ?? $request->input('transaction_status')
            ?? $request->input('payment_status')
            ?? $request->input('result')
            ?? '')
        );

        $amount = $request->input('amount') ?? $request->input('gross_amount') ?? $request->input('total_amount');

        if (! $invoiceNumber) {
            return response()->json([
                'status'  => false,
                'message' => 'Invoice number or order ID is required in callback payload.',
            ], 400);
        }

        // Cari order berdasarkan invoice_number atau ID
        $order = Order::with(['payment', 'seller.user', 'user', 'items.product'])
            ->where('invoice_number', $invoiceNumber)
            ->orWhere('id', $invoiceNumber)
            ->first();

        if (! $order) {
            return response()->json([
                'status'  => false,
                'message' => "Order with invoice {$invoiceNumber} not found.",
            ], 404);
        }

        // Tentukan status pembayaran (paid / settled / success vs failed / expired / cancel)
        $isPaid = in_array($statusPayload, ['paid', 'settlement', 'success', 'verified', 'completed', '200', '00']);
        $isFailed = in_array($statusPayload, ['failed', 'expire', 'expired', 'cancel', 'cancelled', 'deny']);

        DB::transaction(function () use ($order, $isPaid, $isFailed, $amount) {
            $payment = $order->payment ?: new Payment(['order_id' => $order->id]);

            if ($amount) {
                $payment->amount = $amount;
            }

            if ($isPaid) {
                $payment->status = 'verified';
                $payment->verified_at = now();
                $order->status = 'confirmed';
            } elseif ($isFailed) {
                $payment->status = 'failed';
                $order->status = 'cancelled';
            } else {
                $payment->status = 'pending';
            }

            $payment->save();
            $order->save();
        });

        // Trigger notifications
        if ($isPaid) {
            if ($order->seller?->user_id) {
                Notification::create([
                    'user_id' => $order->seller->user_id,
                    'title'   => 'Pembayaran Terkonfirmasi Callback',
                    'message' => "Pembayaran pesanan #{$order->invoice_number} telah diverifikasi otomatis via Callback.",
                    'type'    => 'payment_verified',
                    'link'    => route('seller.orders.show', $order),
                ]);
            }

            Notification::create([
                'user_id' => $order->user_id,
                'title'   => 'Pembayaran Berhasil',
                'message' => "Pembayaran untuk pesanan #{$order->invoice_number} berhasil dikonfirmasi.",
                'type'    => 'payment_verified',
                'link'    => route('buyer.orders.show', $order),
            ]);

            WhatsAppService::sendNewOrderNotification($order);
        }

        return response()->json([
            'status'         => true,
            'message'        => 'Callback processed successfully.',
            'invoice_number' => $order->invoice_number,
            'order_status'   => $order->status,
            'payment_status' => $order->payment?->status,
        ]);
    }
}

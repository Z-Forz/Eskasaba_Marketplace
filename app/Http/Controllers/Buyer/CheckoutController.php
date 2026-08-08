<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Display checkout page.
     */
    public function index(): View
    {
        $cart = Cart::with([
            'items.product.seller.user',
        ])
        ->where('user_id', Auth::id())
        ->firstOrFail();

        if ($cart->items->isEmpty()) {
            abort(404, 'Keranjang kosong.');
        }

        $sellerIds = $cart->items
            ->pluck('product.seller_id')
            ->unique();

        if ($sellerIds->count() > 1) {
            return back()->with(
                'error',
                'Checkout hanya dapat dilakukan untuk satu seller.'
            );
        }

        return view('buyer.checkout.index', compact(
            'cart'
        ));
    }

    /**
     * Process checkout.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'payment_method' => [
                'required',
                'in:cod,qris',
            ],
        ]);

        $cart = Cart::with([
            'items.product',
        ])
        ->where('user_id', Auth::id())
        ->firstOrFail();

        // Dibungkus transaction: kalau salah satu langkah gagal
        // (order/order_items/payment), semuanya di-rollback bareng,
        // gak ada data nyangkut setengah jadi.
        $order = DB::transaction(function () use ($cart, $request) {

            $sellerId = $cart->items
                ->first()
                ->product
                ->seller_id;

            $totalPrice = $cart->items->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            $order = Order::create([
                'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'user_id'        => Auth::id(),
                'seller_id'      => $sellerId,
                'total_price'    => $totalPrice,
                'status'         => 'pending',
            ]);

            foreach ($cart->items as $item) {

                $order->items()->create([
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity'     => $item->quantity,
                    'price'        => $item->price,
                ]);

            }

            Payment::create([
                'order_id' => $order->id,
                'amount'   => $totalPrice,
                'method'   => $request->payment_method,
                'status'   => $request->payment_method === 'cod'
                    ? 'verified'
                    : 'pending',
            ]);

            $cart->items()->delete();

            return $order;
        });

        return redirect()
            ->route('buyer.orders.show', $order)
            ->with('success', 'Checkout berhasil.');
    }
}
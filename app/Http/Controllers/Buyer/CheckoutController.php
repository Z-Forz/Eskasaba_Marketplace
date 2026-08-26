<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PickupSchedule;
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
    public function index(): View|RedirectResponse
    {
        $cart = Cart::with([
            'items.product.seller.user',
        ])
        ->where('user_id', Auth::id())
        ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('buyer.cart.index')
                ->with('error', 'Keranjang kamu masih kosong.');
        }

        // Prevent seller from checking out own products
        $isOwnSeller = $cart->items->contains(function ($item) {
            return $item->product && $item->product->seller && $item->product->seller->user_id === Auth::id();
        });

        if ($isOwnSeller) {
            return redirect()
                ->route('buyer.cart.index')
                ->with('error', 'Kamu tidak dapat melakukan checkout pada produk tokomu sendiri.');
        }

        $sellerIds = $cart->items
            ->pluck('product.seller_id')
            ->unique();

        if ($sellerIds->count() > 1) {
            return redirect()
                ->route('buyer.cart.index')
                ->with('error', 'Checkout hanya dapat dilakukan untuk satu seller.');
        }

        return view('buyer.checkout.index', compact(
            'cart'
        ));
    }

    /**
     * Process checkout and decrement product stock automatically.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'pickup_location' => [
                'required',
                'string',
                'max:255',
            ],
            'payment_method' => [
                'required',
                'in:cod,qris',
            ],
            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'pickup_location.required' => 'Lokasi/titik pengambilan wajib diisi.',
            'payment_method.required'  => 'Metode pembayaran wajib dipilih.',
        ]);

        $cart = Cart::with([
            'items.product.seller',
        ])
        ->where('user_id', Auth::id())
        ->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('buyer.cart.index')
                ->with('error', 'Keranjang kamu masih kosong.');
        }

        // Prevent seller from checking out own products
        $isOwnSeller = $cart->items->contains(function ($item) {
            return $item->product && $item->product->seller && $item->product->seller->user_id === Auth::id();
        });

        if ($isOwnSeller) {
            return redirect()
                ->route('buyer.cart.index')
                ->with('error', 'Kamu tidak dapat melakukan checkout pada produk tokomu sendiri.');
        }

        try {
            // Dibungkus transaction untuk integritas data
            $order = DB::transaction(function () use ($cart, $request) {

                $sellerId = $cart->items
                    ->first()
                    ->product
                    ->seller_id;

                $totalPrice = $cart->items->sum(function ($item) {
                    return $item->price * $item->quantity;
                });

                $order = Order::create([
                    'invoice_number'  => 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'user_id'         => Auth::id(),
                    'seller_id'       => $sellerId,
                    'total_price'     => $totalPrice,
                    'pickup_location' => $request->pickup_location,
                    'note'            => $request->note,
                    'status'          => 'pending',
                ]);

                foreach ($cart->items as $item) {
                    $product = $item->product;

                    if ($product->stock < $item->quantity) {
                        throw new \Exception("Stok produk '{$product->name}' tidak mencukupi (Sisa stok: {$product->stock}).");
                    }

                    $order->items()->create([
                        'product_id'   => $item->product_id,
                        'product_name' => $product->name,
                        'quantity'     => $item->quantity,
                        'price'        => $item->price,
                        'note'         => $item->note,
                    ]);

                    // Otomatis kurangi stok produk setelah pembeli berhasil checkout
                    $product->decrement('stock', $item->quantity);
                }

                Payment::create([
                    'order_id' => $order->id,
                    'amount'   => $totalPrice,
                    'method'   => $request->payment_method,
                    'status'   => $request->payment_method === 'cod'
                        ? 'verified'
                        : 'pending',
                ]);

                // Create default Pickup Schedule for the order
                PickupSchedule::create([
                    'order_id'     => $order->id,
                    'pickup_date'  => now()->addDays(1)->format('Y-m-d'),
                    'pickup_time'  => '10:00',
                    'is_picked_up' => false,
                ]);

                $cart->items()->delete();

                return $order;
            });
        } catch (\Exception $e) {
            return redirect()
                ->route('buyer.cart.index')
                ->with('error', $e->getMessage());
        }

        // Send notification to Seller
        if ($order->seller?->user_id) {
            \App\Models\Notification::create([
                'user_id' => $order->seller->user_id,
                'title'   => 'Pesanan Baru Masuk!',
                'message' => 'Anda mendapatkan pesanan baru dengan invoice #' . $order->invoice_number,
                'type'    => 'new_order',
                'link'    => route('seller.orders.show', $order),
            ]);
        }

        // Send notification to Buyer
        \App\Models\Notification::create([
            'user_id' => Auth::id(),
            'title'   => 'Pesanan Berhasil Dibuat',
            'message' => 'Pesanan #' . $order->invoice_number . ' telah berhasil dibuat. Silakan hubungi penjual.',
            'type'    => 'order_created',
            'link'    => route('buyer.orders.show', $order),
        ]);

        // Send WhatsApp Notification to Seller & Buyer
        \App\Services\WhatsAppService::sendNewOrderNotification($order);

        return redirect()
            ->route('buyer.orders.show', $order)
            ->with('success', 'Pesanan berhasil dibuat. Silakan hubungi penjual untuk konfirmasi pengambilan.');
    }
}
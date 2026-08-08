<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Display the shopping cart.
     */
    public function index(): View
    {
        $cart = Cart::with([
            'items.product.images',
            'items.product.seller.user',
        ])
        ->firstOrCreate([
            'user_id' => Auth::id(),
        ]);

        return view('buyer.cart.index', compact(
            'cart'
        ));
    }

    /**
     * Add product to cart.
     */
    public function store(Product $product): RedirectResponse
    {
        $cart = Cart::firstOrCreate([
            'user_id' => Auth::id(),
        ]);

        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {

            $cartItem->increment('quantity');

        } else {

            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => 1,
                'price'      => $product->price,
            ]);

        }

        return redirect()
            ->route('buyer.cart.index')
            ->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Update cart item.
     */
    public function update(int $id): RedirectResponse
    {
        request()->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $cart = Cart::where(
            'user_id',
            Auth::id()
        )->firstOrFail();

        $cartItem = $cart->items()
            ->findOrFail($id);

        $cartItem->update([
            'quantity' => request('quantity'),
        ]);

        return back()->with(
            'success',
            'Jumlah produk berhasil diperbarui.'
        );
    }

    /**
     * Remove product from cart.
     */
    public function destroy(int $id): RedirectResponse
    {
        $cart = Cart::where(
            'user_id',
            Auth::id()
        )->firstOrFail();

        $cart->items()
            ->findOrFail($id)
            ->delete();

        return back()->with(
            'success',
            'Produk berhasil dihapus dari keranjang.'
        );
    }
}
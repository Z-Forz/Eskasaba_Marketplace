<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     * Add product to cart with flavor/variant selection support.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id'   => ['required', 'exists:products,id'],
            'quantity'     => ['nullable', 'integer', 'min:1'],
            'variant_name' => ['nullable', 'string', 'max:255'],
            'note'         => ['nullable', 'string', 'max:255'],
        ]);

        $productId   = $request->input('product_id');
        $quantity    = max(1, (int) $request->input('quantity', 1));
        $variantName = $request->input('variant_name') ?: $request->input('note');
        $note        = $request->input('note');

        $product = Product::with('seller')->findOrFail($productId);

        // Prevent seller from buying their own product
        if (Auth::check() && $product->seller && $product->seller->user_id === Auth::id()) {
            return back()->with('error', 'Kamu tidak dapat membeli produk dari tokomu sendiri.');
        }

        // Determine correct item price based on selected variant and check stock
        $itemPrice = $product->final_price;
        $availableStock = (int) $product->stock;

        if ($product->hasVariants() && !empty($variantName)) {
            foreach ($product->variants as $var) {
                if (isset($var['name']) && strcasecmp(trim($var['name']), trim($variantName)) === 0) {
                    $itemPrice = (float) $var['price'];
                    if (isset($var['stock'])) {
                        $availableStock = (int) $var['stock'];
                    }
                    break;
                }
            }
        }

        if ($availableStock <= 0) {
            return back()->with('error', 'Stok untuk varian / produk ini sudah habis.');
        }

        $cart = Cart::firstOrCreate([
            'user_id' => Auth::id(),
        ]);

        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->where(function($q) use ($variantName, $note) {
                $q->where('variant_name', $variantName)
                  ->orWhere('note', $note);
            })
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            $cart->items()->create([
                'product_id'   => $product->id,
                'variant_name' => $variantName,
                'quantity'     => $quantity,
                'price'        => $itemPrice,
                'note'         => $note,
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
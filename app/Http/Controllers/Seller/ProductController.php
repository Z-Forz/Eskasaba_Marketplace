<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(): View
    {
        $seller = Seller::where(
            'user_id',
            Auth::id()
        )->firstOrFail();

        $products = Product::with([
            'category',
            'images',
        ])
        ->where('seller_id', $seller->id)
        ->latest()
        ->paginate(10);

        return view('seller.products.index', compact(
            'products'
        ));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('seller.products.create', compact(
            'categories'
        ));
    }

    /**
     * Store a newly created product.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $seller = Seller::where(
            'user_id',
            Auth::id()
        )->firstOrFail();

        Product::create([
            ...$request->validated(),
            'seller_id' => $seller->id,
        ]);

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $product->load([
            'category',
            'images',
        ]);

        return view('seller.products.show', compact(
            'product'
        ));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('seller.products.edit', compact(
            'product',
            'categories'
        ));
    }

    /**
     * Update the specified product.
     */
    public function update(
        ProductRequest $request,
        Product $product
    ): RedirectResponse {

        $product->update(
            $request->validated()
        );

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
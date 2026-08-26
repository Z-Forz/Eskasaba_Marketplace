<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Services\ImageCompressor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

        $uploadedFiles = $request->file('images') ?? [];
        if (count($uploadedFiles) > 5) {
            return back()->withErrors(['images' => 'Maksimal foto produk adalah 5 foto.'])->withInput();
        }

        $product = Product::create([
            ...$request->validated(),
            'seller_id' => $seller->id,
        ]);

        if (!empty($uploadedFiles)) {
            foreach ($uploadedFiles as $file) {
                $path = ImageCompressor::compressAndStore($file, 'products');
                $product->images()->create([
                    'image' => $path,
                ]);
            }
        }

        return redirect()
            ->route('seller.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Redirect product detail to public product page.
     */
    public function show(Product $product): RedirectResponse
    {
        return redirect()->route('products.show', $product);
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

        // 1. Delete requested existing images
        if ($request->has('delete_images') && is_array($request->delete_images)) {
            $imagesToDelete = $product->images()->whereIn('id', $request->delete_images)->get();
            foreach ($imagesToDelete as $img) {
                if ($img->image && Storage::disk('public')->exists($img->image)) {
                    Storage::disk('public')->delete($img->image);
                }
                $img->delete();
            }
        }

        // 2. Validate total remaining + new images <= 5
        $currentCount = $product->images()->count();
        $uploadedFiles = $request->file('images') ?? [];
        if ($currentCount + count($uploadedFiles) > 5) {
            return back()->withErrors(['images' => 'Total foto produk tidak boleh melebihi 5 foto.'])->withInput();
        }

        // 3. Upload and save new images
        if (!empty($uploadedFiles)) {
            foreach ($uploadedFiles as $file) {
                $path = ImageCompressor::compressAndStore($file, 'products');
                $product->images()->create([
                    'image' => $path,
                ]);
            }
        }

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
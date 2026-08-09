<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index(Request $request): View
    {
        $keyword = $request->keyword;

        $categories = Category::orderBy('name')->get();

        $products = Product::with([
            'seller.user',
            'category',
            'images',
        ])
        ->when($keyword, function ($query) use ($keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        })
        ->latest()
        ->paginate(12)
        ->withQueryString();

        return view('home.index', compact(
            'products',
            'categories',
            'keyword'
        ));
    }

    /**
     * Display product detail.
     */
    public function show(Product $product): View
    {
        $product->load([
            'seller.user',
            'category',
            'images',
            'reviews.user',
        ]);

        return view('products.show', compact(
            'product'
        ));
    }
}
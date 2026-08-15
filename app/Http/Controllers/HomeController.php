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
     * Display the products catalog page.
     */
    public function products(Request $request): View
    {
        $search = $request->input('search');
        $categoryId = $request->input('category');
        $sort = $request->input('sort');

        $categories = Category::orderBy('name')->get();

        $products = Product::with([
            'seller.user',
            'category',
            'images',
        ])
        ->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })
        ->when($categoryId, function ($query) use ($categoryId) {
            $query->where('category_id', $categoryId);
        })
        ->when($sort, function ($query) use ($sort) {
            match ($sort) {
                'price_low'  => $query->orderBy('price', 'asc'),
                'price_high' => $query->orderBy('price', 'desc'),
                'name'       => $query->orderBy('name', 'asc'),
                default      => $query->latest(),
            };
        }, function ($query) {
            $query->latest();
        })
        ->paginate(12)
        ->withQueryString();

        return view('products.index', compact(
            'products',
            'categories',
            'search',
            'categoryId',
            'sort'
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
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Seller;
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

        $categories = Category::withCount('products')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderByDesc('products_count')
            ->orderByRaw('COALESCE(reviews_avg_rating, 0) DESC')
            ->orderByDesc('reviews_count')
            ->orderBy('name')
            ->take(8)
            ->get();

        $products = Product::with([
            'seller.user',
            'category',
            'images',
        ])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->withSum(['orderItems as order_items_sum_quantity' => function ($query) {
            $query->whereHas('order', function ($q) {
                $q->whereIn('status', ['confirmed', 'completed', 'paid', 'processing']);
            });
        }], 'quantity')
        ->when($keyword, function ($query) use ($keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        })
        ->latest()
        ->paginate(12)
        ->withQueryString();

        // Featured / Unggulan & Terlaris products (diurutkan berdasarkan terbanyak pesanan & rating tertinggi)
        $featuredProducts = Product::with([
            'seller.user',
            'category',
            'images',
        ])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->withSum(['orderItems as order_items_sum_quantity' => function ($query) {
            $query->whereHas('order', function ($q) {
                $q->whereIn('status', ['confirmed', 'completed', 'paid', 'processing']);
            });
        }], 'quantity')
        ->orderByRaw('COALESCE(order_items_sum_quantity, 0) DESC')
        ->orderByRaw('COALESCE(reviews_avg_rating, 0) DESC')
        ->orderBy('reviews_count', 'desc')
        ->latest()
        ->take(8)
        ->get();

        return view('home.index', compact(
            'products',
            'featuredProducts',
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

        $categories = Category::withCount('products')->orderBy('name')->get();

        $products = Product::with([
            'seller.user',
            'category',
            'images',
        ])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->withSum(['orderItems as order_items_sum_quantity' => function ($query) {
            $query->whereHas('order', function ($q) {
                $q->whereIn('status', ['confirmed', 'completed', 'paid', 'processing']);
            });
        }], 'quantity')
        ->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })
        ->when($categoryId, function ($query) use ($categoryId) {
            $query->where('category_id', $categoryId);
        })
        ->when($sort, function ($query) use ($sort) {
            match ($sort) {
                'price_low'   => $query->orderBy('price', 'asc'),
                'price_high'  => $query->orderBy('price', 'desc'),
                'name'        => $query->orderBy('name', 'asc'),
                'best_seller' => $query->orderByRaw('COALESCE(order_items_sum_quantity, 0) DESC')
                                        ->orderByRaw('COALESCE(reviews_avg_rating, 0) DESC'),
                'rating'      => $query->orderByRaw('COALESCE(reviews_avg_rating, 0) DESC')
                                        ->orderByRaw('COALESCE(order_items_sum_quantity, 0) DESC'),
                default       => $query->latest(),
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
        ])
        ->loadAvg('reviews', 'rating')
        ->loadCount('reviews');

        return view('products.show', compact(
            'product'
        ));
    }

    /**
     * Display seller profile page with seller's products only.
     */
    public function sellerProfile(Request $request, Seller $seller): View
    {
        $seller->load('user')->loadCount('products');

        $search = $request->input('search');
        $categoryId = $request->input('category');
        $sort = $request->input('sort');

        // Categories associated with this seller's products
        $categories = Category::whereHas('products', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->withCount(['products' => function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        }])->orderBy('name')->get();

        // Products belonging ONLY to this seller
        $products = Product::where('seller_id', $seller->id)
            ->with([
                'seller.user',
                'category',
                'images',
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->withSum(['orderItems as order_items_sum_quantity' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->whereIn('status', ['confirmed', 'completed', 'paid', 'processing']);
                });
            }], 'quantity')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($sort, function ($query) use ($sort) {
                match ($sort) {
                    'price_low'   => $query->orderBy('price', 'asc'),
                    'price_high'  => $query->orderBy('price', 'desc'),
                    'name'        => $query->orderBy('name', 'asc'),
                    'best_seller' => $query->orderByRaw('COALESCE(order_items_sum_quantity, 0) DESC')
                                            ->orderByRaw('COALESCE(reviews_avg_rating, 0) DESC'),
                    'rating'      => $query->orderByRaw('COALESCE(reviews_avg_rating, 0) DESC')
                                            ->orderByRaw('COALESCE(order_items_sum_quantity, 0) DESC'),
                    default       => $query->latest(),
                };
            }, function ($query) {
                $query->latest();
            })
            ->paginate(12)
            ->withQueryString();

        // Calculate seller stats
        $totalSalesCount = $seller->orders()
            ->whereIn('status', ['confirmed', 'completed', 'processing', 'ready_for_pickup'])
            ->count();

        $sellerProductIds = Product::where('seller_id', $seller->id)->pluck('id');

        $avgSellerRating = Review::whereIn('product_id', $sellerProductIds)->avg('rating');
        $totalReviewsCount = Review::whereIn('product_id', $sellerProductIds)->count();

        $stats = [
            'total_products' => $seller->products_count,
            'total_sales'    => $totalSalesCount,
            'avg_rating'     => number_format($avgSellerRating ?: 0, 1),
            'total_reviews'  => $totalReviewsCount,
        ];

        return view('sellers.show', compact(
            'seller',
            'products',
            'categories',
            'search',
            'categoryId',
            'sort',
            'stats'
        ));
    }
}
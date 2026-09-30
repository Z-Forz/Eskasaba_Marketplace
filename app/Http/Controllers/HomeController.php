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

        $categories = \Illuminate\Support\Facades\Cache::remember('home_categories_v3', 300, function () {
            return Category::withCount('products')
                ->withAvg('reviews', 'rating')
                ->withCount('reviews')
                ->orderByDesc('products_count')
                ->orderBy('name')
                ->take(8)
                ->get();
        });

        if (empty($keyword)) {
            $products = \Illuminate\Support\Facades\Cache::remember('home_products_page_1', 180, function () {
                return Product::with([
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
                ->latest()
                ->paginate(12);
            });
        } else {
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
            ->where('name', 'like', "%{$keyword}%")
            ->latest()
            ->paginate(12)
            ->withQueryString();
        }

        // Featured / Unggulan & Terlaris products (diurutkan berdasarkan terbanyak pesanan & rating tertinggi)
        $featuredProducts = \Illuminate\Support\Facades\Cache::remember('home_featured_products_v3', 300, function () {
            return Product::with([
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
            ->latest()
            ->get()
            ->sortByDesc(function ($prod) {
                $sales = (int) ($prod->order_items_sum_quantity ?? 0);
                $rating = (float) ($prod->reviews_avg_rating ?? 0);
                $reviews = (int) ($prod->reviews_count ?? 0);
                return ($sales * 1000) + ($rating * 10) + $reviews;
            })
            ->take(8)
            ->values();
        });

        $settings = \App\Models\WebsiteSetting::first();

        return view('home.index', compact(
            'products',
            'featuredProducts',
            'categories',
            'keyword',
            'settings'
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
                'best_seller' => $query->orderByDesc('reviews_count')->latest(),
                'rating'      => $query->orderByDesc('reviews_count')->latest(),
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
                    'best_seller' => $query->orderByDesc('reviews_count')->latest(),
                    'rating'      => $query->orderByDesc('reviews_count')->latest(),
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
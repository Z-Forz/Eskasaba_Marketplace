<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Display a listing of the reviews.
     */
    public function index(): View
    {
        $reviews = Review::with([
            'product',
            'order',
        ])
        ->where('user_id', Auth::id())
        ->latest()
        ->paginate(10);

        return view('buyer.reviews.index', compact(
            'reviews'
        ));
    }

    /**
     * Show the form for creating a review.
     */
    public function create(Order $order): View
    {
        abort_unless($order->user_id === Auth::id(), 403);
        abort_unless($order->status === 'completed', 403, 'Pesanan belum selesai, belum bisa direview.');

        $order->load('items.product');

        return view('buyer.reviews.create', compact(
            'order'
        ));
    }

    /**
     * Store a newly created review.
     */
    public function store(
        ReviewRequest $request
    ): RedirectResponse {

        Review::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('buyer.orders.index')
            ->with('success', 'Review berhasil dikirim.');
    }
}
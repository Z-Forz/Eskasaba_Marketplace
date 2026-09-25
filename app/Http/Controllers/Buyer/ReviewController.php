<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Models\Order;
use App\Models\Review;
use App\Services\ImageCompressor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Display a listing of the reviews.
     */
    public function index(): View
    {
        $reviews = Review::with([
            'product.images',
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

        $order->load(['items.product.images']);

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

        $data = $request->validated();
        $data['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $data['image'] = ImageCompressor::compressAndStore(
                $request->file('image'),
                'review_images'
            );
        }

        Review::create($data);

        return redirect()
            ->back()
            ->with('success', 'Ulasan berhasil dikirim.');
    }

    /**
     * Show the form for editing the specified review.
     */
    public function edit(Review $review): View
    {
        abort_unless($review->user_id === Auth::id(), 403);

        $review->load(['product.images', 'order']);

        return view('buyer.reviews.edit', compact('review'));
    }

    /**
     * Update the specified review.
     */
    public function update(
        ReviewRequest $request,
        Review $review
    ): RedirectResponse {
        abort_unless($review->user_id === Auth::id(), 403);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($review->image && Storage::disk('public')->exists($review->image)) {
                Storage::disk('public')->delete($review->image);
            }
            $data['image'] = ImageCompressor::compressAndStore(
                $request->file('image'),
                'review_images'
            );
        } elseif ($request->boolean('remove_image')) {
            if ($review->image && Storage::disk('public')->exists($review->image)) {
                Storage::disk('public')->delete($review->image);
            }
            $data['image'] = null;
        }

        $review->update($data);

        return redirect()
            ->route('buyer.reviews.index')
            ->with('success', 'Ulasan berhasil diperbarui.');
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Review $review): RedirectResponse
    {
        abort_unless($review->user_id === Auth::id(), 403);

        if ($review->image && Storage::disk('public')->exists($review->image)) {
            Storage::disk('public')->delete($review->image);
        }

        $review->delete();

        return redirect()
            ->back()
            ->with('success', 'Ulasan berhasil dihapus.');
    }
}
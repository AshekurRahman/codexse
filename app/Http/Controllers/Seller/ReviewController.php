<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $seller = auth()->user()->seller;
        $productIds = $seller->products()->pluck('id');

        $query = Review::whereIn('product_id', $productIds)
            ->with(['user', 'product'])
            ->latest();

        // Filter by status
        if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        // Filter by rating
        if ($request->has('rating') && $request->rating >= 1 && $request->rating <= 5) {
            $query->where('rating', $request->rating);
        }

        // Filter by responded
        if ($request->has('responded')) {
            if ($request->responded === 'yes') {
                $query->whereNotNull('seller_response');
            } else {
                $query->whereNull('seller_response');
            }
        }

        $reviews = $query->paginate(20);

        // Get stats
        $stats = [
            'total' => Review::whereIn('product_id', $productIds)->count(),
            'pending_response' => Review::whereIn('product_id', $productIds)
                ->where('status', 'approved')
                ->whereNull('seller_response')
                ->count(),
            'average_rating' => Review::whereIn('product_id', $productIds)
                ->where('status', 'approved')
                ->avg('rating') ?? 0,
        ];

        return view('seller.reviews.index', compact('reviews', 'stats'));
    }

    public function respond(Request $request, Review $review)
    {
        $seller = auth()->user()->seller;

        // Verify seller owns the product
        if ($review->product->seller_id !== $seller->id) {
            abort(403, 'You can only respond to reviews for your own products.');
        }

        $validated = $request->validate([
            'seller_response' => 'required|string|min:10|max:1000',
        ]);

        $review->update([
            'seller_response' => $validated['seller_response'],
            'seller_responded_at' => now(),
        ]);

        return back()->with('success', 'Your response has been saved.');
    }

    public function deleteResponse(Review $review)
    {
        $seller = auth()->user()->seller;

        // Verify seller owns the product
        if ($review->product->seller_id !== $seller->id) {
            abort(403, 'You can only manage reviews for your own products.');
        }

        $review->update([
            'seller_response' => null,
            'seller_responded_at' => null,
        ]);

        return back()->with('success', 'Your response has been removed.');
    }
}

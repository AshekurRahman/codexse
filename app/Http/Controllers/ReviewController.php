<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Store a new review.
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:2000',
        ]);

        $user = auth()->user();

        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already reviewed this product.',
                ], 422);
            }
            return back()->with('error', 'You have already reviewed this product.');
        }

        // Check if user has purchased this product
        $orderItem = $user->orders()
            ->where('status', 'completed')
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->with(['items' => function ($query) use ($product) {
                $query->where('product_id', $product->id);
            }])
            ->first()?->items?->first();

        $isVerifiedPurchase = $orderItem !== null;

        $review = Review::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'order_item_id' => $orderItem?->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_verified_purchase' => $isVerifiedPurchase,
            'status' => 'pending', // Requires approval
        ]);

        $message = 'Thank you for your review! It will be visible after approval.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'review' => [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'status' => $review->status,
                    'is_verified_purchase' => $review->is_verified_purchase,
                ],
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Update an existing review.
     */
    public function update(Request $request, Review $review)
    {
        // Ensure user owns this review
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:2000',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'status' => 'pending', // Re-approve after edit
        ]);

        return back()->with('success', 'Your review has been updated and is pending approval.');
    }

    /**
     * Delete a review.
     */
    public function destroy(Review $review)
    {
        // Ensure user owns this review or is admin
        if ($review->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }

    /**
     * Vote a review as helpful.
     */
    public function vote(Request $request, Review $review)
    {
        $user = auth()->user();

        // Can't vote on your own review
        if ($review->user_id === $user->id) {
            return response()->json(['error' => 'You cannot vote on your own review'], 400);
        }

        // Check if already voted
        $existingVote = DB::table('review_votes')
            ->where('user_id', $user->id)
            ->where('review_id', $review->id)
            ->first();

        if ($existingVote) {
            // Remove vote
            DB::table('review_votes')
                ->where('user_id', $user->id)
                ->where('review_id', $review->id)
                ->delete();

            $review->decrement('helpful_count');

            return response()->json([
                'voted' => false,
                'helpful_count' => $review->fresh()->helpful_count,
            ]);
        }

        // Add vote
        DB::table('review_votes')->insert([
            'user_id' => $user->id,
            'review_id' => $review->id,
            'created_at' => now(),
        ]);

        $review->increment('helpful_count');

        return response()->json([
            'voted' => true,
            'helpful_count' => $review->fresh()->helpful_count,
        ]);
    }

    /**
     * Get reviews for a product (AJAX).
     */
    public function index(Product $product)
    {
        $reviews = $product->reviews()
            ->approved()
            ->with('user')
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }
}

<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        $this->updateProductStats($review);
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        $this->updateProductStats($review);
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        $this->updateProductStats($review);
    }

    /**
     * Update product review statistics.
     */
    protected function updateProductStats(Review $review): void
    {
        $product = $review->product;

        if (!$product) {
            return;
        }

        // Only count approved reviews
        $approvedReviews = $product->reviews()->where('status', 'approved');

        $reviewsCount = $approvedReviews->count();
        $averageRating = $reviewsCount > 0
            ? round($approvedReviews->avg('rating'), 2)
            : 0;

        $product->update([
            'reviews_count' => $reviewsCount,
            'average_rating' => $averageRating,
        ]);
    }
}

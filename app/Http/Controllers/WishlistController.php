<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function toggle(Product $product)
    {
        $user = auth()->user();

        $wishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $message = 'Removed from wishlist';
            $inWishlist = false;
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
            ]);
            $message = 'Added to wishlist';
            $inWishlist = true;
        }

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'in_wishlist' => $inWishlist,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Get share link for wishlist.
     */
    public function getShareLink()
    {
        $user = auth()->user();
        $shareUrl = $user->wishlist_share_url;

        return response()->json([
            'success' => true,
            'share_url' => $shareUrl,
            'is_public' => $user->wishlist_public,
        ]);
    }

    /**
     * Toggle wishlist visibility (public/private).
     */
    public function toggleVisibility()
    {
        $user = auth()->user();
        $user->wishlist_public = !$user->wishlist_public;
        $user->save();

        return response()->json([
            'success' => true,
            'is_public' => $user->wishlist_public,
            'message' => $user->wishlist_public
                ? 'Your wishlist is now public'
                : 'Your wishlist is now private',
        ]);
    }

    /**
     * Regenerate share link.
     */
    public function regenerateShareLink()
    {
        $user = auth()->user();
        $user->regenerateWishlistShareToken();

        return response()->json([
            'success' => true,
            'share_url' => $user->wishlist_share_url,
            'message' => 'Share link has been regenerated',
        ]);
    }

    /**
     * View shared wishlist.
     */
    public function viewShared(string $token)
    {
        $user = User::where('wishlist_share_token', $token)->first();

        if (!$user) {
            abort(404, 'Wishlist not found');
        }

        if (!$user->wishlist_public) {
            abort(403, 'This wishlist is private');
        }

        $wishlists = Wishlist::with(['product.seller', 'product.category'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(12);

        return view('pages.wishlist-shared', [
            'wishlists' => $wishlists,
            'owner' => $user,
        ]);
    }
}

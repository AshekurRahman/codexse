<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Service;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index(): View
    {
        $featuredProducts = Product::with(['seller', 'category'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->latest()
            ->take(8)
            ->get();

        // Cache categories for 1 hour
        $categories = Cache::remember('home_categories', 3600, function () {
            return Category::withCount([
                    'products' => function ($query) {
                        $query->where('status', 'published');
                    }
                ])
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get();
        });

        $trendingProducts = Product::with(['seller', 'category'])
            ->where('status', 'published')
            ->orderByDesc('views_count')
            ->take(4)
            ->get();

        // Featured services
        $featuredServices = Service::with(['seller.user', 'category', 'packages'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->latest()
            ->take(4)
            ->get();

        // Recent job postings
        $recentJobs = JobPosting::with(['client', 'category'])
            ->where('status', 'open')
            ->latest()
            ->take(4)
            ->get();

        // Recently viewed products
        $recentlyViewed = RecentlyViewedController::getProducts(8);

        // Featured sellers
        $featuredSellers = Seller::with('user')
            ->withCount([
                'products' => function ($query) {
                    $query->where('status', 'published');
                },
                'services' => function ($query) {
                    $query->where('status', 'published');
                }
            ])
            ->where('status', 'approved')
            ->where('is_verified', true)
            ->orderByDesc('total_sales')
            ->take(6)
            ->get();

        // Pre-load wishlist IDs for authenticated users (single query)
        $wishlistIds = auth()->check()
            ? auth()->user()->wishlists()->pluck('product_id')
            : collect();

        return view('pages.home', compact(
            'featuredProducts',
            'categories',
            'trendingProducts',
            'featuredServices',
            'recentJobs',
            'recentlyViewed',
            'featuredSellers',
            'wishlistIds'
        ));
    }
}

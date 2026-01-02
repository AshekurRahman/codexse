<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Models\Service;
use App\Models\JobPosting;
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

        $categories = Category::withCount('products')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

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
            ->withCount('products')
            ->where('status', 'approved')
            ->where('is_verified', true)
            ->orderByDesc('total_sales')
            ->take(6)
            ->get();

        return view('pages.home', compact(
            'featuredProducts',
            'categories',
            'trendingProducts',
            'featuredServices',
            'recentJobs',
            'recentlyViewed',
            'featuredSellers'
        ));
    }
}

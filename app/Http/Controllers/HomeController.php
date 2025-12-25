<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
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

        return view('pages.home', compact(
            'featuredProducts',
            'categories',
            'trendingProducts'
        ));
    }
}

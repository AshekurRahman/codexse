<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function index()
    {
        $sellers = Seller::where('status', 'approved')
            ->withCount('products')
            ->orderByDesc('is_featured')
            ->orderByDesc('is_verified')
            ->orderByDesc('total_sales')
            ->paginate(12);

        return view('pages.sellers', compact('sellers'));
    }

    public function show(Seller $seller)
    {
        if ($seller->status !== 'approved') {
            abort(404);
        }

        $products = $seller->products()
            ->where('status', 'approved')
            ->with('category')
            ->latest()
            ->paginate(12);

        return view('pages.seller', compact('seller', 'products'));
    }
}

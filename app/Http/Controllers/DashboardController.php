<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard');
    }

    public function purchases()
    {
        $orders = auth()->user()->orders()->with('items.product')->latest()->paginate(10);
        return view('pages.purchases', compact('orders'));
    }

    public function wishlist()
    {
        $wishlists = auth()->user()->wishlists()->with('product')->latest()->paginate(12);
        return view('pages.wishlist', compact('wishlists'));
    }
}

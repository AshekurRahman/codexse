<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\License;
use Illuminate\Http\Request;

class LicenseController extends Controller
{
    public function index(Request $request)
    {
        $seller = auth()->user()->seller;

        $query = License::whereHas('product', function ($q) use ($seller) {
            $q->where('seller_id', $seller->id);
        })->with(['user', 'product', 'orderItem']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('license_key', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $licenses = $query->latest()->paginate(20)->withQueryString();

        // Get products for filter dropdown
        $products = $seller->products()->select('id', 'name')->get();

        return view('seller.licenses.index', compact('licenses', 'products'));
    }
}

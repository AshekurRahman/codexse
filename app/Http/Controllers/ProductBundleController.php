<?php

namespace App\Http\Controllers;

use App\Models\ProductBundle;
use Illuminate\Http\Request;

class ProductBundleController extends Controller
{
    public function index()
    {
        $bundles = ProductBundle::where('status', 'published')
            ->with(['seller.user', 'products'])
            ->latest()
            ->paginate(12);

        return view('pages.bundles.index', compact('bundles'));
    }

    public function show(ProductBundle $bundle)
    {
        if ($bundle->status !== 'published') {
            abort(404);
        }

        $bundle->load(['seller.user', 'products']);

        return view('pages.bundles.show', compact('bundle'));
    }
}

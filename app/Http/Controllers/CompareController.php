<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    /**
     * Maximum number of products that can be compared.
     */
    const MAX_COMPARE_ITEMS = 4;

    /**
     * Display the comparison page.
     */
    public function index()
    {
        $productIds = session('compare_products', []);
        $products = Product::with(['seller', 'category', 'reviews'])
            ->whereIn('id', $productIds)
            ->published()
            ->get();

        // Gather all unique attributes for comparison
        $allCompatibility = [];
        $allLicenseTypes = [];

        foreach ($products as $product) {
            if ($product->software_compatibility) {
                $allCompatibility = array_merge($allCompatibility, $product->software_compatibility);
            }
            if ($product->license_types) {
                $allLicenseTypes = array_merge($allLicenseTypes, array_keys($product->license_types));
            }
        }

        $allCompatibility = array_unique($allCompatibility);
        $allLicenseTypes = array_unique($allLicenseTypes);

        return view('pages.compare.index', [
            'products' => $products,
            'allCompatibility' => $allCompatibility,
            'allLicenseTypes' => $allLicenseTypes,
        ]);
    }

    /**
     * Add a product to comparison.
     */
    public function add(Product $product)
    {
        $compareProducts = session('compare_products', []);

        // Check if already in list
        if (in_array($product->id, $compareProducts)) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in comparison list',
                'count' => count($compareProducts),
                'products' => $this->getCompareProductsData(),
            ]);
        }

        // Check max limit
        if (count($compareProducts) >= self::MAX_COMPARE_ITEMS) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum ' . self::MAX_COMPARE_ITEMS . ' products can be compared',
                'count' => count($compareProducts),
                'products' => $this->getCompareProductsData(),
            ]);
        }

        // Add product
        $compareProducts[] = $product->id;
        session(['compare_products' => $compareProducts]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to comparison',
            'count' => count($compareProducts),
            'products' => $this->getCompareProductsData(),
        ]);
    }

    /**
     * Remove a product from comparison.
     */
    public function remove(Product $product)
    {
        $compareProducts = session('compare_products', []);

        if (($key = array_search($product->id, $compareProducts)) !== false) {
            unset($compareProducts[$key]);
            $compareProducts = array_values($compareProducts);
            session(['compare_products' => $compareProducts]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product removed from comparison',
            'count' => count($compareProducts),
            'products' => $this->getCompareProductsData(),
        ]);
    }

    /**
     * Clear all products from comparison.
     */
    public function clear()
    {
        session()->forget('compare_products');

        return response()->json([
            'success' => true,
            'message' => 'Comparison list cleared',
            'count' => 0,
            'products' => [],
        ]);
    }

    /**
     * Get comparison list data.
     */
    public function getList()
    {
        $compareProducts = session('compare_products', []);

        return response()->json([
            'success' => true,
            'count' => count($compareProducts),
            'products' => $this->getCompareProductsData(),
        ]);
    }

    /**
     * Get products data for comparison bar.
     */
    private function getCompareProductsData(): array
    {
        $productIds = session('compare_products', []);

        if (empty($productIds)) {
            return [];
        }

        return Product::whereIn('id', $productIds)
            ->published()
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'thumbnail' => $product->thumbnail_url,
                    'price' => $product->current_price,
                ];
            })
            ->toArray();
    }
}

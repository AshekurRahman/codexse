<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount([
                'products' => function ($query) {
                    $query->where('status', 'published');
                }
            ])
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->withCount([
                    'products' => function ($q) {
                        $q->where('status', 'published');
                    }
                ]);
            }])
            ->get();

        return view('pages.categories', compact('categories'));
    }

    public function show(Category $category)
    {
        $products = $category->products()
            ->where('status', 'published')
            ->with(['seller', 'category'])
            ->latest()
            ->paginate(12);

        return view('pages.category', compact('category', 'products'));
    }
}

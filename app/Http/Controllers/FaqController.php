<?php

namespace App\Http\Controllers;

use App\Models\ChatbotFaq;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Display the public FAQ page with search functionality.
     */
    public function index(Request $request): View
    {
        $search = $request->get('q');
        $category = $request->get('category');

        $query = ChatbotFaq::active()->ordered();

        if ($search) {
            $searchTerm = strtolower(trim($search));
            $query->where(function ($q) use ($searchTerm) {
                $q->whereRaw('LOWER(question) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(answer) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(keywords) LIKE ?', ["%{$searchTerm}%"]);
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        $faqs = $query->get();

        // Group by category for display
        $groupedFaqs = $faqs->groupBy(fn ($faq) => $faq->category ?? 'General');

        // Get all categories for filter
        $categories = ChatbotFaq::active()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        // Get popular FAQs for sidebar
        $popularFaqs = ChatbotFaq::active()
            ->where('hit_count', '>', 0)
            ->orderByDesc('hit_count')
            ->limit(5)
            ->get();

        return view('pages.faq', [
            'groupedFaqs' => $groupedFaqs,
            'categories' => $categories,
            'popularFaqs' => $popularFaqs,
            'search' => $search,
            'selectedCategory' => $category,
            'totalCount' => $faqs->count(),
        ]);
    }

    /**
     * Show a single FAQ and record the hit.
     */
    public function show(ChatbotFaq $faq): View
    {
        if (!$faq->is_active) {
            abort(404);
        }

        // Record the view
        $faq->recordHit();

        // Get related FAQs from same category
        $relatedFaqs = ChatbotFaq::active()
            ->where('id', '!=', $faq->id)
            ->when($faq->category, function ($q) use ($faq) {
                return $q->where('category', $faq->category);
            })
            ->ordered()
            ->limit(5)
            ->get();

        return view('pages.faq-show', [
            'faq' => $faq,
            'relatedFaqs' => $relatedFaqs,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ChatbotFaq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'keywords',
        'category',
        'sort_order',
        'is_active',
        'hit_count',
        'is_suggested',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_suggested' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeSuggested(Builder $query): Builder
    {
        return $query->where('is_suggested', true);
    }

    public function scopePopular(Builder $query): Builder
    {
        return $query->orderByDesc('hit_count');
    }

    /**
     * Increment hit count when FAQ is matched
     */
    public function recordHit(): void
    {
        $this->increment('hit_count');
    }

    /**
     * Get suggested FAQs for the chat widget
     */
    public static function getSuggested(int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        // First try to get manually marked suggested FAQs
        $suggested = static::active()->suggested()->ordered()->limit($limit)->get();

        // If not enough, fill with popular ones
        if ($suggested->count() < $limit) {
            $remaining = $limit - $suggested->count();
            $popular = static::active()
                ->where('is_suggested', false)
                ->popular()
                ->limit($remaining)
                ->get();
            $suggested = $suggested->merge($popular);
        }

        // If still not enough, fill with any active FAQs
        if ($suggested->count() < $limit) {
            $remaining = $limit - $suggested->count();
            $ids = $suggested->pluck('id')->toArray();
            $more = static::active()
                ->whereNotIn('id', $ids)
                ->ordered()
                ->limit($remaining)
                ->get();
            $suggested = $suggested->merge($more);
        }

        return $suggested;
    }

    /**
     * Find the best matching FAQ for a user query
     */
    public static function findBestMatch(string $query): ?self
    {
        $query = strtolower(trim($query));

        // Handle common greetings
        $greetings = ['hi', 'hey', 'hello', 'hola', 'howdy', 'good morning', 'good afternoon', 'good evening', 'greetings', 'yo', 'sup', 'whats up', "what's up"];
        if (in_array($query, $greetings) || preg_match('/^(hi|hey|hello|hola)\b/i', $query)) {
            // Look for a greeting FAQ
            $greetingFaq = static::active()
                ->where(function ($q) {
                    $q->where('keywords', 'LIKE', '%greeting%')
                      ->orWhere('keywords', 'LIKE', '%hello%')
                      ->orWhere('category', 'Greeting');
                })
                ->first();

            if ($greetingFaq) {
                return $greetingFaq;
            }
        }

        // First, try exact match on question
        $exactMatch = static::active()
            ->whereRaw('LOWER(question) = ?', [$query])
            ->first();

        if ($exactMatch) {
            return $exactMatch;
        }

        // Try full-text search
        $fullTextMatch = static::active()
            ->whereRaw('MATCH(question, keywords) AGAINST(? IN NATURAL LANGUAGE MODE)', [$query])
            ->orderByRaw('MATCH(question, keywords) AGAINST(? IN NATURAL LANGUAGE MODE) DESC', [$query])
            ->first();

        if ($fullTextMatch) {
            return $fullTextMatch;
        }

        // Fallback: keyword matching with LIKE
        $words = array_filter(explode(' ', $query), fn($w) => strlen($w) > 2);

        if (empty($words)) {
            return null;
        }

        $likeQuery = static::active();
        foreach ($words as $word) {
            $likeQuery->where(function ($q) use ($word) {
                $q->where('question', 'LIKE', "%{$word}%")
                  ->orWhere('keywords', 'LIKE', "%{$word}%");
            });
        }

        return $likeQuery->ordered()->first();
    }

    /**
     * Get all FAQs grouped by category
     */
    public static function getGroupedByCategory(): array
    {
        return static::active()
            ->ordered()
            ->get()
            ->groupBy(fn ($faq) => $faq->category ?? 'General')
            ->toArray();
    }

    /**
     * Get keyword array
     */
    public function getKeywordsArrayAttribute(): array
    {
        if (empty($this->keywords)) {
            return [];
        }
        return array_map('trim', explode(',', $this->keywords));
    }
}

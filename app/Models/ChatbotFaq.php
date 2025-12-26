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
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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

    /**
     * Find the best matching FAQ for a user query
     */
    public static function findBestMatch(string $query): ?self
    {
        $query = strtolower(trim($query));

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

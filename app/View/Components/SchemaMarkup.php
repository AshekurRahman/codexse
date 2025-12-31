<?php

namespace App\View\Components;

use App\Models\SeoSetting;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SchemaMarkup extends Component
{
    public string $type;
    public array $data;

    public function __construct(
        string $type = 'organization',
        array $data = []
    ) {
        $this->type = $type;
        $this->data = $data;
    }

    public function getSchema(): array
    {
        return match ($this->type) {
            'organization' => $this->getOrganizationSchema(),
            'website' => $this->getWebsiteSchema(),
            'product' => $this->getProductSchema(),
            'breadcrumb' => $this->getBreadcrumbSchema(),
            'faq' => $this->getFaqSchema(),
            'review' => $this->getReviewSchema(),
            'person' => $this->getPersonSchema(),
            default => $this->data,
        };
    }

    protected function getOrganizationSchema(): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => SeoSetting::get('organization_name', config('app.name')),
            'url' => SeoSetting::get('organization_url', url('/')),
        ];

        $logo = SeoSetting::get('organization_logo');
        if ($logo) {
            $schema['logo'] = str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo);
        }

        $email = SeoSetting::get('organization_email');
        if ($email) {
            $schema['email'] = $email;
        }

        return $schema;
    }

    protected function getWebsiteSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => SeoSetting::get('site_name', config('app.name')),
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => url('/products') . '?search={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    protected function getProductSchema(): array
    {
        $product = $this->data['product'] ?? null;

        if (!$product) {
            return [];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => strip_tags($product->description ?? ''),
            'url' => url("/products/{$product->slug}"),
        ];

        if ($product->thumbnail) {
            $schema['image'] = asset('storage/' . $product->thumbnail);
        }

        if ($product->seller) {
            $schema['brand'] = [
                '@type' => 'Brand',
                'name' => $product->seller->business_name ?? $product->seller->user->name,
            ];
        }

        $schema['offers'] = [
            '@type' => 'Offer',
            'price' => $product->sale_price ?? $product->price,
            'priceCurrency' => 'USD',
            'availability' => 'https://schema.org/InStock',
            'url' => url("/products/{$product->slug}"),
        ];

        if ($product->reviews_count > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $product->average_rating ?? 0,
                'reviewCount' => $product->reviews_count,
            ];
        }

        return $schema;
    }

    protected function getBreadcrumbSchema(): array
    {
        $items = $this->data['items'] ?? [];

        if (empty($items)) {
            return [];
        }

        $itemListElement = [];
        foreach ($items as $index => $item) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement,
        ];
    }

    protected function getFaqSchema(): array
    {
        $faqs = $this->data['faqs'] ?? [];

        if (empty($faqs)) {
            return [];
        }

        $mainEntity = [];
        foreach ($faqs as $faq) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }

    protected function getReviewSchema(): array
    {
        $review = $this->data['review'] ?? null;

        if (!$review) {
            return [];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $review->rating,
                'bestRating' => 5,
            ],
            'author' => [
                '@type' => 'Person',
                'name' => $review->user->name ?? 'Anonymous',
            ],
            'reviewBody' => $review->comment ?? '',
        ];
    }

    protected function getPersonSchema(): array
    {
        $person = $this->data['person'] ?? null;

        if (!$person) {
            return [];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $person->business_name ?? $person->user->name ?? '',
            'url' => url("/sellers/{$person->slug}"),
        ];

        if ($person->avatar || $person->user->profile_photo_path ?? null) {
            $schema['image'] = asset('storage/' . ($person->avatar ?? $person->user->profile_photo_path));
        }

        if ($person->bio) {
            $schema['description'] = strip_tags($person->bio);
        }

        return $schema;
    }

    public function render(): View
    {
        return view('components.schema-markup');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SeoSetting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $enabled = SeoSetting::get('sitemap_enabled', '1') === '1';

        if (!$enabled) {
            abort(404);
        }

        // Cache sitemap for 24 hours (86400 seconds)
        $content = Cache::remember('sitemap_xml', 86400, function () {
            return $this->generateSitemap();
        });

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    protected function generateSitemap(): string
    {
        $urls = [];
        $changefreq = SeoSetting::get('sitemap_changefreq', 'weekly');
        $priority = SeoSetting::get('sitemap_priority', '0.8');

        // Add homepage
        $urls[] = $this->createUrl(url('/'), now(), $changefreq, '1.0');

        // Add static pages
        $staticPages = [
            '/products' => '0.9',
            '/categories' => '0.8',
            '/sellers' => '0.8',
            '/blog' => '0.8',
            '/faq' => '0.5',
        ];

        foreach ($staticPages as $path => $pagePriority) {
            $urls[] = $this->createUrl(url($path), now(), $changefreq, $pagePriority);
        }

        // Add products
        if (SeoSetting::get('sitemap_include_products', '1') === '1') {
            $products = Product::where('status', 'approved')
                ->select('slug', 'updated_at')
                ->get();

            foreach ($products as $product) {
                $urls[] = $this->createUrl(
                    url("/products/{$product->slug}"),
                    $product->updated_at,
                    $changefreq,
                    $priority
                );
            }
        }

        // Add categories
        if (SeoSetting::get('sitemap_include_categories', '1') === '1') {
            $categories = Category::select('slug', 'updated_at')
                ->get();

            foreach ($categories as $category) {
                $urls[] = $this->createUrl(
                    url("/categories/{$category->slug}"),
                    $category->updated_at,
                    $changefreq,
                    '0.7'
                );
            }
        }

        // Add sellers
        if (SeoSetting::get('sitemap_include_sellers', '1') === '1') {
            $sellers = Seller::where('is_verified', true)
                ->select('store_slug', 'updated_at')
                ->get();

            foreach ($sellers as $seller) {
                $urls[] = $this->createUrl(
                    url("/sellers/{$seller->store_slug}"),
                    $seller->updated_at,
                    $changefreq,
                    '0.6'
                );
            }
        }

        // Add blog posts
        $blogPosts = BlogPost::published()
            ->select('slug', 'updated_at')
            ->get();

        foreach ($blogPosts as $post) {
            $urls[] = $this->createUrl(
                url("/blog/{$post->slug}"),
                $post->updated_at,
                $changefreq,
                '0.7'
            );
        }

        return $this->buildXml($urls);
    }

    protected function createUrl(string $loc, $lastmod, string $changefreq, string $priority): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod ? $lastmod->format('Y-m-d') : now()->format('Y-m-d'),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    protected function buildXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . PHP_EOL;
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        return $xml;
    }

    public function robots(): Response
    {
        $content = SeoSetting::get('robots_txt', $this->getDefaultRobots());

        return response($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    protected function getDefaultRobots(): string
    {
        return "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /dashboard/\nDisallow: /cart/\nDisallow: /checkout/\n\nSitemap: " . url('/sitemap.xml');
    }
}

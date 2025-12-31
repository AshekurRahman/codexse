<?php

namespace App\View\Components;

use App\Models\SeoSetting;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SeoMeta extends Component
{
    public string $title;
    public string $description;
    public string $keywords;
    public string $canonicalUrl;
    public string $robots;
    public string $ogTitle;
    public string $ogDescription;
    public string $ogImage;
    public string $ogType;
    public string $twitterTitle;
    public string $twitterDescription;
    public string $twitterImage;
    public string $twitterCard;

    public function __construct(
        ?string $title = null,
        ?string $description = null,
        ?string $keywords = null,
        ?string $canonicalUrl = null,
        ?string $robots = null,
        ?string $ogTitle = null,
        ?string $ogDescription = null,
        ?string $ogImage = null,
        ?string $ogType = 'website',
        ?string $twitterTitle = null,
        ?string $twitterDescription = null,
        ?string $twitterImage = null,
        ?string $twitterCard = 'summary_large_image',
    ) {
        $siteName = SeoSetting::get('site_name', config('app.name'));

        $this->title = $title ?? SeoSetting::get('default_meta_title', $siteName);
        $this->description = $description ?? SeoSetting::get('default_meta_description', '');
        $this->keywords = $keywords ?? SeoSetting::get('default_meta_keywords', '');
        $this->canonicalUrl = $canonicalUrl ?? url()->current();
        $this->robots = $robots ?? 'index, follow';

        $this->ogTitle = $ogTitle ?? $this->title;
        $this->ogDescription = $ogDescription ?? $this->description;
        $this->ogImage = $ogImage ?? $this->getOgImage();
        $this->ogType = $ogType;

        $this->twitterTitle = $twitterTitle ?? $this->title;
        $this->twitterDescription = $twitterDescription ?? $this->description;
        $this->twitterImage = $twitterImage ?? $this->ogImage;
        $this->twitterCard = $twitterCard;
    }

    protected function getOgImage(): string
    {
        $ogImage = SeoSetting::get('og_default_image', '');

        if ($ogImage && !str_starts_with($ogImage, 'http')) {
            return asset('storage/' . $ogImage);
        }

        return $ogImage ?: asset('images/og-default.jpg');
    }

    public function getTwitterSite(): string
    {
        $site = SeoSetting::get('twitter_site', '');
        return $site ? '@' . ltrim($site, '@') : '';
    }

    public function getTwitterCreator(): string
    {
        $creator = SeoSetting::get('twitter_creator', '');
        return $creator ? '@' . ltrim($creator, '@') : '';
    }

    public function getFacebookAppId(): string
    {
        return SeoSetting::get('facebook_app_id', '');
    }

    public function getGoogleVerification(): string
    {
        return SeoSetting::get('google_site_verification', '');
    }

    public function getBingVerification(): string
    {
        return SeoSetting::get('bing_site_verification', '');
    }

    public function render(): View
    {
        return view('components.seo-meta');
    }
}

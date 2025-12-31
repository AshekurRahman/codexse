<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Global SEO settings
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // Page-specific SEO metadata
        Schema::create('seo_metadata', function (Blueprint $table) {
            $table->id();
            $table->string('seoable_type');
            $table->unsignedBigInteger('seoable_id');
            $table->string('meta_title', 70)->nullable();
            $table->string('meta_description', 160)->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots')->default('index, follow');
            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->string('twitter_title')->nullable();
            $table->string('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();
            $table->json('schema_markup')->nullable();
            $table->boolean('noindex')->default(false);
            $table->boolean('nofollow')->default(false);
            $table->timestamps();

            $table->index(['seoable_type', 'seoable_id']);
        });

        // Insert default SEO settings
        $this->seedDefaultSettings();
    }

    protected function seedDefaultSettings(): void
    {
        $settings = [
            // General
            ['key' => 'site_name', 'value' => 'Codexse', 'group' => 'general'],
            ['key' => 'site_tagline', 'value' => 'Premium Digital Marketplace', 'group' => 'general'],
            ['key' => 'default_meta_title', 'value' => 'Codexse - Premium Digital Marketplace', 'group' => 'general'],
            ['key' => 'default_meta_description', 'value' => 'Discover premium digital products, professional services, and talented freelancers.', 'group' => 'general'],
            ['key' => 'default_meta_keywords', 'value' => 'digital marketplace, digital products, themes, templates, plugins', 'group' => 'general'],

            // Social
            ['key' => 'og_default_image', 'value' => '/images/og-default.jpg', 'group' => 'social'],
            ['key' => 'twitter_site', 'value' => '@codexse', 'group' => 'social'],
            ['key' => 'twitter_creator', 'value' => '@codexse', 'group' => 'social'],
            ['key' => 'facebook_app_id', 'value' => '', 'group' => 'social'],

            // Schema
            ['key' => 'organization_name', 'value' => 'Codexse', 'group' => 'schema'],
            ['key' => 'organization_logo', 'value' => '/images/logo.png', 'group' => 'schema'],
            ['key' => 'organization_url', 'value' => '', 'group' => 'schema'],
            ['key' => 'organization_email', 'value' => '', 'group' => 'schema'],

            // Sitemap
            ['key' => 'sitemap_enabled', 'value' => '1', 'group' => 'sitemap'],
            ['key' => 'sitemap_include_products', 'value' => '1', 'group' => 'sitemap'],
            ['key' => 'sitemap_include_categories', 'value' => '1', 'group' => 'sitemap'],
            ['key' => 'sitemap_include_sellers', 'value' => '1', 'group' => 'sitemap'],
            ['key' => 'sitemap_changefreq', 'value' => 'weekly', 'group' => 'sitemap'],
            ['key' => 'sitemap_priority', 'value' => '0.8', 'group' => 'sitemap'],

            // Robots
            ['key' => 'robots_txt', 'value' => "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /dashboard/\nDisallow: /cart/\nDisallow: /checkout/", 'group' => 'robots'],

            // Verification
            ['key' => 'google_site_verification', 'value' => '', 'group' => 'verification'],
            ['key' => 'bing_site_verification', 'value' => '', 'group' => 'verification'],
        ];

        foreach ($settings as $setting) {
            DB::table('seo_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_metadata');
        Schema::dropIfExists('seo_settings');
    }
};

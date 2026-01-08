<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds composite indexes for frequently used query patterns to improve performance.
     */
    public function up(): void
    {
        // Wishlists: Optimize user+product lookups (used in product cards)
        Schema::table('wishlists', function (Blueprint $table) {
            // Check if index doesn't already exist
            if (!$this->indexExists('wishlists', 'wishlists_user_product_unique')) {
                $table->unique(['user_id', 'product_id'], 'wishlists_user_product_unique');
            }
        });

        // Order items: Optimize seller analytics queries
        Schema::table('order_items', function (Blueprint $table) {
            if (!$this->indexExists('order_items', 'order_items_seller_created_index')) {
                $table->index(['seller_id', 'created_at'], 'order_items_seller_created_index');
            }
        });

        // Reviews: Optimize product reviews with status filter
        Schema::table('reviews', function (Blueprint $table) {
            if (!$this->indexExists('reviews', 'reviews_product_status_index')) {
                $table->index(['product_id', 'status'], 'reviews_product_status_index');
            }
        });

        // Products: Optimize status+featured queries (homepage)
        Schema::table('products', function (Blueprint $table) {
            if (!$this->indexExists('products', 'products_status_featured_index')) {
                $table->index(['status', 'is_featured'], 'products_status_featured_index');
            }
            if (!$this->indexExists('products', 'products_status_views_index')) {
                $table->index(['status', 'views_count'], 'products_status_views_index');
            }
        });

        // Services: Optimize status+featured queries
        Schema::table('services', function (Blueprint $table) {
            if (!$this->indexExists('services', 'services_status_featured_index')) {
                $table->index(['status', 'is_featured'], 'services_status_featured_index');
            }
        });

        // Service orders: Optimize buyer/seller queries
        Schema::table('service_orders', function (Blueprint $table) {
            if (!$this->indexExists('service_orders', 'service_orders_buyer_status_index')) {
                $table->index(['buyer_id', 'status'], 'service_orders_buyer_status_index');
            }
            if (!$this->indexExists('service_orders', 'service_orders_seller_status_index')) {
                $table->index(['seller_id', 'status'], 'service_orders_seller_status_index');
            }
        });

        // Orders: Optimize status queries
        Schema::table('orders', function (Blueprint $table) {
            if (!$this->indexExists('orders', 'orders_status_created_index')) {
                $table->index(['status', 'created_at'], 'orders_status_created_index');
            }
        });

        // Licenses: Optimize product+status queries
        Schema::table('licenses', function (Blueprint $table) {
            if (!$this->indexExists('licenses', 'licenses_product_status_index')) {
                $table->index(['product_id', 'status'], 'licenses_product_status_index');
            }
        });

        // Blog posts: Optimize published posts queries
        Schema::table('blog_posts', function (Blueprint $table) {
            if (!$this->indexExists('blog_posts', 'blog_posts_status_published_index')) {
                $table->index(['status', 'published_at'], 'blog_posts_status_published_index');
            }
        });

        // Job contracts: Optimize seller+status queries
        Schema::table('job_contracts', function (Blueprint $table) {
            if (!$this->indexExists('job_contracts', 'job_contracts_seller_status_index')) {
                $table->index(['seller_id', 'status'], 'job_contracts_seller_status_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropUnique('wishlists_user_product_unique');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_seller_created_index');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex('reviews_product_status_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_status_featured_index');
            $table->dropIndex('products_status_views_index');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex('services_status_featured_index');
        });

        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropIndex('service_orders_buyer_status_index');
            $table->dropIndex('service_orders_seller_status_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_created_index');
        });

        Schema::table('licenses', function (Blueprint $table) {
            $table->dropIndex('licenses_product_status_index');
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropIndex('blog_posts_status_published_index');
        });

        Schema::table('job_contracts', function (Blueprint $table) {
            $table->dropIndex('job_contracts_seller_status_index');
        });
    }

    /**
     * Check if an index exists on a table.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            $indexes = DB::select("SELECT name FROM sqlite_master WHERE type = 'index' AND tbl_name = ? AND name = ?", [$table, $indexName]);
            return count($indexes) > 0;
        }

        // MySQL/MariaDB
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks to allow truncation
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing dummy data (keep admin user)
        DB::table('reviews')->truncate();
        OrderItem::truncate();
        Order::truncate();
        Product::truncate();
        Seller::truncate();
        User::where('is_admin', false)->delete();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create Users
        $users = [
            ['name' => 'Sarah Wilson', 'email' => 'sarah@example.com'],
            ['name' => 'Mike Johnson', 'email' => 'mike@example.com'],
            ['name' => 'Emily Davis', 'email' => 'emily@example.com'],
            ['name' => 'Chris Brown', 'email' => 'chris@example.com'],
            ['name' => 'Jessica Miller', 'email' => 'jessica@example.com'],
            ['name' => 'David Garcia', 'email' => 'david@example.com'],
            ['name' => 'Amanda White', 'email' => 'amanda@example.com'],
            ['name' => 'Ryan Martinez', 'email' => 'ryan@example.com'],
            ['name' => 'Lisa Anderson', 'email' => 'lisa@example.com'],
            ['name' => 'Kevin Taylor', 'email' => 'kevin@example.com'],
        ];

        $createdUsers = [];
        foreach ($users as $index => $userData) {
            $createdUsers[] = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now()->subDays(rand(1, 60)),
                'is_admin' => false,
                'created_at' => now()->subDays(rand(1, 90)),
            ]);
        }

        // Create Sellers (level: bronze, silver, gold, platinum)
        $sellers = [
            ['store_name' => 'PixelCraft Studio', 'description' => 'Premium UI/UX design resources', 'level' => 'platinum', 'is_verified' => true],
            ['store_name' => 'CodeMasters', 'description' => 'High-quality code and scripts', 'level' => 'gold', 'is_verified' => true],
            ['store_name' => 'DesignHub', 'description' => 'Modern design templates and themes', 'level' => 'gold', 'is_verified' => false],
            ['store_name' => 'IconFactory', 'description' => 'Beautiful icon sets and illustrations', 'level' => 'silver', 'is_verified' => false],
            ['store_name' => 'ThemeForest Pro', 'description' => 'WordPress and web themes', 'level' => 'platinum', 'is_verified' => true],
        ];

        $createdSellers = [];
        foreach ($sellers as $index => $sellerData) {
            $createdSellers[] = Seller::create([
                'user_id' => $createdUsers[$index]->id,
                'store_name' => $sellerData['store_name'],
                'store_slug' => Str::slug($sellerData['store_name']),
                'description' => $sellerData['description'],
                'status' => 'approved',
                'level' => $sellerData['level'],
                'is_verified' => $sellerData['is_verified'],
                'is_featured' => $index < 2,
                'commission_rate' => rand(15, 25),
                'total_sales' => rand(1000, 50000),
                'total_earnings' => rand(800, 40000),
                'available_balance' => rand(500, 10000),
                'products_count' => rand(5, 20),
                'approved_at' => now()->subDays(rand(30, 180)),
                'created_at' => now()->subDays(rand(60, 180)),
            ]);
        }

        // Add pending seller
        Seller::create([
            'user_id' => $createdUsers[5]->id,
            'store_name' => 'NewDesigner Shop',
            'store_slug' => 'newdesigner-shop',
            'description' => 'Fresh design resources',
            'status' => 'pending',
            'level' => 'bronze',
            'is_verified' => false,
            'commission_rate' => 20,
            'created_at' => now()->subDays(2),
        ]);

        // Get categories
        $categories = Category::all();

        // Create Products (status: draft, pending, published, rejected, archived)
        $products = [
            ['name' => 'Dashboard Pro UI Kit', 'price' => 79, 'sale_price' => 59, 'category' => 'UI Kits', 'status' => 'published', 'is_featured' => true, 'sales' => 245],
            ['name' => 'E-commerce Template Bundle', 'price' => 129, 'sale_price' => null, 'category' => 'Templates', 'status' => 'published', 'is_featured' => true, 'sales' => 189],
            ['name' => 'Starter Icon Pack', 'price' => 29, 'sale_price' => 19, 'category' => 'Icons', 'status' => 'published', 'is_featured' => false, 'sales' => 412],
            ['name' => 'Mobile App UI Kit', 'price' => 89, 'sale_price' => null, 'category' => 'UI Kits', 'status' => 'published', 'is_featured' => true, 'sales' => 156],
            ['name' => 'Landing Page Templates', 'price' => 49, 'sale_price' => 39, 'category' => 'Templates', 'status' => 'published', 'is_featured' => false, 'sales' => 298],
            ['name' => 'Business Illustration Set', 'price' => 59, 'sale_price' => null, 'category' => 'Illustrations', 'status' => 'published', 'is_featured' => false, 'sales' => 167],
            ['name' => 'WordPress Developer Theme', 'price' => 69, 'sale_price' => 49, 'category' => 'Themes', 'status' => 'published', 'is_featured' => true, 'sales' => 534],
            ['name' => 'React Component Library', 'price' => 99, 'sale_price' => null, 'category' => 'Code', 'status' => 'published', 'is_featured' => false, 'sales' => 89],
            ['name' => 'Modern Sans Font Family', 'price' => 39, 'sale_price' => 29, 'category' => 'Fonts', 'status' => 'published', 'is_featured' => false, 'sales' => 223],
            ['name' => 'Device Mockup Bundle', 'price' => 45, 'sale_price' => null, 'category' => 'Mockups', 'status' => 'published', 'is_featured' => false, 'sales' => 178],
            ['name' => 'Admin Dashboard Template', 'price' => 59, 'sale_price' => 45, 'category' => 'Templates', 'status' => 'published', 'is_featured' => false, 'sales' => 312],
            ['name' => 'Social Media Icon Set', 'price' => 19, 'sale_price' => null, 'category' => 'Icons', 'status' => 'published', 'is_featured' => false, 'sales' => 567],
            ['name' => 'SaaS Landing Kit', 'price' => 79, 'sale_price' => 59, 'category' => 'UI Kits', 'status' => 'published', 'is_featured' => true, 'sales' => 201],
            ['name' => 'Portfolio Website Theme', 'price' => 49, 'sale_price' => null, 'category' => 'Themes', 'status' => 'published', 'is_featured' => false, 'sales' => 145],
            ['name' => 'Laravel Admin Panel', 'price' => 149, 'sale_price' => 119, 'category' => 'Code', 'status' => 'published', 'is_featured' => true, 'sales' => 78],
            ['name' => 'Flat Illustration Pack', 'price' => 35, 'sale_price' => null, 'category' => 'Illustrations', 'status' => 'pending', 'is_featured' => false, 'sales' => 0],
            ['name' => 'Crypto Dashboard UI', 'price' => 89, 'sale_price' => null, 'category' => 'UI Kits', 'status' => 'pending', 'is_featured' => false, 'sales' => 0],
            ['name' => 'Handwritten Font Collection', 'price' => 29, 'sale_price' => null, 'category' => 'Fonts', 'status' => 'draft', 'is_featured' => false, 'sales' => 0],
        ];

        $createdProducts = [];
        foreach ($products as $index => $productData) {
            $category = $categories->where('name', $productData['category'])->first();
            $seller = $createdSellers[array_rand($createdSellers)];

            $createdProducts[] = Product::create([
                'seller_id' => $seller->id,
                'category_id' => $category->id,
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'short_description' => 'High-quality ' . strtolower($productData['category']) . ' for modern projects.',
                'description' => 'This is a premium ' . strtolower($productData['name']) . ' designed for professionals. Includes all source files and documentation.',
                'price' => $productData['price'],
                'sale_price' => $productData['sale_price'],
                'version' => '1.' . rand(0, 5) . '.' . rand(0, 9),
                'status' => $productData['status'],
                'is_featured' => $productData['is_featured'],
                'is_trending' => rand(0, 1),
                'views_count' => rand(100, 5000),
                'downloads_count' => rand(50, 1000),
                'sales_count' => $productData['sales'],
                'average_rating' => rand(40, 50) / 10,
                'reviews_count' => rand(5, 100),
                'published_at' => $productData['status'] === 'published' ? now()->subDays(rand(1, 90)) : null,
                'created_at' => now()->subDays(rand(1, 120)),
            ]);
        }

        // Create Orders (status: pending, processing, completed, failed, refunded, partially_refunded)
        $statuses = ['pending', 'processing', 'completed', 'completed', 'completed', 'completed', 'failed', 'refunded'];
        $publishedProducts = collect($createdProducts)->filter(fn($p) => $p->status === 'published');

        for ($i = 0; $i < 30; $i++) {
            $user = $createdUsers[array_rand($createdUsers)];
            $status = $statuses[array_rand($statuses)];
            $orderDate = now()->subDays(rand(0, 60));
            $itemCount = rand(1, 3);
            $subtotal = 0;

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'CDX-' . strtoupper(Str::random(8)),
                'email' => $user->email,
                'subtotal' => 0,
                'discount' => rand(0, 1) ? rand(5, 20) : 0,
                'total' => 0,
                'currency' => 'USD',
                'status' => $status,
                'payment_method' => ['stripe', 'paypal'][rand(0, 1)],
                'paid_at' => in_array($status, ['completed', 'refunded']) ? $orderDate->addHours(rand(1, 24)) : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Add order items
            $selectedProducts = $publishedProducts->random($itemCount);
            $licenseTypes = ['personal', 'commercial', 'extended'];
            foreach ($selectedProducts as $product) {
                $price = $product->sale_price ?? $product->price;
                $subtotal += $price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'seller_id' => $product->seller_id,
                    'product_name' => $product->name,
                    'license_type' => $licenseTypes[array_rand($licenseTypes)],
                    'price' => $price,
                    'seller_amount' => $price * 0.8,
                    'platform_fee' => $price * 0.2,
                    'created_at' => $orderDate,
                ]);
            }

            $total = $subtotal - $order->discount;
            $order->update([
                'subtotal' => $subtotal,
                'total' => $total,
            ]);
        }

        $this->command->info('Dummy data created successfully!');
    }
}

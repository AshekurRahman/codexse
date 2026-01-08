<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles and permissions
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Create Categories
        $categories = [
            [
                'name' => 'UI Kits',
                'description' => 'Complete UI kits for web and mobile applications',
                'icon' => 'squares-2x2',
            ],
            [
                'name' => 'Templates',
                'description' => 'Ready-to-use website and application templates',
                'icon' => 'document-duplicate',
            ],
            [
                'name' => 'Icons',
                'description' => 'Icon packs and sets for your projects',
                'icon' => 'sparkles',
            ],
            [
                'name' => 'Illustrations',
                'description' => 'Beautiful illustrations for your designs',
                'icon' => 'photo',
            ],
            [
                'name' => 'Themes',
                'description' => 'WordPress, Shopify, and other platform themes',
                'icon' => 'paint-brush',
            ],
            [
                'name' => 'Code',
                'description' => 'Scripts, plugins, and code snippets',
                'icon' => 'code-bracket',
            ],
            [
                'name' => 'Fonts',
                'description' => 'Typography and font families',
                'icon' => 'language',
            ],
            [
                'name' => 'Mockups',
                'description' => 'Device and product mockups',
                'icon' => 'device-phone-mobile',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

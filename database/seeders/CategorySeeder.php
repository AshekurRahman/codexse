<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Main Category 1: Website Templates
            [
                'name' => 'Website Templates',
                'description' => 'Ready-made website designs for quick, professional and high-quality project launches.',
                'icon' => 'heroicon-o-rectangle-group',
                'show_on_homepage' => true,
                'is_active' => true,
                'is_featured' => true,
                'children' => [
                    ['name' => 'HTML Website Templates', 'description' => 'Static and responsive professionally designed HTML templates.', 'icon' => 'heroicon-o-document-text'],
                    ['name' => 'Landing Page Templates', 'description' => 'High-converting landing pages for marketing, promotions and product launches.', 'icon' => 'heroicon-o-window'],
                    ['name' => 'One Page Websites', 'description' => 'Simple, modern single-page website designs.', 'icon' => 'heroicon-o-rectangle-stack'],
                    ['name' => 'Multi-Purpose Templates', 'description' => 'Flexible templates suitable for multiple industries and uses.', 'icon' => 'heroicon-o-squares-2x2'],
                    ['name' => 'Business and Corporate', 'description' => 'Professional website templates for companies and organizations.', 'icon' => 'heroicon-o-briefcase'],
                    ['name' => 'Agency and Creative', 'description' => 'Stylish templates for creative studios, brands and digital agencies.', 'icon' => 'heroicon-o-building-office'],
                    ['name' => 'Portfolio and Personal', 'description' => 'Personal branding and portfolio showcase templates.', 'icon' => 'heroicon-o-user-circle'],
                    ['name' => 'SaaS and Startup', 'description' => 'Templates designed for startups and software companies.', 'icon' => 'heroicon-o-rocket-launch'],
                    ['name' => 'Blog and Magazine', 'description' => 'Content-focused blog and online magazine templates.', 'icon' => 'heroicon-o-newspaper'],
                    ['name' => 'Admin and Dashboard Templates', 'description' => 'Frontend UI templates for admin dashboards and panels.', 'icon' => 'heroicon-o-chart-bar'],
                    ['name' => 'Coming Soon and Under Construction', 'description' => 'Temporary pages for upcoming website launches.', 'icon' => 'heroicon-o-clock'],
                ],
            ],

            // Main Category 2: Website Themes
            [
                'name' => 'Website Themes',
                'description' => 'Fully functional and customizable website themes for popular platforms and frameworks.',
                'icon' => 'heroicon-o-swatch',
                'show_on_homepage' => true,
                'is_active' => true,
                'is_featured' => true,
                'children' => [
                    ['name' => 'WordPress Themes', 'description' => 'Ready-to-use themes for WordPress sites.', 'icon' => 'heroicon-o-document-text'],
                    ['name' => 'WooCommerce Themes', 'description' => 'E-commerce ready WordPress shop themes.', 'icon' => 'heroicon-o-shopping-bag'],
                    ['name' => 'Shopify Themes', 'description' => 'Online store themes for Shopify.', 'icon' => 'heroicon-o-shopping-cart'],
                    ['name' => 'Laravel Themes', 'description' => 'Website themes built using Laravel.', 'icon' => 'heroicon-o-command-line'],
                    ['name' => 'React Themes', 'description' => 'Dynamic themes built using React.', 'icon' => 'heroicon-o-cube'],
                    ['name' => 'Next.js Themes', 'description' => 'High-performance Next.js website themes.', 'icon' => 'heroicon-o-rectangle-group'],
                    ['name' => 'Vue and Nuxt Themes', 'description' => 'Modern themes built with Vue and Nuxt.', 'icon' => 'heroicon-o-rectangle-group'],
                    ['name' => 'CMS Themes', 'description' => 'Themes for Joomla, Drupal, Ghost and more.', 'icon' => 'heroicon-o-cog'],
                    ['name' => 'Blogging Themes', 'description' => 'Themes optimized for blogs and publishers.', 'icon' => 'heroicon-o-newspaper'],
                    ['name' => 'Business and Corporate Themes', 'description' => 'Themes designed for professional businesses.', 'icon' => 'heroicon-o-briefcase'],
                    ['name' => 'Portfolio and Creative Themes', 'description' => 'Beautiful themes for creatives and designers.', 'icon' => 'heroicon-o-sparkles'],
                ],
            ],

            // Main Category 3: Scripts and Web Applications
            [
                'name' => 'Scripts and Web Applications',
                'description' => 'Functional, ready-to-run systems, automation tools and complete web applications.',
                'icon' => 'heroicon-o-cube',
                'show_on_homepage' => true,
                'is_active' => true,
                'is_featured' => true,
                'children' => [
                    ['name' => 'PHP Scripts', 'description' => 'Ready-to-use PHP web scripts.', 'icon' => 'heroicon-o-code-bracket'],
                    ['name' => 'Laravel Applications', 'description' => 'Full Laravel-powered systems.', 'icon' => 'heroicon-o-command-line'],
                    ['name' => 'Node.js Applications', 'description' => 'Backend powered Node.js systems.', 'icon' => 'heroicon-o-server'],
                    ['name' => 'React Applications', 'description' => 'Frontend React based applications.', 'icon' => 'heroicon-o-cube'],
                    ['name' => 'Next.js Applications', 'description' => 'SEO friendly Next.js apps.', 'icon' => 'heroicon-o-rectangle-group'],
                    ['name' => 'Nuxt.js Applications', 'description' => 'Vue and Nuxt powered applications.', 'icon' => 'heroicon-o-rectangle-group'],
                    ['name' => 'Vue.js Applications', 'description' => 'Frontend Vue applications.', 'icon' => 'heroicon-o-cube'],
                    ['name' => 'E-Commerce Systems', 'description' => 'Complete online shop solutions.', 'icon' => 'heroicon-o-shopping-cart'],
                    ['name' => 'SaaS Applications', 'description' => 'Subscription-based ready SaaS platforms.', 'icon' => 'heroicon-o-bolt'],
                    ['name' => 'CRM, ERP, POS Systems', 'description' => 'Business management systems.', 'icon' => 'heroicon-o-chart-pie'],
                    ['name' => 'Booking and Reservation Systems', 'description' => 'Online appointment and booking software.', 'icon' => 'heroicon-o-calendar'],
                    ['name' => 'Learning Management Systems', 'description' => 'E-learning and LMS platforms.', 'icon' => 'heroicon-o-academic-cap'],
                    ['name' => 'Admin Panels and Management Systems', 'description' => 'Admin dashboards and panel systems.', 'icon' => 'heroicon-o-chart-bar'],
                    ['name' => 'Chat, Support and Ticket Systems', 'description' => 'Customer support platforms.', 'icon' => 'heroicon-o-chat-bubble-left-right'],
                    ['name' => 'Tools and Utilities', 'description' => 'Web tools and utility applications.', 'icon' => 'heroicon-o-wrench'],
                ],
            ],

            // Main Category 4: Plugins and Extensions
            [
                'name' => 'Plugins and Extensions',
                'description' => 'Add-ons that extend website features, power and performance.',
                'icon' => 'heroicon-o-puzzle-piece',
                'show_on_homepage' => true,
                'is_active' => true,
                'is_featured' => true,
                'children' => [
                    ['name' => 'WordPress Plugins', 'description' => 'Feature extensions for WordPress.', 'icon' => 'heroicon-o-document-text'],
                    ['name' => 'WooCommerce Plugins', 'description' => 'Add-ons for WooCommerce stores.', 'icon' => 'heroicon-o-shopping-bag'],
                    ['name' => 'Shopify Apps and Extensions', 'description' => 'Extensions for Shopify.', 'icon' => 'heroicon-o-shopping-cart'],
                    ['name' => 'Laravel Packages', 'description' => 'Feature packages for Laravel.', 'icon' => 'heroicon-o-command-line'],
                    ['name' => 'PHP Modules', 'description' => 'PHP based plugin modules.', 'icon' => 'heroicon-o-code-bracket'],
                    ['name' => 'Payment Gateway Plugins', 'description' => 'Secure online payment integrations.', 'icon' => 'heroicon-o-credit-card'],
                    ['name' => 'Security and Performance Plugins', 'description' => 'Improve security and speed.', 'icon' => 'heroicon-o-shield-check'],
                    ['name' => 'SEO Plugins', 'description' => 'Boost ranking and search performance.', 'icon' => 'heroicon-o-magnifying-glass'],
                    ['name' => 'Marketing and Social Plugins', 'description' => 'Marketing and social media tools.', 'icon' => 'heroicon-o-share'],
                    ['name' => 'Form and Contact Plugins', 'description' => 'Advanced form and submission plugins.', 'icon' => 'heroicon-o-envelope'],
                    ['name' => 'Page Builder Addons', 'description' => 'Extensions for page building tools.', 'icon' => 'heroicon-o-puzzle-piece'],
                ],
            ],

            // Main Category 5: UI, UX and Frontend Assets
            [
                'name' => 'UI, UX and Frontend Assets',
                'description' => 'Professional design and frontend resources for beautiful interfaces.',
                'icon' => 'heroicon-o-rectangle-stack',
                'show_on_homepage' => true,
                'is_active' => true,
                'is_featured' => true,
                'children' => [
                    ['name' => 'UI Kits', 'description' => 'Complete UI resource kits.', 'icon' => 'heroicon-o-rectangle-group'],
                    ['name' => 'Design Systems', 'description' => 'Structured design frameworks.', 'icon' => 'heroicon-o-cog'],
                    ['name' => 'Component Libraries', 'description' => 'Prebuilt UI component collections.', 'icon' => 'heroicon-o-squares-2x2'],
                    ['name' => 'React Components', 'description' => 'Reusable React UI components.', 'icon' => 'heroicon-o-cube'],
                    ['name' => 'Vue Components', 'description' => 'Reusable Vue components.', 'icon' => 'heroicon-o-cube'],
                    ['name' => 'Next and Nuxt Components', 'description' => 'Components for Next and Nuxt.', 'icon' => 'heroicon-o-rectangle-group'],
                    ['name' => 'Bootstrap UI Kits', 'description' => 'Bootstrap-based UI kits.', 'icon' => 'heroicon-o-window'],
                    ['name' => 'Tailwind UI Kits', 'description' => 'Tailwind CSS UI kits.', 'icon' => 'heroicon-o-swatch'],
                    ['name' => 'Dashboard UI Kits', 'description' => 'Dashboard focused UI kits.', 'icon' => 'heroicon-o-chart-bar'],
                    ['name' => 'Icon Packs', 'description' => 'Collections of web icons.', 'icon' => 'heroicon-o-photo'],
                    ['name' => 'Illustrations', 'description' => 'Creative illustration packs.', 'icon' => 'heroicon-o-photo'],
                    ['name' => 'Web Elements', 'description' => 'Website UI section elements.', 'icon' => 'heroicon-o-window'],
                ],
            ],

            // Main Category 6: E-Commerce Solutions
            [
                'name' => 'E-Commerce Solutions',
                'description' => 'Complete online selling platforms, systems and store tools.',
                'icon' => 'heroicon-o-shopping-cart',
                'show_on_homepage' => true,
                'is_active' => true,
                'is_featured' => true,
                'children' => [
                    ['name' => 'Complete E-Commerce Websites', 'description' => 'Fully functional online store websites.', 'icon' => 'heroicon-o-globe-alt'],
                    ['name' => 'Marketplace Systems', 'description' => 'Multi vendor and marketplace systems.', 'icon' => 'heroicon-o-users'],
                    ['name' => 'POS Systems', 'description' => 'Point of sale and inventory management.', 'icon' => 'heroicon-o-receipt-refund'],
                    ['name' => 'Storefront Templates', 'description' => 'Frontend designs for online stores.', 'icon' => 'heroicon-o-building-storefront'],
                    ['name' => 'Dropshipping Solutions', 'description' => 'Dropshipping-ready platforms.', 'icon' => 'heroicon-o-truck'],
                    ['name' => 'Shopping Cart Systems', 'description' => 'Shopping cart enabled systems.', 'icon' => 'heroicon-o-shopping-cart'],
                    ['name' => 'Checkout Systems', 'description' => 'Secure checkout process systems.', 'icon' => 'heroicon-o-credit-card'],
                    ['name' => 'Payment Integrations', 'description' => 'Online payment gateway integrations.', 'icon' => 'heroicon-o-banknotes'],
                ],
            ],
        ];

        $sortOrder = 1;

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $categoryData['sort_order'] = $sortOrder++;

            $parent = Category::create($categoryData);

            $childSortOrder = 1;
            foreach ($children as $childData) {
                $childData['parent_id'] = $parent->id;
                $childData['is_active'] = true;
                $childData['sort_order'] = $childSortOrder++;
                Category::create($childData);
            }
        }
    }
}

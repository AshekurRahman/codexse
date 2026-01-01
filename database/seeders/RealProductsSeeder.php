<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RealProductsSeeder extends Seeder
{
    public function run(): void
    {
        $seller = Seller::first();
        $categories = Category::all()->keyBy('slug');

        $products = [
            // UI Kits
            [
                'category' => 'ui-kits',
                'name' => 'Flavor - Modern Dashboard UI Kit',
                'short_description' => 'A comprehensive dashboard UI kit with 200+ components for Figma.',
                'description' => "Flavor is a modern, clean, and fully customizable dashboard UI kit designed for Figma. Perfect for creating admin panels, analytics dashboards, and SaaS applications.\n\n**What's Included:**\n- 200+ UI Components\n- 50+ Dashboard Screens\n- Light & Dark Mode\n- Auto-layout Support\n- Design System with Variables\n- Free Google Fonts\n- Regular Updates\n\n**Features:**\n- Fully responsive design\n- Well-organized layers\n- Easy to customize\n- Pixel-perfect design\n- Compatible with Figma",
                'price' => 79,
                'sale_price' => 59,
                'is_featured' => true,
                'is_trending' => true,
                'version' => '2.1.0',
                'software_compatibility' => json_encode(['Figma']),
            ],
            [
                'category' => 'ui-kits',
                'name' => 'Flavor - Mobile App UI Kit',
                'short_description' => 'Complete iOS & Android UI kit with 150+ screens for mobile apps.',
                'description' => "Design beautiful mobile applications with Flavor Mobile UI Kit. This comprehensive kit includes everything you need to create stunning iOS and Android apps.\n\n**What's Included:**\n- 150+ Mobile Screens\n- 100+ UI Components\n- iOS & Android Guidelines\n- Light & Dark Themes\n- Onboarding Flows\n- E-commerce Screens\n- Social Features\n- Profile & Settings\n\n**Categories:**\n- Authentication\n- Dashboard\n- E-commerce\n- Social\n- Media\n- Finance\n- Health & Fitness",
                'price' => 89,
                'sale_price' => null,
                'is_featured' => true,
                'is_trending' => false,
                'version' => '1.5.0',
                'software_compatibility' => json_encode(['Figma', 'Sketch']),
            ],
            [
                'category' => 'ui-kits',
                'name' => 'Flavor - E-commerce UI Kit',
                'short_description' => 'Complete e-commerce UI kit with shopping cart, checkout, and product pages.',
                'description' => "Build modern e-commerce experiences with Flavor E-commerce UI Kit. Includes all screens needed for a complete online shopping platform.\n\n**What's Included:**\n- Product Listings\n- Product Details\n- Shopping Cart\n- Checkout Flow\n- Order Tracking\n- User Accounts\n- Wishlist\n- Reviews & Ratings\n\n**Perfect for:**\n- Online Stores\n- Marketplaces\n- Fashion Apps\n- Food Delivery\n- Digital Products",
                'price' => 69,
                'sale_price' => 49,
                'is_featured' => false,
                'is_trending' => true,
                'version' => '1.2.0',
                'software_compatibility' => json_encode(['Figma']),
            ],

            // Website Templates
            [
                'category' => 'website-templates',
                'name' => 'Flavor - SaaS Landing Page Template',
                'short_description' => 'High-converting SaaS landing page template with multiple sections.',
                'description' => "Flavor SaaS is a premium landing page template designed to convert visitors into customers. Built with modern design principles and conversion optimization in mind.\n\n**Sections Included:**\n- Hero with CTA\n- Features Grid\n- Pricing Tables\n- Testimonials\n- FAQ Accordion\n- CTA Sections\n- Footer Variants\n\n**Tech Stack:**\n- HTML5 & CSS3\n- Tailwind CSS\n- Alpine.js\n- Responsive Design",
                'price' => 49,
                'sale_price' => null,
                'is_featured' => true,
                'is_trending' => false,
                'version' => '1.0.0',
                'software_compatibility' => json_encode(['HTML', 'Tailwind CSS']),
            ],
            [
                'category' => 'website-templates',
                'name' => 'Flavor - Agency Portfolio Template',
                'short_description' => 'Creative agency portfolio template with stunning animations.',
                'description' => "Showcase your creative work with Flavor Agency template. Features smooth animations, portfolio galleries, and team showcases.\n\n**Pages Included:**\n- Home Page\n- About Us\n- Services\n- Portfolio Grid\n- Portfolio Single\n- Team Page\n- Contact\n- Blog\n\n**Features:**\n- GSAP Animations\n- Portfolio Filtering\n- Contact Form\n- Blog Section\n- Team Profiles",
                'price' => 59,
                'sale_price' => 45,
                'is_featured' => false,
                'is_trending' => false,
                'version' => '1.1.0',
                'software_compatibility' => json_encode(['HTML', 'CSS', 'JavaScript']),
            ],

            // Mobile Apps
            [
                'category' => 'mobile-apps',
                'name' => 'Flavor - Finance App UI Kit',
                'short_description' => 'Modern fintech and banking app UI kit with 80+ screens.',
                'description' => "Create stunning finance applications with Flavor Finance UI Kit. Designed for banking, crypto, and investment apps.\n\n**Screens Include:**\n- Dashboard & Overview\n- Transaction History\n- Send & Receive Money\n- Cards Management\n- Crypto Trading\n- Investment Portfolio\n- Bills & Payments\n- Settings & Security\n\n**Features:**\n- Biometric Authentication UI\n- Charts & Graphs\n- Dark Mode Support\n- Card Design Templates",
                'price' => 79,
                'sale_price' => null,
                'is_featured' => true,
                'is_trending' => true,
                'version' => '2.0.0',
                'software_compatibility' => json_encode(['Figma', 'Sketch']),
            ],
            [
                'category' => 'mobile-apps',
                'name' => 'Flavor - Food Delivery App UI',
                'short_description' => 'Complete food ordering and delivery app UI kit.',
                'description' => "Build the next food delivery sensation with Flavor Food UI Kit. Covers the entire user journey from browsing to delivery tracking.\n\n**User Flows:**\n- Onboarding\n- Restaurant Discovery\n- Menu Browsing\n- Cart & Checkout\n- Order Tracking\n- Reviews & Ratings\n- Profile & Preferences\n\n**Includes:**\n- Customer App Screens\n- Restaurant Partner Screens\n- Delivery Partner Screens",
                'price' => 69,
                'sale_price' => 55,
                'is_featured' => false,
                'is_trending' => false,
                'version' => '1.3.0',
                'software_compatibility' => json_encode(['Figma']),
            ],

            // Icons
            [
                'category' => 'icons',
                'name' => 'Flavor Icons - 2000+ Essential Icons',
                'short_description' => 'Comprehensive icon library with 2000+ icons in multiple styles.',
                'description' => "Flavor Icons is a comprehensive icon library featuring 2000+ carefully crafted icons perfect for any project.\n\n**Icon Styles:**\n- Line Icons\n- Solid Icons\n- Duotone Icons\n- Colored Icons\n\n**Categories:**\n- Interface\n- Arrows\n- Media\n- Communication\n- Commerce\n- Finance\n- Social\n- Weather\n- And more...\n\n**Formats:**\n- SVG\n- PNG (multiple sizes)\n- Figma Components\n- Icon Font",
                'price' => 39,
                'sale_price' => 29,
                'is_featured' => true,
                'is_trending' => true,
                'version' => '3.0.0',
                'software_compatibility' => json_encode(['SVG', 'Figma', 'Sketch', 'Adobe XD']),
            ],
            [
                'category' => 'icons',
                'name' => 'Flavor 3D Icons Pack',
                'short_description' => '500+ beautiful 3D icons with customizable colors.',
                'description' => "Add depth to your designs with Flavor 3D Icons. Modern, vibrant 3D icons that make your projects stand out.\n\n**What's Included:**\n- 500+ 3D Icons\n- Multiple Color Variants\n- High Resolution PNGs\n- Source Files\n- Regular Updates\n\n**Categories:**\n- Business\n- Technology\n- Social Media\n- E-commerce\n- Education\n- Healthcare",
                'price' => 49,
                'sale_price' => null,
                'is_featured' => false,
                'is_trending' => false,
                'version' => '1.2.0',
                'software_compatibility' => json_encode(['PNG', 'Figma', 'Blender']),
            ],

            // Illustrations
            [
                'category' => 'illustrations',
                'name' => 'Flavor Illustrations - Business Pack',
                'short_description' => '100+ business and startup illustrations with editable vectors.',
                'description' => "Flavor Business Illustrations pack includes 100+ modern illustrations perfect for websites, apps, and presentations.\n\n**Themes:**\n- Teamwork\n- Remote Work\n- Startup\n- Marketing\n- Finance\n- Analytics\n- Success\n- Communication\n\n**Features:**\n- Fully Editable Vectors\n- Multiple Skin Tones\n- Customizable Colors\n- AI & SVG Formats",
                'price' => 59,
                'sale_price' => 45,
                'is_featured' => true,
                'is_trending' => false,
                'version' => '2.0.0',
                'software_compatibility' => json_encode(['SVG', 'AI', 'Figma']),
            ],
            [
                'category' => 'illustrations',
                'name' => 'Flavor Characters - Diverse People Pack',
                'short_description' => '200+ character illustrations with diverse representation.',
                'description' => "Create inclusive designs with Flavor Characters. Features diverse characters in various poses and situations.\n\n**Includes:**\n- 200+ Character Poses\n- Multiple Ethnicities\n- Various Professions\n- Different Ages\n- Accessibility Representation\n- Scene Compositions\n\n**Use Cases:**\n- Websites\n- Mobile Apps\n- Presentations\n- Marketing Materials",
                'price' => 69,
                'sale_price' => null,
                'is_featured' => false,
                'is_trending' => true,
                'version' => '1.5.0',
                'software_compatibility' => json_encode(['SVG', 'PNG', 'Figma']),
            ],

            // Themes
            [
                'category' => 'themes',
                'name' => 'flavor starter - Starter Laravel Admin Theme',
                'short_description' => 'Modern Laravel admin panel theme with Tailwind CSS.',
                'description' => "Flavor Admin is a modern admin theme built with Laravel and Tailwind CSS. Perfect for building admin panels and dashboards.\n\n**Features:**\n- Laravel 11 Ready\n- Tailwind CSS 3\n- Livewire Components\n- Dark Mode\n- RTL Support\n- Multiple Layouts\n- Form Components\n- Table Components\n- Chart Widgets\n\n**Pages:**\n- Dashboard\n- User Management\n- Settings\n- Profile\n- Authentication",
                'price' => 99,
                'sale_price' => 79,
                'is_featured' => true,
                'is_trending' => true,
                'version' => '2.0.0',
                'software_compatibility' => json_encode(['Laravel', 'Tailwind CSS', 'Livewire']),
            ],
            [
                'category' => 'themes',
                'name' => 'flavor starter starter - WordPress Theme starter starter',
                'short_description' => 'Starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter',
                'description' => "A starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter starter",
                'price' => 79,
                'sale_price' => 59,
                'is_featured' => false,
                'is_trending' => false,
                'version' => '1.0.0',
                'software_compatibility' => json_encode(['WordPress']),
            ],

            // Dashboards
            [
                'category' => 'dashboards',
                'name' => 'Flavor Analytics Dashboard',
                'short_description' => 'Data visualization dashboard with charts and real-time updates.',
                'description' => "Flavor Analytics is a comprehensive dashboard template for data visualization and business intelligence.\n\n**Charts Included:**\n- Line Charts\n- Bar Charts\n- Pie Charts\n- Area Charts\n- Scatter Plots\n- Heat Maps\n- Gauges\n\n**Features:**\n- Real-time Data Updates\n- Customizable Widgets\n- Export to PDF/Excel\n- Date Range Filters\n- Responsive Design",
                'price' => 89,
                'sale_price' => null,
                'is_featured' => true,
                'is_trending' => false,
                'version' => '1.4.0',
                'software_compatibility' => json_encode(['React', 'Chart.js', 'Tailwind CSS']),
            ],
            [
                'category' => 'dashboards',
                'name' => 'Flavor CRM Dashboard',
                'short_description' => 'Customer relationship management dashboard with pipeline views.',
                'description' => "Manage your customer relationships with Flavor CRM Dashboard. Includes pipeline management, contact tracking, and sales analytics.\n\n**Modules:**\n- Lead Management\n- Contact Database\n- Deal Pipeline\n- Task Management\n- Email Integration\n- Reports & Analytics\n- Team Collaboration\n\n**Features:**\n- Kanban Board\n- Calendar View\n- Activity Timeline\n- Custom Fields",
                'price' => 99,
                'sale_price' => 75,
                'is_featured' => false,
                'is_trending' => true,
                'version' => '2.1.0',
                'software_compatibility' => json_encode(['Vue.js', 'Laravel', 'Tailwind CSS']),
            ],

            // Code & Scripts
            [
                'category' => 'code-scripts',
                'name' => 'Flavor Auth - Laravel Authentication Package',
                'short_description' => 'Complete authentication system with social login and 2FA.',
                'description' => "Flavor Auth is a comprehensive Laravel authentication package with all the features you need.\n\n**Features:**\n- Email/Password Login\n- Social Login (Google, GitHub, Facebook)\n- Two-Factor Authentication\n- Email Verification\n- Password Reset\n- Remember Me\n- Login Throttling\n- Session Management\n\n**Includes:**\n- Blade Views\n- API Routes\n- Livewire Components\n- Full Documentation",
                'price' => 49,
                'sale_price' => null,
                'is_featured' => true,
                'is_trending' => false,
                'version' => '3.0.0',
                'software_compatibility' => json_encode(['Laravel 10+', 'PHP 8.1+']),
            ],
            [
                'category' => 'code-scripts',
                'name' => 'Flavor API - RESTful API Starter Kit',
                'short_description' => 'Production-ready API starter with authentication and documentation.',
                'description' => "Kickstart your API development with Flavor API Starter Kit. Built with best practices and production-ready features.\n\n**Features:**\n- JWT Authentication\n- API Versioning\n- Rate Limiting\n- Request Validation\n- Error Handling\n- API Documentation\n- Testing Suite\n- Docker Ready\n\n**Endpoints:**\n- User Management\n- Authentication\n- File Upload\n- Notifications",
                'price' => 59,
                'sale_price' => 45,
                'is_featured' => false,
                'is_trending' => false,
                'version' => '2.0.0',
                'software_compatibility' => json_encode(['Laravel', 'PHP 8.2+']),
            ],

            // 3D Assets
            [
                'category' => '3d-assets',
                'name' => 'Flavor 3D Device Mockups',
                'short_description' => '50+ high-quality 3D device mockups for presentations.',
                'description' => "Showcase your designs with Flavor 3D Device Mockups. Premium quality renders perfect for presentations and marketing.\n\n**Devices Included:**\n- iPhone 15 Pro\n- MacBook Pro\n- iPad Pro\n- Apple Watch\n- iMac\n- Android Phones\n- Windows Laptop\n\n**Features:**\n- 4K Resolution\n- Multiple Angles\n- Customizable Colors\n- Smart Object Layers\n- Scene Compositions",
                'price' => 69,
                'sale_price' => 55,
                'is_featured' => true,
                'is_trending' => true,
                'version' => '2.5.0',
                'software_compatibility' => json_encode(['PSD', 'Figma', 'Sketch']),
            ],

            // Design Systems
            [
                'category' => 'design-systems',
                'name' => 'Flavor Design System',
                'short_description' => 'Complete design system with tokens, components, and documentation.',
                'description' => "Flavor Design System is a comprehensive design system for building consistent user interfaces at scale.\n\n**Includes:**\n- Design Tokens\n- Color System\n- Typography Scale\n- Spacing System\n- Component Library\n- Pattern Library\n- Documentation\n- Figma Variables\n\n**Components:**\n- Buttons\n- Forms\n- Cards\n- Navigation\n- Modals\n- Tables\n- And 50+ more...",
                'price' => 149,
                'sale_price' => 119,
                'is_featured' => true,
                'is_trending' => true,
                'version' => '3.0.0',
                'software_compatibility' => json_encode(['Figma', 'Storybook', 'React']),
            ],
        ];

        foreach ($products as $index => $data) {
            $category = $categories->get($data['category']);
            if (!$category) continue;

            Product::create([
                'seller_id' => $seller->id,
                'category_id' => $category->id,
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'short_description' => $data['short_description'],
                'description' => $data['description'],
                'price' => $data['price'],
                'sale_price' => $data['sale_price'],
                'version' => $data['version'],
                'software_compatibility' => $data['software_compatibility'],
                'status' => 'published',
                'is_featured' => $data['is_featured'],
                'is_trending' => $data['is_trending'],
                'views_count' => rand(500, 5000),
                'downloads_count' => rand(100, 1000),
                'sales_count' => rand(50, 500),
                'average_rating' => rand(42, 50) / 10,
                'reviews_count' => rand(10, 100),
                'published_at' => now()->subDays(rand(1, 90)),
                'created_at' => now()->subDays(rand(30, 120)),
            ]);
        }

        $this->command->info('Created ' . count($products) . ' real products!');
    }
}

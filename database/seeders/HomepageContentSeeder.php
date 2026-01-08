<?php

namespace Database\Seeders;

use App\Models\HomepageSection;
use App\Models\HomepageStat;
use App\Models\HowItWorksStep;
use App\Models\Testimonial;
use App\Models\TrustBadge;
use Illuminate\Database\Seeder;

class HomepageContentSeeder extends Seeder
{
    /**
     * Seed the homepage content tables.
     */
    public function run(): void
    {
        $this->seedTestimonials();
        $this->seedTrustBadges();
        $this->seedHomepageStats();
        $this->seedHowItWorksSteps();
        $this->seedHomepageSections();
    }

    /**
     * Seed testimonials from existing static data.
     */
    private function seedTestimonials(): void
    {
        $testimonials = [
            [
                'name' => 'Sarah Johnson',
                'role' => 'E-commerce Business Owner',
                'content' => 'Codexse has transformed my online business. The digital products I purchased helped me launch faster than I ever imagined. Excellent quality and support!',
                'rating' => 5,
                'status' => 'approved',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Michael Chen',
                'role' => 'Web Developer',
                'content' => 'As a developer, I appreciate the code quality of the themes and plugins here. Well-documented, clean code, and regular updates. Highly recommended!',
                'rating' => 5,
                'status' => 'approved',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Emily Rodriguez',
                'role' => 'Digital Marketing Agency',
                'content' => 'We\'ve found amazing freelancers through Codexse for our client projects. The escrow system gives us peace of mind, and the quality of work has been consistently excellent.',
                'rating' => 5,
                'status' => 'approved',
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'David Kim',
                'role' => 'Startup Founder',
                'content' => 'From UI kits to complete templates, Codexse has everything we needed to get our MVP launched quickly. The customer support team is incredibly responsive.',
                'rating' => 5,
                'status' => 'approved',
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Lisa Thompson',
                'role' => 'Freelance Designer',
                'content' => 'As a seller on Codexse, I\'ve been able to reach customers worldwide. The platform is easy to use, and the payout system is reliable and fast.',
                'rating' => 5,
                'status' => 'approved',
                'is_featured' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'James Wilson',
                'role' => 'Small Business Owner',
                'content' => 'The job posting feature helped me find the perfect developer for my project. The whole process was smooth, professional, and the results exceeded my expectations.',
                'rating' => 5,
                'status' => 'approved',
                'is_featured' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::updateOrCreate(
                ['name' => $testimonial['name']],
                $testimonial
            );
        }
    }

    /**
     * Seed trust badges.
     */
    private function seedTrustBadges(): void
    {
        $badges = [
            [
                'title' => 'Secure Payments',
                'subtitle' => '256-bit SSL',
                'icon' => 'shield-check',
                'icon_color' => 'success',
                'sort_order' => 1,
            ],
            [
                'title' => 'Money Back',
                'subtitle' => '30-day guarantee',
                'icon' => 'currency-dollar',
                'icon_color' => 'primary',
                'sort_order' => 2,
            ],
            [
                'title' => '24/7 Support',
                'subtitle' => 'Always here to help',
                'icon' => 'lifebuoy',
                'icon_color' => 'accent',
                'sort_order' => 3,
            ],
            [
                'title' => 'Verified Sellers',
                'subtitle' => 'Quality assured',
                'icon' => 'check-badge',
                'icon_color' => 'info',
                'sort_order' => 4,
            ],
            [
                'title' => 'Instant Download',
                'subtitle' => 'Get it now',
                'icon' => 'arrow-down-tray',
                'icon_color' => 'warning',
                'sort_order' => 5,
            ],
            [
                'title' => 'Lifetime Updates',
                'subtitle' => 'Always current',
                'icon' => 'arrow-path',
                'icon_color' => 'danger',
                'sort_order' => 6,
            ],
        ];

        foreach ($badges as $badge) {
            TrustBadge::updateOrCreate(
                ['title' => $badge['title']],
                $badge
            );
        }
    }

    /**
     * Seed homepage stats for the marquee.
     */
    private function seedHomepageStats(): void
    {
        $stats = [
            [
                'label' => 'Products',
                'value' => '10,000',
                'suffix' => '+',
                'color' => 'primary',
                'sort_order' => 1,
            ],
            [
                'label' => 'Services',
                'value' => '5,000',
                'suffix' => '+',
                'color' => 'accent',
                'sort_order' => 2,
            ],
            [
                'label' => 'Verified Sellers',
                'value' => '2,500',
                'suffix' => '+',
                'color' => 'success',
                'sort_order' => 3,
            ],
            [
                'label' => 'Secure Payments',
                'value' => '100',
                'suffix' => '%',
                'color' => 'warning',
                'sort_order' => 4,
            ],
            [
                'label' => 'Support',
                'value' => '24/7',
                'color' => 'info',
                'sort_order' => 5,
            ],
        ];

        foreach ($stats as $stat) {
            HomepageStat::updateOrCreate(
                ['label' => $stat['label']],
                $stat
            );
        }
    }

    /**
     * Seed "How It Works" steps.
     */
    private function seedHowItWorksSteps(): void
    {
        $steps = [
            [
                'step_number' => 1,
                'title' => 'Browse & Discover',
                'description' => 'Explore our vast marketplace of digital products, services, and job opportunities',
                'icon' => 'magnifying-glass',
                'icon_color' => 'primary',
                'sort_order' => 1,
            ],
            [
                'step_number' => 2,
                'title' => 'Purchase Securely',
                'description' => 'Pay with confidence using our secure escrow system. Your money is protected',
                'icon' => 'shield-check',
                'icon_color' => 'accent',
                'sort_order' => 2,
            ],
            [
                'step_number' => 3,
                'title' => 'Download & Enjoy',
                'description' => 'Get instant access to your purchases with lifetime updates and support',
                'icon' => 'arrow-down-tray',
                'icon_color' => 'success',
                'sort_order' => 3,
            ],
        ];

        foreach ($steps as $step) {
            HowItWorksStep::updateOrCreate(
                ['step_number' => $step['step_number']],
                $step
            );
        }
    }

    /**
     * Seed homepage sections (hero, CTA, etc.).
     */
    private function seedHomepageSections(): void
    {
        // Hero Section
        HomepageSection::updateOrCreate(
            ['section_key' => 'hero'],
            [
                'badge_text' => '10,000+ Digital Assets Available',
                'title' => 'Your One-Stop',
                'subtitle' => 'Digital Marketplace',
                'description' => 'Buy premium products, hire expert freelancers, or find your next project. Everything you need to build, grow, and succeed.',
                'is_active' => true,
            ]
        );

        // CTA Seller Section
        HomepageSection::updateOrCreate(
            ['section_key' => 'cta_seller'],
            [
                'badge_text' => 'Start Earning',
                'title' => 'Ready to turn your skills into income?',
                'description' => 'Join thousands of creators earning money from their digital products and services. Set up your store in minutes and start selling today.',
                'button_text' => 'Start Selling Today',
                'button_url' => '/seller/apply',
                'metadata' => [
                    'benefits' => [
                        'Free to join',
                        'Low fees',
                        'Fast payouts',
                    ],
                    'stats' => [
                        ['value' => '$2M+', 'label' => 'Paid to creators'],
                        ['value' => '2,500+', 'label' => 'Active sellers'],
                        ['value' => '50K+', 'label' => 'Happy customers'],
                        ['value' => '4.9', 'label' => 'Average rating'],
                    ],
                ],
                'is_active' => true,
            ]
        );

        // Featured Products Section Header
        HomepageSection::updateOrCreate(
            ['section_key' => 'featured_products'],
            [
                'badge_text' => 'Featured',
                'title' => 'Premium Products',
                'subtitle' => 'Hand-picked digital assets loved by creators',
                'is_active' => true,
            ]
        );

        // Featured Sellers Section Header
        HomepageSection::updateOrCreate(
            ['section_key' => 'featured_sellers'],
            [
                'badge_text' => 'Top Creators',
                'title' => 'Featured Sellers',
                'subtitle' => 'Meet our top-rated sellers creating amazing digital products',
                'is_active' => true,
            ]
        );

        // Services Section Header
        HomepageSection::updateOrCreate(
            ['section_key' => 'services'],
            [
                'badge_text' => 'Freelance Services',
                'title' => 'Hire Expert Freelancers',
                'subtitle' => 'Get your projects done by skilled professionals. From design to development, find the perfect match for your needs.',
                'metadata' => [
                    'benefits' => [
                        [
                            'icon' => 'check-badge',
                            'title' => 'Vetted Professionals',
                            'description' => 'All sellers are verified',
                        ],
                        [
                            'icon' => 'shield-check',
                            'title' => 'Secure Escrow Payments',
                            'description' => 'Your money is protected',
                        ],
                        [
                            'icon' => 'arrow-uturn-left',
                            'title' => 'Money-Back Guarantee',
                            'description' => 'Full refund if not satisfied',
                        ],
                    ],
                ],
                'is_active' => true,
            ]
        );

        // Jobs Section Header
        HomepageSection::updateOrCreate(
            ['section_key' => 'jobs'],
            [
                'badge_text' => 'Find Work',
                'title' => 'Latest Job Opportunities',
                'subtitle' => 'Find freelance projects that match your skills. New opportunities added daily.',
                'is_active' => true,
            ]
        );

        // Testimonials Section Header
        HomepageSection::updateOrCreate(
            ['section_key' => 'testimonials'],
            [
                'badge_text' => 'Testimonials',
                'title' => 'What Our Customers Say',
                'subtitle' => 'Join thousands of satisfied customers who have found success with Codexse',
                'is_active' => true,
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Sarah Mitchell',
                'role' => 'Founder & CEO',
                'company' => 'TechStart Inc.',
                'content' => 'CodexSE transformed our outdated systems into a modern, scalable platform. Their team\'s expertise and dedication exceeded our expectations. The project was delivered on time and the ongoing support has been exceptional.',
                'rating' => 5,
                'status' => 'approved',
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'James Rodriguez',
                'role' => 'CTO',
                'company' => 'Digital Ventures',
                'content' => 'Working with CodexSE was a game-changer for our business. They built a custom solution that perfectly aligned with our needs. Their technical skills and communication throughout the project were outstanding.',
                'rating' => 5,
                'status' => 'approved',
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Emily Chen',
                'role' => 'Product Manager',
                'company' => 'InnovateCorp',
                'content' => 'The e-commerce platform CodexSE developed for us has significantly boosted our online sales. Their attention to detail and user experience expertise made all the difference. Highly recommended!',
                'rating' => 5,
                'status' => 'approved',
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Michael Thompson',
                'role' => 'Operations Director',
                'company' => 'LogiFlow Systems',
                'content' => 'CodexSE delivered a robust inventory management system that streamlined our operations. The team was professional, responsive, and truly understood our business requirements.',
                'rating' => 5,
                'status' => 'approved',
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Amanda Foster',
                'role' => 'Marketing Director',
                'company' => 'BrandWave Agency',
                'content' => 'Our new website has received amazing feedback from clients. CodexSE\'s design team created a beautiful, functional site that truly represents our brand. The SEO improvements have also driven more traffic.',
                'rating' => 5,
                'status' => 'approved',
                'is_featured' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'David Kim',
                'role' => 'Startup Founder',
                'company' => 'HealthTech Solutions',
                'content' => 'As a healthcare startup, we needed a partner who understood compliance and security. CodexSE delivered a HIPAA-compliant platform that our patients love. Their expertise gave us confidence throughout the process.',
                'rating' => 5,
                'status' => 'approved',
                'is_featured' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Rachel Green',
                'role' => 'E-commerce Manager',
                'company' => 'StyleHub Retail',
                'content' => 'The mobile app CodexSE built has transformed how our customers shop. The intuitive design and fast performance have led to a 40% increase in mobile conversions. Excellent work!',
                'rating' => 5,
                'status' => 'approved',
                'is_featured' => false,
                'sort_order' => 7,
            ],
            [
                'name' => 'Robert Martinez',
                'role' => 'IT Director',
                'company' => 'Enterprise Solutions Ltd',
                'content' => 'CodexSE helped us migrate our legacy systems to the cloud seamlessly. Their methodical approach minimized downtime and the new infrastructure has improved our team\'s productivity significantly.',
                'rating' => 4,
                'status' => 'approved',
                'is_featured' => false,
                'sort_order' => 8,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}

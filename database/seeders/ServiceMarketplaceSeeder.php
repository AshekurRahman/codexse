<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Seller;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\ServiceRequirement;
use App\Models\ServiceOrder;
use App\Models\JobPosting;
use App\Models\JobProposal;
use App\Models\JobContract;
use App\Models\JobMilestone;
use App\Models\EscrowTransaction;
use App\Models\Dispute;
use App\Models\Conversation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServiceMarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        // Create test users if they don't exist
        $buyer = User::firstOrCreate(
            ['email' => 'buyer@test.com'],
            [
                'name' => 'John Buyer',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $client = User::firstOrCreate(
            ['email' => 'client@test.com'],
            [
                'name' => 'Sarah Client',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create sellers
        $sellerUser1 = User::firstOrCreate(
            ['email' => 'seller1@test.com'],
            [
                'name' => 'Mike Designer',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $sellerUser2 = User::firstOrCreate(
            ['email' => 'seller2@test.com'],
            [
                'name' => 'Emma Developer',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $seller1 = Seller::firstOrCreate(
            ['user_id' => $sellerUser1->id],
            [
                'store_name' => 'Mike Design Studio',
                'description' => 'Professional graphic designer with 5+ years of experience',
                'status' => 'approved',
                'available_balance' => 1500.00,
                'approved_at' => now()->subMonths(6),
            ]
        );

        $seller2 = Seller::firstOrCreate(
            ['user_id' => $sellerUser2->id],
            [
                'store_name' => 'Emma Dev Solutions',
                'description' => 'Full-stack developer specializing in Laravel and Vue.js',
                'status' => 'approved',
                'available_balance' => 2500.00,
                'approved_at' => now()->subMonths(3),
            ]
        );

        // Get or create a category
        $designCategory = Category::firstOrCreate(
            ['slug' => 'design'],
            ['name' => 'Design', 'description' => 'Graphic design and creative services']
        );

        $devCategory = Category::firstOrCreate(
            ['slug' => 'development'],
            ['name' => 'Development', 'description' => 'Web and software development']
        );

        // Create Services
        $service1 = Service::create([
            'seller_id' => $seller1->id,
            'category_id' => $designCategory->id,
            'name' => 'Professional Logo Design',
            'slug' => 'professional-logo-design-' . Str::random(6),
            'short_description' => 'I will create a unique and memorable logo for your brand',
            'description' => '<p>Get a professional, custom logo designed specifically for your business. I specialize in creating unique, memorable logos that capture your brand\'s essence.</p><p>What you\'ll get:</p><ul><li>Original, custom designs</li><li>Unlimited revisions</li><li>High-resolution files</li><li>Source files included</li></ul>',
            'status' => 'published',
            'is_featured' => true,
            'accepts_custom_orders' => true,
            'views_count' => 245,
            'orders_count' => 12,
            'average_rating' => 4.8,
            'reviews_count' => 10,
            'published_at' => now()->subDays(30),
        ]);

        // Create packages for service 1
        ServicePackage::create([
            'service_id' => $service1->id,
            'name' => 'Basic Logo',
            'tier' => 'basic',
            'price' => 50.00,
            'delivery_days' => 3,
            'revisions' => 2,
            'deliverables' => ['1 logo concept', 'PNG file', 'Standard resolution'],
        ]);

        ServicePackage::create([
            'service_id' => $service1->id,
            'name' => 'Standard Logo',
            'tier' => 'standard',
            'price' => 100.00,
            'delivery_days' => 5,
            'revisions' => 5,
            'deliverables' => ['3 logo concepts', 'PNG & JPG files', 'High resolution', 'Source file'],
        ]);

        $premiumPackage1 = ServicePackage::create([
            'service_id' => $service1->id,
            'name' => 'Premium Logo',
            'tier' => 'premium',
            'price' => 200.00,
            'delivery_days' => 7,
            'revisions' => 999, // unlimited
            'deliverables' => ['5 logo concepts', 'All file formats', 'Source files', 'Brand guidelines', 'Social media kit'],
        ]);

        // Create requirements for service 1
        ServiceRequirement::create([
            'service_id' => $service1->id,
            'question' => 'What is your business name?',
            'type' => 'text',
            'is_required' => true,
            'sort_order' => 1,
        ]);

        ServiceRequirement::create([
            'service_id' => $service1->id,
            'question' => 'Describe your business and target audience',
            'type' => 'textarea',
            'is_required' => true,
            'sort_order' => 2,
        ]);

        ServiceRequirement::create([
            'service_id' => $service1->id,
            'question' => 'Preferred style',
            'type' => 'select',
            'options' => ['Modern', 'Classic', 'Playful', 'Minimalist', 'Luxurious'],
            'is_required' => true,
            'sort_order' => 3,
        ]);

        // Create Service 2
        $service2 = Service::create([
            'seller_id' => $seller2->id,
            'category_id' => $devCategory->id,
            'name' => 'Laravel Web Application Development',
            'slug' => 'laravel-web-app-' . Str::random(6),
            'short_description' => 'I will build a custom web application using Laravel',
            'description' => '<p>Professional Laravel development services for your business needs. I build scalable, secure, and maintainable web applications.</p>',
            'status' => 'published',
            'is_featured' => true,
            'accepts_custom_orders' => true,
            'views_count' => 180,
            'orders_count' => 8,
            'average_rating' => 4.9,
            'reviews_count' => 7,
            'published_at' => now()->subDays(20),
        ]);

        $standardPackage2 = ServicePackage::create([
            'service_id' => $service2->id,
            'name' => 'Standard Package',
            'tier' => 'standard',
            'price' => 500.00,
            'delivery_days' => 14,
            'revisions' => 3,
            'deliverables' => ['Custom Laravel app', 'Basic admin panel', 'Database setup', 'API endpoints'],
        ]);

        // Create a Service Order (in progress)
        $serviceOrder1 = ServiceOrder::create([
            'order_number' => 'SVC-' . strtoupper(Str::random(8)),
            'buyer_id' => $buyer->id,
            'seller_id' => $seller1->id,
            'service_id' => $service1->id,
            'service_package_id' => $premiumPackage1->id,
            'title' => 'Logo design for TechStart Inc',
            'description' => 'Need a modern tech logo',
            'price' => 200.00,
            'platform_fee' => 20.00,
            'seller_amount' => 180.00,
            'delivery_days' => 7,
            'revisions_allowed' => 999,
            'revisions_used' => 0,
            'status' => 'in_progress',
            'requirements_data' => [
                'What is your business name?' => 'TechStart Inc',
                'Describe your business and target audience' => 'A tech startup focused on AI solutions for small businesses',
                'Preferred style' => 'Modern',
            ],
            'due_at' => now()->addDays(5),
            'started_at' => now()->subDays(2),
        ]);

        // Create escrow for service order
        $escrow1 = EscrowTransaction::create([
            'transaction_number' => 'ESC-' . strtoupper(Str::random(10)),
            'payer_id' => $buyer->id,
            'payee_id' => $sellerUser1->id,
            'seller_id' => $seller1->id,
            'escrowable_type' => ServiceOrder::class,
            'escrowable_id' => $serviceOrder1->id,
            'amount' => 200.00,
            'platform_fee' => 20.00,
            'seller_amount' => 180.00,
            'currency' => 'USD',
            'status' => 'held',
            'stripe_payment_intent_id' => 'pi_test_' . Str::random(24),
            'held_at' => now()->subDays(2),
            'auto_release_at' => now()->addDays(12),
        ]);

        // Escrow linked via escrowable morph relationship

        // Create a completed service order
        $serviceOrder2 = ServiceOrder::create([
            'order_number' => 'SVC-' . strtoupper(Str::random(8)),
            'buyer_id' => $client->id,
            'seller_id' => $seller2->id,
            'service_id' => $service2->id,
            'service_package_id' => $standardPackage2->id,
            'title' => 'E-commerce website development',
            'description' => 'Build a simple e-commerce site',
            'price' => 500.00,
            'platform_fee' => 50.00,
            'seller_amount' => 450.00,
            'delivery_days' => 14,
            'revisions_allowed' => 3,
            'revisions_used' => 1,
            'status' => 'completed',
            'due_at' => now()->subDays(7),
            'started_at' => now()->subDays(21),
            'delivered_at' => now()->subDays(8),
            'completed_at' => now()->subDays(5),
        ]);

        // Create Job Postings
        $job1 = JobPosting::create([
            'client_id' => $client->id,
            'category_id' => $devCategory->id,
            'title' => 'Full-Stack Developer for SaaS Project',
            'slug' => 'full-stack-developer-saas-' . Str::random(6),
            'description' => '<p>We are looking for an experienced full-stack developer to help build our new SaaS platform.</p><p>Requirements:</p><ul><li>3+ years experience with Laravel</li><li>Experience with Vue.js or React</li><li>Database design skills</li></ul>',
            'requirements' => 'Must have portfolio of previous work. Good communication skills required.',
            'skills_required' => ['Laravel', 'Vue.js', 'MySQL', 'REST API', 'Git'],
            'budget_type' => 'fixed',
            'budget_min' => 3000.00,
            'budget_max' => 5000.00,
            'deadline' => now()->addMonths(2),
            'duration_type' => 'one_time',
            'experience_level' => 'intermediate',
            'status' => 'open',
            'visibility' => 'public',
            'proposals_count' => 3,
            'views_count' => 156,
            'published_at' => now()->subDays(5),
            'closes_at' => now()->addDays(25),
        ]);

        $job2 = JobPosting::create([
            'client_id' => $buyer->id,
            'category_id' => $designCategory->id,
            'title' => 'Brand Identity Design for Startup',
            'slug' => 'brand-identity-design-' . Str::random(6),
            'description' => '<p>Need a complete brand identity package for our new startup.</p>',
            'skills_required' => ['Logo Design', 'Branding', 'Adobe Illustrator', 'Typography'],
            'budget_type' => 'fixed',
            'budget_min' => 500.00,
            'budget_max' => 1500.00,
            'duration_type' => 'one_time',
            'experience_level' => 'expert',
            'status' => 'in_progress',
            'visibility' => 'public',
            'proposals_count' => 5,
            'views_count' => 89,
            'published_at' => now()->subDays(10),
        ]);

        // Create Job Proposals
        $proposal1 = JobProposal::create([
            'job_posting_id' => $job1->id,
            'seller_id' => $seller2->id,
            'cover_letter' => "Hi! I'm very interested in your SaaS project. I have 5+ years of experience with Laravel and Vue.js, and I've built several similar platforms before.\n\nI can deliver a high-quality, scalable solution within your timeline and budget. Looking forward to discussing the details!",
            'proposed_price' => 4000.00,
            'proposed_duration' => 56, // 8 weeks in days
            'milestones' => [
                ['title' => 'Project Setup & Design', 'amount' => 800, 'duration' => 7],
                ['title' => 'Core Features Development', 'amount' => 1600, 'duration' => 21],
                ['title' => 'Integration & Testing', 'amount' => 1000, 'duration' => 14],
                ['title' => 'Deployment & Documentation', 'amount' => 600, 'duration' => 14],
            ],
            'status' => 'pending',
            'submitted_at' => now()->subDays(3),
        ]);

        $proposal2 = JobProposal::create([
            'job_posting_id' => $job2->id,
            'seller_id' => $seller1->id,
            'cover_letter' => "Hello! Your brand identity project caught my attention. With my experience in creating memorable brand identities for startups, I'm confident I can deliver exactly what you're looking for.",
            'proposed_price' => 1200.00,
            'proposed_duration' => 14, // 2 weeks in days
            'status' => 'accepted',
            'submitted_at' => now()->subDays(8),
            'reviewed_at' => now()->subDays(3),
        ]);

        // Create Job Contract from accepted proposal
        $contract1 = JobContract::create([
            'contract_number' => 'JOB-' . strtoupper(Str::random(8)),
            'job_posting_id' => $job2->id,
            'job_proposal_id' => $proposal2->id,
            'client_id' => $buyer->id,
            'seller_id' => $seller1->id,
            'title' => 'Brand Identity Design for Startup',
            'description' => 'Complete brand identity package including logo, color palette, and brand guidelines.',
            'total_amount' => 1200.00,
            'platform_fee' => 120.00,
            'seller_amount' => 1080.00,
            'payment_type' => 'milestone',
            'status' => 'active',
            'started_at' => now()->subDays(3),
        ]);

        // Create milestones for contract
        $milestone1 = JobMilestone::create([
            'job_contract_id' => $contract1->id,
            'title' => 'Logo Design & Concepts',
            'description' => 'Create 3 logo concepts for review',
            'amount' => 500.00,
            'platform_fee' => 50.00,
            'seller_amount' => 450.00,
            'due_date' => now()->addDays(4),
            'status' => 'funded',
            'sort_order' => 1,
            'funded_at' => now()->subDays(3),
        ]);

        // Create escrow for milestone
        $escrowMilestone1 = EscrowTransaction::create([
            'transaction_number' => 'ESC-' . strtoupper(Str::random(10)),
            'payer_id' => $buyer->id,
            'payee_id' => $sellerUser1->id,
            'seller_id' => $seller1->id,
            'escrowable_type' => JobMilestone::class,
            'escrowable_id' => $milestone1->id,
            'amount' => 500.00,
            'platform_fee' => 50.00,
            'seller_amount' => 450.00,
            'currency' => 'USD',
            'status' => 'held',
            'stripe_payment_intent_id' => 'pi_test_' . Str::random(24),
            'held_at' => now()->subDays(3),
            'auto_release_at' => now()->addDays(11),
        ]);

        // Escrow linked via escrowable morph relationship

        $milestone2 = JobMilestone::create([
            'job_contract_id' => $contract1->id,
            'title' => 'Brand Guidelines Document',
            'description' => 'Complete brand guidelines with color palette, typography, and usage rules',
            'amount' => 400.00,
            'platform_fee' => 40.00,
            'seller_amount' => 360.00,
            'due_date' => now()->addDays(10),
            'status' => 'pending',
            'sort_order' => 2,
        ]);

        $milestone3 = JobMilestone::create([
            'job_contract_id' => $contract1->id,
            'title' => 'Final Deliverables',
            'description' => 'All final files in required formats',
            'amount' => 300.00,
            'platform_fee' => 30.00,
            'seller_amount' => 270.00,
            'due_date' => now()->addDays(14),
            'status' => 'pending',
            'sort_order' => 3,
        ]);

        // Create a dispute example
        $disputedOrder = ServiceOrder::create([
            'order_number' => 'SVC-' . strtoupper(Str::random(8)),
            'buyer_id' => $client->id,
            'seller_id' => $seller1->id,
            'service_id' => $service1->id,
            'service_package_id' => $premiumPackage1->id,
            'title' => 'Logo for Coffee Shop',
            'price' => 200.00,
            'platform_fee' => 20.00,
            'seller_amount' => 180.00,
            'delivery_days' => 7,
            'revisions_allowed' => 999,
            'revisions_used' => 2,
            'status' => 'disputed',
            'due_at' => now()->subDays(3),
            'started_at' => now()->subDays(10),
            'delivered_at' => now()->subDays(4),
        ]);

        $escrowDisputed = EscrowTransaction::create([
            'transaction_number' => 'ESC-' . strtoupper(Str::random(10)),
            'payer_id' => $client->id,
            'payee_id' => $sellerUser1->id,
            'seller_id' => $seller1->id,
            'escrowable_type' => ServiceOrder::class,
            'escrowable_id' => $disputedOrder->id,
            'amount' => 200.00,
            'platform_fee' => 20.00,
            'seller_amount' => 180.00,
            'currency' => 'USD',
            'status' => 'disputed',
            'stripe_payment_intent_id' => 'pi_test_' . Str::random(24),
            'held_at' => now()->subDays(10),
            'disputed_at' => now()->subDays(2),
        ]);

        // Escrow linked via escrowable morph relationship

        Dispute::create([
            'escrow_transaction_id' => $escrowDisputed->id,
            'disputable_type' => ServiceOrder::class,
            'disputable_id' => $disputedOrder->id,
            'initiated_by' => $client->id,
            'reason' => 'not_as_described',
            'description' => 'The delivered logo does not match the style I requested. I asked for a modern minimalist design but received something completely different. The seller has not been responsive to my revision requests.',
            'status' => 'open',
        ]);

        // Create another service (draft)
        Service::create([
            'seller_id' => $seller2->id,
            'category_id' => $devCategory->id,
            'name' => 'WordPress Website Development',
            'slug' => 'wordpress-website-dev-' . Str::random(6),
            'short_description' => 'Professional WordPress websites',
            'description' => '<p>I will create a professional WordPress website for your business.</p>',
            'status' => 'draft',
            'accepts_custom_orders' => true,
        ]);

        $this->command->info('Service Marketplace dummy data created successfully!');
        $this->command->info('');
        $this->command->info('Test accounts created:');
        $this->command->info('- Buyer: buyer@test.com / password');
        $this->command->info('- Client: client@test.com / password');
        $this->command->info('- Seller 1 (Designer): seller1@test.com / password');
        $this->command->info('- Seller 2 (Developer): seller2@test.com / password');
    }
}

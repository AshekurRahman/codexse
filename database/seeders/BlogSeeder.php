<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Create blog categories
        $categories = [
            [
                'name' => 'Tutorials',
                'slug' => 'tutorials',
                'description' => 'Step-by-step guides and tutorials for developers and designers',
                'sort_order' => 1,
            ],
            [
                'name' => 'Design Tips',
                'slug' => 'design-tips',
                'description' => 'Tips and tricks for better design',
                'sort_order' => 2,
            ],
            [
                'name' => 'Industry News',
                'slug' => 'industry-news',
                'description' => 'Latest news and updates from the digital industry',
                'sort_order' => 3,
            ],
            [
                'name' => 'Case Studies',
                'slug' => 'case-studies',
                'description' => 'Real-world examples and success stories',
                'sort_order' => 4,
            ],
            [
                'name' => 'Product Updates',
                'slug' => 'product-updates',
                'description' => 'New features and updates from our marketplace',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            BlogCategory::create($category);
        }

        // Get admin user
        $admin = User::where('email', 'admin@codexse.com')->first() ?? User::first();

        // Create sample blog posts
        $posts = [
            [
                'title' => '10 Essential UI Design Principles Every Designer Should Know',
                'slug' => '10-essential-ui-design-principles',
                'excerpt' => 'Learn the fundamental principles that make great user interfaces stand out from the rest.',
                'content' => '<p>Good UI design is more than just making things look pretty. It\'s about creating interfaces that are intuitive, accessible, and enjoyable to use.</p>

<h2>1. Clarity is King</h2>
<p>Users should never have to guess what something does. Every button, icon, and element should have a clear purpose that\'s immediately apparent.</p>

<h2>2. Consistency Creates Comfort</h2>
<p>When similar elements behave the same way throughout your application, users can predict what will happen. This reduces cognitive load and makes your interface feel natural.</p>

<h2>3. Visual Hierarchy Guides the Eye</h2>
<p>Use size, color, and spacing to direct users\' attention to what matters most. The most important elements should be the most prominent.</p>

<h2>4. Feedback is Essential</h2>
<p>Every action should have a reaction. Whether it\'s a button press, form submission, or error, users need to know their input was received and understood.</p>

<h2>5. Accessibility is Not Optional</h2>
<p>Design for everyone. Consider color blindness, screen readers, keyboard navigation, and other accessibility needs from the start.</p>

<p>These principles form the foundation of great UI design. Master them, and you\'ll create interfaces that users love.</p>',
                'tags' => ['UI Design', 'UX', 'Design Principles', 'Tips'],
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'is_featured' => true,
                'blog_category_id' => 2, // Design Tips
            ],
            [
                'title' => 'Getting Started with Tailwind CSS: A Complete Guide',
                'slug' => 'getting-started-with-tailwind-css',
                'excerpt' => 'Everything you need to know to start building beautiful, responsive websites with Tailwind CSS.',
                'content' => '<p>Tailwind CSS has revolutionized the way we write CSS. This utility-first framework gives you the building blocks to create any design directly in your HTML.</p>

<h2>Why Tailwind CSS?</h2>
<p>Unlike traditional CSS frameworks, Tailwind doesn\'t impose design decisions on you. Instead, it provides low-level utility classes that let you build completely custom designs.</p>

<h2>Installation</h2>
<p>Getting started with Tailwind is straightforward:</p>
<pre><code>npm install -D tailwindcss
npx tailwindcss init</code></pre>

<h2>Core Concepts</h2>
<p>Tailwind uses utility classes like <code>flex</code>, <code>pt-4</code>, <code>text-center</code>, and <code>rotate-90</code> that you combine to build your designs.</p>

<h2>Responsive Design</h2>
<p>Tailwind makes responsive design a breeze with its mobile-first breakpoint system: <code>sm:</code>, <code>md:</code>, <code>lg:</code>, <code>xl:</code>.</p>

<p>Start experimenting with Tailwind today and see how it can speed up your development workflow!</p>',
                'tags' => ['Tailwind CSS', 'CSS', 'Web Development', 'Tutorial'],
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'is_featured' => true,
                'blog_category_id' => 1, // Tutorials
            ],
            [
                'title' => 'The Future of Digital Marketplaces in 2025',
                'slug' => 'future-of-digital-marketplaces-2025',
                'excerpt' => 'Exploring the trends and technologies shaping the future of online marketplaces.',
                'content' => '<p>The digital marketplace landscape is evolving rapidly. Here\'s what we expect to see in the coming year.</p>

<h2>AI-Powered Personalization</h2>
<p>Artificial intelligence will continue to transform how users discover products. Expect more sophisticated recommendation engines and personalized shopping experiences.</p>

<h2>NFTs and Digital Ownership</h2>
<p>Blockchain technology is changing how we think about digital ownership. NFTs are expanding beyond art into software licenses, templates, and more.</p>

<h2>Creator Economy Growth</h2>
<p>More creators are building sustainable businesses selling digital products. Platforms that support independent creators will thrive.</p>

<h2>Sustainability Focus</h2>
<p>Digital products are inherently more sustainable than physical goods. Expect to see more emphasis on this advantage in marketing.</p>

<p>The future is bright for digital marketplaces and the creators who power them.</p>',
                'tags' => ['Trends', 'Digital Products', 'Future', 'Industry'],
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'is_featured' => true,
                'blog_category_id' => 3, // Industry News
            ],
            [
                'title' => 'How We Helped a Designer Earn $100K on Our Platform',
                'slug' => 'designer-success-story-100k',
                'excerpt' => 'A behind-the-scenes look at how one talented designer built a thriving digital products business.',
                'content' => '<p>Meet Sarah, a freelance designer who joined our platform two years ago. Today, she\'s earned over $100,000 selling digital products.</p>

<h2>The Beginning</h2>
<p>Sarah started with a single icon pack. She spent two weeks perfecting 500 icons, paying attention to every detail.</p>

<h2>Finding Her Niche</h2>
<p>After analyzing market trends, Sarah focused on minimal UI components for mobile apps. This specialization helped her stand out.</p>

<h2>Building a Catalog</h2>
<p>Consistency was key. Sarah committed to releasing one new product every month, building a comprehensive catalog over time.</p>

<h2>Engaging with Customers</h2>
<p>Sarah responds to every review and feature request. This dedication to customer service led to repeat buyers and referrals.</p>

<h2>Key Takeaways</h2>
<ul>
<li>Quality over quantity</li>
<li>Find your niche</li>
<li>Be consistent</li>
<li>Listen to customers</li>
</ul>

<p>Sarah\'s story shows that with dedication and the right strategy, anyone can succeed in the digital products market.</p>',
                'tags' => ['Success Story', 'Case Study', 'Designer', 'Tips'],
                'status' => 'published',
                'published_at' => now()->subDays(7),
                'blog_category_id' => 4, // Case Studies
            ],
            [
                'title' => 'Introducing Services Marketplace: Hire Expert Freelancers',
                'slug' => 'introducing-services-marketplace',
                'excerpt' => 'We\'re excited to announce our new services marketplace feature, connecting buyers with skilled freelancers.',
                'content' => '<p>Today, we\'re thrilled to announce the launch of our Services Marketplace – a new way to connect with talented freelancers and get your projects done.</p>

<h2>What\'s New?</h2>
<p>In addition to digital products, sellers can now offer custom services directly on our platform. From logo design to web development, find the expertise you need.</p>

<h2>How It Works</h2>
<ol>
<li>Browse available services</li>
<li>Choose a package or request a custom quote</li>
<li>Work directly with your freelancer</li>
<li>Pay securely through our escrow system</li>
</ol>

<h2>Escrow Protection</h2>
<p>Your payment is held securely until you\'re satisfied with the delivered work. This protects both buyers and sellers.</p>

<h2>For Sellers</h2>
<p>If you\'re a seller, you can now offer services alongside your digital products. Set your own packages, prices, and delivery times.</p>

<p>Try the new Services Marketplace today and discover a better way to work!</p>',
                'tags' => ['Product Update', 'Services', 'Freelance', 'New Feature'],
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'blog_category_id' => 5, // Product Updates
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::create(array_merge($post, [
                'user_id' => $admin->id,
            ]));
        }
    }
}

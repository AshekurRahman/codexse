<?php

namespace Database\Seeders;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RealJobPostingsSeeder extends Seeder
{
    public function run(): void
    {
        // Get user IDs to distribute jobs
        $userIds = User::pluck('id')->toArray();

        $jobs = [
            // UI/UX Design Jobs
            [
                'title' => 'Design a Modern SaaS Dashboard UI',
                'category_id' => 7, // Dashboards
                'description' => "We're looking for a talented UI/UX designer to create a modern, intuitive dashboard interface for our B2B SaaS analytics platform.\n\nThe dashboard should include:\n- Real-time data visualization widgets\n- User management section\n- Settings and configuration panels\n- Notification center\n- Dark and light mode support\n\nWe need a clean, professional design that follows current design trends while maintaining excellent usability.",
                'requirements' => "- Proven experience designing SaaS dashboards\n- Proficiency in Figma\n- Strong understanding of data visualization\n- Portfolio showcasing similar projects\n- Ability to create responsive designs",
                'skills_required' => ['Figma', 'UI Design', 'Dashboard Design', 'Data Visualization', 'UX Design'],
                'budget_type' => 'fixed',
                'budget_min' => 2500,
                'budget_max' => 4000,
                'experience_level' => 'expert',
                'duration_type' => 'one_time',
            ],
            [
                'title' => 'Mobile App UI Design for Fitness Tracking App',
                'category_id' => 3, // Mobile Apps
                'description' => "We need a creative designer to design the complete UI for our iOS and Android fitness tracking application.\n\nApp features include:\n- Workout tracking and logging\n- Progress charts and statistics\n- Social features (challenges, leaderboards)\n- Meal planning and nutrition tracking\n- Integration with wearable devices\n\nWe're looking for a modern, energetic design that motivates users to stay active.",
                'requirements' => "- Experience with fitness/health app design\n- Knowledge of iOS and Android design guidelines\n- Ability to create engaging micro-interactions\n- Experience with Figma or Sketch\n- Understanding of accessibility standards",
                'skills_required' => ['Mobile UI Design', 'iOS Design', 'Android Design', 'Figma', 'UX Research'],
                'budget_type' => 'fixed',
                'budget_min' => 3000,
                'budget_max' => 5000,
                'experience_level' => 'intermediate',
                'duration_type' => 'one_time',
            ],
            [
                'title' => 'Create Custom Icon Set for E-commerce Platform',
                'category_id' => 4, // Icons
                'description' => "We need a comprehensive icon set designed for our e-commerce platform. The set should include approximately 100+ icons covering:\n\n- Navigation icons\n- Product category icons\n- Action icons (cart, wishlist, share, etc.)\n- Payment method icons\n- Shipping and delivery icons\n- User account icons\n- Status indicators\n\nIcons should be provided in multiple sizes and formats (SVG, PNG).",
                'requirements' => "- Strong portfolio of icon design work\n- Experience with e-commerce or retail design\n- Ability to maintain consistency across large icon sets\n- Delivery in vector format (SVG)\n- Quick turnaround time",
                'skills_required' => ['Icon Design', 'Vector Graphics', 'Adobe Illustrator', 'SVG', 'Visual Design'],
                'budget_type' => 'fixed',
                'budget_min' => 800,
                'budget_max' => 1500,
                'experience_level' => 'intermediate',
                'duration_type' => 'one_time',
            ],

            // Website Development Jobs
            [
                'title' => 'Build a WordPress Theme for Real Estate Agency',
                'category_id' => 6, // Themes
                'description' => "Looking for an experienced WordPress developer to create a custom theme for a real estate agency website.\n\nRequired features:\n- Property listing with advanced search/filters\n- IDX/MLS integration ready\n- Agent profiles and team pages\n- Interactive property maps\n- Virtual tour integration\n- Lead capture forms\n- Mobile responsive design\n- SEO optimized structure",
                'requirements' => "- 3+ years WordPress theme development\n- Experience with real estate websites\n- Knowledge of ACF, Elementor or similar\n- Understanding of SEO best practices\n- Clean, well-documented code",
                'skills_required' => ['WordPress', 'PHP', 'Theme Development', 'JavaScript', 'CSS', 'SEO'],
                'budget_type' => 'fixed',
                'budget_min' => 3500,
                'budget_max' => 6000,
                'experience_level' => 'expert',
                'duration_type' => 'one_time',
            ],
            [
                'title' => 'Convert Figma Design to React Components',
                'category_id' => 8, // Code & Scripts
                'description' => "We have a complete Figma design for a marketing website and need it converted to pixel-perfect React components.\n\nProject includes:\n- 15 unique page layouts\n- Reusable component library\n- Responsive design implementation\n- Animation and micro-interactions\n- Form handling with validation\n\nWe use Next.js 14 with Tailwind CSS.",
                'requirements' => "- Expert-level React/Next.js skills\n- Strong CSS/Tailwind proficiency\n- Experience with Framer Motion or similar\n- Attention to design details\n- Clean, maintainable code practices",
                'skills_required' => ['React', 'Next.js', 'Tailwind CSS', 'TypeScript', 'Framer Motion'],
                'budget_type' => 'fixed',
                'budget_min' => 2000,
                'budget_max' => 3500,
                'experience_level' => 'expert',
                'duration_type' => 'one_time',
            ],
            [
                'title' => 'Develop Custom Shopify Theme for Fashion Brand',
                'category_id' => 6, // Themes
                'description' => "High-end fashion brand seeking a developer to create a custom Shopify theme that reflects our luxury brand identity.\n\nRequirements:\n- Unique, editorial-style product pages\n- Lookbook/collection showcase\n- Size guide integration\n- Wishlist functionality\n- Instagram shop integration\n- Fast loading, optimized performance\n- Multi-currency support",
                'requirements' => "- Proven Shopify theme development experience\n- Strong Liquid templating skills\n- Experience with fashion/luxury e-commerce\n- Knowledge of Shopify 2.0 architecture\n- Portfolio of custom Shopify work",
                'skills_required' => ['Shopify', 'Liquid', 'JavaScript', 'CSS', 'E-commerce'],
                'budget_type' => 'fixed',
                'budget_min' => 4000,
                'budget_max' => 7000,
                'experience_level' => 'expert',
                'duration_type' => 'one_time',
            ],

            // Illustration Jobs
            [
                'title' => 'Create Illustration Set for Children\'s Education App',
                'category_id' => 5, // Illustrations
                'description' => "We're developing an educational app for children ages 4-8 and need a set of 30+ custom illustrations.\n\nIllustration needs:\n- Character designs (diverse children, animals, teachers)\n- Educational scene illustrations\n- Interactive element graphics\n- Achievement badges and rewards\n- Background environments\n\nStyle should be friendly, colorful, and engaging for young learners.",
                'requirements' => "- Portfolio showing children's illustration work\n- Experience with educational content\n- Ability to create diverse, inclusive characters\n- Vector illustration skills\n- Consistent style across all pieces",
                'skills_required' => ['Illustration', 'Character Design', 'Adobe Illustrator', 'Digital Art', 'Children\'s Content'],
                'budget_type' => 'fixed',
                'budget_min' => 1500,
                'budget_max' => 2500,
                'experience_level' => 'intermediate',
                'duration_type' => 'one_time',
            ],
            [
                'title' => 'Design Infographic Templates for Marketing Agency',
                'category_id' => 5, // Illustrations
                'description' => "Marketing agency needs a set of 10 editable infographic templates for client presentations and social media.\n\nTemplate types needed:\n- Data comparison charts\n- Process/timeline infographics\n- Statistics showcases\n- How-to/step-by-step guides\n- Comparison tables\n\nTemplates should be easily editable in Canva or Adobe Illustrator.",
                'requirements' => "- Strong infographic design portfolio\n- Experience with data visualization\n- Understanding of marketing/business content\n- Ability to create editable templates\n- Knowledge of Canva template creation",
                'skills_required' => ['Infographic Design', 'Data Visualization', 'Adobe Illustrator', 'Canva', 'Graphic Design'],
                'budget_type' => 'fixed',
                'budget_min' => 600,
                'budget_max' => 1000,
                'experience_level' => 'intermediate',
                'duration_type' => 'one_time',
            ],

            // Template Jobs
            [
                'title' => 'Design Landing Page Templates for SaaS Products',
                'category_id' => 2, // Website Templates
                'description' => "We need a designer to create 5 high-converting landing page templates specifically designed for SaaS product launches.\n\nEach template should include:\n- Hero section with product mockup\n- Features/benefits sections\n- Pricing tables\n- Testimonial sections\n- FAQ accordions\n- CTA sections\n- Footer designs\n\nDesigns should be modern, conversion-focused, and delivered in Figma.",
                'requirements' => "- Strong portfolio of SaaS landing pages\n- Understanding of conversion optimization\n- Expert Figma skills\n- Knowledge of responsive design\n- Experience with A/B testing preferred",
                'skills_required' => ['Landing Page Design', 'Figma', 'UI Design', 'Conversion Optimization', 'SaaS'],
                'budget_type' => 'fixed',
                'budget_min' => 1200,
                'budget_max' => 2000,
                'experience_level' => 'intermediate',
                'duration_type' => 'one_time',
            ],
            [
                'title' => 'Create Email Newsletter Templates for E-commerce',
                'category_id' => 2, // Website Templates
                'description' => "E-commerce company needs a set of 8 responsive email templates for various marketing campaigns.\n\nTemplate types:\n- Welcome series (2 templates)\n- Promotional/sale announcements (2 templates)\n- Product showcase (2 templates)\n- Abandoned cart recovery (1 template)\n- Order confirmation (1 template)\n\nMust be compatible with Klaviyo and Mailchimp.",
                'requirements' => "- Experience with email template design\n- Knowledge of email client compatibility\n- Understanding of email marketing best practices\n- Ability to create mobile-responsive emails\n- Experience with Klaviyo/Mailchimp",
                'skills_required' => ['Email Design', 'HTML Email', 'Klaviyo', 'Mailchimp', 'Responsive Design'],
                'budget_type' => 'fixed',
                'budget_min' => 800,
                'budget_max' => 1400,
                'experience_level' => 'intermediate',
                'duration_type' => 'one_time',
            ],

            // UI Kit Jobs
            [
                'title' => 'Design Complete UI Kit for Healthcare Platform',
                'category_id' => 1, // UI Kits
                'description' => "We're building a telemedicine platform and need a comprehensive UI kit that covers all aspects of the application.\n\nUI Kit should include:\n- Patient portal components\n- Doctor dashboard elements\n- Appointment scheduling interface\n- Video consultation UI\n- Prescription and medical records views\n- Payment and billing components\n- Chat/messaging interface\n\nMust follow healthcare UX best practices and accessibility guidelines.",
                'requirements' => "- Experience with healthcare/medical app design\n- Strong understanding of HIPAA-compliant UX\n- Expert Figma skills with proper component structure\n- Knowledge of accessibility standards (WCAG)\n- Portfolio showing complex app UI kits",
                'skills_required' => ['UI Kit Design', 'Healthcare UX', 'Figma', 'Component Design', 'Accessibility'],
                'budget_type' => 'fixed',
                'budget_min' => 4000,
                'budget_max' => 6500,
                'experience_level' => 'expert',
                'duration_type' => 'one_time',
            ],
            [
                'title' => 'Create Fintech Mobile App UI Kit',
                'category_id' => 1, // UI Kits
                'description' => "Fintech startup needs a modern UI kit for our mobile banking and investment application.\n\nRequired components:\n- Account overview screens\n- Transaction history and details\n- Money transfer flows\n- Investment portfolio views\n- Charts and data visualization\n- Card management screens\n- Settings and security options\n- Onboarding flow\n\nDesign should convey trust and security while being user-friendly.",
                'requirements' => "- Portfolio showing fintech/banking app designs\n- Understanding of financial app UX patterns\n- Experience with data-heavy interfaces\n- Strong typography and visual hierarchy skills\n- Knowledge of iOS and Android patterns",
                'skills_required' => ['Mobile UI Design', 'Fintech', 'Figma', 'Data Visualization', 'UX Design'],
                'budget_type' => 'fixed',
                'budget_min' => 3500,
                'budget_max' => 5500,
                'experience_level' => 'expert',
                'duration_type' => 'one_time',
            ],

            // Development Jobs
            [
                'title' => 'Build REST API for Inventory Management System',
                'category_id' => 8, // Code & Scripts
                'description' => "We need an experienced backend developer to build a robust REST API for our inventory management system.\n\nAPI requirements:\n- Product CRUD operations\n- Stock level management\n- Order processing\n- Supplier management\n- Reporting endpoints\n- User authentication (JWT)\n- Role-based access control\n- Webhook support for integrations\n\nPreferred stack: Laravel or Node.js with PostgreSQL.",
                'requirements' => "- 4+ years backend development experience\n- Strong API design knowledge\n- Experience with Laravel or Node.js\n- Understanding of database optimization\n- Knowledge of security best practices\n- API documentation experience",
                'skills_required' => ['Laravel', 'PHP', 'REST API', 'PostgreSQL', 'JWT', 'API Design'],
                'budget_type' => 'hourly',
                'budget_min' => 50,
                'budget_max' => 80,
                'experience_level' => 'expert',
                'duration_type' => 'ongoing',
            ],
            [
                'title' => 'Develop Chrome Extension for Productivity',
                'category_id' => 8, // Code & Scripts
                'description' => "Looking for a developer to create a Chrome extension that helps users track time spent on different websites and improve productivity.\n\nFeatures needed:\n- Automatic time tracking per website\n- Daily/weekly usage reports\n- Website blocking/limiting functionality\n- Focus mode with customizable sessions\n- Sync across devices\n- Export data functionality\n- Clean, minimal popup UI",
                'requirements' => "- Experience building Chrome extensions\n- Strong JavaScript/TypeScript skills\n- Understanding of Chrome Extension APIs\n- Experience with local storage and sync storage\n- Clean code and documentation practices",
                'skills_required' => ['Chrome Extension', 'JavaScript', 'TypeScript', 'HTML/CSS', 'Browser APIs'],
                'budget_type' => 'fixed',
                'budget_min' => 1500,
                'budget_max' => 2500,
                'experience_level' => 'intermediate',
                'duration_type' => 'one_time',
            ],
            [
                'title' => 'Create Automated Testing Suite for React Application',
                'category_id' => 8, // Code & Scripts
                'description' => "We have an existing React application that needs comprehensive test coverage. Looking for a QA engineer to set up and write tests.\n\nScope:\n- Set up Jest and React Testing Library\n- Write unit tests for components (60+ components)\n- Create integration tests for key user flows\n- Set up E2E tests with Playwright\n- Configure CI/CD test automation\n- Document testing practices\n\nCurrent coverage is approximately 15%, target is 80%.",
                'requirements' => "- Strong experience with React testing\n- Proficiency in Jest, RTL, and Playwright\n- Understanding of testing best practices\n- Experience with CI/CD pipelines\n- Ability to write maintainable tests",
                'skills_required' => ['React Testing', 'Jest', 'Playwright', 'TypeScript', 'CI/CD', 'Test Automation'],
                'budget_type' => 'fixed',
                'budget_min' => 2500,
                'budget_max' => 4000,
                'experience_level' => 'intermediate',
                'duration_type' => 'one_time',
            ],

            // 3D & Motion Jobs
            [
                'title' => '3D Product Visualization for Furniture Company',
                'category_id' => 9, // 3D Assets
                'description' => "Furniture company needs high-quality 3D renders for our product catalog and website.\n\nProject scope:\n- 15 furniture pieces to be modeled and rendered\n- Photorealistic rendering quality\n- Multiple angle shots per product\n- Lifestyle scene renders (5 scenes)\n- 360-degree spin animations\n- AR-ready 3D models (USDZ/GLB)\n\nReference photos and dimensions will be provided.",
                'requirements' => "- Strong portfolio of furniture/product visualization\n- Proficiency in Blender, 3ds Max, or similar\n- Experience with photorealistic rendering\n- Knowledge of AR file formats\n- Attention to material and lighting details",
                'skills_required' => ['3D Modeling', 'Product Visualization', 'Blender', 'Rendering', 'AR Assets'],
                'budget_type' => 'fixed',
                'budget_min' => 3000,
                'budget_max' => 5000,
                'experience_level' => 'expert',
                'duration_type' => 'one_time',
            ],
            [
                'title' => 'Create Animated Explainer Video for Tech Startup',
                'category_id' => 5, // Illustrations
                'description' => "Tech startup needs a 90-second animated explainer video to showcase our AI-powered product.\n\nVideo requirements:\n- Script visualization and storyboarding\n- Custom 2D character animations\n- Motion graphics for product features\n- Professional voiceover integration\n- Background music and sound effects\n- 1080p and 4K deliverables\n\nStyle: Modern, clean, corporate-friendly but approachable.",
                'requirements' => "- Portfolio of explainer video work\n- Experience with After Effects/similar tools\n- Character animation capabilities\n- Understanding of storytelling and pacing\n- Quick turnaround (2-3 weeks)",
                'skills_required' => ['Motion Graphics', 'After Effects', 'Animation', 'Explainer Video', 'Storyboarding'],
                'budget_type' => 'fixed',
                'budget_min' => 2000,
                'budget_max' => 3500,
                'experience_level' => 'intermediate',
                'duration_type' => 'one_time',
            ],

            // Design System Jobs
            [
                'title' => 'Build Design System for Enterprise Software',
                'category_id' => 12, // Design Systems
                'description' => "Enterprise software company needs a comprehensive design system to ensure consistency across multiple products.\n\nDesign system should include:\n- Design tokens (colors, typography, spacing)\n- Core component library (50+ components)\n- Pattern documentation\n- Accessibility guidelines\n- Usage examples and best practices\n- Figma component library with variants\n- Handoff documentation for developers\n\nMust support both light and dark modes.",
                'requirements' => "- Experience building design systems at scale\n- Expert Figma skills with advanced features\n- Understanding of atomic design principles\n- Knowledge of accessibility standards\n- Experience documenting design decisions\n- Collaboration experience with dev teams",
                'skills_required' => ['Design Systems', 'Figma', 'Component Design', 'Documentation', 'Accessibility'],
                'budget_type' => 'hourly',
                'budget_min' => 75,
                'budget_max' => 120,
                'experience_level' => 'expert',
                'duration_type' => 'ongoing',
            ],
            [
                'title' => 'Design Presentation Template Pack for Agency',
                'category_id' => 11, // Presentations
                'description' => "Digital agency needs a professional presentation template pack for client pitches and internal use.\n\nTemplate pack should include:\n- Master slide templates (30+ layouts)\n- Case study presentation template\n- Proposal/pitch deck template\n- Monthly report template\n- Workshop/training template\n\nFormats needed: PowerPoint, Keynote, and Google Slides versions.",
                'requirements' => "- Strong presentation design portfolio\n- Experience with agency/B2B presentations\n- Proficiency in PowerPoint, Keynote, Google Slides\n- Understanding of presentation best practices\n- Ability to create easily editable templates",
                'skills_required' => ['Presentation Design', 'PowerPoint', 'Keynote', 'Google Slides', 'Template Design'],
                'budget_type' => 'fixed',
                'budget_min' => 1000,
                'budget_max' => 1800,
                'experience_level' => 'intermediate',
                'duration_type' => 'one_time',
            ],
            [
                'title' => 'Develop Wireframe Kit for UX Designers',
                'category_id' => 10, // Wireframes
                'description' => "We're creating a comprehensive wireframe kit product and need a UX designer to help develop it.\n\nKit should include:\n- 200+ wireframe components\n- 50+ page templates\n- Mobile and desktop versions\n- Annotation components\n- Flow diagram elements\n- User journey templates\n- Persona templates\n\nMust be well-organized with clear naming conventions.",
                'requirements' => "- Expert-level wireframing experience\n- Strong understanding of UX patterns\n- Proficiency in Figma\n- Experience creating design resources\n- Attention to organization and documentation",
                'skills_required' => ['Wireframing', 'UX Design', 'Figma', 'Component Design', 'Information Architecture'],
                'budget_type' => 'fixed',
                'budget_min' => 2000,
                'budget_max' => 3500,
                'experience_level' => 'expert',
                'duration_type' => 'one_time',
            ],
        ];

        foreach ($jobs as $index => $job) {
            JobPosting::create([
                'client_id' => $userIds[array_rand($userIds)],
                'category_id' => $job['category_id'],
                'title' => $job['title'],
                'description' => $job['description'],
                'requirements' => $job['requirements'],
                'skills_required' => $job['skills_required'],
                'budget_type' => $job['budget_type'],
                'budget_min' => $job['budget_min'],
                'budget_max' => $job['budget_max'],
                'experience_level' => $job['experience_level'],
                'duration_type' => $job['duration_type'],
                'status' => 'open',
                'visibility' => 'public',
                'proposals_count' => rand(0, 15),
                'views_count' => rand(50, 500),
                'deadline' => Carbon::now()->addDays(rand(14, 60)),
                'published_at' => Carbon::now()->subDays(rand(1, 14)),
                'closes_at' => Carbon::now()->addDays(rand(30, 90)),
            ]);
        }

        $this->command->info('Created 20 realistic job postings!');
    }
}

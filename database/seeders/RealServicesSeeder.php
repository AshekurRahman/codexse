<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServicePackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RealServicesSeeder extends Seeder
{
    public function run(): void
    {
        $sellerIds = [1, 2]; // Available seller IDs

        $services = [
            // UI/UX Design Services
            [
                'category_id' => 1, // UI Kits
                'name' => 'Professional Website UI/UX Design',
                'short_description' => 'I will design a modern, user-friendly website UI/UX that converts visitors into customers.',
                'description' => "I will create a stunning, conversion-focused website design tailored to your brand and business goals.\n\n**What you'll get:**\n- Modern, clean UI design\n- User experience optimization\n- Mobile-responsive layouts\n- Interactive prototype\n- Design system components\n- Unlimited revisions until satisfaction\n\n**My process:**\n1. Discovery call to understand your needs\n2. Wireframe creation\n3. High-fidelity mockups\n4. Interactive prototype\n5. Final delivery with all assets\n\nI've designed for startups, agencies, and Fortune 500 companies. Let's create something amazing together!",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Landing Page', 'price' => 299, 'days' => 3, 'revisions' => 2, 'deliverables' => ['1 landing page design', 'Mobile responsive', 'Figma source file']],
                    ['tier' => 'standard', 'name' => 'Multi-Page Website', 'price' => 599, 'days' => 5, 'revisions' => 3, 'deliverables' => ['Up to 5 page designs', 'Mobile responsive', 'Figma source file', 'Basic prototype']],
                    ['tier' => 'premium', 'name' => 'Complete Website', 'price' => 999, 'days' => 7, 'revisions' => 0, 'deliverables' => ['Up to 10 page designs', 'Mobile + tablet responsive', 'Figma source file', 'Interactive prototype', 'Design system']],
                ],
            ],
            [
                'category_id' => 7, // Dashboards
                'name' => 'Custom Dashboard & Admin Panel Design',
                'short_description' => 'I will design a clean, intuitive dashboard or admin panel UI for your SaaS product.',
                'description' => "Transform your data into beautiful, actionable insights with a custom-designed dashboard.\n\n**Services include:**\n- Analytics dashboard design\n- Admin panel interfaces\n- Data visualization components\n- User management screens\n- Settings and configuration pages\n- Dark/light mode support\n\n**Why choose me:**\n- 5+ years of SaaS UI design experience\n- 100+ dashboards designed\n- Focus on usability and clarity\n- Fast turnaround times\n\nI understand that dashboards need to be both beautiful AND functional. Let me help you achieve both!",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Simple Dashboard', 'price' => 399, 'days' => 4, 'revisions' => 2, 'deliverables' => ['3 dashboard screens', 'Basic components', 'Figma file']],
                    ['tier' => 'standard', 'name' => 'Complete Dashboard', 'price' => 799, 'days' => 7, 'revisions' => 3, 'deliverables' => ['8 dashboard screens', 'Component library', 'Dark + light mode', 'Figma file']],
                    ['tier' => 'premium', 'name' => 'Full Admin Panel', 'price' => 1499, 'days' => 14, 'revisions' => 0, 'deliverables' => ['20+ screens', 'Complete component library', 'All themes', 'Interactive prototype', 'Documentation']],
                ],
            ],
            [
                'category_id' => 3, // Mobile Apps
                'name' => 'Mobile App UI/UX Design (iOS & Android)',
                'short_description' => 'I will design a beautiful, intuitive mobile app interface for iOS and Android platforms.',
                'description' => "Create an engaging mobile experience that users will love with my professional app design service.\n\n**What's included:**\n- Complete app UI design\n- User flow optimization\n- iOS and Android variants\n- Custom icon design\n- Micro-interactions\n- Clickable prototype\n\n**App categories I specialize in:**\n- E-commerce & retail\n- Health & fitness\n- Social networking\n- Fintech & banking\n- Food delivery\n- Travel & booking\n\nEvery design follows Apple HIG and Google Material Design guidelines for the best user experience.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'MVP Design', 'price' => 499, 'days' => 5, 'revisions' => 2, 'deliverables' => ['Up to 8 screens', 'One platform', 'Figma source']],
                    ['tier' => 'standard', 'name' => 'Standard App', 'price' => 999, 'days' => 10, 'revisions' => 3, 'deliverables' => ['Up to 20 screens', 'iOS + Android', 'Figma source', 'Prototype']],
                    ['tier' => 'premium', 'name' => 'Complete App', 'price' => 1999, 'days' => 15, 'revisions' => 0, 'deliverables' => ['Unlimited screens', 'iOS + Android', 'Design system', 'Advanced prototype', 'Developer handoff']],
                ],
            ],

            // Development Services
            [
                'category_id' => 8, // Code & Scripts
                'name' => 'WordPress Website Development',
                'short_description' => 'I will build a professional WordPress website that looks great and performs even better.',
                'description' => "Get a fully functional, beautiful WordPress website built with the latest technologies and best practices.\n\n**What I offer:**\n- Custom theme development\n- Plugin integration\n- E-commerce setup (WooCommerce)\n- SEO optimization\n- Speed optimization\n- Security hardening\n- Responsive design\n\n**Technologies:**\n- WordPress 6.x\n- Elementor Pro / Gutenberg\n- WooCommerce\n- ACF Pro\n- Custom PHP when needed\n\nI provide ongoing support and training so you can manage your site with confidence.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Basic Website', 'price' => 499, 'days' => 5, 'revisions' => 2, 'deliverables' => ['Up to 5 pages', 'Mobile responsive', 'Contact form', 'Basic SEO']],
                    ['tier' => 'standard', 'name' => 'Business Website', 'price' => 999, 'days' => 10, 'revisions' => 3, 'deliverables' => ['Up to 10 pages', 'Blog setup', 'Advanced SEO', 'Speed optimization', 'Training session']],
                    ['tier' => 'premium', 'name' => 'E-commerce Website', 'price' => 1999, 'days' => 15, 'revisions' => 0, 'deliverables' => ['Full WooCommerce store', 'Payment integration', 'Unlimited products', 'Email marketing setup', 'Ongoing support']],
                ],
            ],
            [
                'category_id' => 8, // Code & Scripts
                'name' => 'React/Next.js Web Application Development',
                'short_description' => 'I will build a fast, scalable web application using React or Next.js with modern best practices.',
                'description' => "Build your next web application with cutting-edge React/Next.js technology for the best performance and user experience.\n\n**Tech stack:**\n- React 18 / Next.js 14\n- TypeScript\n- Tailwind CSS\n- State management (Zustand/Redux)\n- API integration\n- Authentication\n\n**Services include:**\n- Custom component development\n- API integration\n- Authentication systems\n- Database design\n- Deployment setup\n- Performance optimization\n\nI build applications that are fast, secure, and scalable. Let's discuss your project!",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Landing Page', 'price' => 599, 'days' => 5, 'revisions' => 2, 'deliverables' => ['React landing page', 'Responsive design', 'Basic animations', 'Deployment']],
                    ['tier' => 'standard', 'name' => 'Web Application', 'price' => 1499, 'days' => 14, 'revisions' => 3, 'deliverables' => ['Full web app', 'Authentication', 'Database integration', 'Admin panel', 'Deployment']],
                    ['tier' => 'premium', 'name' => 'Enterprise Application', 'price' => 3999, 'days' => 30, 'revisions' => 0, 'deliverables' => ['Complex web app', 'Multiple user roles', 'Payment integration', 'API development', 'Documentation', '30 days support']],
                ],
            ],
            [
                'category_id' => 8, // Code & Scripts
                'name' => 'Laravel Backend & API Development',
                'short_description' => 'I will develop a robust Laravel backend or REST API for your web or mobile application.',
                'description' => "Power your applications with a solid, scalable Laravel backend built with industry best practices.\n\n**What I build:**\n- RESTful APIs\n- GraphQL APIs\n- Admin dashboards\n- Authentication systems\n- Payment integrations\n- Third-party integrations\n\n**Features:**\n- Clean, documented code\n- Test coverage\n- API documentation (Swagger)\n- Database optimization\n- Caching strategies\n- Queue management\n\nI follow Laravel best practices and SOLID principles to ensure your backend is maintainable and scalable.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Simple API', 'price' => 699, 'days' => 7, 'revisions' => 2, 'deliverables' => ['Basic CRUD API', 'Authentication', 'Documentation', 'Deployment']],
                    ['tier' => 'standard', 'name' => 'Complete Backend', 'price' => 1499, 'days' => 14, 'revisions' => 3, 'deliverables' => ['Full API', 'Admin panel', 'File uploads', 'Email system', 'Deployment']],
                    ['tier' => 'premium', 'name' => 'Enterprise Backend', 'price' => 3499, 'days' => 30, 'revisions' => 0, 'deliverables' => ['Complex API', 'Microservices ready', 'Payment integration', 'Real-time features', 'Full documentation', 'Support']],
                ],
            ],
            [
                'category_id' => 6, // Themes
                'name' => 'Shopify Store Setup & Customization',
                'short_description' => 'I will create a professional Shopify store that drives sales and builds your brand.',
                'description' => "Launch your e-commerce business with a beautifully designed, fully functional Shopify store.\n\n**Services include:**\n- Store setup from scratch\n- Theme customization\n- Product upload\n- Payment gateway setup\n- Shipping configuration\n- SEO optimization\n- App integrations\n\n**What makes my stores special:**\n- Conversion-optimized design\n- Fast loading speeds\n- Mobile-first approach\n- Easy to manage\n\nI've helped 200+ entrepreneurs launch successful online stores. Let me help you too!",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Store Setup', 'price' => 299, 'days' => 3, 'revisions' => 1, 'deliverables' => ['Basic store setup', 'Theme installation', 'Up to 10 products', 'Payment setup']],
                    ['tier' => 'standard', 'name' => 'Custom Store', 'price' => 599, 'days' => 5, 'revisions' => 2, 'deliverables' => ['Custom theme design', 'Up to 50 products', 'App integrations', 'SEO setup', 'Training']],
                    ['tier' => 'premium', 'name' => 'Premium Store', 'price' => 1299, 'days' => 10, 'revisions' => 0, 'deliverables' => ['Premium design', 'Unlimited products', 'Custom features', 'Marketing setup', 'Ongoing support']],
                ],
            ],

            // Logo & Branding Services
            [
                'category_id' => 4, // Icons (using for logos)
                'name' => 'Professional Logo Design',
                'short_description' => 'I will design a unique, memorable logo that perfectly represents your brand identity.',
                'description' => "Your logo is the face of your brand. Let me create one that makes a lasting impression.\n\n**My design process:**\n1. Brand discovery questionnaire\n2. Market research\n3. Concept sketches\n4. Digital refinement\n5. Final delivery\n\n**What you'll receive:**\n- Multiple concept options\n- Unlimited revisions\n- All file formats (AI, EPS, PDF, PNG, SVG)\n- Brand color palette\n- Typography recommendations\n- Social media kit\n\nI create logos that are timeless, versatile, and meaningful. Every design tells your unique story.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Starter Logo', 'price' => 99, 'days' => 2, 'revisions' => 2, 'deliverables' => ['2 logo concepts', 'PNG + JPG files', 'Basic revisions']],
                    ['tier' => 'standard', 'name' => 'Professional Logo', 'price' => 199, 'days' => 3, 'revisions' => 3, 'deliverables' => ['4 logo concepts', 'All file formats', 'Color variations', 'Social media kit']],
                    ['tier' => 'premium', 'name' => 'Brand Identity', 'price' => 499, 'days' => 7, 'revisions' => 0, 'deliverables' => ['6 logo concepts', 'Complete brand guide', 'Business card design', 'Letterhead', 'Social media kit', 'Brand strategy']],
                ],
            ],
            [
                'category_id' => 12, // Design Systems
                'name' => 'Complete Brand Identity Design',
                'short_description' => 'I will create a cohesive brand identity that sets you apart from the competition.',
                'description' => "Build a powerful brand presence with a comprehensive identity package designed to make you stand out.\n\n**Brand identity includes:**\n- Logo design (primary + secondary)\n- Color palette\n- Typography system\n- Brand patterns/textures\n- Icon set\n- Photography style guide\n- Voice and tone guidelines\n\n**Deliverables:**\n- Comprehensive brand book\n- All source files\n- Print-ready files\n- Digital assets\n- Social media templates\n\nA strong brand identity builds trust and recognition. Let's create yours!",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Essential Branding', 'price' => 499, 'days' => 7, 'revisions' => 2, 'deliverables' => ['Logo design', 'Color palette', 'Typography', 'Basic brand guide']],
                    ['tier' => 'standard', 'name' => 'Business Branding', 'price' => 999, 'days' => 14, 'revisions' => 3, 'deliverables' => ['Complete logo suite', 'Brand guide', 'Business cards', 'Social media kit', 'Email signature']],
                    ['tier' => 'premium', 'name' => 'Premium Branding', 'price' => 2499, 'days' => 21, 'revisions' => 0, 'deliverables' => ['Full brand identity', 'Comprehensive brand book', 'All collateral designs', 'Website mockup', 'Presentation templates', 'Strategy session']],
                ],
            ],

            // Illustration Services
            [
                'category_id' => 5, // Illustrations
                'name' => 'Custom Digital Illustration',
                'short_description' => 'I will create stunning custom illustrations for your project, brand, or personal use.',
                'description' => "Bring your ideas to life with unique, hand-crafted digital illustrations.\n\n**Illustration styles:**\n- Flat/vector illustrations\n- Character design\n- Editorial illustrations\n- Book illustrations\n- Infographics\n- Social media graphics\n\n**What I provide:**\n- Original artwork\n- Commercial license\n- Multiple revisions\n- Source files\n- Print-ready files\n\nEvery illustration is custom-made for your specific needs. No templates, no AI - just pure creativity!",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Simple Illustration', 'price' => 99, 'days' => 2, 'revisions' => 2, 'deliverables' => ['1 custom illustration', 'PNG + JPG files', 'Commercial use']],
                    ['tier' => 'standard', 'name' => 'Detailed Illustration', 'price' => 249, 'days' => 4, 'revisions' => 3, 'deliverables' => ['1 detailed illustration', 'Source file (AI/PSD)', 'Multiple formats', 'Commercial use']],
                    ['tier' => 'premium', 'name' => 'Illustration Set', 'price' => 599, 'days' => 7, 'revisions' => 0, 'deliverables' => ['5 custom illustrations', 'Consistent style', 'All source files', 'Commercial use', 'Rush delivery']],
                ],
            ],
            [
                'category_id' => 5, // Illustrations
                'name' => 'Character Design & Mascot Creation',
                'short_description' => 'I will design a unique character or mascot that brings personality to your brand.',
                'description' => "Create a memorable brand mascot or character that connects with your audience on an emotional level.\n\n**What you'll get:**\n- Original character design\n- Multiple expressions/poses\n- Style guide for consistency\n- Animation-ready assets\n- Full commercial rights\n\n**Perfect for:**\n- Brand mascots\n- App characters\n- Game characters\n- YouTube personas\n- Children's books\n- Marketing campaigns\n\nI'll work closely with you to create a character that perfectly represents your brand's personality.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Single Character', 'price' => 199, 'days' => 3, 'revisions' => 2, 'deliverables' => ['1 character design', '1 pose', 'PNG files', 'Commercial license']],
                    ['tier' => 'standard', 'name' => 'Character Pack', 'price' => 449, 'days' => 5, 'revisions' => 3, 'deliverables' => ['1 character design', '5 expressions/poses', 'Source files', 'Style guide']],
                    ['tier' => 'premium', 'name' => 'Mascot Bundle', 'price' => 899, 'days' => 10, 'revisions' => 0, 'deliverables' => ['Full character design', '10+ poses/expressions', 'Animation-ready', 'Complete style guide', 'Merchandise mockups']],
                ],
            ],

            // Video & Animation Services
            [
                'category_id' => 5, // Illustrations (for motion)
                'name' => 'Explainer Video Animation',
                'short_description' => 'I will create an engaging animated explainer video that communicates your message clearly.',
                'description' => "Tell your story in a way that captivates and converts with a professional animated explainer video.\n\n**Video styles available:**\n- 2D character animation\n- Motion graphics\n- Whiteboard animation\n- Isometric animation\n- Mixed media\n\n**What's included:**\n- Script review/writing assistance\n- Storyboard creation\n- Custom illustrations\n- Professional animation\n- Background music\n- Sound effects\n- Voiceover integration\n\nPerfect for product demos, startup pitches, educational content, and marketing campaigns.",
                'packages' => [
                    ['tier' => 'basic', 'name' => '30-Second Video', 'price' => 399, 'days' => 5, 'revisions' => 2, 'deliverables' => ['30-second animation', 'Basic motion graphics', 'Background music', 'HD delivery']],
                    ['tier' => 'standard', 'name' => '60-Second Video', 'price' => 799, 'days' => 10, 'revisions' => 3, 'deliverables' => ['60-second animation', 'Custom illustrations', 'Voiceover sync', '4K delivery', 'Sound effects']],
                    ['tier' => 'premium', 'name' => '2-Minute Video', 'price' => 1499, 'days' => 14, 'revisions' => 0, 'deliverables' => ['2-minute animation', 'Character animation', 'Script writing', 'Professional voiceover', '4K delivery', 'Social media cuts']],
                ],
            ],
            [
                'category_id' => 5, // Illustrations
                'name' => 'Lottie Animation for Web & Mobile',
                'short_description' => 'I will create smooth, lightweight Lottie animations for your website or app.',
                'description' => "Enhance your digital products with engaging micro-animations using Lottie.\n\n**Animation types:**\n- Loading spinners\n- Success/error states\n- Onboarding animations\n- Icon animations\n- Illustration animations\n- Page transitions\n\n**Benefits of Lottie:**\n- Super lightweight\n- Infinitely scalable\n- Easy to implement\n- Works on all platforms\n\nI create animations that are not only beautiful but also optimized for performance.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Single Animation', 'price' => 79, 'days' => 2, 'revisions' => 2, 'deliverables' => ['1 Lottie animation', 'JSON file', 'GIF preview']],
                    ['tier' => 'standard', 'name' => 'Animation Set', 'price' => 249, 'days' => 5, 'revisions' => 3, 'deliverables' => ['5 Lottie animations', 'Consistent style', 'JSON files', 'Implementation guide']],
                    ['tier' => 'premium', 'name' => 'Animation Library', 'price' => 599, 'days' => 10, 'revisions' => 0, 'deliverables' => ['15 Lottie animations', 'Custom illustrations', 'All formats', 'Source files', 'Documentation']],
                ],
            ],

            // Social Media Services
            [
                'category_id' => 2, // Website Templates (using for social)
                'name' => 'Social Media Design Package',
                'short_description' => 'I will design eye-catching social media graphics that boost your engagement.',
                'description' => "Stand out on social media with professionally designed graphics tailored to your brand.\n\n**Platforms I design for:**\n- Instagram (posts, stories, reels covers)\n- Facebook (posts, covers, ads)\n- LinkedIn (posts, banners)\n- Twitter/X (posts, headers)\n- Pinterest (pins)\n- YouTube (thumbnails, banners)\n\n**What you'll get:**\n- On-brand designs\n- Editable templates (Canva)\n- Content calendar template\n- Hashtag research\n- Posting guidelines\n\nConsistent, professional social media presence made easy!",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Starter Pack', 'price' => 99, 'days' => 2, 'revisions' => 1, 'deliverables' => ['10 post designs', '1 platform', 'PNG files']],
                    ['tier' => 'standard', 'name' => 'Growth Pack', 'price' => 249, 'days' => 4, 'revisions' => 2, 'deliverables' => ['20 post designs', '3 platforms', 'Stories templates', 'Canva templates']],
                    ['tier' => 'premium', 'name' => 'Pro Pack', 'price' => 499, 'days' => 7, 'revisions' => 0, 'deliverables' => ['40 post designs', 'All platforms', 'Content calendar', 'Highlight covers', 'Monthly strategy']],
                ],
            ],

            // Presentation Services
            [
                'category_id' => 11, // Presentations
                'name' => 'Professional Pitch Deck Design',
                'short_description' => 'I will design a compelling pitch deck that helps you win investors and clients.',
                'description' => "Make your presentation unforgettable with a professionally designed pitch deck that tells your story.\n\n**What I create:**\n- Investor pitch decks\n- Sales presentations\n- Keynote speeches\n- Product demos\n- Company profiles\n- Training materials\n\n**Why my pitch decks work:**\n- Clear visual hierarchy\n- Compelling storytelling\n- Data visualization\n- Consistent branding\n- Presentation coaching tips\n\nI've helped startups raise millions with my pitch deck designs. Let me help you make your best impression.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Starter Deck', 'price' => 199, 'days' => 3, 'revisions' => 2, 'deliverables' => ['10 slides', 'PowerPoint/Keynote', 'Basic graphics', 'PDF export']],
                    ['tier' => 'standard', 'name' => 'Professional Deck', 'price' => 399, 'days' => 5, 'revisions' => 3, 'deliverables' => ['20 slides', 'Custom graphics', 'Icon design', 'Google Slides', 'Editable charts']],
                    ['tier' => 'premium', 'name' => 'Investor Deck', 'price' => 799, 'days' => 7, 'revisions' => 0, 'deliverables' => ['30+ slides', 'Custom illustrations', 'Data visualization', 'All formats', 'Presentation script', 'Coaching session']],
                ],
            ],

            // SEO & Marketing Services
            [
                'category_id' => 8, // Code & Scripts
                'name' => 'Technical SEO Audit & Optimization',
                'short_description' => 'I will audit your website and fix technical SEO issues to improve your rankings.',
                'description' => "Unlock your website's full potential with a comprehensive technical SEO audit and implementation.\n\n**What's included:**\n- Complete site crawl\n- Speed optimization\n- Mobile usability check\n- Schema markup implementation\n- XML sitemap optimization\n- Robots.txt review\n- Indexation issues\n- Core Web Vitals fixes\n\n**Detailed report includes:**\n- Priority issue list\n- Competitor analysis\n- Keyword opportunities\n- Implementation roadmap\n\nI don't just identify problems - I fix them. Get actionable results, not just reports.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'SEO Audit', 'price' => 199, 'days' => 3, 'revisions' => 1, 'deliverables' => ['Technical audit report', 'Priority action list', 'Basic recommendations']],
                    ['tier' => 'standard', 'name' => 'Audit + Fixes', 'price' => 449, 'days' => 7, 'revisions' => 2, 'deliverables' => ['Complete audit', 'On-page optimization', 'Speed optimization', 'Schema markup', 'Implementation']],
                    ['tier' => 'premium', 'name' => 'Complete SEO', 'price' => 899, 'days' => 14, 'revisions' => 0, 'deliverables' => ['Full audit', 'All fixes implemented', 'Content optimization', 'Monthly monitoring', 'Competitor tracking', 'Strategy session']],
                ],
            ],

            // 3D Services
            [
                'category_id' => 9, // 3D Assets
                'name' => '3D Product Visualization & Rendering',
                'short_description' => 'I will create photorealistic 3D renders of your product for marketing and e-commerce.',
                'description' => "Showcase your products in the best light with stunning 3D visualizations.\n\n**Services include:**\n- 3D product modeling\n- Photorealistic rendering\n- 360° product views\n- Lifestyle scene renders\n- AR-ready models\n- Product animations\n\n**Industries I serve:**\n- Consumer electronics\n- Furniture & home decor\n- Packaging design\n- Fashion & accessories\n- Industrial products\n- Food & beverage\n\nNo physical samples needed - I can work from drawings, photos, or CAD files.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Simple Product', 'price' => 199, 'days' => 3, 'revisions' => 2, 'deliverables' => ['3D model', '3 render angles', 'High-res images', 'White background']],
                    ['tier' => 'standard', 'name' => 'Detailed Product', 'price' => 449, 'days' => 5, 'revisions' => 3, 'deliverables' => ['Detailed 3D model', '6 render angles', 'Lifestyle scene', '360° spin', 'Web-ready']],
                    ['tier' => 'premium', 'name' => 'Product Package', 'price' => 999, 'days' => 10, 'revisions' => 0, 'deliverables' => ['Complex 3D model', 'Multiple products', 'Lifestyle scenes', 'Animation', 'AR-ready files', 'Source files']],
                ],
            ],

            // More UI/UX Services
            [
                'category_id' => 10, // Wireframes
                'name' => 'UX Research & Wireframing',
                'short_description' => 'I will conduct UX research and create detailed wireframes for your digital product.',
                'description' => "Build products that users love with research-driven UX design and detailed wireframes.\n\n**My UX process:**\n1. User research & interviews\n2. Competitor analysis\n3. User persona development\n4. User journey mapping\n5. Information architecture\n6. Wireframe creation\n7. Usability testing\n\n**Deliverables:**\n- Research findings report\n- User personas\n- User flow diagrams\n- Low/high-fidelity wireframes\n- Clickable prototype\n- Testing recommendations\n\nGood UX is invisible - let me make your product intuitive.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Wireframes Only', 'price' => 299, 'days' => 4, 'revisions' => 2, 'deliverables' => ['Up to 10 wireframes', 'User flow diagram', 'Figma files']],
                    ['tier' => 'standard', 'name' => 'UX + Wireframes', 'price' => 599, 'days' => 7, 'revisions' => 3, 'deliverables' => ['Competitor analysis', 'User personas', '20 wireframes', 'Clickable prototype', 'Documentation']],
                    ['tier' => 'premium', 'name' => 'Complete UX', 'price' => 1299, 'days' => 14, 'revisions' => 0, 'deliverables' => ['Full research', 'User interviews', 'Journey maps', 'All wireframes', 'Usability testing', 'UX report']],
                ],
            ],
            [
                'category_id' => 1, // UI Kits
                'name' => 'Figma Design System Creation',
                'short_description' => 'I will build a scalable Figma design system with components, tokens, and documentation.',
                'description' => "Streamline your design process with a comprehensive, well-organized design system.\n\n**What you'll get:**\n- Design tokens (colors, typography, spacing)\n- Atomic components\n- Complex components\n- Page templates\n- Dark/light modes\n- Documentation\n\n**Benefits:**\n- Faster design iterations\n- Consistent user experience\n- Easy developer handoff\n- Scalable for teams\n- Reduced design debt\n\nI build design systems that designers love to use and developers love to implement.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Starter System', 'price' => 499, 'days' => 5, 'revisions' => 2, 'deliverables' => ['Core components', 'Design tokens', 'Basic documentation', 'Figma file']],
                    ['tier' => 'standard', 'name' => 'Professional System', 'price' => 999, 'days' => 10, 'revisions' => 3, 'deliverables' => ['Full component library', 'Variants & states', 'Dark mode', 'Page templates', 'Documentation']],
                    ['tier' => 'premium', 'name' => 'Enterprise System', 'price' => 2499, 'days' => 21, 'revisions' => 0, 'deliverables' => ['Complete design system', 'All components', 'Multi-brand support', 'Storybook-ready specs', 'Team training', 'Maintenance guide']],
                ],
            ],

            // Writing Services
            [
                'category_id' => 8, // Code & Scripts
                'name' => 'Technical Documentation Writing',
                'short_description' => 'I will write clear, comprehensive technical documentation for your software or API.',
                'description' => "Make your product easier to use with professional technical documentation.\n\n**Documentation types:**\n- API documentation\n- User guides\n- Developer docs\n- README files\n- Installation guides\n- Troubleshooting guides\n\n**What sets me apart:**\n- Developer background\n- Clear, concise writing\n- Code examples included\n- Markdown/docs-as-code\n- SEO optimized\n\nGood documentation reduces support tickets and improves user satisfaction. Let me help!",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'README/Quick Start', 'price' => 149, 'days' => 2, 'revisions' => 2, 'deliverables' => ['README file', 'Quick start guide', 'Basic examples', 'Markdown format']],
                    ['tier' => 'standard', 'name' => 'API Documentation', 'price' => 399, 'days' => 5, 'revisions' => 3, 'deliverables' => ['Full API docs', 'Code examples', 'Authentication guide', 'Error handling', 'Postman collection']],
                    ['tier' => 'premium', 'name' => 'Complete Docs', 'price' => 899, 'days' => 10, 'revisions' => 0, 'deliverables' => ['Full documentation site', 'User guides', 'API reference', 'Tutorials', 'Video scripts', 'Maintenance plan']],
                ],
            ],
            [
                'category_id' => 2, // Website Templates
                'name' => 'Website Copywriting & Content',
                'short_description' => 'I will write compelling website copy that converts visitors into customers.',
                'description' => "Words matter. Get website copy that engages, persuades, and converts.\n\n**What I write:**\n- Homepage copy\n- About pages\n- Service/product pages\n- Landing pages\n- Blog content\n- Email sequences\n\n**My approach:**\n- Deep brand research\n- Audience analysis\n- SEO optimization\n- Conversion focus\n- Brand voice development\n\nI combine creativity with strategy to write copy that resonates with your audience and drives action.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Landing Page', 'price' => 199, 'days' => 3, 'revisions' => 2, 'deliverables' => ['Landing page copy', 'SEO meta tags', 'CTA optimization']],
                    ['tier' => 'standard', 'name' => 'Website Copy', 'price' => 499, 'days' => 7, 'revisions' => 3, 'deliverables' => ['5 page website copy', 'SEO optimization', 'Brand voice guide', 'Meta descriptions']],
                    ['tier' => 'premium', 'name' => 'Complete Content', 'price' => 999, 'days' => 14, 'revisions' => 0, 'deliverables' => ['10+ pages', 'Blog posts (5)', 'Email sequence', 'Social media copy', 'Content strategy']],
                ],
            ],

            // More Development Services
            [
                'category_id' => 3, // Mobile Apps
                'name' => 'React Native Mobile App Development',
                'short_description' => 'I will build a cross-platform mobile app using React Native for iOS and Android.',
                'description' => "Get a native-quality mobile app for both platforms with a single codebase.\n\n**What I build:**\n- Consumer apps\n- E-commerce apps\n- Social networking apps\n- On-demand service apps\n- Enterprise apps\n- MVP/startup apps\n\n**Tech stack:**\n- React Native\n- TypeScript\n- Redux/Context\n- Firebase/Supabase\n- REST/GraphQL APIs\n- Push notifications\n\nI deliver apps that feel native and perform excellently on both iOS and Android.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'MVP App', 'price' => 1499, 'days' => 14, 'revisions' => 2, 'deliverables' => ['Up to 10 screens', 'Basic features', 'API integration', 'App store ready']],
                    ['tier' => 'standard', 'name' => 'Standard App', 'price' => 2999, 'days' => 30, 'revisions' => 3, 'deliverables' => ['Up to 25 screens', 'Authentication', 'Push notifications', 'Payments', 'Admin panel']],
                    ['tier' => 'premium', 'name' => 'Enterprise App', 'price' => 5999, 'days' => 60, 'revisions' => 0, 'deliverables' => ['Unlimited screens', 'Complex features', 'Custom backend', 'Analytics', '3 months support', 'App store submission']],
                ],
            ],
            [
                'category_id' => 8, // Code & Scripts
                'name' => 'Custom WordPress Plugin Development',
                'short_description' => 'I will develop a custom WordPress plugin tailored to your specific requirements.',
                'description' => "Get functionality that doesn't exist with a custom-built WordPress plugin.\n\n**What I can build:**\n- Custom post types & taxonomies\n- API integrations\n- Payment gateways\n- Membership systems\n- Booking systems\n- Custom workflows\n- Admin dashboards\n\n**Quality standards:**\n- WordPress coding standards\n- Security best practices\n- Well-documented code\n- Update compatible\n- Performance optimized\n\nIf you can describe it, I can build it.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Simple Plugin', 'price' => 299, 'days' => 5, 'revisions' => 2, 'deliverables' => ['Basic functionality', 'Admin settings', 'Documentation', 'Installation support']],
                    ['tier' => 'standard', 'name' => 'Custom Plugin', 'price' => 699, 'days' => 10, 'revisions' => 3, 'deliverables' => ['Complex features', 'Database integration', 'API connections', 'Shortcodes', 'Admin dashboard']],
                    ['tier' => 'premium', 'name' => 'Enterprise Plugin', 'price' => 1499, 'days' => 21, 'revisions' => 0, 'deliverables' => ['Advanced functionality', 'Multi-site support', 'WooCommerce integration', 'REST API', 'Ongoing support', 'Updates']],
                ],
            ],
            [
                'category_id' => 8, // Code & Scripts
                'name' => 'Python Automation & Scripting',
                'short_description' => 'I will create Python scripts to automate your repetitive tasks and workflows.',
                'description' => "Save hours every week by automating your tedious tasks with custom Python scripts.\n\n**Automation examples:**\n- Data scraping & extraction\n- Report generation\n- File processing\n- Email automation\n- API integrations\n- Database operations\n- Spreadsheet automation\n\n**What you get:**\n- Clean, documented code\n- Easy to run/schedule\n- Error handling\n- Logging\n- Installation instructions\n\nTell me what you do manually, and I'll automate it.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Simple Script', 'price' => 99, 'days' => 2, 'revisions' => 2, 'deliverables' => ['Single automation script', 'Documentation', 'Setup guide']],
                    ['tier' => 'standard', 'name' => 'Automation Suite', 'price' => 299, 'days' => 5, 'revisions' => 3, 'deliverables' => ['Multiple scripts', 'Scheduling setup', 'Error notifications', 'Documentation']],
                    ['tier' => 'premium', 'name' => 'Complete Solution', 'price' => 699, 'days' => 10, 'revisions' => 0, 'deliverables' => ['Full automation system', 'Dashboard', 'API development', 'Deployment', 'Training', 'Support']],
                ],
            ],
            [
                'category_id' => 8, // Code & Scripts
                'name' => 'Database Design & Optimization',
                'short_description' => 'I will design, optimize, or migrate your database for maximum performance.',
                'description' => "Build a solid data foundation with expert database design and optimization.\n\n**Services:**\n- Database schema design\n- Query optimization\n- Index optimization\n- Migration planning\n- Performance tuning\n- Backup strategies\n\n**Databases I work with:**\n- MySQL/MariaDB\n- PostgreSQL\n- MongoDB\n- Redis\n- Elasticsearch\n- SQLite\n\nA well-designed database is the foundation of every successful application.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Schema Design', 'price' => 249, 'days' => 3, 'revisions' => 2, 'deliverables' => ['Database schema', 'ER diagram', 'Documentation']],
                    ['tier' => 'standard', 'name' => 'Optimization', 'price' => 499, 'days' => 5, 'revisions' => 3, 'deliverables' => ['Performance audit', 'Query optimization', 'Index optimization', 'Implementation', 'Report']],
                    ['tier' => 'premium', 'name' => 'Complete Service', 'price' => 999, 'days' => 10, 'revisions' => 0, 'deliverables' => ['Full design/optimization', 'Migration', 'Backup setup', 'Monitoring setup', 'Documentation', 'Training']],
                ],
            ],

            // More Design Services
            [
                'category_id' => 4, // Icons
                'name' => 'Custom Icon Set Design',
                'short_description' => 'I will design a cohesive custom icon set for your website, app, or brand.',
                'description' => "Get icons that are uniquely yours with a custom-designed icon set.\n\n**Icon styles I create:**\n- Line icons\n- Solid/filled icons\n- Duotone icons\n- 3D icons\n- Animated icons\n- Isometric icons\n\n**Deliverables:**\n- Vector source files (SVG, AI)\n- Multiple sizes (16-512px)\n- Multiple formats\n- Icon font (optional)\n- Usage guidelines\n\nEvery icon is pixel-perfect and designed for clarity at any size.",
                'packages' => [
                    ['tier' => 'basic', 'name' => '10 Icons', 'price' => 99, 'days' => 3, 'revisions' => 2, 'deliverables' => ['10 custom icons', 'SVG + PNG files', 'One style']],
                    ['tier' => 'standard', 'name' => '30 Icons', 'price' => 249, 'days' => 5, 'revisions' => 3, 'deliverables' => ['30 custom icons', 'All formats', 'Two styles', 'Icon font']],
                    ['tier' => 'premium', 'name' => '60+ Icons', 'price' => 499, 'days' => 10, 'revisions' => 0, 'deliverables' => ['60+ custom icons', 'All formats', 'Multiple styles', 'Icon font', 'Figma library', 'Guidelines']],
                ],
            ],
            [
                'category_id' => 5, // Illustrations
                'name' => 'Infographic Design',
                'short_description' => 'I will design engaging infographics that make your data easy to understand.',
                'description' => "Transform complex data into beautiful, shareable visual stories.\n\n**Infographic types:**\n- Statistical infographics\n- Process/timeline infographics\n- Comparison infographics\n- Geographic infographics\n- Resume infographics\n- Interactive infographics\n\n**What I provide:**\n- Custom design\n- Data visualization\n- Icon design\n- Print & web formats\n- Source files\n\nMake your data memorable and shareable!",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Simple Infographic', 'price' => 149, 'days' => 3, 'revisions' => 2, 'deliverables' => ['1 infographic', 'Web format', 'PNG + PDF']],
                    ['tier' => 'standard', 'name' => 'Detailed Infographic', 'price' => 299, 'days' => 5, 'revisions' => 3, 'deliverables' => ['1 detailed infographic', 'Custom icons', 'Print + web', 'Source file']],
                    ['tier' => 'premium', 'name' => 'Infographic Set', 'price' => 599, 'days' => 10, 'revisions' => 0, 'deliverables' => ['3 infographics', 'Consistent style', 'Custom illustrations', 'All formats', 'Social media versions']],
                ],
            ],
            [
                'category_id' => 2, // Website Templates
                'name' => 'Email Template Design & Development',
                'short_description' => 'I will design and code responsive HTML email templates that work everywhere.',
                'description' => "Get email templates that look great in every inbox with bulletproof HTML coding.\n\n**What I create:**\n- Newsletter templates\n- Promotional emails\n- Transactional emails\n- Welcome sequences\n- Abandoned cart emails\n- Event invitations\n\n**Features:**\n- Mobile responsive\n- Dark mode support\n- Client tested\n- ESP compatible\n- Easy to edit\n\nI test across 90+ email clients to ensure your emails look perfect everywhere.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Single Template', 'price' => 99, 'days' => 2, 'revisions' => 2, 'deliverables' => ['1 email template', 'Mobile responsive', 'HTML file', 'ESP compatible']],
                    ['tier' => 'standard', 'name' => 'Template Pack', 'price' => 249, 'days' => 5, 'revisions' => 3, 'deliverables' => ['5 email templates', 'Modular design', 'Dark mode', 'Testing report']],
                    ['tier' => 'premium', 'name' => 'Email System', 'price' => 499, 'days' => 10, 'revisions' => 0, 'deliverables' => ['10+ templates', 'Full email system', 'Automation flows', 'ESP setup', 'Documentation']],
                ],
            ],
            [
                'category_id' => 11, // Presentations
                'name' => 'Canva Template Design',
                'short_description' => 'I will create professional Canva templates for your brand that are easy to customize.',
                'description' => "Get branded templates that empower your team to create stunning content without a designer.\n\n**Template types:**\n- Social media templates\n- Presentation templates\n- Document templates\n- Marketing materials\n- Brand kits\n- Lead magnets\n\n**Benefits:**\n- Easy to customize\n- Brand consistent\n- Canva Pro compatible\n- Team sharing ready\n- Training included\n\nGive your team the tools to create professional content independently.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Starter Pack', 'price' => 99, 'days' => 2, 'revisions' => 2, 'deliverables' => ['10 templates', 'One category', 'Canva links']],
                    ['tier' => 'standard', 'name' => 'Business Pack', 'price' => 249, 'days' => 5, 'revisions' => 3, 'deliverables' => ['25 templates', 'Multiple categories', 'Brand kit setup', 'Usage guide']],
                    ['tier' => 'premium', 'name' => 'Complete Brand', 'price' => 499, 'days' => 10, 'revisions' => 0, 'deliverables' => ['50+ templates', 'All categories', 'Brand kit', 'Team setup', 'Training session', 'Support']],
                ],
            ],

            // Consulting Services
            [
                'category_id' => 12, // Design Systems
                'name' => 'UI/UX Design Consultation',
                'short_description' => 'I will review your product design and provide expert recommendations for improvement.',
                'description' => "Get professional insights to take your product design to the next level.\n\n**What I review:**\n- User interface design\n- User experience flow\n- Visual hierarchy\n- Accessibility\n- Conversion optimization\n- Mobile experience\n\n**Deliverables:**\n- Video walkthrough\n- Annotated screenshots\n- Priority recommendations\n- Quick wins list\n- Long-term roadmap\n\nI've reviewed hundreds of products - let me share what works and what doesn't.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Quick Review', 'price' => 99, 'days' => 1, 'revisions' => 1, 'deliverables' => ['15-min video review', 'Top 5 recommendations', 'Quick wins list']],
                    ['tier' => 'standard', 'name' => 'Detailed Review', 'price' => 249, 'days' => 3, 'revisions' => 2, 'deliverables' => ['45-min video review', 'Annotated report', 'Priority matrix', 'Competitor comparison']],
                    ['tier' => 'premium', 'name' => 'Strategic Review', 'price' => 499, 'days' => 5, 'revisions' => 0, 'deliverables' => ['Complete audit', 'Detailed report', 'User testing insights', '1-hour strategy call', 'Action roadmap', 'Follow-up support']],
                ],
            ],
            [
                'category_id' => 8, // Code & Scripts
                'name' => 'Code Review & Consultation',
                'short_description' => 'I will review your codebase and provide expert feedback on architecture and best practices.',
                'description' => "Get your code reviewed by an experienced developer to improve quality and prevent issues.\n\n**What I review:**\n- Code architecture\n- Best practices\n- Security vulnerabilities\n- Performance issues\n- Maintainability\n- Testing coverage\n\n**Languages/frameworks:**\n- JavaScript/TypeScript\n- React/Next.js/Vue\n- PHP/Laravel\n- Python\n- Node.js\n\nPrevent technical debt before it becomes a problem.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Quick Review', 'price' => 149, 'days' => 2, 'revisions' => 1, 'deliverables' => ['Code review', 'Key issues report', 'Recommendations']],
                    ['tier' => 'standard', 'name' => 'Detailed Review', 'price' => 349, 'days' => 5, 'revisions' => 2, 'deliverables' => ['Comprehensive review', 'Security audit', 'Performance analysis', 'Refactoring suggestions', 'Video walkthrough']],
                    ['tier' => 'premium', 'name' => 'Complete Audit', 'price' => 699, 'days' => 10, 'revisions' => 0, 'deliverables' => ['Full codebase audit', 'Architecture review', 'Test coverage analysis', 'Technical debt report', '2-hour consultation', 'Action plan']],
                ],
            ],

            // Additional Services
            [
                'category_id' => 6, // Themes
                'name' => 'Webflow Website Development',
                'short_description' => 'I will design and build a stunning, responsive website using Webflow.',
                'description' => "Get a beautiful, custom-coded website without the hassle of traditional development.\n\n**Why Webflow:**\n- Pixel-perfect design\n- No coding required to edit\n- Built-in CMS\n- Fast hosting\n- SEO optimized\n- Animations included\n\n**What I build:**\n- Marketing websites\n- Portfolio sites\n- Blogs\n- Landing pages\n- E-commerce (basic)\n- Corporate sites\n\nWebflow combines the power of code with the ease of no-code.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Landing Page', 'price' => 399, 'days' => 3, 'revisions' => 2, 'deliverables' => ['1 page design', 'Mobile responsive', 'Basic animations', 'Webflow hosting']],
                    ['tier' => 'standard', 'name' => 'Business Site', 'price' => 899, 'days' => 7, 'revisions' => 3, 'deliverables' => ['5 pages', 'CMS setup', 'Animations', 'Contact forms', 'SEO setup', 'Training']],
                    ['tier' => 'premium', 'name' => 'Complete Site', 'price' => 1999, 'days' => 14, 'revisions' => 0, 'deliverables' => ['10+ pages', 'Full CMS', 'Advanced animations', 'Integrations', 'E-commerce ready', 'Ongoing support']],
                ],
            ],
            [
                'category_id' => 3, // Mobile Apps
                'name' => 'Flutter Mobile App Development',
                'short_description' => 'I will build a beautiful cross-platform mobile app using Flutter and Dart.',
                'description' => "Build natively compiled applications for mobile from a single codebase.\n\n**Why Flutter:**\n- Beautiful UI\n- Native performance\n- Single codebase\n- Fast development\n- Growing ecosystem\n\n**Apps I build:**\n- E-commerce apps\n- Social apps\n- Business apps\n- Utility apps\n- MVP/startup apps\n\nFlutter delivers beautiful, fast apps for iOS and Android simultaneously.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'MVP App', 'price' => 1299, 'days' => 14, 'revisions' => 2, 'deliverables' => ['Up to 8 screens', 'Basic features', 'API integration', 'Both platforms']],
                    ['tier' => 'standard', 'name' => 'Standard App', 'price' => 2499, 'days' => 30, 'revisions' => 3, 'deliverables' => ['Up to 20 screens', 'Authentication', 'Push notifications', 'Admin panel', 'Store submission']],
                    ['tier' => 'premium', 'name' => 'Full App', 'price' => 4999, 'days' => 45, 'revisions' => 0, 'deliverables' => ['Unlimited screens', 'Custom backend', 'Advanced features', 'Analytics', '3 months support']],
                ],
            ],
            [
                'category_id' => 9, // 3D Assets
                'name' => '3D Character Modeling',
                'short_description' => 'I will create custom 3D character models for games, animation, or marketing.',
                'description' => "Bring your characters to life with professional 3D modeling and texturing.\n\n**Services:**\n- Character design\n- 3D modeling\n- Texturing\n- Rigging\n- Animation-ready\n- Game-ready models\n\n**Styles:**\n- Realistic\n- Stylized\n- Cartoon\n- Anime\n- Low-poly\n\nI create characters that are ready for whatever you need - games, animation, or promotional materials.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Simple Character', 'price' => 299, 'days' => 5, 'revisions' => 2, 'deliverables' => ['3D model', 'Basic textures', 'T-pose', 'FBX/OBJ files']],
                    ['tier' => 'standard', 'name' => 'Detailed Character', 'price' => 599, 'days' => 10, 'revisions' => 3, 'deliverables' => ['Detailed model', 'PBR textures', 'Basic rig', 'Multiple formats']],
                    ['tier' => 'premium', 'name' => 'Production Character', 'price' => 1299, 'days' => 21, 'revisions' => 0, 'deliverables' => ['High-detail model', 'Full texturing', 'Complete rig', 'Facial rig', 'Basic animations', 'Game-ready']],
                ],
            ],
            [
                'category_id' => 5, // Illustrations
                'name' => 'Book Cover Design',
                'short_description' => 'I will design a professional book cover that captures readers\' attention.',
                'description' => "Get a book cover that sells with professional design tailored to your genre and audience.\n\n**What I design:**\n- Fiction covers\n- Non-fiction covers\n- Ebook covers\n- Print covers (spine + back)\n- Audiobook covers\n- Series branding\n\n**What's included:**\n- Genre research\n- Multiple concepts\n- Stock image sourcing\n- Print-ready files\n- Kindle/ebook formats\n\nYour book will stand out on any shelf or marketplace.",
                'packages' => [
                    ['tier' => 'basic', 'name' => 'Ebook Cover', 'price' => 99, 'days' => 2, 'revisions' => 2, 'deliverables' => ['Front cover only', 'Ebook format', '2 concepts', 'JPG + PNG']],
                    ['tier' => 'standard', 'name' => 'Print + Ebook', 'price' => 199, 'days' => 4, 'revisions' => 3, 'deliverables' => ['Full wrap cover', 'Ebook version', '3 concepts', 'Print-ready PDF', 'Source file']],
                    ['tier' => 'premium', 'name' => 'Complete Package', 'price' => 399, 'days' => 7, 'revisions' => 0, 'deliverables' => ['Premium cover', 'Custom illustration', 'Audiobook cover', 'Social graphics', '3D mockups', 'Marketing materials']],
                ],
            ],
        ];

        foreach ($services as $index => $serviceData) {
            $packages = $serviceData['packages'];
            unset($serviceData['packages']);

            $service = Service::create([
                'seller_id' => $sellerIds[array_rand($sellerIds)],
                'category_id' => $serviceData['category_id'],
                'name' => $serviceData['name'],
                'short_description' => $serviceData['short_description'],
                'description' => $serviceData['description'],
                'status' => 'published',
                'is_featured' => $index < 8, // First 8 are featured
                'accepts_custom_orders' => true,
                'views_count' => rand(100, 2000),
                'orders_count' => rand(5, 150),
                'average_rating' => rand(45, 50) / 10, // 4.5 - 5.0
                'reviews_count' => rand(10, 200),
                'published_at' => Carbon::now()->subDays(rand(1, 90)),
            ]);

            // Create packages for each service
            foreach ($packages as $sortOrder => $package) {
                ServicePackage::create([
                    'service_id' => $service->id,
                    'name' => $package['name'],
                    'tier' => $package['tier'],
                    'description' => "Perfect for " . strtolower($package['name']) . " projects.",
                    'price' => $package['price'],
                    'delivery_days' => $package['days'],
                    'revisions' => $package['revisions'],
                    'deliverables' => $package['deliverables'],
                    'is_active' => true,
                    'sort_order' => $sortOrder,
                ]);
            }
        }

        $this->command->info('Created 40 realistic services with packages!');
    }
}

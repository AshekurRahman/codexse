<?php

namespace Database\Seeders;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RealSellersSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = [
            // US-based designers
            ['name' => 'Sarah Mitchell', 'store' => 'Mitchell Design Co', 'specialty' => 'UI/UX Design', 'location' => 'San Francisco, CA'],
            ['name' => 'James Wilson', 'store' => 'Wilson Creative Studio', 'specialty' => 'Brand Identity', 'location' => 'New York, NY'],
            ['name' => 'Emily Chen', 'store' => 'Chen Digital', 'specialty' => 'Web Development', 'location' => 'Seattle, WA'],
            ['name' => 'Michael Brown', 'store' => 'Brown & Associates', 'specialty' => 'Motion Graphics', 'location' => 'Los Angeles, CA'],
            ['name' => 'Jessica Taylor', 'store' => 'Taylor UI Labs', 'specialty' => 'Mobile App Design', 'location' => 'Austin, TX'],
            ['name' => 'David Martinez', 'store' => 'Martinez Studio', 'specialty' => 'Illustration', 'location' => 'Miami, FL'],
            ['name' => 'Amanda Johnson', 'store' => 'AJ Designs', 'specialty' => 'Product Design', 'location' => 'Chicago, IL'],
            ['name' => 'Christopher Lee', 'store' => 'Lee Creative', 'specialty' => 'WordPress Development', 'location' => 'Denver, CO'],
            ['name' => 'Stephanie Garcia', 'store' => 'Garcia Graphics', 'specialty' => 'Logo Design', 'location' => 'Phoenix, AZ'],
            ['name' => 'Daniel Thompson', 'store' => 'Thompson Digital', 'specialty' => 'E-commerce', 'location' => 'Portland, OR'],

            // UK-based designers
            ['name' => 'Oliver Wright', 'store' => 'Wright Design London', 'specialty' => 'Brand Strategy', 'location' => 'London, UK'],
            ['name' => 'Charlotte Evans', 'store' => 'Evans Creative', 'specialty' => 'Web Design', 'location' => 'Manchester, UK'],
            ['name' => 'Harry Baker', 'store' => 'Baker Digital Agency', 'specialty' => 'Full Stack Development', 'location' => 'Birmingham, UK'],
            ['name' => 'Sophie Turner', 'store' => 'Turner Studios', 'specialty' => '3D Design', 'location' => 'Edinburgh, UK'],
            ['name' => 'George Clark', 'store' => 'Clark & Co Design', 'specialty' => 'Print Design', 'location' => 'Bristol, UK'],

            // European designers
            ['name' => 'Lucas Müller', 'store' => 'Müller Design Berlin', 'specialty' => 'Industrial Design', 'location' => 'Berlin, Germany'],
            ['name' => 'Emma Schmidt', 'store' => 'Schmidt Creative', 'specialty' => 'UX Research', 'location' => 'Munich, Germany'],
            ['name' => 'Antoine Dubois', 'store' => 'Dubois Design Paris', 'specialty' => 'Fashion Design', 'location' => 'Paris, France'],
            ['name' => 'Camille Laurent', 'store' => 'Laurent Studio', 'specialty' => 'Typography', 'location' => 'Lyon, France'],
            ['name' => 'Marco Rossi', 'store' => 'Rossi Design Milano', 'specialty' => 'Luxury Branding', 'location' => 'Milan, Italy'],
            ['name' => 'Giulia Conti', 'store' => 'Conti Creative', 'specialty' => 'Interior Design', 'location' => 'Rome, Italy'],
            ['name' => 'Jan de Vries', 'store' => 'de Vries Digital', 'specialty' => 'React Development', 'location' => 'Amsterdam, Netherlands'],
            ['name' => 'Sofia Andersson', 'store' => 'Andersson Design', 'specialty' => 'Minimalist Design', 'location' => 'Stockholm, Sweden'],
            ['name' => 'Erik Johansson', 'store' => 'Johansson Studios', 'specialty' => 'Photo Manipulation', 'location' => 'Gothenburg, Sweden'],
            ['name' => 'Maria Santos', 'store' => 'Santos Creative', 'specialty' => 'Social Media Design', 'location' => 'Lisbon, Portugal'],

            // Canadian designers
            ['name' => 'William Morrison', 'store' => 'Morrison Design Toronto', 'specialty' => 'App Development', 'location' => 'Toronto, Canada'],
            ['name' => 'Emma Tremblay', 'store' => 'Tremblay Studio', 'specialty' => 'Bilingual Design', 'location' => 'Montreal, Canada'],
            ['name' => 'Liam Campbell', 'store' => 'Campbell Creative', 'specialty' => 'Video Production', 'location' => 'Vancouver, Canada'],

            // Australian designers
            ['name' => 'Jack Robinson', 'store' => 'Robinson Design Sydney', 'specialty' => 'Startup Branding', 'location' => 'Sydney, Australia'],
            ['name' => 'Olivia Williams', 'store' => 'Williams Studio', 'specialty' => 'Package Design', 'location' => 'Melbourne, Australia'],
            ['name' => 'Thomas Kelly', 'store' => 'Kelly Creative', 'specialty' => 'Environmental Design', 'location' => 'Brisbane, Australia'],

            // Asian designers
            ['name' => 'Yuki Tanaka', 'store' => 'Tanaka Design Tokyo', 'specialty' => 'Minimalist UI', 'location' => 'Tokyo, Japan'],
            ['name' => 'Hiroshi Yamamoto', 'store' => 'Yamamoto Studios', 'specialty' => 'Game Design', 'location' => 'Osaka, Japan'],
            ['name' => 'Min-Jun Kim', 'store' => 'Kim Design Seoul', 'specialty' => 'K-Style Graphics', 'location' => 'Seoul, South Korea'],
            ['name' => 'Ji-Yeon Park', 'store' => 'Park Creative', 'specialty' => 'Animation', 'location' => 'Busan, South Korea'],
            ['name' => 'Wei Zhang', 'store' => 'Zhang Digital', 'specialty' => 'E-commerce Design', 'location' => 'Shanghai, China'],
            ['name' => 'Li Na', 'store' => 'Li Creative Studio', 'specialty' => 'Mobile UI', 'location' => 'Beijing, China'],
            ['name' => 'Raj Patel', 'store' => 'Patel Design Mumbai', 'specialty' => 'Web Development', 'location' => 'Mumbai, India'],
            ['name' => 'Priya Sharma', 'store' => 'Sharma Studios', 'specialty' => 'Illustration', 'location' => 'Bangalore, India'],
            ['name' => 'Arjun Kapoor', 'store' => 'Kapoor Creative', 'specialty' => 'Motion Design', 'location' => 'Delhi, India'],
            ['name' => 'Ananya Gupta', 'store' => 'Gupta Digital', 'specialty' => 'Brand Design', 'location' => 'Hyderabad, India'],

            // Middle Eastern designers
            ['name' => 'Ahmed Hassan', 'store' => 'Hassan Design Dubai', 'specialty' => 'Luxury Branding', 'location' => 'Dubai, UAE'],
            ['name' => 'Fatima Al-Rashid', 'store' => 'Al-Rashid Creative', 'specialty' => 'Arabic Typography', 'location' => 'Abu Dhabi, UAE'],
            ['name' => 'Omar Khalil', 'store' => 'Khalil Studios', 'specialty' => 'Architecture Viz', 'location' => 'Cairo, Egypt'],
            ['name' => 'Layla Ibrahim', 'store' => 'Ibrahim Design', 'specialty' => 'Fashion Branding', 'location' => 'Riyadh, Saudi Arabia'],

            // Latin American designers
            ['name' => 'Carlos Rodriguez', 'store' => 'Rodriguez Design', 'specialty' => 'Vibrant Branding', 'location' => 'Mexico City, Mexico'],
            ['name' => 'Isabella Fernandez', 'store' => 'Fernandez Creative', 'specialty' => 'Packaging Design', 'location' => 'São Paulo, Brazil'],
            ['name' => 'Diego Silva', 'store' => 'Silva Studios', 'specialty' => 'Sports Design', 'location' => 'Rio de Janeiro, Brazil'],
            ['name' => 'Valentina Lopez', 'store' => 'Lopez Design', 'specialty' => 'Editorial Design', 'location' => 'Buenos Aires, Argentina'],
            ['name' => 'Andres Morales', 'store' => 'Morales Creative', 'specialty' => 'Street Art Design', 'location' => 'Bogotá, Colombia'],

            // More US designers
            ['name' => 'Rachel Green', 'store' => 'Green Design NYC', 'specialty' => 'Fashion UI', 'location' => 'New York, NY'],
            ['name' => 'Kevin Park', 'store' => 'Park Digital LA', 'specialty' => 'Entertainment Design', 'location' => 'Los Angeles, CA'],
            ['name' => 'Michelle Adams', 'store' => 'Adams Creative', 'specialty' => 'Healthcare UX', 'location' => 'Boston, MA'],
            ['name' => 'Brandon Lee', 'store' => 'Lee Tech Design', 'specialty' => 'SaaS Design', 'location' => 'San Jose, CA'],
            ['name' => 'Ashley Morgan', 'store' => 'Morgan Studios', 'specialty' => 'Food & Beverage', 'location' => 'Nashville, TN'],
            ['name' => 'Justin Harris', 'store' => 'Harris Digital', 'specialty' => 'Fintech Design', 'location' => 'Charlotte, NC'],
            ['name' => 'Samantha White', 'store' => 'White Creative Co', 'specialty' => 'Non-profit Design', 'location' => 'Washington, DC'],
            ['name' => 'Tyler Brooks', 'store' => 'Brooks Design', 'specialty' => 'Real Estate', 'location' => 'Dallas, TX'],
            ['name' => 'Megan Scott', 'store' => 'Scott Studios', 'specialty' => 'Education Design', 'location' => 'Atlanta, GA'],
            ['name' => 'Ryan Cooper', 'store' => 'Cooper Creative', 'specialty' => 'Automotive Design', 'location' => 'Detroit, MI'],

            // More European designers
            ['name' => 'Felix Weber', 'store' => 'Weber Design', 'specialty' => 'Sustainable Design', 'location' => 'Zurich, Switzerland'],
            ['name' => 'Anna Kowalski', 'store' => 'Kowalski Creative', 'specialty' => 'Illustration', 'location' => 'Warsaw, Poland'],
            ['name' => 'Viktor Novak', 'store' => 'Novak Studios', 'specialty' => '3D Animation', 'location' => 'Prague, Czech Republic'],
            ['name' => 'Elena Petrova', 'store' => 'Petrova Design', 'specialty' => 'Fashion Branding', 'location' => 'Moscow, Russia'],
            ['name' => 'Nikos Papadopoulos', 'store' => 'Papadopoulos Creative', 'specialty' => 'Tourism Design', 'location' => 'Athens, Greece'],
            ['name' => 'Ingrid Larsen', 'store' => 'Larsen Design Oslo', 'specialty' => 'Scandinavian Style', 'location' => 'Oslo, Norway'],
            ['name' => 'Lars Nielsen', 'store' => 'Nielsen Studios', 'specialty' => 'Product Design', 'location' => 'Copenhagen, Denmark'],
            ['name' => 'Katarina Horvat', 'store' => 'Horvat Creative', 'specialty' => 'Event Design', 'location' => 'Zagreb, Croatia'],
            ['name' => 'Miguel Fernandes', 'store' => 'Fernandes Design', 'specialty' => 'Wine & Spirits', 'location' => 'Porto, Portugal'],
            ['name' => 'Ava O\'Brien', 'store' => 'O\'Brien Studios', 'specialty' => 'Celtic Design', 'location' => 'Dublin, Ireland'],

            // More Asian designers
            ['name' => 'Sanjay Krishnan', 'store' => 'Krishnan Digital', 'specialty' => 'Tech Startups', 'location' => 'Chennai, India'],
            ['name' => 'Mei Lin', 'store' => 'Lin Design', 'specialty' => 'Calligraphy', 'location' => 'Hong Kong'],
            ['name' => 'Thanh Nguyen', 'store' => 'Nguyen Creative', 'specialty' => 'Coffee Branding', 'location' => 'Ho Chi Minh City, Vietnam'],
            ['name' => 'Aisha Rahman', 'store' => 'Rahman Studios', 'specialty' => 'Islamic Art', 'location' => 'Kuala Lumpur, Malaysia'],
            ['name' => 'Budi Santoso', 'store' => 'Santoso Design', 'specialty' => 'Batik Modern', 'location' => 'Jakarta, Indonesia'],
            ['name' => 'Jirapat Suwanprasert', 'store' => 'Suwanprasert Creative', 'specialty' => 'Hospitality', 'location' => 'Bangkok, Thailand'],
            ['name' => 'Maria Reyes', 'store' => 'Reyes Design Manila', 'specialty' => 'E-commerce', 'location' => 'Manila, Philippines'],

            // African designers
            ['name' => 'Kwame Asante', 'store' => 'Asante Creative', 'specialty' => 'African Patterns', 'location' => 'Accra, Ghana'],
            ['name' => 'Amara Diallo', 'store' => 'Diallo Design', 'specialty' => 'Fashion Tech', 'location' => 'Dakar, Senegal'],
            ['name' => 'Thabo Molefe', 'store' => 'Molefe Studios', 'specialty' => 'Sports Branding', 'location' => 'Johannesburg, South Africa'],
            ['name' => 'Zara Okonkwo', 'store' => 'Okonkwo Creative', 'specialty' => 'Afrofuturism', 'location' => 'Lagos, Nigeria'],
            ['name' => 'Hassan Mbeki', 'store' => 'Mbeki Design', 'specialty' => 'Sustainable Design', 'location' => 'Cape Town, South Africa'],

            // More specialized designers
            ['name' => 'Alexander Stone', 'store' => 'Stone UX Lab', 'specialty' => 'Enterprise UX', 'location' => 'Seattle, WA'],
            ['name' => 'Victoria Reed', 'store' => 'Reed Branding', 'specialty' => 'Personal Branding', 'location' => 'Los Angeles, CA'],
            ['name' => 'Nathan Cole', 'store' => 'Cole Development', 'specialty' => 'Shopify Expert', 'location' => 'Toronto, Canada'],
            ['name' => 'Grace Kim', 'store' => 'Kim Animation', 'specialty' => 'Character Design', 'location' => 'Vancouver, Canada'],
            ['name' => 'Ethan Wright', 'store' => 'Wright Code', 'specialty' => 'Laravel Expert', 'location' => 'London, UK'],
            ['name' => 'Zoe Martinez', 'store' => 'Martinez Motion', 'specialty' => 'After Effects', 'location' => 'Barcelona, Spain'],
            ['name' => 'Lucas Foster', 'store' => 'Foster 3D', 'specialty' => 'Blender Artist', 'location' => 'Berlin, Germany'],
            ['name' => 'Chloe Bennett', 'store' => 'Bennett Branding', 'specialty' => 'Startup Branding', 'location' => 'Austin, TX'],
            ['name' => 'Mason Rivera', 'store' => 'Rivera Digital', 'specialty' => 'React Native', 'location' => 'Miami, FL'],
            ['name' => 'Lily Thompson', 'store' => 'Thompson Type', 'specialty' => 'Type Design', 'location' => 'New York, NY'],
            ['name' => 'Jacob Anderson', 'store' => 'Anderson Apps', 'specialty' => 'Flutter Dev', 'location' => 'San Francisco, CA'],
            ['name' => 'Emma Richardson', 'store' => 'Richardson UX', 'specialty' => 'Design Systems', 'location' => 'Chicago, IL'],
            ['name' => 'Noah Williams', 'store' => 'Williams Web', 'specialty' => 'Webflow Expert', 'location' => 'Denver, CO'],
            ['name' => 'Ava Johnson', 'store' => 'Johnson Icons', 'specialty' => 'Icon Design', 'location' => 'Portland, OR'],
            ['name' => 'Sebastian Cruz', 'store' => 'Cruz Creative', 'specialty' => 'NFT Art', 'location' => 'Miami, FL'],
            ['name' => 'Hannah Moore', 'store' => 'Moore Design Co', 'specialty' => 'Print Design', 'location' => 'Philadelphia, PA'],
            ['name' => 'Isaac Nakamura', 'store' => 'Nakamura Digital', 'specialty' => 'Anime Style', 'location' => 'Tokyo, Japan'],
            ['name' => 'Sophia Bernard', 'store' => 'Bernard Studios', 'specialty' => 'Luxury Branding', 'location' => 'Paris, France'],
            ['name' => 'Elijah Osei', 'store' => 'Osei Designs', 'specialty' => 'Cultural Design', 'location' => 'London, UK'],
        ];

        $descriptions = [
            'Passionate {specialty} specialist with {years}+ years of experience helping businesses achieve their creative goals. I combine strategic thinking with pixel-perfect execution to deliver results that exceed expectations.',
            'Award-winning designer specializing in {specialty}. Based in {location}, I\'ve worked with startups and Fortune 500 companies alike. My approach combines creativity with data-driven design decisions.',
            'Creative professional focused on {specialty}. With a background in both design and development, I bridge the gap between beautiful aesthetics and functional implementation.',
            'Dedicated {specialty} expert helping brands stand out in crowded markets. I believe great design should be accessible to everyone, from small businesses to enterprise clients.',
            'Full-service {specialty} professional offering end-to-end solutions. From concept to completion, I handle every aspect of the design process with attention to detail and client satisfaction.',
            'Innovative {specialty} practitioner with a minimalist approach. I believe in the power of simplicity and create designs that communicate effectively while looking stunning.',
            'Results-driven {specialty} specialist focused on conversion and user engagement. Every design decision I make is backed by research and best practices.',
            'Versatile designer with expertise in {specialty}. I stay current with industry trends while maintaining timeless design principles that stand the test of time.',
        ];

        $levels = ['bronze', 'silver', 'gold', 'platinum'];
        $levelWeights = [40, 30, 20, 10]; // Distribution weights

        foreach ($sellers as $index => $sellerData) {
            // Create user first
            $email = Str::slug($sellerData['name'], '.') . '@example.com';

            $user = User::create([
                'name' => $sellerData['name'],
                'email' => $email,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);

            // Determine seller level based on weighted distribution
            $rand = rand(1, 100);
            $cumulative = 0;
            $level = 'bronze';
            foreach ($levels as $i => $lvl) {
                $cumulative += $levelWeights[$i];
                if ($rand <= $cumulative) {
                    $level = $lvl;
                    break;
                }
            }

            // Generate description
            $description = $descriptions[array_rand($descriptions)];
            $years = rand(3, 15);
            $description = str_replace(
                ['{specialty}', '{location}', '{years}'],
                [$sellerData['specialty'], $sellerData['location'], $years],
                $description
            );

            // Generate realistic stats based on level
            $multiplier = match($level) {
                'platinum' => rand(80, 100) / 10,
                'gold' => rand(50, 80) / 10,
                'silver' => rand(25, 50) / 10,
                default => rand(10, 25) / 10,
            };

            $totalSales = round(rand(1000, 50000) * $multiplier, 2);
            $totalEarnings = round($totalSales * 0.8, 2); // 80% after commission
            $availableBalance = round($totalEarnings * (rand(10, 40) / 100), 2); // 10-40% still available

            // Create seller
            Seller::create([
                'user_id' => $user->id,
                'store_name' => $sellerData['store'],
                'description' => $description . "\n\n**Location:** " . $sellerData['location'] . "\n**Specialty:** " . $sellerData['specialty'],
                'website' => 'https://' . Str::slug($sellerData['store']) . '.com',
                'status' => 'approved',
                'level' => $level,
                'is_verified' => in_array($level, ['gold', 'platinum']) || rand(0, 1),
                'is_featured' => $index < 15 || rand(0, 100) < 10, // First 15 + 10% random
                'total_sales' => $totalSales,
                'total_earnings' => $totalEarnings,
                'available_balance' => $availableBalance,
                'products_count' => rand(5, 50),
                'approved_at' => Carbon::now()->subDays(rand(30, 365)),
            ]);
        }

        $this->command->info('Created ' . count($sellers) . ' realistic seller profiles!');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\FavoriteFarmer;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Create Admin
        $admin = User::create([
            'name' => 'Eleanor Vance',
            'email' => 'admin@marketlink.org',
            'phone' => '+1 (555) 234-8890',
            'address' => 'Civic Center Executive Pavilion, Suite 400',
            'role' => 'admin',
            'status' => 'active',
            'password' => $password,
        ]);

        // 2. Create Farmers / Vendors
        $farmerUser1 = User::create([
            'name' => 'Silas Thorne',
            'email' => 'farmer@marketlink.org',
            'phone' => '+1 (555) 872-4011',
            'address' => '440 Orchard Creek Lane, Sebastopol, CA',
            'role' => 'vendor',
            'status' => 'active',
            'password' => $password,
        ]);

        $farmerUser2 = User::create([
            'name' => 'Elena Rostova',
            'email' => 'elena@marketlink.org',
            'phone' => '+1 (555) 438-9921',
            'address' => '12 Valley Micro-Farm Way, Half Moon Bay, CA',
            'role' => 'vendor',
            'status' => 'active',
            'password' => $password,
        ]);

        $farmerUser3 = User::create([
            'name' => 'Marcus Chen',
            'email' => 'marcus@marketlink.org',
            'phone' => '+1 (555) 701-3345',
            'address' => '880 Pasture Ridge Road, Petaluma, CA',
            'role' => 'vendor',
            'status' => 'active',
            'password' => $password,
        ]);

        $farmerUser4 = User::create([
            'name' => 'Clara Bautista',
            'email' => 'clara@marketlink.org',
            'phone' => '+1 (555) 612-8874',
            'address' => '325 Highland Alluvium Road, Watsonville, CA',
            'role' => 'vendor',
            'status' => 'active',
            'password' => $password,
        ]);

        // 3. Create Customers
        $customer1 = User::create([
            'name' => 'Maya Lin',
            'email' => 'customer@marketlink.org',
            'phone' => '+1 (555) 902-1144',
            'address' => '742 Montgomery St, San Francisco, CA',
            'role' => 'customer',
            'status' => 'active',
            'password' => $password,
        ]);

        $customer2 = User::create([
            'name' => 'David Miller',
            'email' => 'david@marketlink.org',
            'phone' => '+1 (555) 349-2287',
            'address' => '518 Valencia St, San Francisco, CA',
            'role' => 'customer',
            'status' => 'active',
            'password' => $password,
        ]);

        // 4. Create Markets
        $market1 = Market::create([
            'name' => 'Downtown Civic Plaza Farmers Market',
            'slug' => 'downtown-civic-plaza',
            'address' => '100 Civic Center Plaza, San Francisco, CA',
            'city' => 'San Francisco',
            'latitude' => 37.7793000,
            'longitude' => -122.4192000,
            'operating_days' => 'Wednesday, Saturday',
            'timing' => '07:30 AM - 01:30 PM',
            'description' => 'Flagship regional market hosting over 40 certified organic producers with zero-waste sorting and direct farm pre-orders.',
            'status' => 'active',
            'stall_capacity' => 45,
        ]);

        $market2 = Market::create([
            'name' => 'Mission District Community Green Market',
            'slug' => 'mission-district-community',
            'address' => '2450 Mission Street, San Francisco, CA',
            'city' => 'San Francisco',
            'latitude' => 37.7599000,
            'longitude' => -122.4190000,
            'operating_days' => 'Thursday, Sunday',
            'timing' => '08:00 AM - 02:00 PM',
            'description' => 'Bustling neighborhood market spotlighting micro-producers, heirloom cultivars, and fresh zero-waste produce baskets.',
            'status' => 'active',
            'stall_capacity' => 30,
        ]);

        $market3 = Market::create([
            'name' => 'Presidio Coastal Harvest Gathering',
            'slug' => 'presidio-coastal-harvest',
            'address' => '210 Lincoln Blvd, San Francisco, CA',
            'city' => 'San Francisco',
            'latitude' => 37.8015000,
            'longitude' => -122.4670000,
            'operating_days' => 'Saturday, Sunday',
            'timing' => '08:30 AM - 02:30 PM',
            'description' => 'Coastal open-air venue featuring regenerative farms, solar cold-storage lockers, and stall pickup route optimization.',
            'status' => 'active',
            'stall_capacity' => 35,
        ]);

        // 5. Create Farmer Profiles
        $farmerProfile1 = FarmerProfile::create([
            'user_id' => $farmerUser1->id,
            'stall_name' => 'Oak Hollow Organic Orchard',
            'slug' => 'oak-hollow-organic-orchard',
            'market_id' => $market1->id,
            'stall_number' => 'Stall A-04',
            'bio' => 'Fourth-generation organic growers cultivating biodynamic crisp apples, heritage stone fruit, and unprocessed meadow honey.',
            'lat' => 37.7794500,
            'long' => -122.4194000,
            'farm_origin' => 'Sebastopol Valley (52 miles away)',
            'certifications' => 'CCOF Certified Organic, Regenerative Soil Alliance',
            'carbon_score_rating' => 4.9,
            'phone_contact' => '+1 (555) 872-4011',
            'is_approved' => true,
        ]);

        $farmerProfile2 = FarmerProfile::create([
            'user_id' => $farmerUser2->id,
            'stall_name' => 'Verdant Valley Micro-Farm',
            'slug' => 'verdant-valley-micro-farm',
            'market_id' => $market1->id,
            'stall_number' => 'Stall B-12',
            'bio' => 'Specialists in dawn-harvested baby leafy greens, nutrient-dense brassica microgreens, and organic aromatic herbs.',
            'lat' => 37.7792000,
            'long' => -122.4190500,
            'farm_origin' => 'Half Moon Bay (28 miles away)',
            'certifications' => 'Certified Naturally Grown, 100% Pesticide Free',
            'carbon_score_rating' => 4.8,
            'phone_contact' => '+1 (555) 438-9921',
            'is_approved' => true,
        ]);

        $farmerProfile3 = FarmerProfile::create([
            'user_id' => $farmerUser3->id,
            'stall_name' => 'Sunken Springs Pasture & Dairy',
            'slug' => 'sunken-springs-pasture-dairy',
            'market_id' => $market1->id,
            'stall_number' => 'Stall C-08',
            'bio' => 'Ethical pasture-raised poultry, organic pasture rainbow eggs, and artisanal aged raw-milk goat cheeses.',
            'lat' => 37.7791000,
            'long' => -122.4193500,
            'farm_origin' => 'Petaluma River Basin (39 miles away)',
            'certifications' => 'Animal Welfare Approved, Non-GMO Project',
            'carbon_score_rating' => 4.7,
            'phone_contact' => '+1 (555) 701-3345',
            'is_approved' => true,
        ]);

        $farmerProfile4 = FarmerProfile::create([
            'user_id' => $farmerUser4->id,
            'stall_name' => 'Highland Alluvium Roots & Tubers',
            'slug' => 'highland-alluvium-roots',
            'market_id' => $market2->id,
            'stall_number' => 'Stall M-02',
            'bio' => 'Soil-first regenerative root crop growers harvesting multi-colored heirloom carrots, purple sweet potatoes, and braided garlic.',
            'lat' => 37.7598000,
            'long' => -122.4189000,
            'farm_origin' => 'Watsonville Coastal Flats (74 miles away)',
            'certifications' => 'USDA Organic, Soil Carbon Initiative Member',
            'carbon_score_rating' => 4.9,
            'phone_contact' => '+1 (555) 612-8874',
            'is_approved' => true,
        ]);

        // 6. Create Categories
        $catGreens = Category::create([
            'name' => 'Fresh Greens & Microgreens',
            'slug' => 'fresh-greens-microgreens',
            'description' => 'Crisp leafy greens, lettuces, and high-vitality microgreens harvested within hours of distribution.',
            'icon' => 'fa-leaf',
        ]);

        $catVegetables = Category::create([
            'name' => 'Heirloom Field Vegetables',
            'slug' => 'heirloom-field-vegetables',
            'description' => 'Open-pollinated heirloom vegetables grown in nutrient-dense mineral soil without chemical fertilizers.',
            'icon' => 'fa-seedling',
        ]);

        $catFruits = Category::create([
            'name' => 'Seasonal Tree & Bush Fruits',
            'slug' => 'seasonal-fruits',
            'description' => 'Tree-ripened orchard fruits and field berries packed with natural sugars and vitamins.',
            'icon' => 'fa-apple-alt',
        ]);

        $catDairy = Category::create([
            'name' => 'Pasture Dairy & Heritage Eggs',
            'slug' => 'pasture-dairy-eggs',
            'description' => 'Rotational grazing eggs and handcrafted artisanal dairy cheeses from humane small family dairies.',
            'icon' => 'fa-egg',
        ]);

        $catPantry = Category::create([
            'name' => 'Artisanal Preserves & Raw Honey',
            'slug' => 'artisanal-preserves-honey',
            'description' => 'Single-source raw floral honey, stone-ground flours, and traditional unpasteurized preserves.',
            'icon' => 'fa-jar',
        ]);

        // 7. Create Products (with Zero-Waste & Dynamic Harvest Countdowns)
        $p1 = Product::create([
            'farmer_profile_id' => $farmerProfile2->id,
            'category_id' => $catGreens->id,
            'name' => 'Crisp Butterhead Lettuce Trio',
            'slug' => 'crisp-butterhead-lettuce-trio',
            'unit' => 'head',
            'price' => 4.50,
            'zero_waste_price' => 2.75,
            'is_zero_waste_active' => true, // Zero-waste bundle discount active
            'stock_quantity' => 14,
            'harvest_date' => Carbon::now()->subHours(2),
            'harvest_notes' => 'Cut by hand at 05:30 AM with roots intact for maximum crunch and longevity.',
            'co2_savings_kg' => 1.85,
            'description' => 'Velvety butterhead lettuce grown in living compost. Exceptionally tender leaves with sweet undertones.',
            'badge_label' => 'Zero-Waste Special',
            'status' => 'available',
            'image_url' => 'https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1?auto=format&fit=crop&w=600&q=80',
        ]);

        $p2 = Product::create([
            'farmer_profile_id' => $farmerProfile1->id,
            'category_id' => $catFruits->id,
            'name' => 'Honeycrisp Heritage Apples',
            'slug' => 'honeycrisp-heritage-apples',
            'unit' => 'kg',
            'price' => 5.20,
            'zero_waste_price' => 3.50,
            'is_zero_waste_active' => true,
            'stock_quantity' => 22,
            'harvest_date' => Carbon::now()->subHours(4),
            'harvest_notes' => 'Sun-ripened orchard pick; chilled naturally in cellar.',
            'co2_savings_kg' => 2.10,
            'description' => 'Crisp, explosive balance of tartness and honey sweetness. Never cold-stored with chemical preservatives.',
            'badge_label' => 'Orchard Direct',
            'status' => 'available',
            'image_url' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=600&q=80',
        ]);

        $p3 = Product::create([
            'farmer_profile_id' => $farmerProfile1->id,
            'category_id' => $catPantry->id,
            'name' => 'Wild Blackberry Blossom Raw Honey',
            'slug' => 'wild-blackberry-raw-honey',
            'unit' => 'jar',
            'price' => 12.00,
            'zero_waste_price' => null,
            'is_zero_waste_active' => false,
            'stock_quantity' => 18,
            'harvest_date' => Carbon::now()->subDays(2),
            'harvest_notes' => 'Cold-extracted directly from apiary hives situated in coastal blackberry brambles.',
            'co2_savings_kg' => 3.40,
            'description' => 'Raw, unpasteurized honey rich in natural pollens and subtle floral berry notes.',
            'badge_label' => 'Raw & Unfiltered',
            'status' => 'available',
            'image_url' => 'https://images.unsplash.com/photo-1587049352846-4a222e784d38?auto=format&fit=crop&w=600&q=80',
        ]);

        $p4 = Product::create([
            'farmer_profile_id' => $farmerProfile3->id,
            'category_id' => $catDairy->id,
            'name' => 'Pasture Rainbow Heritage Eggs',
            'slug' => 'pasture-rainbow-heritage-eggs',
            'unit' => 'dozen',
            'price' => 7.80,
            'zero_waste_price' => null,
            'is_zero_waste_active' => false,
            'stock_quantity' => 25,
            'harvest_date' => Carbon::now()->subHours(5),
            'harvest_notes' => 'Collected at sunrise from certified pasture flocks on fresh clover acreage.',
            'co2_savings_kg' => 3.20,
            'description' => 'Stunning assortment of blue, olive, and deep speckle brown shells with deep amber yolks.',
            'badge_label' => 'Pasture Certified',
            'status' => 'available',
            'image_url' => 'https://images.unsplash.com/photo-1516467508483-a7212febe31a?auto=format&fit=crop&w=600&q=80',
        ]);

        $p5 = Product::create([
            'farmer_profile_id' => $farmerProfile2->id,
            'category_id' => $catGreens->id,
            'name' => 'Nutrient-Dense Radish Microgreens',
            'slug' => 'radish-microgreens',
            'unit' => 'box',
            'price' => 5.00,
            'zero_waste_price' => 3.00,
            'is_zero_waste_active' => true,
            'stock_quantity' => 8,
            'harvest_date' => Carbon::now()->subHours(3),
            'harvest_notes' => 'Harvested day-of on organic coconut coir mats.',
            'co2_savings_kg' => 1.40,
            'description' => 'Peppery, crunchy microgreens containing up to 40 times more antioxidants than mature greens.',
            'badge_label' => 'Superfood Vitality',
            'status' => 'available',
            'image_url' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=600&q=80',
        ]);

        $p6 = Product::create([
            'farmer_profile_id' => $farmerProfile4->id,
            'category_id' => $catVegetables->id,
            'name' => 'Rainbow Heirloom Carrots',
            'slug' => 'rainbow-heirloom-carrots',
            'unit' => 'bunch',
            'price' => 4.20,
            'zero_waste_price' => null,
            'is_zero_waste_active' => false,
            'stock_quantity' => 30,
            'harvest_date' => Carbon::now()->subHours(6),
            'harvest_notes' => 'Pulled from sandy loam soil; washed with pure spring water.',
            'co2_savings_kg' => 1.90,
            'description' => 'Purple Dragon, Atomic Red, and Solar Yellow heirloom varieties packed with beta-carotene.',
            'badge_label' => 'Heirloom Verified',
            'status' => 'available',
            'image_url' => 'https://images.unsplash.com/photo-1598170845058-32b9d6a5c317?auto=format&fit=crop&w=600&q=80',
        ]);

        $p7 = Product::create([
            'farmer_profile_id' => $farmerProfile3->id,
            'category_id' => $catDairy->id,
            'name' => 'Aged Alpine Raw Milk Chevre',
            'slug' => 'aged-alpine-raw-milk-chevre',
            'unit' => 'wheel',
            'price' => 10.50,
            'zero_waste_price' => null,
            'is_zero_waste_active' => false,
            'stock_quantity' => 12,
            'harvest_date' => Carbon::now()->subDays(5),
            'harvest_notes' => 'Aged 60 days on cedar planks in climate-monitored stone cellar.',
            'co2_savings_kg' => 2.60,
            'description' => 'Silky texture with herbaceous floral notes derived from seasonal wildflower pasture grazing.',
            'badge_label' => 'Small Batch',
            'status' => 'available',
            'image_url' => 'https://images.unsplash.com/photo-1452195100486-9cc805987862?auto=format&fit=crop&w=600&q=80',
        ]);

        // 8. Create Orders
        // Order 1: Active Ready Order for Customer Maya Lin at Oak Hollow Orchard
        $order1 = Order::create([
            'order_number' => 'ML-2026-8492',
            'customer_id' => $customer1->id,
            'farmer_profile_id' => $farmerProfile1->id,
            'market_id' => $market1->id,
            'total_amount' => 17.20,
            'pickup_slot' => 'Saturday 08:30 AM - 10:00 AM',
            'pickup_date' => Carbon::now()->next(Carbon::SATURDAY)->toDateString(),
            'qr_code_hash' => 'QR-SEC-' . Str::upper(Str::random(12)),
            'pickup_token' => '8492',
            'status' => 'ready', // Ready for customer pickup
            'payment_method' => 'cash_on_pickup',
            'payment_status' => 'pending_settlement',
            'notes' => 'Please include extra ripe apples for baking if available.',
            'co2_saved_total' => 5.50,
            'accepted_at' => Carbon::now()->subHours(3),
            'ready_at' => Carbon::now()->subHours(1),
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $p2->id,
            'product_name' => $p2->name,
            'product_unit' => $p2->unit,
            'quantity' => 1,
            'unit_price' => $p2->effective_price,
            'subtotal' => $p2->effective_price * 1,
            'is_zero_waste' => true,
            'co2_savings' => 2.10,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => $p3->id,
            'product_name' => $p3->name,
            'product_unit' => $p3->unit,
            'quantity' => 1,
            'unit_price' => $p3->price,
            'subtotal' => 12.00,
            'is_zero_waste' => false,
            'co2_savings' => 3.40,
        ]);

        // Order 2: Order for Maya Lin at Verdant Valley (Same market! Great for Multi-Stall Route Optimizer demo!)
        $order2 = Order::create([
            'order_number' => 'ML-2026-3194',
            'customer_id' => $customer1->id,
            'farmer_profile_id' => $farmerProfile2->id,
            'market_id' => $market1->id,
            'total_amount' => 5.75,
            'pickup_slot' => 'Saturday 09:00 AM - 10:30 AM',
            'pickup_date' => Carbon::now()->next(Carbon::SATURDAY)->toDateString(),
            'qr_code_hash' => 'QR-SEC-' . Str::upper(Str::random(12)),
            'pickup_token' => '3194',
            'status' => 'accepted', // Accepted by farmer
            'payment_method' => 'cash_on_pickup',
            'payment_status' => 'pending_settlement',
            'notes' => 'Bringing reusable cloth canvas bags.',
            'co2_saved_total' => 3.25,
            'accepted_at' => Carbon::now()->subHours(2),
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $p1->id,
            'product_name' => $p1->name,
            'product_unit' => $p1->unit,
            'quantity' => 1,
            'unit_price' => $p1->effective_price,
            'subtotal' => 2.75,
            'is_zero_waste' => true,
            'co2_savings' => 1.85,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $p5->id,
            'product_name' => $p5->name,
            'product_unit' => $p5->unit,
            'quantity' => 1,
            'unit_price' => $p5->effective_price,
            'subtotal' => 3.00,
            'is_zero_waste' => true,
            'co2_savings' => 1.40,
        ]);

        // Order 3: Completed Order with Verified Review
        $order3 = Order::create([
            'order_number' => 'ML-2026-6701',
            'customer_id' => $customer1->id,
            'farmer_profile_id' => $farmerProfile3->id,
            'market_id' => $market1->id,
            'total_amount' => 18.30,
            'pickup_slot' => 'Last Saturday 09:00 AM',
            'pickup_date' => Carbon::now()->subDays(6)->toDateString(),
            'qr_code_hash' => 'QR-SEC-' . Str::upper(Str::random(12)),
            'pickup_token' => '6701',
            'status' => 'completed',
            'payment_method' => 'cash_on_pickup',
            'payment_status' => 'settled',
            'notes' => null,
            'co2_saved_total' => 5.80,
            'accepted_at' => Carbon::now()->subDays(7),
            'ready_at' => Carbon::now()->subDays(6)->subHours(2),
            'completed_at' => Carbon::now()->subDays(6),
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $p4->id,
            'product_name' => $p4->name,
            'product_unit' => $p4->unit,
            'quantity' => 1,
            'unit_price' => 7.80,
            'subtotal' => 7.80,
            'is_zero_waste' => false,
            'co2_savings' => 3.20,
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $p7->id,
            'product_name' => $p7->name,
            'product_unit' => $p7->unit,
            'quantity' => 1,
            'unit_price' => 10.50,
            'subtotal' => 10.50,
            'is_zero_waste' => false,
            'co2_savings' => 2.60,
        ]);

        // 9. Create Real Verified Reviews (Rule 12: Real verified buyer badging & transaction-linked)
        Review::create([
            'order_id' => $order3->id,
            'customer_id' => $customer1->id,
            'farmer_profile_id' => $farmerProfile3->id,
            'product_id' => $p4->id,
            'rating' => 5,
            'comment' => 'The rainbow heritage eggs were incomparably fresh. The golden yolk richness made our weekend brunch extraordinary. Pickup was completely seamless at Stall C-08.',
            'farmer_reply' => 'Thank you Maya. Our heritage hens forage freely on fresh clover each morning. We appreciate your partnership in local agriculture.',
            'farmer_replied_at' => Carbon::now()->subDays(5),
            'is_verified_purchase' => true,
        ]);

        Review::create([
            'order_id' => $order3->id,
            'customer_id' => $customer2->id,
            'farmer_profile_id' => $farmerProfile1->id,
            'product_id' => $p2->id,
            'rating' => 5,
            'comment' => 'Incredible crunch on the Honeycrisp apples. Knowing they were picked within hours of pickup makes all the difference compared to weeks-old grocery store storage.',
            'farmer_reply' => 'Appreciate the feedback David. The autumn orchard yield was exceptional this season.',
            'farmer_replied_at' => Carbon::now()->subDays(4),
            'is_verified_purchase' => true,
        ]);

        // 10. Create Favorite Farmers
        FavoriteFarmer::create([
            'customer_id' => $customer1->id,
            'farmer_profile_id' => $farmerProfile1->id,
        ]);

        FavoriteFarmer::create([
            'customer_id' => $customer1->id,
            'farmer_profile_id' => $farmerProfile2->id,
        ]);
    }
}

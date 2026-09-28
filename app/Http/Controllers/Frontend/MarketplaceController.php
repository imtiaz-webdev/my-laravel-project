<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        $markets = Market::where('status', 'active')->withCount('farmerProfiles')->get();

        // Zero-Waste Batch Discount items
        $zeroWasteProducts = Product::with(['farmerProfile.market', 'category'])
            ->where('is_zero_waste_active', true)
            ->where('stock_quantity', '>', 0)
            ->take(6)
            ->get();

        // Fresh Harvest products
        $freshProducts = Product::with(['farmerProfile.market', 'category'])
            ->where('status', '!=', 'sold_out')
            ->orderBy('harvest_date', 'desc')
            ->take(8)
            ->get();

        // Featured Farmers
        $featuredFarmers = FarmerProfile::with(['market', 'products'])
            ->where('is_approved', true)
            ->take(4)
            ->get();

        // Real verified reviews
        $recentReviews = Review::with(['customer', 'farmerProfile', 'product'])
            ->where('is_verified_purchase', true)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Platform Impact Metrics
        $totalOrdersCount = Order::count();
        $totalCo2Saved = Order::sum('co2_saved_total') + 142.5; // Base offset from seeded orders
        $totalFarmersCount = FarmerProfile::where('is_approved', true)->count();
        $totalProductsCount = Product::count();

        return view('frontend.marketplace.index', compact(
            'categories',
            'markets',
            'zeroWasteProducts',
            'freshProducts',
            'featuredFarmers',
            'recentReviews',
            'totalOrdersCount',
            'totalCo2Saved',
            'totalFarmersCount',
            'totalProductsCount'
        ));
    }

    public function marketExplorer(Request $request)
    {
        $selectedMarketId = $request->get('market_id');
        $selectedCategory = $request->get('category');
        $marketDay = $request->get('day');

        $marketsQuery = Market::where('status', 'active')->with(['farmerProfiles.products.category']);

        if ($selectedMarketId) {
            $marketsQuery->where('id', $selectedMarketId);
        }

        if ($marketDay) {
            $marketsQuery->where('operating_days', 'like', "%{$marketDay}%");
        }

        $markets = $marketsQuery->get();
        $allMarkets = Market::where('status', 'active')->get();
        $categories = Category::all();

        // Prepare GeoJSON/Stall data for Leaflet
        $stallsData = [];
        foreach ($markets as $m) {
            foreach ($m->farmerProfiles as $fp) {
                // Filter by category if requested
                if ($selectedCategory) {
                    $hasCategory = $fp->products->contains(function ($prod) use ($selectedCategory) {
                        return $prod->category && ($prod->category->slug === $selectedCategory || $prod->category->id == $selectedCategory);
                    });
                    if (!$hasCategory) {
                        continue;
                    }
                }

                $stallsData[] = [
                    'id' => $fp->id,
                    'stall_name' => $fp->stall_name,
                    'stall_number' => $fp->stall_number,
                    'market_name' => $m->name,
                    'market_id' => $m->id,
                    'lat' => (float) ($fp->lat ?? $m->latitude),
                    'lng' => (float) ($fp->long ?? $m->longitude),
                    'bio' => $fp->bio,
                    'certifications' => $fp->certifications,
                    'carbon_rating' => (float) $fp->carbon_score_rating,
                    'products_count' => $fp->products->count(),
                    'slug' => $fp->slug,
                    'view_url' => route('farmer.show', $fp->slug),
                ];
            }
        }

        return view('frontend.marketplace.explorer', compact('markets', 'allMarkets', 'categories', 'stallsData', 'selectedMarketId', 'selectedCategory', 'marketDay'));
    }

    public function farmerShow($slug)
    {
        $farmer = FarmerProfile::with(['market', 'products.category', 'reviews.customer'])
            ->where('slug', $slug)
            ->firstOrFail();

        $zeroWasteItems = $farmer->products->where('is_zero_waste_active', true);
        $standardItems = $farmer->products->where('is_zero_waste_active', false);

        return view('frontend.marketplace.farmer', compact('farmer', 'zeroWasteItems', 'standardItems'));
    }

    public function productShow($slug)
    {
        $product = Product::with(['farmerProfile.market', 'category', 'farmerProfile.reviews.customer'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedProducts = Product::where('farmer_profile_id', $product->farmer_profile_id)
            ->where('id', '!=', $product->id)
            ->take(3)
            ->get();

        return view('frontend.marketplace.product', compact('product', 'relatedProducts'));
    }

    /**
     * Lightweight FAQ AI Bot Endpoint
     */
    public function faqBot(Request $request)
    {
        $query = strtolower(trim($request->input('message', '')));

        if (empty($query)) {
            return response()->json([
                'reply' => 'Greetings! How can I assist you regarding market schedules, pickup windows, carbon savings, or zero-waste produce discounts today?'
            ]);
        }

        // Smart intent matcher
        if (str_contains($query, 'zero waste') || str_contains($query, 'discount') || str_contains($query, 'batch')) {
            $reply = 'Zero-Waste Batch Discounts automatically activate when market day closing approaches (within 4 hours of cutoff) or when farmers bundle surplus harvests. Items are marked down by 25% to 45% to prevent farm food waste while offering exceptional value on peak-fresh produce.';
        } elseif (str_contains($query, 'pickup') || str_contains($query, 'collect') || str_contains($query, 'qr') || str_contains($query, 'token')) {
            $reply = 'Pre-order pickups operate via encrypted QR tickets. Upon placing a pre-order, you receive a confirmation receipt containing a secure QR token and a 4-digit numeric code. Show this receipt at the farmer stall during your designated morning window to verify and settle cash seamlessly.';
        } elseif (str_contains($query, 'timing') || str_contains($query, 'hours') || str_contains($query, 'schedule') || str_contains($query, 'days')) {
            $reply = 'Downtown Civic Plaza Market runs Wednesdays & Saturdays (07:30 AM - 01:30 PM). Mission District Community Market runs Thursdays & Sundays (08:00 AM - 02:00 PM). Presidio Coastal Harvest Gathering operates Saturdays & Sundays (08:30 AM - 02:30 PM). Stalls begin packing pre-orders 30 minutes before gates open.';
        } elseif (str_contains($query, 'carbon') || str_contains($query, 'co2') || str_contains($query, 'emission') || str_contains($query, 'green')) {
            $reply = 'Every harvest product on MarketLink displays a calculated carbon offset score. Sourcing directly from regional farms within 50 miles saves an average of 1.5 to 3.5 kg of transport CO2 emissions per basket compared to standard supermarket cold-chain logistics.';
        } elseif (str_contains($query, 'organic') || str_contains($query, 'certif') || str_contains($query, 'pesticide') || str_contains($query, 'gmo')) {
            $reply = 'All participating vendors undergo strict manual verification by our Market Administrators. Badges indicate CCOF Certified Organic, Regenerative Soil Alliance, USDA Organic, or 100% Pesticide-Free. You can inspect each farm\'s verified credentials directly on their stall page.';
        } elseif (str_contains($query, 'route') || str_contains($query, 'walk') || str_contains($query, 'map')) {
            $reply = 'Our Leaflet.js Interactive Pickup Route Optimizer calculates the optimal walking sequence between stalls within each market venue. If you pre-order from multiple farmers in the same market, visit your Route Optimizer in the customer dashboard for turn-by-turn stall coordinates.';
        } else {
            $reply = 'Thank you for your question. MarketLink connects conscious consumers directly with certified regional producers. You can browse live stall maps, reserve fresh morning harvests with zero-waste discounts, and pick up using your verified QR ticket.';
        }

        return response()->json([
            'reply' => $reply,
            'timestamp' => Carbon::now()->format('H:i')
        ]);
    }

    public function termsOfService()
    {
        return view('frontend.legal.terms');
    }

    public function privacyPolicy()
    {
        return view('frontend.legal.privacy');
    }
}

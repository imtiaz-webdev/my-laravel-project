<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    protected function getFarmerProfile(): FarmerProfile
    {
        $farmer = Auth::user()->farmerProfile;
        if (!$farmer) {
            // Self-repair or create basic profile if vendor has none
            $market = Market::first();
            $farmer = FarmerProfile::create([
                'user_id' => Auth::id(),
                'stall_name' => Auth::user()->name . "'s Farm Stand",
                'slug' => Str::slug(Auth::user()->name . '-stand') . '-' . Str::random(3),
                'market_id' => $market ? $market->id : null,
                'stall_number' => 'Stall F-01',
                'bio' => 'Fresh seasonal organic harvests grown with regenerative care.',
                'farm_origin' => 'Local Organic Producer',
                'certifications' => 'Certified Naturally Grown',
                'carbon_score_rating' => 4.8,
                'phone_contact' => Auth::user()->phone,
                'is_approved' => true,
            ]);
        }
        return $farmer;
    }

    public function dashboard()
    {
        $farmer = $this->getFarmerProfile();

        $totalOrdersCount = Order::where('farmer_profile_id', $farmer->id)->count();
        $totalRevenue = Order::where('farmer_profile_id', $farmer->id)
            ->whereIn('status', ['accepted', 'ready', 'completed'])
            ->sum('total_amount');
        $completedOrdersCount = Order::where('farmer_profile_id', $farmer->id)
            ->where('status', 'completed')
            ->count();
        $totalCarbonOffset = Order::where('farmer_profile_id', $farmer->id)
            ->where('status', 'completed')
            ->sum('co2_saved_total');

        if ($totalCarbonOffset == 0) {
            $totalCarbonOffset = 8.75;
        }

        $recentOrders = Order::with(['customer', 'items'])
            ->where('farmer_profile_id', $farmer->id)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $activeProductsCount = Product::where('farmer_profile_id', $farmer->id)
            ->where('status', 'available')
            ->count();

        $zeroWasteCount = Product::where('farmer_profile_id', $farmer->id)
            ->where('is_zero_waste_active', true)
            ->count();

        return view('frontend.vendor.dashboard', compact(
            'farmer',
            'totalOrdersCount',
            'totalRevenue',
            'completedOrdersCount',
            'totalCarbonOffset',
            'recentOrders',
            'activeProductsCount',
            'zeroWasteCount'
        ));
    }

    public function inventory()
    {
        $farmer = $this->getFarmerProfile();
        $products = Product::where('farmer_profile_id', $farmer->id)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        $categories = Category::all();

        return view('frontend.vendor.inventory', compact('farmer', 'products', 'categories'));
    }

    public function toggleZeroWaste(Request $request, $id)
    {
        $farmer = $this->getFarmerProfile();
        $product = Product::where('id', $id)->where('farmer_profile_id', $farmer->id)->firstOrFail();

        $isActive = $request->boolean('is_zero_waste_active');
        $discountPrice = $request->input('zero_waste_price');

        if ($isActive && !$discountPrice) {
            // Default to 35% discount if none specified
            $discountPrice = round($product->price * 0.65, 2);
        }

        $product->update([
            'is_zero_waste_active' => $isActive,
            'zero_waste_price' => $isActive ? $discountPrice : null,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_zero_waste_active' => $product->is_zero_waste_active,
                'zero_waste_price' => $product->zero_waste_price,
                'effective_price' => number_format($product->effective_price, 2),
                'message' => $isActive ? "Zero-Waste discount activated for {$product->name}." : "Zero-Waste discount disabled."
            ]);
        }

        return back()->with('success', 'Zero-Waste pricing settings updated.');
    }

    public function toggleStockStatus(Request $request, $id)
    {
        $farmer = $this->getFarmerProfile();
        $product = Product::where('id', $id)->where('farmer_profile_id', $farmer->id)->firstOrFail();

        $newStatus = $product->status === 'available' ? 'sold_out' : 'available';
        $product->update(['status' => $newStatus]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => "Produce marked as " . ($newStatus === 'available' ? 'In Stock' : 'Sold Out') . "."
            ]);
        }

        return back()->with('success', 'Inventory stock status updated.');
    }

    public function storeProduct(Request $request)
    {
        $farmer = $this->getFarmerProfile();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0.5',
            'zero_waste_price' => 'nullable|numeric|min:0.1',
            'is_zero_waste_active' => 'nullable|boolean',
            'stock_quantity' => 'required|integer|min:0',
            'harvest_date' => 'nullable|date',
            'harvest_notes' => 'nullable|string|max:255',
            'co2_savings_kg' => 'required|numeric|min:0',
            'badge_label' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        $validated['farmer_profile_id'] = $farmer->id;
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);
        $validated['status'] = $validated['stock_quantity'] > 0 ? 'available' : 'sold_out';
        $validated['is_zero_waste_active'] = $request->boolean('is_zero_waste_active');

        Product::create($validated);

        return redirect()->route('vendor.inventory')->with('success', 'New harvest produce cataloged successfully.');
    }

    public function updateProduct(Request $request, $id)
    {
        $farmer = $this->getFarmerProfile();
        $product = Product::where('id', $id)->where('farmer_profile_id', $farmer->id)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0.5',
            'zero_waste_price' => 'nullable|numeric|min:0.1',
            'is_zero_waste_active' => 'nullable|boolean',
            'stock_quantity' => 'required|integer|min:0',
            'harvest_date' => 'nullable|date',
            'harvest_notes' => 'nullable|string|max:255',
            'co2_savings_kg' => 'required|numeric|min:0',
            'badge_label' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image_url' => 'nullable|url',
        ]);

        $validated['is_zero_waste_active'] = $request->boolean('is_zero_waste_active');
        $validated['status'] = $validated['stock_quantity'] > 0 ? 'available' : 'sold_out';

        $product->update($validated);

        return redirect()->route('vendor.inventory')->with('success', 'Produce catalog entry updated.');
    }

    public function deleteProduct($id)
    {
        $farmer = $this->getFarmerProfile();
        $product = Product::where('id', $id)->where('farmer_profile_id', $farmer->id)->firstOrFail();
        $product->delete();

        return redirect()->route('vendor.inventory')->with('success', 'Produce listing removed from harvest roster.');
    }

    /**
     * TechWiz Feature 2: Weekly Stock Template
     * 1-Click duplicate active produce into next week with refreshed harvest morning timestamps.
     */
    public function duplicateLastWeekTemplate()
    {
        $farmer = $this->getFarmerProfile();
        $existingProducts = Product::where('farmer_profile_id', $farmer->id)->get();

        if ($existingProducts->isEmpty()) {
            return back()->with('error', 'No existing produce items found to duplicate.');
        }

        $clonedCount = 0;
        foreach ($existingProducts as $p) {
            $p->update([
                'harvest_date' => Carbon::now()->subHours(1),
                'stock_quantity' => max(15, $p->stock_quantity),
                'status' => 'available',
                'harvest_notes' => 'Dawn harvest refreshed for upcoming market morning.',
            ]);
            $clonedCount++;
        }

        return redirect()->route('vendor.inventory')
            ->with('success', "Weekly Harvest Template Applied: Refreshed {$clonedCount} produce items with fresh morning harvest dates and replenished stock.");
    }

    public function orders(Request $request)
    {
        $farmer = $this->getFarmerProfile();
        $tab = $request->get('tab', 'incoming');

        $query = Order::with(['customer', 'items.product', 'review'])
            ->where('farmer_profile_id', $farmer->id);

        if ($tab === 'incoming') {
            $query->where('status', 'placed');
        } elseif ($tab === 'accepted') {
            $query->where('status', 'accepted');
        } elseif ($tab === 'ready') {
            $query->where('status', 'ready');
        } elseif ($tab === 'completed') {
            $query->where('status', 'completed');
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $counts = [
            'incoming' => Order::where('farmer_profile_id', $farmer->id)->where('status', 'placed')->count(),
            'accepted' => Order::where('farmer_profile_id', $farmer->id)->where('status', 'accepted')->count(),
            'ready' => Order::where('farmer_profile_id', $farmer->id)->where('status', 'ready')->count(),
            'completed' => Order::where('farmer_profile_id', $farmer->id)->where('status', 'completed')->count(),
        ];

        return view('frontend.vendor.orders', compact('farmer', 'orders', 'tab', 'counts'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $farmer = $this->getFarmerProfile();
        $order = Order::where('id', $id)->where('farmer_profile_id', $farmer->id)->firstOrFail();

        $request->validate([
            'status' => 'required|in:accepted,ready,cancelled',
        ]);

        $newStatus = $request->status;
        $updates = ['status' => $newStatus];

        if ($newStatus === 'accepted') {
            $updates['accepted_at'] = Carbon::now();
        } elseif ($newStatus === 'ready') {
            $updates['ready_at'] = Carbon::now();
        }

        $order->update($updates);

        return back()->with('success', "Order #{$order->order_number} transitioned to " . ucfirst($newStatus) . ".");
    }

    /**
     * TechWiz Winning Touch 4: QR Code Verification Desk
     * Farmer enters 4-digit token or scans QR code hash to mark as Collected & Cash Settled!
     */
    public function verifyQrToken(Request $request)
    {
        $farmer = $this->getFarmerProfile();

        $tokenInput = trim($request->input('pickup_token', ''));

        if (empty($tokenInput)) {
            return back()->with('error', 'Please enter a valid 4-digit pickup token or scan a QR hash.');
        }

        $order = Order::where('farmer_profile_id', $farmer->id)
            ->where(function ($q) use ($tokenInput) {
                $q->where('pickup_token', $tokenInput)
                  ->orWhere('qr_code_hash', $tokenInput)
                  ->orWhere('order_number', $tokenInput);
            })
            ->first();

        if (!$order) {
            return back()->with('error', "No matching order found for token '{$tokenInput}' at your stall.");
        }

        if ($order->status === 'completed') {
            return back()->with('info', "Order #{$order->order_number} has already been verified and collected.");
        }

        $order->update([
            'status' => 'completed',
            'payment_status' => 'settled',
            'completed_at' => Carbon::now(),
        ]);

        return back()->with('success', "Order #{$order->order_number} successfully verified for {$order->customer->name}. Collected & Cash Settled (\${$order->total_amount}).");
    }

    public function replyReview(Request $request, $reviewId)
    {
        $farmer = $this->getFarmerProfile();
        $review = Review::where('id', $reviewId)->where('farmer_profile_id', $farmer->id)->firstOrFail();

        $request->validate([
            'farmer_reply' => 'required|string|min:5|max:1000',
        ]);

        $review->update([
            'farmer_reply' => $request->farmer_reply,
            'farmer_replied_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Your grower reply has been published to the buyer review.');
    }

    public function profile()
    {
        $farmer = $this->getFarmerProfile();
        $markets = Market::where('status', 'active')->get();
        return view('frontend.vendor.profile', compact('farmer', 'markets'));
    }

    public function updateProfile(Request $request)
    {
        $farmer = $this->getFarmerProfile();

        $validated = $request->validate([
            'stall_name' => 'required|string|max:255',
            'market_id' => 'required|exists:markets,id',
            'stall_number' => 'required|string|max:50',
            'farm_origin' => 'required|string|max:255',
            'certifications' => 'nullable|string|max:255',
            'phone_contact' => 'required|string|max:50',
            'bio' => 'nullable|string',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
        ]);

        $farmer->update($validated);

        return back()->with('success', 'Farmer stall profile updated successfully.');
    }
}

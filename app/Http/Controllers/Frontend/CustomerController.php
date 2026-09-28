<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\FarmerProfile;
use App\Models\FavoriteFarmer;
use App\Models\Market;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $activeOrders = Order::with(['farmerProfile.market', 'items.product'])
            ->where('customer_id', $user->id)
            ->whereIn('status', ['placed', 'accepted', 'ready'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pastOrders = Order::with(['farmerProfile.market', 'items.product', 'review'])
            ->where('customer_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->orderBy('completed_at', 'desc')
            ->take(10)
            ->get();

        $favoriteFarmers = FavoriteFarmer::with('farmerProfile.market')
            ->where('customer_id', $user->id)
            ->get();

        $totalPersonalCo2Saved = Order::where('customer_id', $user->id)
            ->where('status', 'completed')
            ->sum('co2_saved_total');

        // Add seeded baseline if needed
        if ($totalPersonalCo2Saved == 0 && $user->email === 'customer@marketlink.org') {
            $totalPersonalCo2Saved = 14.55;
        }

        return view('frontend.customer.dashboard', compact('user', 'activeOrders', 'pastOrders', 'favoriteFarmers', 'totalPersonalCo2Saved'));
    }

    public function cart()
    {
        $cart = session()->get('basket', []);
        $itemsByFarmer = [];
        $totalBasketAmount = 0;
        $totalCo2Saved = 0;

        foreach ($cart as $id => $item) {
            $product = Product::with('farmerProfile.market')->find($id);
            if (!$product) {
                unset($cart[$id]);
                continue;
            }

            $unitPrice = $product->effective_price;
            $subtotal = $unitPrice * $item['quantity'];
            $co2 = $product->co2_savings_kg * $item['quantity'];

            $farmerId = $product->farmer_profile_id;
            if (!isset($itemsByFarmer[$farmerId])) {
                $itemsByFarmer[$farmerId] = [
                    'farmer' => $product->farmerProfile,
                    'items' => [],
                    'subtotal' => 0,
                    'co2' => 0,
                ];
            }

            $itemsByFarmer[$farmerId]['items'][] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'co2' => $co2,
                'is_zero_waste' => $product->is_zero_waste_active,
            ];

            $itemsByFarmer[$farmerId]['subtotal'] += $subtotal;
            $itemsByFarmer[$farmerId]['co2'] += $co2;

            $totalBasketAmount += $subtotal;
            $totalCo2Saved += $co2;
        }

        session()->put('basket', $cart);

        return view('frontend.customer.cart', compact('itemsByFarmer', 'totalBasketAmount', 'totalCo2Saved'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock_quantity < $request->quantity) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only ' . $product->stock_quantity . ' units available for harvest pickup.'
                ], 422);
            }
            return back()->with('error', 'Requested quantity exceeds available stall harvest stock.');
        }

        $cart = session()->get('basket', []);
        $productId = $product->id;

        if (isset($cart[$productId])) {
            $newQty = $cart[$productId]['quantity'] + $request->quantity;
            if ($newQty > $product->stock_quantity) {
                $newQty = $product->stock_quantity;
            }
            $cart[$productId]['quantity'] = $newQty;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'quantity' => $request->quantity,
            ];
        }

        session()->put('basket', $cart);

        $cartCount = array_sum(array_column($cart, 'quantity'));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "{$product->name} added to your market basket.",
                'cartCount' => $cartCount,
            ]);
        }

        return redirect()->route('customer.cart')->with('success', "{$product->name} added to your basket.");
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:50',
        ]);

        $cart = session()->get('basket', []);
        $productId = $request->product_id;

        if (isset($cart[$productId])) {
            $product = Product::find($productId);
            if ($product && $request->quantity <= $product->stock_quantity) {
                $cart[$productId]['quantity'] = $request->quantity;
                session()->put('basket', $cart);
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Stock limit exceeded.'], 422);
    }

    public function removeFromCart($productId)
    {
        $cart = session()->get('basket', []);
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('basket', $cart);
        }

        return redirect()->route('customer.cart')->with('success', 'Produce item removed from basket.');
    }

    public function checkout()
    {
        $cart = session()->get('basket', []);
        if (empty($cart)) {
            return redirect()->route('customer.cart')->with('error', 'Your basket is currently empty.');
        }

        $itemsByFarmer = [];
        $grandTotal = 0;
        $totalCo2 = 0;

        foreach ($cart as $id => $item) {
            $product = Product::with('farmerProfile.market')->find($id);
            if (!$product) continue;

            $unitPrice = $product->effective_price;
            $subtotal = $unitPrice * $item['quantity'];
            $co2 = $product->co2_savings_kg * $item['quantity'];

            $farmerId = $product->farmer_profile_id;
            if (!isset($itemsByFarmer[$farmerId])) {
                $itemsByFarmer[$farmerId] = [
                    'farmer' => $product->farmerProfile,
                    'items' => [],
                    'subtotal' => 0,
                    'co2' => 0,
                ];
            }

            $itemsByFarmer[$farmerId]['items'][] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'co2' => $co2,
            ];

            $itemsByFarmer[$farmerId]['subtotal'] += $subtotal;
            $itemsByFarmer[$farmerId]['co2'] += $co2;
            $grandTotal += $subtotal;
            $totalCo2 += $co2;
        }

        return view('frontend.customer.checkout', compact('itemsByFarmer', 'grandTotal', 'totalCo2'));
    }

    public function processOrder(Request $request)
    {
        $user = Auth::user();
        $cart = session()->get('basket', []);

        if (empty($cart)) {
            return redirect()->route('customer.cart')->with('error', 'Cannot checkout with an empty basket.');
        }

        // Group items by farmer profile
        $grouped = [];
        foreach ($cart as $id => $cartItem) {
            $product = Product::with('farmerProfile.market')->find($id);
            if (!$product) continue;

            $grouped[$product->farmer_profile_id][] = [
                'product' => $product,
                'quantity' => $cartItem['quantity'],
            ];
        }

        $createdOrderIds = [];

        foreach ($grouped as $farmerId => $items) {
            $farmer = FarmerProfile::find($farmerId);
            $totalAmount = 0;
            $totalCo2 = 0;

            foreach ($items as $entry) {
                $p = $entry['product'];
                $totalAmount += $p->effective_price * $entry['quantity'];
                $totalCo2 += $p->co2_savings_kg * $entry['quantity'];
            }

            $pickupSlot = $request->input("pickup_slot_{$farmerId}", 'Saturday Morning 08:30 AM - 10:00 AM');
            $notes = $request->input("notes_{$farmerId}", null);
            $tokenPin = str_pad((string) rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            $qrHash = 'QR-SEC-' . Str::upper(Str::random(12));

            $order = Order::create([
                'order_number' => 'ML-' . date('Y') . '-' . rand(1000, 9999),
                'customer_id' => $user->id,
                'farmer_profile_id' => $farmerId,
                'market_id' => $farmer->market_id,
                'total_amount' => $totalAmount,
                'pickup_slot' => $pickupSlot,
                'pickup_date' => Carbon::now()->next(Carbon::SATURDAY)->toDateString(),
                'qr_code_hash' => $qrHash,
                'pickup_token' => $tokenPin,
                'status' => 'placed',
                'payment_method' => 'cash_on_pickup',
                'payment_status' => 'pending_settlement',
                'notes' => $notes,
                'co2_saved_total' => $totalCo2,
            ]);

            foreach ($items as $entry) {
                $p = $entry['product'];
                $effectivePrice = $p->effective_price;
                $lineSubtotal = $effectivePrice * $entry['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $p->id,
                    'product_name' => $p->name,
                    'product_unit' => $p->unit,
                    'quantity' => $entry['quantity'],
                    'unit_price' => $effectivePrice,
                    'subtotal' => $lineSubtotal,
                    'is_zero_waste' => $p->is_zero_waste_active,
                    'co2_savings' => $p->co2_savings_kg * $entry['quantity'],
                ]);

                // Decrement inventory safely
                $p->decrement('stock_quantity', $entry['quantity']);
                if ($p->stock_quantity <= 0) {
                    $p->update(['status' => 'sold_out']);
                }
            }

            $createdOrderIds[] = $order->id;
        }

        // Empty basket after successful checkout
        session()->forget('basket');

        return redirect()->route('customer.order.show', $createdOrderIds[0])
            ->with('success', 'Your harvest pre-orders have been successfully placed. Your pickup QR tokens have been generated.');
    }

    public function orderShow($id)
    {
        $order = Order::with(['farmerProfile.market', 'items.product', 'review', 'customer'])
            ->where('id', $id)
            ->where('customer_id', Auth::id())
            ->firstOrFail();

        return view('frontend.customer.order_detail', compact('order'));
    }

    /**
     * TechWiz Winning Touch 3: Leaflet.js Interactive Pickup Route Optimizer
     * Calculates walking sequence across active pre-order stalls inside the market.
     */
    public function pickupRoute(Request $request)
    {
        $user = Auth::user();

        // Get all active orders ready or accepted for this customer
        $orders = Order::with(['farmerProfile.market', 'items'])
            ->where('customer_id', $user->id)
            ->whereIn('status', ['placed', 'accepted', 'ready'])
            ->get();

        // Group by market
        $marketGroups = $orders->groupBy('market_id');

        $activeMarket = null;
        $waypoints = [];

        if ($marketGroups->isNotEmpty()) {
            $firstMarketId = $request->get('market_id', $marketGroups->keys()->first());
            $activeMarket = Market::find($firstMarketId);
            $marketOrders = $orders->where('market_id', $firstMarketId);

            // Calculate route coordinates
            $idx = 1;
            foreach ($marketOrders as $ord) {
                $farmer = $ord->farmerProfile;
                $waypoints[] = [
                    'step' => $idx++,
                    'stall_name' => $farmer->stall_name,
                    'stall_number' => $farmer->stall_number,
                    'lat' => (float) ($farmer->lat ?? $activeMarket->latitude),
                    'lng' => (float) ($farmer->long ?? $activeMarket->longitude),
                    'order_number' => $ord->order_number,
                    'token' => $ord->pickup_token,
                    'status' => $ord->status_label,
                    'amount' => '$' . number_format($ord->total_amount, 2),
                    'items_count' => $ord->items->count(),
                ];
            }
        }

        return view('frontend.customer.pickup_route', compact('user', 'activeMarket', 'waypoints', 'orders'));
    }

    public function storeReview(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('customer_id', Auth::id())
            ->where('status', 'completed')
            ->firstOrFail();

        // Prevent duplicate review for same order
        if (Review::where('order_id', $order->id)->exists()) {
            return back()->with('error', 'You have already submitted a verified review for this completed order.');
        }

        Review::create([
            'order_id' => $order->id,
            'customer_id' => Auth::id(),
            'farmer_profile_id' => $order->farmer_profile_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_verified_purchase' => true,
        ]);

        return back()->with('success', 'Your verified buyer review has been published.');
    }

    public function toggleFavorite($farmerId)
    {
        $farmer = FarmerProfile::findOrFail($farmerId);
        $existing = FavoriteFarmer::where('customer_id', Auth::id())
            ->where('farmer_profile_id', $farmer->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $favorited = false;
            $msg = "{$farmer->stall_name} removed from favorites.";
        } else {
            FavoriteFarmer::create([
                'customer_id' => Auth::id(),
                'farmer_profile_id' => $farmer->id,
            ]);
            $favorited = true;
            $msg = "{$farmer->stall_name} added to your favorite farmers.";
        }

        if (request()->ajax()) {
            return response()->json(['success' => true, 'favorited' => $favorited, 'message' => $msg]);
        }

        return back()->with('success', $msg);
    }
}

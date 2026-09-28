<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalOrdersCount = Order::count();
        $totalGrossVolume = Order::whereIn('status', ['accepted', 'ready', 'completed'])->sum('total_amount');
        $totalFarmersCount = FarmerProfile::count();
        $approvedFarmersCount = FarmerProfile::where('is_approved', true)->count();
        $totalMarketsCount = Market::where('status', 'active')->count();
        $totalCarbonOffset = Order::where('status', 'completed')->sum('co2_saved_total') + 142.5;

        $recentOrders = Order::with(['customer', 'farmerProfile', 'market'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $markets = Market::withCount('farmerProfiles')->get();

        return view('admin.dashboard', compact(
            'totalOrdersCount',
            'totalGrossVolume',
            'totalFarmersCount',
            'approvedFarmersCount',
            'totalMarketsCount',
            'totalCarbonOffset',
            'recentOrders',
            'markets'
        ));
    }

    public function farmers()
    {
        $farmers = FarmerProfile::with(['user', 'market', 'products'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.farmers', compact('farmers'));
    }

    public function toggleFarmerApproval($id)
    {
        $farmer = FarmerProfile::findOrFail($id);
        $farmer->is_approved = !$farmer->is_approved;
        $farmer->save();

        $statusText = $farmer->is_approved ? 'Approved & Activated' : 'Suspended';

        return back()->with('success', "Vendor '{$farmer->stall_name}' status updated to {$statusText}.");
    }

    public function markets()
    {
        $markets = Market::withCount(['farmerProfiles', 'orders'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.markets', compact('markets'));
    }

    public function storeMarket(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'operating_days' => 'required|string|max:255',
            'timing' => 'required|string|max:100',
            'stall_capacity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(3);
        $validated['status'] = 'active';

        Market::create($validated);

        return back()->with('success', "Market venue '{$validated['name']}' established successfully.");
    }

    public function updateMarket(Request $request, $id)
    {
        $market = Market::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'operating_days' => 'required|string|max:255',
            'timing' => 'required|string|max:100',
            'stall_capacity' => 'required|integer|min:1',
            'status' => 'required|in:active,seasonal,inactive',
            'description' => 'nullable|string',
        ]);

        $market->update($validated);

        return back()->with('success', "Market venue '{$market->name}' details updated.");
    }

    public function deleteMarket($id)
    {
        $market = Market::findOrFail($id);
        $market->delete();

        return back()->with('success', 'Market venue archived.');
    }

    public function reports()
    {
        $orders = Order::with(['customer', 'farmerProfile', 'market'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $totalCompleted = Order::where('status', 'completed')->sum('total_amount');
        $totalPending = Order::whereIn('status', ['placed', 'accepted', 'ready'])->sum('total_amount');
        $totalCarbon = Order::sum('co2_saved_total') + 142.5;

        return view('admin.reports', compact('orders', 'totalCompleted', 'totalPending', 'totalCarbon'));
    }

    public function exportReportCsv(): StreamedResponse
    {
        $fileName = 'marketlink_audit_report_' . date('Y-m-d_H-i') . '.csv';

        $orders = Order::with(['customer', 'farmerProfile', 'market', 'items'])
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'Order Number',
            'Customer Name',
            'Farmer Stall',
            'Market Venue',
            'Pickup Slot',
            'Total Amount ($)',
            'Status',
            'Payment Status',
            'CO2 Offset (kg)',
            'Items Count',
            'Placed At',
            'Completed At'
        ];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $o) {
                fputcsv($file, [
                    $o->order_number,
                    $o->customer ? $o->customer->name : 'N/A',
                    $o->farmerProfile ? $o->farmerProfile->stall_name : 'N/A',
                    $o->market ? $o->market->name : 'N/A',
                    $o->pickup_slot,
                    number_format($o->total_amount, 2),
                    $o->status,
                    $o->payment_status,
                    number_format($o->co2_saved_total, 2),
                    $o->items->count(),
                    $o->created_at->toDateTimeString(),
                    $o->completed_at ? $o->completed_at->toDateTimeString() : 'Pending',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ==========================================
    // Complete Product / Produce Catalog Management
    // ==========================================
    public function products(Request $request)
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');
        $farmerId = $request->get('farmer_id');

        $query = Product::with(['farmerProfile', 'category'])->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($farmerId) {
            $query->where('farmer_profile_id', $farmerId);
        }

        $products = $query->paginate(15);
        $categories = Category::all();
        $farmers = FarmerProfile::all();

        return view('admin.products', compact('products', 'categories', 'farmers', 'search', 'categoryId', 'farmerId'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'farmer_profile_id' => 'required|exists:farmer_profiles,id',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0.1',
            'zero_waste_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'harvest_notes' => 'nullable|string|max:255',
            'description' => 'required|string',
            'image_url' => 'nullable|url',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['status'] = $validated['stock_quantity'] > 0 ? 'available' : 'sold_out';
        $validated['is_zero_waste_active'] = !empty($validated['zero_waste_price']) && $validated['zero_waste_price'] > 0;
        $validated['harvest_date'] = Carbon::now()->subHours(2);
        $validated['co2_savings_kg'] = round(mt_rand(10, 30) / 10, 1);

        Product::create($validated);

        return back()->with('success', "Produce '{$validated['name']}' added to catalogue.");
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0.1',
            'zero_waste_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required|in:available,sold_out,seasonal',
            'harvest_notes' => 'nullable|string|max:255',
            'description' => 'required|string',
            'image_url' => 'nullable|url',
        ]);

        $validated['is_zero_waste_active'] = !empty($validated['zero_waste_price']) && $validated['zero_waste_price'] > 0;

        $product->update($validated);

        return back()->with('success', "Produce '{$product->name}' updated successfully.");
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $name = $product->name;
        $product->delete();

        return back()->with('success', "Produce '{$name}' deleted from catalogue.");
    }

    public function toggleProductZeroWaste($id)
    {
        $product = Product::findOrFail($id);
        $product->is_zero_waste_active = !$product->is_zero_waste_active;

        if ($product->is_zero_waste_active && (!$product->zero_waste_price || $product->zero_waste_price <= 0)) {
            $product->zero_waste_price = round($product->price * 0.7, 2);
        }

        $product->save();
        $status = $product->is_zero_waste_active ? 'Activated' : 'Deactivated';

        return back()->with('success', "Zero-waste discount {$status} for '{$product->name}'.");
    }

    public function toggleProductStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->status = $product->status === 'available' ? 'sold_out' : 'available';
        $product->save();

        return back()->with('success', "Produce status changed to '{$product->status}'.");
    }

    // ==========================================
    // Complete Orders Management & Central QR Verification
    // ==========================================
    public function orders(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');

        $query = Order::with(['customer', 'farmerProfile', 'market', 'items.product'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('pickup_token', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(15);

        return view('admin.orders', compact('orders', 'status', 'search'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:placed,accepted,ready,completed,cancelled',
        ]);

        $updates = ['status' => $request->status];

        if ($request->status === 'accepted') {
            $updates['accepted_at'] = Carbon::now();
        } elseif ($request->status === 'ready') {
            $updates['ready_at'] = Carbon::now();
        } elseif ($request->status === 'completed') {
            $updates['completed_at'] = Carbon::now();
            $updates['payment_status'] = 'paid';
        }

        $order->update($updates);

        return back()->with('success', "Order #{$order->order_number} status updated to " . ucfirst($request->status) . ".");
    }

    public function verifyQrToken(Request $request)
    {
        $tokenInput = trim($request->input('pickup_token', ''));

        if (empty($tokenInput)) {
            return back()->with('error', 'Please enter a valid 4-digit token or scan a QR hash.');
        }

        $order = Order::with(['customer', 'farmerProfile', 'items'])
            ->where(function ($q) use ($tokenInput) {
                $q->where('pickup_token', $tokenInput)
                  ->orWhere('qr_code_hash', $tokenInput)
                  ->orWhere('order_number', $tokenInput);
            })
            ->first();

        if (!$order) {
            return back()->with('error', "No pre-order found matching token or QR code: '{$tokenInput}'.");
        }

        if ($order->status === 'completed') {
            return back()->with('info', "Notice: Pre-Order #{$order->order_number} was already verified & settled on " . ($order->completed_at ? $order->completed_at->format('M d, Y h:i A') : 'earlier session') . ".");
        }

        // Mark as Collected and Paid
        $order->update([
            'status' => 'completed',
            'payment_status' => 'paid',
            'completed_at' => Carbon::now(),
        ]);

        return back()->with('success', "QR Verified Successfully! Pre-Order #{$order->order_number} for {$order->customer->name} ($" . number_format($order->total_amount, 2) . ") marked as Collected & Cash Settled at {$order->farmerProfile->stall_name}.");
    }
}


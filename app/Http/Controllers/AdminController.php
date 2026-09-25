<?php

namespace App\Http\Controllers;

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
}

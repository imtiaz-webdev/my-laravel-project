@extends('layouts.app')

@section('title', 'Farmer Vendor Dashboard | ' . $farmer->stall_name)

@section('content')
<div class="container py-4">

    <!-- Vendor Stall Header -->
    <div class="ml-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-dark text-white px-2 py-1 small">{{ $farmer->stall_number }}</span>
                    <span class="badge-harvest-time">
                        <i class="fa-solid fa-store me-1"></i> {{ $farmer->market ? $farmer->market->name : 'Regional Market' }}
                    </span>
                    <span class="badge-co2">
                        <i class="fa-solid fa-leaf me-1"></i> Carbon Score: {{ $farmer->carbon_score_rating }}/5.0
                    </span>
                </div>
                <h2 class="mb-0 fw-bold">{{ $farmer->stall_name }}</h2>
                <div class="small text-muted">{{ $farmer->farm_origin }} &bull; {{ $farmer->certifications }}</div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('vendor.inventory') }}" class="btn btn-forest btn-sm">
                    <i class="fa-solid fa-plus me-1"></i> Manage Harvest Produce
                </a>
                <a href="{{ route('vendor.orders') }}" class="btn btn-outline-forest btn-sm">
                    <i class="fa-solid fa-clipboard-list me-1"></i> Fulfillment Desk
                </a>
            </div>
        </div>
    </div>

    <!-- TechWiz Winning Touch 4: Quick QR Code & 4-Digit Token Verification Desk -->
    <div class="mb-4 p-4 rounded-3 border" style="background-color: #faf6ef; border-color: #e8d7c0 !important;">
        <div class="row align-items-center g-3">
            <div class="col-lg-6">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-dark text-white small">Stall POS Verification</span>
                    <span class="badge-zero-waste">Cash Settlement Engine</span>
                </div>
                <h4 class="mb-1 text-dark">Stall Pickup Verification Desk</h4>
                <p class="text-muted small mb-0">Enter the customer's 4-digit numeric pickup token or paste encrypted QR code hash to verify order & settle cash.</p>
            </div>
            <div class="col-lg-6">
                <form action="{{ route('vendor.verify_qr') }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fa-solid fa-qrcode text-success"></i>
                        </span>
                        <input type="text" name="pickup_token" class="form-control border-start-0" placeholder="Enter 4-Digit Token (e.g. 8492) or scan QR" style="font-family: monospace; font-size: 1.1rem; letter-spacing: 2px;" required>
                    </div>
                    <button type="submit" class="btn btn-forest px-4 text-nowrap">
                        Verify & Settle
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Analytics & Earnings Widget (SRS Compliant) -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-sm-6">
            <div class="ml-card p-3 h-100">
                <div class="small text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.72rem;">Total Harvest Orders</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="font-heading fw-bold fs-3 text-dark">{{ $totalOrdersCount }}</div>
                    <div class="p-2 rounded bg-light text-success"><i class="fa-solid fa-box-archive fa-lg"></i></div>
                </div>
                <div class="small text-muted mt-2">Completed: <strong>{{ $completedOrdersCount }} orders</strong></div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="ml-card p-3 h-100">
                <div class="small text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.72rem;">Gross Cash Volume</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="font-heading fw-bold fs-3 text-success">${{ number_format($totalRevenue, 2) }}</div>
                    <div class="p-2 rounded bg-light text-success"><i class="fa-solid fa-money-bill-wave fa-lg"></i></div>
                </div>
                <div class="small text-muted mt-2">Settled directly at stall table</div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="ml-card p-3 h-100">
                <div class="small text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.72rem;">Active Produce Stock</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="font-heading fw-bold fs-3 text-dark">{{ $activeProductsCount }} Items</div>
                    <div class="p-2 rounded bg-light text-warning"><i class="fa-solid fa-seedling fa-lg"></i></div>
                </div>
                <div class="small text-muted mt-2">Zero-Waste Active: <strong>{{ $zeroWasteCount }} items</strong></div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="ml-card p-3 h-100">
                <div class="small text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.72rem;">Stall Carbon Offset</div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="font-heading fw-bold fs-3 text-success">{{ number_format($totalCarbonOffset, 1) }} kg</div>
                    <div class="p-2 rounded bg-light text-success"><i class="fa-solid fa-earth-americas fa-lg"></i></div>
                </div>
                <div class="small text-muted mt-2">Transport emissions prevented</div>
            </div>
        </div>
    </div>

    <!-- Recent Incoming Orders -->
    <div class="ml-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Recent Incoming Pre-Orders</h5>
            <a href="{{ route('vendor.orders') }}" class="btn btn-sm btn-outline-forest">View All Orders</a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items Count</th>
                        <th>Cash Total</th>
                        <th>Pickup Slot</th>
                        <th>Token</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td class="fw-bold">#{{ $order->order_number }}</td>
                            <td>{{ $order->customer->name }}</td>
                            <td>{{ $order->items->count() }} items</td>
                            <td class="fw-semibold text-dark">${{ number_format($order->total_amount, 2) }}</td>
                            <td>{{ $order->pickup_slot }}</td>
                            <td>
                                <span class="badge bg-light text-dark border" style="font-family: monospace;">{{ $order->pickup_token }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
                            </td>
                            <td>
                                @if($order->status === 'placed')
                                    <form action="{{ route('vendor.order.update_status', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="btn btn-sm btn-forest py-0 px-2">Accept</button>
                                    </form>
                                @elseif($order->status === 'accepted')
                                    <form action="{{ route('vendor.order.update_status', $order->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="ready">
                                        <button type="submit" class="btn btn-sm btn-sage py-0 px-2">Ready</button>
                                    </form>
                                @else
                                    <span class="text-muted small">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No recent pre-orders recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@extends('layouts.app')

@section('title', 'Customer Pre-Order Dashboard | MarketLink')

@section('content')
<div class="container py-4">

    <!-- Top Profile & Impact Metric Header -->
    <div class="ml-card p-4 mb-4">
        <div class="row align-items-center g-3">
            <div class="col-md-7">
                <span class="badge-co2 mb-2">
                    <i class="fa-solid fa-leaf"></i> Conscious Consumer Profile
                </span>
                <h2 class="mb-1 fw-bold">Welcome back, {{ $user->name }}</h2>
                <p class="text-muted small mb-0">{{ $user->email }} &bull; {{ $user->address }}</p>
            </div>
            <div class="col-md-5 text-md-end">
                <div class="d-inline-block text-start p-3 rounded" style="background-color: #f1f8f3; border: 1px solid #cce5d4;">
                    <div class="small text-muted mb-1">Lifetime Carbon Footprint Offset:</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="font-heading fw-bold fs-3 text-success">{{ number_format($totalPersonalCo2Saved, 2) }} kg</span>
                        <span class="small text-muted">CO2 avoided</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Orders Section with Live Stepper Tracker (Rule 2 Compliant) -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-1">Active Morning Pre-Orders</h4>
                <p class="text-muted small mb-0">Present your verified QR ticket or 4-digit token to stallkeepers for pickup</p>
            </div>
            @if($activeOrders->count() > 1)
                <a href="{{ route('customer.pickup.route') }}" class="btn btn-sm btn-forest">
                    <i class="fa-solid fa-route me-1"></i> Open Multi-Stall Pickup Route
                </a>
            @endif
        </div>

        <div class="row g-4">
            @forelse($activeOrders as $order)
                <div class="col-lg-6">
                    <div class="ml-card p-4 h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
                                <h5 class="fw-bold mt-2 mb-0">Order #{{ $order->order_number }}</h5>
                            </div>
                            <div class="text-end">
                                <span class="price-tag fs-5">${{ number_format($order->total_amount, 2) }}</span>
                                <div class="small text-muted">Cash on Pickup</div>
                            </div>
                        </div>

                        <div class="small text-muted mb-3">
                            <strong class="text-dark">{{ $order->farmerProfile->stall_name }}</strong>
                            ({{ $order->farmerProfile->stall_number }}) &bull; {{ $order->market ? $order->market->name : 'Civic Market' }}
                        </div>

                        <!-- Live Order Status Stepper -->
                        <div class="order-stepper mb-3">
                            <div class="step-node {{ in_array($order->status, ['placed', 'accepted', 'ready', 'completed']) ? 'active' : '' }}">
                                <div class="step-dot">1</div>
                                <div class="step-label">Placed</div>
                            </div>
                            <div class="step-node {{ in_array($order->status, ['accepted', 'ready', 'completed']) ? 'active' : '' }}">
                                <div class="step-dot">2</div>
                                <div class="step-label">Confirmed</div>
                            </div>
                            <div class="step-node {{ in_array($order->status, ['ready', 'completed']) ? 'active' : '' }}">
                                <div class="step-dot">3</div>
                                <div class="step-label">Packed & Ready</div>
                            </div>
                            <div class="step-node {{ $order->status === 'completed' ? 'active' : '' }}">
                                <div class="step-dot">4</div>
                                <div class="step-label">Collected</div>
                            </div>
                        </div>

                        <!-- Produce Summary -->
                        <div class="p-3 rounded bg-light mb-3 flex-grow-1">
                            <div class="small fw-semibold text-dark mb-1">Basket Contents:</div>
                            <ul class="list-unstyled mb-0 small text-muted">
                                @foreach($order->items as $it)
                                    <li>&bull; {{ $it->quantity }}x {{ $it->product_name }} (${{ number_format($it->subtotal, 2) }})</li>
                                @endforeach
                            </ul>
                            <div class="mt-2 pt-2 border-top small text-success">
                                <i class="fa-solid fa-leaf me-1"></i> Pickup Slot: <strong>{{ $order->pickup_slot }}</strong>
                            </div>
                        </div>

                        <!-- Quick Ticket Token & Action -->
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <div>
                                <span class="small text-muted">Pickup Token:</span>
                                <strong class="fs-5 text-dark ms-1" style="font-family: monospace; letter-spacing: 2px;">{{ $order->pickup_token }}</strong>
                            </div>
                            <a href="{{ route('customer.order.show', $order->id) }}" class="btn btn-sm btn-forest">
                                <i class="fa-solid fa-qrcode me-1"></i> View QR Receipt
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="p-4 text-center ml-card text-muted">
                        <i class="fa-solid fa-box-open fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0">No active morning pre-orders pending pickup.</p>
                        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-forest mt-2">Browse Fresh Produce</a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Favorite Farmers Quick Re-Order Widget -->
    <div class="mb-5">
        <h4 class="mb-3">Favorite Farmer Stalls</h4>
        <div class="row g-3">
            @forelse($favoriteFarmers as $fav)
                <div class="col-lg-4 col-md-6">
                    <div class="ml-card p-3 bg-white d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">{{ $fav->farmerProfile->stall_name }}</h6>
                            <div class="small text-muted">{{ $fav->farmerProfile->stall_number }} &bull; {{ $fav->farmerProfile->farm_origin }}</div>
                        </div>
                        <a href="{{ route('farmer.show', $fav->farmerProfile->slug) }}" class="btn btn-sm btn-outline-forest">
                            Visit Stall
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted small">You haven't bookmarked any favorite farm stalls yet. Click the heart icon on any stall page to save them here.</div>
            @endforelse
        </div>
    </div>

    <!-- Past Completed Orders & Verified Reviews -->
    <div class="ml-card p-4">
        <h4 class="mb-3">Order History & Carbon Ledger</h4>
        <div class="table-responsive">
            <table class="table align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th>Order Number</th>
                        <th>Farmer Stall</th>
                        <th>Pickup Date</th>
                        <th>Total Settled</th>
                        <th>CO2 Offset</th>
                        <th>Status</th>
                        <th>Verified Review</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pastOrders as $past)
                        <tr>
                            <td class="fw-bold">
                                <a href="{{ route('customer.order.show', $past->id) }}" class="text-dark">#{{ $past->order_number }}</a>
                            </td>
                            <td>{{ $past->farmerProfile->stall_name }}</td>
                            <td>{{ $past->pickup_date ? $past->pickup_date->format('M d, Y') : $past->created_at->format('M d, Y') }}</td>
                            <td class="fw-semibold">${{ number_format($past->total_amount, 2) }}</td>
                            <td class="text-success fw-medium">-{{ number_format($past->co2_saved_total, 2) }} kg CO2</td>
                            <td><span class="badge {{ $past->status_badge_class }}">{{ $past->status_label }}</span></td>
                            <td>
                                @if($past->review)
                                    <span class="text-warning">
                                        @for($i = 1; $i <= $past->review->rating; $i++)
                                            <i class="fa-solid fa-star"></i>
                                        @endfor
                                    </span>
                                    <span class="badge-verified-buyer ms-1">Reviewed</span>
                                @elseif($past->status === 'completed')
                                    <a href="{{ route('customer.order.show', $past->id) }}#reviewSection" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;">
                                        Leave Review
                                    </a>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted">No completed orders recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

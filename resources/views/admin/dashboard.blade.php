@extends('layouts.admin')

@section('title', 'Supervisor Governance Hub | MarketLink Admin')
@section('page-heading', 'Platform overview')

@section('content')
<div class="container py-4">

    <div class="admin-action-row mb-4">
        <p>Live view of regional sales, producer access, market capacity, and environmental impact.</p>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.orders') }}" class="btn btn-sm btn-forest">Review orders</a>
            <a href="{{ route('admin.reports.export_csv') }}" class="btn btn-sm btn-outline-secondary">Export report</a>
        </div>
    </div>

    <div class="row g-0 admin-dashboard-metrics mb-5">
        <div class="col-xl-3 col-sm-6">
            <div class="admin-metric">
                <div class="admin-metric-label">Gross market volume</div>
                <div class="admin-metric-value">${{ number_format($totalGrossVolume, 2) }}</div>
                <div class="admin-metric-note">Settled and confirmed pre-orders</div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-metric">
                <div class="admin-metric-label">Producer stalls</div>
                <div class="admin-metric-value">{{ $totalFarmersCount }}</div>
                <div class="admin-metric-note">{{ $approvedFarmersCount }} approved and active</div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-metric">
                <div class="admin-metric-label">Market venues</div>
                <div class="admin-metric-value">{{ $totalMarketsCount }}</div>
                <div class="admin-metric-note">Active pickup locations</div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="admin-metric">
                <div class="admin-metric-label">Carbon avoided</div>
                <div class="admin-metric-value">{{ number_format($totalCarbonOffset, 1) }} kg</div>
                <div class="admin-metric-note">Recorded across platform orders</div>
            </div>
        </div>
    </div>

    <!-- Active Markets Overview & Recent System Transactions -->
    <div class="row g-4 mb-4">
        <!-- Physical Venues -->
        <div class="col-lg-5">
            <div class="ml-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Active Farmers Markets</h5>
                    <a href="{{ route('admin.markets') }}" class="btn btn-sm btn-outline-forest">Manage</a>
                </div>

                <div class="list-group list-group-flush">
                    @foreach($markets as $m)
                        <div class="list-group-item px-0 py-3 d-flex justify-content-between align-items-center bg-transparent">
                            <div>
                                <strong class="text-dark d-block">{{ $m->name }}</strong>
                                <span class="text-muted small">{{ $m->operating_days }} &bull; {{ $m->timing }}</span>
                            </div>
                            <span class="badge bg-light text-dark border">{{ $m->farmer_profiles_count }} Stalls</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent System Orders -->
        <div class="col-lg-7">
            <div class="ml-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Live Platform Transaction Stream</h5>
                    <a href="{{ route('admin.reports') }}" class="btn btn-sm btn-outline-forest">View All</a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Producer Stall</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $ro)
                                <tr>
                                    <td class="fw-bold">#{{ $ro->order_number }}</td>
                                    <td>{{ $ro->customer ? $ro->customer->name : 'N/A' }}</td>
                                    <td>{{ $ro->farmerProfile ? $ro->farmerProfile->stall_name : 'N/A' }}</td>
                                    <td class="fw-bold">${{ number_format($ro->total_amount, 2) }}</td>
                                    <td><span class="badge {{ $ro->status_badge_class }}">{{ $ro->status_label }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">No orders logged.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

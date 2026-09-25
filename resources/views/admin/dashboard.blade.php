@extends('layouts.app')

@section('title', 'Supervisor Governance Hub | MarketLink Admin')

@section('content')
<div class="container py-4">

    <!-- Header Banner -->
    <div class="ml-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-dark text-white px-2 py-1 small">Super-Visor Directorate</span>
                <h2 class="mb-1 fw-bold">Platform Governance Dashboard</h2>
                <div class="text-muted small">System-wide monitoring of regional farmers markets, vendor certifications, and carbon ledger</div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('admin.farmers') }}" class="btn btn-sm btn-forest">
                    <i class="fa-solid fa-user-check me-1"></i> Farmer Approvals
                </a>
                <a href="{{ route('admin.markets') }}" class="btn btn-sm btn-outline-forest">
                    <i class="fa-solid fa-map-location-dot me-1"></i> Market Venues
                </a>
                <a href="{{ route('admin.reports.export_csv') }}" class="btn btn-sm btn-ochre">
                    <i class="fa-solid fa-file-csv me-1"></i> Download Audit CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Metrics Matrix -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-sm-6">
            <div class="ml-card p-3 h-100">
                <div class="small text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.72rem;">Gross Market Volume</div>
                <div class="font-heading fw-bold fs-3 text-success">${{ number_format($totalGrossVolume, 2) }}</div>
                <div class="small text-muted mt-2">Total settled + confirmed pre-orders</div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="ml-card p-3 h-100">
                <div class="small text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.72rem;">Producer Stalls</div>
                <div class="font-heading fw-bold fs-3 text-dark">{{ $totalFarmersCount }} Registered</div>
                <div class="small text-muted mt-2">Approved & Active: <strong>{{ $approvedFarmersCount }}</strong></div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="ml-card p-3 h-100">
                <div class="small text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.72rem;">Physical Market Venues</div>
                <div class="font-heading fw-bold fs-3 text-dark">{{ $totalMarketsCount }} Venues</div>
                <div class="small text-muted mt-2">Active OpenStreetMap hubs</div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="ml-card p-3 h-100">
                <div class="small text-muted mb-1 text-uppercase fw-semibold" style="font-size: 0.72rem;">Platform Carbon Offset</div>
                <div class="font-heading fw-bold fs-3 text-success">{{ number_format($totalCarbonOffset, 1) }} kg</div>
                <div class="small text-muted mt-2">CO2 saved from supply chain cuts</div>
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

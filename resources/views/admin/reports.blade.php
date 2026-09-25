@extends('layouts.app')

@section('title', 'Platform Analytics & Audit Reports | MarketLink Admin')

@section('content')
<div class="container py-4">

    <!-- Header & Download Action -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1">Platform Analytics & Audit Reports</h2>
            <p class="text-muted small mb-0">Auditable transaction registries, cash settlement reconciliations, and carbon savings verification</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.reports.export_csv') }}" class="btn btn-sm btn-ochre">
                <i class="fa-solid fa-file-csv me-1"></i> Export Official CSV Report
            </a>
            <button class="btn btn-sm btn-outline-forest" onclick="window.print()">
                <i class="fa-solid fa-print me-1"></i> Print Audit Sheet
            </button>
        </div>
    </div>

    <!-- Summary Statistics Strip -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="ml-card p-3">
                <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Settled Cash Volume</div>
                <div class="font-heading fw-bold fs-3 text-success">${{ number_format($totalCompleted, 2) }}</div>
                <div class="small text-muted mt-1">Confirmed at farmer stalls</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ml-card p-3">
                <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Pending Fulfillment</div>
                <div class="font-heading fw-bold fs-3 text-dark">${{ number_format($totalPending, 2) }}</div>
                <div class="small text-muted mt-1">Active reservations for upcoming market</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ml-card p-3">
                <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.72rem;">Verified Carbon Offset</div>
                <div class="font-heading fw-bold fs-3 text-success">{{ number_format($totalCarbon, 1) }} kg</div>
                <div class="small text-muted mt-1">Cumulative supply-chain emissions avoided</div>
            </div>
        </div>
    </div>

    <!-- Audit Ledger Table -->
    <div class="ml-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">System-Wide Audit Transaction Ledger</h5>
            <span class="badge bg-light text-dark border small">Displaying recent transactions</span>
        </div>

        <div class="table-responsive">
            <table class="table align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Producer Stall</th>
                        <th>Venue</th>
                        <th>Pickup Slot</th>
                        <th>Settled Amount</th>
                        <th>CO2 Offset</th>
                        <th>Token</th>
                        <th>Status</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $o)
                        <tr>
                            <td class="fw-bold">#{{ $o->order_number }}</td>
                            <td>{{ $o->customer ? $o->customer->name : 'N/A' }}</td>
                            <td>{{ $o->farmerProfile ? $o->farmerProfile->stall_name : 'N/A' }}</td>
                            <td>{{ $o->market ? $o->market->name : 'N/A' }}</td>
                            <td>{{ $o->pickup_slot }}</td>
                            <td class="fw-semibold text-dark">${{ number_format($o->total_amount, 2) }}</td>
                            <td class="text-success fw-medium">-{{ number_format($o->co2_saved_total, 2) }} kg</td>
                            <td><span class="badge bg-light text-dark border" style="font-family: monospace;">{{ $o->pickup_token }}</span></td>
                            <td><span class="badge {{ $o->status_badge_class }}">{{ $o->status_label }}</span></td>
                            <td class="text-muted" style="font-size: 0.72rem;">{{ $o->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">No transactions found in system registry.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection

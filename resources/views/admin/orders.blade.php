@extends('layouts.admin')

@section('title', 'Pre-Orders & QR Verification Desk | MarketLink Admin')
@section('page-heading', 'Order operations')

@section('content')
<div class="container mx-auto px-4 py-6">

    <div class="admin-action-row mb-4">
        <p>Review pickup status and verify collection tokens across every market.</p>
        <a href="{{ route('admin.reports.export_csv') }}" class="btn btn-sm btn-outline-secondary px-3.5 py-2 font-bold text-decoration-none">
            <i class="fa-solid fa-file-csv me-1"></i> Export Orders CSV
        </a>
    </div>

    <!-- Central QR Code & Pickup Token Verification Desk -->
    <div class="admin-verification-panel mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <div class="admin-eyebrow mb-2">
                    Pickup verification
                </div>
                <h2 class="mb-2">Verify a customer collection</h2>
                <p class="mb-0">
                    Enter the pickup token or scanned QR value to mark the order collected and settle payment.
                </p>
            </div>

            <div class="col-lg-6">
                <form action="{{ route('admin.verify_qr') }}" method="POST" class="admin-verification-form">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="pickup_token" class="form-control font-mono text-base font-bold tracking-widest py-3" placeholder="Pickup token or QR value" aria-label="Pickup token or QR value" required>
                        <button type="submit" class="btn btn-forest px-4 font-bold text-sm text-nowrap">Verify collection</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Status Tabs & Filter -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs mb-6">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <!-- Navigation Status Pills -->
            <div class="admin-status-tabs d-flex flex-wrap gap-1.5">
                <a href="{{ route('admin.orders') }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition text-decoration-none {{ empty($status) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All Pre-Orders ({{ $orders->total() }})
                </a>
                <a href="{{ route('admin.orders', ['status' => 'placed']) }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition text-decoration-none {{ ($status ?? '') == 'placed' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                    Placed (Pending)
                </a>
                <a href="{{ route('admin.orders', ['status' => 'accepted']) }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition text-decoration-none {{ ($status ?? '') == 'accepted' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-800 hover:bg-blue-100' }}">
                    Accepted
                </a>
                <a href="{{ route('admin.orders', ['status' => 'ready']) }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition text-decoration-none {{ ($status ?? '') == 'ready' ? 'bg-teal-600 text-white' : 'bg-teal-50 text-teal-800 hover:bg-teal-100' }}">
                    Packed &amp; Ready
                </a>
                <a href="{{ route('admin.orders', ['status' => 'completed']) }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition text-decoration-none {{ ($status ?? '') == 'completed' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                    Collected &amp; Paid
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('admin.orders') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control form-control-sm rounded-xl text-xs" placeholder="Search order #, token, customer...">
                <button type="submit" class="btn btn-sm btn-forest rounded-xl px-3">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden mb-6">
        <div class="table-responsive">
            <table class="table align-middle mb-0 text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="ps-4">Order Ref</th>
                        <th>Customer</th>
                        <th>Farmer Stall</th>
                        <th>Pickup Slot</th>
                        <th>Produce Items</th>
                        <th>Amount ($)</th>
                        <th>QR Token</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Update Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="ps-4 py-3">
                                <strong class="text-slate-900 font-mono d-block">#{{ $ord->order_number }}</strong>
                                <span class="text-[11px] text-slate-400">{{ $ord->created_at->format('M d, h:i A') }}</span>
                            </td>
                            <td>
                                <strong class="text-slate-900 d-block">{{ $ord->customer ? $ord->customer->name : 'N/A' }}</strong>
                                <span class="text-xs text-slate-400">{{ $ord->customer ? $ord->customer->phone : '' }}</span>
                            </td>
                            <td>
                                <strong class="text-slate-800 d-block">{{ $ord->farmerProfile ? $ord->farmerProfile->stall_name : 'N/A' }}</strong>
                                <span class="badge bg-slate-100 text-slate-600 border text-[10px]">Stall {{ $ord->farmerProfile ? $ord->farmerProfile->stall_number : '' }}</span>
                            </td>
                            <td>
                                <span class="text-xs text-slate-700 font-medium d-block">{{ $ord->pickup_slot }}</span>
                                <span class="text-[11px] text-emerald-600 font-bold">-{{ number_format($ord->co2_saved_total, 1) }} kg CO2</span>
                            </td>
                            <td>
                                <span class="badge bg-slate-100 text-slate-700 border text-xs">
                                    {{ $ord->items->count() }} Produce Line(s)
                                </span>
                            </td>
                            <td>
                                <strong class="text-slate-900 font-bold fs-6">${{ number_format($ord->total_amount, 2) }}</strong>
                                <span class="text-[10px] d-block {{ $ord->payment_status === 'paid' ? 'text-emerald-600 font-bold' : 'text-amber-600' }}">
                                    {{ $ord->payment_status === 'paid' ? 'Cash Settled' : 'Pay at Stall' }}
                                </span>
                            </td>
                            <td>
                                <!-- 4-Digit Token & QR Badge -->
                                <div class="d-inline-flex flex-column align-items-center bg-slate-100 px-2.5 py-1 rounded-xl border border-slate-200">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">PIN</span>
                                    <span class="font-mono font-extrabold text-sm text-slate-900 tracking-widest">{{ $ord->pickup_token }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $ord->status_badge_class }}">
                                    {{ $ord->status_label }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('admin.order.update_status', $ord->id) }}" method="POST" class="d-inline-flex align-items-center gap-1">
                                    @csrf
                                    <select name="status" class="form-select form-select-sm text-xs rounded-xl font-semibold" style="width: 130px;" onchange="this.form.submit()">
                                        <option value="placed" {{ $ord->status == 'placed' ? 'selected' : '' }}>Placed</option>
                                        <option value="accepted" {{ $ord->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                        <option value="ready" {{ $ord->status == 'ready' ? 'selected' : '' }}>Packed Ready</option>
                                        <option value="completed" {{ $ord->status == 'completed' ? 'selected' : '' }}>Collected</option>
                                        <option value="cancelled" {{ $ord->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-slate-500 text-sm">
                                No pre-orders found matching status criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection

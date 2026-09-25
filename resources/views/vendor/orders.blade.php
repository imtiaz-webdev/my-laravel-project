@extends('layouts.app')

@section('title', 'Order Fulfilment Desk | ' . $farmer->stall_name)

@section('content')
<div class="container py-4">

    <!-- Header with QR Token Entry -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1">Order Fulfilment Desk</h2>
            <p class="text-muted small mb-0">Manage incoming morning reservations, pack crates, and authenticate pickup tokens</p>
        </div>

        <div>
            <button class="btn btn-forest btn-sm" data-bs-toggle="modal" data-bs-target="#verifyModal">
                <i class="fa-solid fa-qrcode me-1"></i> Scan QR / Settle Token
            </button>
        </div>
    </div>

    <!-- Tabbed Navigation Filter -->
    <ul class="nav nav-pills mb-4 gap-2">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'incoming' ? 'active' : '' }}" href="{{ route('vendor.orders', ['tab' => 'incoming']) }}">
                Incoming Placed ({{ $counts['incoming'] }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'accepted' ? 'active' : '' }}" href="{{ route('vendor.orders', ['tab' => 'accepted']) }}">
                Accepted & Packing ({{ $counts['accepted'] }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'ready' ? 'active' : '' }}" href="{{ route('vendor.orders', ['tab' => 'ready']) }}">
                Ready at Stall ({{ $counts['ready'] }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'completed' ? 'active' : '' }}" href="{{ route('vendor.orders', ['tab' => 'completed']) }}">
                Settled & Completed ({{ $counts['completed'] }})
            </a>
        </li>
    </ul>

    <!-- Orders Matrix -->
    <div class="ml-card p-4">
        <div class="table-responsive">
            <table class="table align-middle small mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Reserved Items</th>
                        <th>Pickup Slot</th>
                        <th>Total Due</th>
                        <th>4-Digit Token</th>
                        <th>Status</th>
                        <th class="text-end">Fulfillment Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="fw-bold">#{{ $order->order_number }}</td>
                            <td>
                                <strong>{{ $order->customer->name }}</strong>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $order->customer->phone }}</div>
                            </td>
                            <td>
                                <ul class="list-unstyled mb-0" style="font-size: 0.78rem;">
                                    @foreach($order->items as $it)
                                        <li>&bull; {{ $it->quantity }}x {{ $it->product_name }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $order->pickup_slot }}</span>
                            </td>
                            <td class="fw-bold text-success fs-6">
                                ${{ number_format($order->total_amount, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-dark text-white" style="font-family: monospace; letter-spacing: 1px;">
                                    {{ $order->pickup_token }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    @if($order->status === 'placed')
                                        <form action="{{ route('vendor.order.update_status', $order->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="accepted">
                                            <button type="submit" class="btn btn-sm btn-forest py-1 px-2" title="Confirm Pre-order">
                                                <i class="fa-solid fa-check me-1"></i> Accept
                                            </button>
                                        </form>
                                        <form action="{{ route('vendor.order.update_status', $order->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="Reject Order">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    @elseif($order->status === 'accepted')
                                        <form action="{{ route('vendor.order.update_status', $order->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="ready">
                                            <button type="submit" class="btn btn-sm btn-sage py-1 px-2">
                                                <i class="fa-solid fa-box me-1"></i> Pack & Ready
                                            </button>
                                        </form>
                                    @elseif($order->status === 'ready')
                                        <button class="btn btn-sm btn-ochre py-1 px-2" onclick="openTokenModal('{{ $order->pickup_token }}')">
                                            <i class="fa-solid fa-hand-holding-dollar me-1"></i> Settle Cash
                                        </button>
                                    @else
                                        <span class="text-success small fw-semibold"><i class="fa-solid fa-circle-check"></i> Settled</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No orders under '{{ ucfirst($tab) }}' tab.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- QR / Token Settle Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('vendor.verify_qr') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Authenticate Pickup Token</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fa-solid fa-qrcode fa-3x text-success mb-3"></i>
                    <p class="small text-muted mb-3">Enter the 4-digit token or full QR encrypted hash provided on the customer's digital receipt pass.</p>

                    <input type="text" name="pickup_token" id="modalTokenInput" class="form-control text-center fs-3 fw-bold mb-3" placeholder="####" style="letter-spacing: 4px; font-family: monospace;" required autofocus>

                    <div class="small text-muted">Upon submit, order will mark as 'Collected & Settled' and cash balance confirmed.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-forest">Confirm & Settle Cash</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openTokenModal(token) {
        document.getElementById('modalTokenInput').value = token;
        const modal = new bootstrap.Modal(document.getElementById('verifyModal'));
        modal.show();
    }
</script>
@endpush

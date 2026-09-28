@extends('layouts.app')

@section('title', 'Pre-Order #' . $order->order_number . ' | Pickup Receipt')

@section('content')
<div class="container py-4">

    <!-- Breadcrumb -->
    <nav class="mb-4 small">
        <a href="{{ route('customer.dashboard') }}" class="text-muted">My Pre-Orders</a>
        <span class="mx-2 text-muted">/</span>
        <span class="text-dark fw-medium">Order #{{ $order->order_number }}</span>
    </nav>

    <div class="row g-4">
        <!-- Order Details & Items -->
        <div class="col-lg-7">
            <div class="ml-card p-4 mb-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
                        <h3 class="fw-bold mt-2 mb-1">Pre-Order #{{ $order->order_number }}</h3>
                        <div class="small text-muted">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</div>
                    </div>
                    <div class="text-end">
                        <span class="price-tag fs-3">${{ number_format($order->total_amount, 2) }}</span>
                        <div class="small text-muted">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</div>
                    </div>
                </div>

                <!-- Order Status Stepper -->
                <div class="order-stepper my-4">
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

                <!-- Pickup Logistics -->
                <div class="p-3 rounded mb-4" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
                    <div class="row g-2 small">
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Producer Stall:</span>
                            <strong class="text-dark">{{ $order->farmerProfile->stall_name }}</strong> ({{ $order->farmerProfile->stall_number }})
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted d-block">Market Venue:</span>
                            <strong class="text-dark">{{ $order->market ? $order->market->name : 'Regional Market' }}</strong>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <span class="text-muted d-block">Designated Window:</span>
                            <strong class="text-success">{{ $order->pickup_slot }}</strong>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <span class="text-muted d-block">Ecological Transport Offset:</span>
                            <strong class="text-success">-{{ number_format($order->co2_saved_total, 2) }} kg CO2</strong>
                        </div>
                    </div>
                </div>

                <!-- Produce Line Items -->
                <h5 class="fw-bold mb-3">Harvest Items</h5>
                <div class="table-responsive">
                    <table class="table align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th class="text-end">Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                        @if($item->is_zero_waste)
                                            <span class="badge-zero-waste ms-1" style="font-size: 0.65rem;">Zero-Waste Cut</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->quantity }} {{ $item->product_unit }}</td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end fw-bold">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="table-light">
                                <td colspan="3" class="fw-bold">Total Cash Settleable at Stall:</td>
                                <td class="text-end fw-bold fs-6">${{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($order->notes)
                    <div class="mt-3 p-2 rounded bg-light border small text-muted">
                        <strong>Customer Instructions:</strong> {{ $order->notes }}
                    </div>
                @endif
            </div>

            <!-- Verified Review Submission (For Completed Orders) -->
            @if($order->status === 'completed')
                <div class="ml-card p-4" id="reviewSection">
                    <h5 class="fw-bold mb-2">Verified Stall Feedback</h5>
                    @if($order->review)
                        <div class="p-3 rounded bg-light border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge-verified-buyer">
                                    <i class="fa-solid fa-circle-check"></i> Published Review
                                </span>
                                <div class="text-warning small">
                                    @for($i = 1; $i <= $order->review->rating; $i++)
                                        <i class="fa-solid fa-star"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="small text-dark mb-1">"{{ $order->review->comment }}"</p>
                            <div class="small text-muted">{{ $order->review->created_at->format('M d, Y') }}</div>

                            @if($order->review->farmer_reply)
                                <div class="mt-2 p-2 rounded bg-white border small">
                                    <strong class="text-success"><i class="fa-solid fa-reply me-1"></i> Farmer Response:</strong>
                                    <span class="text-muted">{{ $order->review->farmer_reply }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <form action="{{ route('customer.review.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Rating (1 to 5 Stars)</label>
                                <select name="rating" class="form-select form-select-sm" style="max-width: 160px;" required>
                                    <option value="5">5 - Outstanding Freshness</option>
                                    <option value="4">4 - Very Good Quality</option>
                                    <option value="3">3 - Satisfactory</option>
                                    <option value="2">2 - Below Expectations</option>
                                    <option value="1">1 - Unsatisfactory</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Review Comment</label>
                                <textarea name="comment" rows="3" class="form-control form-control-sm" placeholder="Describe the quality of produce, pickup ease, and interaction with the grower..." required minlength="10"></textarea>
                            </div>

                            <button type="submit" class="btn btn-sm btn-forest">
                                Submit Verified Buyer Review
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        <!-- TechWiz Winning Touch 4: QR Code Verification Receipt Panel -->
        <div class="col-lg-5">
            <div class="ml-card p-4 text-center">
                <span class="badge-co2 mb-3">
                    <i class="fa-solid fa-qrcode"></i> Stall Collection Pass
                </span>

                <h5 class="fw-bold mb-1">Digital Pickup Pass</h5>
                <p class="text-muted small mb-4">Present this barcode or 4-digit token to the grower at the stall</p>

                <!-- QR Code Canvas -->
                <div class="qr-code-box mb-3 shadow-sm">
                    <canvas id="qrCanvas"></canvas>
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block">4-Digit Confirmation Token:</span>
                    <span class="qr-token-pin">{{ $order->pickup_token }}</span>
                </div>

                <div class="p-3 rounded text-start mb-3" style="background-color: #f7faf8; border: 1px solid #d5eadc;">
                    <div class="small fw-semibold text-dark mb-1">Pickup Instructions:</div>
                    <ul class="mb-0 ps-3 small text-muted" style="line-height: 1.45;">
                        <li>Head to <strong>{{ $order->farmerProfile->stall_number }}</strong> ({{ $order->farmerProfile->stall_name }}).</li>
                        <li>Show this screen to the stall manager or speak your token: <strong>{{ $order->pickup_token }}</strong>.</li>
                        <li>Settle <strong>${{ number_format($order->total_amount, 2) }}</strong> in cash upon inspection of your harvest box.</li>
                    </ul>
                </div>

                <button class="btn btn-outline-secondary btn-sm w-100" onclick="window.print()">
                    <i class="fa-solid fa-print me-1"></i> Print / Save Receipt
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Generate cryptographic QR code offline
        const qr = new QRious({
            element: document.getElementById('qrCanvas'),
            value: '{{ $order->qr_code_hash }}',
            size: 144,
            background: '#FAF9F6',
            foreground: '#1b4332'
        });
    });
</script>
@endpush

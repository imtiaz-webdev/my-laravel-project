@extends('layouts.app')

@section('title', 'Finalize Pickup Windows & Settlement | MarketLink')

@section('content')
<div class="container py-4">

    <div class="mb-4">
        <h2 class="mb-1">Finalize Pre-Order Collection Windows</h2>
        <p class="text-muted small mb-0">Select your preferred morning pickup slots for each producer stall and review cash terms</p>
    </div>

    <form action="{{ route('customer.order.process') }}" method="POST">
        @csrf

        <div class="row g-4">
            <!-- Stall Pickup Schedule Details -->
            <div class="col-lg-8">
                @foreach($itemsByFarmer as $farmerId => $group)
                    <div class="ml-card mb-4">
                        <div class="ml-card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $group['farmer']->stall_name }}</h6>
                                <span class="small text-muted">{{ $group['farmer']->stall_number }} &bull; {{ $group['farmer']->market ? $group['farmer']->market->name : 'Central Market' }}</span>
                            </div>
                            <span class="badge bg-light text-success border">
                                ${{ number_format($group['subtotal'], 2) }}
                            </span>
                        </div>

                        <div class="ml-card-body">
                            <!-- Items List -->
                            <div class="mb-3">
                                <span class="small text-muted fw-semibold">Reserved Produce Items:</span>
                                <ul class="list-unstyled mb-0 mt-1">
                                    @foreach($group['items'] as $item)
                                        <li class="small d-flex justify-content-between py-1 border-bottom">
                                            <span>
                                                <strong>{{ $item['quantity'] }}x</strong> {{ $item['product']->name }}
                                                @if($item['product']->is_zero_waste_active)
                                                    <span class="badge-zero-waste ms-1" style="font-size: 0.65rem;">Zero-Waste Cut</span>
                                                @endif
                                            </span>
                                            <span class="text-dark">${{ number_format($item['subtotal'], 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Pickup Window Selection -->
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Pickup Time Window</label>
                                    <select name="pickup_slot_{{ $farmerId }}" class="form-select form-select-sm" required>
                                        <option value="Saturday Morning 08:00 AM - 09:30 AM">Saturday 08:00 AM - 09:30 AM (Early Pick)</option>
                                        <option value="Saturday Morning 09:30 AM - 11:00 AM" selected>Saturday 09:30 AM - 11:00 AM (Standard)</option>
                                        <option value="Saturday Midday 11:00 AM - 12:30 PM">Saturday 11:00 AM - 12:30 PM (Midday)</option>
                                        <option value="Sunday Morning 09:00 AM - 11:00 AM">Sunday 09:00 AM - 11:00 AM (Community Day)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Special Instructions for Farmer (Optional)</label>
                                    <input type="text" name="notes_{{ $farmerId }}" class="form-control form-control-sm" placeholder="e.g. Packing in reusable canvas, extra ripe batch">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Settlement & Agreement Summary -->
            <div class="col-lg-4">
                <div class="ml-card p-4">
                    <h5 class="fw-bold mb-3">Settlement Agreement</h5>

                    <div class="p-3 rounded mb-3" style="background-color: #f7faf8; border: 1px solid #c7e5ce;">
                        <div class="d-flex align-items-center gap-2 mb-2 text-success fw-bold small">
                            <i class="fa-solid fa-handshake"></i> Direct Cash on Pickup
                        </div>
                        <p class="small text-muted mb-0" style="line-height: 1.45;">
                            In accordance with eGreen Basket transparency rules, 100% of your payment is settled directly in cash to the grower at the stall. No intermediary merchant deduction fees are incurred.
                        </p>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Total Producers:</span>
                        <strong class="text-dark">{{ count($itemsByFarmer) }} Stalls</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Transport Carbon Saved:</span>
                        <strong class="text-success">{{ number_format($totalCo2, 2) }} kg CO2</strong>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-baseline mb-4">
                        <span class="fs-5 fw-bold">Total Cash Due:</span>
                        <span class="price-tag fs-3">${{ number_format($grandTotal, 2) }}</span>
                    </div>

                    <button type="submit" class="btn btn-forest w-100 py-2">
                        <i class="fa-solid fa-qrcode me-2"></i> Confirm & Generate QR Tokens
                    </button>

                    <div class="text-center mt-3">
                        <span class="small text-muted" style="font-size: 0.76rem;">
                            <i class="fa-solid fa-lock text-success me-1"></i> Unique cryptographic hash & 4-digit token generated on submit.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection

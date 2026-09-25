@extends('layouts.app')

@section('title', $product->name . ' | Harvest Pre-Order')

@section('content')
<div class="container py-4">

    <!-- Breadcrumb -->
    <nav class="mb-4 small">
        <a href="{{ route('home') }}" class="text-muted">Marketplace</a>
        <span class="mx-2 text-muted">/</span>
        <a href="{{ route('farmer.show', $product->farmerProfile->slug) }}" class="text-muted">{{ $product->farmerProfile->stall_name }}</a>
        <span class="mx-2 text-muted">/</span>
        <span class="text-dark fw-medium">{{ $product->name }}</span>
    </nav>

    <div class="row g-5 mb-5">
        <!-- Product Image Preview -->
        <div class="col-lg-6">
            <div class="ml-card overflow-hidden">
                <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $product->name }}" class="w-100 object-fit-cover" style="height: 420px;">
            </div>
            <div class="mt-3 d-flex gap-2">
                <span class="badge-harvest-time">
                    <i class="fa-solid fa-clock me-1"></i> {{ $product->harvest_status_string }}
                </span>
                <span class="badge-co2">
                    <i class="fa-solid fa-leaf me-1"></i> {{ $product->co2_savings_kg }} kg CO2 Offset
                </span>
                @if($product->is_zero_waste_active)
                    <span class="badge-zero-waste">
                        <i class="fa-solid fa-tag me-1"></i> {{ $product->discount_percent }}% Zero-Waste Batch Discount
                    </span>
                @endif
            </div>
        </div>

        <!-- Product Purchase & Harvest Details -->
        <div class="col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <a href="{{ route('farmer.show', $product->farmerProfile->slug) }}" class="small fw-semibold text-success">
                    <i class="fa-solid fa-store me-1"></i> {{ $product->farmerProfile->stall_name }} ({{ $product->farmerProfile->stall_number }})
                </a>
                <span class="badge bg-light text-dark border small">Stall Stock: {{ $product->stock_quantity }} {{ $product->unit }}s</span>
            </div>

            <h1 class="display-6 fw-bold mb-3">{{ $product->name }}</h1>

            <!-- Price & Unit -->
            <div class="p-3 rounded mb-4" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    @if($product->is_zero_waste_active)
                        <span class="price-original fs-5">${{ number_format($product->price, 2) }}</span>
                        <span class="price-tag price-discounted fs-2">${{ number_format($product->effective_price, 2) }}</span>
                    @else
                        <span class="price-tag fs-2">${{ number_format($product->price, 2) }}</span>
                    @endif
                    <span class="text-muted">/ {{ $product->unit }}</span>
                </div>
                @if($product->is_zero_waste_active)
                    <div class="small text-danger fw-semibold">
                        <i class="fa-solid fa-fire me-1"></i> Zero-Waste Auto-Discount Active: Save ${{ number_format($product->price - $product->zero_waste_price, 2) }} per {{ $product->unit }}.
                    </div>
                @endif
            </div>

            <!-- TechWiz Winning Touch 1: Dynamic Harvest Micro-Countdown -->
            <div class="p-3 rounded mb-4" style="background-color: #f1f8f3; border: 1px solid #c7e5ce;">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="fa-solid fa-calendar-check text-success"></i>
                    <strong class="text-dark small">Harvest Timeline & Freshness Guarantee</strong>
                </div>
                <div class="small text-muted mb-2">
                    Harvest Note: {{ $product->harvest_notes ?? 'Field harvested and prepared specifically for morning pickup.' }}
                </div>
                <div class="small text-success fw-semibold">
                    Live Status: {{ $product->harvest_status_string }} ({{ $product->stock_quantity }} {{ $product->unit }}s remaining for upcoming market session)
                </div>
            </div>

            <p class="text-muted mb-4">{{ $product->description }}</p>

            <!-- TechWiz Winning Touch 5: Real Carbon Footprint Metric -->
            <div class="d-flex align-items-center gap-3 p-3 rounded mb-4" style="background-color: #fdfaf5; border: 1px solid #ebdcc5;">
                <i class="fa-solid fa-earth-americas text-warning fa-2x"></i>
                <div class="small">
                    <strong>Environmental Impact Metric:</strong>
                    <div class="text-muted">
                        Buying this local {{ $product->unit }} saves approximately <strong>{{ $product->co2_savings_kg }} kg CO2</strong> transport emissions compared to conventional refrigerated wholesale supply chains.
                    </div>
                </div>
            </div>

            <!-- Purchase / Reserve Controls -->
            <div class="d-flex align-items-center gap-3">
                <div style="width: 90px;">
                    <label class="form-label small text-muted mb-1">Quantity</label>
                    <input type="number" id="qty-{{ $product->id }}" value="1" min="1" max="{{ $product->stock_quantity }}" class="form-control text-center">
                </div>
                <div class="flex-grow-1 pt-4">
                    <button class="btn btn-forest w-100 py-2 btn-add-to-basket" data-product-id="{{ $product->id }}">
                        <i class="fa-solid fa-cart-plus me-2"></i> Add Produce to Basket
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@extends('layouts.app')

@section('title', $product->name . ' | Harvest Pre-Order')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Breadcrumb -->
    <nav class="mb-6 text-xs text-slate-500 d-flex align-items-center gap-2">
        <a href="{{ route('home') }}" class="text-slate-500 hover:text-emerald-700 text-decoration-none">Marketplace</a>
        <span>/</span>
        <a href="{{ route('farmer.show', $product->farmerProfile->slug) }}" class="text-slate-500 hover:text-emerald-700 text-decoration-none">{{ $product->farmerProfile->stall_name }}</a>
        <span>/</span>
        <span class="text-slate-900 font-bold">{{ $product->name }}</span>
    </nav>

    <div class="row g-5 mb-10">
        <!-- Product Image Preview with Modern Rounded-3xl Frame -->
        <div class="col-lg-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 overflow-hidden shadow-sm">
                <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $product->name }}" class="w-100 object-cover" style="height: 440px;">
            </div>
            <div class="mt-3.5 d-flex flex-wrap gap-2">
                <span class="badge-harvest-pill">
                    <i class="fa-solid fa-clock text-emerald-600"></i> {{ $product->harvest_label }}
                </span>
                <span class="badge-co2-pill">
                    <i class="fa-solid fa-leaf text-emerald-600"></i> {{ number_format($product->co2_savings_kg, 1) }} kg CO2 Offset
                </span>
                @if($product->is_zero_waste_active)
                    <span class="badge-flash-cut">
                        <i class="fa-solid fa-bolt text-amber-600"></i> {{ $product->discount_percent }}% Zero-Waste Batch
                    </span>
                @endif
            </div>
        </div>

        <!-- Product Purchase & Harvest Details -->
        <div class="col-lg-6">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <a href="{{ route('farmer.show', $product->farmerProfile->slug) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 text-decoration-none">
                    <i class="fa-solid fa-store me-1"></i> {{ $product->farmerProfile->stall_name }} ({{ $product->farmerProfile->stall_number }})
                </a>
                <span class="badge bg-slate-100 text-slate-700 border border-slate-200 text-xs px-2.5 py-1 rounded-full font-semibold">
                    Stall Stock: {{ $product->stock_quantity }} {{ $product->unit }}s
                </span>
            </div>

            <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900 mb-4 font-heading">{{ $product->name }}</h1>

            <!-- Price & Unit -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 mb-5">
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    @if($product->is_zero_waste_active)
                        <span class="price-strikethrough text-xl">${{ number_format($product->price, 2) }}</span>
                        <span class="text-3xl font-extrabold text-amber-700 font-heading">${{ number_format($product->effective_price, 2) }}</span>
                    @else
                        <span class="text-3xl font-extrabold text-slate-900 font-heading">${{ number_format($product->price, 2) }}</span>
                    @endif
                    <span class="text-sm font-semibold text-slate-500">/ {{ $product->unit }}</span>
                </div>
                @if($product->is_zero_waste_active)
                    <div class="text-xs text-amber-700 font-bold d-flex align-items-center gap-1.5 mt-1">
                        <i class="fa-solid fa-fire text-amber-500"></i>
                        <span>Zero-Waste Cut Active: Save ${{ number_format($product->price - $product->zero_waste_price, 2) }} per {{ $product->unit }}.</span>
                    </div>
                @endif
            </div>

            <!-- Harvest Timeline & Freshness Guarantee (Dynamic Label Formatted) -->
            <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/70 mb-5">
                <div class="d-flex align-items-center gap-2 mb-1.5">
                    <i class="fa-solid fa-calendar-check text-emerald-600"></i>
                    <strong class="text-slate-900 text-xs uppercase tracking-wider font-bold">Harvest Freshness Log</strong>
                </div>
                <div class="text-xs text-slate-600 mb-2">
                    {{ $product->harvest_notes ?? 'Field harvested and prepared specifically for morning pickup.' }}
                </div>
                <div class="text-xs text-emerald-800 font-bold">
                    Current Status: {{ $product->harvest_label }} ({{ $product->stock_quantity }} {{ $product->unit }}s remaining for upcoming session)
                </div>
            </div>

            <p class="text-sm text-slate-600 mb-6 leading-relaxed">{{ $product->description }}</p>

            <!-- Real Carbon Footprint Metric -->
            <div class="d-flex align-items-center gap-3 p-3.5 rounded-2xl bg-amber-50/50 border border-amber-200/60 mb-6">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-earth-americas text-base"></i>
                </div>
                <div class="text-xs text-slate-600">
                    <strong class="text-slate-900 d-block mb-0.5">Ecological Transport Metric:</strong>
                    Reserving this local produce saves <strong>{{ number_format($product->co2_savings_kg, 1) }} kg CO2</strong> emissions compared to long-haul wholesale distribution.
                </div>
            </div>

            <!-- Purchase / Reserve Controls -->
            <div class="d-flex align-items-center gap-3">
                <div style="width: 90px;">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider d-block mb-1">Quantity</label>
                    <input type="number" id="qty-{{ $product->id }}" value="1" min="1" max="{{ $product->stock_quantity }}" class="form-control text-center font-bold rounded-xl py-2">
                </div>
                <div class="flex-grow-1 pt-4">
                    <button class="btn-cta-emerald w-100 justify-content-center py-2.5 btn-add-to-basket" data-product-id="{{ $product->id }}">
                        <i class="fa-solid fa-cart-plus me-1"></i> Add Produce to Basket
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

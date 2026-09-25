@extends('layouts.app')

@section('title', $farmer->stall_name . ' | Farmer Stall Profile')

@section('content')
<div class="container py-4">

    <!-- Farmer Header Profile Card -->
    <div class="ml-card p-4 mb-4">
        <div class="row align-items-center g-4">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-dark text-white px-2 py-1 small">{{ $farmer->stall_number }}</span>
                    <span class="badge-co2">
                        <i class="fa-solid fa-leaf"></i> {{ $farmer->carbon_score_rating }}/5.0 Carbon Rating
                    </span>
                    @if($farmer->market)
                        <span class="badge bg-light text-muted border small">
                            <i class="fa-solid fa-map-pin me-1 text-danger"></i> {{ $farmer->market->name }}
                        </span>
                    @endif
                </div>

                <h1 class="display-6 fw-bold mb-2">{{ $farmer->stall_name }}</h1>
                <p class="text-muted mb-3" style="max-width: 680px;">{{ $farmer->bio }}</p>

                <div class="d-flex flex-wrap gap-3 small text-muted">
                    <div>
                        <strong class="text-dark"><i class="fa-solid fa-location-arrow me-1 text-success"></i> Farm Origin:</strong> {{ $farmer->farm_origin }}
                    </div>
                    @if($farmer->certifications)
                        <div>
                            <strong class="text-dark"><i class="fa-solid fa-certificate me-1 text-warning"></i> Certifications:</strong> {{ $farmer->certifications }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-md-4 text-md-end">
                @auth
                    @if(auth()->user()->isCustomer())
                        @php
                            $isFav = auth()->user()->favoriteFarmers()->where('farmer_profile_id', $farmer->id)->exists();
                        @endphp
                        <button class="btn btn-outline-forest btn-toggle-favorite {{ $isFav ? 'text-danger' : '' }} mb-2 w-100 w-md-auto" data-farmer-id="{{ $farmer->id }}">
                            <i class="fa-{{ $isFav ? 'solid' : 'regular' }} fa-heart me-1"></i>
                            {{ $isFav ? 'Favorited Grower' : 'Add to Favorites' }}
                        </button>
                    @endif
                @endauth
                <div class="p-3 rounded mt-2 text-start" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
                    <div class="small text-muted mb-1">Pre-Order Pickup Desk:</div>
                    <div class="fw-bold text-dark">{{ $farmer->market ? $farmer->market->timing : 'Standard Market Hours' }}</div>
                    <div class="small text-success mt-1"><i class="fa-solid fa-qrcode me-1"></i> QR Token Verification Ready</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Zero-Waste Produce Section (if available) -->
    @if($zeroWasteItems->isNotEmpty())
        <div class="mb-5 p-4 rounded-3 border" style="background-color: #fbf7f0; border-color: #edd9be !important;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <span class="badge-zero-waste mb-1">
                        <i class="fa-solid fa-tag"></i> Active Zero-Waste Batch
                    </span>
                    <h4 class="mb-0 text-dark">Closing Window Bundles</h4>
                </div>
                <span class="small text-muted">Discount automatically applied</span>
            </div>

            <div class="row g-3">
                @foreach($zeroWasteItems as $item)
                    <div class="col-lg-4 col-md-6">
                        <div class="ml-card bg-white p-3 h-100 d-flex flex-column">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge-harvest-time">{{ $item->harvest_status_string }}</span>
                                <span class="badge-co2">-{{ $item->co2_savings_kg }} kg CO2</span>
                            </div>
                            <h6 class="fw-bold mb-1"><a href="{{ route('product.show', $item->slug) }}" class="text-dark">{{ $item->name }}</a></h6>
                            <p class="small text-muted mb-3 flex-grow-1">{{ Str::limit($item->description, 60) }}</p>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <div>
                                    <span class="price-original">${{ number_format($item->price, 2) }}</span>
                                    <span class="price-tag price-discounted">${{ number_format($item->effective_price, 2) }}</span>
                                    <span class="small text-muted">/ {{ $item->unit }}</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <input type="number" id="qty-{{ $item->id }}" value="1" min="1" max="{{ $item->stock_quantity }}" class="form-control form-control-sm text-center" style="width: 50px;">
                                    <button class="btn btn-sm btn-forest btn-add-to-basket" data-product-id="{{ $item->id }}">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Regular Harvest Produce Matrix -->
    <div class="mb-5">
        <h3 class="mb-3">Current Harvest Roster</h3>

        <div class="row g-4">
            @forelse($standardItems as $product)
                <div class="col-lg-3 col-md-6">
                    <div class="ml-card ml-card-hover h-100 d-flex flex-column">
                        <div class="product-media">
                            <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1598170845058-32b9d6a5c317?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $product->name }}">
                            <div class="product-badges">
                                <span class="badge-harvest-time">{{ $product->harvest_status_string }}</span>
                            </div>
                        </div>
                        <div class="ml-card-body d-flex flex-column flex-grow-1">
                            <h6 class="fw-bold mb-1"><a href="{{ route('product.show', $product->slug) }}" class="text-dark">{{ $product->name }}</a></h6>
                            <p class="small text-muted mb-3 flex-grow-1">{{ Str::limit($product->description, 60) }}</p>
                            <div class="small mb-2 text-muted">
                                <i class="fa-solid fa-tree text-success me-1"></i> {{ $product->co2_savings_kg }} kg CO2 Saved
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <div>
                                    <span class="price-tag">${{ number_format($product->price, 2) }}</span>
                                    <span class="small text-muted">/ {{ $product->unit }}</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <input type="number" id="qty-{{ $product->id }}" value="1" min="1" max="{{ $product->stock_quantity }}" class="form-control form-control-sm text-center" style="width: 50px;">
                                    <button class="btn btn-sm btn-forest btn-add-to-basket" data-product-id="{{ $product->id }}">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted">No additional harvest items listed for this cycle.</div>
            @endforelse
        </div>
    </div>

    <!-- Verified Buyer Reviews for this Farmer -->
    <div class="ml-card p-4">
        <h4 class="mb-3">Verified Buyer Reviews</h4>
        <div class="row g-3">
            @forelse($farmer->reviews as $rev)
                <div class="col-md-6">
                    <div class="p-3 rounded bg-white border h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge-verified-buyer">
                                <i class="fa-solid fa-circle-check"></i> Verified Order
                            </span>
                            <div class="text-warning small">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $rev->rating ? '' : 'text-muted opacity-25' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="small text-dark mb-2">"{{ $rev->comment }}"</p>
                        <div class="small text-muted mb-2">&mdash; {{ $rev->customer->name }}, {{ $rev->created_at->format('M d, Y') }}</div>
                        @if($rev->farmer_reply)
                            <div class="p-2 rounded bg-light border small">
                                <strong class="text-success"><i class="fa-solid fa-reply me-1"></i> Grower Reply:</strong>
                                <span class="text-muted">{{ $rev->farmer_reply }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted small">No customer reviews recorded yet for this producer stall.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection

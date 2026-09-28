@extends('layouts.app')

@section('title', $farmer->stall_name . ' | Farmer Stall Profile')

@section('content')
<div class="container mx-auto px-4 py-8">

    <!-- Farmer Header Profile Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm mb-8">
        <div class="row align-items-center g-4">
            <div class="col-md-8">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-900 text-white text-xs font-bold">{{ $farmer->stall_number }}</span>
                    <span class="badge-organic">
                        <i class="fa-solid fa-leaf text-xs"></i> {{ $farmer->carbon_score_rating }}/5.0 Carbon Rating
                    </span>
                    @if($farmer->market)
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold">
                            <i class="fa-solid fa-map-pin me-1 text-emerald-600"></i> {{ $farmer->market->name }}
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-2 font-heading">{{ $farmer->stall_name }}</h1>
                <p class="text-sm text-slate-600 mb-4 max-w-2xl leading-relaxed">{{ $farmer->bio }}</p>

                <div class="d-flex flex-wrap gap-4 text-xs text-slate-500">
                    <div>
                        <strong class="text-slate-800"><i class="fa-solid fa-location-arrow me-1 text-emerald-600"></i> Farm Origin:</strong> {{ $farmer->farm_origin }}
                    </div>
                    @if($farmer->certifications)
                        <div>
                            <strong class="text-slate-800"><i class="fa-solid fa-certificate me-1 text-amber-500"></i> Certifications:</strong> {{ $farmer->certifications }}
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
                        <button class="btn btn-outline-forest btn-toggle-favorite {{ $isFav ? 'text-danger' : '' }} mb-3 w-100 w-md-auto rounded-full py-2 px-4 text-xs font-bold" data-farmer-id="{{ $farmer->id }}">
                            <i class="fa-{{ $isFav ? 'solid' : 'regular' }} fa-heart me-1"></i>
                            {{ $isFav ? 'Favorited Grower' : 'Add to Favorites' }}
                        </button>
                    @endif
                @endauth
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-start text-xs">
                    <div class="text-slate-500 mb-1">Pre-Order Pickup Desk:</div>
                    <div class="font-bold text-slate-900 text-sm mb-1">{{ $farmer->market ? $farmer->market->timing : 'Standard Market Hours' }}</div>
                    <div class="text-emerald-700 font-semibold"><i class="fa-solid fa-qrcode me-1"></i> QR Token Verification Ready</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Zero-Waste Produce Section (if available) -->
    @if($zeroWasteItems->isNotEmpty())
        <div class="mb-10 p-6 rounded-3xl bg-gradient-to-r from-amber-50/70 via-white to-amber-50/40 border border-amber-200/80 shadow-xs">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="badge-flash-cut mb-1">
                        <i class="fa-solid fa-bolt"></i> Active Zero-Waste Batch
                    </span>
                    <h4 class="font-bold text-xl text-slate-900 mb-0 font-heading">Closing Window Bundles</h4>
                </div>
                <span class="text-xs text-slate-500 font-medium">Automatic markdown applied</span>
            </div>

            <div class="row g-4">
                @foreach($zeroWasteItems as $item)
                    <div class="col-lg-4 col-md-6">
                        <div class="product-card-modern bg-white h-100 group">
                            <div class="product-image-container" style="height: 180px;">
                                <img src="{{ $item->image_url ?? 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $item->name }}" loading="lazy">
                                <div class="absolute top-2.5 left-2.5 d-flex flex-column gap-1 z-10">
                                    <span class="badge-flash-cut text-[11px]">{{ $item->discount_percent }}% Off</span>
                                    <span class="badge-harvest-pill text-[11px]"><i class="fa-solid fa-clock text-amber-600"></i> {{ $item->harvest_label }}</span>
                                </div>
                            </div>
                            <div class="p-3.5 d-flex flex-column flex-grow-1">
                                <h6 class="font-bold text-sm text-slate-900 mb-1 group-hover:text-emerald-700 transition">
                                    <a href="{{ route('product.show', $item->slug) }}" class="text-decoration-none text-slate-900 group-hover:text-emerald-700">{{ $item->name }}</a>
                                </h6>
                                <p class="text-xs text-slate-500 mb-3 flex-grow-1 line-clamp-2">{{ Str::limit($item->description, 60) }}</p>
                                <div class="d-flex justify-content-between align-items-center pt-2.5 border-t border-slate-100 mt-auto">
                                    <div>
                                        <span class="price-strikethrough text-xs">${{ number_format($item->price, 2) }}</span>
                                        <span class="price-display price-flash text-base">${{ number_format($item->effective_price, 2) }}</span>
                                        <span class="text-[11px] text-slate-400">/ {{ $item->unit }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <input type="number" id="qty-{{ $item->id }}" value="1" min="1" max="{{ $item->stock_quantity }}" class="form-control form-control-sm text-center font-bold rounded-lg text-xs" style="width: 44px; height: 32px;">
                                        <button class="btn-add-modern btn-add-to-basket py-1 px-2.5 text-xs rounded-lg" data-product-id="{{ $item->id }}">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Regular Harvest Produce Matrix -->
    <div class="mb-10">
        <h3 class="font-bold text-2xl text-slate-900 mb-4 font-heading">Current Harvest Roster</h3>

        <div class="row g-4">
            @forelse($standardItems as $product)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="product-card-modern group h-100">
                        <div class="product-image-container" style="height: 190px;">
                            <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1598170845058-32b9d6a5c317?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $product->name }}" loading="lazy">
                            <div class="absolute top-2.5 left-2.5 z-10">
                                <span class="badge-harvest-pill text-[11px]"><i class="fa-solid fa-clock text-emerald-600"></i> {{ $product->harvest_label }}</span>
                            </div>
                        </div>
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <h6 class="font-bold text-base text-slate-900 mb-1 group-hover:text-emerald-700 transition">
                                <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none text-slate-900 group-hover:text-emerald-700">{{ $product->name }}</a>
                            </h6>
                            <p class="text-xs text-slate-500 mb-3 flex-grow-1 line-clamp-2">{{ Str::limit($product->description, 60) }}</p>
                            <div class="text-xs text-slate-500 mb-3">
                                <i class="fa-solid fa-tree text-emerald-600 me-1"></i> {{ number_format($product->co2_savings_kg, 1) }} kg CO2 Saved
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-t border-slate-100 mt-auto">
                                <div>
                                    <span class="price-display text-lg">${{ number_format($product->price, 2) }}</span>
                                    <span class="text-xs text-slate-400">/ {{ $product->unit }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-1.5">
                                    <input type="number" id="qty-{{ $product->id }}" value="1" min="1" max="{{ $product->stock_quantity }}" class="form-control form-control-sm text-center font-bold rounded-lg text-xs" style="width: 44px; height: 32px;">
                                    <button class="btn-add-modern btn-add-to-basket py-1 px-2.5 text-xs rounded-lg" data-product-id="{{ $product->id }}">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-slate-500 text-sm">No additional harvest items listed for this cycle.</div>
            @endforelse
        </div>
    </div>

    <!-- Verified Buyer Reviews for this Farmer -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <h4 class="font-bold text-xl text-slate-900 mb-4 font-heading">Verified Buyer Reviews</h4>
        <div class="row g-4">
            @forelse($farmer->reviews as $rev)
                <div class="col-md-6">
                    <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200/60 h-100 text-xs">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge-organic text-[11px]">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i> Verified Order
                            </span>
                            <div class="text-amber-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $rev->rating ? '' : 'text-slate-200' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="text-slate-700 leading-relaxed mb-2 font-medium">"{{ $rev->comment }}"</p>
                        <div class="text-slate-400 mb-2">&mdash; {{ $rev->customer->name }}, {{ $rev->created_at->format('M d, Y') }}</div>
                        @if($rev->farmer_reply)
                            <div class="p-2.5 rounded-xl bg-white border border-slate-200/60 mt-2">
                                <strong class="text-emerald-700 d-block mb-0.5"><i class="fa-solid fa-reply me-1"></i> Grower Reply:</strong>
                                <span class="text-slate-600">{{ $rev->farmer_reply }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12 text-slate-400 text-xs">No customer reviews recorded yet for this producer stall.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection

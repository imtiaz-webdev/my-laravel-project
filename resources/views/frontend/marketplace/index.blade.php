@extends('layouts.app')

@section('title', 'MarketLink | Local Organic Harvest Pre-Orders & Zero-Waste Baskets')

@section('content')
<div class="container mx-auto px-4 py-6">

    <section class="market-hero mb-5">
        <div class="row align-items-center g-0">
            <div class="col-lg-7">
                <div class="market-hero-copy">
                    <div class="market-kicker"><span></span> Local growers, one marketplace</div>

                    <h1 class="market-hero-title">
                        Fresh from the farm.<br>
                        <span>Ready for your table.</span>
                    </h1>

                    <p class="market-hero-description">
                        Reserve the morning harvest from regional growers, find seasonal prices, and collect your order at a nearby market.
                    </p>

                    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
                        <a href="{{ route('market.explorer') }}" class="btn-market-primary text-decoration-none">
                            Explore the market
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 12h15m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="square"/></svg>
                        </a>
                        <a href="#zero-waste" class="btn-market-secondary text-decoration-none">Browse reduced-price harvest</a>
                    </div>

                    <div class="market-hero-facts">
                        <div><strong>{{ number_format($totalCo2Saved, 1) }} kg</strong><span>CO2 avoided</span></div>
                        <div><strong>{{ $totalFarmersCount }}</strong><span>local producers</span></div>
                        <div><strong>Direct</strong><span>market pickup</span></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <figure class="market-hero-visual">
                    <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1200&q=85" alt="Fresh produce displayed at a local farmers market" fetchpriority="high">
                    <figcaption><span>Market day</span><strong>Downtown Civic Plaza</strong><a href="{{ route('market.explorer', ['market_id' => 1]) }}">View stalls <span aria-hidden="true">&rarr;</span></a></figcaption>
                </figure>
            </div>
        </div>
    </section>

    <!-- Category Pills Strip -->
    <div class="mb-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="font-bold text-2xl text-slate-900 mb-0 font-heading">Harvest Categories</h3>
                <span class="text-xs text-slate-500">Curated organic crop classifications</span>
            </div>
            <a href="{{ route('market.explorer') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 text-decoration-none">
                View All Categories <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-3">
            @foreach($categories as $cat)
                <div class="col-lg-2 col-md-4 col-6">
                    <a href="{{ route('market.explorer', ['category' => $cat->slug]) }}" class="category-card-modern group">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-2.5 group-hover:bg-emerald-600 group-hover:text-white transition duration-200">
                            <i class="fa-solid {{ $cat->icon ?? 'fa-leaf' }} text-lg"></i>
                        </div>
                        <div class="font-bold text-sm text-slate-800 mb-1 group-hover:text-emerald-700 transition">{{ $cat->name }}</div>
                        <span class="text-[11px] font-semibold text-slate-400">{{ $cat->products_count }} Available</span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Zero-Waste Batch Pricing Section (Product Cards Upgrade: rounded-3xl, hover:shadow-xl, clean prices) -->
    <div class="mb-12" id="zero-waste">
        <div class="bg-gradient-to-r from-amber-50/80 via-white to-amber-50/50 border border-amber-200/70 rounded-3xl p-6 sm:p-8">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-6">
                <div>
                    <div class="d-inline-flex align-items-center gap-2 mb-1.5">
                        <span class="badge-flash-cut">
                            <i class="fa-solid fa-bolt"></i> Auto-Discount Engine
                        </span>
                        <span class="text-xs text-amber-800 font-semibold">Closing Window Surplus Baskets</span>
                    </div>
                    <h3 class="text-2xl font-extrabold text-slate-900 mb-0 font-heading">Zero-Waste Harvest Bundles</h3>
                    <p class="text-xs text-slate-500 mb-0">Unsold morning harvest crates automatically marked down to prevent food waste.</p>
                </div>
                <div>
                    <span class="text-xs font-bold text-amber-800 bg-amber-100/80 px-3 py-1.5 rounded-full border border-amber-200">
                        <i class="fa-solid fa-clock me-1"></i> Saturday Pickup Reserve
                    </span>
                </div>
            </div>

            <!-- Upgraded Modern Product Cards Grid -->
            <div class="row g-4">
                @forelse($zeroWasteProducts as $item)
                    <div class="col-lg-4 col-md-6">
                        <div class="product-card-modern group h-100">
                            <!-- Image Container with Hover Scale & Styled Badges -->
                            <div class="product-image-container">
                                <img src="{{ $item->image_url ?? 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $item->name }}" loading="lazy">
                                <div class="absolute top-3 left-3 d-flex flex-column gap-1.5 z-10">
                                    <span class="badge-flash-cut">
                                        {{ $item->discount_percent }}% Off Bundle
                                    </span>
                                    <!-- Formatted Dynamic Label (No Raw Decimals!) -->
                                    <span class="badge-harvest-pill">
                                        <i class="fa-solid fa-clock text-amber-600"></i> {{ $item->harvest_label }}
                                    </span>
                                </div>
                                <div class="absolute top-3 right-3 z-10">
                                    <span class="badge-organic">
                                        <i class="fa-solid fa-leaf text-xs"></i> Organic
                                    </span>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-4 d-flex flex-column flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-xs font-semibold text-slate-500 truncate max-w-[180px]">{{ $item->farmerProfile->stall_name }}</span>
                                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                                        -{{ number_format($item->co2_savings_kg, 1) }} kg CO2
                                    </span>
                                </div>

                                <h5 class="font-bold text-base text-slate-900 mb-1 group-hover:text-emerald-700 transition">
                                    <a href="{{ route('product.show', $item->slug) }}" class="text-decoration-none text-slate-900 group-hover:text-emerald-700">
                                        {{ $item->name }}
                                    </a>
                                </h5>

                                <p class="text-xs text-slate-500 mb-4 flex-grow-1 line-clamp-2 leading-relaxed">
                                    {{ Str::limit($item->description, 80) }}
                                </p>

                                <!-- Clean Price Tag & Interactive Add Button -->
                                <div class="d-flex justify-content-between align-items-center pt-3 border-t border-slate-100 mt-auto">
                                    <div>
                                        <span class="price-strikethrough">${{ number_format($item->price, 2) }}</span>
                                        <span class="price-display price-flash">${{ number_format($item->effective_price, 2) }}</span>
                                        <span class="text-xs text-slate-400">/ {{ $item->unit }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1.5">
                                        <input type="number" id="qty-{{ $item->id }}" value="1" min="1" max="{{ $item->stock_quantity }}" class="form-control form-control-sm text-center font-bold text-xs rounded-xl" style="width: 48px; height: 34px;">
                                        <button class="btn-add-modern btn-add-to-basket" data-product-id="{{ $item->id }}" title="Add to basket">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                            <span>Add</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-8 text-slate-500 text-sm">
                        All zero-waste flash bundles for current market session reserved.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Peak Morning Harvests (Clean Modern Cards with rounded-3xl & formatted dynamic labels) -->
    <div class="mb-12">
        <div class="d-flex justify-content-between align-items-center mb-6">
            <div>
                <h3 class="text-2xl font-extrabold text-slate-900 mb-0 font-heading">Peak Morning Harvests</h3>
                <span class="text-xs text-slate-500">Picked at dawn from certified regional soil</span>
            </div>
            <a href="{{ route('market.explorer') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 text-decoration-none">
                All Harvest Listings <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($freshProducts as $product)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="product-card-modern group h-100">
                        <div class="product-image-container">
                            <img src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1598170845058-32b9d6a5c317?auto=format&fit=crop&w=600&q=80' }}" alt="{{ $product->name }}" loading="lazy">
                            <div class="absolute top-3 left-3 d-flex flex-column gap-1.5 z-10">
                                @if($product->badge_label)
                                    <span class="badge-organic bg-slate-900 text-white border-0">
                                        {{ $product->badge_label }}
                                    </span>
                                @else
                                    <span class="badge-organic">
                                        <i class="fa-solid fa-check text-xs"></i> Verified
                                    </span>
                                @endif
                                <!-- Formatted Dynamic Label (No Raw Decimals!) -->
                                <span class="badge-harvest-pill">
                                    <i class="fa-solid fa-scissors text-emerald-600"></i> {{ $product->harvest_label }}
                                </span>
                            </div>
                        </div>

                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <a href="{{ route('farmer.show', $product->farmerProfile->slug) }}" class="text-xs font-semibold text-slate-500 hover:text-emerald-700 text-decoration-none truncate max-w-[160px]">
                                    {{ $product->farmerProfile->stall_name }}
                                </a>
                                <span class="text-[11px] font-bold text-slate-400">{{ $product->farmerProfile->stall_number }}</span>
                            </div>

                            <h5 class="font-bold text-base text-slate-900 mb-1 group-hover:text-emerald-700 transition">
                                <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none text-slate-900 group-hover:text-emerald-700">
                                    {{ $product->name }}
                                </a>
                            </h5>

                            <p class="text-xs text-slate-500 mb-3 flex-grow-1 line-clamp-2 leading-relaxed">
                                {{ Str::limit($product->harvest_notes ?? $product->description, 75) }}
                            </p>

                            <div class="mb-3">
                                <span class="badge-co2-pill">
                                    <i class="fa-solid fa-tree text-emerald-600 text-xs"></i> {{ number_format($product->co2_savings_kg, 1) }} kg CO2 Offset
                                </span>
                            </div>

                            <!-- Clean Price & Button -->
                            <div class="d-flex justify-content-between align-items-center pt-3 border-t border-slate-100 mt-auto">
                                <div>
                                    <span class="price-display">${{ number_format($product->price, 2) }}</span>
                                    <span class="text-xs text-slate-400">/ {{ $product->unit }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-1.5">
                                    <input type="number" id="qty-{{ $product->id }}" value="1" min="1" max="{{ $product->stock_quantity }}" class="form-control form-control-sm text-center font-bold text-xs rounded-xl" style="width: 48px; height: 34px;">
                                    <button class="btn-add-modern btn-add-to-basket" data-product-id="{{ $product->id }}" title="Add to basket">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                        <span>Add</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Verified Buyer Feedback Strip (Modern Card Layout) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm mb-8">
        <div class="d-flex justify-content-between align-items-center mb-6">
            <div>
                <h4 class="font-bold text-xl text-slate-900 mb-0 font-heading">Verified Stall Reviews</h4>
                <span class="text-xs text-slate-500">Authenticated feedback tethered directly to settled pickup tokens</span>
            </div>
            <span class="badge-organic">
                <i class="fa-solid fa-shield-check text-emerald-600"></i> Cryptographically Verified
            </span>
        </div>

        <div class="row g-4">
            @foreach($recentReviews as $rev)
                <div class="col-lg-4 col-md-6">
                    <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-200/70 h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2.5">
                            <span class="badge-organic text-[11px]">
                                <i class="fa-solid fa-check text-emerald-600"></i> Verified Buyer
                            </span>
                            <div class="text-amber-400 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $rev->rating ? '' : 'text-slate-200' }}"></i>
                                @endfor
                            </div>
                        </div>

                        <p class="text-xs text-slate-700 leading-relaxed mb-4 flex-grow-1">
                            "{{ $rev->comment }}"
                        </p>

                        <div class="pt-3 border-t border-slate-200/60 d-flex justify-content-between align-items-end text-xs">
                            <div>
                                <strong class="text-slate-900 d-block">{{ $rev->customer->name }}</strong>
                                <span class="text-slate-500 text-[11px]">{{ $rev->farmerProfile->stall_name }}</span>
                            </div>
                            <span class="text-slate-400 text-[11px]">{{ $rev->created_at->diffForHumans() }}</span>
                        </div>

                        @if($rev->farmer_reply)
                            <div class="mt-3 p-2.5 rounded-xl bg-white border border-slate-200/60 text-xs">
                                <div class="font-bold text-emerald-700 text-[11px] mb-1">
                                    <i class="fa-solid fa-reply me-1"></i> Farmer Response:
                                </div>
                                <div class="text-slate-600 text-[11px]">{{ $rev->farmer_reply }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

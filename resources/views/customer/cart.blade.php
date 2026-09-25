@extends('layouts.app')

@section('title', 'Market Basket | Pre-Order Summary')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Market Basket</h2>
            <p class="text-muted small mb-0">Harvests grouped by producer stall for consolidated Saturday market pickup</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-forest">
            <i class="fa-solid fa-arrow-left me-1"></i> Add More Produce
        </a>
    </div>

    @if(empty($itemsByFarmer))
        <div class="ml-card p-5 text-center">
            <i class="fa-solid fa-basket-shopping fa-3x text-muted mb-3 opacity-50"></i>
            <h4 class="fw-bold mb-2">Your Market Basket is Empty</h4>
            <p class="text-muted small mb-4">Browse fresh morning harvests or explore the interactive market map to reserve produce.</p>
            <div>
                <a href="{{ route('home') }}" class="btn btn-forest px-4 py-2 me-2">Browse Marketplace</a>
                <a href="{{ route('market.explorer') }}" class="btn btn-sage px-4 py-2">Explore Market Map</a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <!-- Basket Items Grouped by Farmer -->
            <div class="col-lg-8">
                @foreach($itemsByFarmer as $farmerId => $group)
                    <div class="ml-card mb-4">
                        <div class="ml-card-header d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-dark">{{ $group['farmer']->stall_name }}</strong>
                                <span class="badge bg-light text-muted border ms-2">{{ $group['farmer']->stall_number }}</span>
                            </div>
                            <span class="small text-muted">
                                {{ $group['farmer']->market ? $group['farmer']->market->name : 'Central Venue' }}
                            </span>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light small">
                                    <tr>
                                        <th style="width: 45%;">Produce</th>
                                        <th>Unit Price</th>
                                        <th style="width: 15%;">Quantity</th>
                                        <th>Subtotal</th>
                                        <th style="width: 10%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($group['items'] as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    @if($item['product']->image_url)
                                                        <img src="{{ $item['product']->image_url }}" alt="" class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <a href="{{ route('product.show', $item['product']->slug) }}" class="fw-semibold text-dark d-block">
                                                            {{ $item['product']->name }}
                                                        </a>
                                                        <div class="d-flex gap-2">
                                                            @if($item['is_zero_waste'])
                                                                <span class="badge-zero-waste" style="font-size: 0.65rem;">Zero-Waste Cut</span>
                                                            @endif
                                                            <span class="text-muted" style="font-size: 0.75rem;">{{ $item['product']->harvest_status_string }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                ${{ number_format($item['unit_price'], 2) }} <span class="small text-muted">/ {{ $item['product']->unit }}</span>
                                            </td>
                                            <td>
                                                <input type="number" value="{{ $item['quantity'] }}" min="1" max="{{ $item['product']->stock_quantity }}" class="form-control form-control-sm text-center cart-qty-input" data-product-id="{{ $item['product']->id }}">
                                            </td>
                                            <td class="fw-bold text-dark">
                                                ${{ number_format($item['subtotal'], 2) }}
                                            </td>
                                            <td class="text-end">
                                                <form action="{{ route('customer.cart.remove', $item['product']->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Remove produce">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="p-3 bg-light d-flex justify-content-between align-items-center border-top">
                            <span class="small text-success fw-medium">
                                <i class="fa-solid fa-leaf me-1"></i> Prevents {{ number_format($group['co2'], 2) }} kg CO2 emissions
                            </span>
                            <span class="fw-bold">
                                Stall Subtotal: ${{ number_format($group['subtotal'], 2) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Order Summary Card -->
            <div class="col-lg-4">
                <div class="ml-card p-4">
                    <h5 class="fw-bold mb-3">Pre-Order Reservation</h5>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Total Produce Items:</span>
                        <strong class="text-dark">{{ array_sum(array_column(session('basket', []), 'quantity')) }} items</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Participating Stalls:</span>
                        <strong class="text-dark">{{ count($itemsByFarmer) }} Stalls</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-3 small">
                        <span class="text-muted">Estimated Transport CO2 Saved:</span>
                        <strong class="text-success">{{ number_format($totalCo2Saved, 2) }} kg CO2</strong>
                    </div>

                    <div class="p-3 rounded mb-3" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
                        <div class="small text-muted mb-1">Settlement Method:</div>
                        <div class="fw-bold text-dark">
                            <i class="fa-solid fa-money-bill-wave text-success me-1"></i> Cash on Pickup
                        </div>
                        <div class="text-muted" style="font-size: 0.78rem;">
                            Direct payment to growers upon QR token presentation at stalls.
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between align-items-baseline mb-4">
                        <span class="fs-5 fw-bold">Total Estimated:</span>
                        <span class="price-tag fs-3">${{ number_format($totalBasketAmount, 2) }}</span>
                    </div>

                    <a href="{{ route('customer.checkout') }}" class="btn btn-forest w-100 py-2">
                        Proceed to Pickup Windows <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.cart-qty-input').forEach(input => {
        input.addEventListener('change', function () {
            const productId = this.dataset.productId;
            const quantity = parseInt(this.value);

            fetch('/basket/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Exceeds stall inventory.');
                    location.reload();
                }
            });
        });
    });
</script>
@endpush

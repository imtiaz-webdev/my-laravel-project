@extends('layouts.app')

@section('title', 'Terms of Service | MarketLink Platform')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="ml-card p-5 bg-white">
                <div class="mb-4 pb-3 border-bottom">
                    <span class="badge-co2 mb-2">Legal Governance Framework</span>
                    <h1 class="fw-bold mb-2">Terms of Service & Farmer Market Operating Agreement</h1>
                    <div class="small text-muted">Effective Date: September 2026 &bull; TechWiz 7 eGreen Basket Edition</div>
                </div>

                <div class="legal-body text-dark" style="line-height: 1.7; font-size: 0.95rem;">
                    <h5 class="fw-bold text-success mb-2">1. Scope and Acceptance of Platform Service</h5>
                    <p class="text-muted mb-4">
                        MarketLink operates as an agroecological software utility facilitating direct farm-to-consumer reservations, zero-waste produce bundle allocations, and in-person market collection routing. By creating a Customer or Vendor account, you explicitly covenant to abide by these terms, municipal farmers market codes, and regional organic verification protocols.
                    </p>

                    <h5 class="fw-bold text-success mb-2">2. Producer Obligations and Verification Standards</h5>
                    <p class="text-muted mb-4">
                        All registered growers attest that produce listed on the Stock Matrix is harvested in accordance with declared standards (CCOF Certified Organic, Certified Naturally Grown, or verified pesticide-free regenerative cultivation). Farmers are strictly required to honor pre-order allocations packed during designated market pickup windows.
                    </p>

                    <h5 class="fw-bold text-success mb-2">3. Zero-Waste Batch Pricing Protocol</h5>
                    <p class="text-muted mb-4">
                        The Zero-Waste Batch Engine automatically adjusts item valuations to prevent farm discards. When an item is marked as a zero-waste bundle, the customer agrees to collect the produce during the specified morning pickup session. Uncollected orders impact customer priority standing for subsequent harvest cycles.
                    </p>

                    <h5 class="fw-bold text-success mb-2">4. Direct Cash Settlement and Token Verification</h5>
                    <p class="text-muted mb-4">
                        MarketLink does not hold consumer funds or levy transaction fees on farm sales. 100% of the agreed order sum is settled directly in cash between the consumer and the producer stall upon verification of the encrypted QR token pass or 4-digit numeric code.
                    </p>

                    <h5 class="fw-bold text-success mb-2">5. Carbon Footprint and Ecological Metrics Representation</h5>
                    <p class="text-muted mb-4">
                        Displayed CO2 savings scores represent comparative mathematical offsets calculated against conventional long-haul refrigerated logistics models. These metrics are compiled to promote regional food sovereignty and conscious purchasing decisions.
                    </p>

                    <h5 class="fw-bold text-success mb-2">6. Limitation of Liability and Quality Dispute Resolution</h5>
                    <p class="text-muted mb-0">
                        In the event of weather cancellations or harvest failures, growers must notify customers promptly through the fulfillment portal. Any produce quality adjustments are resolved directly at the physical market stall prior to cash exchange.
                    </p>
                </div>

                <div class="mt-5 pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="small text-muted">&copy; {{ date('Y') }} MarketLink Directorate. All Rights Reserved.</span>
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-forest">Return to Marketplace</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

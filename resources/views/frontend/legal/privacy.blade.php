@extends('layouts.app')

@section('title', 'Privacy Policy & Data Stewardship | MarketLink')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="ml-card p-5 bg-white">
                <div class="mb-4 pb-3 border-bottom">
                    <span class="badge-co2 mb-2">Consumer Data Integrity</span>
                    <h1 class="fw-bold mb-2">Privacy Policy & Telemetry Governance</h1>
                    <div class="small text-muted">Version 2026.1 &bull; Updated for TechWiz 7 Standards</div>
                </div>

                <div class="legal-body text-dark" style="line-height: 1.7; font-size: 0.95rem;">
                    <h5 class="fw-bold text-success mb-2">1. Data Minimization Principles</h5>
                    <p class="text-muted mb-4">
                        MarketLink collects only essential operational telemetry needed to coordinate physical stall pickups. We do not store financial payment tokens, credit card numbers, or biometric credentials, because 100% of marketplace payments occur in person through cash settlement.
                    </p>

                    <h5 class="fw-bold text-success mb-2">2. Geolocation and Map Coordinates Usage</h5>
                    <p class="text-muted mb-4">
                        Marketplace map routing utilizes OpenStreetMap and open-source Leaflet.js libraries. GPS coordinates of farmer stalls are published solely to assist pedestrian navigation within public market pavilions. User real-time locations are never transmitted to third-party ad networks or tracking trackers.
                    </p>

                    <h5 class="fw-bold text-success mb-2">3. Cryptographic QR Verification Hashes</h5>
                    <p class="text-muted mb-4">
                        QR tokens generated upon checkout are hashed using non-reversible cryptographic salts. These tokens serve exclusively as single-use confirmation vouchers between the buyer and the farmer stall desk.
                    </p>

                    <h5 class="fw-bold text-success mb-2">4. Review Authentication and Verified Badging</h5>
                    <p class="text-muted mb-4">
                        To eradicate fraudulent testimonials, customer reviews are mathematically tethered to authenticated, settled orders. Customer names displayed alongside reviews reflect verified profile names to maintain community trust.
                    </p>

                    <h5 class="fw-bold text-success mb-2">5. Data Retention and Erasure Rights</h5>
                    <p class="text-muted mb-0">
                        Users may request account deactivation or transaction ledger anonymization at any time by contacting the Market Directorate. Historical carbon offset calculations are preserved in aggregate to maintain regional sustainability reporting benchmarks.
                    </p>
                </div>

                <div class="mt-5 pt-3 border-top d-flex justify-content-between align-items-center">
                    <span class="small text-muted">&copy; {{ date('Y') }} MarketLink Directorate. Data Privacy Office.</span>
                    <a href="{{ route('home') }}" class="btn btn-sm btn-outline-forest">Return to Marketplace</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

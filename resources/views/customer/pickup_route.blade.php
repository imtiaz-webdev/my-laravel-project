@extends('layouts.app')

@section('title', 'Market Stall Pickup Route Optimizer | Leaflet.js')

@section('content')
<div class="container py-4">

    <!-- Header & Concept Notice -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <div class="d-inline-flex align-items-center gap-2 mb-1">
                <span class="badge bg-dark text-white small" style="font-size: 0.72rem;">TechWiz Innovation</span>
                <span class="badge-co2">Multi-Stall Trajectory</span>
            </div>
            <h2 class="mb-0 fw-bold">Market Stall Pickup Route Optimizer</h2>
            <p class="text-muted small mb-0">Automated shortest-path pedestrian routing across your reserved farm stalls</p>
        </div>

        <div>
            <a href="{{ route('customer.dashboard') }}" class="btn btn-sm btn-outline-forest">
                <i class="fa-solid fa-arrow-left me-1"></i> Pre-Orders Hub
            </a>
        </div>
    </div>

    @if(!$activeMarket || empty($waypoints))
        <div class="ml-card p-5 text-center">
            <i class="fa-solid fa-route fa-3x text-muted mb-3 opacity-50"></i>
            <h4 class="fw-bold mb-2">No Multi-Stall Pre-Orders Detected</h4>
            <p class="text-muted small mb-4">Place pre-orders across two or more stalls within the same market to generate an automated walking itinerary.</p>
            <a href="{{ route('home') }}" class="btn btn-forest px-4 py-2">Browse Marketplace Stalls</a>
        </div>
    @else
        <div class="row g-4">
            <!-- Leaflet Interactive Route Map -->
            <div class="col-lg-8">
                <div class="ml-card p-2">
                    <div id="routeMap" style="height: 540px;"></div>
                    <div class="p-2 d-flex justify-content-between align-items-center small text-muted">
                        <span>
                            <i class="fa-solid fa-person-walking text-success me-1"></i> Optimized Pedestrian Circuit: {{ count($waypoints) }} Pickup Checkpoints
                        </span>
                        <span class="fw-semibold text-dark">
                            Venue: {{ $activeMarket->name }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Route Sequence Sidebar -->
            <div class="col-lg-4">
                <div class="ml-card p-3 h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                        <h6 class="fw-bold mb-0">Walking Circuit Itinerary</h6>
                        <span class="badge-harvest-time">Est. 4 min walking</span>
                    </div>

                    <div class="overflow-y-auto flex-grow-1 pe-1" style="max-height: 460px;">
                        <!-- Entrance Node -->
                        <div class="p-2 mb-2 rounded bg-light border small d-flex align-items-center gap-2">
                            <span class="badge bg-secondary rounded-circle px-2 py-1">0</span>
                            <div>
                                <strong class="text-dark">Market Entrance & Gate</strong>
                                <div class="text-muted" style="font-size: 0.75rem;">100 Civic Center Plaza Gateway</div>
                            </div>
                        </div>

                        <!-- Stall Sequence Checkpoints -->
                        @foreach($waypoints as $wp)
                            <div class="p-3 mb-2 rounded bg-white border ml-card-hover cursor-pointer" onclick="focusRoutePoint({{ $wp['lat'] }}, {{ $wp['lng'] }}, '{{ addslashes($wp['stall_name']) }}')">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-success rounded-pill px-2 py-1 small">
                                        Stop {{ $wp['step'] }}
                                    </span>
                                    <span class="badge bg-dark text-white small" style="font-size: 0.7rem;">{{ $wp['stall_number'] }}</span>
                                </div>

                                <h6 class="fw-bold mb-1 text-dark">{{ $wp['stall_name'] }}</h6>
                                <div class="small text-muted mb-2">Order #{{ $wp['order_number'] }} &bull; {{ $wp['items_count'] }} item(s)</div>

                                <div class="d-flex justify-content-between align-items-center pt-2 border-top small">
                                    <div>
                                        <span class="text-muted">Token:</span>
                                        <strong class="text-dark ms-1" style="font-family: monospace;">{{ $wp['token'] }}</strong>
                                    </div>
                                    <span class="fw-bold text-success">{{ $wp['amount'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="p-2 rounded bg-light mt-3 text-center small text-muted border">
                        <i class="fa-solid fa-shield-check text-success me-1"></i> Sequential tokens optimize market hall navigation and minimize dwell time.
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
@if(!empty($waypoints))
<script>
    let routeMap;
    const points = @json($waypoints);
    const market = @json($activeMarket);
    let routeMarkers = [];

    document.addEventListener('DOMContentLoaded', function () {
        const centerLat = market ? market.latitude : 37.7793;
        const centerLng = market ? market.longitude : -122.4192;

        routeMap = L.map('routeMap').setView([centerLat, centerLng], 17);

        // OpenStreetMap Standard Tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(routeMap);

        const latlngs = [];

        // Entrance Node
        const entranceLat = centerLat - 0.0004;
        const entranceLng = centerLng - 0.0004;
        latlngs.push([entranceLat, entranceLng]);

        const entranceIcon = L.divIcon({
            className: 'custom-farm-pin',
            html: '<i class="fa-solid fa-door-open"></i>',
            iconSize: [28, 28],
            iconAnchor: [14, 14]
        });
        L.marker([entranceLat, entranceLng], { icon: entranceIcon })
            .addTo(routeMap)
            .bindPopup('<strong>Market North Pedestrian Entrance</strong><br>Starting point of pickup circuit.');

        // Stall Waypoint Nodes
        points.forEach((p, index) => {
            latlngs.push([p.lat, p.lng]);

            const stepIcon = L.divIcon({
                className: 'custom-farm-pin route-step',
                html: `${p.step}`,
                iconSize: [32, 32],
                iconAnchor: [16, 16]
            });

            const marker = L.marker([p.lat, p.lng], { icon: stepIcon }).addTo(routeMap);
            marker.bindPopup(`
                <div style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    <strong style="color: #1b4332;">Stop ${p.step}: ${p.stall_name}</strong>
                    <div style="font-size: 11px; color: #555;">Stall: ${p.stall_number}</div>
                    <div style="font-size: 11px; margin-top: 4px;">Pickup Token: <strong>${p.token}</strong></div>
                    <div style="font-size: 11px; color: #1b4332; font-weight: 600;">Settle: ${p.amount}</div>
                </div>
            `);

            routeMarkers.push({ lat: p.lat, lng: p.lng, marker: marker });
        });

        // Draw Polyline Walking Route
        const routePolyline = L.polyline(latlngs, {
            color: '#1b4332',
            weight: 4,
            opacity: 0.85,
            dashArray: '8, 8',
            lineJoin: 'round'
        }).addTo(routeMap);

        routeMap.fitBounds(routePolyline.getBounds(), { padding: [50, 50] });
    });

    function focusRoutePoint(lat, lng, name) {
        if (!routeMap) return;
        routeMap.setView([lat, lng], 18, { animate: true });
        const target = routeMarkers.find(m => Math.abs(m.lat - lat) < 0.0001 && Math.abs(m.lng - lng) < 0.0001);
        if (target) {
            target.marker.openPopup();
        }
    }
</script>
@endif
@endpush

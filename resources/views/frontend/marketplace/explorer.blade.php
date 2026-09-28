@extends('layouts.app')

@section('title', 'Live OpenStreetMap Farm & Stall Explorer')

@section('content')
<div class="container-fluid px-lg-5 py-4">

    <!-- Header & Filter Form -->
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-5">
            <h2 class="mb-1">Live Farmers Market Explorer</h2>
            <p class="text-muted small mb-0">OpenStreetMap GPS telemetry of regional stalls, operating schedules, and harvest availability</p>
        </div>

        <div class="col-md-7">
            <form action="{{ route('market.explorer') }}" method="GET" class="row g-2 justify-content-md-end">
                <div class="col-sm-4">
                    <select name="market_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Market Venues</option>
                        @foreach($allMarkets as $m)
                            <option value="{{ $m->id }}" {{ $selectedMarketId == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-4">
                    <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">All Produce Types</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->slug }}" {{ $selectedCategory == $c->slug ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-3">
                    <select name="day" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Any Day</option>
                        <option value="Wednesday" {{ $marketDay == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                        <option value="Thursday" {{ $marketDay == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                        <option value="Saturday" {{ $marketDay == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                        <option value="Sunday" {{ $marketDay == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                    </select>
                </div>

                <div class="col-sm-1">
                    <a href="{{ route('market.explorer') }}" class="btn btn-sm btn-outline-secondary w-100" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Map & Stall List Split View -->
    <div class="row g-4">
        <!-- Interactive Leaflet Map -->
        <div class="col-lg-8">
            <div class="ml-card p-2">
                <div id="marketMap" style="height: 580px;"></div>
                <div class="p-2 d-flex justify-content-between align-items-center small text-muted">
                    <span>
                        <i class="fa-solid fa-location-crosshairs text-success me-1"></i> OpenStreetMap Cartography (Leaflet Engine)
                    </span>
                    <span>
                        <span class="badge bg-success me-1">{{ count($stallsData) }}</span> Stalls Plotted
                    </span>
                </div>
            </div>
        </div>

        <!-- Stalls Side Panel -->
        <div class="col-lg-4">
            <div class="ml-card h-100 d-flex flex-column" style="max-height: 620px;">
                <div class="ml-card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Active Producer Stalls</h6>
                    <span class="badge bg-light text-dark border">{{ count($stallsData) }} Found</span>
                </div>

                <div class="p-3 overflow-y-auto flex-grow-1" style="background-color: var(--color-bg-warm);">
                    @forelse($stallsData as $stall)
                        <div class="ml-card p-3 mb-3 bg-white ml-card-hover cursor-pointer" onclick="zoomToStall({{ $stall['lat'] }}, {{ $stall['lng'] }}, '{{ addslashes($stall['stall_name']) }}')">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="badge bg-dark text-white small" style="font-size: 0.7rem;">{{ $stall['stall_number'] }}</span>
                                <span class="badge-co2">
                                    <i class="fa-solid fa-leaf"></i> {{ $stall['carbon_rating'] }} Rating
                                </span>
                            </div>

                            <h6 class="fw-bold mb-1 text-dark">{{ $stall['stall_name'] }}</h6>
                            <div class="small text-muted mb-2">Venue: {{ $stall['market_name'] }}</div>

                            <p class="small text-muted mb-2" style="line-height: 1.4;">
                                {{ Str::limit($stall['bio'], 75) }}
                            </p>

                            @if($stall['certifications'])
                                <div class="mb-2">
                                    <span class="badge bg-light text-success border small" style="font-size: 0.68rem;">
                                        <i class="fa-solid fa-certificate me-1"></i> {{ $stall['certifications'] }}
                                    </span>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span class="small text-muted">{{ $stall['products_count'] }} Harvest Items</span>
                                <a href="{{ $stall['view_url'] }}" class="btn btn-sm btn-forest py-1 px-2" style="font-size: 0.78rem;">
                                    View Stall
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-filter fa-2x mb-2 opacity-50"></i>
                            <p>No farmer stalls match selected filter criteria.</p>
                            <a href="{{ route('market.explorer') }}" class="btn btn-sm btn-outline-forest">Reset All Filters</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let map;
    let markers = [];
    const stalls = @json($stallsData);

    document.addEventListener('DOMContentLoaded', function () {
        // Default coordinates: San Francisco Civic Center
        const defaultLat = stalls.length > 0 ? stalls[0].lat : 37.7793;
        const defaultLng = stalls.length > 0 ? stalls[0].lng : -122.4192;

        map = L.map('marketMap').setView([defaultLat, defaultLng], 13);

        // OpenStreetMap Free Tile Layer (No paid keys required)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Plot custom markers
        const bounds = [];

        stalls.forEach(s => {
            const customIcon = L.divIcon({
                className: 'custom-farm-pin',
                html: '<i class="fa-solid fa-seedling"></i>',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32]
            });

            const marker = L.marker([s.lat, s.lng], { icon: customIcon }).addTo(map);

            const popupHtml = `
                <div style="min-width: 220px; font-family: 'Plus Jakarta Sans', sans-serif;">
                    <div style="font-weight: 700; color: #1b4332; font-size: 14px; margin-bottom: 2px;">${s.stall_name}</div>
                    <div style="font-size: 11px; color: #666; margin-bottom: 6px;">${s.market_name} &bull; <strong>${s.stall_number}</strong></div>
                    <div style="font-size: 12px; color: #333; margin-bottom: 8px;">${s.bio}</div>
                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eee; padding-top: 6px;">
                        <span style="font-size: 11px; color: #1b4332; font-weight: 600;">Carbon Score: ${s.carbon_rating}/5.0</span>
                        <a href="${s.view_url}" style="background-color: #1b4332; color: #fff; padding: 3px 8px; border-radius: 4px; font-size: 11px; text-decoration: none;">Pre-Order</a>
                    </div>
                </div>
            `;

            marker.bindPopup(popupHtml);
            markers.push({ id: s.id, marker: marker, lat: s.lat, lng: s.lng });
            bounds.push([s.lat, s.lng]);
        });

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [40, 40] });
        }
    });

    function zoomToStall(lat, lng, name) {
        if (!map) return;
        map.setView([lat, lng], 16, { animate: true });
        const target = markers.find(m => Math.abs(m.lat - lat) < 0.0001 && Math.abs(m.lng - lng) < 0.0001);
        if (target) {
            target.marker.openPopup();
        }
    }
</script>
@endpush

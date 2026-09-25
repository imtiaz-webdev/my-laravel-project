@extends('layouts.app')

@section('title', 'Market Location Manager | MarketLink Admin')

@section('content')
<div class="container py-4">

    <!-- Header & Action Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1">Market Location Manager</h2>
            <p class="text-muted small mb-0">Configure physical farmers market pavilions, operating calendar schedules, and GPS coordinates</p>
        </div>

        <button type="button" class="btn btn-sm btn-forest" data-bs-toggle="modal" data-bs-target="#addMarketModal">
            <i class="fa-solid fa-plus me-1"></i> Add Physical Market Venue
        </button>
    </div>

    <!-- Markets Grid -->
    <div class="row g-4 mb-4">
        @foreach($markets as $m)
            <div class="col-lg-4 col-md-6">
                <div class="ml-card p-4 h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge {{ $m->status === 'active' ? 'bg-success' : 'bg-secondary' }} text-white text-capitalize">
                            {{ $m->status }} Venue
                        </span>
                        <span class="badge bg-light text-dark border small">Capacity: {{ $m->stall_capacity }} Stalls</span>
                    </div>

                    <h5 class="fw-bold mb-1 text-dark">{{ $m->name }}</h5>
                    <p class="text-muted small mb-3">{{ $m->address }}, {{ $m->city }}</p>

                    <div class="p-3 rounded bg-light mb-3 flex-grow-1 small">
                        <div class="mb-1">
                            <i class="fa-solid fa-calendar-days text-success me-1"></i>
                            <strong>Days:</strong> {{ $m->operating_days }}
                        </div>
                        <div class="mb-1">
                            <i class="fa-solid fa-clock text-warning me-1"></i>
                            <strong>Timing:</strong> {{ $m->timing }}
                        </div>
                        <div>
                            <i class="fa-solid fa-location-crosshairs text-primary me-1"></i>
                            <strong>GPS:</strong> {{ number_format($m->latitude, 4) }}, {{ number_format($m->longitude, 4) }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="small text-muted">{{ $m->farmer_profiles_count }} Stalls Hosted</span>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-outline-secondary py-1 px-2" data-bs-toggle="modal" data-bs-target="#editMarketModal-{{ $m->id }}">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </button>
                            <form action="{{ route('admin.market.delete', $m->id) }}" method="POST" onsubmit="return confirm('Archive {{ addslashes($m->name) }}?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Market Modal -->
            <div class="modal fade" id="editMarketModal-{{ $m->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('admin.market.update', $m->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Edit Market Venue: {{ $m->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label small fw-semibold">Market Name</label>
                                        <input type="text" name="name" value="{{ $m->name }}" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Venue Status</label>
                                        <select name="status" class="form-select form-select-sm" required>
                                            <option value="active" {{ $m->status === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="seasonal" {{ $m->status === 'seasonal' ? 'selected' : '' }}>Seasonal</option>
                                            <option value="inactive" {{ $m->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label small fw-semibold">Physical Street Address</label>
                                        <input type="text" name="address" value="{{ $m->address }}" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">City</label>
                                        <input type="text" name="city" value="{{ $m->city }}" class="form-control form-control-sm" required>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Operating Days</label>
                                        <input type="text" name="operating_days" value="{{ $m->operating_days }}" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Operating Timing</label>
                                        <input type="text" name="timing" value="{{ $m->timing }}" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold">Stall Capacity</label>
                                        <input type="number" name="stall_capacity" value="{{ $m->stall_capacity }}" class="form-control form-control-sm" required>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Latitude</label>
                                        <input type="text" name="latitude" value="{{ $m->latitude }}" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Longitude</label>
                                        <input type="text" name="longitude" value="{{ $m->longitude }}" class="form-control form-control-sm" required>
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label small fw-semibold">Description</label>
                                    <textarea name="description" rows="2" class="form-control form-control-sm">{{ $m->description }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-forest">Save Venue Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- Add Market Modal -->
<div class="modal fade" id="addMarketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.market.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Establish New Physical Market Venue</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Market Name</label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Sunset District Coastal Market" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Stall Capacity</label>
                            <input type="number" name="stall_capacity" value="30" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Physical Street Address</label>
                            <input type="text" name="address" class="form-control form-control-sm" placeholder="1990 Irving Street" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">City</label>
                            <input type="text" name="city" value="San Francisco" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Operating Days</label>
                            <input type="text" name="operating_days" value="Sunday" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Operating Timing</label>
                            <input type="text" name="timing" value="09:00 AM - 02:00 PM" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Latitude</label>
                            <input type="text" name="latitude" value="37.7635" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Longitude</label>
                            <input type="text" name="longitude" value="-122.4789" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small fw-semibold">Venue Overview</label>
                        <textarea name="description" rows="2" class="form-control form-control-sm">Ocean-adjacent open pavilion hosting local organic produce and zero-waste bulk grains.</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-forest">Establish Venue</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

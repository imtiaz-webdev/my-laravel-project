@extends('layouts.app')

@section('title', 'Stall Profile Settings | ' . $farmer->stall_name)

@section('content')
<div class="container py-4">

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="ml-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">Producer Stall Profile</h3>
                        <p class="text-muted small mb-0">Update farm provenance, stall identifiers, and geographical coordinates</p>
                    </div>
                    <span class="badge bg-light text-success border">
                        <i class="fa-solid fa-circle-check me-1"></i> Active Status
                    </span>
                </div>

                <form action="{{ route('vendor.profile.update') }}" method="POST">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Stall / Farm Name</label>
                            <input type="text" name="stall_name" value="{{ old('stall_name', $farmer->stall_name) }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Primary Market Venue</label>
                            <select name="market_id" class="form-select form-select-sm" required>
                                @foreach($markets as $m)
                                    <option value="{{ $m->id }}" {{ $farmer->market_id == $m->id ? 'selected' : '' }}>
                                        {{ $m->name }} ({{ $m->city }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Stall Number / Identifier</label>
                            <input type="text" name="stall_number" value="{{ old('stall_number', $farmer->stall_number) }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Farm Origin Distance / Region</label>
                            <input type="text" name="farm_origin" value="{{ old('farm_origin', $farmer->farm_origin) }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Phone Contact</label>
                            <input type="text" name="phone_contact" value="{{ old('phone_contact', $farmer->phone_contact) }}" class="form-control form-control-sm" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Stall Latitude (GPS)</label>
                            <input type="text" name="lat" value="{{ old('lat', $farmer->lat) }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Stall Longitude (GPS)</label>
                            <input type="text" name="long" value="{{ old('long', $farmer->long) }}" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Agricultural Certifications</label>
                        <input type="text" name="certifications" value="{{ old('certifications', $farmer->certifications) }}" class="form-control form-control-sm" placeholder="e.g. CCOF Certified Organic, Regenerative Soil Alliance">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Farm Bio & Ecological Stewardship Summary</label>
                        <textarea name="bio" rows="4" class="form-control form-control-sm">{{ old('bio', $farmer->bio) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-forest px-4 py-2">
                        Save Profile Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

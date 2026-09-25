@extends('layouts.app')

@section('title', 'Register | MarketLink')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="ml-card p-4">
                <div class="text-center mb-4">
                    <div class="ml-brand justify-content-center mb-2">
                        <i class="fa-solid fa-basket-shopping text-success"></i>
                        <span>MarketLink</span>
                    </div>
                    <h4 class="fw-bold mb-1">Create MarketLink Account</h4>
                    <p class="text-muted small mb-0">Join as an eco-conscious consumer or enroll your certified farm stall</p>
                </div>

                <form action="{{ route('register.post') }}" method="POST" id="registerForm">
                    @csrf

                    <!-- Role Switcher -->
                    <div class="mb-4">
                        <label class="form-label small fw-semibold">Select Account Role</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="role" id="roleCustomer" value="customer" {{ old('role', 'customer') == 'customer' ? 'checked' : '' }} onchange="toggleVendorFields()">
                                <label class="btn btn-outline-forest w-100 py-2" for="roleCustomer">
                                    <i class="fa-solid fa-user me-1"></i> Customer
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="role" id="roleVendor" value="vendor" {{ old('role') == 'vendor' ? 'checked' : '' }} onchange="toggleVendorFields()">
                                <label class="btn btn-outline-forest w-100 py-2" for="roleVendor">
                                    <i class="fa-solid fa-tractor me-1"></i> Farmer / Vendor
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Full Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required placeholder="Elena Rostova">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email Address</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required placeholder="elena@farm.org">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Phone Contact</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" required placeholder="+1 (555) 000-0000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Physical Address / Farm Location</label>
                            <input type="text" name="address" value="{{ old('address') }}" class="form-control" required placeholder="Street address, City">
                        </div>
                    </div>

                    <!-- Vendor Specific Section -->
                    <div id="vendorFields" class="p-3 rounded mb-3 border {{ old('role') == 'vendor' ? '' : 'd-none' }}" style="background-color: #f7f9f7; border-color: #cce5d4 !important;">
                        <h6 class="fw-bold text-success mb-3">
                            <i class="fa-solid fa-store me-1"></i> Farmer Stall Credentials
                        </h6>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Stall / Farm Name</label>
                                <input type="text" name="stall_name" id="stall_name" value="{{ old('stall_name') }}" class="form-control" placeholder="Verdant Meadow Micro-Farm">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Primary Market Venue</label>
                                <select name="market_id" id="market_id" class="form-select">
                                    <option value="">Select market venue...</option>
                                    @foreach($markets as $m)
                                        <option value="{{ $m->id }}" {{ old('market_id') == $m->id ? 'selected' : '' }}>
                                            {{ $m->name }} ({{ $m->city }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Agricultural Certifications</label>
                            <input type="text" name="certifications" value="{{ old('certifications') }}" class="form-control" placeholder="e.g. CCOF Organic, Regenerative Soil Alliance">
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-semibold">Farm Biography & Growing Methods</label>
                            <textarea name="bio" rows="2" class="form-control" placeholder="Describe your harvesting techniques, soil health practices, and crop specialties...">{{ old('bio') }}</textarea>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Minimum 6 characters">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="Repeat password">
                        </div>
                    </div>

                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="termsCheck" required checked>
                        <label class="form-check-label small text-muted" for="termsCheck">
                            I agree to the <a href="{{ route('legal.terms') }}" target="_blank" class="text-success">Terms of Service</a> and <a href="{{ route('legal.privacy') }}" target="_blank" class="text-success">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-forest w-100 py-2">
                        Complete Registration
                    </button>
                </form>

                <div class="text-center mt-3 small text-muted">
                    Already registered? <a href="{{ route('login') }}" class="text-success fw-semibold">Sign in here</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleVendorFields() {
        const isVendor = document.getElementById('roleVendor').checked;
        const vendorFields = document.getElementById('vendorFields');
        if (isVendor) {
            vendorFields.classList.remove('d-none');
            document.getElementById('stall_name').setAttribute('required', 'required');
            document.getElementById('market_id').setAttribute('required', 'required');
        } else {
            vendorFields.classList.add('d-none');
            document.getElementById('stall_name').removeAttribute('required');
            document.getElementById('market_id').removeAttribute('required');
        }
    }
</script>
@endpush

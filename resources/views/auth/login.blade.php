@extends('layouts.app')

@section('title', 'Sign In | MarketLink')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="ml-card p-4">
                <div class="text-center mb-4">
                    <div class="ml-brand justify-content-center mb-2">
                        <i class="fa-solid fa-basket-shopping text-success"></i>
                        <span>MarketLink</span>
                    </div>
                    <h4 class="fw-bold mb-1">Access Market Portal</h4>
                    <p class="text-muted small mb-0">Sign in with your customer, vendor, or administrator account</p>
                </div>

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus placeholder="name@example.com">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="Enter password">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small text-muted" for="remember">
                                Keep me signed in
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-forest w-100 py-2 mb-3">
                        Sign In to Portal
                    </button>
                </form>

                <!-- One-click Demo Credentials for Judges -->
                <div class="mt-4 pt-3 border-top">
                    <div class="small fw-bold text-muted mb-2 text-uppercase" style="letter-spacing: 0.05em; font-size: 0.72rem;">
                        Quick-Fill Demo Credentials (TechWiz Evaluation):
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('demo.switch', 'customer') }}" class="btn btn-sm btn-outline-secondary text-start py-2">
                            <i class="fa-solid fa-user text-success me-2"></i> Sign in as <strong>Customer (Maya Lin)</strong>
                        </a>
                        <a href="{{ route('demo.switch', 'vendor') }}" class="btn btn-sm btn-outline-secondary text-start py-2">
                            <i class="fa-solid fa-tractor text-success me-2"></i> Sign in as <strong>Farmer Silas (Stall A-04)</strong>
                        </a>
                        <a href="{{ route('demo.switch', 'admin') }}" class="btn btn-sm btn-outline-secondary text-start py-2">
                            <i class="fa-solid fa-shield text-success me-2"></i> Sign in as <strong>Director Eleanor (Admin)</strong>
                        </a>
                    </div>
                </div>

                <div class="text-center mt-4 small text-muted">
                    Do not have an account? <a href="{{ route('register') }}" class="text-success fw-semibold">Register here</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

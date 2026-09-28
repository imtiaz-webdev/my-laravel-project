@extends('layouts.app')

@section('title', 'Sign In & Admin Portal Access | MarketLink')

@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xl">
                
                <!-- Brand Icon & Header -->
                <div class="text-center mb-6">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-600/30 mb-3">
                        <i class="fa-solid fa-basket-shopping text-2xl"></i>
                    </div>
                    <h3 class="font-extrabold text-2xl text-slate-900 mb-1 font-heading">Sign In to MarketLink</h3>
                    <p class="text-xs text-slate-500 mb-0">Enter your credentials to access your Customer, Farmer, or Administrator backend</p>
                </div>

                <!-- Login Form -->
                <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label text-xs font-bold uppercase tracking-wider text-slate-700">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email" name="email" id="emailInput" value="{{ old('email') }}" class="form-control text-sm py-2.5 rounded-r-xl border-slate-200" required autofocus placeholder="name@example.com">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-xs font-bold uppercase tracking-wider text-slate-700">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-slate-50 border-slate-200 text-slate-400">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" name="password" id="passwordInput" class="form-control text-sm py-2.5 rounded-r-xl border-slate-200" required placeholder="Enter password">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-5 text-xs text-slate-500">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                            <label class="form-check-label" for="remember">
                                Keep me signed in
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-forest w-100 py-3 rounded-2xl font-bold shadow-md shadow-emerald-700/20 mb-3 text-sm">
                        <i class="fa-solid fa-arrow-right-to-bracket me-1.5"></i> Sign In to Account
                    </button>
                </form>

                <div class="text-center mt-5 text-xs text-slate-500">
                    Don't have an account yet? <a href="{{ route('register') }}" class="text-emerald-700 font-bold text-decoration-none hover:underline">Register here</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

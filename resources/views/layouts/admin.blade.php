<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administration') | MarketLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/marketlink.css') }}">
    @stack('styles')
</head>
<body class="admin-layout">
    <div class="admin-frame">
        <aside class="admin-sidebar" aria-label="Admin navigation">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                <span class="admin-brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M4 19.5h16M6.5 19.5V11l5.5-4 5.5 4v8.5M9.5 19.5v-5h5v5M4.5 10.7l7.5-5.2 7.5 5.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="square" stroke-linejoin="miter"/></svg>
                </span>
                <span><strong>MarketLink</strong><small>Administration</small></span>
            </a>
            <div class="admin-nav-label">Workspace</div>
            <nav class="admin-side-nav">
                <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])><span class="admin-nav-icon">01</span> Overview</a>
                <a href="{{ route('admin.orders') }}" @class(['active' => request()->routeIs('admin.orders')])><span class="admin-nav-icon">02</span> Orders</a>
                <a href="{{ route('admin.products') }}" @class(['active' => request()->routeIs('admin.products')])><span class="admin-nav-icon">03</span> Produce</a>
                <a href="{{ route('admin.farmers') }}" @class(['active' => request()->routeIs('admin.farmers')])><span class="admin-nav-icon">04</span> Farmers</a>
                <a href="{{ route('admin.markets') }}" @class(['active' => request()->routeIs('admin.markets')])><span class="admin-nav-icon">05</span> Markets</a>
                <a href="{{ route('admin.reports') }}" @class(['active' => request()->routeIs('admin.reports')])><span class="admin-nav-icon">06</span> Reports</a>
            </nav>
            <div class="admin-sidebar-bottom">
                <a href="{{ route('home') }}" class="admin-public-link">View public marketplace</a>
                <div class="admin-user-row">
                    <span class="admin-user-initials">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                    <span class="admin-user-copy"><strong>{{ auth()->user()->name }}</strong><small>Administrator</small></span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="admin-logout">Sign out</button>
                </form>
            </div>
        </aside>
        <div class="admin-main-column">
            <header class="admin-topbar">
                <div><span class="admin-eyebrow">MarketLink / Control room</span><h1>@yield('page-heading', 'Platform overview')</h1></div>
                <span class="admin-live-status"><span></span> System active</span>
            </header>
            @if(session('success'))<div class="container-fluid px-4 pt-3"><div class="admin-alert success" role="status">{{ session('success') }}</div></div>@endif
            @if(session('error'))<div class="container-fluid px-4 pt-3"><div class="admin-alert error" role="alert">{{ session('error') }}</div></div>@endif
            @if(session('info'))<div class="container-fluid px-4 pt-3"><div class="admin-alert" role="status">{{ session('info') }}</div></div>@endif
            @if($errors->any())<div class="container-fluid px-4 pt-3"><div class="admin-alert error" role="alert">{{ $errors->first() }}</div></div>@endif
            <main class="admin-content">@yield('content')</main>
            <footer class="admin-footer"><span>MarketLink administration</span><span><a href="{{ route('legal.terms') }}">Terms</a><a href="{{ route('legal.privacy') }}">Privacy</a></span></footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/marketlink.js') }}"></script>
    @stack('scripts')
</body>
</html>
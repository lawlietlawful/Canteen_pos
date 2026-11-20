<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS') &bull; Canteen Dawn</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Premium Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CDN CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Executive Design System -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @livewireStyles
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

@auth
<!-- Main Executive Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark main-navbar sticky-top">
    <div class="container-fluid px-3 px-lg-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
            <span class="brand-mark">
                <svg width="20" height="20" viewBox="0 0 64 64" fill="none">
                    <rect width="64" height="64" rx="14" fill="#0f172a"/>
                    <path d="M42 22C39.5 19.5 36 18 32 18C24.268 18 18 24.268 18 32C18 39.732 24.268 46 32 46C36 46 39.5 44.5 42 42" stroke="#ffffff" stroke-width="4.5" stroke-linecap="round"/>
                    <circle cx="33" cy="32" r="3.5" fill="#38bdf8"/>
                    <line x1="33" y1="25" x2="33" y2="28" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
            </span>
            <span class="fw-bold text-white" style="letter-spacing: -0.01em;">CANTEEN <span class="fw-light opacity-75">DAWN</span></span>
        </a>

        <button class="navbar-toggler border-0 shadow-none px-2" type="button" data-bs-toggle="collapse" data-bs-target="#canteenNavbar" aria-controls="canteenNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="canteenNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 nav-pills gap-1 ms-lg-2">
                <!-- Canteen Core -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pos') ? 'active' : '' }}" href="{{ route('pos') }}">
                        <i class="bi bi-cart3 me-1"></i> Point of Sale
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        <i class="bi bi-cup-hot me-1"></i> Menu Items
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                        <i class="bi bi-tags me-1"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                        <i class="bi bi-receipt me-1"></i> Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                        <i class="bi bi-graph-up me-1"></i> Reports
                    </a>
                </li>

                <!-- Separator -->
                <li class="nav-item d-none d-lg-flex align-items-center px-1">
                    <div style="width: 1px; height: 22px; background: rgba(255,255,255,0.15);"></div>
                </li>

                <!-- Centralized Inventory Hub Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-1 {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="#" id="inventoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-weight: 600;">
                        <i class="bi bi-box-seam me-1 text-info"></i> Inventory Hub
                        @if (!empty($navLowStockCount) && $navLowStockCount > 0)
                            <span class="badge rounded-pill bg-danger ms-1 px-1.5 py-0.5" style="font-size: 0.65rem;" title="{{ $navLowStockCount }} low-stock items">{{ $navLowStockCount }}</span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark shadow-lg border-0 mt-2" aria-labelledby="inventoryDropdown" style="background: #0f172a; border-radius: 10px; font-size: 0.88rem; min-width: 220px;">
                        <li>
                            <a class="dropdown-item py-2 {{ request()->routeIs('inventory.dashboard') ? 'active bg-primary' : '' }}" href="{{ route('inventory.dashboard') }}">
                                <i class="bi bi-grid-1x2 text-info me-2"></i> Inventory Overview
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 d-flex justify-content-between align-items-center {{ request()->routeIs('inventory.products.*') ? 'active bg-primary' : '' }}" href="{{ route('inventory.products.index') }}">
                                <span><i class="bi bi-boxes text-info me-2"></i> Warehouse Stock</span>
                                @if (!empty($navLowStockCount) && $navLowStockCount > 0)
                                    <span class="badge rounded-pill bg-danger" style="font-size: 0.65rem;">{{ $navLowStockCount }} Low</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 {{ request()->routeIs('inventory.movements.*') ? 'active bg-primary' : '' }}" href="{{ route('inventory.movements.index') }}">
                                <i class="bi bi-arrow-left-right text-info me-2"></i> Stock Movements
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1 opacity-25"></li>
                        <li>
                            <a class="dropdown-item py-2 {{ request()->routeIs('inventory.purchase-orders.*') ? 'active bg-primary' : '' }}" href="{{ route('inventory.purchase-orders.index') }}">
                                <i class="bi bi-file-earmark-check text-info me-2"></i> Purchase Orders
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2 {{ request()->routeIs('inventory.suppliers.*') ? 'active bg-primary' : '' }}" href="{{ route('inventory.suppliers.index') }}">
                                <i class="bi bi-truck text-info me-2"></i> Suppliers Directory
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                <div class="cashier-badge d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 text-white rounded-circle" style="width: 22px; height: 22px; font-size: 0.72rem;">
                        <i class="bi bi-person-fill"></i>
                    </span>
                    <div class="d-flex flex-column text-start lh-1">
                        <span class="fw-semibold small text-white">{{ Auth::user()->name }}</span>
                        <span style="font-size: 0.68rem; color: #94a3b8;">ID: {{ Auth::user()->idno }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="d-inline ms-1">
                    @csrf
                    <button type="submit" class="btn-navbar-logout" onclick="return confirm('Sign out of cashier session?');">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
@endauth

<!-- Flash Message Notifications -->
<div class="container-fluid px-3 px-lg-4 mt-3 flash-container">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm d-flex align-items-center border-0 bg-white" style="border-left: 3px solid #059669 !important;" role="alert">
            <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
            <div class="flex-grow-1 text-dark small">{!! session('success') !!}</div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm d-flex align-items-center border-0 bg-white" style="border-left: 3px solid #e11d48 !important;" role="alert">
            <i class="bi bi-x-circle-fill text-danger me-2 fs-5"></i>
            <div class="flex-grow-1 text-dark small">{!! session('error') !!}</div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm d-flex align-items-center border-0 bg-white" style="border-left: 3px solid #d97706 !important;" role="alert">
            <i class="bi bi-exclamation-circle-fill text-warning me-2 fs-5"></i>
            <div class="flex-grow-1 text-dark small">{!! session('warning') !!}</div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show shadow-sm d-flex align-items-center border-0 bg-white" style="border-left: 3px solid #0284c7 !important;" role="alert">
            <i class="bi bi-info-circle-fill text-info me-2 fs-5"></i>
            <div class="flex-grow-1 text-dark small">{!! session('info') !!}</div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 bg-white" style="border-left: 3px solid #e11d48 !important;" role="alert">
            <div class="fw-semibold text-danger mb-1 small"><i class="bi bi-exclamation-octagon me-1"></i>Please fix the following:</div>
            <ul class="mb-0 ps-3 small text-secondary">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

<main class="flex-grow-1 pb-4">
    @yield('content')
</main>

<footer class="footer mt-auto py-3 bg-white border-top text-muted small">
    <div class="container-fluid px-3 px-lg-4 d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-semibold text-dark">CANTEEN DAWN</span>
            <span>&bull;</span>
            <span class="text-muted">Centralized Canteen & Inventory &bull; &copy; {{ date('Y') }}</span>
        </div>
        <div class="d-flex align-items-center gap-3 text-secondary" style="font-size: 0.8rem;">
            <span>Timezone: <strong class="text-dark">Asia/Manila</strong></span>
            <span>&bull;</span>
            <span id="footerClock" class="tabular-nums font-monospace">{{ now()->format('h:i:s A') }}</span>
        </div>
    </div>
</footer>

<!-- Bootstrap 5.3 CDN JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    setInterval(function() {
        const now = new Date();
        const el = document.getElementById('footerClock');
        if (el) {
            el.textContent = now.toLocaleTimeString('en-US', { timeZone: 'Asia/Manila' });
        }
    }, 1000);
</script>

@livewireScripts
@stack('scripts')

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS') &bull; Canteen Dawn & Centralized Hub</title>

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

                <!-- Separate Inventory System Bridge Link -->
                <li class="nav-item d-flex align-items-center ms-lg-2">
                    <a href="http://127.0.0.1:8001" target="_blank" class="btn btn-outline-info btn-sm py-1 px-2.5 d-flex align-items-center gap-1.5" style="font-size: 0.78rem; border-color: rgba(56, 189, 248, 0.4);" title="Open Group 2's Standalone Inventory System">
                        <i class="bi bi-box-seam text-info"></i> Open Inventory System (Port 8001) &rarr;
                        @if (!empty($navLowStockCount) && $navLowStockCount > 0)
                            <span class="badge rounded-pill bg-danger ms-1 px-1.5 py-0.5" style="font-size: 0.65rem;" title="{{ $navLowStockCount }} low-stock items in warehouse">{{ $navLowStockCount }} Low</span>
                        @endif
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0 flex-wrap">
                <!-- User Profile Badge with Group Identity -->
                <div class="cashier-badge d-flex align-items-center gap-2">
                    @if (method_exists(Auth::user(), 'isInventoryUser') && Auth::user()->isInventoryUser())
                        <span class="d-inline-flex align-items-center justify-content-center bg-info text-dark rounded-circle" style="width: 24px; height: 24px; font-size: 0.75rem;">
                            <i class="bi bi-box-seam"></i>
                        </span>
                        <div class="d-flex flex-column text-start lh-1">
                            <div class="d-flex align-items-center gap-1">
                                <span class="fw-semibold small text-white">{{ Auth::user()->name }}</span>
                                <span class="badge bg-info-subtle text-info" style="font-size: 0.6rem;">INVENTORY</span>
                            </div>
                            <span style="font-size: 0.68rem; color: #94a3b8;">{{ Auth::user()->idno }} &bull; {{ ucfirst(Auth::user()->role) }}</span>
                        </div>
                    @else
                        <span class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 24px; height: 24px; font-size: 0.75rem;">
                            <i class="bi bi-cart3"></i>
                        </span>
                        <div class="d-flex flex-column text-start lh-1">
                            <div class="d-flex align-items-center gap-1">
                                <span class="fw-semibold small text-white">{{ Auth::user()->name }}</span>
                                <span class="badge bg-success-subtle text-success" style="font-size: 0.6rem;">CANTEEN</span>
                            </div>
                            <span style="font-size: 0.68rem; color: #94a3b8;">{{ Auth::user()->idno }} &bull; {{ ucfirst(Auth::user()->role) }}</span>
                        </div>
                    @endif
                </div>

                <!-- 1-Click Role Switcher Modal Button -->
                <button type="button" class="btn btn-outline-light btn-sm py-1 px-2 d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#switchUserModal" title="Switch User / Role" style="font-size: 0.75rem; border-color: rgba(255,255,255,0.25);">
                    <i class="bi bi-arrow-repeat"></i> Switch
                </button>

                <form method="POST" action="{{ route('logout') }}" class="d-inline ms-1">
                    @csrf
                    <button type="submit" class="btn-navbar-logout" onclick="return confirm('Sign out of active session?');">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Switch User Modal -->
<div class="modal fade" id="switchUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content shadow border-0">
            <div class="modal-header border-bottom py-2.5 px-3">
                <h6 class="modal-title fw-bold text-dark mb-0">Switch Active Team Member</h6>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="mb-3">
                    <span class="text-uppercase small fw-bold text-muted" style="font-size: 0.7rem;">Group 3 &bull; Canteen Management System</span>
                    <div class="list-group list-group-flush mt-1 border rounded">
                        @foreach (\App\Models\User::where('is_active', true)->get() as $cu)
                            <form method="POST" action="{{ route('login.quick') }}" class="m-0">
                                @csrf
                                <input type="hidden" name="username" value="{{ $cu->username }}">
                                <button type="submit" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 border-0 border-bottom {{ Auth::user()->username === $cu->username ? 'bg-light fw-bold' : '' }}">
                                    <div>
                                        <div class="small text-dark">{{ $cu->name }}</div>
                                        <small class="text-muted font-monospace" style="font-size: 0.68rem;">{{ $cu->username }}</small>
                                    </div>
                                    <span class="badge bg-success-subtle text-success small">{{ ucfirst($cu->role) }}</span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

                <div>
                    <span class="text-uppercase small fw-bold text-muted" style="font-size: 0.7rem;">Group 2 &bull; Inventory Management System</span>
                    <div class="list-group list-group-flush mt-1 border rounded">
                        @foreach (\App\Models\InventoryUser::where('is_active', true)->where('username', '!=', 'canteen_system')->get() as $iu)
                            <form method="POST" action="{{ route('login.quick') }}" class="m-0">
                                @csrf
                                <input type="hidden" name="username" value="{{ $iu->username }}">
                                <button type="submit" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3 border-0 border-bottom {{ Auth::user()->username === $iu->username ? 'bg-light fw-bold' : '' }}">
                                    <div>
                                        <div class="small text-dark">{{ $iu->name }}</div>
                                        <small class="text-muted font-monospace" style="font-size: 0.68rem;">{{ $iu->username }}</small>
                                    </div>
                                    <span class="badge bg-info-subtle text-info small">{{ ucfirst($iu->role) }}</span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top p-2 bg-light">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
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
            <span class="text-muted">Centralized Multi-System Portal (POS & Inventory) &bull; &copy; {{ date('Y') }}</span>
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

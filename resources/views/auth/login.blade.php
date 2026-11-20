<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In &bull; Canteen Dawn & Centralized Inventory Hub</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CDN CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body {
            background-color: #0f172a;
            min-height: 100vh;
        }
        .auth-card {
            border-radius: 14px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            background-color: #ffffff;
        }
        .account-chip {
            transition: all 0.15s ease-in-out;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .account-chip:hover {
            border-color: #0284c7;
            background: #f0f9ff;
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3 py-5">

<div class="container" style="max-width: 520px;">
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 bg-white mb-3" style="border-left: 3px solid #059669 !important;" role="alert">
            <div class="small text-dark">{{ session('success') }}</div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show shadow-sm border-0 bg-white mb-3" style="border-left: 3px solid #0284c7 !important;" role="alert">
            <div class="small text-dark">{{ session('info') }}</div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card auth-card p-4 p-sm-5">
        <div class="text-center mb-4">
            <div class="d-inline-flex align-items-center justify-content-center p-2 mb-2">
                <svg width="42" height="42" viewBox="0 0 64 64" fill="none">
                    <rect width="64" height="64" rx="16" fill="#0f172a"/>
                    <path d="M42 22C39.5 19.5 36 18 32 18C24.268 18 18 24.268 18 32C18 39.732 24.268 46 32 46C36 46 39.5 44.5 42 42" stroke="#ffffff" stroke-width="4.5" stroke-linecap="round"/>
                    <circle cx="33" cy="32" r="3.5" fill="#38bdf8"/>
                    <line x1="33" y1="25" x2="33" y2="28" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
            </div>
            <h4 class="fw-bold text-dark mb-0 tracking-tight">CANTEEN DAWN</h4>
            <p class="text-muted small mb-0">Canteen Management System (Group 3 POS)</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger small py-2 border-0 bg-danger-subtle text-danger mb-3" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="username" class="tracking-wide-caps mb-1 d-block text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-person"></i></span>
                    <input type="text" 
                           class="form-control border-start-0 ps-0 @error('username') is-invalid @enderror" 
                           id="username" 
                           name="username" 
                           value="{{ old('username') }}" 
                           placeholder="e.g. llorejo or cpescante" 
                           required 
                           autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="tracking-wide-caps mb-1 d-block text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem;">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-lock"></i></span>
                    <input type="password" 
                           class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="Enter password" 
                           required>
                </div>
                <div class="form-text small text-muted">Default system password: <code class="text-dark">password123</code></div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2 mb-3">
                <i class="bi bi-box-arrow-in-right"></i> Sign In to Canteen POS
            </button>
        </form>

        <!-- Quick 1-Click Evaluation Switcher -->
        <div class="pt-3 border-top">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small fw-bold text-dark text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">1-Click Cashier Logins:</span>
                <span class="badge bg-success-subtle text-success border small">Group 3 Roster</span>
            </div>

            <div class="d-flex flex-column gap-1.5" style="max-height: 220px; overflow-y: auto;">
                @foreach ($canteenUsers as $cu)
                    <form method="POST" action="{{ route('login.quick') }}" class="d-inline m-0">
                        @csrf
                        <input type="hidden" name="username" value="{{ $cu->username }}">
                        <button type="submit" class="account-chip w-100 text-start p-2 rounded d-flex justify-content-between align-items-center text-dark text-decoration-none">
                            <div>
                                <div class="fw-semibold small lh-1 text-dark">{{ $cu->name }}</div>
                                <small class="text-muted font-monospace" style="font-size: 0.7rem;">user: {{ $cu->username }}</small>
                            </div>
                            <span class="badge bg-success-subtle text-success small" style="font-size: 0.65rem;">{{ ucfirst($cu->role) }} &rarr;</span>
                        </button>
                    </form>
                @endforeach
            </div>
        </div>

        <div class="mt-3 pt-2 border-top text-center">
            <span class="text-muted small">Need a new cashier account?</span>
            <a href="{{ route('register') }}" class="text-dark fw-semibold small text-decoration-none ms-1">Register here &rarr;</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

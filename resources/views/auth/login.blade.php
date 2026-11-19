<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In &bull; Canteen Dawn</title>

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
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
            background-color: #ffffff;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center p-3">

<div class="container" style="max-width: 420px;">
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 bg-white" style="border-left: 3px solid #059669 !important;" role="alert">
            <div class="small text-dark">{{ session('success') }}</div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show shadow-sm border-0 bg-white" style="border-left: 3px solid #0284c7 !important;" role="alert">
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
            <p class="text-muted small mb-0">Point of Sale Terminal</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger small py-2 border-0 bg-danger-subtle text-danger mb-3" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="username" class="tracking-wide-caps mb-1 d-block">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-person"></i></span>
                    <input type="text" 
                           class="form-control border-start-0 ps-0 @error('username') is-invalid @enderror" 
                           id="username" 
                           name="username" 
                           value="{{ old('username') }}" 
                           placeholder="Enter username" 
                           required 
                           autofocus>
                </div>
            </div>

            <div class="mb-4">
                <label for="password" class="tracking-wide-caps mb-1 d-block">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-lock"></i></span>
                    <input type="password" 
                           class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="Enter password" 
                           required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2">
                Sign In to POS
            </button>
        </form>

        <div class="mt-4 pt-3 border-top text-center">
            <span class="text-muted small">Need cashier credentials?</span>
            <a href="{{ route('register') }}" class="text-dark fw-semibold small text-decoration-none ms-1">Register here &rarr;</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

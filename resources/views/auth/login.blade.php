<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | {{ config('app.name', 'Hospital Management System') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/hms.css') }}" rel="stylesheet">
</head>
<body>
<div class="login-page">
    <div class="card login-card">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="login-logo"><i class="bi bi-heart-pulse-fill"></i></div>
                <h1 class="h4 fw-bold text-white mb-1">MediCare HMS</h1>
                <p class="text-muted mb-0 small">Hospital Management System &mdash; Staff Login</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-octagon me-1"></i>{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               placeholder="you@hospital.com" required autofocus autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="••••••••" required autocomplete="current-password">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" aria-label="Show password">
                            <i class="bi bi-eye"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small" for="remember">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-hms w-100 py-2 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                </button>
                        </form>

        </div>
    </div>
</div>

<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const input = document.getElementById('password');
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        this.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
</script>
</body>
</html>

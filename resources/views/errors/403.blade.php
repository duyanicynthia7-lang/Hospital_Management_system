<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Access Denied | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/hms.css') }}" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-card card text-center p-4">
        <div class="login-logo mx-auto mb-3"><i class="bi bi-shield-lock-fill"></i></div>
        <h1 class="h3 text-white">403 — Access Denied</h1>
        <p class="text-muted mb-4">{{ $exception?->getMessage() ?: 'You do not have permission to view this page.' }}</p>
        <div class="d-flex justify-content-center gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-outline-light"><i class="bi bi-arrow-left me-1"></i>Go back</a>
            <a href="{{ route('dashboard') }}" class="btn btn-hms"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a>
        </div>
    </div>
</body>
</html>

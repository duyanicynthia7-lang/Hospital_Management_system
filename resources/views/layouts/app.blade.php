<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | {{ config('app.name', 'Hospital Management System') }}</title>

    <!-- Bootstrap 5 + Bootstrap Icons (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- App theme -->
    <link href="{{ asset('css/hms.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
@php
    $authUser = auth()->user();
    $navItems = [
        ['section' => 'Main'],
        ['route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard', 'roles' => ['admin', 'doctor', 'receptionist']],
        ['section' => 'Clinical'],
        ['route' => 'patients.index', 'match' => 'patients.*', 'icon' => 'bi-people', 'label' => 'Patients', 'roles' => ['admin', 'doctor', 'receptionist']],
        ['route' => 'appointments.index', 'match' => 'appointments.*', 'icon' => 'bi-calendar2-week', 'label' => 'Appointments', 'roles' => ['admin', 'doctor', 'receptionist']],
        ['route' => 'admissions.index', 'match' => 'admissions.*', 'icon' => 'bi-hospital', 'label' => 'Admissions', 'roles' => ['admin', 'doctor']],
        ['route' => 'medical-records.index', 'match' => 'medical-records.*', 'icon' => 'bi-file-earmark-medical', 'label' => 'Medical Records', 'roles' => ['admin', 'doctor']],
        ['section' => 'Hospital'],
        ['route' => 'doctors.index', 'match' => 'doctors.*', 'icon' => 'bi-person-badge', 'label' => 'Doctors', 'roles' => ['admin', 'doctor', 'receptionist']],
        ['route' => 'departments.index', 'match' => 'departments.*', 'icon' => 'bi-diagram-3', 'label' => 'Departments', 'roles' => ['admin']],
        ['route' => 'wards.index', 'match' => 'wards.*', 'icon' => 'bi-door-open', 'label' => 'Wards & Beds', 'roles' => ['admin']],
        ['section' => 'Administration', 'roles' => ['admin']],
        ['route' => 'users.index', 'match' => 'users.*', 'icon' => 'bi-shield-lock', 'label' => 'User Management', 'roles' => ['admin']],
    ];
@endphp

<!-- ============ Sidebar ============ -->
<aside class="hms-sidebar" id="hmsSidebar">
    <a href="{{ route('dashboard') }}" class="hms-brand">
        <span class="brand-icon"><i class="bi bi-heart-pulse-fill"></i></span>
        <span>MediCare HMS<small>Hospital Management</small></span>
    </a>

    <nav class="hms-nav">
        @foreach ($navItems as $item)
            @if (isset($item['section']))
                @if (! isset($item['roles']) || in_array($authUser->role, $item['roles'], true))
                    <div class="nav-section">{{ $item['section'] }}</div>
                @endif
            @elseif (in_array($authUser->role, $item['roles'], true))
                <a href="{{ route($item['route']) }}"
                   class="nav-link {{ request()->routeIs($item['match']) ? 'active' : '' }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    <div class="hms-sidebar-footer">
        <i class="bi bi-shield-check me-1"></i> Logged in as <strong class="text-white">{{ $authUser->role_label }}</strong>
    </div>
</aside>
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- ============ Main ============ -->
<div class="hms-main">
    <header class="hms-topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-outline-light d-lg-none" id="sidebarToggle" type="button" aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="d-none d-md-inline text-muted small">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->format('D, d M Y') }}
            </span>
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none text-white dropdown-toggle"
                   data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="hms-avatar">{{ $authUser->initials }}</span>
                    <span class="d-none d-sm-inline">
                        <span class="d-block lh-1">{{ $authUser->name }}</span>
                        <small class="text-muted">{{ $authUser->role_label }}</small>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><span class="dropdown-item-text small text-muted">{{ $authUser->email }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="d-none d-md-block">
                @csrf
                <button type="submit" class="btn btn-sm btn-hms"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
            </form>
        </div>
    </header>

    <main class="hms-content">
        @include('partials.flash')
        @yield('content')
    </main>

    <footer class="hms-footer d-flex justify-content-between">
        <span>&copy; {{ date('Y') }} MediCare Hospital Management System</span>
        <span>Laravel {{ app()->version() }}</span>
    </footer>
</div>

@include('partials.confirm-modal')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/hms.js') }}"></script>
@stack('scripts')
</body>
</html>

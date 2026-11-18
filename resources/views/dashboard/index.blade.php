@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
@php $user = auth()->user(); @endphp

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h2 class="h4 text-white mb-1">Welcome, {{ $user->name }} 👋</h2>
        <p class="text-muted mb-0">Here is what's happening in the hospital today ({{ now()->format('l, d F Y') }}).</p>
    </div>
    <span class="badge bg-hms-red fs-6 px-3 py-2"><i class="bi bi-person-badge me-1"></i>{{ $user->role_label }}</span>
</div>

{{-- ===== Main stats ===== --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('patients.index') }}" class="text-decoration-none">
            <div class="card stat-card h-100"><div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-hms-red"><i class="bi bi-people-fill"></i></div>
                <div><div class="stat-value">{{ number_format($stats['patients']) }}</div><div class="stat-label">Total Patients</div></div>
            </div></div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('doctors.index') }}" class="text-decoration-none">
            <div class="card stat-card h-100"><div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-hms-blue"><i class="bi bi-person-badge-fill"></i></div>
                <div><div class="stat-value">{{ number_format($stats['doctors']) }}</div><div class="stat-label">Total Doctors</div></div>
            </div></div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('appointments.index', ['date' => today()->toDateString()]) }}" class="text-decoration-none">
            <div class="card stat-card h-100"><div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-hms-green"><i class="bi bi-calendar-check-fill"></i></div>
                <div><div class="stat-value">{{ number_format($stats['today_appointments']) }}</div><div class="stat-label">Today's Appointments</div></div>
            </div></div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        @if ($user->hasRole('admin', 'doctor'))
            <a href="{{ route('admissions.index', ['status' => 'admitted']) }}" class="text-decoration-none">
        @endif
            <div class="card stat-card h-100"><div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-hms-amber"><i class="bi bi-hospital-fill"></i></div>
                <div><div class="stat-value">{{ number_format($stats['current_admissions']) }}</div><div class="stat-label">Current Admissions</div></div>
            </div></div>
        @if ($user->hasRole('admin', 'doctor'))
            </a>
        @endif
    </div>
</div>

{{-- ===== Role-specific stats ===== --}}
<div class="row g-3 mb-4">
    @if ($user->isAdmin())
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body">
                <div class="d-flex justify-content-between"><span class="text-muted">System Users</span><i class="bi bi-shield-lock text-danger"></i></div>
                <div class="fs-3 fw-bold text-white">{{ $extra['users'] }}</div>
                <a href="{{ route('users.index') }}" class="small">Manage users &rarr;</a>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body">
                <div class="d-flex justify-content-between"><span class="text-muted">Departments</span><i class="bi bi-diagram-3 text-danger"></i></div>
                <div class="fs-3 fw-bold text-white">{{ $extra['departments'] }}</div>
                <a href="{{ route('departments.index') }}" class="small">View departments &rarr;</a>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card h-100"><div class="card-body">
                <div class="d-flex justify-content-between"><span class="text-muted">Bed Occupancy</span><span class="fw-semibold">{{ $extra['occupied_beds'] }} / {{ $extra['total_beds'] }}</span></div>
                <div class="fs-3 fw-bold text-white">{{ $extra['bed_occupancy'] }}%</div>
                <div class="progress mt-2">
                    <div class="progress-bar bg-{{ $extra['bed_occupancy'] >= 90 ? 'danger' : ($extra['bed_occupancy'] >= 70 ? 'warning' : 'success') }}"
                         style="width: {{ $extra['bed_occupancy'] }}%"></div>
                </div>
            </div></div>
        </div>
    @elseif ($user->isDoctor())
        @if ($doctor)
            <div class="col-md-4">
                <div class="card h-100"><div class="card-body">
                    <span class="text-muted">My Appointments Today</span>
                    <div class="fs-3 fw-bold text-white">{{ $extra['my_today'] }}</div>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card h-100"><div class="card-body">
                    <span class="text-muted">My Upcoming Appointments</span>
                    <div class="fs-3 fw-bold text-white">{{ $extra['my_upcoming'] }}</div>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card h-100"><div class="card-body">
                    <span class="text-muted">My Admitted Patients</span>
                    <div class="fs-3 fw-bold text-white">{{ $extra['my_patients_admitted'] }}</div>
                </div></div>
            </div>
        @else
            <div class="col-12">
                <div class="alert alert-warning mb-0"><i class="bi bi-exclamation-triangle me-1"></i>
                    Your user account is not yet linked to a doctor profile. Ask an administrator to link it under <strong>Doctors</strong>.
                </div>
            </div>
        @endif
    @else
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <span class="text-muted">Upcoming Scheduled Appointments</span>
                <div class="fs-3 fw-bold text-white">{{ $extra['scheduled_total'] }}</div>
            </div></div>
        </div>
        <div class="col-md-6">
            <div class="card h-100"><div class="card-body">
                <span class="text-muted">New Patients (last 7 days)</span>
                <div class="fs-3 fw-bold text-white">{{ $extra['new_patients_week'] }}</div>
            </div></div>
        </div>
    @endif
</div>

<div class="row g-3">
    {{-- ===== Recent appointments ===== --}}
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2 text-danger"></i>{{ $doctor ? 'My Recent Appointments' : 'Recent Appointments' }}</span>
                <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-hms">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr><th>Patient</th><th>Doctor</th><th>Date &amp; Time</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                    @forelse ($recentAppointments as $appointment)
                        <tr>
                            <td>
                                <a href="{{ route('patients.show', $appointment->patient) }}" class="text-white text-decoration-none fw-semibold">{{ $appointment->patient->full_name }}</a>
                                <div class="small text-muted">{{ $appointment->reason ?: '—' }}</div>
                            </td>
                            <td>{{ $appointment->doctor->display_name }}</td>
                            <td>
                                {{ $appointment->appointment_date->format('d M Y') }}
                                <div class="small text-muted">{{ $appointment->appointment_date->format('h:i A') }}</div>
                            </td>
                            <td><span class="badge bg-{{ $appointment->status_color }}">{{ $appointment->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="empty-state"><i class="bi bi-calendar-x"></i>No appointments yet.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== Quick actions ===== --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-lightning-charge me-2 text-danger"></i>Quick Actions</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('patients.create') }}" class="btn btn-hms text-start"><i class="bi bi-person-plus me-2"></i>Register New Patient</a>
                <a href="{{ route('appointments.create') }}" class="btn btn-outline-hms text-start"><i class="bi bi-calendar-plus me-2"></i>Book Appointment</a>
                @if ($user->hasRole('admin', 'doctor'))
                    <a href="{{ route('admissions.create') }}" class="btn btn-outline-hms text-start"><i class="bi bi-hospital me-2"></i>Admit Patient</a>
                    <a href="{{ route('medical-records.index') }}" class="btn btn-outline-light text-start"><i class="bi bi-file-earmark-medical me-2"></i>Medical Records</a>
                @endif
                @if ($user->isAdmin())
                    <a href="{{ route('doctors.create') }}" class="btn btn-outline-light text-start"><i class="bi bi-person-badge me-2"></i>Add Doctor</a>
                    <a href="{{ route('users.create') }}" class="btn btn-outline-light text-start"><i class="bi bi-shield-plus me-2"></i>Create User Account</a>
                @endif
                <a href="{{ route('patients.index') }}" class="btn btn-outline-light text-start"><i class="bi bi-search me-2"></i>Search Patients</a>
            </div>
        </div>
    </div>
</div>
@endsection

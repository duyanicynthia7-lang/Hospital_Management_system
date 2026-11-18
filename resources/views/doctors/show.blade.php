@extends('layouts.app')

@section('title', $doctor->display_name)
@section('page-title', 'Doctor Profile')

@section('content')
@php $isAdmin = auth()->user()->isAdmin(); @endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <a href="{{ route('doctors.index') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>All Doctors</a>
    <div class="d-flex gap-2">
        <a href="{{ route('appointments.create', ['doctor_id' => $doctor->id]) }}" class="btn btn-sm btn-outline-hms"><i class="bi bi-calendar-plus me-1"></i>Book with this Doctor</a>
        @if ($isAdmin)
            <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-sm btn-hms"><i class="bi bi-pencil me-1"></i>Edit</a>
            <button type="button" class="btn btn-sm btn-outline-danger"
                    data-bs-toggle="modal" data-bs-target="#confirmModal"
                    data-action="{{ route('doctors.destroy', $doctor) }}" data-method="DELETE"
                    data-title="Delete doctor" data-message="Delete {{ $doctor->display_name }}?">
                <i class="bi bi-trash me-1"></i>Delete
            </button>
        @endif
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="hms-avatar mx-auto mb-3 bg-hms-blue" style="width:80px;height:80px;font-size:1.8rem;"><i class="bi bi-person-badge"></i></div>
                <h3 class="h5 text-white mb-1">{{ $doctor->display_name }}</h3>
                <div class="text-muted">{{ $doctor->specialization }}</div>
                <div class="mt-2">
                    @if ($doctor->is_active)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif
                    @foreach ($doctor->headedDepartments as $headed)
                        <span class="badge bg-hms-red ms-1">Head of {{ $headed->name }}</span>
                    @endforeach
                </div>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Department</span><span>{{ $doctor->department?->name ?? '—' }}</span></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">License No.</span><span>{{ $doctor->license_number ?: '—' }}</span></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Phone</span><span>{{ $doctor->phone }}</span></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Email</span><span>{{ $doctor->email ?: '—' }}</span></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Schedule</span><span>{{ $doctor->schedule ?: '—' }}</span></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Login account</span><span>{{ $doctor->user?->email ?? 'Not linked' }}</span></li>
            </ul>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row g-3 mb-3">
            <div class="col-4"><div class="card stat-card"><div class="card-body text-center">
                <div class="stat-value">{{ $stats['total_appointments'] }}</div><div class="stat-label">Total Appointments</div>
            </div></div></div>
            <div class="col-4"><div class="card stat-card"><div class="card-body text-center">
                <div class="stat-value">{{ $stats['completed'] }}</div><div class="stat-label">Completed</div>
            </div></div></div>
            <div class="col-4"><div class="card stat-card"><div class="card-body text-center">
                <div class="stat-value">{{ $stats['admitted'] }}</div><div class="stat-label">Patients Admitted</div>
            </div></div></div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-calendar2-week me-2 text-danger"></i>Upcoming Appointments</div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Date &amp; Time</th><th>Patient</th><th>Reason</th></tr></thead>
                    <tbody>
                    @forelse ($upcomingAppointments as $appointment)
                        <tr>
                            <td>{{ $appointment->appointment_date->format('d M Y, h:i A') }}</td>
                            <td><a href="{{ route('patients.show', $appointment->patient) }}" class="text-white">{{ $appointment->patient->full_name }}</a></td>
                            <td>{{ $appointment->reason ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3"><div class="empty-state"><i class="bi bi-calendar-x"></i>No upcoming appointments.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-hospital me-2 text-danger"></i>Currently Admitted Patients</div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Patient</th><th>Ward</th><th>Admitted</th><th>Diagnosis</th></tr></thead>
                    <tbody>
                    @forelse ($currentAdmissions as $admission)
                        <tr>
                            <td><a href="{{ route('patients.show', $admission->patient) }}" class="text-white">{{ $admission->patient->full_name }}</a></td>
                            <td>{{ $admission->ward?->name }}</td>
                            <td>{{ $admission->admitted_at->format('d M Y') }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($admission->diagnosis, 40) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="empty-state"><i class="bi bi-hospital"></i>No admitted patients.</div></td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

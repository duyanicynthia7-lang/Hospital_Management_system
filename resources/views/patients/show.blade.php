@extends('layouts.app')

@section('title', $patient->full_name)
@section('page-title', 'Patient Details')

@section('content')
@php $user = auth()->user(); @endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <a href="{{ route('patients.index') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>All Patients</a>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('appointments.create', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-outline-hms"><i class="bi bi-calendar-plus me-1"></i>Book Appointment</a>
        @if ($user->hasRole('admin', 'doctor'))
            @unless ($patient->currentAdmission)
                <a href="{{ route('admissions.create', ['patient_id' => $patient->id]) }}" class="btn btn-sm btn-outline-hms"><i class="bi bi-hospital me-1"></i>Admit</a>
            @endunless
            <a href="{{ route('medical-records.show', $patient) }}" class="btn btn-sm btn-outline-light"><i class="bi bi-file-earmark-medical me-1"></i>Medical Record</a>
        @endif
        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-hms"><i class="bi bi-pencil me-1"></i>Edit</a>
        @if ($user->isAdmin())
            <button type="button" class="btn btn-sm btn-outline-danger"
                    data-bs-toggle="modal" data-bs-target="#confirmModal"
                    data-action="{{ route('patients.destroy', $patient) }}" data-method="DELETE"
                    data-title="Delete patient" data-message="Delete {{ $patient->full_name }} and all related records?">
                <i class="bi bi-trash me-1"></i>Delete
            </button>
        @endif
    </div>
</div>

<div class="row g-3">
    {{-- Profile card --}}
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="hms-avatar mx-auto mb-3" style="width:80px;height:80px;font-size:1.8rem;">
                    {{ mb_strtoupper(mb_substr($patient->first_name, 0, 1).mb_substr($patient->last_name, 0, 1)) }}
                </div>
                <h3 class="h5 text-white mb-1">{{ $patient->full_name }}</h3>
                <code>{{ $patient->patient_code }}</code>
                <div class="mt-2">
                    @if ($patient->currentAdmission)
                        <span class="badge bg-warning text-dark"><i class="bi bi-hospital me-1"></i>Admitted &middot; {{ $patient->currentAdmission->ward?->name }}</span>
                    @else
                        <span class="badge bg-success">Outpatient</span>
                    @endif
                    @if ($patient->blood_type)<span class="badge bg-danger ms-1">{{ $patient->blood_type }}</span>@endif
                </div>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Gender</span><span>{{ $patient->gender_label }}</span></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Date of birth</span><span>{{ $patient->date_of_birth->format('d M Y') }} ({{ $patient->age }} yrs)</span></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Phone</span><span>{{ $patient->phone }}</span></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Email</span><span>{{ $patient->email ?: '—' }}</span></li>
                <li class="list-group-item"><div class="text-muted">Address</div><div>{{ $patient->address }}</div></li>
                <li class="list-group-item">
                    <div class="text-muted">Emergency contact</div>
                    <div>{{ $patient->emergency_contact_name }} @if ($patient->emergency_contact_relation)<span class="text-muted">({{ $patient->emergency_contact_relation }})</span>@endif</div>
                    <div><i class="bi bi-telephone me-1"></i>{{ $patient->emergency_contact_phone }}</div>
                </li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Registered</span><span>{{ $patient->created_at->format('d M Y') }}</span></li>
            </ul>
        </div>
    </div>

    {{-- Medical info + history tabs --}}
    <div class="col-lg-8">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="card h-100"><div class="card-body">
                    <div class="detail-label mb-1"><i class="bi bi-exclamation-diamond text-danger me-1"></i>Allergies</div>
                    <div class="detail-value">{{ $patient->allergies ?: 'No known allergies' }}</div>
                </div></div>
            </div>
            <div class="col-md-6">
                <div class="card h-100"><div class="card-body">
                    <div class="detail-label mb-1"><i class="bi bi-journal-medical text-danger me-1"></i>Medical notes</div>
                    <div class="detail-value" style="white-space: pre-line;">{{ $patient->medical_notes ?: '—' }}</div>
                </div></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header pb-0">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-appointments" type="button" role="tab">
                            <i class="bi bi-calendar2-week me-1"></i>Appointments <span class="badge bg-secondary">{{ $patient->appointments->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-admissions" type="button" role="tab">
                            <i class="bi bi-hospital me-1"></i>Admissions <span class="badge bg-secondary">{{ $patient->admissions->count() }}</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-appointments" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Date &amp; Time</th><th>Doctor</th><th>Reason</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                            <tbody>
                            @forelse ($patient->appointments as $appointment)
                                <tr>
                                    <td>{{ $appointment->appointment_date->format('d M Y, h:i A') }}</td>
                                    <td>{{ $appointment->doctor->display_name }}<div class="small text-muted">{{ $appointment->doctor->department?->name }}</div></td>
                                    <td>{{ $appointment->reason ?: '—' }}</td>
                                    <td><span class="badge bg-{{ $appointment->status_color }}">{{ $appointment->status_label }}</span></td>
                                    <td class="text-end"><a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-sm btn-outline-warning btn-icon"><i class="bi bi-pencil"></i></a></td>
                                </tr>
                            @empty
                                <tr><td colspan="5"><div class="empty-state"><i class="bi bi-calendar-x"></i>No appointments recorded.</div></td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-admissions" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Admitted</th><th>Discharged</th><th>Ward</th><th>Doctor</th><th>Diagnosis</th><th>Status</th></tr></thead>
                            <tbody>
                            @forelse ($patient->admissions as $admission)
                                <tr>
                                    <td>{{ $admission->admitted_at->format('d M Y') }}</td>
                                    <td>{{ $admission->discharged_at?->format('d M Y') ?? '—' }}</td>
                                    <td>{{ $admission->ward?->name }}</td>
                                    <td>{{ $admission->doctor?->display_name }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($admission->diagnosis, 50) }}</td>
                                    <td><span class="badge bg-{{ $admission->status_color }}">{{ ucfirst($admission->status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-hospital"></i>No admissions recorded.</div></td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

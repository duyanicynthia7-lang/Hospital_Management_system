@extends('layouts.app')

@section('title', 'Medical Record - '.$patient->full_name)
@section('page-title', 'Medical Record')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 d-print-none">
    <a href="{{ route('medical-records.index') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>All records</a>
    <div class="d-flex gap-2">
        <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-light"><i class="bi bi-person me-1"></i>Patient profile</a>
        <button type="button" class="btn btn-sm btn-hms" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print</button>
    </div>
</div>

{{-- Patient header --}}
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
            <span class="hms-avatar" style="width:56px;height:56px;font-size:1.2rem;">{{ strtoupper(substr($patient->first_name, 0, 1).substr($patient->last_name, 0, 1)) }}</span>
            <div>
                <h4 class="mb-0 text-white">{{ $patient->full_name }}</h4>
                <span class="badge bg-dark border border-secondary">{{ $patient->patient_code }}</span>
                @if ($patient->currentAdmission && $patient->currentAdmission->isAdmitted())
                    <span class="badge bg-danger ms-1"><i class="bi bi-hospital me-1"></i>Admitted — {{ $patient->currentAdmission->ward?->name }}</span>
                @endif
            </div>
        </div>
        <div class="row g-3">
            <div class="col-6 col-md-3"><div class="detail-label">Age / Gender</div><div class="detail-value">{{ $patient->age ?? '—' }} yrs · {{ $patient->gender_label }}</div></div>
            <div class="col-6 col-md-3"><div class="detail-label">Date of birth</div><div class="detail-value">{{ $patient->date_of_birth?->format('d M Y') ?? '—' }}</div></div>
            <div class="col-6 col-md-3"><div class="detail-label">Blood type</div><div class="detail-value">{{ $patient->blood_type ?: '—' }}</div></div>
            <div class="col-6 col-md-3"><div class="detail-label">Phone</div><div class="detail-value">{{ $patient->phone ?: '—' }}</div></div>
            <div class="col-md-6"><div class="detail-label">Allergies</div><div class="detail-value {{ $patient->allergies ? 'text-warning' : '' }}">{{ $patient->allergies ?: 'None recorded' }}</div></div>
            <div class="col-md-6"><div class="detail-label">Medical notes</div><div class="detail-value">{{ $patient->medical_notes ?: '—' }}</div></div>
        </div>
    </div>
</div>

{{-- Summary cards --}}
<div class="row g-3 mb-3">
    @php
        $cards = [
            ['label' => 'Appointments', 'value' => $summary['appointments'], 'icon' => 'bi-calendar-check', 'bg' => 'bg-hms-blue'],
            ['label' => 'Completed visits', 'value' => $summary['completed'], 'icon' => 'bi-check2-circle', 'bg' => 'bg-hms-green'],
            ['label' => 'Admissions', 'value' => $summary['admissions'], 'icon' => 'bi-hospital', 'bg' => 'bg-hms-red'],
            ['label' => 'Days admitted', 'value' => $summary['days_admitted'], 'icon' => 'bi-hourglass-split', 'bg' => 'bg-hms-amber'],
        ];
    @endphp
    @foreach ($cards as $card)
        <div class="col-6 col-xl-3">
            <div class="card stat-card h-100"><div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon {{ $card['bg'] }}"><i class="bi {{ $card['icon'] }}"></i></div>
                <div><div class="stat-value">{{ $card['value'] }}</div><div class="stat-label">{{ $card['label'] }}</div></div>
            </div></div>
        </div>
    @endforeach
</div>
<p class="text-muted small mb-3">
    <i class="bi bi-clock-history me-1"></i>Last completed visit:
    {{ $summary['last_visit'] ? \Illuminate\Support\Carbon::parse($summary['last_visit'])->format('d M Y, H:i') : 'No completed visits yet' }}
</p>

<div class="row g-3">
    {{-- Timeline --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-activity me-2 text-danger"></i>Medical Timeline</div>
            <div class="card-body">
                @if ($timeline->isEmpty())
                    <div class="empty-state"><i class="bi bi-journal"></i>No medical history recorded yet.</div>
                @else
                    <div class="timeline">
                        @foreach ($timeline as $item)
                            <div class="timeline-item">
                                <span class="timeline-dot bg-{{ $item['color'] }}"><i class="bi {{ $item['icon'] }}"></i></span>
                                <div class="small text-muted">{{ $item['date']?->format('d M Y, H:i') ?? '—' }}</div>
                                <div class="fw-semibold text-white">{{ $item['title'] }}</div>
                                @if ($item['subtitle'])
                                    <div class="small">{{ $item['subtitle'] }}</div>
                                @endif
                                @if ($item['details'])
                                    <div class="small text-muted fst-italic">{{ $item['details'] }}</div>
                                @endif
                                <span class="badge bg-{{ $item['color'] }} mt-1">{{ $item['status'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        {{-- Appointments table --}}
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-calendar-check me-2 text-danger"></i>Appointments</div>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead><tr><th>Date</th><th>Doctor</th><th>Department</th><th>Reason</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse ($patient->appointments as $appointment)
                        <tr>
                            <td class="text-nowrap">{{ $appointment->appointment_date?->format('d M Y H:i') }}</td>
                            <td>{{ $appointment->doctor?->display_name ?? '—' }}</td>
                            <td>{{ $appointment->doctor?->department?->name ?? '—' }}</td>
                            <td class="small">{{ $appointment->reason ?: '—' }}</td>
                            <td><span class="badge bg-{{ $appointment->status_color }}">{{ $appointment->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No appointments.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Admissions table --}}
        <div class="card">
            <div class="card-header"><i class="bi bi-hospital me-2 text-danger"></i>Admissions</div>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead><tr><th>Admitted</th><th>Discharged</th><th>Ward</th><th>Doctor</th><th>Diagnosis</th><th>Days</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse ($patient->admissions as $admission)
                        <tr>
                            <td class="text-nowrap">{{ $admission->admitted_at?->format('d M Y') }}</td>
                            <td class="text-nowrap">{{ $admission->discharged_at?->format('d M Y') ?? '—' }}</td>
                            <td>{{ $admission->ward?->name ?? '—' }}</td>
                            <td>{{ $admission->doctor?->display_name ?? '—' }}</td>
                            <td class="small">{{ $admission->diagnosis ?: '—' }}</td>
                            <td>{{ $admission->length_of_stay }}</td>
                            <td><span class="badge bg-{{ $admission->status_color }}">{{ ucfirst($admission->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">No admissions.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

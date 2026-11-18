@extends('layouts.app')

@section('title', 'Appointments')
@section('page-title', 'Appointments')

@section('content')
@php
    $tabs = ['' => 'All'] + \App\Models\Appointment::STATUSES;
    $baseParams = array_filter(['date' => $date, 'mine' => $mine ? 1 : null]);
@endphp

<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="bi bi-calendar2-week me-2 text-danger"></i>Appointments</span>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <form method="GET" action="{{ route('appointments.index') }}" class="d-flex gap-2 align-items-center">
                @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                @if ($myDoctor)
                    <div class="form-check form-switch mb-0 me-2">
                        <input class="form-check-input" type="checkbox" id="mine" name="mine" value="1" @checked($mine) onchange="this.form.submit()">
                        <label class="form-check-label small" for="mine">My appointments only</label>
                    </div>
                @endif
                <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm" onchange="this.form.submit()" title="Filter by date">
                @if ($date)
                    <a href="{{ route('appointments.index', array_filter(['status' => $status, 'mine' => $mine ? 1 : null])) }}" class="btn btn-sm btn-outline-secondary text-nowrap">Clear date</a>
                @endif
            </form>
            <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-hms text-nowrap"><i class="bi bi-calendar-plus me-1"></i>Book Appointment</a>
        </div>
    </div>

    {{-- Status filter tabs --}}
    <div class="px-3 pt-2">
        <ul class="nav nav-tabs">
            @foreach ($tabs as $key => $label)
                <li class="nav-item">
                    <a class="nav-link {{ (string) $status === (string) $key ? 'active' : '' }}"
                       href="{{ route('appointments.index', array_merge($baseParams, $key !== '' ? ['status' => $key] : [])) }}">
                        {{ $label }}
                        <span class="badge bg-secondary ms-1">{{ $counts[$key === '' ? 'all' : $key] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Date &amp; Time</th><th>Patient</th><th>Doctor</th><th>Reason</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($appointments as $appointment)
                <tr>
                    <td class="text-nowrap">
                        {{ $appointment->appointment_date->format('d M Y') }}
                        <div class="small text-muted">{{ $appointment->appointment_date->format('h:i A') }}
                            @if ($appointment->appointment_date->isToday())<span class="badge bg-hms-red ms-1">Today</span>@endif
                        </div>
                    </td>
                    <td><a href="{{ route('patients.show', $appointment->patient) }}" class="text-white fw-semibold text-decoration-none">{{ $appointment->patient->full_name }}</a>
                        <div class="small text-muted">{{ $appointment->patient->patient_code }}</div></td>
                    <td>{{ $appointment->doctor->display_name }}<div class="small text-muted">{{ $appointment->doctor->department?->name }}</div></td>
                    <td class="small">{{ $appointment->reason ?: '—' }}</td>
                    <td><span class="badge bg-{{ $appointment->status_color }}">{{ $appointment->status_label }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('appointments.edit', $appointment) }}" class="btn btn-sm btn-outline-warning btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-icon" title="Delete"
                                data-bs-toggle="modal" data-bs-target="#confirmModal"
                                data-action="{{ route('appointments.destroy', $appointment) }}" data-method="DELETE"
                                data-title="Delete appointment"
                                data-message="Delete the appointment for {{ $appointment->patient->full_name }} on {{ $appointment->appointment_date->format('d M Y, h:i A') }}?">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-calendar-x"></i>No appointments found for this filter.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($appointments->hasPages())
        <div class="card-footer">{{ $appointments->links() }}</div>
    @endif
</div>
@endsection

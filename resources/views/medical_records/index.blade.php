@extends('layouts.app')

@section('title', 'Medical Records')
@section('page-title', 'Medical Records')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('medical-records.index') }}" class="row g-2 align-items-end">
            <div class="col-md-9">
                <label for="search" class="form-label small">Find a patient</label>
                <input type="text" id="search" name="search" value="{{ $search }}" class="form-control" placeholder="Name, patient code, phone or email">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-hms flex-fill"><i class="bi bi-search me-1"></i>Search</button>
                <a href="{{ route('medical-records.index') }}" class="btn btn-outline-light" title="Reset"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="bi bi-journal-medical me-2 text-danger"></i>Patient Records <span class="badge bg-secondary ms-1">{{ $patients->total() }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Code</th><th>Patient</th><th>Age / Gender</th><th>Blood</th><th>Appointments</th><th>Admissions</th><th class="text-end">Record</th></tr></thead>
            <tbody>
            @forelse ($patients as $patient)
                <tr>
                    <td><span class="badge bg-dark border border-secondary">{{ $patient->patient_code }}</span></td>
                    <td class="fw-semibold text-white">{{ $patient->full_name }}</td>
                    <td>{{ $patient->age ?? '—' }} yrs · {{ $patient->gender_label }}</td>
                    <td>{{ $patient->blood_type ?: '—' }}</td>
                    <td><span class="badge bg-primary">{{ $patient->appointments_count }}</span></td>
                    <td><span class="badge bg-info text-dark">{{ $patient->admissions_count }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('medical-records.show', $patient) }}" class="btn btn-sm btn-outline-hms">
                            <i class="bi bi-folder2-open me-1"></i>Open
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-journal-x"></i>No patients match your search.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($patients->hasPages())
        <div class="card-footer">{{ $patients->links() }}</div>
    @endif
</div>
@endsection

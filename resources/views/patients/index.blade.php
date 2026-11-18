@extends('layouts.app')

@section('title', 'Patients')
@section('page-title', 'Patients')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="bi bi-people me-2 text-danger"></i>Patient Register <span class="badge bg-secondary ms-1">{{ $patients->total() }}</span></span>
        <div class="d-flex flex-wrap gap-2">
            <form method="GET" action="{{ route('patients.index') }}" class="d-flex gap-2" role="search">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Name, ID, phone or email" style="min-width: 220px;">
                </div>
                <button type="submit" class="btn btn-sm btn-outline-light">Search</button>
                @if ($search !== '')
                    <a href="{{ route('patients.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
                @endif
            </form>
            <a href="{{ route('patients.create') }}" class="btn btn-sm btn-hms"><i class="bi bi-person-plus me-1"></i>New Patient</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Patient ID</th>
                <th>Name</th>
                <th>Gender / Age</th>
                <th>Blood</th>
                <th>Phone</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($patients as $patient)
                <tr>
                    <td><code>{{ $patient->patient_code }}</code></td>
                    <td>
                        <a href="{{ route('patients.show', $patient) }}" class="text-white fw-semibold text-decoration-none">{{ $patient->full_name }}</a>
                        @if ($patient->email)<div class="small text-muted">{{ $patient->email }}</div>@endif
                    </td>
                    <td>{{ $patient->gender_label }} &middot; {{ $patient->age }} yrs</td>
                    <td>@if ($patient->blood_type)<span class="badge bg-danger">{{ $patient->blood_type }}</span>@else<span class="text-muted">—</span>@endif</td>
                    <td>{{ $patient->phone }}</td>
                    <td>
                        @if ($patient->currentAdmission)
                            <span class="badge bg-warning text-dark" title="{{ $patient->currentAdmission->ward?->name }}"><i class="bi bi-hospital me-1"></i>Admitted</span>
                        @else
                            <span class="badge bg-success">Outpatient</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-info btn-icon" title="View"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-outline-warning btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                        @if (auth()->user()->isAdmin())
                            <button type="button" class="btn btn-sm btn-outline-danger btn-icon" title="Delete"
                                    data-bs-toggle="modal" data-bs-target="#confirmModal"
                                    data-action="{{ route('patients.destroy', $patient) }}" data-method="DELETE"
                                    data-title="Delete patient"
                                    data-message="Delete {{ $patient->full_name }}? All their appointments and admission records will also be removed.">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">
                    <div class="empty-state">
                        <i class="bi bi-person-x"></i>
                        {{ $search !== '' ? 'No patients match "'.$search.'".' : 'No patients registered yet.' }}
                    </div>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($patients->hasPages())
        <div class="card-footer">{{ $patients->links() }}</div>
    @endif
</div>
@endsection

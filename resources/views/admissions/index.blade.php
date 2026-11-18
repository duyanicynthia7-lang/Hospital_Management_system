@extends('layouts.app')

@section('title', 'Admissions')
@section('page-title', 'Admissions')

@section('content')
@php $tabs = ['' => ['All', 'all'], 'admitted' => ['Currently Admitted', 'admitted'], 'discharged' => ['Discharged', 'discharged']]; @endphp

<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="bi bi-hospital me-2 text-danger"></i>Inpatient Admissions</span>
        <div class="d-flex flex-wrap gap-2">
            <form method="GET" action="{{ route('admissions.index') }}" class="d-flex gap-2">
                @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
                <select name="ward_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All wards</option>
                    @foreach ($wards as $ward)
                        <option value="{{ $ward->id }}" @selected($wardId == $ward->id)>{{ $ward->name }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admissions.create') }}" class="btn btn-sm btn-hms text-nowrap"><i class="bi bi-plus-lg me-1"></i>Admit Patient</a>
        </div>
    </div>

    <div class="px-3 pt-2">
        <ul class="nav nav-tabs">
            @foreach ($tabs as $key => [$label, $countKey])
                <li class="nav-item">
                    <a class="nav-link {{ (string) $status === (string) $key ? 'active' : '' }}"
                       href="{{ route('admissions.index', array_filter(['status' => $key ?: null, 'ward_id' => $wardId])) }}">
                        {{ $label }} <span class="badge bg-secondary ms-1">{{ $counts[$countKey] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Patient</th><th>Ward</th><th>Doctor</th><th>Admitted</th><th>Discharged</th><th>Stay</th><th>Diagnosis</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($admissions as $admission)
                <tr>
                    <td><a href="{{ route('patients.show', $admission->patient) }}" class="text-white fw-semibold text-decoration-none">{{ $admission->patient->full_name }}</a>
                        <div class="small text-muted">{{ $admission->patient->patient_code }}</div></td>
                    <td>{{ $admission->ward?->name }}</td>
                    <td>{{ $admission->doctor?->display_name }}</td>
                    <td class="text-nowrap">{{ $admission->admitted_at->format('d M Y') }}<div class="small text-muted">{{ $admission->admitted_at->format('h:i A') }}</div></td>
                    <td class="text-nowrap">{{ $admission->discharged_at?->format('d M Y') ?? '—' }}</td>
                    <td>{{ $admission->length_of_stay }}d</td>
                    <td class="small">{{ \Illuminate\Support\Str::limit($admission->diagnosis, 40) }}</td>
                    <td><span class="badge bg-{{ $admission->status_color }}">{{ ucfirst($admission->status) }}</span></td>
                    <td class="text-end text-nowrap">
                        @if ($admission->isAdmitted())
                            <button type="button" class="btn btn-sm btn-outline-success btn-icon" title="Discharge now"
                                    data-bs-toggle="modal" data-bs-target="#confirmModal"
                                    data-action="{{ route('admissions.discharge', $admission) }}" data-method="PATCH"
                                    data-title="Discharge patient" data-confirm="Discharge"
                                    data-message="Discharge {{ $admission->patient->full_name }} from {{ $admission->ward?->name }} now? The bed will be freed.">
                                <i class="bi bi-box-arrow-right"></i>
                            </button>
                        @endif
                        <a href="{{ route('admissions.edit', $admission) }}" class="btn btn-sm btn-outline-warning btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-icon" title="Delete"
                                data-bs-toggle="modal" data-bs-target="#confirmModal"
                                data-action="{{ route('admissions.destroy', $admission) }}" data-method="DELETE"
                                data-title="Delete admission" data-message="Delete this admission record permanently?">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9"><div class="empty-state"><i class="bi bi-hospital"></i>No admissions found.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($admissions->hasPages())
        <div class="card-footer">{{ $admissions->links() }}</div>
    @endif
</div>
@endsection

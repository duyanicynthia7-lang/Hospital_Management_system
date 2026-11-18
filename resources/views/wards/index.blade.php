@extends('layouts.app')

@section('title', 'Wards & Beds')
@section('page-title', 'Wards & Bed Occupancy')

@section('content')
{{-- Hospital-wide summary --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon bg-hms-blue"><i class="bi bi-door-open"></i></div>
        <div><div class="stat-value">{{ $summary['wards'] }}</div><div class="stat-label">Wards</div></div>
    </div></div></div>
    <div class="col-6 col-md-3"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon bg-hms-purple"><i class="bi bi-grid-3x3-gap"></i></div>
        <div><div class="stat-value">{{ $summary['capacity'] }}</div><div class="stat-label">Total Beds</div></div>
    </div></div></div>
    <div class="col-6 col-md-3"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon bg-hms-red"><i class="bi bi-person-fill-lock"></i></div>
        <div><div class="stat-value">{{ $summary['occupied'] }}</div><div class="stat-label">Occupied</div></div>
    </div></div></div>
    <div class="col-6 col-md-3"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3">
        <div class="stat-icon bg-hms-green"><i class="bi bi-check2-square"></i></div>
        <div><div class="stat-value">{{ $summary['available'] }}</div><div class="stat-label">Available ({{ 100 - $summary['percent'] }}%)</div></div>
    </div></div></div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h5 text-white mb-0"><i class="bi bi-door-open me-2 text-danger"></i>All Wards</h2>
    <a href="{{ route('wards.create') }}" class="btn btn-sm btn-hms"><i class="bi bi-plus-lg me-1"></i>New Ward</a>
</div>

<div class="row g-3">
    @forelse ($wards as $ward)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h3 class="h6 text-white mb-0">{{ $ward->name }}</h3>
                            <small class="text-muted">{{ $ward->department?->name ?? 'No department' }}</small>
                        </div>
                        <span class="badge bg-secondary">{{ $ward->type_label }}</span>
                    </div>

                    <div class="d-flex justify-content-between small mb-1 mt-3">
                        <span>Occupancy</span>
                        <span class="fw-semibold">{{ $ward->current_occupancy }} / {{ $ward->capacity }} beds ({{ $ward->occupancy_percent }}%)</span>
                    </div>
                    <div class="progress" role="progressbar" aria-valuenow="{{ $ward->occupancy_percent }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar bg-{{ $ward->occupancy_color }}" style="width: {{ $ward->occupancy_percent }}%"></div>
                    </div>
                    <div class="small mt-2 {{ $ward->isFull() ? 'text-danger fw-semibold' : 'text-muted' }}">
                        @if ($ward->isFull())
                            <i class="bi bi-exclamation-triangle me-1"></i>Ward is full
                        @else
                            {{ $ward->available_beds }} {{ \Illuminate\Support\Str::plural('bed', $ward->available_beds) }} available
                        @endif
                    </div>
                    @if ($ward->description)
                        <p class="small text-muted mt-2 mb-0">{{ \Illuminate\Support\Str::limit($ward->description, 90) }}</p>
                    @endif
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admissions.index', ['ward_id' => $ward->id, 'status' => 'admitted']) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-people me-1"></i>Patients</a>
                    <a href="{{ route('wards.edit', $ward) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                    <button type="button" class="btn btn-sm btn-outline-danger"
                            data-bs-toggle="modal" data-bs-target="#confirmModal"
                            data-action="{{ route('wards.destroy', $ward) }}" data-method="DELETE"
                            data-title="Delete ward" data-message="Delete ward {{ $ward->name }}? Wards with admission history cannot be deleted.">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="card"><div class="empty-state"><i class="bi bi-door-closed"></i>No wards created yet.</div></div></div>
    @endforelse
</div>

@if ($wards->hasPages())
    <div class="mt-3">{{ $wards->links() }}</div>
@endif
@endsection

@extends('layouts.app')

@section('title', 'Edit Ward')
@section('page-title', 'Edit Ward')

@section('content')
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pencil-square me-2 text-danger"></i>Edit {{ $ward->name }}</span>
                <a href="{{ route('wards.index') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
            </div>
            <form method="POST" action="{{ route('wards.update', $ward) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @include('wards._form')
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('wards.index') }}" class="btn btn-outline-light">Cancel</a>
                    <button type="submit" class="btn btn-hms"><i class="bi bi-save me-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-people me-2 text-danger"></i>Current Patients
                <span class="float-end small">{{ $ward->current_occupancy }} / {{ $ward->capacity }}</span>
            </div>
            <div class="card-body pb-0">
                <div class="progress mb-3">
                    <div class="progress-bar bg-{{ $ward->occupancy_color }}" style="width: {{ $ward->occupancy_percent }}%"></div>
                </div>
            </div>
            <ul class="list-group list-group-flush">
                @forelse ($currentPatients as $admission)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('patients.show', $admission->patient) }}" class="text-white fw-semibold">{{ $admission->patient->full_name }}</a>
                            <small class="text-muted">since {{ $admission->admitted_at->format('d M') }}</small>
                        </div>
                        <small class="text-muted">{{ $admission->doctor?->display_name }} &middot; {{ \Illuminate\Support\Str::limit($admission->diagnosis, 40) }}</small>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No patients currently in this ward.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

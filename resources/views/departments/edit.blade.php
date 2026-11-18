@extends('layouts.app')

@section('title', 'Edit Department')
@section('page-title', 'Edit Department')

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pencil-square me-2 text-danger"></i>Edit {{ $department->name }}</span>
                <a href="{{ route('departments.index') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
            </div>
            <form method="POST" action="{{ route('departments.update', $department) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @include('departments._form')
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('departments.index') }}" class="btn btn-outline-light">Cancel</a>
                    <button type="submit" class="btn btn-hms"><i class="bi bi-save me-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-person-badge me-2 text-danger"></i>Doctors ({{ $department->doctors->count() }})</div>
            <ul class="list-group list-group-flush">
                @forelse ($department->doctors as $doctor)
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="{{ route('doctors.show', $doctor) }}" class="text-white">{{ $doctor->display_name }}</a>
                        <small class="text-muted">{{ $doctor->specialization }}</small>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No doctors in this department.</li>
                @endforelse
            </ul>
        </div>
        <div class="card">
            <div class="card-header"><i class="bi bi-door-open me-2 text-danger"></i>Wards ({{ $department->wards->count() }})</div>
            <ul class="list-group list-group-flush">
                @forelse ($department->wards as $ward)
                    <li class="list-group-item d-flex justify-content-between">
                        <a href="{{ route('wards.edit', $ward) }}" class="text-white">{{ $ward->name }}</a>
                        <small class="text-muted">{{ $ward->capacity }} beds</small>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No wards in this department.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Edit Appointment')
@section('page-title', 'Edit Appointment')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-pencil-square me-2 text-danger"></i>Edit Appointment
            <span class="badge bg-{{ $appointment->status_color }} ms-2">{{ $appointment->status_label }}</span></span>
        <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <form method="POST" action="{{ route('appointments.update', $appointment) }}">
        @csrf
        @method('PUT')
        <div class="card-body">
            @include('appointments._form')
        </div>
        <div class="card-footer d-flex justify-content-between gap-2">
            <button type="button" class="btn btn-outline-danger"
                    data-bs-toggle="modal" data-bs-target="#confirmModal"
                    data-action="{{ route('appointments.destroy', $appointment) }}" data-method="DELETE"
                    data-title="Delete appointment" data-message="Delete this appointment permanently?">
                <i class="bi bi-trash me-1"></i>Delete
            </button>
            <div class="d-flex gap-2">
                <a href="{{ route('appointments.index') }}" class="btn btn-outline-light">Cancel</a>
                <button type="submit" class="btn btn-hms"><i class="bi bi-save me-1"></i>Save Changes</button>
            </div>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Edit Patient')
@section('page-title', 'Edit Patient')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-pencil-square me-2 text-danger"></i>Edit {{ $patient->full_name }} <code class="ms-2">{{ $patient->patient_code }}</code></span>
        <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <form method="POST" action="{{ route('patients.update', $patient) }}">
        @csrf
        @method('PUT')
        <div class="card-body">
            @include('patients._form')
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-light">Cancel</a>
            <button type="submit" class="btn btn-hms"><i class="bi bi-save me-1"></i>Save Changes</button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Register Patient')
@section('page-title', 'Register New Patient')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-plus me-2 text-danger"></i>Patient Registration Form</span>
        <a href="{{ route('patients.index') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <form method="POST" action="{{ route('patients.store') }}">
        @csrf
        <div class="card-body">
            @include('patients._form')
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('patients.index') }}" class="btn btn-outline-light">Cancel</a>
            <button type="submit" class="btn btn-hms"><i class="bi bi-check2-circle me-1"></i>Register Patient</button>
        </div>
    </form>
</div>
@endsection

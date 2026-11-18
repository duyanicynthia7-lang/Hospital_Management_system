@extends('layouts.app')

@section('title', 'Add Doctor')
@section('page-title', 'Add Doctor')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-plus me-2 text-danger"></i>New Doctor Profile</span>
        <a href="{{ route('doctors.index') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <form method="POST" action="{{ route('doctors.store') }}">
        @csrf
        <div class="card-body">
            @include('doctors._form')
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('doctors.index') }}" class="btn btn-outline-light">Cancel</a>
            <button type="submit" class="btn btn-hms"><i class="bi bi-check2-circle me-1"></i>Save Doctor</button>
        </div>
    </form>
</div>
@endsection

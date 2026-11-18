@extends('layouts.app')

@section('title', 'Book Appointment')
@section('page-title', 'Book Appointment')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-plus me-2 text-danger"></i>New Appointment</span>
        <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <form method="POST" action="{{ route('appointments.store') }}">
        @csrf
        <div class="card-body">
            @include('appointments._form')
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('appointments.index') }}" class="btn btn-outline-light">Cancel</a>
            <button type="submit" class="btn btn-hms"><i class="bi bi-check2-circle me-1"></i>Book Appointment</button>
        </div>
    </form>
</div>
@endsection

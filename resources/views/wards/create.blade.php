@extends('layouts.app')

@section('title', 'New Ward')
@section('page-title', 'New Ward')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-plus-square me-2 text-danger"></i>Create Ward</span>
        <a href="{{ route('wards.index') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <form method="POST" action="{{ route('wards.store') }}">
        @csrf
        <div class="card-body">
            @include('wards._form')
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('wards.index') }}" class="btn btn-outline-light">Cancel</a>
            <button type="submit" class="btn btn-hms"><i class="bi bi-check2-circle me-1"></i>Create Ward</button>
        </div>
    </form>
</div>
@endsection

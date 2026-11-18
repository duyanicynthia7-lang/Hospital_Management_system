@extends('layouts.app')

@section('title', 'Admit Patient')
@section('page-title', 'Admit Patient')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-hospital me-2 text-danger"></i>New Admission</span>
        <a href="{{ route('admissions.index') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <form method="POST" action="{{ route('admissions.store') }}">
        @csrf
        <input type="hidden" name="status" value="admitted">
        <div class="card-body">
            @include('admissions._form')
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admissions.index') }}" class="btn btn-outline-light">Cancel</a>
            <button type="submit" class="btn btn-hms"><i class="bi bi-check2-circle me-1"></i>Admit Patient</button>
        </div>
    </form>
</div>
@endsection

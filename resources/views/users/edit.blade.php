@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-pencil-square me-2 text-danger"></i>Edit {{ $user->name }}</span>
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="card-body">
            @include('users._form')
        </div>
        <div class="card-footer d-flex justify-content-between gap-2">
            @unless ($user->is(auth()->user()))
                <button type="button" class="btn btn-outline-danger"
                        data-bs-toggle="modal" data-bs-target="#confirmModal"
                        data-action="{{ route('users.destroy', $user) }}" data-method="DELETE"
                        data-title="Delete user"
                        data-message="Delete the account for {{ $user->name }}? They will no longer be able to log in.">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            @else
                <span></span>
            @endunless
            <div class="d-flex gap-2">
                <a href="{{ route('users.index') }}" class="btn btn-outline-light">Cancel</a>
                <button type="submit" class="btn btn-hms"><i class="bi bi-check2-circle me-1"></i>Save Changes</button>
            </div>
        </div>
    </form>
</div>
@endsection

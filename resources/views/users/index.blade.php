@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label for="search" class="form-label small">Search</label>
                <input type="text" id="search" name="search" value="{{ $search }}" class="form-control" placeholder="Name or email">
            </div>
            <div class="col-md-3">
                <label for="role" class="form-label small">Role</label>
                <select id="role" name="role" class="form-select">
                    <option value="">All roles</option>
                    @foreach (\App\Models\User::ROLES as $value => $label)
                        <option value="{{ $value }}" @selected($role === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-hms flex-fill"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-light" title="Reset"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2 text-danger"></i>System Users <span class="badge bg-secondary ms-1">{{ $users->total() }}</span></span>
        <a href="{{ route('users.create') }}" class="btn btn-sm btn-hms"><i class="bi bi-person-plus me-1"></i>New User</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>User</th><th>Email</th><th>Role</th><th>Phone</th><th>Doctor Profile</th><th>Created</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($users as $u)
                @php
                    $roleColor = match ($u->role) {
                        'admin' => 'danger',
                        'doctor' => 'primary',
                        default => 'info text-dark',
                    };
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="hms-avatar">{{ $u->initials }}</span>
                            <span class="fw-semibold text-white">{{ $u->name }}</span>
                            @if ($u->is(auth()->user()))
                                <span class="badge bg-success">You</span>
                            @endif
                        </div>
                    </td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge bg-{{ $roleColor }}">{{ $u->role_label }}</span></td>
                    <td>{{ $u->phone ?: '—' }}</td>
                    <td>
                        @if ($u->doctor)
                            <a href="{{ route('doctors.show', $u->doctor) }}" class="text-white"><i class="bi bi-person-badge me-1"></i>{{ $u->doctor->display_name }}</a>
                        @elseif ($u->isDoctor())
                            <span class="text-warning small"><i class="bi bi-exclamation-triangle me-1"></i>Not linked</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $u->created_at?->format('d M Y') }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-warning btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                        @unless ($u->is(auth()->user()))
                            <button type="button" class="btn btn-sm btn-outline-danger btn-icon" title="Delete"
                                    data-bs-toggle="modal" data-bs-target="#confirmModal"
                                    data-action="{{ route('users.destroy', $u) }}" data-method="DELETE"
                                    data-title="Delete user"
                                    data-message="Delete the account for {{ $u->name }}? They will no longer be able to log in.">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-people"></i>No users found.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($users->hasPages())
        <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>
@endsection

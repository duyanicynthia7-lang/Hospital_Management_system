@extends('layouts.app')

@section('title', 'Doctors')
@section('page-title', 'Doctors')

@section('content')
@php $isAdmin = auth()->user()->isAdmin(); @endphp
<div class="card">
    <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="bi bi-person-badge me-2 text-danger"></i>Medical Staff <span class="badge bg-secondary ms-1">{{ $doctors->total() }}</span></span>
        <div class="d-flex flex-wrap gap-2">
            <form method="GET" action="{{ route('doctors.index') }}" class="d-flex flex-wrap gap-2">
                <input type="search" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Name or specialization" style="min-width: 200px;">
                <select name="department_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All departments</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected($departmentId == $department->id)>{{ $department->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-outline-light"><i class="bi bi-search"></i></button>
            </form>
            @if ($isAdmin)
                <a href="{{ route('doctors.create') }}" class="btn btn-sm btn-hms"><i class="bi bi-plus-lg me-1"></i>Add Doctor</a>
            @endif
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr><th>Doctor</th><th>Specialization</th><th>Department</th><th>Contact</th><th>Schedule</th><th>Upcoming</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
            @forelse ($doctors as $doctor)
                <tr>
                    <td>
                        <a href="{{ route('doctors.show', $doctor) }}" class="text-white fw-semibold text-decoration-none">{{ $doctor->display_name }}</a>
                        <div class="small text-muted">
                            @if ($doctor->user)<i class="bi bi-link-45deg"></i>{{ $doctor->user->email }}@else<i class="bi bi-person-x"></i> No login account@endif
                        </div>
                    </td>
                    <td>{{ $doctor->specialization }}</td>
                    <td>{{ $doctor->department?->name ?? '—' }}</td>
                    <td>{{ $doctor->phone }}@if ($doctor->email)<div class="small text-muted">{{ $doctor->email }}</div>@endif</td>
                    <td class="small">{{ $doctor->schedule ?: '—' }}</td>
                    <td><span class="badge bg-primary">{{ $doctor->upcoming_count }}</span></td>
                    <td>
                        @if ($doctor->is_active)<span class="badge bg-success">Active</span>@else<span class="badge bg-secondary">Inactive</span>@endif
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-sm btn-outline-info btn-icon" title="View"><i class="bi bi-eye"></i></a>
                        @if ($isAdmin)
                            <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-sm btn-outline-warning btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-icon" title="Delete"
                                    data-bs-toggle="modal" data-bs-target="#confirmModal"
                                    data-action="{{ route('doctors.destroy', $doctor) }}" data-method="DELETE"
                                    data-title="Delete doctor" data-message="Delete {{ $doctor->display_name }}? Their appointments will also be removed.">
                                <i class="bi bi-trash"></i>
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8"><div class="empty-state"><i class="bi bi-person-badge"></i>No doctors found.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($doctors->hasPages())
        <div class="card-footer">{{ $doctors->links() }}</div>
    @endif
</div>
@endsection

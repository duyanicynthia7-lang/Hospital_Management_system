@extends('layouts.app')

@section('title', 'Departments')
@section('page-title', 'Departments')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-diagram-3 me-2 text-danger"></i>Hospital Departments <span class="badge bg-secondary ms-1">{{ $departments->total() }}</span></span>
        <a href="{{ route('departments.create') }}" class="btn btn-sm btn-hms"><i class="bi bi-plus-lg me-1"></i>New Department</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Name</th><th>Head Doctor</th><th>Description</th><th>Doctors</th><th>Wards</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($departments as $department)
                <tr>
                    <td class="fw-semibold text-white">{{ $department->name }}</td>
                    <td>
                        @if ($department->headDoctor)
                            <a href="{{ route('doctors.show', $department->headDoctor) }}" class="text-white">{{ $department->headDoctor->display_name }}</a>
                        @else
                            <span class="text-muted">Not assigned</span>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $department->description ? \Illuminate\Support\Str::limit($department->description, 70) : '—' }}</td>
                    <td><span class="badge bg-primary">{{ $department->doctors_count }}</span></td>
                    <td><span class="badge bg-info text-dark">{{ $department->wards_count }}</span></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-outline-warning btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-icon" title="Delete"
                                data-bs-toggle="modal" data-bs-target="#confirmModal"
                                data-action="{{ route('departments.destroy', $department) }}" data-method="DELETE"
                                data-title="Delete department"
                                data-message="Delete the {{ $department->name }} department? Its doctors and wards will remain but become unassigned.">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-diagram-3"></i>No departments yet.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($departments->hasPages())
        <div class="card-footer">{{ $departments->links() }}</div>
    @endif
</div>
@endsection

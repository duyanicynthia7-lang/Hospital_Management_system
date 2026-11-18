@extends('layouts.app')

@section('title', 'Edit Admission')
@section('page-title', 'Edit Admission')

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-pencil-square me-2 text-danger"></i>Admission: {{ $admission->patient->full_name }}
                    <span class="badge bg-{{ $admission->status_color }} ms-2">{{ ucfirst($admission->status) }}</span></span>
                <a href="{{ route('admissions.index') }}" class="btn btn-sm btn-outline-light"><i class="bi bi-arrow-left me-1"></i>Back</a>
            </div>
            <form method="POST" action="{{ route('admissions.update', $admission) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    @include('admissions._form')

                    <hr class="border-secondary my-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="admission_status" class="form-label required">Status</label>
                            <select id="admission_status" name="status" required class="form-select @error('status') is-invalid @enderror">
                                @foreach (\App\Models\Admission::STATUSES as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $admission->status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 discharge-wrapper">
                            <label for="discharged_at" class="form-label">Discharge date &amp; time</label>
                            <input type="datetime-local" id="discharged_at" name="discharged_at" step="60"
                                   value="{{ old('discharged_at', $admission->discharged_at?->format('Y-m-d\TH:i')) }}"
                                   class="form-control @error('discharged_at') is-invalid @enderror">
                            <div class="form-text">Required when status is Discharged. Ignored while Admitted.</div>
                            @error('discharged_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('admissions.index') }}" class="btn btn-outline-light">Cancel</a>
                    <button type="submit" class="btn btn-hms"><i class="bi bi-save me-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        @if ($admission->isAdmitted())
            {{-- Quick discharge --}}
            <div class="card border-success mb-3">
                <div class="card-header text-success"><i class="bi bi-box-arrow-right me-2"></i>Discharge Patient</div>
                <form method="POST" action="{{ route('admissions.discharge', $admission) }}">
                    @csrf
                    @method('PATCH')
                    <div class="card-body">
                        <p class="small text-muted">Record the discharge and free the bed in <strong class="text-white">{{ $admission->ward?->name }}</strong>.</p>
                        <label for="quick_discharged_at" class="form-label">Discharge date &amp; time</label>
                        <input type="datetime-local" id="quick_discharged_at" name="discharged_at" step="60"
                               value="{{ now()->format('Y-m-d\TH:i') }}"
                               min="{{ $admission->admitted_at->format('Y-m-d\TH:i') }}" class="form-control">
                        <div class="form-text">Leave as is to discharge now.</div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success w-100"><i class="bi bi-check2-circle me-1"></i>Discharge Now</button>
                    </div>
                </form>
            </div>
        @else
            <div class="alert alert-success"><i class="bi bi-check-circle me-1"></i>
                Discharged on {{ $admission->discharged_at?->format('d M Y, h:i A') }}.
            </div>
        @endif

        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2 text-danger"></i>Summary</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Patient</span><a href="{{ route('patients.show', $admission->patient) }}" class="text-white">{{ $admission->patient->full_name }}</a></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Ward</span><span>{{ $admission->ward?->name }}</span></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Doctor</span><span>{{ $admission->doctor?->display_name }}</span></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Admitted</span><span>{{ $admission->admitted_at->format('d M Y, h:i A') }}</span></li>
                <li class="list-group-item d-flex justify-content-between"><span class="text-muted">Length of stay</span><span>{{ $admission->length_of_stay }} days</span></li>
            </ul>
        </div>
    </div>
</div>
@endsection

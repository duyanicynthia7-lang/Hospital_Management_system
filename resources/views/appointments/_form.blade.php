{{-- Shared appointment form. Expects $appointment, $patients, $doctors. --}}
<div class="row g-3">
    <div class="col-md-6">
        <label for="patient_id" class="form-label required">Patient</label>
        <select id="patient_id" name="patient_id" required class="form-select @error('patient_id') is-invalid @enderror">
            <option value="">-- Select patient --</option>
            @foreach ($patients as $patient)
                <option value="{{ $patient->id }}" @selected(old('patient_id', $appointment->patient_id) == $patient->id)>
                    {{ $patient->full_name }} ({{ $patient->patient_code }})
                </option>
            @endforeach
        </select>
        @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Patient not listed? <a href="{{ route('patients.create') }}">Register a new patient</a>.</div>
    </div>
    <div class="col-md-6">
        <label for="doctor_id" class="form-label required">Doctor</label>
        <select id="doctor_id" name="doctor_id" required class="form-select @error('doctor_id') is-invalid @enderror">
            <option value="">-- Select doctor --</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}" @selected(old('doctor_id', $appointment->doctor_id) == $doctor->id)>
                    {{ $doctor->display_name }} — {{ $doctor->specialization }}{{ $doctor->department ? ' ('.$doctor->department->name.')' : '' }}
                </option>
            @endforeach
        </select>
        @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="appointment_date" class="form-label required">Date &amp; time</label>
        <input type="datetime-local" id="appointment_date" name="appointment_date" required step="60"
               @unless ($appointment->exists) min="{{ now()->startOfDay()->format('Y-m-d\TH:i') }}" @endunless
               value="{{ old('appointment_date', $appointment->appointment_date?->format('Y-m-d\TH:i')) }}"
               class="form-control @error('appointment_date') is-invalid @enderror">
        @error('appointment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="status" class="form-label required">Status</label>
        <select id="status" name="status" required class="form-select @error('status') is-invalid @enderror">
            @foreach (\App\Models\Appointment::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $appointment->status) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="reason" class="form-label">Reason for visit</label>
        <input type="text" id="reason" name="reason" maxlength="255" placeholder="e.g. Routine check-up"
               value="{{ old('reason', $appointment->reason) }}" class="form-control @error('reason') is-invalid @enderror">
        @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="notes" class="form-label">Notes</label>
        <textarea id="notes" name="notes" rows="4" maxlength="2000"
                  class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $appointment->notes) }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

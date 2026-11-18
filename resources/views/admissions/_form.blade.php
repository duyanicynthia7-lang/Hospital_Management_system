{{-- Shared admission form. Expects $admission, $patients, $wards, $doctors. --}}
<div class="row g-3">
    <div class="col-md-6">
        <label for="patient_id" class="form-label required">Patient</label>
        <select id="patient_id" name="patient_id" required class="form-select @error('patient_id') is-invalid @enderror">
            <option value="">-- Select patient --</option>
            @foreach ($patients as $patient)
                <option value="{{ $patient->id }}" @selected(old('patient_id', $admission->patient_id) == $patient->id)>
                    {{ $patient->full_name }} ({{ $patient->patient_code }})
                </option>
            @endforeach
        </select>
        @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="doctor_id" class="form-label required">Attending doctor</label>
        <select id="doctor_id" name="doctor_id" required class="form-select @error('doctor_id') is-invalid @enderror">
            <option value="">-- Select doctor --</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}" @selected(old('doctor_id', $admission->doctor_id) == $doctor->id)>
                    {{ $doctor->display_name }} — {{ $doctor->specialization }}
                </option>
            @endforeach
        </select>
        @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="ward_id" class="form-label required">Ward</label>
        <select id="ward_id" name="ward_id" required class="form-select @error('ward_id') is-invalid @enderror">
            <option value="">-- Select ward --</option>
            @foreach ($wards as $ward)
                @php
                    $isCurrentWard = $admission->exists && $admission->isAdmitted() && $admission->ward_id == $ward->id;
                    // Full wards are disabled, except the patient's current ward and when editing a discharged record
                    $full = $ward->isFull() && ! $isCurrentWard && ! ($admission->exists && ! $admission->isAdmitted());
                @endphp
                <option value="{{ $ward->id }}" @selected(old('ward_id', $admission->ward_id) == $ward->id) @disabled($full)>
                    {{ $ward->name }} — {{ $ward->available_beds }} of {{ $ward->capacity }} beds free{{ $full ? ' (FULL)' : '' }}
                </option>
            @endforeach
        </select>
        @error('ward_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="admitted_at" class="form-label required">Admission date &amp; time</label>
        <input type="datetime-local" id="admitted_at" name="admitted_at" required step="60"
               value="{{ old('admitted_at', $admission->admitted_at?->format('Y-m-d\TH:i')) }}"
               class="form-control @error('admitted_at') is-invalid @enderror">
        @error('admitted_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="diagnosis" class="form-label required">Diagnosis</label>
        <textarea id="diagnosis" name="diagnosis" rows="3" maxlength="2000" required
                  class="form-control @error('diagnosis') is-invalid @enderror">{{ old('diagnosis', $admission->diagnosis) }}</textarea>
        @error('diagnosis')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="notes" class="form-label">Treatment notes</label>
        <textarea id="notes" name="notes" rows="3" maxlength="2000"
                  class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $admission->notes) }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

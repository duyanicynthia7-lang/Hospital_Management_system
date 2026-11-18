{{-- Shared patient form fields. Expects $patient (new or existing). --}}
<div class="row g-3">
    <div class="col-12"><h6 class="text-danger mb-0"><i class="bi bi-person-vcard me-1"></i>Personal Information</h6></div>

    <div class="col-md-6">
        <label for="first_name" class="form-label required">First name</label>
        <input type="text" id="first_name" name="first_name" maxlength="100" required
               value="{{ old('first_name', $patient->first_name) }}" class="form-control @error('first_name') is-invalid @enderror">
        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="last_name" class="form-label required">Last name</label>
        <input type="text" id="last_name" name="last_name" maxlength="100" required
               value="{{ old('last_name', $patient->last_name) }}" class="form-control @error('last_name') is-invalid @enderror">
        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="date_of_birth" class="form-label required">Date of birth</label>
        <input type="date" id="date_of_birth" name="date_of_birth" max="{{ now()->toDateString() }}" required
               value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}" class="form-control @error('date_of_birth') is-invalid @enderror">
        @error('date_of_birth')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="gender" class="form-label required">Gender</label>
        <select id="gender" name="gender" required class="form-select @error('gender') is-invalid @enderror">
            <option value="">-- Select --</option>
            @foreach (\App\Models\Patient::GENDERS as $value => $label)
                <option value="{{ $value }}" @selected(old('gender', $patient->gender) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="blood_type" class="form-label">Blood type</label>
        <select id="blood_type" name="blood_type" class="form-select @error('blood_type') is-invalid @enderror">
            <option value="">Unknown</option>
            @foreach (\App\Models\Patient::BLOOD_TYPES as $type)
                <option value="{{ $type }}" @selected(old('blood_type', $patient->blood_type) === $type)>{{ $type }}</option>
            @endforeach
        </select>
        @error('blood_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 mt-4"><h6 class="text-danger mb-0"><i class="bi bi-telephone me-1"></i>Contact Details</h6></div>

    <div class="col-md-6">
        <label for="phone" class="form-label required">Phone</label>
        <input type="tel" id="phone" name="phone" maxlength="30" required placeholder="+254 7XX XXX XXX"
               value="{{ old('phone', $patient->phone) }}" class="form-control @error('phone') is-invalid @enderror">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" maxlength="255"
               value="{{ old('email', $patient->email) }}" class="form-control @error('email') is-invalid @enderror">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="address" class="form-label required">Address</label>
        <textarea id="address" name="address" rows="2" maxlength="500" required
                  class="form-control @error('address') is-invalid @enderror">{{ old('address', $patient->address) }}</textarea>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 mt-4"><h6 class="text-danger mb-0"><i class="bi bi-person-exclamation me-1"></i>Emergency Contact</h6></div>

    <div class="col-md-5">
        <label for="emergency_contact_name" class="form-label required">Contact name</label>
        <input type="text" id="emergency_contact_name" name="emergency_contact_name" maxlength="150" required
               value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}" class="form-control @error('emergency_contact_name') is-invalid @enderror">
        @error('emergency_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="emergency_contact_phone" class="form-label required">Contact phone</label>
        <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" maxlength="30" required
               value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" class="form-control @error('emergency_contact_phone') is-invalid @enderror">
        @error('emergency_contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="emergency_contact_relation" class="form-label">Relationship</label>
        <input type="text" id="emergency_contact_relation" name="emergency_contact_relation" maxlength="50" placeholder="e.g. Spouse"
               value="{{ old('emergency_contact_relation', $patient->emergency_contact_relation) }}" class="form-control @error('emergency_contact_relation') is-invalid @enderror">
        @error('emergency_contact_relation')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 mt-4"><h6 class="text-danger mb-0"><i class="bi bi-clipboard2-pulse me-1"></i>Medical Information</h6></div>

    <div class="col-md-6">
        <label for="allergies" class="form-label">Allergies</label>
        <textarea id="allergies" name="allergies" rows="3" maxlength="1000" placeholder="e.g. Penicillin, peanuts"
                  class="form-control @error('allergies') is-invalid @enderror">{{ old('allergies', $patient->allergies) }}</textarea>
        @error('allergies')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="medical_notes" class="form-label">Medical notes / history</label>
        <textarea id="medical_notes" name="medical_notes" rows="3" maxlength="2000"
                  class="form-control @error('medical_notes') is-invalid @enderror">{{ old('medical_notes', $patient->medical_notes) }}</textarea>
        @error('medical_notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

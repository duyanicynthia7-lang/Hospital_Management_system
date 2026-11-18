{{-- Shared doctor form. Expects $doctor, $departments, $users. --}}
<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label required">Full name</label>
        <div class="input-group">
            <span class="input-group-text">Dr.</span>
            <input type="text" id="name" name="name" maxlength="150" required placeholder="e.g. Jane Doe"
                   value="{{ old('name', $doctor->name) }}" class="form-control @error('name') is-invalid @enderror">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <label for="specialization" class="form-label required">Specialization</label>
        <input type="text" id="specialization" name="specialization" maxlength="150" required placeholder="e.g. Cardiologist" list="specializationList"
               value="{{ old('specialization', $doctor->specialization) }}" class="form-control @error('specialization') is-invalid @enderror">
        <datalist id="specializationList">
            @foreach (['General Practitioner', 'Cardiologist', 'Pediatrician', 'General Surgeon', 'Neurologist', 'Orthopedic Surgeon', 'Gynecologist', 'Dermatologist', 'Psychiatrist', 'Radiologist', 'Anesthesiologist', 'Oncologist'] as $spec)
                <option value="{{ $spec }}">
            @endforeach
        </datalist>
        @error('specialization')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="department_id" class="form-label">Department</label>
        <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror">
            <option value="">-- No department --</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}" @selected(old('department_id', $doctor->department_id) == $department->id)>{{ $department->name }}</option>
            @endforeach
        </select>
        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="license_number" class="form-label">License number</label>
        <input type="text" id="license_number" name="license_number" maxlength="50" placeholder="e.g. KMPDC-12345"
               value="{{ old('license_number', $doctor->license_number) }}" class="form-control @error('license_number') is-invalid @enderror">
        @error('license_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label required">Phone</label>
        <input type="tel" id="phone" name="phone" maxlength="30" required
               value="{{ old('phone', $doctor->phone) }}" class="form-control @error('phone') is-invalid @enderror">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="email" class="form-label">Email</label>
        <input type="email" id="email" name="email" maxlength="255"
               value="{{ old('email', $doctor->email) }}" class="form-control @error('email') is-invalid @enderror">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="schedule" class="form-label">Working schedule</label>
        <input type="text" id="schedule" name="schedule" maxlength="255" placeholder="e.g. Mon-Fri 08:00-16:00"
               value="{{ old('schedule', $doctor->schedule) }}" class="form-control @error('schedule') is-invalid @enderror">
        @error('schedule')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="user_id" class="form-label">Linked login account</label>
        <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror">
            <option value="">-- No login account --</option>
            @foreach ($users as $u)
                <option value="{{ $u->id }}" @selected(old('user_id', $doctor->user_id) == $u->id)>{{ $u->name }} ({{ $u->email }})</option>
            @endforeach
        </select>
        <div class="form-text">Only users with the <strong>Doctor</strong> role that are not linked yet are listed. Create one under User Management.</div>
        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1"
                   @checked(old('is_active', $doctor->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active (available for new appointments)</label>
        </div>
    </div>
</div>

{{-- Shared department form. Expects $department and $doctors. --}}
<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label required">Department name</label>
        <input type="text" id="name" name="name" maxlength="150" required placeholder="e.g. Cardiology"
               value="{{ old('name', $department->name) }}" class="form-control @error('name') is-invalid @enderror">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="head_doctor_id" class="form-label">Head doctor</label>
        <select id="head_doctor_id" name="head_doctor_id" class="form-select @error('head_doctor_id') is-invalid @enderror">
            <option value="">-- Not assigned --</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}" @selected(old('head_doctor_id', $department->head_doctor_id) == $doctor->id)>
                    {{ $doctor->display_name }} — {{ $doctor->specialization }}
                </option>
            @endforeach
        </select>
        @error('head_doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" rows="4" maxlength="1000"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $department->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

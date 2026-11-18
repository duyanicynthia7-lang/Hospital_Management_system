{{-- Shared ward form. Expects $ward and $departments. --}}
<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label required">Ward name</label>
        <input type="text" id="name" name="name" maxlength="150" required placeholder="e.g. General Ward B"
               value="{{ old('name', $ward->name) }}" class="form-control @error('name') is-invalid @enderror">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="department_id" class="form-label">Department</label>
        <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror">
            <option value="">-- No department --</option>
            @foreach ($departments as $department)
                <option value="{{ $department->id }}" @selected(old('department_id', $ward->department_id) == $department->id)>{{ $department->name }}</option>
            @endforeach
        </select>
        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="type" class="form-label required">Ward type</label>
        <select id="type" name="type" required class="form-select @error('type') is-invalid @enderror">
            @foreach (\App\Models\Ward::TYPES as $value => $label)
                <option value="{{ $value }}" @selected(old('type', $ward->type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="capacity" class="form-label required">Bed capacity</label>
        <input type="number" id="capacity" name="capacity" min="1" max="1000" required
               value="{{ old('capacity', $ward->capacity) }}" class="form-control @error('capacity') is-invalid @enderror">
        @if ($ward->exists)
            <div class="form-text">Currently occupied: {{ $ward->current_occupancy }} beds (capacity cannot go below this).</div>
        @endif
        @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" rows="3" maxlength="1000"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $ward->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

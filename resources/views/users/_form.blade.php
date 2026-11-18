
{{-- Shared user form. Expects $user. --}}
@php
    $isEdit = $user->exists;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label required">Full name</label>
        <input
            type="text"
            id="name"
            name="name"
            maxlength="150"
            required
            value="{{ old('name', $user->name) }}"
            class="form-control @error('name') is-invalid @enderror"
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label required">Email address</label>
        <input
            type="email"
            id="email"
            name="email"
            maxlength="255"
            required
            value="{{ old('email', $user->email) }}"
            class="form-control @error('email') is-invalid @enderror"
        >
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="role" class="form-label required">Role</label>
        <select
            id="role"
            name="role"
            required
            class="form-select @error('role') is-invalid @enderror"
        >
            @foreach (\App\Models\User::ROLES as $value => $label)
                <option
                    value="{{ $value }}"
                    @selected(old('role', $user->role) === $value)
                >
                    {{ $label }}
                </option>
            @endforeach
        </select>

        @error('role')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text text-muted">
            Admins manage everything; doctors see medical records and admissions;
            receptionists handle patients and appointments.
        </div>
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label">Phone</label>
        <input
            type="text"
            id="phone"
            name="phone"
            maxlength="30"
            placeholder="+254 7XX XXX XXX"
            value="{{ old('phone', $user->phone) }}"
            class="form-control @error('phone') is-invalid @enderror"
        >
        @error('phone')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="password" class="form-label {{ $isEdit ? '' : 'required' }}">
            Password
        </label>
        <input
            type="password"
            id="password"
            name="password"
            autocomplete="new-password"
            {{ $isEdit ? '' : 'required' }}
            minlength="8"
            class="form-control @error('password') is-invalid @enderror"
        >
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        <div class="form-text text-muted">
            {{ $isEdit ? 'Leave blank to keep the current password.' : 'At least 8 characters.' }}
        </div>
    </div>

    <div class="col-md-6">
        <label
            for="password_confirmation"
            class="form-label {{ $isEdit ? '' : 'required' }}"
        >
            Confirm password
        </label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            autocomplete="new-password"
            {{ $isEdit ? '' : 'required' }}
            minlength="8"
            class="form-control @error('password_confirmation') is-invalid @enderror"
        >
        @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

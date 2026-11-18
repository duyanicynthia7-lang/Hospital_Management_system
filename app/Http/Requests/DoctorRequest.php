<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        $doctorId = $this->route('doctor')?->id;

        return [
            'name' => ['required', 'string', 'max:150'],
            'specialization' => ['required', 'string', 'max:150'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'license_number' => ['nullable', 'string', 'max:50', Rule::unique('doctors', 'license_number')->ignore($doctorId)],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{7,30}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'schedule' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'user_id' => [
                'nullable',
                'integer',
                // Linked account must exist and have the "doctor" role
                Rule::exists('users', 'id')->where('role', User::ROLE_DOCTOR),
                // A user account can only be linked to one doctor profile
                Rule::unique('doctors', 'user_id')->ignore($doctorId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.exists' => 'The selected user account must have the Doctor role.',
            'user_id.unique' => 'This user account is already linked to another doctor.',
            'phone.regex' => 'Enter a valid phone number.',
        ];
    }
}

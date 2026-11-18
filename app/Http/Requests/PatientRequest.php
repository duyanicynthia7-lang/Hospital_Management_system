<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Access is controlled by route middleware (auth + role)
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today', 'after:1900-01-01'],
            'gender' => ['required', Rule::in(array_keys(Patient::GENDERS))],
            'blood_type' => ['nullable', Rule::in(Patient::BLOOD_TYPES)],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{7,30}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'emergency_contact_name' => ['required', 'string', 'max:150'],
            'emergency_contact_phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{7,30}$/'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:50'],
            'allergies' => ['nullable', 'string', 'max:1000'],
            'medical_notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a valid phone number (digits, spaces, +, - and brackets only).',
            'emergency_contact_phone.regex' => 'Enter a valid emergency contact phone number.',
            'date_of_birth.before_or_equal' => 'Date of birth cannot be in the future.',
        ];
    }
}

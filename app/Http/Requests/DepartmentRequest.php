<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $departmentId = $this->route('department')?->id;

        return [
            'name' => ['required', 'string', 'max:150', Rule::unique('departments', 'name')->ignore($departmentId)],
            'description' => ['nullable', 'string', 'max:1000'],
            'head_doctor_id' => ['nullable', 'integer', 'exists:doctors,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'head_doctor_id' => 'head doctor',
        ];
    }
}

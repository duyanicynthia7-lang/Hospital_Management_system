<?php

namespace App\Http\Requests;

use App\Models\Ward;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class WardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $wardId = $this->route('ward')?->id;

        return [
            'name' => ['required', 'string', 'max:150', Rule::unique('wards', 'name')->ignore($wardId)],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'type' => ['required', Rule::in(array_keys(Ward::TYPES))],
            'capacity' => ['required', 'integer', 'min:1', 'max:1000'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * When editing, capacity cannot drop below the number of patients currently admitted.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $ward = $this->route('ward');
                if (! $ward instanceof Ward || $validator->errors()->has('capacity')) {
                    return;
                }

                $occupied = $ward->activeAdmissions()->count();
                if ((int) $this->input('capacity') < $occupied) {
                    $validator->errors()->add(
                        'capacity',
                        "Capacity cannot be less than the current occupancy ({$occupied} patients admitted)."
                    );
                }
            },
        ];
    }
}

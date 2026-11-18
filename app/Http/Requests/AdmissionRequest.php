<?php

namespace App\Http\Requests;

use App\Models\Admission;
use App\Models\Ward;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AdmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // If a discharge date is entered, the admission is discharged; otherwise it is active.
        if (! $this->filled('status')) {
            $this->merge([
                'status' => $this->filled('discharged_at') ? Admission::STATUS_DISCHARGED : Admission::STATUS_ADMITTED,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'ward_id' => ['required', 'integer', 'exists:wards,id'],
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'admitted_at' => ['required', 'date'],
            'discharged_at' => ['nullable', 'date', 'after_or_equal:admitted_at', 'required_if:status,'.Admission::STATUS_DISCHARGED],
            'diagnosis' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(array_keys(Admission::STATUSES))],
        ];
    }

    public function messages(): array
    {
        return [
            'discharged_at.after_or_equal' => 'Discharge date must be on or after the admission date.',
            'discharged_at.required_if' => 'A discharge date is required when the status is Discharged.',
        ];
    }

    /**
     * Business rules: ward must have a free bed, and a patient cannot be admitted twice at the same time.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['patient_id', 'ward_id', 'status'])) {
                    return;
                }
                if ($this->input('status') !== Admission::STATUS_ADMITTED) {
                    return; // discharged records don't occupy a bed
                }

                /** @var Admission|null $current */
                $current = $this->route('admission');
                $ward = Ward::find($this->input('ward_id'));

                // Ward capacity check (exclude this admission if it is already in the same ward)
                $occupied = $ward->activeAdmissions()
                    ->when($current, fn ($q) => $q->whereKeyNot($current->id))
                    ->count();

                if ($occupied >= $ward->capacity) {
                    $validator->errors()->add('ward_id', "{$ward->name} is full ({$occupied}/{$ward->capacity} beds occupied).");
                }

                // Patient already admitted elsewhere?
                $alreadyAdmitted = Admission::query()
                    ->where('patient_id', $this->input('patient_id'))
                    ->where('status', Admission::STATUS_ADMITTED)
                    ->when($current, fn ($q) => $q->whereKeyNot($current->id))
                    ->exists();

                if ($alreadyAdmitted) {
                    $validator->errors()->add('patient_id', 'This patient is already admitted. Discharge the current admission first.');
                }
            },
        ];
    }
}

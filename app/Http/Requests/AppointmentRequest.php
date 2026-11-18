<?php
namespace App\Http\Requests;
use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCreate = $this->isMethod('post');

        return [
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => [
                'required',
                'integer',
                $isCreate ? Rule::exists('doctors', 'id')->where('is_active', true) : Rule::exists('doctors', 'id'),
            ],
            // New appointments cannot be booked in the past; existing ones may be edited freely
            'appointment_date' => array_filter([
                'required',
                'date',
                $isCreate ? 'after_or_equal:today' : null,
            ]),
            'status' => ['required', Rule::in(array_keys(Appointment::STATUSES))],
            'reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'doctor_id.exists' => 'Please select an active doctor.',
            'appointment_date.after_or_equal' => 'New appointments cannot be booked in the past.',
        ];
    }

    /**
     * Prevent double-booking: a doctor cannot have two scheduled appointments at the same date/time.
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['doctor_id', 'appointment_date', 'status'])) {
                    return;
                }
                if ($this->input('status') !== Appointment::STATUS_SCHEDULED) {
                    return;
                }

                $date = Carbon::parse($this->input('appointment_date'))->format('Y-m-d H:i:00');
                $current = $this->route('appointment');

                $clash = Appointment::query()
                    ->where('doctor_id', $this->input('doctor_id'))
                    ->where('status', Appointment::STATUS_SCHEDULED)
                    ->where('appointment_date', $date)
                    ->when($current, fn ($q) => $q->whereKeyNot($current->id))
                    ->exists();

                if ($clash) {
                    $validator->errors()->add('appointment_date', 'This doctor already has a scheduled appointment at that date and time.');
                }
            },
        ];
    }
}

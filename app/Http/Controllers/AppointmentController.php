<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        if (! array_key_exists((string) $status, Appointment::STATUSES)) {
            $status = null;
        }
        $date = $request->query('date');
        $mine = $request->boolean('mine');
        $myDoctor = $request->user()->isDoctor() ? $request->user()->doctor : null;

        $baseQuery = Appointment::query()
            ->when($mine && $myDoctor, fn ($q) => $q->where('doctor_id', $myDoctor->id))
            ->when($date, fn ($q) => $q->whereDate('appointment_date', $date));

        // Counts for the filter tabs
        $counts = ['all' => (clone $baseQuery)->count()];
        foreach (array_keys(Appointment::STATUSES) as $s) {
            $counts[$s] = (clone $baseQuery)->where('status', $s)->count();
        }

        $appointments = (clone $baseQuery)
            ->with(['patient', 'doctor.department'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderByDesc('appointment_date')
            ->paginate(10)
            ->withQueryString();

        return view('appointments.index', compact('appointments', 'status', 'counts', 'date', 'mine', 'myDoctor'));
    }

    public function create(Request $request): View
    {
        $appointment = new Appointment([
            'status' => Appointment::STATUS_SCHEDULED,
            'patient_id' => $request->integer('patient_id') ?: null,
            'doctor_id' => $request->integer('doctor_id') ?: ($request->user()->doctor?->id),
        ]);

        return view('appointments.create', array_merge(['appointment' => $appointment], $this->formData()));
    }

    public function store(AppointmentRequest $request): RedirectResponse
    {
        Appointment::create($request->validated());

        return redirect()->route('appointments.index')->with('success', 'Appointment booked successfully.');
    }

    public function show(Appointment $appointment): RedirectResponse
    {
        return redirect()->route('appointments.edit', $appointment);
    }

    public function edit(Appointment $appointment): View
    {
        return view('appointments.edit', array_merge(['appointment' => $appointment], $this->formData($appointment)));
    }

    public function update(AppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $appointment->update($request->validated());

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Appointment deleted.');
    }

    private function formData(?Appointment $appointment = null): array
    {
        return [
            'patients' => Patient::orderBy('first_name')->orderBy('last_name')->get(),
            // Active doctors, plus the currently assigned doctor when editing (even if inactive)
            'doctors' => Doctor::with('department')
                ->where(function ($q) use ($appointment) {
                    $q->where('is_active', true);
                    if ($appointment?->doctor_id) {
                        $q->orWhere('id', $appointment->doctor_id);
                    }
                })
                ->orderBy('name')
                ->get(),
        ];
    }
}

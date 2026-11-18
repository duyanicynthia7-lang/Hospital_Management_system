<?php

namespace App\Http\Controllers;

use App\Http\Requests\PatientRequest;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $patients = Patient::query()
            ->search($search)
            ->with('currentAdmission.ward')
            ->withCount('appointments')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('patients.index', compact('patients', 'search'));
    }

    public function create(): View
    {
        return view('patients.create', ['patient' => new Patient]);
    }

    public function store(PatientRequest $request): RedirectResponse
    {
        $patient = Patient::create($request->validated());

        return redirect()->route('patients.show', $patient)
            ->with('success', "Patient {$patient->full_name} registered successfully (ID: {$patient->patient_code}).");
    }

    public function show(Patient $patient): View
    {
        $patient->load([
            'appointments' => fn ($q) => $q->with('doctor.department')->orderByDesc('appointment_date'),
            'admissions' => fn ($q) => $q->with(['ward', 'doctor'])->orderByDesc('admitted_at'),
        ]);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(PatientRequest $request, Patient $patient): RedirectResponse
    {
        $patient->update($request->validated());

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient details updated successfully.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        if ($patient->admissions()->where('status', 'admitted')->exists()) {
            return back()->with('error', 'Cannot delete a patient who is currently admitted. Discharge them first.');
        }

        $name = $patient->full_name;
        $patient->delete(); // appointments & admissions are removed via cascade

        return redirect()->route('patients.index')->with('success', "Patient {$name} deleted.");
    }
}

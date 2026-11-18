<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicalRecordController extends Controller
{
    /**
     * Patient picker for medical records.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $patients = Patient::query()
            ->search($search)
            ->withCount(['appointments', 'admissions'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->paginate(12)
            ->withQueryString();

        return view('medical_records.index', compact('patients', 'search'));
    }

    /**
     * Full medical history for one patient: appointments + admissions merged into a timeline.
     */
    public function show(Patient $patient): View
    {
        $patient->load([
            'appointments' => fn ($q) => $q->with('doctor.department')->orderByDesc('appointment_date'),
            'admissions' => fn ($q) => $q->with(['ward.department', 'doctor'])->orderByDesc('admitted_at'),
            'currentAdmission.ward',
        ]);

        $timeline = collect();
        foreach ($patient->appointments as $appointment) {
            $timeline->push([
                'type' => 'appointment',
                'date' => $appointment->appointment_date,
                'title' => 'Appointment with '.($appointment->doctor?->display_name ?? 'Unknown doctor'),
                'subtitle' => $appointment->reason ?: 'General consultation',
                'details' => $appointment->notes,
                'status' => $appointment->status_label,
                'color' => $appointment->status_color,
                'icon' => 'bi-calendar-check',
            ]);
        }
        foreach ($patient->admissions as $admission) {
            $timeline->push([
                'type' => 'admission',
                'date' => $admission->admitted_at,
                'title' => 'Admitted to '.($admission->ward?->name ?? 'ward'),
                'subtitle' => $admission->diagnosis,
                'details' => $admission->notes,
                'status' => ucfirst($admission->status).($admission->discharged_at ? ' on '.$admission->discharged_at->format('d M Y') : ''),
                'color' => $admission->status_color,
                'icon' => 'bi-hospital',
            ]);
        }
        $timeline = $timeline->sortByDesc(fn ($item) => $item['date']?->timestamp ?? 0)->values();

        $summary = [
            'appointments' => $patient->appointments->count(),
            'completed' => $patient->appointments->where('status', 'completed')->count(),
            'admissions' => $patient->admissions->count(),
            'days_admitted' => $patient->admissions->sum(fn ($a) => $a->length_of_stay),
            'last_visit' => $patient->appointments->where('status', 'completed')->max('appointment_date'),
        ];

        return view('medical_records.show', compact('patient', 'timeline', 'summary'));
    }
}

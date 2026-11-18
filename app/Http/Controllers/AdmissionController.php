<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdmissionRequest;
use App\Models\Admission;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Ward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdmissionController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        if (! array_key_exists((string) $status, Admission::STATUSES)) {
            $status = null;
        }
        $wardId = $request->integer('ward_id') ?: null;

        $admissions = Admission::query()
            ->with(['patient', 'ward', 'doctor'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($wardId, fn ($q) => $q->where('ward_id', $wardId))
            ->orderBy('status') // 'admitted' sorts before 'discharged'
            ->orderByDesc('admitted_at')
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'all' => Admission::count(),
            'admitted' => Admission::where('status', Admission::STATUS_ADMITTED)->count(),
            'discharged' => Admission::where('status', Admission::STATUS_DISCHARGED)->count(),
        ];
        $wards = Ward::orderBy('name')->get();

        return view('admissions.index', compact('admissions', 'status', 'counts', 'wards', 'wardId'));
    }

    public function create(Request $request): View
    {
        $admission = new Admission([
            'patient_id' => $request->integer('patient_id') ?: null,
            'doctor_id' => $request->user()->doctor?->id,
            'admitted_at' => now(),
            'status' => Admission::STATUS_ADMITTED,
        ]);

        return view('admissions.create', array_merge(['admission' => $admission], $this->formData()));
    }

    public function store(AdmissionRequest $request): RedirectResponse
    {
        $data = $this->normalise($request->validated());
        $admission = Admission::create($data);

        return redirect()->route('admissions.index')
            ->with('success', "{$admission->patient->full_name} admitted to {$admission->ward->name}.");
    }

    public function show(Admission $admission): RedirectResponse
    {
        return redirect()->route('admissions.edit', $admission);
    }

    public function edit(Admission $admission): View
    {
        $admission->load(['patient', 'ward', 'doctor']);

        return view('admissions.edit', array_merge(['admission' => $admission], $this->formData()));
    }

    public function update(AdmissionRequest $request, Admission $admission): RedirectResponse
    {
        $admission->update($this->normalise($request->validated()));

        return redirect()->route('admissions.index')->with('success', 'Admission updated successfully.');
    }

    /**
     * Discharge a patient now (or at the provided date/time) and free the bed.
     */
    public function discharge(Request $request, Admission $admission): RedirectResponse
    {
        if (! $admission->isAdmitted()) {
            return back()->with('error', 'This patient has already been discharged.');
        }

        $validated = $request->validate([
            'discharged_at' => ['nullable', 'date', 'after_or_equal:'.$admission->admitted_at->format('Y-m-d H:i:s')],
        ], [
            'discharged_at.after_or_equal' => 'Discharge date must be on or after the admission date.',
        ]);

        $admission->update([
            'status' => Admission::STATUS_DISCHARGED,
            'discharged_at' => $validated['discharged_at'] ?? now(),
        ]);

        return redirect()->route('admissions.index')
            ->with('success', "{$admission->patient->full_name} has been discharged.");
    }

    public function destroy(Admission $admission): RedirectResponse
    {
        $admission->delete();

        return redirect()->route('admissions.index')->with('success', 'Admission record deleted.');
    }

    /** Keep status and discharge date consistent. */
    private function normalise(array $data): array
    {
        if ($data['status'] === Admission::STATUS_ADMITTED) {
            $data['discharged_at'] = null;
        }

        return $data;
    }

    private function formData(): array
    {
        return [
            'patients' => Patient::orderBy('first_name')->orderBy('last_name')->get(),
            'wards' => Ward::with('department')->withCount('activeAdmissions')->orderBy('name')->get(),
            'doctors' => Doctor::orderBy('name')->get(),
        ];
    }
}

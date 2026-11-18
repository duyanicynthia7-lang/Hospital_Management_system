<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRequest;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $departmentId = $request->integer('department_id') ?: null;

        $doctors = Doctor::query()
            ->with(['department', 'user'])
            ->withCount([
                'appointments as upcoming_count' => fn ($q) => $q->where('status', 'scheduled')->where('appointment_date', '>=', now()),
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('specialization', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $departments = Department::orderBy('name')->get();

        return view('doctors.index', compact('doctors', 'departments', 'search', 'departmentId'));
    }

    public function create(): View
    {
        return view('doctors.create', [
            'doctor' => new Doctor(['is_active' => true]),
            'departments' => Department::orderBy('name')->get(),
            'users' => $this->availableUsers(),
        ]);
    }

    public function store(DoctorRequest $request): RedirectResponse
    {
        $doctor = Doctor::create($request->validated());

        return redirect()->route('doctors.show', $doctor)
            ->with('success', "{$doctor->display_name} added successfully.");
    }

    public function show(Doctor $doctor): View
    {
        $doctor->load(['department', 'user', 'headedDepartments']);

        $upcomingAppointments = $doctor->appointments()
            ->with('patient')
            ->where('status', 'scheduled')
            ->where('appointment_date', '>=', now()->startOfDay())
            ->orderBy('appointment_date')
            ->limit(10)
            ->get();

        $currentAdmissions = $doctor->admissions()
            ->with(['patient', 'ward'])
            ->where('status', 'admitted')
            ->orderByDesc('admitted_at')
            ->get();

        $stats = [
            'total_appointments' => $doctor->appointments()->count(),
            'completed' => $doctor->appointments()->where('status', 'completed')->count(),
            'admitted' => $currentAdmissions->count(),
        ];

        return view('doctors.show', compact('doctor', 'upcomingAppointments', 'currentAdmissions', 'stats'));
    }

    public function edit(Doctor $doctor): View
    {
        return view('doctors.edit', [
            'doctor' => $doctor,
            'departments' => Department::orderBy('name')->get(),
            'users' => $this->availableUsers($doctor),
        ]);
    }

    public function update(DoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        $doctor->update($request->validated());

        return redirect()->route('doctors.show', $doctor)->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        if ($doctor->admissions()->exists()) {
            return back()->with('error', 'This doctor has admission records and cannot be deleted. Mark them as inactive instead.');
        }

        // Remove as head of any department before deleting
        $doctor->headedDepartments()->update(['head_doctor_id' => null]);
        $name = $doctor->display_name;
        $doctor->delete();

        return redirect()->route('doctors.index')->with('success', "{$name} deleted.");
    }

    /**
     * Users with the doctor role that are not yet linked to a doctor profile
     * (plus the currently linked user when editing).
     */
    private function availableUsers(?Doctor $doctor = null): Collection
    {
        return User::where('role', User::ROLE_DOCTOR)
            ->where(function ($q) use ($doctor) {
                $q->whereDoesntHave('doctor');
                if ($doctor?->user_id) {
                    $q->orWhere('id', $doctor->user_id);
                }
            })
            ->orderBy('name')
            ->get();
    }
}

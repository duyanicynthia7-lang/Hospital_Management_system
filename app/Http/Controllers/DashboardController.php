<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $doctor = $user->isDoctor() ? $user->doctor : null;

        // Core stats (visible to every role)
        $stats = [
            'patients' => Patient::count(),
            'doctors' => Doctor::where('is_active', true)->count(),
            'today_appointments' => Appointment::whereDate('appointment_date', today())->count(),
            'current_admissions' => Admission::where('status', Admission::STATUS_ADMITTED)->count(),
        ];

        // Role-specific extras
        $extra = [];
        if ($user->isAdmin()) {
            $wards = Ward::withCount('activeAdmissions')->get();
            $totalBeds = $wards->sum('capacity');
            $occupiedBeds = $wards->sum('active_admissions_count');
            $extra = [
                'users' => User::count(),
                'departments' => Department::count(),
                'total_beds' => $totalBeds,
                'occupied_beds' => $occupiedBeds,
                'bed_occupancy' => $totalBeds > 0 ? (int) round($occupiedBeds / $totalBeds * 100) : 0,
            ];
        } elseif ($doctor) {
            $extra = [
                'my_today' => $doctor->appointments()->whereDate('appointment_date', today())->count(),
                'my_upcoming' => $doctor->appointments()->where('status', Appointment::STATUS_SCHEDULED)->where('appointment_date', '>=', now())->count(),
                'my_patients_admitted' => $doctor->admissions()->where('status', Admission::STATUS_ADMITTED)->count(),
            ];
        } else {
            $extra = [
                'scheduled_total' => Appointment::where('status', Appointment::STATUS_SCHEDULED)->where('appointment_date', '>=', now())->count(),
                'new_patients_week' => Patient::where('created_at', '>=', now()->subDays(7))->count(),
            ];
        }

        // Recent appointments: doctors see their own, others see everything
        $recentAppointments = Appointment::with(['patient', 'doctor'])
            ->when($doctor, fn ($q) => $q->where('doctor_id', $doctor->id))
            ->orderByDesc('appointment_date')
            ->limit(8)
            ->get();

        return view('dashboard.index', compact('stats', 'extra', 'recentAppointments', 'doctor'));
    }
}

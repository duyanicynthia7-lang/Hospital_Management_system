<?php

namespace App\Http\Controllers;

use App\Http\Requests\WardRequest;
use App\Models\Department;
use App\Models\Ward;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WardController extends Controller
{
    public function index(): View
    {
        $wards = Ward::with('department')
            ->withCount('activeAdmissions')
            ->orderBy('name')
            ->paginate(12);

        // Hospital-wide occupancy summary
        $all = Ward::withCount('activeAdmissions')->get();
        $summary = [
            'wards' => $all->count(),
            'capacity' => $all->sum('capacity'),
            'occupied' => $all->sum('active_admissions_count'),
        ];
        $summary['available'] = max(0, $summary['capacity'] - $summary['occupied']);
        $summary['percent'] = $summary['capacity'] > 0 ? (int) round($summary['occupied'] / $summary['capacity'] * 100) : 0;

        return view('wards.index', compact('wards', 'summary'));
    }

    public function create(): View
    {
        return view('wards.create', [
            'ward' => new Ward(['type' => 'general', 'capacity' => 10]),
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function store(WardRequest $request): RedirectResponse
    {
        $ward = Ward::create($request->validated());

        return redirect()->route('wards.index')->with('success', "Ward {$ward->name} created.");
    }

    public function show(Ward $ward): RedirectResponse
    {
        return redirect()->route('wards.edit', $ward);
    }

    public function edit(Ward $ward): View
    {
        $ward->loadCount('activeAdmissions');
        $currentPatients = $ward->activeAdmissions()->with(['patient', 'doctor'])->orderBy('admitted_at')->get();

        return view('wards.edit', [
            'ward' => $ward,
            'departments' => Department::orderBy('name')->get(),
            'currentPatients' => $currentPatients,
        ]);
    }

    public function update(WardRequest $request, Ward $ward): RedirectResponse
    {
        $ward->update($request->validated());

        return redirect()->route('wards.index')->with('success', 'Ward updated successfully.');
    }

    public function destroy(Ward $ward): RedirectResponse
    {
        if ($ward->admissions()->exists()) {
            return back()->with('error', 'This ward has admission records and cannot be deleted.');
        }

        $name = $ward->name;
        $ward->delete();

        return redirect()->route('wards.index')->with('success', "Ward {$name} deleted.");
    }
}

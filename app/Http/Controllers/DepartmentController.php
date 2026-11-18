<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::with('headDoctor')
            ->withCount(['doctors', 'wards'])
            ->orderBy('name')
            ->paginate(10);

        return view('departments.index', compact('departments'));
    }

    public function create(): View
    {
        return view('departments.create', [
            'department' => new Department,
            'doctors' => Doctor::orderBy('name')->get(),
        ]);
    }

    public function store(DepartmentRequest $request): RedirectResponse
    {
        $department = Department::create($request->validated());

        return redirect()->route('departments.index')
            ->with('success', "Department {$department->name} created.");
    }

    public function show(Department $department): RedirectResponse
    {
        return redirect()->route('departments.edit', $department);
    }

    public function edit(Department $department): View
    {
        return view('departments.edit', [
            'department' => $department->load(['doctors', 'wards']),
            'doctors' => Doctor::orderBy('name')->get(),
        ]);
    }

    public function update(DepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        // Doctors and wards keep existing; their department_id is set to NULL by the foreign key.
        $name = $department->name;
        $department->delete();

        return redirect()->route('departments.index')->with('success', "Department {$name} deleted.");
    }
}

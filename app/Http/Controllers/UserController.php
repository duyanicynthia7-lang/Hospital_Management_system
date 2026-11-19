<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $role = $request->query('role');
        if (! array_key_exists((string) $role, User::ROLES)) {
            $role = null;
        }

        $users = User::query()
            ->with('doctor')
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
            ->when($role, fn ($q) => $q->where('role', $role))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'search', 'role'));
    }

    public function create(): View
    {
        return view('users.create', ['user' => new User(['role' => User::ROLE_RECEPTIONIST])]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $message = "User {$user->name} created.";
        if ($user->isDoctor()) {
            $message .= ' Remember to create/link a Doctor profile under Doctors.';
        }

        return redirect()->route('users.index')->with('success', $message);
    }

    public function show(User $user): RedirectResponse
    {
        return redirect()->route('users.edit', $user);
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

public function update(UserRequest $request, User $user): RedirectResponse
{
    $data = $request->validated();

    // Prevent an admin from demoting their own account.
    if ($user->is($request->user()) && $data['role'] !== User::ROLE_ADMIN) {
        return back()->withInput()->with(
            'error',
            'You cannot remove the admin role from your own account.'
        );
    }

    if (empty($data['password'])) {
        unset($data['password']);
    } else {
        $data['password'] = Hash::make($data['password']);
    }

    $result = DB::transaction(function () use ($user, $data) {
        $adminIds = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->orderBy('id')
            ->lockForUpdate()
            ->pluck('id');

        $lockedUser = User::query()
            ->lockForUpdate()
            ->findOrFail($user->id);

        if (
            $lockedUser->isAdmin()
            && $data['role'] !== User::ROLE_ADMIN
            && $adminIds->count() <= 1
        ) {
            return 'last_admin';
        }

        $lockedUser->update($data);

        return 'success';
    });

    if ($result === 'last_admin') {
        return back()->withInput()->with(
            'error',
            'You cannot remove or demote the last administrator.'
        );
    }

    return redirect()->route('users.index')
        ->with('success', 'User updated successfully.');
}



public function destroy(Request $request, User $user): RedirectResponse
{
    // Prevent users from deleting their own accounts.
    if ($user->is($request->user())) {
        return back()->with(
            'error',
            'You cannot delete your own account.'
        );
    }

    $result = DB::transaction(function () use ($user) {

        // Lock administrator records to protect the last admin.
        $adminIds = User::query()
            ->where('role', User::ROLE_ADMIN)
            ->orderBy('id')
            ->lockForUpdate()
            ->pluck('id');

        $lockedUser = User::query()
            ->lockForUpdate()
            ->findOrFail($user->id);

        // Prevent deletion of the last administrator.
        if (
            $lockedUser->isAdmin()
            && $adminIds->count() <= 1
        ) {
            return 'last_admin';
        }

        $lockedUser->delete();

        return 'success';
    });

    if ($result === 'last_admin') {
        return back()->with(
            'error',
            'You cannot delete the last administrator.'
        );
    }

    return redirect()->route('users.index')
        ->with('success', "User {$user->name} deleted.");
}

}

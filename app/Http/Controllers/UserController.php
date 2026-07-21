<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Rules\KathGovGhEmail;
use App\Models\AuditLog;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::query()->with(['role', 'team', 'ward'])->latest()->paginate(20),
        ]);
    }

    public function accessControl(): View
    {
        return view('users.access-control', [
            'users' => User::query()->with(['role', 'team', 'ward'])->orderBy('name')->get(),
            'roles' => Role::query()->orderBy('name')->get(),
            'teams' => Team::query()->orderBy('name')->get(),
            'wards' => Ward::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('users.create', [
            'roles' => Role::query()->orderBy('name')->get(),
            'teams' => Team::query()->orderBy('name')->get(),
            'wards' => Ward::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', new KathGovGhEmail(), 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $user = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
        ]);

        AuditLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'user_created',
            'description' => sprintf('User %s created', $user->email),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user->load(['role', 'team', 'ward']),
            'roles' => Role::query()->orderBy('name')->get(),
            'teams' => Team::query()->orderBy('name')->get(),
            'wards' => Ward::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', new KathGovGhEmail(), Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        AuditLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'user_updated',
            'description' => sprintf('User %s updated', $user->email),
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()?->id === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $email = $user->email;
        $user->delete();

        AuditLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'user_deleted',
            'description' => sprintf('User %s deleted', $email),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'User deleted successfully.');
    }
}

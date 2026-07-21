<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use App\Models\Team;
use App\Models\Ward;
use App\Rules\KathGovGhEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        $teams = Team::query()->orderBy('name')->get();
        $wards = Ward::query()->orderBy('name')->get();

        return view('auth.register', [
            'teams' => $teams,
            'wards' => $wards,
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', new KathGovGhEmail(), 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'ward_id' => ['required', 'integer', 'exists:wards,id'],
        ]);

        $role = Role::query()->where('name', 'Ward Staff')->firstOrFail();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role_id' => $role->id,
            'team_id' => $validated['team_id'],
            'ward_id' => $validated['ward_id'],
            'status' => 'active',
        ]);

        return redirect()->route('login')->with('success', 'Account created successfully. Please sign in.');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = (bool) ($credentials['remember'] ?? false);
        unset($credentials['remember']);
        $credentials['status'] = 'active';

        if (! Auth::attempt($credentials, $remember)) {
            AuditLog::create([
                'user_id' => null,
                'action' => 'failed_login_attempt',
                'description' => sprintf('Failed login attempt for %s', $request->string('email')),
                'ip_address' => $request->ip(),
            ]);

            throw ValidationException::withMessages([
                'email' => __('Invalid credentials or inactive account.'),
            ]);
        }

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'description' => 'User logged into KEPTS',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->intended($this->redirectForUser($user));
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user) {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'description' => 'User logged out of KEPTS',
                'ip_address' => $request->ip(),
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectForUser(User $user): string
    {
        return match (true) {
            $user->isAdmin() => route('admin.dashboard'),
            $user->isTriage() => route('triage.dashboard'),
            $user->isWard() => route('ward.dashboard', ['ward' => 'RED']),
            $user->isSpecialtyDoctor() => route('specialty.dashboard', ['team' => $user->team?->name ?? 'Emergency Medicine']),
            default => route('login'),
        };
    }
}

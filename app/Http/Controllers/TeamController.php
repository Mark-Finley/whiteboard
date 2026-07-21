<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        return view('teams.index', [
            'teams' => Team::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:teams,name'],
        ]);

        Team::create($validated);

        AuditLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'team_created',
            'description' => sprintf('Team %s created', $validated['name']),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Team created successfully.');
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:teams,name,' . $team->id],
        ]);

        $team->update($validated);

        AuditLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'team_updated',
            'description' => sprintf('Team %s updated', $team->name),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Team updated successfully.');
    }

    public function destroy(Request $request, Team $team): RedirectResponse
    {
        $teamName = $team->name;
        $team->delete();

        AuditLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'team_deleted',
            'description' => sprintf('Team %s deleted', $teamName),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Team deleted successfully.');
    }
}

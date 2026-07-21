<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\Team;
use App\Models\Ward;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\PatientMovementService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $patients = Patient::query()
            ->with(['ward', 'team', 'enteredBy', 'movements.fromWard', 'movements.toWard', 'movements.movedBy'])
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search');
                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery->where('ghims_number', 'like', "%{$search}%")
                        ->orWhere('patient_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $wards = Ward::query()->orderBy('name')->get();

        return view('patients.index', compact('patients', 'wards'));
    }

    public function create(): View
    {
        return view('patients.create', [
            'wards' => Ward::query()->orderBy('name')->get(),
            'teams' => Team::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ghims_number' => ['required', 'string', 'max:50', Rule::unique('patients', 'ghims_number')],
            'patient_name' => ['required', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:0', 'max:150'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'chief_complaint' => ['required', 'string', 'max:5000'],
            'nurse_notes' => ['nullable', 'string', 'max:5000'],
            'condition' => ['nullable', Rule::in(['critical', 'serious', 'moderate', 'stable'])],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'triage_outcome' => ['required', Rule::in(['alive', 'dead'])],
            'team_id' => ['nullable', 'exists:teams,id'],
            'team_ids' => ['nullable', 'array'],
            'team_ids.*' => ['exists:teams,id'],
        ]);

        DB::transaction(function () use ($request, $validated): void {
            $status = $validated['triage_outcome'] === 'dead' ? 'deceased' : 'active';

            // determine ward assignment: alive patients go to TRIAGE HOLDING
            $triageWardId = Ward::query()->where('name', Ward::TRIAGE_HOLDING)->value('id');

            $data = array_filter($validated, fn($k) => !in_array($k, ['triage_outcome', 'team_ids'], true), ARRAY_FILTER_USE_KEY);

            if ($status === 'active') {
                $data['ward_id'] = $triageWardId;
            } else {
                $data['ward_id'] = $data['ward_id'] ?? null;
            }

            // Sync backward-compatible team_id with the first of team_ids
            $teamIds = $request->input('team_ids', []);
            if (!empty($teamIds)) {
                $data['team_id'] = $teamIds[0];
            }

            $patient = Patient::create([
                ...$data,
                'age' => Carbon::parse($validated['date_of_birth'])->age,
                'condition' => $validated['condition'] ?? 'stable',
                'time_in' => now(),
                'status' => $status,
                'entered_by' => $request->user()?->id,
                'time_out' => $status === 'deceased' ? now() : null,
            ]);

            // Sync the many-to-many relationship
            if (!empty($teamIds)) {
                $patient->teams()->sync($teamIds);
            } elseif (!empty($data['team_id'])) {
                $patient->teams()->sync([$data['team_id']]);
            }

            $patient->load(['ward', 'team']);

            AuditLog::create([
                'user_id' => $request->user()?->id,
                'patient_id' => $patient->id,
                'ward_id' => $patient->ward_id,
                'team_id' => $patient->team_id,
                'action' => 'patient_created',
                'description' => sprintf(
                    'Patient %s registered into ward %s and team %s by %s',
                    $patient->ghims_number,
                    $patient->ward?->name ?? 'Unassigned',
                    $patient->team?->name ?? 'Unassigned',
                    $request->user()?->name ?? 'System'
                ),
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('patients.index')->with('success', 'Patient registered successfully.');
    }

    public function edit(Patient $patient): View
    {
        return view('patients.edit', [
            'patient' => $patient->load(['ward', 'team', 'teams']),
            'wards' => Ward::query()->orderBy('name')->get(),
            'teams' => Team::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'ghims_number' => ['required', 'string', 'max:50', Rule::unique('patients', 'ghims_number')->ignore($patient->id)],
            'patient_name' => ['required', 'string', 'max:255'],
            'age' => ['required', 'integer', 'min:0', 'max:150'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'chief_complaint' => ['required', 'string', 'max:5000'],
            'nurse_notes' => ['nullable', 'string', 'max:5000'],
            'condition' => ['required'],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'team_ids' => ['nullable', 'array'],
            'team_ids.*' => ['exists:teams,id'],
        ]);

        DB::transaction(function () use ($request, $patient, $validated): void {
            $data = array_filter($validated, fn($k) => $k !== 'team_ids', ARRAY_FILTER_USE_KEY);

            // Sync backward-compatible team_id with the first of team_ids
            $teamIds = $request->input('team_ids', []);
            if (!empty($teamIds)) {
                $data['team_id'] = $teamIds[0];
            } else {
                $data['team_id'] = $validated['team_id'] ?? null;
            }

            $patient->update([
                ...$data,
                'age' => Carbon::parse($validated['date_of_birth'])->age,
            ]);

            // Sync pivot
            if (!empty($teamIds)) {
                $patient->teams()->sync($teamIds);
            } elseif ($request->has('team_id')) {
                $patient->teams()->sync($data['team_id'] ? [$data['team_id']] : []);
            }

            $patient->load(['ward', 'team']);

            AuditLog::create([
                'user_id' => $request->user()?->id,
                'patient_id' => $patient->id,
                'ward_id' => $patient->ward_id,
                'team_id' => $patient->team_id,
                'action' => 'patient_updated',
                'description' => sprintf(
                    'Patient %s updated: ward %s, team %s by %s',
                    $patient->ghims_number,
                    $patient->ward?->name ?? 'Unassigned',
                    $patient->team?->name ?? 'Unassigned',
                    $request->user()?->name ?? 'System'
                ),
                'ip_address' => $request->ip(),
            ]);
        });

        return redirect()->route('patients.index')->with('success', 'Patient updated successfully.');
    }

    public function show(Patient $patient): View
    {
        $this->authorize('view', $patient);

        return view('patients.show', [
            'patient' => $patient->load(['ward', 'team', 'teams', 'enteredBy', 'movements.fromWard', 'movements.toWard', 'movements.movedBy']),
        ]);
    }

    public function cancel(Request $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        $validated = $request->validate([
            'cancel_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $patient->update([
            'status' => 'cancelled',
            'cancelled_reason' => $validated['cancel_reason'] ?? null,
        ]);

        AuditLog::create([
            'user_id' => $request->user()?->id,
            'patient_id' => $patient->id,
            'ward_id' => $patient->ward_id,
            'team_id' => $patient->team_id,
            'action' => 'patient_cancelled',
            'description' => sprintf('Patient %s cancelled by %s: %s', $patient->ghims_number, $request->user()?->name ?? 'System', $validated['cancel_reason'] ?? 'No reason provided'),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Patient record has been cancelled.');
    }

    public function redo(Request $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        if ($patient->status !== 'cancelled') {
            return back()->with('warning', 'Only cancelled patients can be reopened.');
        }

        $patient->update([
            'status' => 'active',
            'time_in' => now(),
            'cancelled_reason' => null,
        ]);

        AuditLog::create([
            'user_id' => $request->user()?->id,
            'patient_id' => $patient->id,
            'ward_id' => $patient->ward_id,
            'team_id' => $patient->team_id,
            'action' => 'patient_redone',
            'description' => sprintf('Patient %s reopened by %s', $patient->ghims_number, $request->user()?->name ?? 'System'),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Patient record has been reopened.');
    }

    public function discharge(Request $request, Patient $patient, PatientMovementService $movementService): RedirectResponse
    {
        $movementService->discharge($patient, $request->user()?->id, $request->ip(), $request->input('notes'));

        return back()->with('success', 'Patient discharged successfully.');
    }

    public function admit(Request $request, Patient $patient, PatientMovementService $movementService): RedirectResponse
    {
        $movementService->admit($patient, $request->user()?->id, $request->ip());

        return back()->with('success', 'Patient admitted successfully.');
    }

    public function markDeceased(Request $request, Patient $patient, PatientMovementService $movementService): RedirectResponse
    {
        $movementService->markDeceased($patient, $request->user()?->id, $request->ip(), $request->input('notes'));

        return back()->with('success', 'Patient marked as deceased.');
    }

    public function saveNotes(Request $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        $validated = $request->validate([
            'nurse_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $patient->update([
            'nurse_notes' => $validated['nurse_notes'] ?? null,
        ]);

        \App\Models\AuditLog::create([
            'user_id' => $request->user()?->id,
            'patient_id' => $patient->id,
            'ward_id' => $patient->ward_id,
            'team_id' => $patient->team_id,
            'action' => 'patient_notes_updated',
            'description' => sprintf('Nurse notes updated for %s by %s', $patient->ghims_number, $request->user()?->name ?? 'System'),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Notes saved.');
    }
}

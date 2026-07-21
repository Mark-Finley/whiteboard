<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Ward;
use App\Services\PatientMovementService;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function store(Request $request, Patient $patient, PatientMovementService $movementService)
    {
        $validated = $request->validate([
            'to_ward_id' => ['required', 'integer', 'exists:wards,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $toWard = Ward::query()->findOrFail($validated['to_ward_id']);

        $movementService->transfer(
            patient: $patient,
            toWard: $toWard,
            movedBy: $request->user()?->id,
            notes: $validated['notes'] ?? null,
            ipAddress: $request->ip(),
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Patient transferred successfully.']);
        }

        return back()->with('success', 'Patient transferred successfully.');
    }

    public function discharge(Request $request, Patient $patient, PatientMovementService $movementService)
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $movementService->discharge(
            $patient,
            $request->user()?->id,
            $request->ip(),
            $validated['notes'] ?? null,
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Patient discharged']);
        }

        return back()->with('success', 'Patient discharged successfully.');
    }
}

<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Patient;
use App\Models\PatientMovement;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Support\Facades\DB;

class PatientMovementService
{
    public function transfer(Patient $patient, Ward $toWard, ?int $movedBy, ?string $notes = null, ?string $ipAddress = null): void
    {
        $fromWard = $patient->ward;
        $this->assertAllowedMovement($fromWard?->name, $toWard->name);
        $this->assertConditionAllowsWard($patient->condition ?? 'stable', $toWard->name);

        DB::transaction(function () use ($patient, $fromWard, $toWard, $movedBy, $notes, $ipAddress): void {
            PatientMovement::create([
                'patient_id' => $patient->id,
                'from_ward_id' => $fromWard?->id,
                'to_ward_id' => $toWard->id,
                'moved_by' => $movedBy,
                'notes' => $notes,
            ]);

            $patient->update([
                'time_out' => now(),
                'ward_id' => $toWard->id,
                'status' => 'transferred',
                'time_in' => now(),
            ]);

            $movedByUser = optional(User::find($movedBy))->name ?? 'System';

            AuditLog::create([
                'user_id' => $movedBy,
                'patient_id' => $patient->id,
                'ward_id' => $toWard->id,
                'action' => 'patient_transferred',
                'description' => sprintf('%s moved from %s to %s by %s%s', $patient->ghims_number, $fromWard?->name ?? 'Unknown', $toWard->name, $movedByUser, $notes ? ' | Notes: '.$notes : ''),
                'ip_address' => $ipAddress,
            ]);

            // refresh and cache cumulative ward times
            $patient->refresh();
            $patient->recomputeWardTimeCache();
        });
    }

    public function discharge(Patient $patient, ?int $movedBy, ?string $ipAddress = null, ?string $notes = null): void
    {
        DB::transaction(function () use ($patient, $movedBy, $ipAddress, $notes): void {
            PatientMovement::create([
                'patient_id' => $patient->id,
                'from_ward_id' => $patient->ward_id,
                'to_ward_id' => null,
                'moved_by' => $movedBy,
                'notes' => $notes,
            ]);

            $patient->update([
                'status' => 'discharged',
                'time_out' => now(),
            ]);

            $currentWard = $patient->ward?->name ?? 'Unassigned';
            $movedByUser = optional(User::find($movedBy))->name ?? 'System';

            AuditLog::create([
                'user_id' => $movedBy,
                'patient_id' => $patient->id,
                'ward_id' => $patient->ward_id,
                'action' => 'patient_discharged',
                'description' => sprintf('Patient %s discharged from ward %s by %s%s', $patient->ghims_number, $currentWard, $movedByUser, $notes ? ' | Notes: '.$notes : ''),
                'ip_address' => $ipAddress,
            ]);

            $patient->refresh();
            $patient->recomputeWardTimeCache();
        });
    }

    public function admit(Patient $patient, ?int $movedBy, ?string $ipAddress = null): void
    {
        DB::transaction(function () use ($patient, $movedBy, $ipAddress): void {
            $patient->update([
                'status' => 'admitted',
                'time_in' => now(),
            ]);

            $currentWard = $patient->ward?->name ?? 'Unassigned';
            $movedByUser = optional(User::find($movedBy))->name ?? 'System';

            AuditLog::create([
                'user_id' => $movedBy,
                'patient_id' => $patient->id,
                'ward_id' => $patient->ward_id,
                'action' => 'patient_admitted',
                'description' => sprintf('Patient %s admitted into ward %s by %s', $patient->ghims_number, $currentWard, $movedByUser),
                'ip_address' => $ipAddress,
            ]);

            $patient->refresh();
            $patient->recomputeWardTimeCache();
        });
    }

    public function markDeceased(Patient $patient, ?int $movedBy, ?string $ipAddress = null, ?string $notes = null): void
    {
        DB::transaction(function () use ($patient, $movedBy, $ipAddress, $notes): void {
            PatientMovement::create([
                'patient_id' => $patient->id,
                'from_ward_id' => $patient->ward_id,
                'to_ward_id' => null,
                'moved_by' => $movedBy,
                'notes' => $notes,
            ]);

            $patient->update([
                'status' => 'deceased',
                'time_out' => now(),
            ]);

            AuditLog::create([
                'user_id' => $movedBy,
                'action' => 'patient_deceased',
                'description' => sprintf('Patient %s marked deceased%s', $patient->ghims_number, $notes ? ' | Notes: '.$notes : ''),
                'ip_address' => $ipAddress,
            ]);

            $patient->refresh();
            $patient->recomputeWardTimeCache();
        });
    }

    private function assertAllowedMovement(?string $fromWard, string $toWard): void
    {
        $colorWards = ['RED', 'ORANGE', 'YELLOW'];

        if ($fromWard === 'TRIAGE HOLDING' && in_array($toWard, $colorWards, true)) {
            return;
        }

        if (in_array($fromWard, $colorWards, true) && in_array($toWard, $colorWards, true) && $fromWard !== $toWard) {
            return;
        }

        abort(422, 'This ward movement is not allowed.');
    }

    private function assertConditionAllowsWard(string $condition, string $wardName): void
    {
        if ($condition === 'stable') {
            return;
        }

        $allowedWards = match ($condition) {
            'critical' => ['RED'],
            'serious' => ['RED', 'ORANGE'],
            'moderate' => ['ORANGE', 'YELLOW'],
            default => ['YELLOW', 'TRIAGE HOLDING'],
        };

        if (! in_array($wardName, $allowedWards, true)) {
            abort(422, sprintf('The patient condition %s does not allow transfer to %s.', $condition, $wardName));
        }
    }
}
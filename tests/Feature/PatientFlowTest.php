<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_triage_nurse_can_register_patient(): void
    {
        [$user] = $this->makeTriageContext();

        $response = $this->actingAs($user)->post('/patients', [
            'ghims_number' => 'GHIMS-1001',
            'patient_name' => 'John Doe',
            'age' => 36,
            'date_of_birth' => '1990-01-15',
            'chief_complaint' => 'Severe abdominal pain',
            'condition' => 'critical',
            'triage_outcome' => 'alive',
            'ward_id' => null,
            'team_id' => null,
        ]);

        $response->assertRedirect('/patients');
        $this->assertDatabaseHas('patients', [
            'ghims_number' => 'GHIMS-1001',
            'patient_name' => 'John Doe',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'patient_created',
        ]);
    }

    public function test_triage_nurse_can_transfer_patient_between_allowed_wards(): void
    {
        [$user, $wardHolding, $wardRed] = $this->makeTriageContext();
        $patient = Patient::query()->create([
            'ghims_number' => 'GHIMS-2001',
            'patient_name' => 'Jane Doe',
            'date_of_birth' => '1988-06-20',
            'age' => 37,
            'chief_complaint' => 'Chest pain',
            'condition' => 'critical',
            'ward_id' => $wardHolding->id,
            'team_id' => null,
            'status' => 'active',
            'time_in' => now(),
            'entered_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->post('/patients/'.$patient->id.'/movements', [
            'to_ward_id' => $wardRed->id,
            'notes' => 'Urgent transfer',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'ward_id' => $wardRed->id,
            'status' => 'transferred',
        ]);
        $this->assertDatabaseHas('patient_movements', [
            'patient_id' => $patient->id,
            'from_ward_id' => $wardHolding->id,
            'to_ward_id' => $wardRed->id,
        ]);
    }

    public function test_triage_nurse_can_discharge_and_admit_patient(): void
    {
        [$user, $wardHolding] = $this->makeTriageContext();
        $patient = Patient::query()->create([
            'ghims_number' => 'GHIMS-3001',
            'patient_name' => 'Mark Doe',
            'date_of_birth' => '1975-03-05',
            'age' => 51,
            'chief_complaint' => 'Head injury',
            'condition' => 'moderate',
            'ward_id' => $wardHolding->id,
            'team_id' => null,
            'status' => 'active',
            'time_in' => now(),
            'entered_by' => $user->id,
        ]);

        $this->actingAs($user)->post('/patients/'.$patient->id.'/discharge')
            ->assertRedirect();
        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'status' => 'discharged',
        ]);

        $this->actingAs($user)->post('/patients/'.$patient->id.'/admit')
            ->assertRedirect();
        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'status' => 'admitted',
        ]);
    }

    public function test_triage_nurse_can_mark_patient_deceased(): void
    {
        [$user, $wardHolding] = $this->makeTriageContext();
        $patient = Patient::query()->create([
            'ghims_number' => 'GHIMS-3002',
            'patient_name' => 'Deceased Patient',
            'date_of_birth' => '1965-07-10',
            'age' => 60,
            'chief_complaint' => 'Cardiac arrest',
            'condition' => 'critical',
            'ward_id' => $wardHolding->id,
            'team_id' => null,
            'status' => 'active',
            'time_in' => now(),
            'entered_by' => $user->id,
        ]);

        $this->actingAs($user)->post('/patients/'.$patient->id.'/deceased')
            ->assertRedirect();

        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'status' => 'deceased',
        ]);
    }

    private function makeTriageContext(): array
    {
        $adminRole = Role::query()->create(['name' => 'Admin']);
        $triageRole = Role::query()->create(['name' => 'Triage Nurse']);
        $team = Team::query()->create(['name' => 'Emergency Medicine']);
        $wardHolding = Ward::query()->create(['name' => Ward::TRIAGE_HOLDING, 'color_code' => Ward::TRIAGE_COLOR]);
        $wardRed = Ward::query()->create(['name' => Ward::RED, 'color_code' => Ward::RED_COLOR]);

        $user = User::query()->create([
            'name' => 'Triage Nurse',
            'email' => 'triage@test.local',
            'phone' => '233000000001',
            'password' => 'Password123!',
            'role_id' => $triageRole->id,
            'team_id' => $team->id,
            'status' => 'active',
        ]);

        return [$user, $wardHolding, $wardRed];
    }
}

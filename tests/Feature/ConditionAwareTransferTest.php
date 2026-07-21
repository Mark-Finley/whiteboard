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

class ConditionAwareTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_critical_patient_cannot_be_transferred_to_yellow(): void
    {
        [$user, $wardHolding, $wardYellow] = $this->makeTriageContext();
        $patient = Patient::query()->create([
            'ghims_number' => 'GHIMS-5001',
            'patient_name' => 'Critical Patient',
            'date_of_birth' => '1980-01-01',
            'age' => 46,
            'chief_complaint' => 'Multiple trauma',
            'condition' => 'critical',
            'ward_id' => $wardHolding->id,
            'team_id' => null,
            'status' => 'active',
            'time_in' => now(),
            'entered_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->post('/patients/'.$patient->id.'/movements', [
                'to_ward_id' => $wardYellow->id,
                'notes' => 'Should be rejected',
            ])
            ->assertStatus(422);

        $this->assertDatabaseHas('patients', [
            'id' => $patient->id,
            'ward_id' => $wardHolding->id,
            'status' => 'active',
        ]);
    }

    private function makeTriageContext(): array
    {
        $triageRole = Role::query()->create(['name' => 'Triage Nurse']);
        $team = Team::query()->create(['name' => 'Emergency Medicine']);
        $wardHolding = Ward::query()->create(['name' => Ward::TRIAGE_HOLDING, 'color_code' => Ward::TRIAGE_COLOR]);
        $wardYellow = Ward::query()->create(['name' => Ward::YELLOW, 'color_code' => Ward::YELLOW_COLOR]);

        $user = User::query()->create([
            'name' => 'Triage Nurse',
            'email' => 'triage@kath.gov.gh',
            'phone' => '233000000001',
            'password' => 'Password123!',
            'role_id' => $triageRole->id,
            'team_id' => $team->id,
            'status' => 'active',
        ]);

        return [$user, $wardHolding, $wardYellow];
    }
}

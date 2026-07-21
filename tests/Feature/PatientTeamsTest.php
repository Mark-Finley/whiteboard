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

class PatientTeamsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_can_assign_multiple_teams_to_patient_on_registration(): void
    {
        $triageNurseRole = Role::where('name', 'Triage Nurse')->firstOrFail();
        $nurse = $this->createUserWithRole($triageNurseRole, 'nurse@test.local');
        $ward = Ward::where('name', Ward::TRIAGE_HOLDING)->firstOrFail();

        $teamCardio = Team::where('name', 'Cardiology')->firstOrFail();
        $teamHaem = Team::where('name', 'Haematology')->firstOrFail();

        $response = $this->actingAs($nurse)->post('/patients', [
            'ghims_number' => 'GH-2026-9999',
            'patient_name' => 'Multi Team Patient',
            'age' => 45,
            'date_of_birth' => '1981-06-05',
            'triage_outcome' => 'alive',
            'ward_id' => $ward->id,
            'condition' => 'stable',
            'chief_complaint' => 'Shortness of breath',
            'team_ids' => [$teamCardio->id, $teamHaem->id],
        ]);

        $response->assertRedirect('/patients');
        
        $patient = Patient::where('ghims_number', 'GH-2026-9999')->firstOrFail();
        
        // Assert primary team is set to first team in array (backward compatibility)
        $this->assertEquals($teamCardio->id, $patient->team_id);
        
        // Assert both teams are present in many-to-many relationship
        $this->assertCount(2, $patient->teams);
        $this->assertTrue($patient->teams->contains($teamCardio));
        $this->assertTrue($patient->teams->contains($teamHaem));

        // Assert patient appears on Cardiology specialty dashboard API
        $cardioResponse = $this->actingAs($nurse)->getJson('/api/team-patients/Cardiology');
        $cardioResponse->assertOk();
        $cardioResponse->assertJsonFragment(['patient_name' => 'Multi Team Patient']);

        // Assert patient also appears on Haematology specialty dashboard API
        $haemResponse = $this->actingAs($nurse)->getJson('/api/team-patients/Haematology');
        $haemResponse->assertOk();
        $haemResponse->assertJsonFragment(['patient_name' => 'Multi Team Patient']);
    }

    public function test_can_update_multiple_teams_assignment(): void
    {
        $triageNurseRole = Role::where('name', 'Triage Nurse')->firstOrFail();
        $nurse = $this->createUserWithRole($triageNurseRole, 'nurse@test.local');
        $ward = Ward::where('name', Ward::TRIAGE_HOLDING)->firstOrFail();

        $teamCardio = Team::where('name', 'Cardiology')->firstOrFail();
        $teamHaem = Team::where('name', 'Haematology')->firstOrFail();
        $teamOnco = Team::where('name', 'Oncology')->firstOrFail();

        // Create patient assigned to Cardiology & Haematology
        $patient = Patient::create([
            'ghims_number' => 'GH-2026-8888',
            'patient_name' => 'Update Teams Patient',
            'date_of_birth' => '1990-01-01',
            'age' => 36,
            'chief_complaint' => 'Fever',
            'condition' => 'stable',
            'ward_id' => $ward->id,
            'team_id' => $teamCardio->id,
            'status' => 'active',
            'entered_by' => $nurse->id,
        ]);
        $patient->teams()->sync([$teamCardio->id, $teamHaem->id]);

        // Update to Haematology & Oncology
        $response = $this->actingAs($nurse)->put("/patients/{$patient->id}", [
            'ghims_number' => 'GH-2026-8888',
            'patient_name' => 'Update Teams Patient',
            'age' => 36,
            'date_of_birth' => '1990-01-01',
            'chief_complaint' => 'Fever',
            'condition' => 'stable',
            'ward_id' => $ward->id,
            'team_ids' => [$teamHaem->id, $teamOnco->id],
        ]);

        $response->assertRedirect('/patients');
        
        $patient->refresh();
        
        // Primary team updated to Haematology (first in new array)
        $this->assertEquals($teamHaem->id, $patient->team_id);
        
        // Many-to-many relationship updated
        $this->assertCount(2, $patient->teams);
        $this->assertTrue($patient->teams->contains($teamHaem));
        $this->assertTrue($patient->teams->contains($teamOnco));
        $this->assertFalse($patient->teams->contains($teamCardio));
    }

    private function createUserWithRole(Role $role, string $email): User
    {
        $team = Team::where('name', 'Emergency Medicine')->firstOrFail();
        $ward = Ward::where('name', Ward::TRIAGE_HOLDING)->firstOrFail();

        return User::create([
            'name' => 'Test User ' . $role->name,
            'email' => $email,
            'phone' => '233555' . rand(100000, 999999),
            'password' => bcrypt('Password123!'),
            'role_id' => $role->id,
            'team_id' => $team->id,
            'ward_id' => $ward->id,
            'status' => 'active',
        ]);
    }
}

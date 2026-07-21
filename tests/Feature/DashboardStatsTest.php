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

class DashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_correct_counts_after_transfer_and_discharge(): void
    {
        $adminRole = Role::query()->create(['name' => 'Admin']);
        $triageRole = Role::query()->create(['name' => 'Triage Nurse']);
        $team = Team::query()->create(['name' => 'Emergency Medicine']);

        $wardHolding = Ward::query()->create(['name' => Ward::TRIAGE_HOLDING, 'color_code' => Ward::TRIAGE_COLOR]);
        $wardRed = Ward::query()->create(['name' => Ward::RED, 'color_code' => Ward::RED_COLOR]);
        $wardYellow = Ward::query()->create(['name' => Ward::YELLOW, 'color_code' => Ward::YELLOW_COLOR]);

        $admin = User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.local',
            'phone' => '233000000000',
            'password' => 'Password123!',
            'role_id' => $adminRole->id,
            'team_id' => $team->id,
            'ward_id' => $wardHolding->id,
            'status' => 'active',
        ]);

        $triageUser = User::query()->create([
            'name' => 'Triage Nurse',
            'email' => 'triage@test.local',
            'phone' => '233000000001',
            'password' => 'Password123!',
            'role_id' => $triageRole->id,
            'team_id' => $team->id,
            'ward_id' => $wardHolding->id,
            'status' => 'active',
        ]);

        $admittedPatient = Patient::query()->create([
            'ghims_number' => 'GHIMS-5001',
            'patient_name' => 'Admitted Patient',
            'date_of_birth' => '1990-01-01',
            'age' => 34,
            'chief_complaint' => 'Pain',
            'condition' => 'moderate',
            'ward_id' => $wardHolding->id,
            'team_id' => $team->id,
            'status' => 'active',
            'time_in' => now(),
            'entered_by' => $triageUser->id,
        ]);

        $dischargedPatient = Patient::query()->create([
            'ghims_number' => 'GHIMS-5002',
            'patient_name' => 'Discharged Patient',
            'date_of_birth' => '1985-02-02',
            'age' => 39,
            'chief_complaint' => 'Fever',
            'condition' => 'serious',
            'ward_id' => $wardRed->id,
            'team_id' => $team->id,
            'status' => 'admitted',
            'time_in' => now(),
            'entered_by' => $triageUser->id,
        ]);

        // Simulate transfer: patient moves from TRIAGE HOLDING to YELLOW and becomes admitted.
        $admittedPatient->update(['ward_id' => $wardYellow->id, 'status' => 'admitted', 'time_in' => now()]);

        // Simulate discharge.
        $dischargedPatient->update(['status' => 'discharged', 'time_out' => now()]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('totalActivePatients', 1);
        $response->assertViewHas('yellowCount', 1);
        $response->assertViewHas('redCount', 0);
        $response->assertViewHas('dischargedToday', 1);
        $response->assertViewHas('admittedToday', 1);
    }
}

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

class PollingEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_live_polling_endpoints_return_active_patients(): void
    {
        $admin = $this->makeAdminUser();
        $red = Ward::query()->create(['name' => Ward::RED, 'color_code' => Ward::RED_COLOR]);
        $orange = Ward::query()->create(['name' => Ward::ORANGE, 'color_code' => Ward::ORANGE_COLOR]);
        $yellow = Ward::query()->create(['name' => Ward::YELLOW, 'color_code' => Ward::YELLOW_COLOR]);
        $triage = Ward::query()->create(['name' => Ward::TRIAGE_HOLDING, 'color_code' => Ward::TRIAGE_COLOR]);
        $team = Team::query()->create(['name' => 'Cardiology']);

        Patient::query()->create([
            'ghims_number' => 'GHIMS-4001',
            'patient_name' => 'Red Patient',
            'date_of_birth' => '1991-01-01',
            'age' => 35,
            'chief_complaint' => 'Severe bleeding',
            'condition' => 'critical',
            'ward_id' => $red->id,
            'team_id' => $team->id,
            'status' => 'active',
            'time_in' => now(),
            'entered_by' => $admin->id,
        ]);
        Patient::query()->create([
            'ghims_number' => 'GHIMS-4002',
            'patient_name' => 'Orange Patient',
            'date_of_birth' => '1987-02-02',
            'age' => 39,
            'chief_complaint' => 'Fracture',
            'condition' => 'serious',
            'ward_id' => $orange->id,
            'team_id' => $team->id,
            'status' => 'active',
            'time_in' => now(),
            'entered_by' => $admin->id,
        ]);
        Patient::query()->create([
            'ghims_number' => 'GHIMS-4003',
            'patient_name' => 'Yellow Patient',
            'date_of_birth' => '2000-03-03',
            'age' => 26,
            'chief_complaint' => 'Minor wound',
            'condition' => 'moderate',
            'ward_id' => $yellow->id,
            'team_id' => $team->id,
            'status' => 'active',
            'time_in' => now(),
            'entered_by' => $admin->id,
        ]);
        Patient::query()->create([
            'ghims_number' => 'GHIMS-4004',
            'patient_name' => 'Triage Patient',
            'date_of_birth' => '1995-04-04',
            'age' => 31,
            'chief_complaint' => 'Awaiting assignment',
            'condition' => 'stable',
            'ward_id' => $triage->id,
            'team_id' => $team->id,
            'status' => 'active',
            'time_in' => now(),
            'entered_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->getJson('/api/red-patients')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.ghims_number', 'GHIMS-4001');

        $this->actingAs($admin)
            ->getJson('/api/orange-patients')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.ghims_number', 'GHIMS-4002');

        $this->actingAs($admin)
            ->getJson('/api/yellow-patients')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.ghims_number', 'GHIMS-4003');

        $this->actingAs($admin)
            ->getJson('/api/triage-patients')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.ghims_number', 'GHIMS-4004');

        $this->actingAs($admin)
            ->getJson('/api/team-patients/Cardiology')
            ->assertOk()
            ->assertJsonCount(4, 'data');
    }

    private function makeAdminUser(): User
    {
        $role = Role::query()->create(['name' => 'Admin']);
        $team = Team::query()->create(['name' => 'Emergency Medicine']);

        return User::query()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.local',
            'phone' => '233000000000',
            'password' => 'Password123!',
            'role_id' => $role->id,
            'team_id' => $team->id,
            'status' => 'active',
        ]);
    }
}

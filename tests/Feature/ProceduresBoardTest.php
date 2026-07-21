<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\PatientInvestigation;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\Ward;
use App\Models\SystemNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProceduresBoardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Run seeders to populate roles, wards, teams, and master catalog
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_only_authorized_roles_can_assign_investigations(): void
    {
        $patient = $this->createTestPatient();

        // 1. Authorized user (Doctor role)
        $doctorRole = Role::where('name', 'Doctor')->firstOrFail();
        $doctor = $this->createUserWithRole($doctorRole, 'doctor@test.local');

        $response = $this->actingAs($doctor)->post("/patients/{$patient->id}/investigations", [
            'investigation_type_select' => 'ECG',
            'category' => 'Procedures',
            'priority' => 'Routine',
            'notes' => 'Perform ECG now',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('patient_investigations', [
            'patient_id' => $patient->id,
            'investigation_type' => 'ECG',
            'status' => 'Pending',
        ]);

        // 2. Unauthorized user (Ward Staff role)
        $wardRole = Role::where('name', 'Ward Staff')->firstOrFail();
        $wardStaff = $this->createUserWithRole($wardRole, 'wardstaff@test.local');

        $response = $this->actingAs($wardStaff)->post("/patients/{$patient->id}/investigations", [
            'investigation_type_select' => 'Full Blood Count (FBC)',
            'category' => 'Laboratory',
            'priority' => 'Urgent',
        ]);

        $response->assertStatus(403);
    }

    public function test_investigation_assignment_updates_db_and_logs(): void
    {
        $patient = $this->createTestPatient();
        $doctorRole = Role::where('name', 'Doctor')->firstOrFail();
        $doctor = $this->createUserWithRole($doctorRole, 'doctor@test.local');

        $this->actingAs($doctor)->postJson("/patients/{$patient->id}/investigations", [
            'investigation_type_select' => 'Chest X-Ray',
            'category' => 'Imaging',
            'priority' => 'Urgent',
            'notes' => 'Suspected pneumonia',
        ])->assertOk();

        // Assert tables updated
        $this->assertDatabaseHas('patient_investigations', [
            'patient_id' => $patient->id,
            'investigation_type' => 'Chest X-Ray',
            'category' => 'Imaging',
            'priority' => 'Urgent',
            'status' => 'Pending',
        ]);

        $investigation = PatientInvestigation::where('patient_id', $patient->id)->firstOrFail();

        $this->assertDatabaseHas('patient_investigation_updates', [
            'patient_investigation_id' => $investigation->id,
            'status' => 'Pending',
            'updated_by' => $doctor->id,
            'comments' => 'Initial assignment',
        ]);

        $this->assertDatabaseHas('system_notifications', [
            'patient_investigation_id' => $investigation->id,
            'type' => 'urgent',
            'is_read' => false,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'patient_id' => $patient->id,
            'action' => 'investigation_assigned',
        ]);
    }

    public function test_staff_can_progress_investigation_status(): void
    {
        $patient = $this->createTestPatient();
        $doctorRole = Role::where('name', 'Doctor')->firstOrFail();
        $doctor = $this->createUserWithRole($doctorRole, 'doctor@test.local');

        // Create an investigation
        $investigation = PatientInvestigation::create([
            'patient_id' => $patient->id,
            'investigation_type' => 'FBC',
            'category' => 'Laboratory',
            'priority' => 'Routine',
            'status' => 'Pending',
            'assigned_by' => $doctor->id,
            'assigned_at' => now(),
        ]);

        // Progress status to Sample Taken
        $this->actingAs($doctor)->postJson("/investigations/{$investigation->id}/status", [
            'status' => 'Sample Taken',
            'comments' => 'Blood sample drawn',
        ])->assertOk();

        $this->assertDatabaseHas('patient_investigations', [
            'id' => $investigation->id,
            'status' => 'Sample Taken',
        ]);

        $this->assertDatabaseHas('patient_investigation_updates', [
            'patient_investigation_id' => $investigation->id,
            'status' => 'Sample Taken',
            'comments' => 'Blood sample drawn',
        ]);

        // Progress status to Completed (triggers completed alert)
        $this->actingAs($doctor)->postJson("/investigations/{$investigation->id}/status", [
            'status' => 'Completed',
            'comments' => 'Results uploaded',
        ])->assertOk();

        $this->assertDatabaseHas('patient_investigations', [
            'id' => $investigation->id,
            'status' => 'Completed',
        ]);

        $this->assertDatabaseHas('system_notifications', [
            'patient_investigation_id' => $investigation->id,
            'type' => 'completed',
        ]);
    }

    public function test_procedures_board_and_notifications_json_endpoints(): void
    {
        $patient = $this->createTestPatient();
        $doctorRole = Role::where('name', 'Doctor')->firstOrFail();
        $doctor = $this->createUserWithRole($doctorRole, 'doctor@test.local');

        // Assign one routine investigation
        $investigation = PatientInvestigation::create([
            'patient_id' => $patient->id,
            'investigation_type' => 'Malaria Test',
            'category' => 'Laboratory',
            'priority' => 'Routine',
            'status' => 'Pending',
            'assigned_by' => $doctor->id,
            'assigned_at' => now(),
        ]);

        // Assert board JSON endpoint
        $response = $this->actingAs($doctor)->getJson('/api/procedures-board');
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'ghims_number',
                    'patient_name',
                    'investigations' => [
                        '*' => [
                            'id',
                            'investigation_type',
                            'category',
                            'priority',
                            'status',
                            'updates',
                        ]
                    ]
                ]
            ]
        ]);

        // Create an unread alert
        $notification = SystemNotification::create([
            'type' => 'assigned',
            'patient_id' => $patient->id,
            'patient_investigation_id' => $investigation->id,
            'message' => 'New investigation assigned',
            'is_read' => false,
        ]);

        // Assert notification polling
        $response = $this->actingAs($doctor)->getJson('/api/notifications/unread');
        $response->assertOk();
        $response->assertJsonPath('unread_count', 1);

        // Mark read
        $this->actingAs($doctor)->postJson("/api/notifications/{$notification->id}/read")->assertOk();
        $this->assertTrue($notification->fresh()->is_read);
    }

    public function test_patient_details_page_loads_successfully_with_investigations(): void
    {
        $patient = $this->createTestPatient();
        $adminRole = Role::where('name', 'Admin')->firstOrFail();
        $admin = $this->createUserWithRole($adminRole, 'admin-test-show@test.local');

        $investigation = PatientInvestigation::create([
            'patient_id' => $patient->id,
            'investigation_type' => 'Full Blood Count (FBC)',
            'category' => 'Laboratory',
            'priority' => 'Routine',
            'status' => 'Pending',
            'assigned_by' => $admin->id,
            'assigned_at' => now(),
        ]);

        $investigation->updates()->create([
            'status' => 'Pending',
            'updated_by' => $admin->id,
            'comments' => 'Initial assignment',
        ]);

        $response = $this->actingAs($admin)->get("/patients/{$patient->id}");
        $response->assertOk();
        $response->assertSee('Full Blood Count (FBC)', false);
        $response->assertSee('Pending', false);
    }

    private function createTestPatient(): Patient
    {
        $adminRole = Role::where('name', 'Admin')->firstOrFail();
        $admin = $this->createUserWithRole($adminRole, 'admin-test@test.local');
        $ward = Ward::where('name', Ward::RED)->firstOrFail();
        $team = Team::where('name', 'Emergency Medicine')->firstOrFail();

        return Patient::create([
            'ghims_number' => 'GHIMS-999-888',
            'patient_name' => 'John Doe',
            'date_of_birth' => '1980-01-01',
            'age' => 46,
            'chief_complaint' => 'Chest pain',
            'condition' => 'critical',
            'ward_id' => $ward->id,
            'team_id' => $team->id,
            'status' => 'active',
            'time_in' => now(),
            'entered_by' => $admin->id,
        ]);
    }

    private function createUserWithRole(Role $role, string $email): User
    {
        $team = Team::where('name', 'Emergency Medicine')->firstOrFail();
        $ward = Ward::where('name', Ward::RED)->firstOrFail();

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

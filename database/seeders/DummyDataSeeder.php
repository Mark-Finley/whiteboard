<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\PatientMovement;
use App\Models\Team;
use App\Models\User;
use App\Models\Ward;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        $wards = Ward::query()->get();
        $teams = Team::query()->get();
        $users = User::query()->get();

        if ($wards->isEmpty()) {
            $this->command->info('No wards found, run DatabaseSeeder first.');
            return;
        }

        $count = 50;
        for ($i = 1; $i <= $count; $i++) {
            $ghims = 'GHIMS-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            $name = $faker->name();
            $dob = Carbon::now()->subYears(rand(18, 85))->subDays(rand(0, 365));
            $timeIn = Carbon::now()->subHours(rand(0, 72))->subMinutes(rand(0, 59));

            $ward = $wards->random();
            $team = $teams->isNotEmpty() && rand(0, 1) ? $teams->random() : null;
            $enteredBy = $users->isNotEmpty() ? $users->random()->id : null;

            $status = rand(0, 9) < 8 ? 'active' : 'admitted';

            $patient = Patient::query()->create([
                'ghims_number' => $ghims,
                'patient_name' => $name,
                'date_of_birth' => $dob->toDateString(),
                'chief_complaint' => $faker->sentence(6),
                'condition' => $faker->randomElement(['critical', 'serious', 'moderate', 'stable']),
                'ward_id' => $ward->id,
                'team_id' => $team?->id,
                'status' => $status,
                'time_in' => $timeIn,
                'entered_by' => $enteredBy,
            ]);

            // optionally add a time_out (discharged) for some patients
            if (rand(0, 9) < 2) { // 20% discharged
                $patient->time_out = (clone $timeIn)->addHours(rand(1, 72))->addMinutes(rand(0, 59));
                $patient->status = 'discharged';
                $patient->saveQuietly();
            }

            // create 0-3 movements to simulate transfers
            $movementCount = rand(0, 3);
            $currentWard = $ward;
            $lastTimestamp = $timeIn;
            for ($m = 0; $m < $movementCount; $m++) {
                $toWard = $wards->random();
                // movement time between lastTimestamp and now/time_out
                $maxEnd = $patient->time_out ?? Carbon::now();
                $movementTime = (clone $lastTimestamp)->addHours(rand(1, 12))->addMinutes(rand(0, 59));
                if ($movementTime->greaterThan($maxEnd)) {
                    $movementTime = $maxEnd->subMinutes(rand(0, 30));
                }

                $movement = PatientMovement::query()->create([
                    'patient_id' => $patient->id,
                    'from_ward_id' => $currentWard->id,
                    'to_ward_id' => $toWard->id,
                    'moved_by' => $enteredBy,
                    'notes' => $faker->optional()->sentence(8),
                ]);

                // set created_at to movement time
                $movement->created_at = $movementTime;
                $movement->saveQuietly();

                $currentWard = $toWard;
                $lastTimestamp = $movementTime;
            }

            // recompute ward time cache
            $patient->recomputeWardTimeCache();
        }

        $this->command->info("Created {$count} dummy patients.");
    }
}

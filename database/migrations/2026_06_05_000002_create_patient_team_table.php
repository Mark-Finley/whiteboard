<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_team', function (Blueprint $table): void {
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('team_id')->constrained('teams')->onDelete('cascade');
            $table->primary(['patient_id', 'team_id']);
        });

        // Copy existing patient-team associations to pivot table
        $patients = DB::table('patients')->whereNotNull('team_id')->get(['id', 'team_id']);
        foreach ($patients as $patient) {
            DB::table('patient_team')->insertOrIgnore([
                'patient_id' => $patient->id,
                'team_id' => $patient->team_id,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_team');
    }
};

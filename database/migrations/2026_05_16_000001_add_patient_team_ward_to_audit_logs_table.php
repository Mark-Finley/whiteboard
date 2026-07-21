<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table): void {
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('ward_id')->nullable()->constrained('wards')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete()->cascadeOnUpdate();

            $table->index(['patient_id', 'ward_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('patient_id');
            $table->dropConstrainedForeignId('ward_id');
            $table->dropConstrainedForeignId('team_id');
        });
    }
};

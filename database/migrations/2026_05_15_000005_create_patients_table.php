<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table): void {
            $table->id();
            $table->string('ghims_number')->unique();
            $table->string('patient_name');
            $table->date('date_of_birth');
            $table->unsignedInteger('age');
            $table->text('chief_complaint');
            $table->enum('condition', ['critical', 'serious', 'moderate', 'stable'])->default('stable');
            $table->foreignId('ward_id')->nullable()->constrained('wards')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete()->cascadeOnUpdate();
            $table->enum('status', ['active', 'discharged', 'admitted', 'transferred', 'deceased'])->default('active');
            $table->timestamp('time_in')->useCurrent();
            $table->timestamp('time_out')->nullable();
            $table->foreignId('entered_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->index(['status', 'ward_id']);
            $table->index(['status', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};

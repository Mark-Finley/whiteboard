<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('from_ward_id')->nullable()->constrained('wards')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('to_ward_id')->nullable()->constrained('wards')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('moved_by')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_movements');
    }
};

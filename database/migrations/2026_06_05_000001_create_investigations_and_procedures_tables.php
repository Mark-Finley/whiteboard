<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investigation_catalog', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->enum('category', ['Laboratory', 'Imaging', 'Procedures']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('patient_investigations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('investigation_type');
            $table->enum('category', ['Laboratory', 'Imaging', 'Procedures']);
            $table->enum('priority', ['Routine', 'Urgent', 'Stat']);
            $table->enum('status', ['Pending', 'Sample Taken', 'Sent', 'In Progress', 'Completed', 'Cancelled'])->default('Pending');
            $table->text('notes')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at');
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('patient_investigation_updates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_investigation_id')->constrained('patient_investigations')->cascadeOnDelete();
            $table->string('status');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('comments')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('system_notifications', function (Blueprint $table): void {
            $table->id();
            $table->enum('type', ['assigned', 'completed', 'urgent']);
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('patient_investigation_id')->nullable()->constrained('patient_investigations')->cascadeOnDelete();
            $table->string('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_notifications');
        Schema::dropIfExists('patient_investigation_updates');
        Schema::dropIfExists('patient_investigations');
        Schema::dropIfExists('investigation_catalog');
    }
};

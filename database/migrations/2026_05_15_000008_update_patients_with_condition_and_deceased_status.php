<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('patients', function (Blueprint $table): void {
            if (! Schema::hasColumn('patients', 'condition')) {
                $table->enum('condition', ['critical', 'serious', 'moderate', 'stable'])
                    ->default('stable')
                    ->after('chief_complaint');
            }
        });

        DB::statement("ALTER TABLE patients MODIFY status ENUM('active','discharged','admitted','transferred','deceased') NOT NULL DEFAULT 'active'");
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE patients MODIFY status ENUM('active','discharged','admitted','transferred') NOT NULL DEFAULT 'active'");

        if (Schema::hasColumn('patients', 'condition')) {
            Schema::table('patients', function (Blueprint $table): void {
                $table->dropColumn('condition');
            });
        }
    }
};

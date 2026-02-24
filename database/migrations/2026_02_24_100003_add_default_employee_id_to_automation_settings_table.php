<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automation_settings', function (Blueprint $table) {
            $table->foreignId('default_employee_id')->nullable()->after('auto_assign_after_minutes')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('automation_settings', function (Blueprint $table) {
            $table->dropForeign(['default_employee_id']);
        });
    }
};

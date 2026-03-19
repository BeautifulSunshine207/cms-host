<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            if (! Schema::hasColumn('attendance_records', 'admin_response_name')) {
                $table->string('admin_response_name')->nullable()->after('remarks');
            }

            if (! Schema::hasColumn('attendance_records', 'admin_response_path')) {
                $table->string('admin_response_path')->nullable()->after('admin_response_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            if (Schema::hasColumn('attendance_records', 'admin_response_path')) {
                $table->dropColumn('admin_response_path');
            }

            if (Schema::hasColumn('attendance_records', 'admin_response_name')) {
                $table->dropColumn('admin_response_name');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('team_members', 'avatar')) {
            Schema::table('team_members', function (Blueprint $table) {
                $table->string('avatar')->nullable()->after('phone');
            });
        }
    }

    public function down(): void
    {
        // No-op: avatar may be an original schema column in existing databases.
    }
};

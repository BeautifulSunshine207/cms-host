<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('material_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id')->nullable();
            $table->enum('action', ['added', 'updated', 'deleted']);
            $table->longText('from_data')->nullable();
            $table->longText('to_data')->nullable();
            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('materials')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_histories');
    }
};

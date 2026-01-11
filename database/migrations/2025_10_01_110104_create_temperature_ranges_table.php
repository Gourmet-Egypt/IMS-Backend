<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('IMS_Temperature_ranges', function (Blueprint $table) {
            $table->id();
            $table->string('department');
            $table->string('min_temp')->nullable();
            $table->string('max_temp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('IMS_Temperature_ranges');
    }
};

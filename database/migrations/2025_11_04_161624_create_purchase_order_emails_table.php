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
        Schema::create('IMS_Purchase_order_emails', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();;
            $table->string('store_id');
            $table->string('employee_number')->nullable();
            $table->string('role')->nullable();
            $table->string('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('IMS_Purchase_order_emails');
    }
};

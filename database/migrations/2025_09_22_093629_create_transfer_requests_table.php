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
        Schema::create('IMS_Transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('other_store_id');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->string('status');
            $table->string('type');
            $table->date('delivery_date')->nullable();
            $table->timestamps();

            $table->index(['store_id', 'other_store_id']);
            $table->index('status');
            $table->index('delivery_date');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('IMS_Transfer_requests');
    }
};

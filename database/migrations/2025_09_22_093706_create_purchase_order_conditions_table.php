<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_order_conditions', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_order_id');
            $table->foreign('purchase_order_id')->references('ID')->on('PurchaseOrder')->onDelete('cascade');
            $table->string('vehicle_type');
            $table->decimal('vehicle_temp', 5, 2);
            $table->decimal('item_temp', 5, 2);
            $table->string('delivery_permit_number');
            $table->string('status');
            $table->text('notes')->nullable();
            $table->timestamps();


            $table->unique('purchase_order_id');
            $table->unique('delivery_permit_number');
            $table->index('delivery_permit_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_conditions');
    }
};

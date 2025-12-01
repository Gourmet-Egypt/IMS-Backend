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
        Schema::create('IMS_PurchaseOrder_Conditions', function (Blueprint $table) {
            $table->id();
            $table->integer('StoreID');
            $table->integer('purchase_order_id');
            $table->foreign('purchase_order_id')
                ->references('ID')
                ->on('PurchaseOrder')
                ->onDelete('cascade');
            $table->string('vehicle_type', 50);
            $table->decimal('vehicle_temp', 5, 2);
            $table->decimal('item_temp', 5, 2);
            $table->string('delivery_permit_number', 50);
            $table->string('status', 10)->nullable();
            $table->text('notes')->nullable();
            $table->string('seal_number', 50)->nullable();
            $table->timestamps();

            $table->unique('purchase_order_id');
            $table->unique('delivery_permit_number');
            $table->index('delivery_permit_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('IMS_PurchaseOrder_Conditions');
    }


};

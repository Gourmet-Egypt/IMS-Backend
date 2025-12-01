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
        Schema::create('IMS_PurchaseOrderEntry_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_order_entry_id');
            $table->foreign('purchase_order_entry_id')
                ->references('ID')
                ->on('PurchaseOrderEntry')
                ->onDelete('cascade');
            $table->decimal('quantity_issued', 10, 2);
            $table->date('production_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->increments('SN');
            $table->timestamps();

            $table->index(['production_date', 'expire_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('IMS_PurchaseOrderEntry_infos');
    }
};

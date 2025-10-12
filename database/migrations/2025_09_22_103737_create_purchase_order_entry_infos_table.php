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
        Schema::create('purchase_order_entry_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('purchase_order_entry_id');
            $table->foreign('purchase_order_entry_id')->references('ID')->on('PurchaseOrderEntry')->onDelete('cascade');
            $table->decimal('quantity_issued', 10, 2);
            $table->date('production_date');
            $table->date('expire_date');
            $table->timestamps();

            $table->index(['production_date', 'expire_date']);;
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_entry_infos');
    }
};

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
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedBigInteger('from_store_id');
            $table->unsignedBigInteger('to_store_id');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->string('status');
            $table->string('type');
            $table->date('delivery_date')->nullable();
            $table->timestamps();

            $table->index(['from_store_id', 'to_store_id']);
            $table->index('status');
            $table->index('delivery_date');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_requests');
    }
};

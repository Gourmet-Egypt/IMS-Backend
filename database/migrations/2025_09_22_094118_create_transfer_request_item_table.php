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
        Schema::create('transfer_request_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_request_id');
            $table->foreign('transfer_request_id')->references('id')->on('transfer_requests')->onDelete('cascade');
            $table->integer('item_id');
//            $table->foreign('item_id')->references('HQID')->on('Item')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();


            $table->unique(['transfer_request_id', 'item_id']);


            $table->index('transfer_request_id');
            $table->index('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_transfer_request');
    }
};

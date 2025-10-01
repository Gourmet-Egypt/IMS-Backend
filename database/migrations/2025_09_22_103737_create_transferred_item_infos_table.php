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
        Schema::create('transferred_item_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_transfer_request_id');
            $table->foreign('item_transfer_request_id')->references('id')->on('transfer_request_item')->onDelete('cascade');
            $table->decimal('quantity_on_hand', 10, 2);
            $table->decimal('quantity_issued', 10, 2);
            $table->date('production_date');
            $table->date('expire_date');
            $table->timestamps();

            $table->index(['production_date', 'expire_date']);;
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transferred_item_infos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations - Default Connection (IMS Database)
     */
    public function up(): void
    {
        // IMS Users Table
        Schema::create('IMS_Users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedBigInteger('store_id');
            $table->string('user_number');
            $table->foreign('user_number')->references('Number')->on('Cashier')->onDelete('cascade');
            $table->string('role');
            $table->integer('security_level')->default(4);
            $table->rememberToken();
            $table->timestamps();
        });

        // Transfer Requests Table
        Schema::create('transfer_requests', function (Blueprint $table) {
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

        // Transfer Request Item Table
        Schema::create('transfer_request_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_request_id');
            $table->foreign('transfer_request_id')->references('id')->on('transfer_requests')->onDelete('cascade');
            $table->integer('item_id');
            $table->decimal('quantity', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['transfer_request_id', 'item_id']);
            $table->index('transfer_request_id');
            $table->index('item_id');
        });

        // Purchase Order PDFs Table
        Schema::create('purchase_order_pdfs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->string('file_path');
            $table->string('file_name');
            $table->timestamps();
        });

        // Jobs Table
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('purchase_order_pdfs');
        Schema::dropIfExists('transfer_request_item');
        Schema::dropIfExists('transfer_requests');
        Schema::dropIfExists('IMS_Users');
    }
};

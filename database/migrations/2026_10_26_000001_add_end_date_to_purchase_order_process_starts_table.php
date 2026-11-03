<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('purchase_order_process_starts', 'end_date')) {
            return;
        }

        Schema::table('purchase_order_process_starts', function (Blueprint $table) {
            $table->dateTime('end_date')->nullable()->after('start_date');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_process_starts', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }
};

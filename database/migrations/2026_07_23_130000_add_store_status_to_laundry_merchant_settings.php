<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('laundry_merchant_settings', function (Blueprint $table) {
            $table->string('store_status')->default('open'); // open, closed, unattended
        });
    }

    public function down(): void
    {
        Schema::table('laundry_merchant_settings', function (Blueprint $table) {
            $table->dropColumn('store_status');
        });
    }
};

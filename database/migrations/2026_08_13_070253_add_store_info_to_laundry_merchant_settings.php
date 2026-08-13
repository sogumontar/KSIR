<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laundry_merchant_settings', function (Blueprint $table) {
            $table->string('store_name')->nullable()->after('user_id');
            $table->string('store_address')->nullable()->after('store_name');
            $table->string('header_bg_image')->nullable()->after('store_address');
        });
    }

    public function down(): void
    {
        Schema::table('laundry_merchant_settings', function (Blueprint $table) {
            $table->dropColumn(['store_name', 'store_address', 'header_bg_image']);
        });
    }
};


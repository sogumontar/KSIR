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
        Schema::table('users', function (Blueprint $table) {
            $table->string('store_name')->nullable();
            $table->text('store_description')->nullable();
            $table->string('public_email')->nullable();
            $table->string('support_phone')->nullable();
            $table->boolean('operating_status')->default(true);
            $table->string('timezone')->default('UTC');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['store_name', 'store_description', 'public_email', 'support_phone', 'operating_status', 'timezone']);
        });
    }
};

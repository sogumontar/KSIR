<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('sell_price', 15, 2)->nullable()->after('price');
            $table->decimal('profit', 15, 2)->nullable()->after('total_price');
        });

        // Backfill existing rows: sell_price = price (sold at cost), profit = 0
        DB::statement('UPDATE transactions SET sell_price = price, profit = 0 WHERE sell_price IS NULL');
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['sell_price', 'profit']);
        });
    }
};
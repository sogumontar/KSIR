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
            $table->boolean('menu_sales_record')->default(true);
            $table->boolean('menu_goods_inventory')->default(true);
            $table->boolean('menu_sales_monitoring')->default(true);
            $table->boolean('menu_expenses')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'menu_sales_record',
                'menu_goods_inventory',
                'menu_sales_monitoring',
                'menu_expenses'
            ]);
        });
    }
};

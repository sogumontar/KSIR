<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laundry_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('laundry_order_items', 'qty')) {
                $table->decimal('qty', 8, 2)->default(1)->after('price_snapshot');
            }
        });
    }

    public function down(): void
    {
        Schema::table('laundry_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('laundry_order_items', 'qty')) {
                $table->dropColumn('qty');
            }
        });
    }
};

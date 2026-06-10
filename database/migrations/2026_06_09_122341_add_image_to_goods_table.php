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
        Schema::table('goods', function (Blueprint $table) {
            $table->string('image')->nullable()->after('unit_type');
        });
    }

    public function down(): void
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};

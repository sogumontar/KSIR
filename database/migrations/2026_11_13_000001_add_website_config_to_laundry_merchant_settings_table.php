<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laundry_merchant_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('laundry_merchant_settings', 'website_slug')) {
                $table->string('website_slug')->nullable()->unique()->after('store_status');
            }
            if (!Schema::hasColumn('laundry_merchant_settings', 'website_enabled')) {
                $table->boolean('website_enabled')->default(true)->after('website_slug');
            }
            if (!Schema::hasColumn('laundry_merchant_settings', 'website_config')) {
                $table->json('website_config')->nullable()->after('website_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('laundry_merchant_settings', function (Blueprint $table) {
            $columns = [];
            foreach (['website_slug', 'website_enabled', 'website_config'] as $col) {
                if (Schema::hasColumn('laundry_merchant_settings', $col)) {
                    $columns[] = $col;
                }
            }
            if (!empty($columns)) {
                if (in_array('website_slug', $columns, true)) {
                    $table->dropUnique(['website_slug']);
                }
                $table->dropColumn($columns);
            }
        });
    }
};

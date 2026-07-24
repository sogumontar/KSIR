<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE laundry_orders DROP CONSTRAINT IF EXISTS laundry_orders_status_check');
            DB::statement("ALTER TABLE laundry_orders ADD CONSTRAINT laundry_orders_status_check CHECK (status::text = ANY (ARRAY['pending'::text, 'processing'::text, 'ready'::text, 'completed'::text, 'cancelled'::text]))");
        }
    }

    public function down(): void
    {
        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE laundry_orders DROP CONSTRAINT IF EXISTS laundry_orders_status_check');
            DB::statement("ALTER TABLE laundry_orders ADD CONSTRAINT laundry_orders_status_check CHECK (status::text = ANY (ARRAY['pending'::text, 'in_progress'::text, 'ready'::text, 'completed'::text, 'cancelled'::text]))");
        }
    }
};

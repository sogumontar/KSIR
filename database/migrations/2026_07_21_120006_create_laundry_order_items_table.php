<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundry_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laundry_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('laundry_service_id')->nullable()->constrained('laundry_services')->nullOnDelete();
            $table->string('service_name_snapshot');
            $table->decimal('price_snapshot', 15, 2);
            $table->string('treatment')->nullable();
            $table->date('date_in');
            $table->date('date_estimated_done');
            $table->boolean('is_free')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_order_items');
    }
};

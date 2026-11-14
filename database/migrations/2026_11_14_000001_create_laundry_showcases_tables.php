<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('laundry_showcases')) {
            Schema::create('laundry_showcases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('title');                       // e.g. "Unyellowing"
                $table->string('icon')->default('auto_awesome');
                $table->text('description')->nullable();
                $table->string('video_url', 500)->nullable();  // YouTube / TikTok / IG link (never uploaded)
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('laundry_showcase_photos')) {
            Schema::create('laundry_showcase_photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('laundry_showcase_id')->constrained('laundry_showcases')->cascadeOnDelete();
                $table->string('label')->nullable();           // e.g. "Nike Air Force 1"
                $table->string('before_path');                 // compressed file path on public disk (NOT blob in DB)
                $table->string('after_path');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_showcase_photos');
        Schema::dropIfExists('laundry_showcases');
    }
};

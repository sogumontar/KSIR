<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('laundry_store_contributors');
        Schema::create('laundry_store_contributors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('contributor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('invite_token')->unique();
            $table->string('invite_name')->nullable(); // display name for the invitation
            $table->enum('status', ['pending', 'accepted'])->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->unique(['owner_user_id', 'contributor_user_id'], 'lsc_owner_contributor_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundry_store_contributors');
    }
};

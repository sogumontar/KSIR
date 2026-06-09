<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Good;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'is_admin' => true,
            'status' => 'active',
            'created_at' => now(),
        ]);

        // Regular user for testing
        $testUser = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'is_admin' => false,
            'status' => 'active',
            'created_at' => now(),
        ]);

        // Seed some goods for the regular test user
        Good::factory()->count(10)->create([
            'user_id' => $testUser->id,
        ]);

        // Users from last month
        User::factory()->count(10)->create([
            'created_at' => now()->subMonth(),
        ]);

        // Users from this month
        User::factory()->count(25)->create([
            'created_at' => now(),
        ]);

        // Seed transactions (which will automatically query or create goods for each user)
        Transaction::factory()->count(50)->create();
    }
}

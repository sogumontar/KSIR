<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Good;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

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
            'created_at' => now()->subMonths(3),
        ]);

        // Seed goods for the test user
        Good::factory()->count(10)->create([
            'user_id' => $testUser->id,
        ]);

        // Create 35 regular users spread across 3 months
        $users = [];
        for ($i = 0; $i < 35; $i++) {
            $monthsBack = $i < 12 ? 3 : ($i < 24 ? 2 : 1);
            $users[] = User::factory()->create([
                'created_at' => now()->subMonths($monthsBack)->addDays(rand(0, 28)),
            ]);
        }

        // Seed goods for each user
        foreach ($users as $user) {
            Good::factory()->count(rand(3, 8))->create([
                'user_id' => $user->id,
            ]);
        }

        // Generate transactions: min 2 per week for 10 weeks across different months
        $allUsers = collect([$testUser])->merge($users);

        foreach ($allUsers as $user) {
            $userGoods = Good::where('user_id', $user->id)->get();

            if ($userGoods->isEmpty()) {
                continue;
            }

            // 10 weeks spanning ~3 months
            for ($week = 9; $week >= 0; $week--) {
                $weekStart = now()->subWeeks($week)->startOfWeek(Carbon::MONDAY);

                // Minimum 2 orders per week, randomize up to 5
                $ordersThisWeek = rand(2, 5);

                for ($j = 0; $j < $ordersThisWeek; $j++) {
                    $good = $userGoods->random();
                    $qty = rand(1, min(10, $good->stock));
                    $transactionDate = $weekStart->addDays(rand(0, 6))->addHours(rand(8, 18));

                    Transaction::create([
                        'user_id' => $user->id,
                        'good_id' => $good->id,
                        'transaction_date' => $transactionDate,
                        'item_name' => $good->name,
                        'recipient_name' => fake()->name(),
                        'quantity' => $qty,
                        'price' => $good->price,
                        'total_price' => $qty * $good->price,
                        'status' => fake()->randomElement(['pending', 'transit', 'delivered', 'loan', 'failed']),
                        'description' => fake()->sentence(),
                    ]);
                }
            }
        }
    }
}
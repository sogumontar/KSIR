<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Good;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 10);
        $userId = User::inRandomOrder()->first()?->id ?? User::factory();
        
        // Ensure there are goods for this user
        $good = Good::where('user_id', $userId)->inRandomOrder()->first();
        if (!$good) {
            $good = Good::factory()->create(['user_id' => $userId]);
        }

        return [
            'user_id' => $userId,
            'good_id' => $good->id,
            'transaction_date' => fake()->dateTimeBetween('-2 months', 'now'),
            'item_name' => $good->name,
            'recipient_name' => fake()->name(),
            'quantity' => $qty,
            'price' => $good->price,
            'total_price' => $qty * $good->price,
            'status' => fake()->randomElement(['pending', 'transit', 'delivered', 'loan', 'failed']),
            'description' => fake()->sentence(),
        ];
    }
}

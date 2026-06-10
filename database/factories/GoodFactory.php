<?php

namespace Database\Factories;

use App\Models\Good;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoodFactory extends Factory
{
    protected $model = Good::class;

    public function definition(): array
    {
        $names = [
            'MacBook Pro M3 Max', 'ThinkPad T14 Gen 4', 'Dell XPS 15',
            'iPhone 15 Pro', 'Samsung Galaxy S24 Ultra', 'iPad Pro 12.9',
            'Logitech MX Master 3S', 'Sony WH-1000XM5 Headphones',
            'Dell 27 Monitor U2723QE', 'Keychron K2 Mechanical Keyboard'
        ];

        $unitTypes = ['pcs', 'box', 'pack', 'set', 'kg', 'liter', 'bundle', 'roll', 'drum', 'unit'];

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'name' => fake()->randomElement($names) . ' ' . fake()->unique()->bothify('??-###'),
            'price' => fake()->randomFloat(2, 50, 2500),
            'stock' => fake()->numberBetween(10, 100),
            'unit_type' => fake()->randomElement($unitTypes),
            'description' => fake()->paragraph(),
        ];
    }
}
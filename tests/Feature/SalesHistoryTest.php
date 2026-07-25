<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SalesHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_history_sorting_and_trends()
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'is_admin' => false,
            'menu_sales_record' => true,
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'transaction_date' => now()->subDay(),
            'item_name' => 'Widget A',
            'recipient_name' => 'Customer 1',
            'quantity' => 5,
            'price' => 10000,
            'sell_price' => 15000,
            'total_price' => 75000,
            'profit' => 25000,
            'status' => 'completed',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'transaction_date' => now(),
            'item_name' => 'Widget B',
            'recipient_name' => 'Customer 2',
            'quantity' => 10,
            'price' => 20000,
            'sell_price' => 30000,
            'total_price' => 300000,
            'profit' => 100000,
            'status' => 'completed',
        ]);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\User\SalesHistory::class)
            ->call('sort', 'total_price')
            ->assertSet('sortColumn', 'total_price')
            ->assertSet('sortDirection', 'asc')
            ->call('sort', 'total_price')
            ->assertSet('sortDirection', 'desc')
            ->assertViewHas('unitChartValues')
            ->assertViewHas('chartValues')
            ->assertViewHas('realTotalUnits', 15)
            ->assertHasNoErrors();
    }
}

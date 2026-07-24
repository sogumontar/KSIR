<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Good;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_merchant_can_configure_storefront()
    {
        // Setup a merchant (staff member)
        $merchant = User::factory()->create([
            'is_admin' => false,
            'role' => 'staff',
            'store_name' => 'Original Name',
            'public_email' => 'old@store.com',
            'category' => 'hardware',
            'operating_status' => false,
        ]);

        $this->actingAs($merchant);

        // Test the storefront config page
        Livewire::test(\App\Livewire\Merchant\StorefrontConfig::class)
            ->assertSet('store_name', 'Original Name')
            ->assertSet('public_email', 'old@store.com')
            ->set('store_name', 'Apex Hardware Ltd.')
            ->set('store_description', 'High-quality components.')
            ->set('public_email', 'sales@apexhardware.com')
            ->set('category', 'hardware')
            ->set('operating_status', true)
            ->call('save')
            ->assertHasNoErrors();

        // Assert database changes
        $this->assertDatabaseHas('users', [
            'id' => $merchant->id,
            'store_name' => 'Apex Hardware Ltd.',
            'store_description' => 'High-quality components.',
            'public_email' => 'sales@apexhardware.com',
            'category' => 'hardware',
            'operating_status' => true,
        ]);
    }

    public function test_customer_can_view_linked_merchant_storefront()
    {
        // Setup merchant (staff)
        $merchant = User::factory()->create([
            'is_admin' => false,
            'role' => 'staff',
            'store_name' => 'Apex Hardware Ltd.',
            'unique_code' => 'APEXCORP',
            'operating_status' => true,
        ]);

        // Add a product
        $good = Good::create([
            'user_id' => $merchant->id,
            'name' => 'Steel Bracket',
            'price' => 15000,
            'stock' => 10,
            'is_visible' => true,
        ]);

        // Setup customer
        $customer = User::factory()->create([
            'is_admin' => false,
            'role' => 'customer',
        ]);

        // Link customer and merchant (via pivot)
        $customer->merchants()->attach($merchant->id);

        $this->actingAs($customer);

        // Access the customer storefront page
        $response = $this->get(route('customer.storefront', $merchant->unique_code));
        $response->assertStatus(200);

        // Test Livewire component renders goods
        Livewire::test(\App\Livewire\Customer\Storefront::class, ['merchantToken' => $merchant->unique_code])
            ->assertSee('Steel Bracket')
            ->assertSee('Rp 15.000')
            ->call('addToCart', $good->id)
            ->assertHasNoErrors();

        // Check if item was added to session cart
        $this->assertEquals(1, session('cart')[$good->id]['quantity']);
    }

    public function test_customer_cannot_view_unlinked_merchant_storefront()
    {
        $merchant = User::factory()->create([
            'is_admin' => false,
            'role' => 'staff',
            'unique_code' => 'UNLINKED',
        ]);

        $customer = User::factory()->create([
            'is_admin' => false,
            'role' => 'customer',
        ]);

        $this->actingAs($customer);

        // Attempting to visit unlinked merchant storefront should return 404
        $response = $this->get(route('customer.storefront', $merchant->unique_code));
        $response->assertStatus(404);
    }
}

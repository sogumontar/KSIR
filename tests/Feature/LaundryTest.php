<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\LaundryService;
use App\Models\LaundryOrder;
use App\Models\LaundryPromo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LaundryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_laundry_permission_cannot_access_laundry_routes()
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'is_admin' => false,
            'menu_laundry' => false,
        ]);

        $response = $this->actingAs($user)->get(route('laundry.dashboard'));
        // Even if request goes through layout check, layout checks isAuthorized and renders access restricted template
        $response->assertStatus(200);
        $response->assertSee('Access Restricted');
    }

    public function test_user_with_laundry_permission_can_access_laundry_dashboard()
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'is_admin' => false,
            'menu_laundry' => true,
        ]);

        $response = $this->actingAs($user)->get(route('laundry.dashboard'));
        $response->assertStatus(200);
        $response->assertDontSee('Access Restricted');
        $response->assertSee('Laundry Dashboard');
    }

    public function test_laundry_service_crud()
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'is_admin' => false,
            'menu_laundry' => true,
        ]);

        $this->actingAs($user);

        // Test create service
        Livewire::test(\App\Livewire\Laundry\ServiceManager::class)
            ->set('name', 'Shoe Cleaning')
            ->set('price', 50000)
            ->set('description', 'Deep clean shoes')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('laundry_services', [
            'user_id' => $user->id,
            'name' => 'Shoe Cleaning',
            'price' => 50000.00,
        ]);
    }

    public function test_public_tracking_works()
    {
        $merchant = User::factory()->create([
            'role' => 'staff',
            'is_admin' => false,
        ]);

        $order = LaundryOrder::create([
            'user_id' => $merchant->id,
            'customer_name' => 'John Doe',
            'customer_phone' => '08123456789',
            'delivery_type' => 'pickup',
            'status' => 'pending',
            'subtotal' => 100000,
            'total_amount' => 100000,
        ]);

        // Access public tracking using uuid tracking_code
        $response = $this->get(route('laundry.public.track', $order->tracking_code));
        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertSee($order->order_code);
    }

    public function test_merchant_can_save_settings_with_qr_code()
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'is_admin' => false,
            'menu_laundry' => true,
        ]);

        $this->actingAs($user);

        \Storage::fake('public');
        $file = \Illuminate\Http\UploadedFile::fake()->image('qr.png');

        Livewire::test(\App\Livewire\Laundry\Settings::class)
            ->set('qrCode', $file)
            ->set('paymentNotes', 'Test notes BCA 1234')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('laundry_merchant_settings', [
            'user_id' => $user->id,
            'payment_notes' => 'Test notes BCA 1234',
        ]);
        
        $setting = \App\Models\LaundryMerchantSetting::where('user_id', $user->id)->first();
        $this->assertNotEmpty($setting->qr_code_path);
        \Storage::disk('public')->assertExists($setting->qr_code_path);
    }

    public function test_laundry_promo_discount_calculation_and_order_creation()
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'is_admin' => false,
            'menu_laundry' => true,
        ]);

        $service = LaundryService::create([
            'user_id' => $user->id,
            'name' => 'Wash & Fold',
            'price' => 50000,
            'is_active' => true,
        ]);

        $promo = LaundryPromo::create([
            'user_id' => $user->id,
            'name' => '10% Discount',
            'type' => 'percentage',
            'discount_percent' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($user);

        Livewire::test(\App\Livewire\Laundry\CreateOrder::class)
            ->set('customerName', 'Jane Doe')
            ->set('items.0.service_id', $service->id)
            ->set('items.0.price', 50000)
            ->set('items.0.date_in', now()->format('Y-m-d'))
            ->set('items.0.date_estimated_done', now()->addDays(2)->format('Y-m-d'))
            ->set('selectedPromoId', $promo->id)
            ->assertSet('subtotal', 50000)
            ->assertSet('discountAmount', 5000)
            ->assertSet('total', 45000)
            ->call('submit')
            ->assertHasNoErrors();

        $createdOrder = LaundryOrder::where('customer_name', 'Jane Doe')->first();
        $this->assertNotNull($createdOrder);
        $this->assertEquals(45000, $createdOrder->items->first()->final_price);
    }
}

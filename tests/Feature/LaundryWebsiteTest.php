<?php

namespace Tests\Feature;

use App\Livewire\Laundry\PublicLandingPage;
use App\Livewire\Laundry\WebsiteManager;
use App\Models\LaundryMerchantSetting;
use App\Models\LaundryOrder;
use App\Models\LaundryOrderItem;
use App\Models\LaundryService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LaundryWebsiteTest extends TestCase
{
    use RefreshDatabase;

    private function makeOwner(): User
    {
        return User::factory()->create([
            'is_admin'     => false,
            'menu_laundry' => true,
        ]);
    }

    public function test_public_cleanlab_landing_page_is_accessible_without_login_and_isolated_from_ksir(): void
    {
        $owner = $this->makeOwner();

        LaundryMerchantSetting::create([
            'user_id'         => $owner->id,
            'store_name'      => 'CLEAN LAB',
            'website_slug'    => 'clean-lab',
            'website_enabled' => true,
            'website_config'  => LaundryMerchantSetting::defaultWebsiteConfig(),
        ]);

        $response = $this->get('/cleanlab');

        $response->assertStatus(200);
        $response->assertSee('CLEAN LAB');
        $response->assertSee('Shoes (Sepatu)');
        $response->assertSee('Sandals (Sandal)');
        $response->assertSee('Helmets (Helm)');
        $response->assertSee('Bags & Caps');
        $response->assertSee('Fast Cleaning');
        $response->assertSee('Unyellowing');
        $response->assertSee('https://maps.app.goo.gl/7KEhxvXJmQnWpAbz6');
        $response->assertSee('Menara Cawang');

        // Must NOT contain KSIR internal navigation or branding
        $response->assertDontSee('Inventory Pro');
        $response->assertDontSee(route('user.dashboard'));
        $response->assertDontSee(route('laundry.dashboard'));
    }

    public function test_custom_slug_landing_page_works_for_guests(): void
    {
        $owner = $this->makeOwner();

        $config = LaundryMerchantSetting::defaultWebsiteConfig();
        $config['brand_name'] = 'Clean Lab Sneaker Spa';

        LaundryMerchantSetting::create([
            'user_id'         => $owner->id,
            'store_name'      => 'Clean Lab Sneaker Spa',
            'website_slug'    => 'clean-lab-jakarta',
            'website_enabled' => true,
            'website_config'  => $config,
        ]);

        $response = $this->get('/s/clean-lab-jakarta');
        $response->assertStatus(200);
        $response->assertSee('Clean Lab Sneaker Spa');
    }

    public function test_dynamic_promo_banners_with_discount_validity_and_tnc_render_on_public_page(): void
    {
        $owner = $this->makeOwner();

        Livewire::actingAs($owner)
            ->test(WebsiteManager::class)
            ->set('promoBanners', [
                [
                    'is_active'     => true,
                    'logo_path'     => null,
                    'badge'         => '🔥 PROMO AKHIR PEKAN',
                    'title'         => 'Cuci 2 Pasang Sepatu Potongan Spesial',
                    'description'   => 'Berlaku untuk Deep Cleaning dan Unyellowing',
                    'discount_text' => 'POTONGAN RP 20.000',
                    'valid_from'    => '2026-11-01',
                    'valid_until'   => '2026-12-31',
                    'tnc'           => 'Minimal transaksi 2 pasang sepatu.',
                ],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $response = $this->get('/cleanlab');
        $response->assertStatus(200);
        $response->assertSee('PROMO AKHIR PEKAN');
        $response->assertSee('Cuci 2 Pasang Sepatu Potongan Spesial');
        $response->assertSee('POTONGAN RP 20.000');
        $response->assertSee('Minimal transaksi 2 pasang sepatu.');
    }

    public function test_guest_can_track_order_inline_on_cleanlab_page(): void
    {
        $owner = $this->makeOwner();

        LaundryMerchantSetting::create([
            'user_id'         => $owner->id,
            'store_name'      => 'CLEAN LAB',
            'website_slug'    => 'clean-lab',
            'website_enabled' => true,
            'website_config'  => LaundryMerchantSetting::defaultWebsiteConfig(),
        ]);

        $service = LaundryService::create([
            'user_id'    => $owner->id,
            'name'       => 'Deep Cleaning Shoes',
            'short_code' => 'SHOES',
            'price'      => 50000,
            'is_active'  => true,
        ]);

        $order = LaundryOrder::create([
            'user_id'         => $owner->id,
            'customer_name'   => 'Budi Santoso',
            'customer_phone'  => '08123456789',
            'payment_status'  => 'paid',
            'delivery_type'   => 'pickup',
            'status'          => 'processing',
            'subtotal'        => 50000,
            'discount_amount' => 0,
            'total_amount'    => 50000,
        ]);

        LaundryOrderItem::create([
            'laundry_order_id'      => $order->id,
            'laundry_service_id'    => $service->id,
            'service_name_snapshot' => $service->name,
            'treatment'             => 'Nike Air Force 1 White',
            'date_in'               => now()->toDateString(),
            'date_estimated_done'   => now()->addDays(2)->toDateString(),
            'price_snapshot'        => 50000,
            'final_price'           => 50000,
            'qty'                   => 1,
            'is_free'               => false,
        ]);

        Livewire::test(PublicLandingPage::class)
            ->set('trackingInput', $order->order_code)
            ->call('checkOrder')
            ->assertSee('Budi Santoso')
            ->assertSee('Deep Cleaning Shoes')
            ->assertSee('LUNAS');
    }

    public function test_owner_can_update_website_settings_via_website_manager(): void
    {
        $owner = $this->makeOwner();

        Livewire::actingAs($owner)
            ->test(WebsiteManager::class)
            ->set('brandName', 'Clean Lab Official')
            ->set('heroTitle', 'Spesialis Cuci Sepatu, Tas, Helm & Koper Terbaik')
            ->set('whatsappNumber', '6285139763366')
            ->set('googleMapsLink', 'https://maps.app.goo.gl/7KEhxvXJmQnWpAbz6')
            ->call('save')
            ->assertHasNoErrors();

        $setting = LaundryMerchantSetting::where('user_id', $owner->id)->first();
        $this->assertNotNull($setting);
        $this->assertEquals('Clean Lab Official', $setting->website_config['brand_name']);
        $this->assertEquals('Spesialis Cuci Sepatu, Tas, Helm & Koper Terbaik', $setting->website_config['hero_title']);
        $this->assertEquals('https://maps.app.goo.gl/7KEhxvXJmQnWpAbz6', $setting->website_config['google_maps_link']);

        $this->get('/cleanlab')
            ->assertStatus(200)
            ->assertSee('Clean Lab Official')
            ->assertSee('Spesialis Cuci Sepatu, Tas, Helm & Koper Terbaik');
    }
}

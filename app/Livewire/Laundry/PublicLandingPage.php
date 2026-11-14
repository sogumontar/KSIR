<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryMerchantSetting;
use App\Models\LaundryOrder;
use App\Models\LaundryPromo;
use App\Models\LaundryService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.cleanlab')]
#[Title('Clean Lab — Spesialis Cuci Sepatu, Tas, Sandal, Helm & Koper')]
class PublicLandingPage extends Component
{
    public ?LaundryMerchantSetting $setting = null;
    public array $config = [];
    public string $storeStatus = 'open';

    #[Url(as: 'nota')]
    public string $trackingInput = '';

    public ?LaundryOrder $trackedOrder = null;
    public bool $hasSearched = false;
    public string $activeCategoryFilter = 'all';

    public function mount(?string $slug = null): void
    {
        if ($slug) {
            $this->setting = LaundryMerchantSetting::where('website_slug', $slug)
                ->where('website_enabled', true)
                ->first();

            if (!$this->setting && $slug === 'clean-lab') {
                $this->setting = LaundryMerchantSetting::where('website_enabled', true)
                    ->orderBy('id')
                    ->first();
            }

            if (!$this->setting && $slug !== 'clean-lab') {
                abort(404);
            }
        } else {
            $this->setting = LaundryMerchantSetting::where('website_slug', 'clean-lab')
                ->where('website_enabled', true)
                ->first()
                ?? LaundryMerchantSetting::where('website_enabled', true)->orderBy('id')->first();
        }

        if ($this->setting && !$this->setting->website_enabled) {
            abort(404);
        }

        $this->config = $this->setting
            ? $this->setting->getWebsiteConfigWithDefaults()
            : LaundryMerchantSetting::defaultWebsiteConfig();

        $this->storeStatus = $this->setting?->store_status ?? 'open';

        if (!empty(trim($this->trackingInput))) {
            $this->checkOrder();
        }
    }

    public function checkOrder(): void
    {
        $code = trim($this->trackingInput);
        if ($code === '') {
            $this->trackedOrder = null;
            $this->hasSearched  = false;
            return;
        }

        $this->hasSearched = true;

        $query = LaundryOrder::with(['items.service', 'promo']);

        if ($this->setting?->user_id) {
            $query->where('user_id', $this->setting->user_id);
        }

        $this->trackedOrder = $query->where(function ($q) use ($code) {
            $q->whereRaw('LOWER(order_code) = ?', [strtolower($code)])
              ->orWhereRaw('LOWER(tracking_code) = ?', [strtolower($code)]);
        })->latest()->first();
    }

    public function clearTrackedOrder(): void
    {
        $this->trackingInput = '';
        $this->trackedOrder  = null;
        $this->hasSearched   = false;
    }

    public function render()
    {
        $ownerId = $this->setting?->user_id;

        $services = collect();
        $promos   = collect();

        if (($this->config['show_live_services'] ?? true) && $ownerId) {
            $services = LaundryService::where('user_id', $ownerId)
                ->where('is_active', true)
                ->orderBy('price')
                ->get();
        }

        if (($this->config['show_live_promos'] ?? true) && $ownerId) {
            $promos = LaundryPromo::where('user_id', $ownerId)
                ->where('is_active', true)
                ->latest()
                ->get();
        }

        $showcases = $ownerId
            ? \App\Models\LaundryShowcase::where('user_id', $ownerId)
                ->where('is_active', true)
                ->with('photos')
                ->orderBy('sort_order')->orderBy('id')
                ->get()
            : collect();

        return view('livewire.laundry.public-landing-page', [
            'services'  => $services,
            'promos'    => $promos,
            'showcases' => $showcases,
        ])->title(($this->config['brand_name'] ?? 'Clean Lab') . ' — ' . ($this->config['tagline'] ?? 'Shoe, Bag, Helmet & Luggage Care'));
    }
}

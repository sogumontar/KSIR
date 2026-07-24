<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryOrder;
use App\Models\LaundryMerchantSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.guest')]
#[Title('Track Your Laundry - Inventory Pro')]
class PublicTracking extends Component
{
    public LaundryOrder $order;
    public ?LaundryMerchantSetting $merchantSetting = null;

    public function mount($tracking_code)
    {
        $this->order = LaundryOrder::with(['items.service', 'promo', 'user.merchantSetting'])
            ->where('tracking_code', $tracking_code)
            ->firstOrFail();

        $this->merchantSetting = $this->order->user->merchantSetting;
    }

    public function render()
    {
        return view('livewire.laundry.public-tracking');
    }
}

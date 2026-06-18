<?php

namespace App\Livewire\Merchant;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.user')]
#[Title('Storefront Configuration - Inventory Pro')]
class StorefrontConfig extends Component
{
    use WithFileUploads;

    public $profile_photo;
    public $banner_photo;
    public string $business_address = '';
    public string $category = '';
    public string $contact_channel = '';
    public string $payment_instructions = '';

    public function mount()
    {
        $user = Auth::user();
        $this->business_address = $user->business_address ?? '';
        $this->category = $user->category ?? '';
        $this->contact_channel = $user->contact_channel ?? '';
        $this->payment_instructions = $user->payment_instructions ?? '';
    }

    public function save()
    {
        $user = Auth::user();
        
        $data = [
            'business_address' => $this->business_address,
            'category' => $this->category,
            'contact_channel' => $this->contact_channel,
            'payment_instructions' => $this->payment_instructions,
        ];

        if ($this->profile_photo) {
            $data['profile_photo'] = $this->profile_photo->store('storefront', 'public');
        }

        if ($this->banner_photo) {
            $data['banner_photo'] = $this->banner_photo->store('storefront', 'public');
        }

        $user->update($data);

        session()->flash('message', 'Storefront updated successfully.');
    }

    public function render()
    {
        return view('livewire.merchant.storefront-config');
    }
}

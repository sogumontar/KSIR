<?php

namespace App\Livewire\Merchant;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.user')]
#[Title('Storefront Configuration - Inventory Pro')]
class StorefrontConfig extends Component
{
    use WithFileUploads;

    public $profile_photo;
    public $banner_photo;
    public string $store_name = '';
    public string $store_description = '';
    public string $public_email = '';
    public string $support_phone = '';
    public string $category = '';
    public bool $operating_status = true;
    public string $timezone = 'UTC';

    public function mount()
    {
        $user = Auth::user();
        $this->store_name = $user->store_name ?? $user->name ?? '';
        $this->store_description = $user->store_description ?? '';
        $this->public_email = $user->public_email ?? $user->email ?? '';
        $this->support_phone = $user->support_phone ?? $user->phone_number ?? '';
        $this->category = $user->category ?? 'software';
        $this->operating_status = (bool) ($user->operating_status ?? true);
        $this->timezone = $user->timezone ?? 'EST';
    }

    public function removeImages()
    {
        $user = Auth::user();
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->update(['profile_photo' => null]);
        }
        if ($user->banner_photo) {
            Storage::disk('public')->delete($user->banner_photo);
            $user->update(['banner_photo' => null]);
        }
        $this->profile_photo = null;
        $this->banner_photo = null;
        session()->flash('message', 'Images removed successfully.');
    }

    public function save()
    {
        $this->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'nullable|string|max:500',
            'public_email' => 'required|email|max:255',
            'support_phone' => 'nullable|string|max:20',
            'category' => 'required|string',
            'operating_status' => 'boolean',
            'timezone' => 'required|string',
            'profile_photo' => 'nullable|image|max:1024',
            'banner_photo' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        
        $data = [
            'store_name' => $this->store_name,
            'store_description' => $this->store_description,
            'public_email' => $this->public_email,
            'support_phone' => $this->support_phone,
            'category' => $this->category,
            'operating_status' => $this->operating_status,
            'timezone' => $this->timezone,
        ];

        if ($this->profile_photo) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $data['profile_photo'] = $this->profile_photo->store('storefront', 'public');
        }

        if ($this->banner_photo) {
            if ($user->banner_photo) {
                Storage::disk('public')->delete($user->banner_photo);
            }
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

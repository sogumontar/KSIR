<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryMerchantSetting;
use App\Models\LaundryStoreContributor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.user')]
#[Title('Pilih Toko Laundry - Inventory Pro')]
class StoreSelector extends Component
{
    public function mount()
    {
        $user = Auth::user();

        // If user only has their own store (no contributions), skip straight to dashboard
        $contributedStores = LaundryStoreContributor::where('contributor_user_id', $user->id)
            ->where('status', 'accepted')
            ->count();

        if ($contributedStores === 0) {
            // No external stores — go directly to own dashboard
            $this->redirect(route('laundry.dashboard', ['storeOwnerId' => $user->id]), navigate: true);
        }
    }

    public function selectStore(int $ownerId): void
    {
        $user = Auth::user();

        // Verify access: either own store or accepted contributor
        if ($ownerId === $user->id) {
            $this->redirect(route('laundry.dashboard', ['storeOwnerId' => $ownerId]), navigate: true);
            return;
        }

        $isContributor = LaundryStoreContributor::where('owner_user_id', $ownerId)
            ->where('contributor_user_id', $user->id)
            ->where('status', 'accepted')
            ->exists();

        if ($isContributor) {
            $this->redirect(route('laundry.dashboard', ['storeOwnerId' => $ownerId]), navigate: true);
        }
    }

    public function render()
    {
        $user = Auth::user();

        // Own store
        $ownSetting = LaundryMerchantSetting::where('user_id', $user->id)->first();

        // Contributed stores
        $contributions = LaundryStoreContributor::where('contributor_user_id', $user->id)
            ->where('status', 'accepted')
            ->with(['owner.merchantSetting'])
            ->get();

        return view('livewire.laundry.store-selector', [
            'ownSetting'    => $ownSetting,
            'contributions' => $contributions,
        ]);
    }
}

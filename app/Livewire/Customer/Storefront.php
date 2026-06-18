<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\User;
use App\Models\Good;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.guest')]
#[Title('Merchant Storefront - Inventory Pro')]
class Storefront extends Component
{
    public $merchant;
    public $goods;

    public function mount(string $merchantToken)
    {
        $this->merchant = User::where('unique_code', $merchantToken)
            ->whereHas('customers', function ($query) {
                $query->where('customer_id', Auth::id());
            })->firstOrFail();

        $this->goods = Good::where('user_id', $this->merchant->id)
            ->where('is_visible', true)
            ->get();
    }

    public function addToCart($goodId)
    {
        // Simple cart implementation using session
        $cart = session()->get('cart', []);
        $good = Good::findOrFail($goodId);
        
        $cart[$goodId] = [
            'name' => $good->name,
            'price' => $good->price,
            'quantity' => ($cart[$goodId]['quantity'] ?? 0) + 1,
        ];
        
        session()->put('cart', $cart);
        session()->flash('message', 'Item added to cart.');
    }

    public function render()
    {
        return view('livewire.customer.storefront', [
            'merchant' => $this->merchant,
            'goods' => $this->goods,
        ]);
    }
}

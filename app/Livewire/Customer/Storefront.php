<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use App\Models\User;
use App\Models\Good;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.customer')]
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

    #[Computed]
    public function cartCount()
    {
        $cart = session()->get('cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }

    public function render()
    {
        return view('livewire.customer.storefront', [
            'merchant' => $this->merchant,
            'goods' => $this->goods,
        ]);
    }
}

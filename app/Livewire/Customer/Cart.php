<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;

#[Layout('components.layouts.customer')]
#[Title('Your Cart - Inventory Pro')]
class Cart extends Component
{
    public $cart = [];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function increment($goodId)
    {
        if (isset($this->cart[$goodId])) {
            $this->cart[$goodId]['quantity']++;
            $this->updateSession();
        }
    }

    public function decrement($goodId)
    {
        if (isset($this->cart[$goodId])) {
            if ($this->cart[$goodId]['quantity'] > 1) {
                $this->cart[$goodId]['quantity']--;
            } else {
                unset($this->cart[$goodId]);
            }
            $this->updateSession();
        }
    }

    public function removeItem($goodId)
    {
        if (isset($this->cart[$goodId])) {
            unset($this->cart[$goodId]);
            $this->updateSession();
        }
    }

    private function updateSession()
    {
        session()->put('cart', $this->cart);
    }

    #[Computed]
    public function total()
    {
        return array_reduce($this->cart, function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);
    }

    public function proceedToCheckout()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }
        
        return redirect()->route('customer.checkout');
    }

    public function render()
    {
        return view('livewire.customer.cart');
    }
}

<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.customer')]
#[Title('Customer Dashboard - Inventory Pro')]
class Dashboard extends Component
{
    public function render()
    {
        $merchants = Auth::user()->merchants;
        $orders = Order::where('customer_id', Auth::id())
            ->with(['merchant', 'items.good', 'payment'])
            ->latest()
            ->get();

        return view('livewire.customer.dashboard', [
            'merchants' => $merchants,
            'orders' => $orders,
        ]);
    }
}


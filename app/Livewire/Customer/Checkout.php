<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Good;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.guest')]
#[Title('Checkout - Inventory Pro')]
class Checkout extends Component
{
    use WithFileUploads;

    public $cart = [];
    public $proof;
    public $merchantId;

    public function mount()
    {
        $this->cart = session()->get('cart', []);
        // Assuming all items in cart are from the same merchant, 
        // we should validate this in a real app, but for now take the first item
        $firstGoodId = array_key_first($this->cart);
        if ($firstGoodId) {
            $this->merchantId = Good::findOrFail($firstGoodId)->user_id;
        }
    }

    public function checkout()
    {
        $this->validate([
            'proof' => 'required|image|max:2048',
        ]);

        DB::transaction(function () {
            $total = array_reduce($this->cart, function ($sum, $item) {
                return $sum + ($item['price'] * $item['quantity']);
            }, 0);

            $order = Order::create([
                'merchant_id' => $this->merchantId,
                'customer_id' => Auth::id(),
                'status' => 'Pending',
                'total_amount' => $total,
            ]);

            foreach ($this->cart as $goodId => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'good_id' => $goodId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Stock Reservation (Hold)
                $good = Good::findOrFail($goodId);
                $good->increment('stock_hold', $item['quantity']);
            }

            $path = $this->proof->store('payments', 'public');
            OrderPayment::create([
                'order_id' => $order->id,
                'proof_path' => $path,
            ]);
        });

        session()->forget('cart');
        session()->flash('message', 'Order placed successfully. Awaiting merchant verification.');

        return redirect()->route('customer.dashboard');
    }

    public function render()
    {
        return view('livewire.customer.checkout');
    }
}

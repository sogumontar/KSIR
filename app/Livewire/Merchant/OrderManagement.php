<?php

namespace App\Livewire\Merchant;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Order;
use App\Models\Good;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.user')]
#[Title('Order Management - Inventory Pro')]
class OrderManagement extends Component
{
    public function approve($orderId)
    {
        DB::transaction(function () use ($orderId) {
            $order = Order::findOrFail($orderId);
            
            foreach ($order->items as $item) {
                $good = Good::findOrFail($item->good_id);
                // Commit: deduct from stock, release from hold
                $good->decrement('stock', $item->quantity);
                $good->decrement('stock_hold', $item->quantity);
            }
            
            $order->update(['status' => 'Delivering']);
        });
        
        session()->flash('message', 'Order approved.');
    }

    public function reject($orderId)
    {
        DB::transaction(function () use ($orderId) {
            $order = Order::findOrFail($orderId);
            
            foreach ($order->items as $item) {
                $good = Good::findOrFail($item->good_id);
                // Release hold
                $good->decrement('stock_hold', $item->quantity);
            }
            
            $order->update(['status' => 'Cancelled']);
        });
        
        session()->flash('message', 'Order rejected.');
    }

    public function render()
    {
        $orders = Order::where('merchant_id', Auth::id())->latest()->get();
        return view('livewire.merchant.order-management', [
            'orders' => $orders,
        ]);
    }
}

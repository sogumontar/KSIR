<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.user')]
#[Title('Order Detail - Laundry - Inventory Pro')]
class OrderDetail extends Component
{
    use WithFileUploads;

    public LaundryOrder $order;
    public $photoAfter;

    public function mount($id)
    {
        $this->order = LaundryOrder::with(['items.service', 'promo'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
    }

    public function updateStatus($newStatus)
    {
        if (in_array($newStatus, ['pending', 'processing', 'ready', 'completed', 'cancelled'])) {
            $this->order->update(['status' => $newStatus]);
            $this->order->refresh();
            session()->flash('message', "Order status updated to $newStatus.");
        }
    }

    public function updatePaymentStatus($newStatus)
    {
         if (in_array($newStatus, ['paid', 'unpaid'])) {
            $this->order->update(['payment_status' => $newStatus]);
            $this->order->refresh();
            session()->flash('message', "Payment status updated to $newStatus.");
        }
    }

    public function uploadPhotoAfter()
    {
        $this->validate([
            'photoAfter' => 'required|image|max:5120',
        ]);

        $path = $this->photoAfter->store('laundry/orders/after', 'public');
        $this->order->update(['photo_after' => $path]);
        $this->photoAfter = null;

        session()->flash('message', 'After condition photo uploaded successfully.');
    }

    public function getWhatsappLink()
    {
        if (!$this->order->customer_phone) return '#';
        
        $phone = $this->order->customer_phone;
        // Replace leading 0 with 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        
        $text = urlencode("Hello {$this->order->customer_name}, your laundry order ({$this->order->order_code}) is currently: " . ucfirst($this->order->status) . ".");
        return "https://wa.me/{$phone}?text={$text}";
    }

    public function deleteOrder()
    {
        $this->order->delete();
        session()->flash('status_message', 'Order ' . $this->order->order_code . ' deleted successfully.');
        return redirect()->route('laundry.dashboard');
    }

    public function render()
    {
        return view('livewire.laundry.order-detail');
    }
}

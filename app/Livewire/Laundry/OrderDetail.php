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

    public function getWhatsappLink(): string
    {
        if (!$this->order->customer_phone) return '#';

        $phone = $this->order->customer_phone;
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $order = $this->order;

        // Fetch store info from settings (if available)
        $setting = \App\Models\LaundryMerchantSetting::where('user_id', $order->user_id)->first();

        // Build header
        $header = "CLEAN LAB👟\n";
        if ($setting && $setting->payment_notes) {
            $header .= $setting->payment_notes . "\n";
        }

        // First item dates (use earliest date_in and latest date_estimated_done)
        $items       = $order->items ?? collect([]);
        $earliestIn  = $items->min('date_in');
        $latestDone  = $items->max('date_estimated_done');
        $dateIn      = $earliestIn  ? \Carbon\Carbon::parse($earliestIn)->translatedFormat('d F Y')  : '-';
        $estDone     = $latestDone  ? \Carbon\Carbon::parse($latestDone)->translatedFormat('d F Y') : '-';

        // Payment & order status labels
        $paymentLabel = $order->payment_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS';
        $statusLabel  = strtoupper($order->status);

        // Build item lines
        $itemLines = '';
        foreach ($items as $item) {
            $qty      = (float) $item->qty;
            $qtyLabel = ($qty == (int) $qty) ? (int) $qty : number_format($qty, 2);
            $price    = $item->is_free ? 0 : (float) ($item->final_price ?? $item->item_subtotal ?? 0);
            $name     = $item->service_name_snapshot;
            $treatment = $item->treatment ? ' (' . $item->treatment . ')' : '';
            $itemLines .= "* {$qtyLabel}x {$name}{$treatment} - Rp " . number_format($price, 0, ',', '.') . "\n";
        }

        $total = 'Rp ' . number_format((float) $order->total_amount, 0, ',', '.');

        $trackingUrl = route('laundry.public.track', $order->tracking_code);

        $text = $header
            . "\nHalo Ka {$order->customer_name},\n"
            . "Berikut adalah detail pesanan laundry Kakak di tempat kami:\n"
            . "* No. Nota: #{$order->order_code}\n"
            . "* Tgl Terima: {$dateIn}\n"
            . "* Estimasi Selesai: {$estDone}\n"
            . "\nDetail Pesanan:\n"
            . $itemLines
            . "* Total Tagihan: {$total}\n"
            . "* Status Pembayaran: [{$paymentLabel}]\n"
            . "* Status Pesanan: [{$statusLabel}]\n"
            . "\n🔍 Cek status pesanan Kakak di sini:\n{$trackingUrl}";

        return 'https://wa.me/' . $phone . '?text=' . urlencode($text);
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

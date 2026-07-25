<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryOrder;
use App\Models\LaundryOrderItem;
use App\Models\LaundryPromo;
use App\Models\LaundryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.user')]
#[Title('Edit Order - Laundry - Inventory Pro')]
class EditOrder extends Component
{
    use WithFileUploads;

    public LaundryOrder $order;

    // Customer
    public $customerName = '';
    public $customerPhone = '';
    public $photoBefore = null;
    public $existingPhotoBefore = null;
    public $paymentStatus = 'unpaid';
    public $orderStatus = 'pending';

    // Delivery
    public $deliveryType = 'pickup';
    public $customerAddress = '';

    // Cart
    public $items = [];

    // Promo
    public $selectedPromoId = null;

    public function mount($id)
    {
        $this->order = LaundryOrder::with('items')->where('user_id', Auth::id())->findOrFail($id);

        $this->customerName = $this->order->customer_name;
        $this->customerPhone = $this->order->customer_phone ?? '';
        $this->existingPhotoBefore = $this->order->photo_before;
        $this->paymentStatus = $this->order->payment_status;
        $this->orderStatus = $this->order->status;
        $this->deliveryType = $this->order->delivery_type ?? 'pickup';
        $this->customerAddress = $this->order->customer_address ?? '';
        $this->selectedPromoId = $this->order->laundry_promo_id;

        $this->items = $this->order->items->map(function ($item) {
            return [
                'id' => $item->id,
                'service_id' => $item->laundry_service_id,
                'treatment' => $item->treatment ?? '',
                'date_in' => $item->date_in ? $item->date_in->format('Y-m-d') : now()->format('Y-m-d'),
                'date_estimated_done' => $item->date_estimated_done ? $item->date_estimated_done->format('Y-m-d') : now()->addDays(2)->format('Y-m-d'),
                'price' => (float) $item->price_snapshot,
                'qty' => (float) ($item->qty ?? 1),
            ];
        })->toArray();

        if (empty($this->items)) {
            $this->addItem();
        }
    }

    public function addItem()
    {
        $this->items[] = [
            'id' => null,
            'service_id' => '',
            'treatment' => '',
            'date_in' => now()->format('Y-m-d'),
            'date_estimated_done' => now()->addDays(2)->format('Y-m-d'),
            'price' => 0,
            'qty' => 1,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        if (empty($this->items)) {
            $this->addItem();
        }
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        $field = end($parts);
        $index = count($parts) >= 2 ? $parts[count($parts) - 2] : null;

        if ($field === 'service_id' && $value && $index !== null && isset($this->items[$index])) {
            $service = LaundryService::find($value);
            if ($service) {
                $this->items[$index]['price'] = (float) $service->price;
            }
        }
    }

    #[Computed]
    public function subtotal()
    {
        return collect($this->items)->sum(function ($item) {
            $price = (float) ($item['price'] ?? 0);
            $qty = (float) ($item['qty'] ?? 1);
            return $price * max(0.01, $qty);
        });
    }

    #[Computed]
    public function discountAmount()
    {
        if (!$this->selectedPromoId) {
            return 0;
        }

        $promo = LaundryPromo::find($this->selectedPromoId);
        if (!$promo) {
            return 0;
        }

        if ($promo->type === 'percentage') {
            return $this->subtotal * (($promo->discount_percent ?? 0) / 100);
        } elseif ($promo->type === 'accumulative') {
            $count = count($this->items);
            if (($promo->buy_quantity ?? 0) > 0) {
                $freeCount = intdiv($count, $promo->buy_quantity) * ($promo->free_quantity ?? 0);
                $prices = collect($this->items)->map(fn($item) => (float)($item['price'] ?? 0) * (float)($item['qty'] ?? 1))->sort()->values()->all();
                $discount = 0;
                for ($i = 0; $i < $freeCount && $i < count($prices); $i++) {
                    $discount += (float) $prices[$i];
                }
                return $discount;
            }
        }

        return 0;
    }

    #[Computed]
    public function total()
    {
        return max(0, $this->subtotal - $this->discountAmount);
    }

    public function submit()
    {
        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerPhone' => 'nullable|string|max:50',
            'paymentStatus' => 'required|in:unpaid,paid',
            'orderStatus' => 'required|in:pending,processing,ready,completed,cancelled',
            'deliveryType' => 'required|in:pickup,delivery',
            'customerAddress' => 'required_if:deliveryType,delivery|nullable|string|max:1000',
            'photoBefore' => 'nullable|image|max:5120',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:laundry_services,id',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.date_in' => 'required|date',
            'items.*.date_estimated_done' => 'required|date|after_or_equal:items.*.date_in',
        ], [
            'items.*.service_id.required' => 'Please select a service for all items.',
        ]);

        DB::transaction(function () {
            $photoPath = $this->existingPhotoBefore;
            if ($this->photoBefore) {
                $photoPath = $this->photoBefore->store('laundry/orders/before', 'public');
            }

            $subtotal = collect($this->items)->sum(fn($item) => (float) ($item['price'] ?? 0) * max(0.01, (float) ($item['qty'] ?? 1)));
            $discountAmt = 0;

            if ($this->selectedPromoId) {
                $promo = LaundryPromo::find($this->selectedPromoId);
                if ($promo) {
                    if ($promo->type === 'percentage' && $promo->discount_percent > 0) {
                        $discountAmt = $subtotal * ($promo->discount_percent / 100);
                    } elseif ($promo->type === 'accumulative' && $promo->buy_quantity > 0) {
                        $count = count($this->items);
                        $freeCount = intdiv($count, $promo->buy_quantity) * ($promo->free_quantity ?? 0);
                        $prices = collect($this->items)->map(fn($item) => (float) ($item['price'] ?? 0) * max(0.01, (float) ($item['qty'] ?? 1)))->sort()->values()->all();
                        for ($i = 0; $i < $freeCount && $i < count($prices); $i++) {
                            $discountAmt += $prices[$i];
                        }
                    }
                }
            }

            $totalAmt = max(0, $subtotal - $discountAmt);

            $this->order->update([
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'customer_address' => $this->customerAddress,
                'delivery_type' => $this->deliveryType,
                'payment_status' => $this->paymentStatus,
                'status' => $this->orderStatus,
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmt,
                'total_amount' => $totalAmt,
                'photo_before' => $photoPath,
                'laundry_promo_id' => $this->selectedPromoId,
            ]);

            // Sync items: delete existing items not in new list
            $keptItemIds = collect($this->items)->pluck('id')->filter()->toArray();
            LaundryOrderItem::where('laundry_order_id', $this->order->id)
                ->whereNotIn('id', $keptItemIds)
                ->delete();

            $freeIndices = [];
            if ($this->selectedPromoId) {
                $promo = LaundryPromo::find($this->selectedPromoId);
                if ($promo && $promo->type === 'accumulative' && ($promo->buy_quantity ?? 0) > 0) {
                    $count = count($this->items);
                    $freeCount = intdiv($count, $promo->buy_quantity) * ($promo->free_quantity ?? 0);
                    $freeIndices = collect($this->items)
                        ->map(fn($item, $idx) => ['index' => $idx, 'price' => (float)($item['price'] ?? 0) * max(0.01, (float)($item['qty'] ?? 1))])
                        ->sortBy('price')
                        ->take($freeCount)
                        ->pluck('index')
                        ->all();
                }
            }

            foreach ($this->items as $index => $item) {
                $service = LaundryService::find($item['service_id']);
                $itemData = [
                    'laundry_order_id' => $this->order->id,
                    'laundry_service_id' => $service->id,
                    'service_name_snapshot' => $service->name,
                    'price_snapshot' => $item['price'],
                    'qty' => $item['qty'] ?? 1,
                    'treatment' => $item['treatment'],
                    'date_in' => $item['date_in'],
                    'date_estimated_done' => $item['date_estimated_done'],
                    'is_free' => in_array($index, $freeIndices),
                ];

                if (!empty($item['id'])) {
                    LaundryOrderItem::where('id', $item['id'])->update($itemData);
                } else {
                    LaundryOrderItem::create($itemData);
                }
            }
        });

        session()->flash('message', 'Order ' . $this->order->order_code . ' updated successfully!');
        return redirect()->route('laundry.orders.show', $this->order->id);
    }

    public function render()
    {
        $services = LaundryService::where('user_id', Auth::id())->where('is_active', true)->get();
        $promos = LaundryPromo::where('user_id', Auth::id())->where('is_active', true)->get();

        return view('livewire.laundry.edit-order', [
            'services' => $services,
            'promos' => $promos,
        ]);
    }
}

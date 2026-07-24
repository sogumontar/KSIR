<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryOrder;
use App\Models\LaundryOrderItem;
use App\Models\LaundryPromo;
use App\Models\LaundryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.user')]
#[Title('Create Order - Laundry - Inventory Pro')]
class CreateOrder extends Component
{
    use WithFileUploads;

    // Customer
    public $customerName = '';
    public $customerPhone = '';
    public $photoBefore = null;
    public $paymentStatus = 'unpaid';

    // Delivery
    public $deliveryType = 'pickup';
    public $customerAddress = '';

    // Cart
    public $items = [];

    // Promo
    public $selectedPromoId = null;

    public function mount()
    {
        $this->addItem();
    }

    public function addItem()
    {
        $this->items[] = [
            'service_id' => '',
            'treatment' => '',
            'date_in' => now()->format('Y-m-d'),
            'date_estimated_done' => now()->addDays(2)->format('Y-m-d'),
            'price' => 0,
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
        if (count($parts) == 2 && $parts[1] === 'service_id' && $value) {
            $index = $parts[0];
            $service = LaundryService::find($value);
            if ($service) {
                $this->items[$index]['price'] = $service->price;
            }
        }
    }

    #[Computed]
    public function subtotal()
    {
        return collect($this->items)->sum(function ($item) {
            return (float) ($item['price'] ?? 0);
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
            // Count matching items (assuming accumulative counts all items as units, logic can vary, let's just use total item count)
            $count = count($this->items);
            if (($promo->buy_quantity ?? 0) > 0) {
                $freeCount = intdiv($count, $promo->buy_quantity) * ($promo->free_quantity ?? 0);
                // Find cheapest items to make free? Or just average? We'll make the cheapest ones free.
                $prices = collect($this->items)->pluck('price')->sort()->values()->all();
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
            'deliveryType' => 'required|in:pickup,delivery',
            'customerAddress' => 'required_if:deliveryType,delivery|nullable|string|max:1000',
            'photoBefore' => 'nullable|image|max:5120',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:laundry_services,id',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.date_in' => 'required|date',
            'items.*.date_estimated_done' => 'required|date|after_or_equal:items.*.date_in',
        ], [
            'items.*.service_id.required' => 'Please select a service for all items.',
        ]);

        $order = DB::transaction(function () {
            $photoPath = null;
            if ($this->photoBefore) {
                $photoPath = $this->photoBefore->store('laundry/orders/before', 'public');
            }

            // Calculate totals inline to avoid stale computed property values
            $subtotal = collect($this->items)->sum(fn($item) => (float) ($item['price'] ?? 0));
            $discountAmt = 0;

            if ($this->selectedPromoId) {
                $promo = LaundryPromo::find($this->selectedPromoId);
                if ($promo) {
                    if ($promo->type === 'percentage' && $promo->discount_percent > 0) {
                        $discountAmt = $subtotal * ($promo->discount_percent / 100);
                    } elseif ($promo->type === 'accumulative' && $promo->buy_quantity > 0) {
                        $count = count($this->items);
                        $freeCount = intdiv($count, $promo->buy_quantity) * ($promo->free_quantity ?? 0);
                        $prices = collect($this->items)->pluck('price')->sort()->values()->all();
                        for ($i = 0; $i < $freeCount && $i < count($prices); $i++) {
                            $discountAmt += $prices[$i];
                        }
                    }
                }
            }

            $totalAmt = max(0, $subtotal - $discountAmt);

            $order = LaundryOrder::create([
                'user_id' => Auth::id(),
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'customer_address' => $this->customerAddress,
                'delivery_type' => $this->deliveryType,
                'payment_status' => $this->paymentStatus,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmt,
                'total_amount' => $totalAmt,
                'photo_before' => $photoPath,
                'laundry_promo_id' => $this->selectedPromoId,
            ]);

            $freeIndices = [];
            if ($this->selectedPromoId) {
                $promo = LaundryPromo::find($this->selectedPromoId);
                if ($promo && $promo->type === 'accumulative' && ($promo->buy_quantity ?? 0) > 0) {
                    $count = count($this->items);
                    $freeCount = intdiv($count, $promo->buy_quantity) * ($promo->free_quantity ?? 0);
                    $freeIndices = collect($this->items)
                        ->map(fn($item, $idx) => ['index' => $idx, 'price' => (float)($item['price'] ?? 0)])
                        ->sortBy('price')
                        ->take($freeCount)
                        ->pluck('index')
                        ->all();
                }
            }

            foreach ($this->items as $index => $item) {
                $service = LaundryService::find($item['service_id']);
                LaundryOrderItem::create([
                    'laundry_order_id' => $order->id,
                    'laundry_service_id' => $service->id,
                    'service_name_snapshot' => $service->name,
                    'price_snapshot' => $item['price'],
                    'treatment' => $item['treatment'],
                    'date_in' => $item['date_in'],
                    'date_estimated_done' => $item['date_estimated_done'],
                    'is_free' => in_array($index, $freeIndices),
                ]);
            }

            return $order;
        });

        session()->flash('message', 'Order created successfully!');
        $this->redirect(route('laundry.orders.show', $order->id), navigate: true);
    }

    public function render()
    {
        $services = LaundryService::where('user_id', Auth::id())->where('is_active', true)->get();
        $promos = LaundryPromo::where('user_id', Auth::id())->where('is_active', true)->get();

        return view('livewire.laundry.create-order', [
            'services' => $services,
            'promos' => $promos,
        ]);
    }
}

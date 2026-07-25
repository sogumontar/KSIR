<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'laundry_order_id',
    'laundry_service_id',
    'service_name_snapshot',
    'price_snapshot',
    'qty',
    'treatment',
    'date_in',
    'date_estimated_done',
    'is_free',
])]
class LaundryOrderItem extends Model
{
    protected function casts(): array
    {
        return [
            'price_snapshot' => 'decimal:2',
            'qty' => 'decimal:2',
            'date_in' => 'date',
            'date_estimated_done' => 'date',
            'is_free' => 'boolean',
        ];
    }

    public function laundryOrder(): BelongsTo
    {
        return $this->belongsTo(LaundryOrder::class);
    }

    public function laundryService(): BelongsTo
    {
        return $this->belongsTo(LaundryService::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(LaundryService::class, 'laundry_service_id');
    }

    public function getItemSubtotalAttribute(): float
    {
        $qty = (float) ($this->qty ?? 1);
        return round((float) $this->price_snapshot * max(0.01, $qty), 2);
    }

    public function getFinalPriceAttribute()
    {
        if ($this->is_free) {
            return 0;
        }

        $itemSubtotal = $this->item_subtotal;
        $order = $this->laundryOrder;

        if ($order && (float) $order->subtotal > 0 && (float) $order->discount_amount > 0) {
            if ($order->promo && $order->promo->type === 'accumulative') {
                return $itemSubtotal;
            }
            $ratio = (float) $order->total_amount / (float) $order->subtotal;
            return round($itemSubtotal * $ratio, 2);
        }

        return $itemSubtotal;
    }
}

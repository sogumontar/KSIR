<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'user_id',
    'tracking_code',
    'order_code',
    'customer_name',
    'customer_phone',
    'photo_before',
    'photo_after',
    'payment_status',
    'delivery_type',
    'customer_address',
    'status',
    'subtotal',
    'discount_amount',
    'total_amount',
    'laundry_promo_id',
    'notes',
])]
class LaundryOrder extends Model
{
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    /** Temporary prefix set by CreateOrder before calling create() */
    public string $orderCodePrefix = '';

    protected static function booted(): void
    {
        static::creating(function (LaundryOrder $order) {
            $order->tracking_code = \Illuminate\Support\Str::uuid()->toString();

            // Use the caller-supplied prefix or fall back to 'ORD'
            $prefix = strtoupper(trim($order->orderCodePrefix)) ?: 'ORD';

            // Count existing orders whose code starts with this prefix
            $count = self::where('order_code', 'like', $prefix . '-%')->count();
            $seq   = str_pad($count + 1, 4, '0', STR_PAD_LEFT);

            // Ensure uniqueness in the rare case of a race
            $code = $prefix . '-' . $seq;
            while (self::where('order_code', $code)->exists()) {
                $count++;
                $seq  = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
                $code = $prefix . '-' . $seq;
            }

            $order->order_code = $code;
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function laundryPromo(): BelongsTo
    {
        return $this->belongsTo(LaundryPromo::class);
    }

    public function promo(): BelongsTo
    {
        return $this->belongsTo(LaundryPromo::class, 'laundry_promo_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LaundryOrderItem::class);
    }

    public function getTotalAttribute()
    {
        return $this->total_amount;
    }

    public function getDiscountAttribute()
    {
        return $this->discount_amount;
    }
}

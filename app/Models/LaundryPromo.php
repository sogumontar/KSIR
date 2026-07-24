<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'name',
    'type',
    'discount_percent',
    'buy_quantity',
    'free_quantity',
    'required_service_id',
    'free_service_id',
    'is_active',
    'valid_from',
    'valid_until',
])]
class LaundryPromo extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'discount_percent' => 'decimal:2',
            'valid_from' => 'date',
            'valid_until' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requiredService(): BelongsTo
    {
        return $this->belongsTo(LaundryService::class, 'required_service_id');
    }

    public function freeService(): BelongsTo
    {
        return $this->belongsTo(LaundryService::class, 'free_service_id');
    }
}

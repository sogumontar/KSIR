<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'short_code', 'description', 'price', 'is_active'])]
class LaundryService extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function laundryOrderItems(): HasMany
    {
        return $this->hasMany(LaundryOrderItem::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'good_id', 'transaction_date', 'item_name', 'recipient_name', 'quantity', 'price', 'sell_price', 'total_price', 'profit', 'status', 'description', 'due_date', 'proof_of_delivery', 'sales_type', 'sales_code'])]
class Transaction extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'price' => 'decimal:2',
            'sell_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'profit' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function good(): BelongsTo
    {
        return $this->belongsTo(Good::class)->withTrashed();
    }
}

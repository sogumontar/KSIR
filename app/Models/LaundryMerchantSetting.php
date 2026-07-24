<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'qr_code_path', 'payment_notes', 'store_status'])]
class LaundryMerchantSetting extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

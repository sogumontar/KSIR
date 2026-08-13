<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'store_name', 'store_address', 'header_bg_image', 'qr_code_path', 'payment_notes', 'store_status'])]
class LaundryMerchantSetting extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

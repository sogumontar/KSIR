<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

#[Fillable(['user_id', 'name', 'price', 'stock', 'description'])]
class Good extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->withTrashed();
    }

    protected static function booted()
    {
        static::updated(function ($good) {
            // Cascade name and price updates to active transaction records
            if ($good->wasChanged(['name', 'price'])) {
                Transaction::where('good_id', $good->id)->update([
                    'item_name' => $good->name,
                    'price' => $good->price,
                    'total_price' => DB::raw('quantity * ' . (float) $good->price),
                ]);
            }
        });
    }
}

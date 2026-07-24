<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'name', 'price', 'stock', 'stock_hold', 'is_visible', 'unit_type', 'description', 'image'])]
class Good extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'stock_hold' => 'integer',
            'is_visible' => 'boolean',
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

    public function getImageUrlAttribute(): string
    {
        if ($this->image && \Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        // Return a high-quality stylized placeholder
        return 'https://images.unsplash.com/photo-1553413077-190dd305871c?q=80&w=400&auto=format&fit=crop';
    }

    protected static function booted()
    {
        static::updated(function ($good) {
            if ($good->wasChanged(['name', 'price'])) {
                Transaction::where('good_id', $good->id)->update([
                    'item_name' => $good->name,
                    'price' => $good->price,
                ]);
            }
        });
    }
}

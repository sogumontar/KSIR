<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['laundry_showcase_id', 'label', 'before_path', 'after_path', 'sort_order'])]
class LaundryShowcasePhoto extends Model
{
    protected static function booted(): void
    {
        static::deleting(fn (LaundryShowcasePhoto $p) => $p->deleteFiles());
    }

    public function showcase(): BelongsTo
    {
        return $this->belongsTo(LaundryShowcase::class, 'laundry_showcase_id');
    }

    public function deleteFiles(): void
    {
        Storage::disk('public')->delete(array_filter([$this->before_path, $this->after_path]));
    }
}

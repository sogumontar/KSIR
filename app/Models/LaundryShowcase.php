<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable(['user_id', 'title', 'icon', 'description', 'video_url', 'sort_order', 'is_active'])]
class LaundryShowcase extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected static function booted(): void
    {
        // Files live on disk, so remove them when the showcase goes away.
        static::deleting(function (LaundryShowcase $showcase) {
            foreach ($showcase->photos as $photo) {
                $photo->deleteFiles();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(LaundryShowcasePhoto::class)->orderBy('sort_order')->orderBy('id');
    }

    /** Returns an embeddable YouTube URL, or null for any other provider. */
    public function getVideoEmbedUrlAttribute(): ?string
    {
        $url = trim((string) $this->video_url);
        if ($url === '') {
            return null;
        }

        if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?rel=0';
        }

        return null;
    }
}

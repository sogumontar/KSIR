<?php

if (!function_exists('storage_url')) {
    /**
     * Generate a URL to a file stored in storage/app/public.
     *
     * On local: uses the standard asset('storage/...') path (requires symlink).
     * On production: uses the /files/{path} route which serves files directly
     *                via PHP, bypassing the need for a symlink entirely.
     *
     * @param  string|null  $path  The relative path from storage/app/public
     *                             e.g. "laundry/orders/before/abc.jpg"
     * @return string|null
     */
    function storage_url(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // In local development, if the file is not in storage/app/public yet
        // (e.g. bundled Clean Lab logo/pricelist), serve via the /files/{path} route fallback.
        if (app()->environment('local') && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        return route('storage.file', ['path' => $path]);
    }
}

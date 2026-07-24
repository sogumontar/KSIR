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

        // In local development, use the symlink-based asset URL.
        // In production (where symlinks don't work), use the PHP file-serving route.
        if (app()->environment('local')) {
            return asset('storage/' . $path);
        }

        return route('storage.file', ['path' => $path]);
    }
}

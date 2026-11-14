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

if (!function_exists('cleanlab_asset_src')) {
    /**
     * Resolve an image source for Clean Lab default or uploaded assets.
     * If a custom file exists on the public storage disk, serve it via storage_url().
     * Otherwise, embed or serve from resources/images/cleanlab/{type}.jpg so it never 404s on cPanel.
     */
    function cleanlab_asset_src(?string $storagePath, string $defaultFile = 'logo.jpg'): string
    {
        if (!empty($storagePath) && \Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)) {
            return storage_url($storagePath);
        }

        $bundled = resource_path('images/cleanlab/' . $defaultFile);
        if (is_file($bundled)) {
            return 'data:image/jpeg;base64,' . base64_encode((string) file_get_contents($bundled));
        }

        return route('storage.file', ['path' => 'images/cleanlab/' . $defaultFile]);
    }
}

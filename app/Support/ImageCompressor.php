<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Shrinks uploaded photos for lightweight shared hosting.
 * Resizes to a max edge, fixes EXIF rotation, re-encodes as WebP (JPEG fallback)
 * and stores the file on the public disk. Typically 4MB phone photo -> ~60-120KB.
 */
class ImageCompressor
{
    public static function store(UploadedFile $file, string $dir, int $maxEdge = 1000, int $quality = 76): string
    {
        $src  = $file->getRealPath();
        $info = @getimagesize($src);

        if (!$info) {
            throw new RuntimeException('File bukan gambar yang valid.');
        }

        $img = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($src),
            IMAGETYPE_PNG  => @imagecreatefrompng($src),
            IMAGETYPE_WEBP => @imagecreatefromwebp($src),
            default        => throw new RuntimeException('Format gambar tidak didukung (gunakan JPG/PNG/WEBP).'),
        };

        if (!$img) {
            throw new RuntimeException('Gagal membaca gambar.');
        }

        // Respect camera orientation
        if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($src);
            $orientation = is_array($exif) ? ($exif['Orientation'] ?? 1) : 1;
            $angle = match ($orientation) { 3 => 180, 6 => -90, 8 => 90, default => 0 };
            if ($angle !== 0) {
                $rotated = @imagerotate($img, $angle, 0);
                if ($rotated instanceof \GdImage) {
                    $img = $rotated;
                }
            }
        }

        // Downscale (never upscale) using imagecopyresampled for crisp quality & reliability
        $srcW = imagesx($img);
        $srcH = imagesy($img);
        $scale = min(1.0, $maxEdge / max($srcW, $srcH));
        $dstW = max(1, (int) round($srcW * $scale));
        $dstH = max(1, (int) round($srcH * $scale));

        // Flatten transparency on white background (avoids black backgrounds in JPEG/WebP)
        $canvas = imagecreatetruecolor($dstW, $dstH);
        $white  = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        imagecopyresampled($canvas, $img, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);
        imagedestroy($img);

        $webp = function_exists('imagewebp');
        ob_start();
        if ($webp) {
            imagewebp($canvas, null, $quality);
        } else {
            imagejpeg($canvas, null, $quality);
        }
        $data = ob_get_clean();
        imagedestroy($canvas);

        $path = trim($dir, '/') . '/' . Str::uuid() . ($webp ? '.webp' : '.jpg');
        Storage::disk('public')->put($path, $data);

        return $path;
    }
}

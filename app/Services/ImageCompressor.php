<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageCompressor
{
    /**
     * Compress an uploaded image file to <= 200KB (~100KB - 200KB) and save to storage.
     *
     * @param  UploadedFile  $file
     * @param  string  $directory  Storage folder relative to disk (e.g. 'avatars', 'products', 'settings', 'review_images')
     * @param  string  $disk       Storage disk name (default: 'public')
     * @param  int     $targetMin  Target minimum size in KB (default: 100)
     * @param  int     $targetMax  Target maximum size in KB (default: 200)
     * @return string  Relative storage path
     */
    public static function compressAndStore(
        UploadedFile $file,
        string $directory = 'uploads',
        string $disk = 'public',
        int $targetMin = 100,
        int $targetMax = 200
    ): string {
        @ini_set('memory_limit', '256M');

        $realPath = $file->getRealPath();
        $mime = strtolower((string) $file->getMimeType());
        $originalExtension = strtolower((string) $file->getClientOriginalExtension());

        // Max target size in bytes (default: 200 KB)
        $maxSizeBytes = $targetMax * 1024;
        $minSizeBytes = $targetMin * 1024;

        // Try creating GD image instance from binary content string or extension functions
        $srcImage = null;
        if (file_exists($realPath) && is_readable($realPath)) {
            $contents = @file_get_contents($realPath);
            if ($contents !== false) {
                $srcImage = @imagecreatefromstring($contents);
            }
        }

        if (!$srcImage) {
            if (str_contains($mime, 'jpeg') || str_contains($mime, 'jpg') || in_array($originalExtension, ['jpg', 'jpeg'])) {
                $srcImage = @imagecreatefromjpeg($realPath);
            } elseif (str_contains($mime, 'png') || $originalExtension === 'png') {
                $srcImage = @imagecreatefrompng($realPath);
            } elseif (str_contains($mime, 'webp') || $originalExtension === 'webp') {
                $srcImage = @imagecreatefromwebp($realPath);
            } elseif (str_contains($mime, 'gif') || $originalExtension === 'gif') {
                $srcImage = @imagecreatefromgif($realPath);
            }
        }

        // Fallback: If GD cannot create image resource, store file directly
        if (! $srcImage) {
            return $file->store($directory, $disk);
        }

        $origWidth = imagesx($srcImage);
        $origHeight = imagesy($srcImage);

        // Maximum dimension (width/height) allowed for web images (1200px)
        $maxDimension = 1200;
        $newWidth = $origWidth;
        $newHeight = $origHeight;

        if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
            $ratio = min($maxDimension / $origWidth, $maxDimension / $origHeight);
            $newWidth = (int) round($origWidth * $ratio);
            $newHeight = (int) round($origHeight * $ratio);
        }

        // Resample/Resize image
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve PNG / WebP transparency
        if (str_contains($mime, 'png') || str_contains($mime, 'webp') || in_array($originalExtension, ['png', 'webp'])) {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagedestroy($srcImage);

        // Compress image dynamically to target size range (<= 200KB)
        $bestBuffer = null;
        $bestSize = 0;

        // Iterate quality settings from 85 down to 25
        for ($quality = 85; $quality >= 25; $quality -= 5) {
            ob_start();
            if (str_contains($mime, 'png') || $originalExtension === 'png') {
                $pngQuality = (int) round((100 - $quality) / 10);
                imagepng($dstImage, null, min(9, max(0, $pngQuality)));
            } elseif (str_contains($mime, 'webp') || $originalExtension === 'webp') {
                imagewebp($dstImage, null, $quality);
            } else {
                imagejpeg($dstImage, null, $quality);
            }
            $buffer = ob_get_clean();
            $bufferSize = strlen($buffer);

            $bestBuffer = $buffer;
            $bestSize = $bufferSize;

            if ($bufferSize <= $maxSizeBytes) {
                break;
            }
        }

        // If buffer size is still > 200KB (e.g. large uncompressed PNGs), convert to WebP or scale down to 800px
        if ($bestSize > $maxSizeBytes && (str_contains($mime, 'png') || $originalExtension === 'png')) {
            ob_start();
            imagewebp($dstImage, null, 75);
            $webpBuffer = ob_get_clean();
            if (strlen($webpBuffer) < $bestSize) {
                $bestBuffer = $webpBuffer;
                $originalExtension = 'webp';
            }
        }

        imagedestroy($dstImage);

        // Determine target extension (.jpg, .png, or .webp)
        $outExt = match (true) {
            str_contains($mime, 'png') || $originalExtension === 'png' => 'png',
            str_contains($mime, 'webp') || $originalExtension === 'webp' => 'webp',
            default => 'jpg',
        };

        $filename = Str::random(40) . '.' . $outExt;
        $targetPath = trim($directory, '/') . '/' . $filename;

        // Put compressed image buffer to disk
        Storage::disk($disk)->put($targetPath, $bestBuffer);

        return $targetPath;
    }
}

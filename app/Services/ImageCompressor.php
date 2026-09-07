<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageCompressor
{
    /**
     * Compress an uploaded image file to ~300KB - 400KB and save to storage.
     *
     * @param  UploadedFile  $file
     * @param  string  $directory  Storage folder relative to disk (e.g. 'avatars', 'products', 'settings')
     * @param  string  $disk       Storage disk name (default: 'public')
     * @param  int     $targetMin  Target minimum size in KB (default: 300)
     * @param  int     $targetMax  Target maximum size in KB (default: 400)
     * @return string  Relative storage path
     */
    public static function compressAndStore(
        UploadedFile $file,
        string $directory = 'uploads',
        string $disk = 'public',
        int $targetMin = 300,
        int $targetMax = 400
    ): string {
        @ini_set('memory_limit', '256M');

        $realPath = $file->getRealPath();
        $mime = strtolower((string) $file->getMimeType());
        $originalExtension = strtolower((string) $file->getClientOriginalExtension());

        // Max target size in bytes (400 KB)
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

        // Fallback: If GD cannot create image resource, store file directly without losing it!
        if (! $srcImage) {
            return $file->store($directory, $disk);
        }

        $origWidth = imagesx($srcImage);
        $origHeight = imagesy($srcImage);

        // Maximum dimension (width/height) allowed for web images
        $maxDimension = 1600;
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

        // Compress image dynamically to target size range (~300-400KB)
        $bestBuffer = null;
        $bestSize = 0;

        // Iterate quality settings from 90 down to 30 to hit target file size (300KB-400KB)
        for ($quality = 90; $quality >= 30; $quality -= 5) {
            ob_start();
            if (str_contains($mime, 'png') || $originalExtension === 'png') {
                // Convert quality 0-100 to PNG compression level 0-9
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

            // Stop if compressed size is within or below the target max (400KB)
            if ($bufferSize <= $maxSizeBytes) {
                break;
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

<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ImageUploadService
{
    public function storeProductImage(UploadedFile $file, int $tenantId): string
    {
        return $this->storeOptimizedImage($file, 'products/'.$tenantId);
    }

    public function storeLogo(UploadedFile $file, int $tenantId): string
    {
        return $this->storeOptimizedImage($file, 'logos/'.$tenantId);
    }

    private function storeOptimizedImage(UploadedFile $file, string $directory): string
    {
        $this->validate($file);

        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
        $filename = Str::uuid()->toString().'.jpg';
        $path = $directory.'/'.$filename;

        $optimized = $this->optimize($file);
        Storage::disk($disk)->put($path, $optimized, ['visibility' => 'public']);

        return $path;
    }

    public function validate(UploadedFile $file): void
    {
        $maxKb = (int) config('precifique.uploads.product_image_max_kb', 4096);
        if ($file->getSize() > $maxKb * 1024) {
            throw ValidationException::withMessages([
                'photo' => "A imagem deve ter no máximo {$maxKb} KB.",
            ]);
        }

        $mime = $file->getMimeType();
        $allowed = config('precifique.uploads.allowed_mimes', []);
        if ($mime && ! in_array($mime, $allowed, true)) {
            throw ValidationException::withMessages([
                'photo' => 'Formato de imagem não permitido.',
            ]);
        }

        $size = @getimagesize($file->getRealPath());
        if ($size === false) {
            throw ValidationException::withMessages([
                'photo' => 'Arquivo de imagem inválido.',
            ]);
        }

        [$width, $height] = $size;
        $maxW = (int) config('precifique.uploads.product_image_max_width', 4000);
        $maxH = (int) config('precifique.uploads.product_image_max_height', 4000);
        if ($width > $maxW || $height > $maxH) {
            throw ValidationException::withMessages([
                'photo' => "Dimensões máximas: {$maxW}×{$maxH} px.",
            ]);
        }
    }

    private function optimize(UploadedFile $file): string
    {
        if (! extension_loaded('gd')) {
            return (string) file_get_contents($file->getRealPath());
        }

        $sourcePath = $file->getRealPath();
        $info = @getimagesize($sourcePath);

        if ($info === false) {
            return (string) file_get_contents($sourcePath);
        }

        [$width, $height, $type] = $info;
        $maxWidth = (int) config('precifique.uploads.product_image_display_max_width', 1200);

        $source = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
            IMAGETYPE_GIF => @imagecreatefromgif($sourcePath),
            default => false,
        };

        if ($source === false) {
            return (string) file_get_contents($sourcePath);
        }

        if ($width > $maxWidth) {
            $newHeight = (int) round($height * ($maxWidth / $width));
            $resized = imagecreatetruecolor($maxWidth, $newHeight);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
            imagedestroy($source);
            $source = $resized;
        }

        ob_start();
        imagejpeg($source, null, (int) config('precifique.uploads.product_image_jpeg_quality', 85));
        imagedestroy($source);
        $binary = ob_get_clean();

        return $binary !== false ? $binary : (string) file_get_contents($sourcePath);
    }
}

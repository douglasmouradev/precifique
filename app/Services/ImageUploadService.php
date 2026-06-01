<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class ImageUploadService
{
    public function storeProductImage(UploadedFile $file, int $tenantId): string
    {
        $this->validate($file);

        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';

        return $file->store('products/'.$tenantId, $disk);
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
}

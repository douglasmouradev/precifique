<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    public function show(Request $request, string $path, ImageUploadService $images): StreamedResponse
    {
        if (! preg_match('#^(products|logos)/\d+/[a-f0-9\-]+\.jpg$#i', $path)) {
            abort(404);
        }

        $variant = (string) $request->query('variant', 'display');
        $resolved = $path;

        if ($variant === 'thumb') {
            $thumbPath = preg_replace('/\.jpg$/', '_thumb.jpg', $path);
            if (is_string($thumbPath)) {
                $resolved = $thumbPath;
            }
        }

        $disk = $images->uploadDisk();

        if (! Storage::disk($disk)->exists($resolved)) {
            if ($variant === 'thumb' && Storage::disk($disk)->exists($path)) {
                $resolved = $path;
            } else {
                abort(404);
            }
        }

        return Storage::disk($disk)->response($resolved, headers: [
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}

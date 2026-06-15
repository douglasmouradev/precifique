<?php

declare(strict_types=1);

/**
 * Gera PNGs da logo (hexágono verde) para PWA, iOS e e-mails.
 * Uso: php scripts/generate-icons.php
 */

$root = dirname(__DIR__);
$outDir = $root.'/public/images';
$svgPath = $outDir.'/logo-icon.svg';

if (! is_readable($svgPath)) {
    fwrite(STDERR, "Arquivo não encontrado: {$svgPath}\n");
    exit(1);
}

foreach ([180 => 'apple-touch-icon.png', 192 => 'icon-192.png', 512 => 'icon-512.png'] as $size => $filename) {
    $outPath = $outDir.'/'.$filename;

    if (renderFromSvg($size, $outPath, $svgPath) || renderWithGd($size, $outPath)) {
        echo "Gerado: {$filename} ({$size}x{$size})\n";
        continue;
    }

    fwrite(STDERR, "Falha ao gerar {$filename}\n");
    exit(1);
}

function renderFromSvg(int $size, string $outPath, string $svgPath): bool
{
    if (! extension_loaded('imagick')) {
        return false;
    }

    try {
        $imagick = new Imagick();
        $imagick->setBackgroundColor(new ImagickPixel('transparent'));
        $imagick->setResolution(384, 384);
        $imagick->readImage($svgPath);
        $imagick->setImageFormat('png32');
        $imagick->resizeImage($size, $size, Imagick::FILTER_LANCZOS, 1, true);
        $imagick->writeImage($outPath);

        return true;
    } catch (Throwable) {
        return false;
    }
}

function renderWithGd(int $size, string $outPath): bool
{
    if (! extension_loaded('gd')) {
        return false;
    }

    $img = imagecreatetruecolor($size, $size);
    imagesavealpha($img, true);

    $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
    imagefill($img, 0, 0, $transparent);

    $green = imagecolorallocate($img, 0, 200, 150);
    $greenDark = imagecolorallocate($img, 0, 166, 125);
    $white = imagecolorallocate($img, 255, 255, 255);
    $ink = imagecolorallocate($img, 13, 13, 13);

    $center = $size / 2;
    $radius = $size * 0.46;
    $hex = hexagonPoints($center, $center, $radius);
    imagefilledpolygon($img, $hex, $green);

    $inner = hexagonPoints($center, $center, $radius * 0.9);
    imagefilledpolygon($img, $inner, $greenDark);

    $tagScale = $size / 48;
    $tagX = (int) round(17.5 * $tagScale);
    $tagY = (int) round(19.5 * $tagScale);
    $tagW = (int) round(10.5 * $tagScale);
    $tagH = (int) round(9 * $tagScale);
    imagefilledrectangle($img, $tagX, $tagY, $tagX + $tagW, $tagY + $tagH, $white);

    $holeX = (int) round(20 * $tagScale);
    $holeY = (int) round(24 * $tagScale);
    imagefilledellipse($img, $holeX, $holeY, (int) round(2.4 * $tagScale), (int) round(2.4 * $tagScale), $greenDark);

    imagesetthickness($img, max(1, (int) round(1.8 * $tagScale)));
    imageline($img, (int) round(27 * $tagScale), (int) round(26.5 * $tagScale), (int) round(30 * $tagScale), (int) round(23.5 * $tagScale), $ink);
    imageline($img, (int) round(30 * $tagScale), (int) round(23.5 * $tagScale), (int) round(33 * $tagScale), (int) round(26.5 * $tagScale), $ink);
    imageline($img, (int) round(30 * $tagScale), (int) round(23.5 * $tagScale), (int) round(30 * $tagScale), (int) round(30 * $tagScale), $ink);

    imagepng($img, $outPath);

    return true;
}

/**
 * @return list<int>
 */
function hexagonPoints(float $cx, float $cy, float $radius): array
{
    $points = [];

    for ($i = 0; $i < 6; $i++) {
        $angle = deg2rad(60 * $i - 90);
        $points[] = (int) round($cx + $radius * cos($angle));
        $points[] = (int) round($cy + $radius * sin($angle));
    }

    return $points;
}

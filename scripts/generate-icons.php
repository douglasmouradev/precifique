<?php

declare(strict_types=1);

/**
 * Gera PNGs opacos da logo para iOS (180x180) e PWA Android.
 * iOS rejeita PNG transparente e gera o "P" preto automaticamente.
 * Uso: php scripts/generate-icons.php
 */

$root = dirname(__DIR__);
$imagesDir = $root.'/public/images';
$svgPath = $imagesDir.'/logo-icon.svg';

if (! is_readable($svgPath)) {
    fwrite(STDERR, "Arquivo não encontrado: {$svgPath}\n");
    exit(1);
}

$targets = [
    180 => [
        $root.'/public/apple-touch-icon.png',
        $root.'/public/apple-touch-icon-precomposed.png',
        $imagesDir.'/apple-touch-icon.png',
    ],
    192 => [$imagesDir.'/icon-192.png'],
    512 => [$imagesDir.'/icon-512.png'],
];

foreach ($targets as $size => $paths) {
    $temp = $imagesDir.'/.icon-tmp-'.$size.'.png';

    if (! renderIcon($size, $temp, $svgPath)) {
        fwrite(STDERR, "Falha ao gerar ícone {$size}x{$size}\n");
        exit(1);
    }

    foreach ($paths as $path) {
        if (! copy($temp, $path)) {
            fwrite(STDERR, "Falha ao copiar para {$path}\n");
            exit(1);
        }
        echo 'Gerado: '.str_replace($root.'/', '', $path)." ({$size}x{$size})\n";
    }

    unlink($temp);
}

function renderIcon(int $size, string $outPath, string $svgPath): bool
{
    return renderFromSvg($size, $outPath, $svgPath) || renderWithGd($size, $outPath);
}

function renderFromSvg(int $size, string $outPath, string $svgPath): bool
{
    if (! extension_loaded('imagick')) {
        return false;
    }

    try {
        $logo = new Imagick();
        $logo->setBackgroundColor(new ImagickPixel('transparent'));
        $logo->setResolution(600, 600);
        $logo->readImage($svgPath);
        $logo->setImageFormat('png32');

        $logoSize = (int) round($size * 0.9);
        $logo->resizeImage($logoSize, $logoSize, Imagick::FILTER_LANCZOS, 1, true);

        $canvas = new Imagick();
        $canvas->newImage($size, $size, new ImagickPixel('#FFFFFF'));
        $canvas->setImageFormat('png24');
        $canvas->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);

        $offset = (int) round(($size - $logoSize) / 2);
        $canvas->compositeImage($logo, Imagick::COMPOSITE_OVER, $offset, $offset);
        $canvas->writeImage($outPath);

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
    $white = imagecolorallocate($img, 255, 255, 255);
    imagefill($img, 0, 0, $white);

    $green = imagecolorallocate($img, 0, 200, 150);
    $greenDark = imagecolorallocate($img, 0, 166, 125);
    $ink = imagecolorallocate($img, 13, 13, 13);

    $center = $size / 2;
    $radius = $size * 0.42;
    imagefilledpolygon($img, hexagonPoints($center, $center, $radius), $green);
    imagefilledpolygon($img, hexagonPoints($center, $center, $radius * 0.9), $greenDark);

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

    imagepng($img, $outPath, 1);

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

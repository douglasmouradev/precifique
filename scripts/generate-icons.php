<?php

declare(strict_types=1);

/**
 * Gera PNGs da logo (P branco em fundo escuro) para PWA e iOS.
 * Uso: php scripts/generate-icons.php
 */

$root = dirname(__DIR__);
$outDir = $root.'/public/images';

if (! extension_loaded('gd')) {
    fwrite(STDERR, "Extensão GD não disponível.\n");
    exit(1);
}

$fontCandidates = [
    'C:/Windows/Fonts/arialbd.ttf',
    'C:/Windows/Fonts/segoeuib.ttf',
    '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
    '/System/Library/Fonts/Supplemental/Arial Bold.ttf',
];

$fontFile = null;
foreach ($fontCandidates as $candidate) {
    if (is_readable($candidate)) {
        $fontFile = $candidate;
        break;
    }
}

if ($fontFile === null) {
    fwrite(STDERR, "Nenhuma fonte TrueType encontrada para gerar os ícones.\n");
    exit(1);
}

foreach ([180 => 'apple-touch-icon.png', 192 => 'icon-192.png', 512 => 'icon-512.png'] as $size => $filename) {
    renderIcon($size, $outDir.'/'.$filename, $fontFile);
    echo "Gerado: {$filename} ({$size}x{$size})\n";
}

function renderIcon(int $size, string $path, string $fontFile): void
{
    $img = imagecreatetruecolor($size, $size);
    imagesavealpha($img, true);

    $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
    imagefill($img, 0, 0, $transparent);

    $bg = imagecolorallocate($img, 26, 26, 26);
    $white = imagecolorallocate($img, 255, 255, 255);
    $radius = (int) round($size * 0.23);

    filledRoundedRect($img, 0, 0, $size - 1, $size - 1, $radius, $bg);

    $fontSize = $size * 0.46;
    $text = 'P';
    $box = imagettfbbox($fontSize, 0, $fontFile, $text);
    $textWidth = $box[2] - $box[0];
    $textHeight = $box[1] - $box[7];
    $x = (int) round(($size - $textWidth) / 2 - $box[0]);
    $y = (int) round(($size + $textHeight) / 2 - $box[1]);

    imagettftext($img, $fontSize, 0, $x, $y, $white, $fontFile, $text);
    imagepng($img, $path);
}

function filledRoundedRect($img, int $x1, int $y1, int $x2, int $y2, int $radius, int $color): void
{
    imagefilledrectangle($img, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
    imagefilledrectangle($img, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
    imagefilledellipse($img, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($img, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($img, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($img, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
}

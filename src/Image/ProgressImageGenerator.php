<?php

namespace EtoA\Image;

class ProgressImageGenerator
{
    public function create(int $value = 0, int $width = 400, int $height = 20, bool $reverse = false): void
    {
        $value = max(0, $value);
        $value = min(100, $value);

        $im = imagecreatetruecolor($width, $height);

        $cp = $reverse ? 100 - $value : $value;
        $r = $cp < 50 ? 220 + (floor(25 / 50 * $cp)) : 255 - floor(255 / 50 * ($cp - 50));
        $g = $cp < 50 ? floor(255 / 50 * $cp) : 255 - floor(100 / 50 * ($cp - 50));
        $b = 0;

        $col = imagecolorallocate($im, (int)$r, (int)$g, $b);
        imagefilledrectangle($im, 0, 0, (int)round($width / 100 * $value), $height, $col);

        $foregroundColor = imagecolorallocate($im, 0, 0, 0);
        $w = (int)(($width / 2) - round(imagefontwidth(2) * strlen($value . "%") / 2));
        imagestring($im, 2, $w, 2, $value . "%", $foregroundColor);
        imagestring($im, 2, $w, 4, $value . "%", $foregroundColor);
        imagestring($im, 2, $w - 1, 3, $value . "%", $foregroundColor);
        imagestring($im, 2, $w + 1, 3, $value . "%", $foregroundColor);

        $foregroundColor = imagecolorallocate($im, 255, 255, 255);
        imagestring($im, 2, $w, 3, $value . "%", $foregroundColor);

        imagerectangle($im, 0, 0, $width - 1, $height - 1, imagecolorallocate($im, 200, 200, 200));
        imagerectangle($im, 1, 1, $width - 2, $height - 2, imagecolorallocate($im, 100, 100, 100));

        imagepng($im);
    }
}
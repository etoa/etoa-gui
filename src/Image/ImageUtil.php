<?php

namespace EtoA\Image;

class ImageUtil
{
    public static function dashedLine($image, $x0, $y0, $x1, $y1, $fg, $bg): void
    {
        $st = array($fg, $fg, $fg, $fg, $bg, $bg, $bg, $bg);
        imagesetstyle($image, $st);
        imageline($image, $x0, $y0, $x1, $y1, IMG_COLOR_STYLED);
    }

    public static function imageArrow($im, $x1, $y1, $x2, $y2, $color): void
    {
        imageline($im, $x1, $y1, $x2, $y2, $color);

        $dx = abs($x2 - $x1);
        $dy = abs($y2 - $y1);
        $r = hypot($dx, $dy);
        $sin = $dy / $r;
        $cos = $dx / $r;

        if ($x1 == $x2 && $y2 > $y1)
            $poly = array($x2, $y2, $x2 - 5, $y2 - 10, $x2 + 5, $y2 - 10);
        elseif ($x1 == $x2)
            $poly = array($x2, $y2, $x2 - 5, $y2 + 10, $x2 + 5, $y2 + 10);
        elseif ($y1 == $y2 && $x1 > $x2)
            $poly = array($x2, $y2, $x2 + 10, $y2 - 5, $x2 + 10, $y2 + 5);
        else
            $poly = array($x2, $y2, $x2 - 10, $y2 - 5, $x2 - 10, $y2 + 5);

        imagefilledpolygon($im, $poly, 3, $color);
    }

    public static function icon($name): string
    {
        return "<img src=\"/build/images/icons/" . $name . ".png\" alt=\"$name\" />";
    }
}

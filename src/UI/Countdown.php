<?php

declare(strict_types=1);

namespace EtoA\UI;

class Countdown
{
    /**
     * Starts the javascript countdown in assets/js which writes into the given element.
     *
     * @param int    $time     seconds remaining, or a timestamp when $format says so
     * @param string $targetId id of the element the countdown writes into
     * @param int    $format   format understood by the javascript time() function
     * @param string $text     wrapper text, the placeholder (TIME) is replaced by the value
     */
    public static function script(int $time, string $targetId, int $format = 0, string $text = ''): string
    {
        return "<script type=\"text/javascript\">time(" . $time . ", '" . $targetId . "', " . $format . ", '" . $text . "');</script>";
    }
}

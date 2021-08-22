<?php

declare(strict_types=1);

namespace EtoA\Support;

class StringUtils
{
    /**
     * Splits a string of words into an array and treats words in quotes as a single word
     *
     * Test:
     *   StringUtils::splitBySpaces('Lorem ipsum "dolor sit amet" consectetur "adipiscing \\"elit" dolor')
     *   == array ('Lorem','ipsum','dolor sit amet','consectetur','adipiscing "elit','dolor'))
     *
     * @see http://stackoverflow.com/questions/2202435/php-explode-the-string-but-treat-words-in-quotes-as-a-single-word
     * @return string[]
     */
    public static function splitBySpaces(string $text): array
    {
        if (preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/', $text, $matches)) {
            $rtn = array();
            for ($x = 0; $x < count($matches[0]); $x++) {
                $rtn[$x] = preg_replace(array('/^"/', '/"$/', '/\\\"/'), array('', '', '"'), $matches[0][$x]);
            }

            return $rtn;
        } else {
            if (strlen($text) > 0) {
                return array($text);
            }

            return array();
        }
    }

    public static function encodeJavascriptString(string $str): string
    {
        $controlChars = array(
            chr(92) => '\\\\',            // \ to \\
            chr(39) => "\'",            // '
            chr(34) => '\"',            // "
            chr(13) . chr(10) => '\n',    // CR LF
            chr(10) . chr(13) => '\n',    // LF CR
            chr(10) => '\n',             // LF
            chr(13) => '\n',              // CR
        );

        return strtr($str, $controlChars);
    }

    public static function encodeDBStringToJS(string $str): string
    {
        // Pass the string to a JS variable inline (so no " and no ' occurence possible)
        return str_replace("'", "\\'", str_replace("\\", "\\\\", htmlspecialchars($str, ENT_COMPAT, 'UTF-8')));
    }

    /* Encode a string from the DB (stored via mysql_real_escape_string)
     * to be displayed as plaintext in the HTML document, so changing
     * all the tags to entities etc. AND replacing newlines with <br> tags
     */
    public static function encodeDBStringToPlaintext(string $str): string
    {
        return StringUtils::replaceBR(htmlspecialchars($str, ENT_COMPAT, 'UTF-8'));
    }

    /* Wrapper function, like encodeDBStringToPlaintext() but for inside a textarea.
     */
    public static function encodeDBStringForTextarea(string $str): string
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8', true);
    }

    public static function replaceAsciiControlCharsUnicode(string $str): string
    {
        $controlChars = array(
            chr(0) => chr(0x2400),
            chr(1) => chr(0x2401),
            chr(2) => chr(0x2402),
            chr(3) => chr(0x2403),
            chr(4) => chr(0x2404),
            chr(5) => chr(0x2405),
            chr(6) => chr(0x2406),
            chr(7) => chr(0x2407),
            chr(8) => chr(0x2408),
            chr(9) => chr(0x2409),
            chr(10) => chr(0x240A),
            chr(11) => chr(0x240B),
            chr(12) => chr(0x240C),
            chr(13) => chr(0x240D),
            chr(14) => chr(0x240E),
            chr(15) => chr(0x240F),
            chr(16) => chr(0x2410),
            chr(17) => chr(0x2411),
            chr(18) => chr(0x2412),
            chr(19) => chr(0x2413),
            chr(20) => chr(0x2414),
            chr(21) => chr(0x2415),
            chr(22) => chr(0x2416),
            chr(23) => chr(0x2417),
            chr(24) => chr(0x2418),
            chr(25) => chr(0x2419),
            chr(26) => chr(0x241A),
            chr(27) => chr(0x241B),
            chr(28) => chr(0x241C),
            chr(29) => chr(0x241D),
            chr(30) => chr(0x241E),
            chr(31) => chr(0x241F),
            chr(127) => chr(0x2421),
        );

        return strtr($str, $controlChars);
    }

    public static function replaceAsciiControlChars(string $str): string
    {
        $controlChars = array(
            chr(0) => '&#x2400;',
            chr(1) => '&#x2401;',
            chr(2) => '&#x2402;',
            chr(3) => '&#x2403;',
            chr(4) => '&#x2404;',
            chr(5) => '&#x2405;',
            chr(6) => '&#x2406;',
            chr(7) => '&#x2407;',
            chr(8) => '&#x2408;',
            chr(9) => '&#x2409;&#x200B;',
            chr(10) => '&#x240A;',
            chr(11) => '&#x240B;',
            chr(12) => '&#x240C;',
            chr(13) => '&#x240D;',
            chr(14) => '&#x240E;',
            chr(15) => '&#x240F;',
            chr(16) => '&#x2410;',
            chr(17) => '&#x2411;',
            chr(18) => '&#x2412;',
            chr(19) => '&#x2413;',
            chr(20) => '&#x2414;',
            chr(21) => '&#x2415;',
            chr(22) => '&#x2416;',
            chr(23) => '&#x2417;',
            chr(24) => '&#x2418;',
            chr(25) => '&#x2419;',
            chr(26) => '&#x241A;',
            chr(27) => '&#x241B;',
            chr(28) => '&#x241C;',
            chr(29) => '&#x241D;',
            chr(30) => '&#x241E;',
            chr(31) => '&#x241F;',
            chr(127) => '&#x2421;',
        );

        return strtr($str, $controlChars);
    }

    public static function replaceBR(string $str): string
    {
        $controlChars = array(
            chr(13) . chr(10) => '<br />',    // CR LF
            chr(10) . chr(13) => '<br />',    // LF CR
            chr(10) => '<br />',             // LF
            chr(13) => '<br />',             // CR
        );

        return strtr($str, $controlChars);
    }

    /**
     * Corrects a web url
     */
    public static function formatLink(string $string): string
    {
        $string = preg_replace("#([ \n])(http|https|ftp)://([^ ,\n]*)#i", "\\1[url]\\2://\\3[/url]", $string);
        $string = preg_replace("#([ \n])www\\.([^ ,\n]*)#i", "\\1[url]https://www.\\2[/url]", $string);
        $string = preg_replace("#^(http|https|ftp)://([^ ,\n]*)#i", "[url]\\1://\\2[/url]", $string);
        $string = preg_replace("#^www\\.([^ ,\n]*)#i", "[url]https://www.\\1[/url]", $string);
        $string = preg_replace('#\[url\]www.([^\[]*)\[/url\]#i', '<a href="https://www.\1">\1</a>', $string);
        $string = preg_replace('#\[url\]([^\[]*)\[/url\]#i', '<a href="\1">\1</a>', $string);
        $string = preg_replace('#\[mailurl\]([^\[]*)\[/mailurl\]#i', '<a href="\1">Link</a>', $string);

        return $string;
    }

    /**
     * Überprüft ob unerlaubte Zeichen im Text sind und gibt Antwort zurück
     *
     * @todo Should be removed (better use some regex and strip-/addslashes/trim
     */
    public static function checkIllegalSigns(string $string): string
    {
        if (
            !stristr($string, "'")
            && !stristr($string, "<")
            && !stristr($string, ">")
            && !stristr($string, "?")
            && !stristr($string, "\"")
            && !stristr($string, "$")
            && !stristr($string, "!")
            && !stristr($string, "=")
            && !stristr($string, ";")
            && !stristr($string, "&")
        ) {
            return "";
        }

        return "&lt; &gt; &apos; &quot; ? ! $ = ; &amp;";
    }


    /**
     * Cuts a string by a given length
     */
    public static function cutString(string $string, int $num): string
    {
        if (strlen($string) > $num + 3) {
            return substr($string, 0, $num) . "...";
        }

        return $string;
    }

    /**
     * Format time in seconds to hour, minute, seconds
     */
    public static function formatTimespan(float $ts): string
    {
        $w = floor($ts / 3600 / 24 / 7);
        $ts -= $w * 3600 * 24 * 7;
        $t = floor($ts / 3600 / 24);
        $h = floor(($ts - ($t * 3600 * 24)) / 3600);
        $m = floor(($ts - ($t * 3600 * 24) - ($h * 3600)) / 60);
        $s = floor(($ts - ($t * 3600 * 24) - ($h * 3600) - ($m * 60)));

        $str = "";
        if ($w > 0) {
            $str .= $w . "w ";
        }
        if ($t > 0) {
            $str .= $t . "d ";
        }
        if ($h > 0) {
            $str .= $h . "h ";
        }
        if ($m > 0) {
            $str .= $m . "m ";
        }
        if ($s > 0) {
            $str .= $s . "s ";
        }

        return $str;
    }

    public static function formatDate(int $date, bool $seconds = true): string
    {
        if ($seconds) {
            if (date("dmY") == date("dmY", $date)) {
                $string = "Heute, " . date("H:i:s", $date);
            } else {
                $string = date("d.m.Y, H:i:s", $date);
            }
        } else {
            if (date("dmY") == date("dmY", $date)) {
                $string = "Heute, " . date("H:i", $date);
            } else {
                $string = date("d.m.Y, H:i", $date);
            }
        }

        return $string;
    }

    public static function formatNumber(float $number, bool $colorize = false, bool $ex = false): string
    {
        if ($ex) {
            if ($number > 1000000000) {
                $n = round($number / 1000000000, 3) . " G";
            } elseif ($number > 1000000) {
                $n = round($number / 1000000, 3) . " M";
            } elseif ($number > 1000) {
                $n = round($number / 1000, 3) . " K";
            } else {
                $n = round($number, 0);
            }

            return $n;
        } else {
            $n = number_format($number, 0, ",", "`");
        }
        if ($colorize) {
            if ($number > 0) {
                return "<span style=\"color:#0f0\">" . $n . "</span>";
            }
            if ($number < 0) {
                return "<span style=\"color:#f00\">" . $n . "</span>";
            }
        }

        return $n;
    }

    /**
     * Convert formatted number back to integer
     *
     * @return int|float|string
     */
    public static function parseFormattedNumber(string $number, bool $colorize = false)
    {
        $number = str_replace('`', '', $number);
        $number = str_replace('%', '', $number);
        $number = intval($number);
        if ($colorize == 1) {
            if ($number > 0) {
                return "<span style=\"color:#0f0\">" . number_format($number, 0, ",", ".") . "</span>";
            }
            if ($number < 0) {
                return "<span style=\"color:#f00\">" . number_format($number, 0, ",", ".") . "</span>";
            }
        }
        $number = abs($number);

        return $number;
    }

    /**
     * Prozentwert generieren und zurückgeben
     *
     * @param float|int|array<float|int> $val Einzelner Wert oder Array von Werten als Dezimalzahl; 1.0 = 0%
     */
    public static function formatPercentString($val, bool $colors = false, bool $inverse = false): string
    {
        $string = 0;
        if (is_array($val)) {
            foreach ($val as $v) {
                $string += ($v * 100) - 100;
            }
        } else {
            $string = ($val * 100) - 100;
        }

        $string = round($string, 2);

        if ($string > 0) {
            if ($colors != 0) {
                if ($inverse == 1) {
                    $string = "<span style=\"color:#f00\">+" . $string . "%</span>";
                } else {
                    $string = "<span style=\"color:#0f0\">+" . $string . "%</span>";
                }
            } else {
                $string = $string . "%";
            }
        } elseif ($string < 0) {
            if ($colors != 0) {
                if ($inverse == 1) {
                    $string = "<span style=\"color:#0f0\">" . $string . "%</span>";
                } else {
                    $string = "<span style=\"color:#f00\">" . $string . "%</span>";
                }
            } else {
                $string = $string . "%";
            }
        } else {
            $string = "0%";
        }

        return $string;
    }

    /**
     * Formates a given number of bytes to a human readable string of Bytes, Kilobytes,
     * Megabytes, Gigabytes or Terabytes and rounds it to three digits
     *
     * @param int $s Number of bytes
     * @return string Well-formatted byte number
     */
    public static function formatBytes(int $s): string
    {
        if ($s >= 1099511627776) {
            return round($s / 1099511627776, 3) . " TB";
        }
        if ($s >= 1073741824) {
            return round($s / 1073741824, 3) . " GB";
        }
        if ($s >= 1048576) {
            return round($s / 1048576, 3) . " MB";
        }
        if ($s >= 1024) {
            return round($s / 1024, 3) . " KB";
        }

        return round($s) . " B";
    }

    /**
     * Returns true if the string contains alphabetic characters, dots or underlines
     */
    public static function hasAlphaDotsOrUnderlines(string $str): bool
    {
        return ctype_alpha(str_replace('_', '', str_replace('.', '', $str)));
    }
}

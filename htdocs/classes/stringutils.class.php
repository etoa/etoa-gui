<?PHP

/**
 * String utilities
 *
 * @author MrCage <mrcage@etoa.ch>
 */
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
     */
    public static function splitBySpaces($text)
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

    public static function encodeJavascriptString($str)
    {
        $controlChars = array(
            chr(92) => '\\\\',            // \ to \\
            chr(39) => "\'",            // '
            chr(34) => '\"',            // "
            chr(13) . chr(10) => '\n',    // CR LF
            chr(10) . chr(13) => '\n',    // LF CR
            chr(10) => '\n',             // LF
            chr(13) => '\n'              // CR
        );
        return strtr($str, $controlChars);
    }

    public static function encodeDBStringToJS($str)
    {
        // Pass the string to a JS variable inline (so no " and no ' occurence possible)
        return str_replace("'", "\\'", str_replace("\\", "\\\\", htmlspecialchars($str, ENT_COMPAT, 'UTF-8')));
    }

    /* Encode a string from the DB (stored via mysql_real_escape_string)
     * to be displayed as plaintext in the HTML document, so changing
     * all the tags to entities etc. AND replacing newlines with <br> tags
     */
    public static function encodeDBStringToPlaintext($str)
    {
        return StringUtils::replaceBR(htmlspecialchars($str, ENT_COMPAT, 'UTF-8'));
    }

    /* Wrapper function, like encodeDBStringToPlaintext() but for inside a textarea.
     */
    public static function encodeDBStringForTextarea($str)
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8', true);
    }

    public static function replaceAsciiControlCharsUnicode($str)
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
            chr(127) => chr(0x2421)
        );
        return strtr($str, $controlChars);
    }

    public static function replaceAsciiControlChars($str)
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
            chr(127) => '&#x2421;'
        );
        return strtr($str, $controlChars);
    }

    public static function replaceBR($str)
    {
        $controlChars = array(
            chr(13) . chr(10) => '<br />',    // CR LF
            chr(10) . chr(13) => '<br />',    // LF CR
            chr(10) => '<br />',             // LF
            chr(13) => '<br />'              // CR
        );
        return strtr($str, $controlChars);
    }
}

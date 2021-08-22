<?PHP

/**
 *	Callback-Funktion für preg_replace_callback zum Unterscheiden externer URLs in bbcode
 *
 * @param string[] $match Array mit [0]=> ganzer String, [1]..[n]=> subpatterns in ()
 * @return string mit html-links
 *
 * @author river
 *
 * Im javascript gibt es bereits sowas.
 * Diese Funktion überprüft nicht, ob eine valide URL vorliegt.
 */
function bbcode_urls_to_links_with_newtab($match)
{
    $url = $match[1];
    $scheme = parse_url($url, PHP_URL_SCHEME);
    $host = parse_url($url, PHP_URL_HOST);
    $path = parse_url($url, PHP_URL_PATH);
    // bei relativen / unvollständigen URLs automatisch
    // scheme, host und path hinzufügen.
    // Setzt eine gültige URL voraus.
    if ($scheme === NULL) {
        if ($host === NULL) {
            if ($path === NULL) {
                $url = '/' . $url;
            }
            $url = $_SERVER['SERVER_NAME'] . $url;
        }
        $url = 'http://' . $url;
    }
    $intern = (preg_match('#etoa.ch$|etoa.net$#i', parse_url($url, PHP_URL_HOST)) === 1);

    return '<a href="' . $url . '"' . ($intern ? '' : ' target="_blank"') . '>' . (isset($match[2]) ? $match[2] : $match[1]) . '</a>';
}

function bbcode_page_to_links($match)
{
    $parts = array();
    if (preg_match('/^([a-z\_]+)(?:\s+(.+))?$/i', $match[1], $parts)) {
        $page = $parts[1];
        $url = '?page=' . $page;
        if (isset($parts[2])) {
            foreach (preg_split('/\s+/', $parts[2]) as $e) {
                $url .= '&' . $e;
            }
        }
    } else {
        $url = $match[1];
    }
    $label = (isset($match[2]) ? $match[2] : $match[1]);
    return '<a href="' . $url . '">' . $label . '</a>';
}

function ctype_alsc($str)
{
    return ctype_alpha(str_replace('_', '', $str));
}

function ctype_aldotsc($str)
{
    return ctype_alpha(str_replace('_', '', str_replace('.', '', $str)));
}

<?PHP

use Doctrine\Common\Collections\ArrayCollection;
use EtoA\Core\Configuration\ConfigurationService;

/**
 * Returns a string containing the game name, version and round
 */
function getGameIdentifier()
{
    // TODO
    global $app;

    /** @var ConfigurationService */
    $config = $app[ConfigurationService::class];

    return APP_NAME . ' ' . getAppVersion() . ' ' . $config->get('roundname');
}

function getAppVersion()
{
    require_once __DIR__ . '/../version.php';
    return APP_VERSION;
}

/**
 * Baut die Datenbankverbindung auf
 */
function dbconnect($throwError = 1)
{
    return DBManager::getInstance()->connect($throwError);
}

/**
 * Trennt die Datenbankverbindung
 */
function dbclose()
{
    return DBManager::getInstance()->close();
}

/**
 * Führt eine Datenbankabfrage aus
 *
 * @param string $string SQL-Abfrage
 * #param int $fehler Erzwing Fehleranzeige, Standard: 1
 */
function dbquery($string, $fehler = 1)
{
    return DBManager::getInstance()->query($string, $fehler);
}

/**
 * Executes an sql query savely and protects agains SQL injections
 *
 * @param string $query SQL-Query
 * @param array $params Array of arguments
 */
function dbQuerySave($query, $params = array())
{
    return DBManager::getInstance()->safeQuery($query, $params);
}

/**
 * User-Nick via User-Id auslesen
 */
function get_user_nick($id)
{
    $res = dbquery("
        SELECT
            user_nick
        FROM
            users
        WHERE
            user_id='" . $id . "';
    ");
    if (mysql_num_rows($res) > 0) {
        $arr = mysql_fetch_assoc($res);
        return $arr['user_nick'];
    } else {
        return "<i>Unbekannter Benutzer</i>";
    }
}

/**
 * Format number
 */
function nf($number, $colorize = 0, $ex = 0)    // Number format
{
    if ($ex == 1) {
        if ($number > 1000000000)
            $n = round($number / 1000000000, 3) . " G";
        elseif ($number > 1000000)
            $n = round($number / 1000000, 3) . " M";
        elseif ($number > 1000)
            $n = round($number / 1000, 3) . " K";
        else
            $n = round($number, 0);
        return $n;
    } else
        $n = number_format($number, 0, ",", "`");
    if ($colorize == 1) {
        if ($number > 0)
            return "<span style=\"color:#0f0\">" . $n . "</span>";
        if ($number < 0)
            return "<span style=\"color:#f00\">" . $n . "</span>";
    }
    return $n;
}

/**
 * Format number (round up)
 */
function nf_up($number, $colorize = 0, $ex = 0)    // Number format
{
    return nf(ceil($number), $colorize, $ex);
}

/**
 * Convert formated number back to integer
 */
function nf_back($number, $colorize = 0)
{
    $number = str_replace('`', '', $number);
    $number = str_replace('%', '', $number);
    $number = intval($number);
    if ($colorize == 1) {
        if ($number > 0)
            return "<span style=\"color:#0f0\">" . number_format($number, 0, ",", ".") . "</span>";
        if ($number < 0)
            return "<span style=\"color:#f00\">" . number_format($number, 0, ",", ".") . "</span>";
    }
    $number = abs($number);
    return $number;
}

/**
 * Convert formated number back to integer (positive & negative number)
 */
function nf_back_sign($number, $colorize = 0)
{
    $number = str_replace('`', '', $number);
    $number = (float) str_replace('%', '', $number);
    if ($colorize == 1) {
        if ($number > 0)
            return "<span style=\"color:#0f0\">" . number_format($number, 0, ",", ".") . "</span>";
        if ($number < 0)
            return "<span style=\"color:#f00\">" . number_format($number, 0, ",", ".") . "</span>";
    }

    return (int) $number;
}

/**
 * Format time in seconds to hour,minute,seconds
 */
function tf($ts)    // Time format
{
    $w = floor($ts / 3600 / 24 / 7);
    $ts -= $w * 3600 * 24 * 7;
    $t = floor($ts / 3600 / 24);
    $h = floor(($ts - ($t * 3600 * 24)) / 3600);
    $m = floor(($ts - ($t * 3600 * 24) - ($h * 3600)) / 60);
    $s = floor(($ts - ($t * 3600 * 24) - ($h * 3600) - ($m * 60)));

    $str = "";
    if ($w > 0)
        $str .= $w . "w ";
    if ($t > 0)
        $str .=  $t . "d ";
    if ($h > 0)
        $str .=  $h . "h ";
    if ($m > 0)
        $str .=  $m . "m ";
    if ($s > 0)
        $str .=  $s . "s ";

    return $str;
}

/**
 * Corrects a web url
 */
function format_link($string)
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
function check_illegal_signs($string)
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
    } else {
        return "&lt; &gt; &apos; &quot; ? ! $ = ; &amp;";
    }
}


/**
 * Cuts a string by a given length
 */
function cut_string($string, $num)
{
    if (strlen($string) > $num + 3)
        return substr($string, 0, $num) . "...";
    else
        return $string;
}

/**
 * Checks for a valid mail address
 */
function checkEmail($email)
{
    return preg_match('/^[a-zA-Z0-9-_.]+@[a-zA-Z0-9-_.]+\.[a-zA-Z]{2,4}$/', $email);
}

/**
 * Checks vor a vaid name
 */
function checkValidName($name)
{
    return preg_match(REGEXP_NAME, $name);
}

/**
 * Checks for a valid nick
 */
function checkValidNick($name)
{
    return preg_match(REGEXP_NICK, $name);
}

/**
 * User Name in Array speichern
 */
function get_user_names()
{
    $names = array();
    $res = dbquery("
        SELECT
            user_id,
            user_nick,
            user_name,
            user_email,
            user_alliance_id
        FROM
            users;
    ");
    while ($arr = mysql_fetch_assoc($res)) {
        $names[$arr['user_id']]['nick'] = $arr['user_nick'];
        $names[$arr['user_id']]['name'] = $arr['user_name'];
        $names[$arr['user_id']]['email'] = $arr['user_email'];
        $names[$arr['user_id']]['alliance_id'] = $arr['user_alliance_id'];
    }
    return $names;
}

function tableStart($title = "", $width = 0, $layout = "", $id = "")
{
    if (is_numeric($width) && $width > 0) {
        $w = "width:" . $width . "px;";
    } elseif ($width != "") {
        $w = "width:" . $width . "";
    } else {
        global $cu;
        if (isset($cu->properties) && $cu->properties->cssStyle == "Graphite")
            $w = "width:650px";
        else
            $w = "width:100%";
    }
    if ($id != "") {
        $id = "id=\"" . $id . "\"";
    }
    if ($layout == "double") {
        echo "<table " . $id . " style=\"" . $w . "\"><tr><td style=\"width:50%;vertical-align:top;\">";
    } elseif ($layout == "nondisplay") {
        echo "<table " . $id . " class=\"tb\" style=\"display:none;" . $w . "\">";
    } else {
        echo "<table " . $id . " class=\"tb\" style=\"" . $w . "\">";
    }

    if ($title != "") {
        echo "<caption>" . $title . "</caption>";
    }
}

function tableEnd()
{
    echo "</table>";
}

/**
 * Infobox-Header
 */
function iBoxStart($title = "", $class = "")
{
    echo '<div class="boxLayout ' . $class . '">';
    if ($title != "") {
        echo '<div class="infoboxtitle"><span>' . $title . '</span></div>';
    }
    echo '<div class="infoboxcontent">';
}

/**
 * Infobox-Footer
 */
function iBoxEnd()
{
    echo "</div>";
    echo "</div>";
}

/**
 * Formatierte Erfolgsmeldung anzeigen
 *
 * $msg: OK-Meldung
 */
function success_msg($text)
{
    iBoxStart("Erfolg", "success");
    echo text2html($text);
    iBoxEnd();
}

/**
 * Formatierte Info-Meldung anzeigen
 *
 * $text: Info-Meldung
 */
function info_msg($text)
{
    iBoxStart("Information", "information");
    echo text2html($text);
    iBoxEnd();
}

/*
* Formatierte Fehlermeldung anzeigen
*
* $msg: Fehlermeldung
*/
function error_msg($text, $type = 0, $exit = 0, $addition = 0, $stacktrace = null)
{
    switch ($type) {
        case 1:
            $title = '';
            break;
        case 2:
            $title = 'Warnung';
            break;
        case 3:
            $title = 'Problem';
            break;
        case 4:
            $title = 'Datenbankproblem';
            break;
        default:
            $title = 'Fehler';
    }

    iBoxStart($title, "error");
    echo text2html($text);

    // Addition
    switch ($addition) {
        case 1:
            echo text2html("\n\n[url " . FORUM_URL . "]Zum Forum[/url] | [email mail@etoa.ch]Mail an die Spielleitung[/email]");
            break;
        case 2:
            echo text2html("\n\n[url " . DEVCENTER_PATH . "]Fehler melden[/url]");
            break;
        default:
            echo '';
    }

    // Stacktrace
    if (isset($stacktrace)) {
        echo "<div style=\"text-align:left;border-top:1px solid #000;\">
        <b>Stack-Trace:</b><br/>" . nl2br($stacktrace) . "<br/><a href=\"" . DEVCENTER_PATH . "\" target=\"_blank\">Fehler melden</a></div>";
    }
    iBoxEnd();
    if ($exit > 0) {
        echo "</body></html>";
        exit;
    }
}

/**
 * Prozentwert generieren und zurückgeben
 *
 * $val: Einzelner Wert oder Array von Werten als Dezimalzahl; 1.0 = 0%
 * $colors: Farben anzeigen (1) oder nicht anzeigen (0)
 */
function get_percent_string($val, $colors = 0, $inverse = 0)
{
    $string = 0;
    if (is_array($val)) {
        foreach ($val as $v) {
            $string += ($v * 100) - 100;
        }
    } else
        $string = ($val * 100) - 100;

    $string = round($string, 2);

    if ($string > 0) {
        if ($colors != 0) {
            if ($inverse == 1)
                $string = "<span style=\"color:#f00\">+" . $string . "%</span>";
            else
                $string = "<span style=\"color:#0f0\">+" . $string . "%</span>";
        } else
            $string = $string . "%";
    } elseif ($string < 0) {
        if ($colors != 0) {
            if ($inverse == 1)
                $string = "<span style=\"color:#0f0\">" . $string . "%</span>";
            else
                $string = "<span style=\"color:#f00\">" . $string . "%</span>";
        } else
            $string = $string . "%";
    } else {
        $string = "0%";
    }
    return $string;
}

/**
 * Tabulator-Menü anzeigen
 *
 * $varname: Name des Modusfeldes
 * $data: Array mit Menüdaten
 */
function show_tab_menu($varname, $data)
{
    global $page, $$varname;

    echo "<div class=\"tabMenu\">";
    $cnt = 0;
    foreach ($data as $val => $text) {
        $cnt++;
        if ($$varname == $val)
            echo "<a href=\"?page=$page&amp;" . $varname . "=$val\" class=\"tabEnabled" . ($cnt == count($data) ? ' tabLast' : '') . "\">$text</a>";
        else
            echo "<a href=\"?page=$page&amp;" . $varname . "=$val\"" . ($cnt == count($data) ? ' class="tabLast"' : '') . ">$text</a>";
    }
    echo "<br style=\"clear:both;\"/>";
    echo "</div>";
}

/**
 * Get imagepacks
 */
function get_imagepacks()
{
    $packs = array();
    if ($d = opendir(IMAGEPACK_DIRECTORY)) {
        while ($f = readdir($d)) {
            $dir = IMAGEPACK_DIRECTORY . "/" . $f;
            if (is_dir($dir) && $f != ".." && $f != ".") {
                $file = $dir . "/" . IMAGEPACK_CONFIG_FILE_NAME;

                if (is_file($file)) {
                    $pack = [];
                    $pack['dir'] = $dir;
                    $pack['path'] = substr($dir, strlen(RELATIVE_ROOT));
                    $xml = new XMLReader();
                    $xml->open($file);
                    while ($xml->read()) {
                        switch ($xml->name) {
                            case "name":
                                $xml->read();
                                $pack['name'] = $xml->value;
                                $xml->read();
                                break;
                            case "description":
                                $xml->read();
                                $pack['description'] = $xml->value;
                                $xml->read();
                                break;
                            case "version":
                                $xml->read();
                                $pack['version'] = $xml->value;
                                $xml->read();
                                break;
                            case "changed":
                                $xml->read();
                                $pack['changed'] = $xml->value;
                                $xml->read();
                                break;
                            case "extensions":
                                $xml->read();
                                $pack['extensions'] = explode(",", $xml->value);
                                $xml->read();
                                break;
                            case "author":
                                $xml->read();
                                $pack['author'] = $xml->value;
                                $xml->read();
                                break;
                            case "email":
                                $xml->read();
                                $pack['email'] = $xml->value;
                                $xml->read();
                                break;
                            case "files":
                                $xml->read();
                                $pack['files'] = explode(",", $xml->value);
                                $xml->read();
                                break;
                        }
                    }
                    $xml->close();
                    $packs[basename($dir)] = $pack;
                }
            }
        }
    }
    return $packs;
}

/**
 * Wählt die verschiedenen Designs aus und schreibt sie in ein array. by Lamborghini
 */
function get_designs()
{
    $rootDir = RELATIVE_ROOT . DESIGN_DIRECTORY;
    $designs = array();
    foreach (array('official', 'custom') as $rd) {
        $baseDir = $rootDir . '/' . $rd;
        if ($d = opendir($baseDir)) {
            while ($f = readdir($d)) {
                $dir = $baseDir . "/" . $f;
                if (is_dir($dir) && !preg_match('/^\./', $f)) {
                    $file = $dir . "/" . DESIGN_CONFIG_FILE_NAME;
                    $design = parseDesignInfoFile($file);
                    if ($design != null) {
                        $design['dir'] = $dir;
                        $design['custom'] = ($rd == 'custom');
                        $designs[$f] = $design;
                    }
                }
            }
        }
    }
    return $designs;
}

/**
 * Parses a design info file
 */
function parseDesignInfoFile($file)
{
    if (is_file($file)) {
        $design = [];
        $xml = new XMLReader();
        $xml->open($file);
        while ($xml->read()) {
            switch ($xml->name) {
                case "name":
                    $xml->read();
                    $design['name'] = $xml->value;
                    $xml->read();
                    break;
                case "changed":
                    $xml->read();
                    $design['changed'] = $xml->value;
                    $xml->read();
                    break;
                case "version":
                    $xml->read();
                    $design['version'] = $xml->value;
                    $xml->read();
                    break;
                case "author":
                    $xml->read();
                    $design['author'] = $xml->value;
                    $xml->read();
                    break;
                case "email":
                    $xml->read();
                    $design['email'] = $xml->value;
                    $xml->read();
                    break;
                case "description":
                    $xml->read();
                    $design['description'] = $xml->value;
                    $xml->read();
                    break;
                case "restricted":
                    $xml->read();
                    $design['restricted'] = $xml->value == "true";
                    $xml->read();
                    break;
            }
        }
        $xml->close();
        return $design;
    }
    return null;
}

/**
 * Fremde, feindliche Flotten
 * Gibt Anzahl feindliche Flotten zurück unter beachtung von Tarn- und Spionagetechnik
 * Sind keine Flotten unterwegs -> return 0
 *
 * @author MrCage
 * @param int $user_id User ID
 *
 */
function check_fleet_incomming($user_id)
{
    $fm = new FleetManager($user_id);
    return $fm->loadAggressiv();
}

/**
 * Check for buddys who are online
 */
function check_buddys_online($id)
{
    $res = dbquery("
        SELECT
            COUNT(user_id)
        FROM
            buddylist AS bl
            INNER JOIN user_sessions AS u
            ON bl.bl_buddy_id = u.user_id
            AND bl_user_id='" . $id . "'
            AND bl_allow=1;
    ");
    $arr = mysql_fetch_row($res);
    return $arr[0];
}

function check_buddy_req($id)
{
    $res = dbquery("
        SELECT
        COUNT(bl_id)
        FROM
        buddylist
        WHERE
        bl_buddy_id='" . $id . "'
        AND bl_allow=0");
    $arr = mysql_fetch_row($res);
    return $arr[0];
}

/**
 * The form checker - init
 */
function checker_init($debug = 0)
{
    $_SESSION['checker'] = md5(mt_rand(0, 99999999) . time());
    if (isset($_SESSION['checker_last'])) {
        while ($_SESSION['checker_last'] == $_SESSION['checker']) {
            $_SESSION['checker'] = md5(mt_rand(0, 99999999) . time());
        }
    }
    $_SESSION['checker_last'] = $_SESSION['checker'];
    echo "<input type=\"hidden\" name=\"checker\" value=\"" . $_SESSION['checker'] . "\" />";
    if ($debug == 1)
        echo "Checker initialized with " . $_SESSION['checker'] . "<br/><br/>";
    return "<input type=\"hidden\" name=\"checker\" value=\"" . $_SESSION['checker'] . "\" />";
}

/**
 * The form checker - verify
 */
function checker_verify($debug = 0, $msg = 1, $throw = false)
{
    global $_POST, $_GET;
    if ($debug == 1)
        echo "Checker-Session is: " . $_SESSION['checker'] . ", Checker-POST is: " . $_POST['checker'] . "<br/><br/>";
    if (isset($_SESSION['checker']) && ((isset($_POST['checker']) && $_SESSION['checker'] == $_POST['checker']) || (isset($_GET['checker']) && $_SESSION['checker'] == $_GET['checker'])) && $_SESSION['checker'] != "") {
        $_SESSION['checker'] = Null;
        return true;
    } else {
        if ($throw) {
            throw new \RuntimeException('Seite kann nicht mehrfach aufgerufen werden!');
        }
        if ($msg == 1) {
            error_msg("Seite kann nicht mehrfach aufgerufen werden!");
        } else {
            echo "<b>Fehler:</b> Seite kann nicht mehrfach aufgerufen werden!<br/><br/>";
        }
        return false;
    }
}

/**
 * The form checker - get key
 */
function checker_get_key()
{
    return $_SESSION['checker'];
}

/**
 * The form checker - debug
 */
function checker_get_link_key()
{
    return "&amp;checker=" . $_SESSION['checker'];
}

/**
 * Displays a simple back button
 */
function return_btn()
{
    global $page, $index;
    if ($index != "")
        echo "<input type=\"button\" onclick=\"document.location='?index=$index'\" value=\"Zur&uuml;ck\" />";
    else
        echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Zur&uuml;ck\" />";
}

function button($label, $target)
{
    return "<input type=\"button\" value=\"$label\" onclick=\"document.location='$target'\" />";
}

/**
 * Prevents negative numbers
 */
function zeroPlus($val)
{
    if ($val < 0)
        return 0;
    else
        return $val;
}

/**
 * Diese Funktion liefert 5 Optionsfelder in denen man den Tag,Monat,Jahr,Stunde,Minute auswählen kann
 */
function show_timebox($element_name, $def_val, $seconds = 0)
{
    // Liefert Tag 1-31
    echo "<select name=\"" . $element_name . "_d\" id=\"" . $element_name . "_d\">";
    for ($x = 1; $x < 32; $x++) {
        echo "<option value=\"$x\"";
        if (date("d", $def_val) == $x) echo " selected=\"selected\"";
        echo ">";
        if ($x < 10) echo 0;
        echo "$x</option>";
    }
    echo "</select>.";

    // Liefert Monat 1-12
    echo "<select name=\"" . $element_name . "_m\" id=\"" . $element_name . "_m\">";
    for ($x = 1; $x < 13; $x++) {
        echo "<option value=\"$x\"";
        if (date("m", $def_val) == $x) echo " selected=\"selected\"";
        echo ">";
        if ($x < 10) echo 0;
        echo "$x</option>";
    }
    echo "</select>.";

    // Liefert Jahr +-1 vom jetzigen Jahr
    echo "<select name=\"" . $element_name . "_y\" id=\"" . $element_name . "_y\">";
    $year = (int) date("Y");
    for ($x = $year - 1; $x < $year + 2; $x++) {
        echo "<option value=\"$x\"";
        if (date("Y", $def_val) == $x) echo " selected=\"selected\"";
        echo ">$x</option>";
    }
    echo "</select> &nbsp;&nbsp;";

    // Liefert Stunden von 00-24
    echo "<select name=\"" . $element_name . "_h\" id=\"" . $element_name . "_h\">";
    for ($x = 0; $x < 25; $x++) {
        echo "<option value=\"$x\"";
        if (date("H", $def_val) == $x) echo " selected=\"selected\"";
        echo ">";
        if ($x < 10) echo 0;
        echo "$x</option>";
    }
    echo "</select>:";

    // Liefert Minuten 1-60
    echo "<select name=\"" . $element_name . "_i\" id=\"" . $element_name . "_i\">";
    for ($x = 0; $x < 60; $x++) {
        echo "<option value=\"$x\"";
        if (date("i", $def_val) == $x) echo " selected=\"selected\"";
        echo ">";
        if ($x < 10) echo 0;
        echo "$x</option>";
    }
    echo "</select>";
    if ($seconds == 1)
        echo ":";

    // Liefert Sekunden 1-60
    if ($seconds == 1) {
        echo "<select name=\"" . $element_name . "_s\" id=\"" . $element_name . "_s\">";
        for ($x = 0; $x < 60; $x++) {
            echo "<option value=\"$x\"";
            if (date("s", $def_val) == $x) echo " selected=\"selected\"";
            echo ">";
            if ($x < 10) echo 0;
            echo "$x</option>";
        }
        echo "</select>";
    }
}

/**
 * Servertime
 */
function serverTime()
{
    echo date("H:i:s");
}

/**
 * Tipmessage
 */
function tm($title, $text)
{
    return mTT($title, $text);
}

/**
 * Date format
 */
function df($date, $seconds = 1)
{
    if ($seconds == 1) {
        if (date("dmY") == date("dmY", $date))
            $string = "Heute, " . date("H:i:s", $date);
        else
            $string = date("d.m.Y, H:i:s", $date);
    } else {
        if (date("dmY") == date("dmY", $date))
            $string = "Heute, " . date("H:i", $date);
        else
            $string = date("d.m.Y, H:i", $date);
    }
    return $string;
}

/**
 * Zeigt ein Avatarbild an
 */
function show_avatar($avatar = BOARD_DEFAULT_IMAGE)
{
    if ($avatar == "") $avatar = BOARD_DEFAULT_IMAGE;
    echo "<div style=\"padding:8px;\">";
    echo "<img id=\"avatar\" src=\"" . BOARD_AVATAR_DIR . "/" . $avatar . "\" alt=\"avatar\" style=\"width:64px;height:64px;\"/></div>";
}

/**
 * Stopwatch start
 */
function timerStart()
{
    // Renderzeit-Start festlegen
    $render_time = explode(" ", microtime());
    return (float) $render_time[1] + (float)$render_time[0];
}

/**
 * Stopwatch stop
 */
function timerStop($starttime)
{
    // Renderzeit
    $render_time = explode(" ", microtime());
    $rtime = (float) $render_time[1] + (float) $render_time[0] - $starttime;
    return round($rtime, 3);
}

function imagecreatefromfile($path, $user_functions = false)
{
    $info = @getimagesize($path);

    if (!$info) {
        return false;
    }

    $functions = array(
        IMAGETYPE_GIF => 'imagecreatefromgif',
        IMAGETYPE_JPEG => 'imagecreatefromjpeg',
        IMAGETYPE_PNG => 'imagecreatefrompng',
        IMAGETYPE_WBMP => 'imagecreatefromwbmp',
        IMAGETYPE_XBM => 'imagecreatefromwxbm',
    );

    if ($user_functions) {
        $functions[IMAGETYPE_BMP] = 'imagecreatefrombmp';
    }

    if (!isset($functions[$info[2]])) {
        return false;
    }

    if (!function_exists($functions[$info[2]])) {
        return false;
    }

    return $functions[$info[2]]($path);
}


/**
 * Resizes a image and save it to a given filename
 *
 */
function resizeImage($fileFrom, $fileTo, $newMaxWidth = 0, $newMaxHeight = 0, $type = "jpeg")
{
    if (!in_array($type, ['png', 'gif', 'jpeg', 'jpg'], true)) {
        return false;
    }

    if ($img = imagecreatefromfile($fileFrom)) {
        $width = imagesx($img);
        $height = imagesy($img);
        $resize = false;

        $newWidth = $newMaxWidth;;
        $newHeight = $newMaxHeight;
        if ($width > $newMaxWidth) {
            $newWidth = $newMaxWidth;
            $newHeight = (int) ($height * ($newWidth / $width));
            if ($newHeight > $newMaxHeight) {
                $newHeight = $newMaxHeight;
                $newWidth = (int) ($width * ($newHeight / $height));
            }
            $resize = true;
        } else if ($height > $newMaxHeight) {
            $newHeight = $newMaxHeight;
            $newWidth = (int) ($width * ($newHeight / $height));
            $resize = true;
        }

        if ($resize) {
            // resize using appropriate function
            if (GD_VERSION == 2) {
                $imageId =  imagecreatetruecolor($newWidth, $newHeight);

                imagealphablending($imageId, false);
                imagesavealpha($imageId, true);
                $transparent = imagecolorallocatealpha($imageId, 255, 255, 255, 127);
                imagefilledrectangle($imageId, 0, 0, $newWidth, $newHeight, $transparent);

                imagecopyresampled($imageId, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            } else {
                $imageId = imagecreate($newWidth, $newHeight);
                imagecopyresized($imageId, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            }
            $handle = $imageId;
            // free original image
            imagedestroy($img);
        } else {
            $handle = $img;
        }

        switch ($type) {
            case 'png':
                imagepng($handle, $fileTo);
                break;
            case 'gif':
                imagegif($handle, $fileTo);
                break;
            case 'jpg':
            case 'jpeg':
                imagejpeg($handle, $fileTo, 100);
                break;
            default:
                throw new \InvalidArgumentException('Unknown image type: ' . $type);
        }

        imagedestroy($handle);
        return true;
    }
    return false;
}

/**
 * Calculates costs per level for a given building costs array
 *
 * @param array<string, float> $buildingArray Array of db cost values
 * @param int $level Level
 * @param float $fac costFactor (like specialist)
 * @return array<string, float> Array of calculated costs
 *
 */
function calcBuildingCosts($buildingArray, $level, $fac = 1)
{
    global $cp;
    global $cu;

    global $app;

    /** @var ConfigurationService */
    $config = $app[ConfigurationService::class];

    $bc = array();
    $bc['metal'] = $fac * $buildingArray['building_costs_metal'] * pow($buildingArray['building_build_costs_factor'], $level);
    $bc['crystal'] = $fac * $buildingArray['building_costs_crystal'] * pow($buildingArray['building_build_costs_factor'], $level);
    $bc['plastic'] = $fac * $buildingArray['building_costs_plastic'] * pow($buildingArray['building_build_costs_factor'], $level);
    $bc['fuel'] = $fac * $buildingArray['building_costs_fuel'] * pow($buildingArray['building_build_costs_factor'], $level);
    $bc['food'] = $fac * $buildingArray['building_costs_food'] * pow($buildingArray['building_build_costs_factor'], $level);
    $bc['power'] = $fac * $buildingArray['building_costs_power'] * pow($buildingArray['building_build_costs_factor'], $level);

    $typeBuildTime = 1.0;
    $starBuildTime = 1.0;

    if (isset($cp->typeBuildtime))
        $typeBuildTime = $cp->typeBuildtime;
    if (isset($cp->starBuildtime))
        $starBuildTime = $cp->starBuildtime;

    $bonus = $cu->race->buildTime + $typeBuildTime + $starBuildTime + $cu->specialist->buildTime - 3;
    $bc['time'] = ($bc['metal'] + $bc['crystal'] + $bc['plastic'] + $bc['fuel'] + $bc['food']) / $config->getInt('global_time') * $config->getFloat('build_build_time');
    $bc['time'] *= $bonus;
    return $bc;
}

function calcAllianceBuildingCosts($buildingArray, $level, $fac = 1)
{
    $bc = array();
    $bc['metal'] = $fac * $buildingArray['alliance_building_costs_metal'] * pow($buildingArray['alliance_building_costs_factor'], $level);
    $bc['crystal'] = $fac * $buildingArray['alliance_building_costs_crystal'] * pow($buildingArray['alliance_building_costs_factor'], $level);
    $bc['plastic'] = $fac * $buildingArray['alliance_building_costs_plastic'] * pow($buildingArray['alliance_building_costs_factor'], $level);
    $bc['fuel'] = $fac * $buildingArray['alliance_building_costs_fuel'] * pow($buildingArray['alliance_building_costs_factor'], $level);
    $bc['food'] = $fac * $buildingArray['alliance_building_costs_food'] * pow($buildingArray['alliance_building_costs_factor'], $level);
    return $bc;
}

/**
 * Calculates costs per level for a given technology costs array
 *
 * @param int $l Level
 * @param float $fac costFactor (like specialist)
 * @return array<string, float> Array of calculated costs
 *
 */
function calcTechCosts(\EtoA\Technology\Technology $technology, $l, $fac = 1)
{

    // Baukostenberechnung          Baukosten = Grundkosten * (Kostenfaktor ^ Ausbaustufe)
    $bc = array();
    $bc['metal'] = $fac * $technology->costsMetal * pow($technology->buildCostsFactor, $l);
    $bc['crystal'] = $fac * $technology->costsCrystal * pow($technology->buildCostsFactor, $l);
    $bc['plastic'] = $fac * $technology->costsPlastic * pow($technology->buildCostsFactor, $l);
    $bc['fuel'] = $fac * $technology->costsFuel * pow($technology->buildCostsFactor, $l);
    $bc['food'] = $fac * $technology->costsFood * pow($technology->buildCostsFactor, $l);
    return $bc;
}

/**
 * Formates a given number of bytes to a humand readable string of Bytes, Kilobytes,
 * Megabytes, Gigabytes or Terabytes and rounds it to three digits
 *
 * @param int $s Number of bytes
 * @return string Well-formated byte number
 * @author Nicolas Perrenoud
 */
function byte_format($s)
{
    if ($s >= 1099511627776) {
        return round($s / 1099511627776, 3) . " TB";
    }
    if ($s >= 1073741824) {
        return round($s / 1073741824, 3) . " GB";
    } elseif ($s >= 1048576) {
        return round($s / 1048576, 3) . " MB";
    } elseif ($s >= 1024) {
        return round($s / 1024, 3) . " KB";
    } else {
        return round($s) . " B";
    }
}

/**
 * Generates a password using the password string, and possibly a user selected seed
 *
 * @param string $pw Password from user
 * @param string $salt User's salt (must be random)
 * @return string Returns a salted password concatenated with the salt itself to be saved in a user database
 */
function saltPasswort($pw, $salt = null)
{
    if ($salt == null) {
        $salt = generateSalt();
    }
    return sha1($salt . $pw) . $salt;
}

/**
 * Returns the salt which is part of a salted password string
 *
 * @param string $passwordAndSalt Salted password
 */
function getSaltFromPassword($passwordAndSalt)
{
    $len = strlen(sha1(""));
    return substr($passwordAndSalt, $len, $len);
}

/**
 * Validates if a given input matches the salted password
 *
 * @param string $input Clear-Text password input
 * @param string $passwordAndSalt Salted password from a user database
 */
function validatePasswort($input, $passwordAndSalt)
{
    return saltPasswort($input, getSaltFromPassword($passwordAndSalt)) == $passwordAndSalt;
}

/**
 * Generates a new random salt value
 */
function generateSalt()
{
    return sha1(uniqid((string) mt_rand(), true));
}

/**
 * Generates a new random password of length 8
 */
function generatePasswort()
{
    return substr(sha1((string) mt_rand()), 0, 8);
}

/**
 * Generates a random string
 *
 * @param int $length Length of the string
 */
function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Displays a button which opens an abuse report dialog when clicked
 *
 * @param string $cat Preselected category
 * @param string $title Title of the button
 * @param int $uid Concerning user id
 * @param int $aid Concerning alliance id
 */
function ticket_button($cat, $title = "Missbrauch", $uid = 0, $aid = 0)
{
    echo "<input type=\"button\" value=\"" . $title . "\" onclick=\"window.open('show.php?page=ticket&ext=1&cat=" . $cat . "&uid=" . $uid . "&$aid=" . $aid . "','abuse','width=700,height=470,status=no,scrollbars=yes')\" />";
}

/**
 * Simple recursive function to calculate the power of a number
 * this is faster than the original implementation of pow()
 * because it only uses integer exponents
 */
function intpow($base, $exponent)
{
    if ($exponent <= 0)
        return 1;
    return $base * intpow($base, $exponent - 1);
}


function countDown($elem, $targettime, $elementToSetEmpty = "")
{
?>
    <script type="text/javascript">
        if (document.getElementById('<?PHP echo $elem; ?>') != null) {
            cnt["<?PHP echo $elem; ?>"] = 0;
            setCountdown('<?PHP echo $elem; ?>', <?PHP echo time(); ?>, <?PHP echo $targettime; ?>, '<?PHP echo $elementToSetEmpty; ?>');
        }
    </script>
<?PHP
}

function jsProgressBar($elem, $startTime, $endTime)
{
?>
    <script>
        $(function() {
            updateProgressBar('#<?PHP echo $elem; ?>', <?PHP echo floor($startTime); ?>, <?PHP echo ceil($endTime); ?>, <?PHP echo time(); ?>);
        });
    </script>
<?PHP
}

function jsSlider($elem, $value = 100, $target = '"#value"')
{
?>
    <script type="text/javascript">
        $(function() {
            $("#slider").slider({
                value: <?PHP echo $value ?>,
                min: 1,
                max: 100,
                step: 1,
                slide: function(event, ui) {
                    $(<?PHP echo $target; ?>).val(ui.value + ' %');
                }
            });
            $(<?PHP echo $target; ?>).val($("#slider").slider("value") + " %");
        });
    </script>

<?PHP
}

/**
 * Startet den Javascript Counter bzw. die Uhr
 *
 * @param int $time Gibt die Restzeit oder den Timestamp an
 * @param string $target Gibt die Ziel-ID an
 * @param int $format 0=Counter, 1=Uhr
 * @param string $text Ein optionaler Text kann eingebunden werden -> "Es geht noch TIME bis zum Ende"
 */
function startTime($time, $target, $format = 0, $text = "")
{
    return "<script type=\"text/javascript\">time(" . $time . ", '" . $target . "', " . $format . ", '" . $text . "');</script>";
}

/**
 * Prints an array
 * For debug purposes only
 */
function etoa_dump($val, $return = 0)
{
    ob_start();
    print_r($val);
    $tmp = ob_get_clean();
    if ($return == 1)
        return $tmp;
    echo "<pre>" . ($tmp) . "</pre>";
}

function popUp($caption, $args, $width = 800, $height = 600)
{
    return "<a href=\"?" . $args . "\" onclick=\"window.open('popup.php?" . $args . "','popup','status=no,width=" . $width . ",height=" . $height . ",scrollbars=yes');return false;\">" . $caption . "</a> ";
}

function userPopUp($userId, $userNick, $msg = 1, $strong = 0)
{
    $userNick = $userId > 0 ? ($userNick != '' ? $userNick : '<i>Unbekannt</i>') : '<i>System</i>';

    $out = "";
    if ($userId > 0 && $userNick != '') {
        $out .= "<div id=\"ttuser" . $userId . "\" style=\"display:none;\">
        " . popUp("Profil anzeigen", "page=userinfo&id=" . $userId) . "<br/>
        " . popUp("Punkteverlauf", "page=stats&mode=user&userdetail=" . $userId) . "<br/>";
        if ($userId != $_SESSION['user_id']) {
            if ($msg == 1)
                $out .=  "<a href=\"?page=messages&mode=new&message_user_to=" . $userId . "\">Nachricht senden</a><br/>";
            $out .= "<a href=\"?page=buddylist&add_id=" . $userId . "\">Als Freund hinzufügen</a>";
        }
        $out .= "</div>";
        if ($strong) $out .= '<strong>';
        $out .= '<a href="#" ' . cTT($userNick, "ttuser" . $userId) . '>' . $userNick . '</a>';
        if ($strong) $out .= '</strong>';
    } else {
        $out .= $userNick;
    }
    return $out;
}

function ticketLink($caption, $category)
{
    $width = 700;
    $height = 600;
    return "<a href=\"?page=ticket&amp;cat=" . $category . "\" onclick=\"window.open('popup.php?page=ticket&amp;cat=" . $category . "','popup','status=no,width=" . $width . ",height=" . $height . ",scrollbars=yes');return false;\">" . $caption . "</a>";
}

function helpLink($site, $caption = "Hilfe", $style = "")
{
    $width = 900;
    $height = 600;
    return "<a href=\"?page=help&amp;site=" . $site . "\" style=\"$style\" onclick=\"window.open('popup.php?page=help&amp;site=" . $site . "','popup','status=no,width=" . $width . ",height=" . $height . ",scrollbars=yes');return false;\">" . $caption . "</a>";
}

function helpImageLink($site, $url, $alt = "Item", $style = "")
{
    $width = 900;
    $height = 600;
    return "<a href=\"?page=help&amp;site=" . $site . "\" onclick=\"window.open('popup.php?page=help&amp;site=" . $site . "','popup','status=no,width=" . $width . ",height=" . $height . ",scrollbars=yes');return false;\">
    <img src=\"" . $url . "\" alt=\"" . $alt . "\" style=\"border:none;" . $style . "\" />
    </a>";
}

function showTechTree($type, $itemId)
{
    echo "<div id=\"reqInfo\" style=\"width:100%;text-align:center;;
    color:#fff;border:none;margin:0px auto;\">
    Bitte warten...
    </div>";
    echo '<script type="text/javascript">xajax_reqInfo(' . $itemId . ',"' . $type . '")</script>';
}


function getInitTT()
{
    return '<div class="tooltip" id="tooltip" style="display:none;" onmouseup="hideTT();">
<div class="tttitle" id="tttitle"></div>
<div class="ttcontent" id="ttcontent"></div>
    </div> ';
}

function cTT($title, $content)
{
    return " onclick=\"showTT('" . StringUtils::encodeDBStringToJS($title) . "','" . StringUtils::encodeDBStringToJS($content) . "',0,event,this);return false;\"  ";
}

function mTT($title, $content)
{
    return " onmouseover=\"showTT('" . StringUtils::encodeDBStringToJS($title) . "','" . StringUtils::replaceBR(StringUtils::encodeDBStringToJS($content)) . "',1,event,this);\" onmouseout=\"hideTT();\" ";
}

function tt($content)
{
    return " onmouseover=\"showTT('','" . StringUtils::encodeDBStringToJS($content) . "',1,event,this);\" onmouseout=\"hideTT();\" ";
}

function icon($name)
{
    return "<img src=\"" . (defined('IMAGE_DIR') ? IMAGE_DIR : 'images') . "/icons/" . $name . ".png\" alt=\"$name\" />";
}

function htmlSelect($name, array $data, $default = null)
{
    echo '<select name="' . $name . '">';
    foreach ($data as $k => $v) {
        echo '<option value="' . $k . '" ' . ($default == $k ? ' selected="selected"' : '') . '>' . $v . '</option>';
    }
    echo "</select>";
}

function forward($url, $msgTitle = null, $msgText = null)
{
    header("Location: " . $url);
    echo "<h1>" . $msgTitle . "</h1><p>" . $msgText . "</p><p>Falls die Weiterleitung nicht klappt, <a href=\"" . $url . "l\">hier</a> klicken...</p>";
    exit;
}

function defineImagePaths()
{
    // TODO
    global $cu;
    global $app;

    /** @var ConfigurationService */
    $config = $app[ConfigurationService::class];

    if (!defined('IMAGE_PATH')) {
        if (!isset($cu) && isset($_SESSION['user_id'])) {
            $cu = new CurrentUser($_SESSION['user_id']);
        }

        $design = DESIGN_DIRECTORY . "/official/" . $config->get('default_css_style');
        if (isset($cu) && $cu->properties->cssStyle != '') {
            if (is_dir(DESIGN_DIRECTORY . "/custom/" . $cu->properties->cssStyle)) {
                $design = DESIGN_DIRECTORY . "/custom/" . $cu->properties->cssStyle;
            } else if (is_dir(DESIGN_DIRECTORY . "/official/" . $cu->properties->cssStyle)) {
                $design = DESIGN_DIRECTORY . "/official/" . $cu->properties->cssStyle;
            }
        }
        define('CSS_STYLE', $design);

        // Image paths
        if (isset($cu) && $cu->properties->imageUrl != '' && $cu->properties->imageExt != '') {
            define('IMAGE_PATH', $cu->properties->imageUrl);
            define('IMAGE_EXT', $cu->properties->imageExt);
        } else {
            define("IMAGE_PATH", (ADMIN_MODE ? '../' : '') . $config->get('default_image_path'));
            define("IMAGE_EXT", "png");
        }
    }
}

function logAccess($target, $domain = "", $sub = "")
{
    // TODO
    global $app;

    /** @var ConfigurationService */
    $config = $app[ConfigurationService::class];

    if ($config->getBoolean('accesslog')) {
        if (!isset($_SESSION['accesslog_sid']))
            $_SESSION['accesslog_sid'] = uniqid((string) mt_rand(), true);
        dbquery("
        INSERT INTO
        accesslog
        (target,timestamp,sid,sub,domain)
        VALUES ('$target',UNIX_TIMESTAMP(),'" . $_SESSION['accesslog_sid'] . "','$sub','$domain');");
    }
}

/**
 * Checks wether a given config file exists
 */
function configFileExists($file)
{
    return file_exists(getConfigFilePath($file));
}

function getConfigFilePath($file)
{
    return __DIR__ . '/../config/' . $file;
}

function writeConfigFile($file, $contents)
{
    file_put_contents(RELATIVE_ROOT . "config/" . $file, $contents);
}

/**
 * Fetches the contents of a JSON config file and returns it as an associative array
 */
function fetchJsonConfig($file)
{
    $path = getConfigFilePath($file);
    if (!file_exists($path)) {
        throw new EException("Config file $file not found!");
    }
    $data = json_decode(file_get_contents($path), true);
    if (json_last_error() != JSON_ERROR_NONE) {
        throw new EException("Failed to parse config file $file (JSON error " . json_last_error() . ")!");
    }
    return $data;
}

function getLoginUrl($args = array())
{
    // TODO
    global $app;

    /** @var ConfigurationService */
    $config = $app[ConfigurationService::class];

    $url = $config->get('loginurl');
    if (!$url) {
        $url = "show.php?index=login";
        if (sizeof($args) > 0 && isset($args['page'])) {
            unset($args['page']);
        }
    }
    if (count($args) > 0) {
        foreach ($args as $k => $v) {
            if (!stristr($url, '?')) {
                $url .= "?";
            } else {
                $url .= "&";
            }
            $url .= $k . "=" . $v;
        }
    }
    return $url;
}

function createZipFromDirectory($dir, $zipFile)
{

    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZIPARCHIVE::CREATE) !== TRUE) {
        throw new Exception("Cannot open ZIP file " . $zipFile);
    }

    // create recursive directory iterator
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::LEAVES_ONLY);

    // let's iterate
    foreach ($files as $name => $file) {
        $new_filename = substr($name, strlen(dirname($dir)) + 1);
        if (is_file($file)) {
            $zip->addFile($file, $new_filename);
        }
    }

    // close the zip file
    if (!$zip->close()) {
        throw new Exception("There was a problem writing the ZIP archive " . $zipFile);
    }
}

/**
 * Recursively remove a directory and its contents
 */
function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") {
                    rrmdir($dir . "/" . $object);
                } else {
                    unlink($dir . "/" . $object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

/**
 * Returns true if the debug mode is enabled
 * by checking the existence of the file config/debug
 */
function isDebugEnabled(): bool
{
    return file_exists(RELATIVE_ROOT . 'config/debug');
}

/**
 * Returns true if script is run on command line
 */
function isCLI(): bool
{
    return php_sapi_name() === 'cli';
}

function isUnixOS(): bool
{
    return defined('POSIX_F_OK');
}

function isWindowsOS(): bool
{
    return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}

/**
 * Returns true if the specified unix command exists
 */
function unix_command_exists(string $cmd): bool
{
    if (isUnixOS()) {
        return (bool) shell_exec("which $cmd 2>/dev/null");
    }
    return false;
}

function getAbsPath(string $path): string
{
    return (substr($path, 0, 1) != "/" ? realpath(RELATIVE_ROOT) . '/' : '') . $path;
}

/**
 * Textfunktionen einbinden
 */
include_once __DIR__ . '/text.inc.php';

/**
 * Remove BBCode
 */
function stripBBCode($text_to_search)
{
    $pattern = '|[[\\/\\!]*?[^\\[\\]]*?]|si';
    $replace = '';
    return preg_replace($pattern, $replace, $text_to_search);
}

/**
 * Creates a new collection from the given array of data
 *
 * @param array $data
 * @return ArrayCollection
 */
function collect(array $data): ArrayCollection
{
    return new ArrayCollection($data);
}

if (!function_exists('blank')) {
    /**
     * Determine if the given value is "blank".
     *
     * @param  mixed  $value
     * @return bool
     * @see https://github.com/illuminate/support/blob/master/helpers.php
     */
    function blank($value)
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }
}

if (!function_exists('filled')) {
    /**
     * Determine if a value is "filled".
     *
     * @param  mixed  $value
     * @return bool
     * @see https://github.com/illuminate/support/blob/master/helpers.php
     */
    function filled($value)
    {
        return !blank($value);
    }
}

function flatten(array $array): array
{
    $return = array();
    array_walk_recursive($array, function ($a) use (&$return): void {
        $return[] = $a;
    });
    return $return;
}

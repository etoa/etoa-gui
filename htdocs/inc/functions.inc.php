<?PHP

use Doctrine\Common\Collections\ArrayCollection;
use EtoA\Admin\AllianceBoardAvatar;
use EtoA\Core\AppName;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Design\Design;
use EtoA\Fleet\ForeignFleetLoader;
use EtoA\Log\AccessLogRepository;
use EtoA\Race\RaceDataRepository;
use EtoA\Specialist\SpecialistService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\ExternalUrl;
use EtoA\Support\StringUtils;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRepository;

/**
 * Returns a string containing the game name, version and round
 */
function getGameIdentifier()
{
    // TODO
    global $app;

    /** @var ConfigurationService $config */
    $config = $app[ConfigurationService::class];

    return AppName::NAME . ' ' . getAppVersion() . ' ' . $config->get('roundname');
}

function getAppVersion()
{
    require_once __DIR__ . '/../version.php';
    return APP_VERSION;
}

/**
 * User-Nick via User-Id auslesen
 */
function get_user_nick($id)
{
    global $app;

    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];
    $userNick = $userRepository->getNick($id);
    if ($userNick !== null) {
        return $userNick;
    }

    return "<i>Unbekannter Benutzer</i>";
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
    return preg_match(\EtoA\User\User::NAME_PATTERN, $name);
}

/**
 * Checks for a valid nick
 */
function checkValidNick($name)
{
    return preg_match(\EtoA\User\User::NICK_PATTERN, $name);
}

function tableStart($title = "", $width = 0, $layout = "", $id = "")
{
    if (is_numeric($width) && $width > 0) {
        $w = "width:" . $width . "px;";
    } elseif ($width != "") {
        $w = "width:" . $width . "";
    } else {
        global $cu;
        global $app;

        $w = "width:100%";
        if ($cu) {
            /** @var UserPropertiesRepository $userPropertiesRepository */
            $userPropertiesRepository = $app[UserPropertiesRepository::class];
            $properties = $userPropertiesRepository->getOrCreateProperties($cu->id);
            if ($properties->cssStyle == "Graphite") {
                $w = "width:650px";
            }
        }
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
    echo BBCodeUtils::toHTML($text);
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
    echo BBCodeUtils::toHTML($text);
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
    echo BBCodeUtils::toHTML($text);

    // Addition
    switch ($addition) {
        case 1:
            echo BBCodeUtils::toHTML("\n\n[url " . ExternalUrl::FORUM . "]Zum Forum[/url] | [email mail@etoa.ch]Mail an die Spielleitung[/email]");
            break;
        case 2:
            echo BBCodeUtils::toHTML("\n\n[url " . ExternalUrl::DEV_CENTER . "]Fehler melden[/url]");
            break;
        default:
            echo '';
    }

    // Stacktrace
    if (isset($stacktrace)) {
        echo "<div style=\"text-align:left;border-top:1px solid #000;\">
        <b>Stack-Trace:</b><br/>" . nl2br($stacktrace) . "<br/><a href=\"" . ExternalUrl::DEV_CENTER . "\" target=\"_blank\">Fehler melden</a></div>";
    }
    iBoxEnd();
    if ($exit > 0) {
        echo "</body></html>";
        exit;
    }
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
 * Wählt die verschiedenen Designs aus und schreibt sie in ein array. by Lamborghini
 */
function get_designs()
{
    $rootDir = __DIR__ . '/../' . Design::DIRECTORY;
    $designs = array();

    $rd = 'official';
    $baseDir = $rootDir . '/' . $rd;
    if ($d = opendir($baseDir)) {
        while ($f = readdir($d)) {
            $dir = $baseDir . "/" . $f;
            if (is_dir($dir) && !preg_match('/^\./', $f)) {
                $file = $dir . "/" . Design::CONFIG_FILE_NAME;
                $design = parseDesignInfoFile($file);
                if ($design != null) {
                    $design['dir'] = $dir;
                    $design['custom'] = false;
                    $designs[$f] = $design;
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
function check_fleet_incomming($user_id): int
{
    global $app;

    /** @var ForeignFleetLoader $loader */
    $loader = $app[ForeignFleetLoader::class];

    return $loader->getVisibleFleets($user_id)->aggressiveCount;
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
 * Zeigt ein Avatarbild an
 */
function show_avatar($avatar = AllianceBoardAvatar::DEFAULT_IMAGE)
{
    if ($avatar == "") $avatar = AllianceBoardAvatar::DEFAULT_IMAGE;
    echo "<div style=\"padding:8px;\">";
    echo "<img id=\"avatar\" src=\"" . AllianceBoardAvatar::IMAGE_PATH . $avatar . "\" alt=\"avatar\" style=\"width:64px;height:64px;\"/></div>";
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

/**
 * Calculates costs per level for a given building costs array
 *
 * @param int $level Level
 * @param float $fac costFactor (like specialist)
 * @return array<string, float> Array of calculated costs
 *
 */
function calcBuildingCosts(\EtoA\Building\Building $building, $level, $fac = 1)
{
    global $cp;
    global $cu;

    global $app;

    /** @var ConfigurationService $config */
    $config = $app[ConfigurationService::class];

    /** @var SpecialistService $specialistService */
    $specialistService = $app[SpecialistService::class];
    $specialist = $specialistService->getSpecialistOfUser($cu->id);
    /** @var RaceDataRepository $raceRepository */
    $raceRepository = $app[RaceDataRepository::class];
    $race = $raceRepository->getRace($cu->raceId);

    $bc = array();
    $bc['metal'] = $fac * $building->costsMetal * pow($building->buildCostsFactor, $level);
    $bc['crystal'] = $fac * $building->costsCrystal * pow($building->buildCostsFactor, $level);
    $bc['plastic'] = $fac * $building->costsPlastic * pow($building->buildCostsFactor, $level);
    $bc['fuel'] = $fac * $building->costsFuel * pow($building->buildCostsFactor, $level);
    $bc['food'] = $fac * $building->costsFood * pow($building->buildCostsFactor, $level);
    $bc['power'] = $fac * $building->costsPower * pow($building->buildCostsFactor, $level);

    $typeBuildTime = 1.0;
    $starBuildTime = 1.0;

    if (isset($cp->typeBuildtime))
        $typeBuildTime = $cp->typeBuildtime;
    if (isset($cp->starBuildtime))
        $starBuildTime = $cp->starBuildtime;

    $bonus = $race->buildTime + $typeBuildTime + $starBuildTime + ($specialist !== null ? $specialist->timeBuildings : 1) - 3;
    $bc['time'] = ($bc['metal'] + $bc['crystal'] + $bc['plastic'] + $bc['fuel'] + $bc['food']) / $config->getInt('global_time') * $config->getFloat('build_build_time');
    $bc['time'] *= $bonus;
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
    return "<img src=\"images/icons/" . $name . ".png\" alt=\"$name\" />";
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

function logAccess($target, $domain = "", $sub = "")
{
    // TODO
    global $app;

    /** @var ConfigurationService $config */
    $config = $app[ConfigurationService::class];
    /** @var AccessLogRepository $accessLogRepository */
    $accessLogRepository = $app[AccessLogRepository::class];

    if ($config->getBoolean('accesslog')) {
        if (!isset($_SESSION['accesslog_sid'])) {
            $_SESSION['accesslog_sid'] = uniqid((string) mt_rand(), true);
        }

        $accessLogRepository->add($target, $_SESSION['accesslog_sid'], $sub ?? '', $domain);
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
    file_put_contents(__DIR__ . '/../config/' . $file, $contents);
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

    /** @var ConfigurationService $config */
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

/**
 * Returns true if the debug mode is enabled
 * by checking the existence of the file config/debug
 */
function isDebugEnabled(): bool
{
    return file_exists(__DIR__ . '/../config/debug');
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
    return (substr($path, 0, 1) != "/" ? realpath(__DIR__) . '/../../htdocs/' : '') . $path;
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

function intOrNull(?string $value): ?int
{
    return $value !== null ? (int) $value : null;
}

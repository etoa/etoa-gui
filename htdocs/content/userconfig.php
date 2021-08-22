<?PHP

use EtoA\Support\StringUtils;
use EtoA\User\UserWarningRepository;

echo "<h1>Einstellungen</h1>";

/****************/
/* Menu			*/
/****************/

/** @var UserWarningRepository $userWarningRepository */
$userWarningRepository = $app[UserWarningRepository::class];

$mode = (isset($_GET['mode']) && $_GET['mode'] != "") ? $_GET['mode'] : 'general';

$tabitems = array(
    "general" => "Profil",
    "game" => "Spiel",
    "messages" => "Nachrichten",
    "design" => "Design",
    "sitting" => "Sitting",
    "dual" => "Dual",
    "password" => "Passwort",
    "logins" => "Logins",
    "banner" => "Banner",
    "misc" => "Sonstiges"
);

$warnings = $userWarningRepository->getUserWarnings($cu->getId());
if (count($warnings) > 0)
    $tabitems['warnings'] = "Verwarnungen";

show_tab_menu("mode", $tabitems);
echo '<br/>';


/****************/
/* Spiel    */
/****************/
if ($mode == 'game') {
    require("content/userconfig/game.php");
}

/****************/
/* Nachrichten    */
/****************/
elseif ($mode == 'messages') {
    // todo: sitter
    if ($s->sittingActive == 0) {
        require("content/userconfig/messages.php");
    } else {
        echo "Im Sittermodus ist dieser Bereich gesperrt!";
    }
}

/****************/
/* Verwarnungen    */
/****************/
elseif ($mode == 'warnings') {
    if ($s->sittingActive == 0) {
        tableStart("Ausgesprochene Verwarnungne");
        echo "
                    <tr>
                        <th>Text</th>
                        <th>Datum</th>
                        <th>Verwarnt von</th>
                    </tr>";

        foreach ($warnings as $warning) {
            echo "<tr>
                                <td>" . stripslashes(nl2br($warning->text)) . "</td>
                                <td>" . StringUtils::formatDate($warning->date) . "</td>
                                <td><a href=\"?page=contact&rcpt=" . $warning->adminId . "\">" . $warning->adminNick . "</a>
                                </td>
                            </tr>";
        }

        tableEnd();
    } else {
        echo "Im Sittermodus ist dieser Bereich gesperrt!";
    }
}


/****************/
/* Design     	*/
/****************/
elseif ($mode == 'design') {
    require("content/userconfig/design.php");
}

/****************/
/* Sitting			*/
/****************/
elseif ($mode == 'sitting') {
    if (!$s->sittingActive || $s->falseSitter) {
        require("content/userconfig/sitting.php");
    } else {
        echo "Im Sittermodus ist dieser Bereich gesperrt!<br><br>";
    }
}

/****************/
/* Dual			*/
/****************/
elseif ($mode == 'dual') {
    if (!$s->sittingActive) {
        require("content/userconfig/dual.php");
    } else {
        echo "Im Sittermodus ist dieser Bereich gesperrt!";
    }
}

/****************/
/* Passwort			*/
/****************/
elseif ($mode == 'password') {
    if (!$s->sittingActive) {
        require("content/userconfig/password.php");
    } else {
        echo "Im Sittermodus ist dieser Bereich gesperrt!";
    }
}

/****************/
/* Sonstiges		*/
/****************/
elseif ($mode == 'misc') {
    if (!$s->sittingActive) {
        require("content/userconfig/misc.php");
    } else {
        echo "Im Sittermodus ist dieser Bereich gesperrt!";
    }
}

/****************/
/* Logins		*/
/****************/

elseif ($mode == "logins") {
    require("content/userconfig/logins.php");
}

/****************/
/* Banner		*/
/****************/

elseif ($mode == "banner") {
    require("content/userconfig/banner.php");
}

/****************/
/* Userdaten    */
/****************/
else {
    if (!$s->sittingActive) {
        require("content/userconfig/general.php");
    } else {
        echo "Im Sittermodus ist dieser Bereich gesperrt!";
    }
}

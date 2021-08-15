<?PHP

//
// Fehlerhafte Logins
//

use EtoA\Alliance\AllianceRepository;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Race\RaceDataRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use EtoA\User\UserService;
use Symfony\Component\HttpFoundation\Request;

/** @var UserService */
$userService = $app[UserService::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var LogRepository $logRepository */
$logRepository = $app[LogRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

if ($sub == "xml") {
    require("user/xml.inc.php");
}

//
// Ip-Search
//
elseif ($sub == "ipsearch") {
    require("user/ipsearch.inc.php");
}

//
// Sessions
//
elseif ($sub == "sessions") {
    require("user/sessions.inc.php");
}

//
// Erstellen
//
elseif ($sub == "create") {
    echo "<h1>Spieler erstellen</h1>";

    if ($request->request->has('create')) {
        try {
            $newUser = $userService->register(
                $request->request->get('user_name'),
                $request->request->get('user_email'),
                $request->request->get('user_nick'),
                $request->request->get('user_password'),
                $request->request->getInt('user_race'),
                $request->request->has('user_ghost'),
                true
            );
            $logRepository->add(LogFacility::USER, LogSeverity::INFO, "Der Benutzer " . $newUser->nick . " (" . $newUser->name . ", " . $newUser->email . ") wurde registriert!");
            success_msg("Benutzer wurde erstellt! [[page user sub=edit id=" . $newUser->id . "]Details[/page]]");
        } catch (Exception $e) {
            error_msg("Benutzer konnte nicht erstellt werden!\n\n" . $e->getMessage());
        }
    }

    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
    tableStart("", "400");
    echo "<tr><th>Name:</th><td>
        <input type=\"text\" name=\"user_name\" value=\"\" />
        </td></td>";
    echo "<tr><th>E-Mail:</th><td>
        <input type=\"text\" name=\"user_email\" value=\"\" />
        </td></td>";
    echo "<tr><th>Nick:</th><td>
        <input type=\"text\" name=\"user_nick\" value=\"\" />
        </td></td>";
    echo "<tr><th>Passwort:</th><td>
        <input type=\"password\" name=\"user_password\" value=\"\" />
        </td></td>";
    echo "<tr><th>Rasse:</th><td>
        <select name=\"user_race\" />
        <option value=\"0\">Keine</option>";
    /** @var RaceDataRepository $raceRepository */
    $raceRepository = $app[RaceDataRepository::class];
    $raceNames = $raceRepository->getRaceNames();
    foreach ($raceNames as $raceId => $raceName) {
        echo "<option value=\"" . $raceId . "\">" . $raceName . "</option>";
    }
    echo "</select>
        </td></td>";
    echo "<tr><th>Geist:</th><td>
        <input type=\"radio\" name=\"user_ghost\" value=\"1\" /> Ja &nbsp;
        <input type=\"radio\" name=\"user_ghost\" value=\"0\" checked=\"checked\" /> Nein
        </td></td>";

    tableEnd();
    echo "<p><input type=\"submit\" name=\"create\" value=\"Erstellen\" /></p>
        </form>";
}


//
// Fehlerhafte Logins
//
elseif ($sub == "specialists") {
    advanced_form("specialists", $twig);
}

//
// Fehlerhafte Logins
//
elseif ($sub == "loginfailures") {
    require("user/loginfailures.inc.php");
}

//
// Beobachter
//
elseif ($sub == "observed") {
    require("user/observed.inc.php");
}

//
// Tickets
//
elseif ($sub == "tickets") {
    require("user/tickets.inc.php");
}


//
// Verwarnungen
//
elseif ($sub == "warnings") {
    require("user/warnings.inc.php");
}

//
// Bilder prüfen
//
elseif ($sub == "imagecheck") {
    require("user/imagecheck.inc.php");
}

//
// User banner
//
elseif ($sub == "userbanner") {
    require("user/userbanner.inc.php");
}

//
// Session-Log
//
elseif ($sub == "userlog") {
    require("user/userlog.inc.php");
}

//
// Rassen
//
elseif ($sub == "race") {
    advanced_form("races", $twig);
}

//
// Änderungsanträge
//
elseif ($sub == "requests") {
    require("user/requests.inc.php");
}

//
// Punkteverlauf
//
elseif ($sub == "point") {
    require("user/point.inc.php");
}

//
// Multisuche
//
elseif ($sub == "multi") {
    require("user/multi.inc.php");
}

//
// Sittings
//
elseif ($sub == "sitting") {
    require("user/sitting.inc.php");
}


//
// User-Suchergebnisse anzeigen
//

else {
    $twig->addGlobal("title", 'Spieler');

    if ((isset($_GET['special']) || isset($_POST['user_search']) || isset($_SESSION['admin']['user_query'])) && isset($_GET['action']) && $_GET['action'] == "search") {
        $twig->addGlobal("subtitle", 'Suchergebnisse');

        $userSearch = UserSearch::create();
        if (isset($_GET['special'])) {
            switch ($_GET['special']) {
                case "ip":
                    $userSearch->ip(base64_decode($_GET['val'], true));
                    break;
                case "host":
                    $userSearch->hostname(base64_decode($_GET['val'], true));
                    break;
                case "blocked":
                    $userSearch->blocked();
                    break;
                default:
                    $userSearch->nickLike(base64_decode($_GET['val'], true));
            }
            $_SESSION['admin']['user_query'] = serialize($userSearch);
        } elseif ($_SESSION['admin']['user_query'] == "") {
            $sql = '';
            if ($_POST['user_id'] != "") {
                $userSearch->user($_POST['user_id']);
            }
            if (isset($_POST['user_nick_search']) != "") {
                $userSearch->nickLike($_POST['user_nick_search']);
            }
            if ($_POST['user_nick'] != "") {
                $userSearch->nickLike($_POST['user_nick']);
            }
            if ($_POST['user_name'] != "") {
                $userSearch->nameLike($_POST['user_name']);
            }
            if ($_POST['user_email'] != "") {
                $userSearch->emailLike($_POST['user_email']);
            }
            if ($_POST['user_email_fix'] != "") {
                $userSearch->emailFixLike($_POST['user_email_fix']);
            }
            if ($_POST['user_password'] != "") {
                $userSearch->password(md5($_POST['user_password'])); // I don't think this works
            }
            if ($_POST['user_ip'] != "") {
                $userSearch->ipLike($_POST['user_ip']);
            }
            if ($_POST['user_alliance'] != "") {
                $userSearch->allianceLike($_POST['user_alliance']);
            }
            if ($_POST['user_race_id'] != "") {
                $userSearch->race($_POST['user_race_id']);
            }
            if ($_POST['user_profile_text'] != "") {
                $sql .= " AND user_profile_text LIKE '%" . $_POST['user_profile_text'] . "%'";
            }
            if (isset($_POST['user_hmode']) && $_POST['user_hmode'] < 2) {
                if ($_POST['user_hmode'] == 1)
                    $userSearch->inHmode();
                else
                    $userSearch->notInHmode();
            }
            if (isset($_POST['user_blocked']) && $_POST['user_blocked'] < 2) {
                if ($_POST['user_blocked'] == 1)
                    $userSearch->blocked();
                else
                    $userSearch->notBlocked();
            }
            if (isset($_POST['user_chatadmin']) && $_POST['user_chatadmin'] < 2) {
                $userSearch->chatadmin($_POST['user_chatadmin'] == 1);
            }
            if (isset($_POST['user_ghost']) && $_POST['user_ghost'] < 2) {
                $userSearch->ghost($_POST['user_ghost'] == 1);
            }

            $_SESSION['admin']['user_query'] = serialize($userSearch);
        } else {
            if (isset($_SESSION['admin']['user_query'])) {
                $userSearch = unserialize($_SESSION['admin']['user_query'], ['allowed_classes' => [UserSearch::class]]);
            }
        }

        $users = $userRepository->searchUsers($userSearch);
        $nr = count($users);
        if ($nr == 1) {
            $user = array_pop($users);
            echo "<script>document.location='?page=$page&sub=edit&id=" . $user->id . "';</script>
                Klicke <a href=\"?page=$page&sub=edit&id=" . $user->id . "\">hier</a> falls du nicht automatisch weitergeleitet wirst...";
        } elseif ($nr > 0) {
            echo $nr . " Datens&auml;tze vorhanden<br/><br/>";
            if ($nr > 20) {
                echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /><br/><br/>";
            }

            /** @var RaceDataRepository */
            $raceRepository = $app[RaceDataRepository::class];
            $raceNames = $raceRepository->getRaceNames();
            /** @var AllianceRepository */
            $allianceRepository = $app[AllianceRepository::class];
            $allianceNameWithTags = $allianceRepository->getAllianceNamesWithTags();
            $time = time();

            tableStart();
            echo "<tr>";
            echo "<th>ID</th>";
            echo "<th>Nick</th>";
            echo "<th>Status</th>";
            echo "<th>Name</th>";
            echo "<th>E-Mail</th>";
            echo "<th>Dual Name</th>";
            echo "<th>Dual E-Mail</th>";
            echo "<th>Punkte</th>";
            echo "<th>Allianz</th>";
            echo "<th>Rasse</th>
                <th></th>";
            echo "</tr>";
            foreach ($users as $user) {
                if ($user->blockedFrom < $time && $user->blockedTo > $time) {
                    $status = "Gesperrt";
                    $uCol = ' class="userLockedColor"';
                } elseif ($user->hmodFrom < $time && $user->hmodTo > $time) {
                    $status = "Urlaub";
                    $uCol = ' class="userHolidayColor"';
                } elseif ($user->deleted > 0) {
                    $status = "Löschauftrag";
                    $uCol = ' class="userDeletedColor"';
                } elseif ($user->admin != 0) {
                    $status = "Admin";
                    $uCol = ' class="adminColor"';
                } elseif ($user->ghost) {
                    $status = "Geist";
                    $uCol = ' class="userGhostColor"';
                } else {
                    $status = 'Spieler';
                    $uCol = "";
                }
                echo "<tr>";
                echo "<td>" . $user->id . "</td>";
                echo "<td><a href=\"?page=$page&amp;sub=edit&amp;id=" . $user->id . "\">" . $user->nick . "</a></td>";
                echo "<td " . $uCol . ">" . $status . "</td>";
                echo "<td title=\"" . $user->name . "\">" . cut_string($user->name, 15) . "</td>";
                echo "<td title=\"" . $user->email . "\">" . cut_string($user->email, 15) . "</td>";
                echo "<td title=\"" . $user->dualName . "\">" . cut_string($user->dualName, 15) . "</td>";
                echo "<td title=\"" . $user->dualEmail . "\">" . cut_string($user->dualEmail, 15) . "</td>";
                echo "<td>" . nf($user->points) . "</td>";
                echo "<td>" . ($user->allianceId > 0 ? $allianceNameWithTags[$user->allianceId] : '-') . "</td>";
                echo "<td>" . ($user->raceId > 0 ? $raceNames[$user->raceId] : '-') . "</td>";
                echo "<td>
                    " . edit_button("?page=$page&amp;sub=edit&amp;id=" . $user->id) . "
                    </td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<p><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> &nbsp; ";
            echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=search'\" value=\"Aktualisieren\" /></p>";
        } else {
            $twig->addGlobal('infoMessage', "Die Suche lieferte keine Resultate!");
            echo "<p><input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" /></p>";
        }
    }

    //
    // User-Daten bearbeiten
    //

    elseif ($sub == "edit") {
        require("user/edit.inc.php");
    }

    //
    // Suchmaske
    //

    else {
        $twig->addGlobal("subtitle", 'Suchmaske');

        $_SESSION['admin']['user_query'] = "";
        echo "<form action=\"?page=$page&amp;action=search\" method=\"post\">";
        echo "<table class=\"tbl\">";
        echo "<tr><th>ID</th><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
        echo "<tr><th>Nickname</th><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" id=\"user_nick\"  value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\"/> <br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td></tr>";
        echo "<tr><th>Name</th><td class=\"tbldata\"><input type=\"text\" name=\"user_name\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
        echo "<tr><th>E-Mail</th><td class=\"tbldata\"><input type=\"text\" name=\"user_email\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
        echo "<tr><th>Fixe E-Mail</th><td class=\"tbldata\"><input type=\"text\" name=\"user_email_fix\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
        echo "<tr><th>Passwort</th><td class=\"tbldata\"><input type=\"text\" name=\"user_password\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
        echo "<tr><th>IP-Adresse</th><td class=\"tbldata\"><input type=\"text\" name=\"user_ip\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
        echo "<tr><th>Allianz</th><td class=\"tbldata\"><input type=\"text\" name=\"user_alliance\" id=\"user_alliance\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchAlliance(this.value,'user_alliance','citybox2');\"/> <br><div class=\"citybox\" id=\"citybox2\">&nbsp;</div></td></tr>";

        /** @var RaceDataRepository */
        $raceRepository = $app[RaceDataRepository::class];
        $raceNames = $raceRepository->getRaceNames();

        echo "<tr><th>Rasse</th><td class=\"tbldata\"><select name=\"user_race_id\">";
        echo "<option value=\"\">(egal)</option>";
        foreach ($raceNames as $id => $raceName) {
            echo "<option value=\"$id\">" . $raceName . "</option>";
        }
        echo "</select></td></tr>";
        echo "<tr><th>Profil-Text</th><td class=\"tbldata\"><input type=\"text\" name=\"user_profile_text\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
        echo "<tr><th>Urlaubsmodus</th><td class=\"tbldata\">
                <input type=\"radio\" name=\"user_hmode\" value=\"2\" checked=\"checked\" /> Egal &nbsp;
                <input type=\"radio\" name=\"user_hmode\" value=\"0\" /> Nein &nbsp;
                <input type=\"radio\" name=\"user_hmode\" value=\"1\" /> Ja</td></tr>";
        echo "<tr><th>Gesperrt</th><td class=\"tbldata\">
                <input type=\"radio\" name=\"user_blocked\" value=\"2\" checked=\"checked\" /> Egal &nbsp;
                <input type=\"radio\" name=\"user_blocked\" value=\"0\" /> Nein &nbsp;
                <input type=\"radio\" name=\"user_blocked\" value=\"1\"  /> Ja</td></tr>";
        echo "<tr><th>Geist</th><td class=\"tbldata\"><input type=\"radio\" name=\"user_ghost\" value=\"2\" checked=\"checked\" /> Egal &nbsp; <input type=\"radio\" name=\"user_ghost\" value=\"0\" /> Nein &nbsp; <input type=\"radio\" name=\"user_ghost\" value=\"1\"  /> Ja</td></tr>";
        echo "<tr><th>Chat-Admin</th><td class=\"tbldata\"><input type=\"radio\" name=\"user_chatadmin\" value=\"2\" checked=\"checked\" /> Egal &nbsp; <input type=\"radio\" name=\"user_chatadmin\" value=\"0\" /> Nein &nbsp; <input type=\"radio\" name=\"user_chatadmin\" value=\"1\"  /> Ja</td></tr>";
        echo "</table>";
        echo "<br/><input type=\"submit\" name=\"user_search\" value=\"Suche starten\" /> (wenn nichts eingegeben wird werden alle Datens&auml;tze angezeigt)</form>";

        echo "<br/>Es sind " . nf($userRepository->count()) . " Eintr&auml;ge in der Datenbank vorhanden.";
    }
}

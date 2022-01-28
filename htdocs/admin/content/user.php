<?PHP

//
// Fehlerhafte Logins
//

use EtoA\Admin\Forms\RacesForm;
use EtoA\Admin\Forms\SpecialistsForm;
use EtoA\Alliance\AllianceRepository;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Race\RaceDataRepository;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use EtoA\User\UserService;
use Symfony\Component\HttpFoundation\Request;

/** @var UserService $userService */
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
    SpecialistsForm::render($app, $request);
}

//
// Beobachter
//
elseif ($sub == "observed") {
    require("user/observed.inc.php");
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
// Rassen
//
elseif ($sub == "race") {
    RacesForm::render($app, $request);
}

//
// Änderungsanträge
//
elseif ($sub == "requests") {
    require("user/requests.inc.php");
}

//
// Multisuche
//
elseif ($sub == "multi") {
    require("user/multi.inc.php");
}

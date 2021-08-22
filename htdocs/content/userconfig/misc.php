<?PHP

use EtoA\Backend\BackendMessageService;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\StringUtils;
use EtoA\User\UserHolidayService;
use EtoA\User\UserRepository;
use EtoA\User\UserService;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var BackendMessageService $backendMessageService */
$backendMessageService = $app[BackendMessageService::class];

/** @var UserService $userService */
$userService = $app[UserService::class];
/** @var UserHolidayService $userHolidayService */
$userHolidayService = $app[UserHolidayService::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

$umod = false;

//
// Urlaubsmodus einschalten
//

if (isset($_POST['hmod_on']) && checker_verify()) {
    if ($userHolidayService->activateHolidayMode($cu->getId())) {
        success_msg("Du bist nun im Urlaubsmodus bis [b]" . StringUtils::formatDate(time()) . "[/b].");
        $userService->addToUserLog($cu->id, "settings", "{nick} ist nun im Urlaub.", true);
        $umod = true;
    } else {
        error_msg("Es sind noch Flotten unterwegs!");
    }
}

//
// Urlaubsmodus aufheben
//

if (isset($_POST['hmod_off']) && checker_verify()) {
    $user = $userRepository->getUser($cu->getId());
    if ($user->deleted === 0 && $userHolidayService->deactivateHolidayMode($user)) {
        success_msg("Urlaubsmodus aufgehoben! Denke daran, auf allen deinen Planeten die Produktion zu überprüfen!");
        $userService->addToUserLog($cu->id, "settings", "{nick} ist nun aus dem Urlaub zurück.", true);

        echo '<input type="button" value="Zur Übersicht" onclick="document.location=\'?page=overview\'" />';
    } else {
        error_msg("Urlaubsmodus kann nicht aufgehoben werden!");
    }
}

//
// Löschbestätigung
//
elseif (isset($_POST['remove']) && checker_verify()) {
    echo "<form action=\"?page=$page&amp;mode=misc\" method=\"post\">";
    iBoxStart("Löschung bestätigen");
    echo "Soll dein Account wirklich zur Löschung vorgeschlagen werden?<br/><br/>";
    echo "<b>Passwort eingeben:</b> <input type=\"password\" name=\"remove_password\" value=\"\" />";
    iBoxEnd();
    echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page&mode=misc'\" />
        <input type=\"submit\" name=\"remove_submit\" value=\"Account l&ouml;schen\" />";
    echo "</form>";
}

//
// User löschen
//
elseif (isset($_POST['remove_submit'])) {
    if ($userService->deleteRequest($cu->id, $_POST['remove_password'])) {
        $s = Null;
        session_destroy();
        success_msg("Deine Daten werden am " . StringUtils::formatDate(time() + ($config->getInt('user_delete_days') * 3600 * 24)) . " Uhr von unserem System gelöscht! Wir w&uuml;nschen weiterhin viel Erfolg im Netz!");
        $userHolidayService->activateHolidayMode($cu->getId(), true);
        $userService->addToUserLog($cu->id, "settings", "{nick} hat seinen Account zur Löschung freigegeben.", true);
        echo '<input type="button" value="Zur Startseite" onclick="document.location=\'' . getLoginUrl() . '\'" />';
    } else {
        error_msg("Falsches Passwort!");
        echo '<input type="button" value="Weiter" onclick="document.location=\'?page=userconfig&mode=misc\'" />';
    }
}

//
// Löschantrag aufheben
//
elseif (isset($_POST['remove_cancel']) && checker_verify()) {
    $userService->revokeDelete($cu->id);
    success_msg("Löschantrag aufgehoben!");
    $userService->addToUserLog($cu->id, "settings", "{nick} hat seine Accountlöschung aufgehoben.", true);
    echo '<input type="button" value="Weiter" onclick="document.location=\'?page=userconfig&mode=misc\'" />';
}

//
// Auswahl
//
else {
    echo "<form action=\"?page=$page&amp;mode=misc\" method=\"post\">";
    checker_init();
    tableStart("Sonstige Accountoptionen");

    // Urlaubsmodus
    if ($cu->deleted == 0) {
        echo "<tr><th style=\"width:150px;\">Urlaubsmodus</th>
        <td>Im Urlaubsmodus kannst du nicht angegriffen werden, aber deine Produktion steht auch still. </br> Dauer: mindestens " . $config->getInt('hmode_days') . " Tage, nach " . $config->param1Int('hmode_days') . " Tagen Urlaubsmodus wird der Account inaktiv und kann wieder angegriffen werden.</td>
        <td>";

        if ($cu->hmode_from > 0 && $cu->hmode_from < time() && $cu->hmode_to < time()) {
            echo "<input type=\"submit\" style=\"color:#0f0\" name=\"hmod_off\" value=\"Urlaubsmodus deaktivieren\" />";
        } elseif ($cu->hmode_from > 0 && $cu->hmode_from < time() && $cu->hmode_to >= time() || $umod) {
            echo "<span style=\"color:#f90\">Urlaubsmodus ist aktiv bis mindestens <b>" . StringUtils::formatDate($cu->hmode_to) . "</b>!</span>";
        } else {
            echo "<input type=\"submit\" value=\"Urlaubsmodus aktivieren\" name=\"hmod_on\" onclick=\"return confirm('Soll der Urlaubsmodus wirklich aktiviert werden?')\" />";
        }
        echo "</td></tr>";
    } else {
        echo "<tr><th style=\"width:150px;\">Urlaubsmodus</th>
        <td>Um den Urlaubsmodus zu beenden, musst du erst die Accountlöschung aufheben</td>
        <td>";
        echo "</td></tr>";
    }

    // Account löschen
    echo "<tr><th>Account l&ouml;schen</th>
    <td>Hier kannst du deinen Account mitsamt aller Daten löschen. Dafür wird der Account automatisch in den Urlaubsmodus gesetzt und nach " . $config->getInt('user_delete_days') . " Tagen gelöscht.</td>
    <td>";
    if ($cu->deleted > 0) {
        echo "<input type=\"submit\" name=\"remove_cancel\" value=\"Löschantrag aufheben\"  style=\"color:#0f0\" />";
    } else {
        echo "<input type=\"submit\" name=\"remove\" value=\"Account l&ouml;schen\" />";
    }
    echo "</td></tr>";

    tableEnd();
    echo "</form>";
}

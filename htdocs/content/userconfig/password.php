<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserService;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var UserService $userService */
$userService = $app[UserService::class];

iBoxStart("Logins");
echo "Solltest du Probleme mit dem Passwort haben schreibe
    ein " . ticketLink("Ticket", 17) . " oder " . popUp("kontaktiere", "page=contact") . " einen Admin.";
iBoxEnd();

// Änderungen speichern
if (isset($_POST['password_submit']) && checker_verify()) {
    try {
        $userService->setPassword($cu->id, $_POST['user_password'], $_POST['user_password1'], $_POST['user_password2']);
        success_msg("Das Passwort wurde ge&auml;ndert!");
    } catch (Exception $ex) {
        error_msg($ex->getMessage());
    }
}

// Formular anzeigen
$cstr = checker_init();
echo "<form action=\"?page=$page&mode=password\" method=\"post\">";
echo $cstr;
tableStart("Passwort &auml;ndern");
echo "<tr><th>Altes Passwort:</th><td><input type=\"password\" name=\"user_password\" maxlength=\"255\" size=\"20\" autocomplete=\"off\"></td></tr>";
echo "<tr><th>Neues Passwort (mind. " . $config->getInt('password_minlength') . " Zeichen):</th><td><input type=\"password\" name=\"user_password1\" maxlength=\"255\" size=\"20\"  autocomplete=\"off\"></td></tr>";
echo "<tr><th>Neues Passwort wiederholen:</th><td><input type=\"password\" name=\"user_password2\" maxlength=\"255\" size=\"20\"  autocomplete=\"off\"></td></tr>";
tableEnd();
echo "Beachte dass Passw&ouml;rter eine L&auml;nge von mindestens " . $config->getInt('password_minlength') . " Zeichen haben m&uuml;ssen!<br/><br/>";
echo "<input type=\"submit\" name=\"password_submit\" value=\"Passwort &auml;ndern\"></form><br/><br/>";

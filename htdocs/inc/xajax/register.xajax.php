<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserRepository;

$xajax->register(XAJAX_FUNCTION, 'registerCheckName');
$xajax->register(XAJAX_FUNCTION, 'registerCheckNick');
$xajax->register(XAJAX_FUNCTION, 'registerCheckEmail');
$xajax->register(XAJAX_FUNCTION, 'registerCheckPassword');

//Überprüft die Korrektheit der Eingabe von Vor- und Nachname
function registerCheckName($val)
{
    global $db;

    $objResponse = new xajaxResponse();
    if (checkValidName($val)) {
        if (preg_match("/^.+ .+( .*)*$/", $val)) {
            $objResponse->assign('nameStatus', 'innerHTML', "Ok");
            $objResponse->assign('nameStatus', 'style.color', "#0f0");
        } else {
            $objResponse->assign('nameStatus', 'innerHTML', "Der Name muss Vor- und Nachname enthalten!");
            $objResponse->assign('nameStatus', 'style.color', "#f90");
        }
    } else {
        $objResponse->assign('nameStatus', 'innerHTML', "Der Name darf keine ungültigen Zeichen enthalten!");
        $objResponse->assign('nameStatus', 'style.color', "#f90");
    }

    $objResponse->assign('nameStatus', 'style.fontWeight', "bold");
    return $objResponse;
}

//Überprüft die Korrektheit des Nicks und prüft ob dieser schon vorhanden ist
function registerCheckNick($val)
{
    // TODO
    global $app;

    /** @var ConfigurationService */
    $config = $app[ConfigurationService::class];
    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];

    $objResponse = new xajaxResponse();
    $objResponse->assign('nickStatus', 'style.fontWeight', "bold");
    if (checkValidNick($val)) {
        if (strlen($val) >= $config->param1Int('nick_length')) {
            if ($userRepository->getUserIdByNick($val) !== null) {
                $objResponse->assign('nickStatus', 'innerHTML', "Dieser Benutzername wird bereits benutzt!");
                $objResponse->assign('nickStatus', 'style.color', "#f90");
            } else {
                $objResponse->assign('nickStatus', 'innerHTML', "Ok");
                $objResponse->assign('nickStatus', 'style.color', "#0f0");
            }
        } else {
            $objResponse->assign('nickStatus', 'innerHTML', "Der Benutzername ist noch zu kurz (Mind. " . $config->param1Int('nick_length') . " Zeichen)!");
            $objResponse->assign('nickStatus', 'style.color', "#f90");
        }
    } else {
        $objResponse->assign('nickStatus', 'innerHTML', "Der Benutzername ist nicht korrekt!");
        $objResponse->assign('nickStatus', 'style.color', "#f90");
    }

    return $objResponse;
}

//Überprüft die Korrektheit der Eingabe von der Email Adresse und prüft ob diese schon vorhanden ist
function registerCheckEmail($val)
{
    $objResponse = new xajaxResponse();
    if (checkEmail($val)) {
        $res = dbquery("SELECT user_id FROM users WHERE user_email='$val' OR user_email_fix='$val';");
        if (mysql_num_rows($res) > 0) {
            $objResponse->assign('emailStatus', 'innerHTML', "Diese E-Mail-Adresse wird bereits benutzt!");
            $objResponse->assign('emailStatus', 'style.color', "#f90");
            $objResponse->assign('emailStatus', 'style.fontWeight', "bold");
        } else {
            $objResponse->assign('emailStatus', 'innerHTML', "Ok");
            $objResponse->assign('emailStatus', 'style.color', "#0f0");
            $objResponse->assign('emailStatus', 'style.fontWeight', "bold");
        }
    } else {
        $objResponse->assign('emailStatus', 'innerHTML', "Du musst eine korrekte E-Mail-Adresse eingeben!");
        $objResponse->assign('emailStatus', 'style.color', "#f90");
        $objResponse->assign('emailStatus', 'style.fontWeight', "bold");
    }

    return $objResponse;
}

//Überprüft die Korrektheit des Passworts
function registerCheckPassword($val)
{
    // TODO
    global $app;

    /** @var ConfigurationService */
    $config = $app[ConfigurationService::class];

    $objResponse = new xajaxResponse();
    $objResponse->assign('passwordStatus', 'style.fontWeight', "bold");

    if (strlen($val) >= $config->getInt('password_minlength')) {
        $objResponse->assign('passwordStatus', 'innerHTML', "Ok");
        $objResponse->assign('passwordStatus', 'style.color', "#0f0");
    } else {
        $objResponse->assign('passwordStatus', 'innerHTML', "Das Passwort ist noch zu kurz (mind. " . $config->getInt('password_minlength') . " Zeichen sind nötig)!");
        $objResponse->assign('passwordStatus', 'style.color', "#f90");
    }

    return $objResponse;
}

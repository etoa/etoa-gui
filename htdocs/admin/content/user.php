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

//
// Ip-Search
//
if ($sub == "ipsearch") {
    require("user/ipsearch.inc.php");
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

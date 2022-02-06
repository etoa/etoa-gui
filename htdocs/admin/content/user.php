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
// Fehlerhafte Logins
//
if ($sub == "specialists") {
    SpecialistsForm::render($app, $request);
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

elseif ($sub == "edit") {
    require("user/edit.inc.php");
}

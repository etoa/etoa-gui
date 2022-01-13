<?PHP

use EtoA\Admin\Forms\DefenseCategoriesForm;
use EtoA\Admin\Forms\DefensesForm;
use EtoA\Admin\Forms\ObjectTransformsForm;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Core\ObjectWithImage;
use EtoA\Defense\Defense;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseListSearch;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseQueueSearch;
use EtoA\Defense\DefenseRepository;
use EtoA\Defense\DefenseSort;
use EtoA\Ranking\RankingService;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var DefenseDataRepository $defenseDataRepository */
$defenseDataRepository = $app[DefenseDataRepository::class];
$defenseNames = $defenseDataRepository->getDefenseNames(true);

/** @var DefenseRepository $defenseRepository */
$defenseRepository = $app[DefenseRepository::class];

/** @var DefenseQueueRepository $defenseQueueRepository */
$defenseQueueRepository = $app[DefenseQueueRepository::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var PlanetRepository $planetRepository */
$planetRepository = $app[PlanetRepository::class];

/** @var RankingService $rankingService */
$rankingService = $app[RankingService::class];

$request = Request::createFromGlobals();

//
//
//
if ($sub == "transforms") {
    ObjectTransformsForm::render($app, $request);
}

//
// Bearbeiten
//
elseif ($sub == "data") {
    DefensesForm::render($app, $request);
}

//
// Kategorien
//
elseif ($sub == "cat") {
    DefenseCategoriesForm::render($app, $request);
}

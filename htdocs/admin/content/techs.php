<?PHP

use EtoA\Admin\Forms\TechnologiesForm;
use EtoA\Admin\Forms\TechnologyTypesForm;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Core\ObjectWithImage;
use EtoA\Ranking\RankingService;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyListItemSearch;
use EtoA\Technology\TechnologyPointRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Technology\TechnologySort;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var TechnologyRepository $technologyRepository */
$technologyRepository = $app[TechnologyRepository::class];

/** @var TechnologyDataRepository $technologyDataRepository */
$technologyDataRepository = $app[TechnologyDataRepository::class];

/** @var TechnologyPointRepository $technologyPointRepository */
$technologyPointRepository = $app[TechnologyPointRepository::class];

/** @var RankingService $rankingService */
$rankingService = $app[RankingService::class];

/** @var PlanetRepository $planetRepository */
$planetRepository = $app[PlanetRepository::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

if ($sub == "type") {
    TechnologyTypesForm::render($app, $request);
}

//
// Technologien
//
elseif ($sub == "data") {
    TechnologiesForm::render($app, $request);
}
//
// Anforderungen
//
elseif ($sub == "req") {

    define("TITLE", "Forschungsanforderungen");
    define("REQ_TBL", "tech_requirements");
    define("ITEM_ENABLE_FLD", "tech_show");

    define("ITEM_IMAGE_PATH", ObjectWithImage::BASE_PATH . "/technologies/technology<DB_TABLE_ID>_small.png");

    $objectNames = $technologyDataRepository->getTechnologyNames(true, TechnologySort::type());
    include("inc/requirements.inc.php");
}

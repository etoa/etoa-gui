<?PHP

use EtoA\Admin\Forms\AllianceBuildingsForm;
use EtoA\Admin\Forms\AllianceTechnologiesForm;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceService;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var AllianceRepository $repository */
$repository = $app[AllianceRepository::class];

/** @var AllianceService $service */
$service = $app[AllianceService::class];

/** @var AllianceRankRepository $allianceRankRepository */
$allianceRankRepository = $app[AllianceRankRepository::class];

/** @var AllianceDiplomacyRepository $allianceDiplomacyRepository */
$allianceDiplomacyRepository = $app[AllianceDiplomacyRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

if ($sub == "buildingsdata") {
    AllianceBuildingsForm::render($app, $request);
} elseif ($sub == "techdata") {
    AllianceTechnologiesForm::render($app, $request);
} else {
    \EtoA\Admin\LegacyTemplateTitleHelper::$title = 'Allianzen';

    if ($request->query->has('sub') && $request->query->get('sub') == "edit") {
        include("alliance/edit.inc.php");
    }
}

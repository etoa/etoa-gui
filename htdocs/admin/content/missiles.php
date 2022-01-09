<?PHP

use EtoA\Admin\Forms\MissilesForm;
use EtoA\Core\ObjectWithImage;
use EtoA\Missile\MissileDataRepository;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;

/** @var Request */
$request = Request::createFromGlobals();

if ($sub == "data") {
    editMissileData($app, $request);
} elseif ($sub == "req") {
    missileRequirements();
}

function editMissileData(Container $app, Request $request): void
{
    MissilesForm::render($app, $request);
}

function missileRequirements(): void
{
    global $app;

    define("TITLE", "Raketemanforderungen");
    define("REQ_TBL", "missile_requirements");
    define("ITEM_IMAGE_PATH", ObjectWithImage::BASE_PATH . "/missiles/missile<DB_TABLE_ID>_small.png");

    /** @var MissileDataRepository $missileDataRepository */
    $missileDataRepository = $app[MissileDataRepository::class];
    $objectNames = $missileDataRepository->getMissileNames(true);
    include __DIR__ . '/../inc/requirements.inc.php';
}

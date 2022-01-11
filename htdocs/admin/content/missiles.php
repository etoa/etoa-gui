<?PHP

use EtoA\Admin\Forms\MissilesForm;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;

/** @var Request */
$request = Request::createFromGlobals();

if ($sub == "data") {
    editMissileData($app, $request);
}

function editMissileData(Container $app, Request $request): void
{
    MissilesForm::render($app, $request);
}

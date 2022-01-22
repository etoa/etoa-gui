<?PHP

use EtoA\Admin\Forms\BuildingsForm;
use EtoA\Admin\Forms\BuildingTypesForm;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;

/** @var Request $request */
$request = Request::createFromGlobals();

if ($sub == "type") {
    editCategories($app, $request);
} elseif ($sub == "data") {
    editData($app, $request);
}

function editCategories(Container $app, Request $request)
{
    BuildingTypesForm::render($app, $request);
}

function editData(Container $app, Request $request)
{
    BuildingsForm::render($app, $request);
}

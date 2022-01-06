<?PHP

use EtoA\Admin\Forms\PlanetTypesForm;
use EtoA\Admin\Forms\StarTypesForm;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;

/** @var Request $request */
$request = Request::createFromGlobals();

if ($sub == "planet_types") {
    planetTypes($app, $request);
} elseif ($sub == "sol_types") {
    starTypes($app, $request);
}

function planetTypes(Container $app, Request $request)
{
    PlanetTypesForm::render($app, $request);
}

function starTypes(Container $app, Request $request)
{
    StarTypesForm::render($app, $request);
}

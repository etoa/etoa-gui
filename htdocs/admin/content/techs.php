<?PHP

use EtoA\Admin\Forms\TechnologiesForm;
use EtoA\Admin\Forms\TechnologyTypesForm;
use Symfony\Component\HttpFoundation\Request;

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

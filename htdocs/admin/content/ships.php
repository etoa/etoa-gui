<?PHP

use EtoA\Admin\Forms\ShipCategoriesForm;
use EtoA\Admin\Forms\ShipsForm;
use Symfony\Component\HttpFoundation\Request;

/** @var Request */
$request = Request::createFromGlobals();

//
// Kategorien
//
if ($sub == "cat") {
    ShipCategoriesForm::render($app, $request);
}

//
// Daten
//
elseif ($sub == "data") {
    ShipsForm::render($app, $request);
}

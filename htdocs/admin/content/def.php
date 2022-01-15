<?PHP

use EtoA\Admin\Forms\DefenseCategoriesForm;
use EtoA\Admin\Forms\DefensesForm;
use EtoA\Admin\Forms\ObjectTransformsForm;
use Symfony\Component\HttpFoundation\Request;

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

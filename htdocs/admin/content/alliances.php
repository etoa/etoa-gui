<?PHP

use EtoA\Admin\Forms\AllianceBuildingsForm;
use EtoA\Admin\Forms\AllianceTechnologiesForm;
use Symfony\Component\HttpFoundation\Request;

/** @var Request */
$request = Request::createFromGlobals();

if ($sub == "buildingsdata") {
    AllianceBuildingsForm::render($app, $request);
} elseif ($sub == "techdata") {
    AllianceTechnologiesForm::render($app, $request);
}

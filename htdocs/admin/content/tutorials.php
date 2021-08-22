<?PHP

use EtoA\Admin\Forms\TutorialsForm;
use Symfony\Component\HttpFoundation\Request;

/** @var Request $request */
$request = Request::createFromGlobals();

TutorialsForm::render($app, $twig, $request);

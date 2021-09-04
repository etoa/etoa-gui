<?PHP

//
// Start-Items
//

use EtoA\Admin\Forms\TicketCategoriesForm;
use EtoA\Admin\Forms\TippsForm;
use Symfony\Component\HttpFoundation\Request;

/** @var Request $request */
$request = Request::createFromGlobals();

if ($sub == "defaultitems") {
    include("config/defaultitems.inc.php");
}

//
// Tipps
//
elseif ($sub == "tipps") {
    TippsForm::render($app, $twig, $request);
}

//
// Ticket-Cat
//
elseif ($sub == "ticketcat") {
    TicketCategoriesForm::render($app, $twig, $request);
}

//
// Designs
//
elseif ($sub == "designs") {
    include("misc/designs.inc.php");
}

else {
    echo "<h1>Diverses</h1>";
    echo "Wähle eine Unterseite aus dem Menü!";
}

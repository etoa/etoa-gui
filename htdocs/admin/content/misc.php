<?PHP

//
// Start-Items
//

use EtoA\Admin\Forms\TicketCategoriesForm;
use EtoA\Admin\Forms\TippsForm;
use Symfony\Component\HttpFoundation\Request;

/** @var Request $request */
$request = Request::createFromGlobals();

//
// Tipps
//
if ($sub == "tipps") {
    TippsForm::render($app, $request);
}

//
// Ticket-Cat
//
elseif ($sub == "ticketcat") {
    TicketCategoriesForm::render($app, $request);
}

else {
    echo "<h1>Diverses</h1>";
    echo "Wähle eine Unterseite aus dem Menü!";
}

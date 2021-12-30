<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\Fleet;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearchParameters;
use EtoA\Fleet\FleetService;
use EtoA\Fleet\FleetStatus;
use EtoA\Fleet\InvalidFleetParametersException;
use EtoA\Ship\ShipDataRepository;
use EtoA\Support\StringUtils;
use EtoA\UI\EntityCoordinatesSelector;
use EtoA\UI\ShipSelector;
use EtoA\UI\UserSelector;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var EntityRepository $entityRepository */
$entityRepository = $app[EntityRepository::class];

/** @var EntityService $entityService */
$entityService = $app[EntityService::class];

/** @var FleetRepository $fleetRepository */
$fleetRepository = $app[FleetRepository::class];

/** @var FleetService $fleetService */
$fleetService = $app[FleetService::class];

/** @var EntityCoordinatesSelector $entityCoordinatesSelector */
$entityCoordinatesSelector = $app[EntityCoordinatesSelector::class];

/** @var UserSelector $userSelector */
$userSelector = $app[UserSelector::class];

/** @var ShipSelector $shipSelector */
$shipSelector = $app[ShipSelector::class];

/** @var Request */
$request = Request::createFromGlobals();

if ($sub == "fleetoptions") {
    fleetOptions($request, $config);
}

function fleetOptions(Request $request, ConfigurationService $config): void
{
    global $page;
    global $sub;

    echo "<h1>Flottenoptionen</h1>";

    //
    // Updates
    //

    // Flottensperre deaktivieren
    if ($request->request->has('flightban_deactivate')) {
        $config->set('flightban', 0, '');
    }

    // Flottensperre aktivieren
    if ($request->request->has('flightban_activate') || $request->request->has('flightban_update')) {
        $flightban_from = parseDatePicker('flightban_time_from', $request->request->all());
        $flightban_to = parseDatePicker('flightban_time_to', $request->request->all());

        if ($flightban_from < $flightban_to) {
            $config->set('flightban', 1, $request->request->get('flightban_reason'));
            $config->set('flightban_time', '', $flightban_from, $flightban_to);
        } else {
            echo "<b>Fehler:</b> Das Ende muss nach dem Start erfolgen!<br><br>";
        }
    }

    // Kampfsperre deaktivieren
    if ($request->request->has('battleban_deactivate')) {
        $config->set('battleban', 0, '');
    }

    // Kampfsperre aktivieren
    if ($request->request->has('battleban_activate') || $request->request->has('battleban_update')) {
        $battleban_from = parseDatePicker('battleban_time_from', $request->request->all());
        $battleban_to = parseDatePicker('battleban_time_to', $request->request->all());

        if ($battleban_from < $battleban_to) {
            $config->set('battleban', 1, $request->request->get('battleban_reason'));
            $config->set('battleban_time', '', $battleban_from, $battleban_to);
            $config->set('battleban_arrival_text', '', $request->request->get('battleban_arrival_text_fleet'), $request->request->get('battleban_arrival_text_missiles'));
        } else {
            echo "<b>Fehler:</b> Das Ende muss nach dem Start erfolgen!<br><br>";
        }
    }

    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";

    //
    // Flottensperre
    //

    // Setzt Variabeln wenn Flottensperre aktiv...
    if ($config->getBoolean('flightban')) {
        // Prüft, ob die Sperre zum jetzigen Zeitpunkt gilt
        if ($config->param1Int('flightban_time') <= time() && $config->param2Int('flightban_time') >= time()) {
            $flightban_time_status = "Sie wirkt zum jetzigen Zeitpunkt!";
        } elseif ($config->param1Int('flightban_time') > time() && $config->param2Int('flightban_time') > time()) {
            $flightban_time_status = "Sie wirkt erst ab: " . date("d.m.Y H:i", $config->param1Int('flightban_time')) . "!";
        } else {
            $flightban_time_status = "Sie ist nun aber abgelaufen!";
        }

        $flightban_status = "<div style=\"color:#f90\">Die Flottensperre ist aktiviert! " . $flightban_time_status . "</div>";
        $flightban_time_from = $config->param1Int('flightban_time');
        $flightban_time_to = $config->param2Int('flightban_time');
        $flightban_reason = $config->param1('flightban');
        $flightban_button = "<input type=\"submit\" name=\"flightban_update\" value=\"Aktualisieren\" /> <input type=\"submit\" name=\"flightban_deactivate\" value=\"Deaktivieren\" />";
    }
    // ...wenn nicht aktiv
    else {
        $flightban_status = "<div style=\"color:#0f0\">Die Flottensperre ist deaktiviert!</div>";
        $flightban_time_from = time();
        $flightban_time_to = time() + 3600;
        $flightban_reason = "";
        $flightban_button = "<input type=\"submit\" name=\"flightban_activate\" value=\"Aktivieren\" />";
    }

    echo "<h2>Flottensperre</h2><table class=\"tbl\">";
    echo "<tr>
                    <td class=\"tbltitle\" width=\"15%\">Info</td>
                    <td class=\"tbldata\" width=\"85%\">Es können keine FlÜge gestartet werden</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\" width=\"15%\">Status</td>
                    <td class=\"tbldata\" width=\"85%\">" . $flightban_status . "</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Von</td>
                    <td class=\"tbldata\">";
    echo showDatepicker("flightban_time_from", $flightban_time_from, true);
    echo "</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Bis</td>
                    <td class=\"tbldata\">";
    echo showDatepicker("flightban_time_to", $flightban_time_to, true);
    echo "</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Grund</td>
                    <td class=\"tbldata\">
                        <textarea name=\"flightban_reason\" cols=\"50\" rows=\"3\">" . $flightban_reason . "</textarea>
                    </td>
                </tr>
            </table>
            <p>" . $flightban_button . "</p><br/>";

    //
    // Kampfsperre
    //

    // Setzt Variabeln wenn Kampfsperre aktiv...
    if ($config->getBoolean('battleban')) {
        // Prüft, ob die Sperre zum jetzigen Zeitpunkt gilt
        if ($config->param1Int('battleban_time') <= time() && $config->param2Int('battleban_time') >= time()) {
            $battleban_time_status = "Sie wirkt zum jetzigen Zeitpunkt!";
        } elseif ($config->param1Int('battleban_time') > time() && $config->param2Int('battleban_time') > time()) {
            $battleban_time_status = "Sie wirkt erst ab: " . date("d.m.Y H:i", $config->param1Int('battleban_time')) . "!";
        } else {
            $battleban_time_status = "Sie ist nun aber abgelaufen!";
        }

        $battleban_status = "<div style=\"color:#f90\">Die Kampfsperre ist aktiviert! " . $battleban_time_status . "</div>";
        $battleban_time_from = $config->param1Int('battleban_time');
        $battleban_time_to = $config->param2Int('battleban_time');
        $battleban_reason = $config->param1('battleban');
        $battleban_button = "<input type=\"submit\" name=\"battleban_update\" value=\"Aktualisieren\" /> <input type=\"submit\" name=\"battleban_deactivate\" value=\"Deaktivieren\" />";
    }
    // ...wenn nicht aktiv
    else {
        $battleban_status = "<div style=\"color:#0f0\">Die Kampfsperre ist deaktiviert!</div>";
        $battleban_time_from = time();
        $battleban_time_to = time() + 3600;
        $battleban_reason = "";
        $battleban_button = "<input type=\"submit\" name=\"battleban_activate\" value=\"Aktivieren\" />";
    }

    echo "<h2>Kampfsperre</h2>
    <table class=\"tbl\">";
    echo "<tr>
                    <td class=\"tbltitle\" width=\"15%\">Info</td>
                    <td class=\"tbldata\" width=\"85%\">Es können keine Angriffe geflogen werden</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\" width=\"15%\">Status</td>
                    <td class=\"tbldata\" width=\"85%\">" . $battleban_status . "</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Von</td>
                    <td class=\"tbldata\">";
    showDatepicker("battleban_time_from", $battleban_time_from, true);
    echo "</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Bis</td>
                    <td class=\"tbldata\">";
    showDatepicker("battleban_time_to", $battleban_time_to, true);
    echo "</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Grund</td>
                    <td class=\"tbldata\">
                        <textarea name=\"battleban_reason\" cols=\"50\" rows=\"3\">" . $battleban_reason . "</textarea>
                    </td>
                </tr>
                <tr>
                    <td class=\"tbltitle\" colspan=\"2\"><div style=\"text-align:center;\">Ankunftstext während Sperre</div></td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Flotten</td>
                    <td class=\"tbldata\">
                        <textarea name=\"battleban_arrival_text_fleet\" cols=\"50\" rows=\"3\">" . $config->param1('battleban_arrival_text') . "</textarea>
                    </td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Raketen</td>
                    <td class=\"tbldata\">
                        <textarea name=\"battleban_arrival_text_missiles\" cols=\"50\" rows=\"3\">" . $config->param2('battleban_arrival_text') . "</textarea>
                    </td>
                </tr>
            </tr>
        </table>
        <p>" . $battleban_button . "</p>";

    echo "</form>";
}

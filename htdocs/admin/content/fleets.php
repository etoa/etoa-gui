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
use EtoA\UI\EntityCoordinatesSelector;
use EtoA\UI\ShipSelector;
use EtoA\UI\UserSelector;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
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
} else {
    fleets(
        $request,
        $twig,
        $userRepository,
        $shipDataRepository,
        $planetRepo,
        $entityRepository,
        $entityService,
        $fleetRepository,
        $fleetService,
        $entityCoordinatesSelector,
        $userSelector,
        $shipSelector
    );
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

function fleets(
    Request $request,
    Environment $twig,
    UserRepository $userRepository,
    ShipDataRepository $shipDataRepository,
    PlanetRepository $planetRepo,
    EntityRepository $entityRepository,
    EntityService $entityService,
    FleetRepository $fleetRepository,
    FleetService $fleetService,
    EntityCoordinatesSelector $entityCoordinatesSelector,
    UserSelector $userSelector,
    ShipSelector $shipSelector
): void {
    global $page;
    global $sub;

    $twig->addGlobal('title', "Flotten");

    //
    // Flotte bearbeiten
    //
    if ($request->query->getInt('fleetedit') > 0) {
        echo "<h2>Flotte bearbeiten</h2>";

        $fleet = $fleetRepository->find($request->query->getInt('fleetedit'));
        if ($fleet !== null) {
            if ($request->request->has('submit_edit')) {
                $launchTime = parseDatePicker('launchtime', $request->request->all());
                $landTime = parseDatePicker('landtime', $request->request->all());
                if ($landTime <= $launchTime) {
                    $landTime = $launchTime + 60;
                }
                $fleet->launchTime = $launchTime;
                $fleet->landTime = $landTime;

                $srcCoords = $entityCoordinatesSelector->parse('start', $request->request);
                if ($srcCoords !== null) {
                    $srcEnt = $entityRepository->findByCoordinates($srcCoords);
                    if ($srcEnt !== null) {
                        $fleet->entityFrom = $srcEnt->id;
                    } else {
                        error_msg("Startentität nicht vorhanden");
                    }
                }

                $trgCoords = $entityCoordinatesSelector->parse('end', $request->request);
                if ($trgCoords !== null) {
                    $trgEnt = $entityRepository->findByCoordinates($trgCoords);
                    if ($trgEnt !== null) {
                        $fleet->entityTo = $trgEnt->id;
                    } else {
                        error_msg("Zielentität nicht vorhanden");
                    }
                }

                $fleet->userId = $request->request->getInt('user_id');
                $fleet->action = $request->request->get('action');
                $fleet->status = $request->request->getInt('status');
                $fleet->pilots = $request->request->getInt('pilots');
                $fleet->usageFuel = $request->request->getInt('usage_fuel');
                $fleet->usageFood = $request->request->getInt('usage_food');
                $fleet->usagePower = $request->request->getInt('usage_power');
                $fleet->resMetal = $request->request->getInt('res_metal');
                $fleet->resCrystal = $request->request->getInt('res_crystal');
                $fleet->resPlastic = $request->request->getInt('res_plastic');
                $fleet->resFuel = $request->request->getInt('res_fuel');
                $fleet->resFood = $request->request->getInt('res_food');
                $fleet->resPower = $request->request->getInt('res_power');
                $fleet->resPeople = $request->request->getInt('res_people');
                $fleet->fetchMetal = $request->request->getInt('fetch_metal');
                $fleet->fetchCrystal = $request->request->getInt('fetch_crystal');
                $fleet->fetchPlastic = $request->request->getInt('fetch_plastic');
                $fleet->fetchFuel = $request->request->getInt('fetch_fuel');
                $fleet->fetchFood = $request->request->getInt('fetch_food');
                $fleet->fetchPower = $request->request->getInt('fetch_power');
                $fleet->fetchPeople = $request->request->getInt('fetch_people');

                $fleetRepository->save($fleet);

                success_msg("Flottendaten geändert!");
            }

            // Cancel flight
            if ($request->request->has('submit_cancel')) {
                try {
                    $fleetService->cancel($fleet->id);
                    $fleet = $fleetRepository->find($request->query->getInt('fleetedit'));
                } catch (InvalidFleetParametersException $ex) {
                    error_msg("Kann Flotte nicht abbrechen: " . $ex->getMessage());
                }
            }

            // Return flight
            if ($request->request->has('submit_return')) {
                try {
                    $fleetService->cancel($fleet->id, true);
                    $fleet = $fleetRepository->find($request->query->getInt('fleetedit'));
                } catch (InvalidFleetParametersException $ex) {
                    error_msg("Kann Flotte nicht zurücksenden: " . $ex->getMessage());
                }
            }

            // Land fleet
            if ($request->request->has('submit_land')) {
                try {
                    $fleetService->land($fleet->id);
                    unset($fleet);
                } catch (InvalidFleetParametersException $ex) {
                    error_msg("Kann Flotte nicht landen: " . $ex->getMessage());
                }
            }

            if (isset($fleet)) {
                fleetEditForm(
                    $request,
                    $fleet,
                    $fleetRepository,
                    $entityRepository,
                    $entityService,
                    $shipDataRepository,
                    $userSelector,
                    $shipSelector,
                    $entityCoordinatesSelector
                );
            } else {
                echo MessageBox::error("", "Flotte nicht mehr vorhanden!");
            }

            echo "<br/><br/><input type=\"button\" value=\"Zurück zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" /> ";
            echo "<input type=\"button\" value=\"Neue zur Suche\" onclick=\"document.location='?page=$page&sub=$sub'\" />";
        } else {
            echo MessageBox::error("", "Datensatz nicht vorhanden!");
        }
    } elseif ($request->request->has('fleet_search') || $request->query->get('action') == "searchresults") {
        if ($request->query->getInt('fleetdel') > 0) {
            deleteFleet($request, $fleetRepository);
        }

        fleetSearchResults(
            $request,
            $fleetRepository,
            $entityRepository,
            $entityService,
            $userRepository,
            $entityCoordinatesSelector,
            $twig
        );
    } else {
        echo '<div class="tabs">
        <ul>
            <li><a href="#tabs-1">Suchmaske</a></li>
            <li><a href="#tabs-2">Flotte erstellen</a></li>
            <li><a href="#tabs-3">Schiffe senden</a></li>
        </ul>
        <div id="tabs-1">';

        $_SESSION['fleetedit']['query'] = null;

        fleetSearchForm(
            $entityCoordinatesSelector
        );

        echo '</div><div id="tabs-2">';

        if ($request->request->has('submit_new_fleet')) {
            createNewFleet(
                $request,
                $entityRepository,
                $fleetRepository,
                $entityCoordinatesSelector,
                $twig
            );
        }

        createNewFleetForm(
            $userSelector,
            $entityCoordinatesSelector,
            $shipSelector
        );

        echo '</div><div id="tabs-3">';

        if ($request->request->has('submit_send_ships')) {
            sendNewFleet(
                $request,
                $entityRepository,
                $planetRepo,
                $fleetRepository,
                $entityCoordinatesSelector,
                $twig
            );
        }

        sendNewFleetForm(
            $entityCoordinatesSelector,
            $shipSelector
        );

        echo '</div>
        </div>';

        echo "<br/>Es sind " . nf($fleetRepository->count()) . " Einträge in der Datenbank vorhanden.";
    }
}

function fleetEditForm(
    Request $request,
    Fleet $fleet,
    FleetRepository $fleetRepository,
    EntityRepository $entityRepository,
    EntityService $entityService,
    ShipDataRepository $shipDataRepository,
    UserSelector $userSelector,
    ShipSelector $shipSelector,
    EntityCoordinatesSelector $entityCoordinatesSelector
): void {
    global $page;
    global $sub;

    echo "<form action=\"?page=$page&amp;sub=$sub&amp;fleetedit=" . $request->query->getInt('fleetedit') . "\" method=\"post\">";
    echo "<table class=\"tbl\">";

    // Owner
    echo "<tr><th class=\"tbltitle\">Besitzer:</th><td class=\"tbldata\">";
    echo $userSelector->getHTML('user_id', $fleet->userId);
    echo "</td></tr>";

    // Time Data
    echo "<tr><th class=\"tbltitle\">Startzeit:</th><td class=\"tbldata\">";
    showDatepicker("launchtime", $fleet->launchTime, true, true);
    echo "</td></tr>";
    echo "<tr><th class=\"tbltitle\">Landezeit:</th><td class=\"tbldata\">";
    showDatepicker("landtime", $fleet->landTime, true, true);
    echo " &nbsp; Flugdauer: " . tf($fleet->landTime - $fleet->launchTime) . "</td></tr>";

    // Origin
    $srcEnt = $entityRepository->findIncludeCell($fleet->entityFrom);
    echo "<tr><td class=\"tbltitle\">Startzelle</td><td class=\"tbldata\">";
    echo $entityCoordinatesSelector->getHTML('start', $srcEnt->getCoordinates(), false);
    echo "&nbsp; " . $entityService->formattedString($srcEnt);
    echo "</td></tr>";

    // Destination
    $trgEnt = $entityRepository->findIncludeCell($fleet->entityTo);
    echo "<tr><td class=\"tbltitle\">Endzelle</td><td class=\"tbldata\">";
    echo $entityCoordinatesSelector->getHTML('end', $trgEnt->getCoordinates(), false);
    echo " &nbsp; " . $entityService->formattedString($trgEnt);
    echo "</td></tr>";

    // Action
    echo "<tr><td class=\"tbltitle\">Aktion:</td><td class=\"tbldata\"><select name=\"action\">";
    echo "<option value=\"\">(egal)</option>";
    $fas = \FleetAction::getAll();
    foreach ($fas as $fa) {
        echo "<option value=\"" . $fa->code() . "\" style=\"color:" . \FleetAction::$attitudeColor[$fa->attitude()] . "\"";
        if ($fleet->action == $fa->code())
            echo " selected=\"selected\"";

        echo ">" . $fa->name() . "</option>";
    }
    echo "</select> &nbsp; <select name=\"status\">";
    echo "<option value=\"\">(egal)</option>";
    foreach (\FleetAction::$statusCode as $k => $v) {
        echo "<option value=\"" . $k . "\" ";
        if ($fleet->status == $k)
            echo " selected=\"selected\"";
        echo ">" . $v . "</option>";
    }
    echo "</select></td></tr>";

    // Usage
    echo "<tr><td style=\"background:#000;height:2px;\" colspan=\"2\"></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Piloten:</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"pilots\" value=\"" . $fleet->pilots . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Verbrauch: " . RES_FUEL . ":</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"usage_fuel\" value=\"" . $fleet->usageFuel . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Verbrauch: " . RES_FOOD . ":</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"usage_food\" value=\"" . $fleet->usageFood . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Verbrauch: " . RES_POWER . ":</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"usage_power\" value=\"" . $fleet->usagePower . "\" size=\"10\" /></td></tr>";

    // Freight
    echo "<tr><td style=\"background:#000;height:2px;\" colspan=\"2\"></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Fracht: " . RES_METAL . ":</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"res_metal\" value=\"" . $fleet->resMetal . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Fracht: " . RES_CRYSTAL . "::</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"res_crystal\" value=\"" . $fleet->resCrystal . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Fracht: " . RES_PLASTIC . ":</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"res_plastic\" value=\"" . $fleet->resPlastic . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Fracht: " . RES_FUEL . ":</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"res_fuel\" value=\"" . $fleet->resFuel . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Fracht: " . RES_FOOD . ":</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"res_food\" value=\"" . $fleet->resFood . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Fracht: " . RES_POWER . ":</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"res_power\" value=\"" . $fleet->resPower . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Passagiere:</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"res_people\" value=\"" . $fleet->resPeople . "\" size=\"10\" /></td></tr>";

    echo "<tr><td style=\"background:#000;height:2px;\" colspan=\"2\"></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Abholen: " . RES_METAL . ":</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"fetch_metal\" value=\"" . $fleet->fetchMetal . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Abholen: " . RES_CRYSTAL . "::</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"fetch_crystal\" value=\"" . $fleet->fetchCrystal . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Abholen: " . RES_PLASTIC . ":</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"fetch_plastic\" value=\"" . $fleet->fetchPlastic . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Abholen: " . RES_FUEL . ":</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"fetch_fuel\" value=\"" . $fleet->fetchFuel . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Abholen: " . RES_FOOD . ":</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"fetch_food\" value=\"" . $fleet->fetchFood . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Abholen: " . RES_POWER . ":</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"fetch_power\" value=\"" . $fleet->fetchPower . "\" size=\"10\" /></td></tr>";
    echo "<tr>
        <td class=\"tbltitle\">Abholen: Passagiere:</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"fetch_people\" value=\"" . $fleet->fetchPeople . "\" size=\"10\" /></td></tr>";

    echo "</table><br/>
        <input type=\"submit\" value=\"Übernehmen\" name=\"submit_edit\" /> ";
    if ($fleet->status == FleetStatus::DEPARTURE) {
        echo "<input type=\"submit\" value=\"Flug abbrechen\" name=\"submit_cancel\" />
        <input type=\"submit\" value=\"Flug zurückschicken\" name=\"submit_return\" />
        <input type=\"submit\" value=\"Flotte auf dem Ziel landen\" name=\"submit_land\" />";
    } else {
        echo "<input type=\"submit\" value=\"Flotte auf dem Ziel landen\" name=\"submit_land\" />";
    }
    echo "</form><br/>";

    // Ships
    echo "<h3>Schiffe der Flotte bearbeiten</h3>";
    if ($request->request->get('newship_submit', '') != "" && $request->request->getInt('fs_ship_cnt_new') > 0 && $request->request->getInt('fs_ship_id_new') > 0) {
        $fleetRepository->addShipsToFleet(
            $request->query->getInt('fleetedit'),
            $request->request->getInt('fs_ship_id_new'),
            $request->request->getInt('fs_ship_cnt_new')
        );
        success_msg("Schiffe hinzugefügt");
    }
    if ($request->request->get('editship_submit', '') != "") {
        foreach ($request->request->get('fs_ship_cnt') as $ship => $cnt) {
            $fleetRepository->updateShipsInFleet(
                $request->query->getInt('fleetedit'),
                (int) $ship,
                (int) $cnt
            );
        }
        success_msg("Schiffe geändert");
    }
    if ($request->query->getInt('shipdel') > 0) {
        $fleetRepository->removeShipsFromFleet(
            $request->query->getInt('fleetedit'),
            $request->query->getInt('shipdel')
        );
        success_msg("Schiffe gelöscht");
    }

    $shipEntries = $fleetRepository->findAllShipsInFleet($request->query->getInt('fleetedit'));
    if (count($shipEntries) > 0) {
        $shipNames = $shipDataRepository->getShipNames(true);

        echo "<form action=\"?page=$page&amp;sub=$sub&amp;fleetedit=" . $request->query->getInt('fleetedit') . "\" method=\"post\">";

        echo "<table class=\"tbl\">";
        echo "<tr><th class=\"tbltitle\">Typ</th><th class=\"tbltitle\">Anzahl</th><th class=\"tbltitle\">&nbsp;</th></tr>";
        foreach ($shipEntries as $shipEntry) {
            echo "<tr><td class=\"tbldata\">" . ($shipNames[$shipEntry->shipId] ?? 'Unknown') . "</td>";
            echo "<td class=\"tbldata\">
                <input type=\"text\" name=\"fs_ship_cnt[" . $shipEntry->shipId . "]\" value=\"" . $shipEntry->count . "\" size=\"5\" /></td>";
            echo "<td class=\"tbldata\">
                <a href=\"?page=$page&amp;sub=$sub&amp;fleetedit=" . $request->query->getInt('fleetedit') . "&amp;shipdel=" . $shipEntry->shipId . "\" onclick=\"return confirm('Soll " . ($shipNames[$shipEntry->shipId] ?? 'Unknown') . " wirklich aus der Flotte entfernt werden?');\">Löschen</a></td>";
            echo "</tr>";
        }
        echo "</table><br/>";

        // Zeigt alle gefakten schiffe in der flotte
        $fakedShips = $fleetRepository->findAllShipsInFleet($fleet->id, true);
        if (count($fakedShips) > 0) {
            echo "<table class=\"tbl\">";
            echo "<tr><th class=\"tbltitle\" colspan=\"3\">Gefakte Schiffe</th></tr>";
            echo "<tr><th class=\"tbltitle\">Typ</th><th class=\"tbltitle\">Anzahl</th><th class=\"tbltitle\">&nbsp;</th></tr>";
            foreach ($fakedShips as $shipEntry) {
                echo "<tr><td class=\"tbldata\">" . ($shipNames[$shipEntry->shipId] ?? 'Unbekannt') . "</td>";
                echo "<td class=\"tbldata\"><input type=\"text\" name=\"fs_ship_cnt[" . $shipEntry->shipId . "]\" value=\"" . $shipEntry->count . "\" size=\"5\" /></td>";
                echo "<td class=\"tbldata\"><a href=\"?page=$page&amp;sub=$sub&amp;fleetedit=" . $request->query->getInt('fleetedit') . "&amp;shipdel=" . $shipEntry->shipId . "\" onclick=\"return confirm('Soll " . ($shipNames[$shipEntry->shipId] ?? 'Unbekannt') . " wirklich aus der Flotte entfernt werden?');\">Löschen</a></td>";
                echo "</tr>";
            }
            echo "</table><br/>";
        }

        echo "<input type=\"submit\" name=\"editship_submit\" value=\"Änderungen übernehmen\" />
        <br/><br/>";

        echo "<input type=\"text\" name=\"fs_ship_cnt_new\" value=\"1\" size=\"5\" /> Schiffe des Typs";
        echo $shipSelector->getHTML('fs_ship_id_new');
        echo "<input type=\"submit\" name=\"newship_submit\" value=\"Hinzufügen\" />";
        echo "</form><br/>";
    } else {
        echo MessageBox::error("", "Diese Flotte besitzt keine Schiffe!");
    }
}

function deleteFleet(Request $request, FleetRepository $fleetRepository): void
{
    $fleetRepository->removeAllShipsFromFleet($request->query->getInt('fleetdel'));
    $fleetRepository->remove($request->query->getInt('fleetdel'));
    echo MessageBox::ok("", "Die Flotte wurde gelöscht!");
}

function fleetSearchResults(
    Request $request,
    FleetRepository $fleetRepository,
    EntityRepository $entityRepository,
    EntityService $entityService,
    UserRepository $userRepository,
    EntityCoordinatesSelector $entityCoordinatesSelector,
    Environment $twig
): void {
    global $page;
    global $sub;

    if (isset($_SESSION['fleetedit']['query'])) {
        $searchParams = $_SESSION['fleetedit']['query'];
    } else {
        $searchParams = new FleetSearchParameters();

        if ($request->request->getInt('entity_from_id') > 0) {
            $searchParams->entityFrom = $request->request->getInt('entity_from_id');
        } else {
            $srcCoords = $entityCoordinatesSelector->parse('start', $request->request);
            if ($srcCoords !== null) {
                $srcEnt = $entityRepository->findByCoordinates($srcCoords);
                if ($srcEnt !== null) {
                    $searchParams->entityFrom = $srcEnt->id;
                } else {
                    error_msg("Startentität existiert nicht, Bedingung ausgelassen!");
                }
            }
        }

        if ($request->request->getInt('entity_to_id') > 0) {
            $searchParams->entityTo = $request->request->getInt('entity_to_id');
        } else {
            $trgCoords = $entityCoordinatesSelector->parse('end', $request->request);
            if ($trgCoords !== null) {
                $trgEnt = $entityRepository->findByCoordinates($trgCoords);
                if ($trgEnt !== null) {
                    $searchParams->entityTo = $trgEnt->id;
                } else {
                    error_msg("Zielentität existiert nicht, Bedingung ausgelassen!");
                }
            }
        }

        if ($request->request->get('fleet_action', '') != "") {
            if ($request->request->get('fleet_action') == "-") {
                $searchParams->action = '';
            } else {
                $searchParams->action = $request->request->get('fleet_action');
            }
        }

        if ($request->request->getInt('user_id') > 0) {
            $searchParams->userId = $request->request->getInt('user_id');
        } elseif ($request->request->get('user_nick', '') != "") {
            $searchParams->userNick = $request->request->get('user_nick');
        }

        if ($request->request->getInt('fleet_id') > 0) {
            $searchParams->id = $request->request->getInt('id');
        }
    }
    $fleets = $fleetRepository->findByParameters($searchParams);

    if (count($fleets) > 0) {

        $_SESSION['fleetedit']['query'] = $searchParams;

        echo count($fleets)  . " Datensätze vorhanden<br/><br/>";
        if (count($fleets) > 20) {
            echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /><br/><br/>";
        }

        echo "<table class=\"tbl\">";
        echo "<tr>";
        echo "<td class=\"tbltitle\">Besitzer</td>";
        echo "<td class=\"tbltitle\">Aktion</td>";
        echo "<td class=\"tbltitle\">Status</td>";
        echo "<td class=\"tbltitle\">Start</td>";
        echo "<td class=\"tbltitle\">Ziel</td>";
        echo "<td class=\"tbltitle\">Startzeit</td>";
        echo "<td class=\"tbltitle\">Landezeit</td>";
        echo "<td class=\"tbltitle\">Aktion</td>";
        echo "</tr>";
        foreach ($fleets as $fleet) {
            $stl = "";
            if ($fleet->landTime < time()) {
                $stl = "style=\"color:orange;\"";
            }

            echo "<tr>";

            $owner = $fleet->userId > 0
                ? $userRepository->getNick($fleet->userId)
                : "<span style=\"color:#99f\">System</span>";
            echo "<td class=\"tbldata\" $stl>" . $owner . "</td>";

            if ($fleetAction = \FleetAction::createFactory($fleet->action)) {
                echo "<td class=\"tbldata\">";
                echo "<span style=\"color:" . \FleetAction::$attitudeColor[$fleetAction->attitude()] . "\">" . $fleetAction . "</span>";
                echo "</td>";
                echo "<td class=\"tbldata\">";
                echo \FleetAction::$statusCode[$fleet->status];
                echo "</td>";
            } else {
                echo "<td class=\"tbldata\" colspan=\"2\">";
                echo "<span style=\"color:red\">Ungültig (" . $fleet->action . ")</span><br/>";
                echo "</td>";
            }

            $startEntity = $entityRepository->findIncludeCell($fleet->entityFrom);
            echo "<td class=\"tbldata\" $stl>" . $entityService->formattedString($startEntity) . "</td>";

            $endEntity = $entityRepository->findIncludeCell($fleet->entityTo);
            echo "<td class=\"tbldata\" $stl>" . $entityService->formattedString($endEntity) . "</td>";

            echo "<td class=\"tbldata\" $stl>" . date("d.m.y", $fleet->launchTime) . " &nbsp; " . date("H:i:s", $fleet->launchTime) . "</td>";
            echo "<td class=\"tbldata\" $stl>" . date("d.m.y", $fleet->landTime) . " &nbsp; " . date("H:i:s", $fleet->landTime) . "</td>";

            echo "<td class=\"tbldata\">";
            echo edit_button("?page=$page&amp;sub=$sub&fleetedit=" . $fleet->id) . " ";
            echo del_button("?page=$page&amp;sub=$sub&fleetdel=" . $fleet->id . "&amp;action=searchresults", "return confirm('Soll diese Flotte wirklich gelöscht werden?');");
            echo "</tr>";
        }
        echo "</table>";
        echo "<br/><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /> ";
        echo "<input type=\"button\" value=\"Aktualisieren\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults'\" />";
    } else {
        $twig->addGlobal("infoMessage", "Die Suche lieferte keine Resultate!");
        echo "<p><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /></p>";
        $_SESSION['fleetedit']['query'] = null;
    }
}

function fleetSearchForm(
    EntityCoordinatesSelector $entityCoordinatesSelector
): void {
    global $page;
    global $sub;

    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
    echo "<table class=\"tbl\">";
    echo "<tr><td class=\"tbltitle\">Startentität-Koordinaten</td><td class=\"tbldata\">";
    echo $entityCoordinatesSelector->getHTML('start');
    echo "</td></tr>";
    echo "<tr><td class=\"tbltitle\">Zielentität-Koordinaten</td><td class=\"tbldata\">";
    echo $entityCoordinatesSelector->getHTML('end');
    echo "</td></tr>";
    echo "<tr><td class=\"tbltitle\">Startentität-ID</td><td class=\"tbldata\"><input type=\"text\" name=\"entity_from_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
    echo "<tr><td class=\"tbltitle\">Zielentität-ID</td><td class=\"tbldata\"><input type=\"text\" name=\"entity_to_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
    echo "<tr><td class=\"tbltitle\">Flottenaktion</td><td class=\"tbldata\"><select name=\"fleet_action\">";
    echo "<option value=\"\">(egal)</option>";
    $fas = \FleetAction::getAll();
    foreach ($fas as $fa) {
        echo "<option value=\"" . $fa->code() . "\">" . $fa->name() . "</option>";
    }
    echo "<option value=\"-\">(keine)</option>";
    echo "</select></td></tr>";
    echo "<tr><td class=\"tbltitle\">Flotten ID</td><td class=\"tbldata\"><input type=\"text\" name=\"fleet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
    echo "<tr><td class=\"tbltitle\">Besitzer ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
    echo "<tr><td class=\"tbltitle\">Besitzer Nick</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" /> ";
    echo "</td></tr>";

    echo "</table>";
    echo "<br/><input type=\"submit\" class=\"button\" name=\"fleet_search\" value=\"Suche starten\" />";
    echo "</form>";
}

function createNewFleet(
    Request $request,
    EntityRepository $entityRepository,
    FleetRepository $fleetRepository,
    EntityCoordinatesSelector $entityCoordinatesSelector,
    Environment $twig
): void {
    global $page;
    global $sub;

    $srcCoords = $entityCoordinatesSelector->parse('start', $request->request);
    if ($srcCoords === null) {
        $twig->addGlobal('errorMessage', "Ungültige Startkoordinaten.");
        return;
    }

    $srcEnt = $entityRepository->findByCoordinates($srcCoords);
    if ($srcEnt === null) {
        $twig->addGlobal('errorMessage', "Startentität nicht vorhanden.");
        return;
    }

    $trgCoords = $entityCoordinatesSelector->parse('end', $request->request);
    if ($trgCoords === null) {
        $twig->addGlobal('errorMessage', "Ungültige Zielkoordinaten.");
        return;
    }

    $trgEnt = $entityRepository->findByCoordinates($trgCoords);
    if ($trgEnt === null) {
        $twig->addGlobal('errorMessage', "Zielentität nicht vorhanden.");
        return;
    }

    $launchtime = parseDatePicker('launchtime', $request->request->all());
    $landtime = parseDatePicker('landtime', $request->request->all());

    $shipId = $request->request->getInt('fs_ship_id_new');
    if ($shipId <= 0) {
        $twig->addGlobal('errorMessage', "Kein Schiff angegeben.");
        return;
    }

    $shipCount = $request->request->getInt('fs_ship_cnt_new');
    if ($shipCount <= 0) {
        $twig->addGlobal('errorMessage', "Ungültige Anzahl Schiffe.");
        return;
    }

    $fleetId = $fleetRepository->add(
        $request->request->getInt('user_id'),
        $launchtime,
        $landtime,
        $srcEnt->id,
        $trgEnt->id,
        $request->request->get('action'),
        $request->request->getInt('status'),
        new BaseResources()
    );

    $fleetRepository->addShipsToFleet(
        $fleetId,
        $shipId,
        $shipCount
    );

    $twig->addGlobal('successMessage', "Neue Flotte erstellt! <a href=\"?page=$page&amp;sub=$sub&fleetedit=" . $fleetId . "\">Details</a>");
}

function createNewFleetForm(
    UserSelector $userSelector,
    EntityCoordinatesSelector $entityCoordinatesSelector,
    ShipSelector $shipSelector
): void {
    global $page;

    echo "<form action=\"?page=$page\" method=\"post\">";
    echo "<table class=\"tbl\">";

    // Owner
    echo "<tr>
        <th class=\"tbltitle\">Besitzer:</th>
        <td class=\"tbldata\">";
    echo $userSelector->getHTML('user_id');
    echo "</td></tr>";

    // Time Data
    echo "<tr><th class=\"tbltitle\">Startzeit:</th><td class=\"tbldata\">";
    showDatepicker("launchtime", time() + 10, true, true);
    echo "</td></tr>";
    echo "<tr><th class=\"tbltitle\">Landezeit:</th><td class=\"tbldata\">";
    showDatepicker("landtime", time() + 90,  true, true);
    echo " </td></tr>";

    // Source and Target Data
    echo "<tr><td class=\"tbltitle\">Startzelle</td><td class=\"tbldata\">";
    echo $entityCoordinatesSelector->getHTML('start', null, false);
    echo "</td></tr>";
    echo "<tr><td class=\"tbltitle\">Endzelle</td><td class=\"tbldata\">";
    echo $entityCoordinatesSelector->getHTML('end', null, false);

    // Action
    echo "</td></tr>";
    echo "<tr><td class=\"tbltitle\">Aktion:</td><td class=\"tbldata\"><select name=\"action\">";
    $fas = \FleetAction::getAll();
    foreach ($fas as $fa) {
        echo "<option value=\"" . $fa->code() . "\" style=\"color:" . \FleetAction::$attitudeColor[$fa->attitude()] . "\"";
        echo ">" . $fa->name() . "</option>";
    }
    echo "</select> &nbsp; <select name=\"status\">";
    foreach (\FleetAction::$statusCode as $k => $v) {
        echo "<option value=\"" . $k . "\" ";
        echo ">" . $v . "</option>";
    }
    echo "</select></td></tr>";
    echo "<tr>
    <td class=\"tbltitle\">Schiffe:</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"fs_ship_cnt_new\" value=\"1\" size=\"5\" />";
    echo $shipSelector->getHTML('fs_ship_id_new');
    echo "</td></tr>";
    echo "</table><br/>
    <input type=\"submit\" value=\"Erstellen\" name=\"submit_new_fleet\" /> ";
    echo "</form>";
}

function sendNewFleet(
    Request $request,
    EntityRepository $entityRepository,
    PlanetRepository $planetRepo,
    FleetRepository $fleetRepository,
    EntityCoordinatesSelector $entityCoordinatesSelector,
    Environment $twig
): void {

    $srcCoords = $entityCoordinatesSelector->parse('start', $request->request);
    if ($srcCoords === null) {
        $twig->addGlobal('errorMessage', "Ungültige Startkoordinaten.");
        return;
    }

    $srcEnt = $entityRepository->findByCoordinates($srcCoords);
    if ($srcEnt === null) {
        $twig->addGlobal('errorMessage', "Startentität nicht vorhanden");
        return;
    }

    $shipId = $request->request->getInt('fs_ship_id_new');
    if ($shipId <= 0) {
        $twig->addGlobal('errorMessage', "Kein Schiff angegeben.");
        return;
    }

    $shipCount = $request->request->getInt('fs_ship_cnt_new');
    if ($shipCount <= 0) {
        $twig->addGlobal('errorMessage', "Ungültige Anzahl Schiffe.");
        return;
    }

    $launchtime = parseDatePicker('launchtime', $request->request->all());
    $landtime = parseDatePicker('landtime', $request->request->all());

    $count = 0;
    foreach ($planetRepo->getMainPlanets() as $planet) {

        $fleetId = $fleetRepository->add(
            $planet->userId,
            $launchtime,
            $landtime,
            $srcEnt->id,
            $planet->id,
            FleetAction::FLIGHT,
            FleetStatus::ARRIVAL,
            new BaseResources()
        );
        $fleetRepository->addShipsToFleet(
            $fleetId,
            $shipId,
            $shipCount
        );
        $count++;
    }
    $twig->addGlobal('successMessage', "$count Flotten erstellt!");
}

function sendNewFleetForm(
    EntityCoordinatesSelector $entityCoordinatesSelector,
    ShipSelector $shipSelector
): void {
    global $page;

    echo "<form action=\"?page=$page\" method=\"post\">";
    echo "<table class=\"tbl\">";

    // Time Data
    echo "<tr><th clas s=\"tbltitle\">Startzeit:</th><td class=\"tbldata\">";
    showDatepicker("launchtime", time() + 10, true, true);
    echo "</td></tr>";
    echo "<tr><th class=\"tbltitle\">Landezeit:</th><td class=\"tbldata\">";
    showDatepicker("landtime", time() + 90,  true, true);
    echo " </td></tr>";

    // Source and Target Data
    echo "<tr><th class=\"tbltitle\">Startzelle:</th><td class=\"tbldata\">";
    echo $entityCoordinatesSelector->getHTML('start', null, false);
    echo "</td></tr>";

    echo "<tr><th>Ziel:</th><td>Hauptplanet jedes Spielers</td></tr>";

    echo "<tr>
    <td class=\"tbltitle\">Schiffe:</td>
        <td class=\"tbldata\">
            <input type=\"text\" name=\"fs_ship_cnt_new\" value=\"1\" size=\"5\" />";
    echo $shipSelector->getHTML('fs_ship_id_new');
    echo "</td></tr>";
    echo "</table><br/>
    <input type=\"submit\" value=\"Erstellen\" name=\"submit_send_ships\" /> ";
    echo "</form>";
}

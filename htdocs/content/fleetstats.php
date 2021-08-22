<?PHP

use EtoA\Fleet\FleetRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipQueueSearch;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipSort;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetService;

/** @var PlanetService $planetService */
$planetService = $app[PlanetService::class];
/** @var FleetRepository $fleetRepository */
$fleetRepository = $app[FleetRepository::class];
/** @var ShipRepository $shipRepository */
$shipRepository = $app[ShipRepository::class];
/** @var ShipQueueRepository $shipQueueRepository */
$shipQueueRepository = $app[ShipQueueRepository::class];
/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];

echo '<h1>Schiffsübersicht</h1>';

define('HELP_URL', "?page=help&site=shipyard");

//Button "Zurück zum Raumschiffshafen"
echo '<input type="button" onclick="document.location=\'?page=fleets\'" value="Flotten anzeigen" /> &nbsp; ';
echo '<input type="button" onclick="document.location=\'?page=haven\'" value="Raumschiffshafen des aktuellen Planeten anzeigen" /><br/><br/>';

//Prüft ob Schiffe vorhanden sind
$shiplist = $shipRepository->findForUser($cu->getId());
if (count($shiplist) > 0) {
    //
    // Läd alle benötigten Daten
    //

    //Speichert Planetnamen in ein Array
    $planet_data = $planetService->getUserPlanetNames($cu->id);

    // Speichert alle Schiffe des Users, welche auf den Planeten stationiert sind
    $shiplist_data = array();
    $shiplist_bunkered = [];
    foreach ($shiplist as $item) {
        $shiplist_data[$item->shipId][$item->entityId] = $item->count;
        $shiplist_bunkered[$item->shipId][$item->entityId] = $item->bunkered;
    }


    // Speichert alle Schiffe des Users, die sich im Bau befinden
    $queue_data = array();
    $queuelist = $shipQueueRepository->searchQueueItems(ShipQueueSearch::create()->userId($cu->getId()));
    foreach ($queuelist as $item) {
        $queue_data[$item->shipId][$item->entityId] = $item->count;
    }

    // Speichert alle Schiffe des Users, die sich im All befinden
    $fleet_data = $fleetRepository->getUserFleetShipCounts($cu->getId());
    tableStart("Schiffe");
    echo '<tr>
                        <th colspan=\'2\'>Schiff</th>
                        <th width=\'100\'>Im Orbit</th>
                        <th width=\'100\'>Eingebunkert</th>
                        <th width=\'100\'>Im Bau</th>
                        <th width=\'100\'>Im All</th>
                    </tr>';

    //Listet alle Schiffe auf, die allgemein gebaut werden können (auch die, die der User nach dem Technikbaum noch nicht bauen könnte oder nicht seiner Rasse entsprechen)
    $ships = $shipDataRepository->searchShips(null, ShipSort::specialWithUserSort('name', 'ASC'));
    if (count($ships) > 0) {
        foreach ($ships as $ship) {
            //Zeigt Informationen (Zeile) an wenn Schiffe vorhanden sind
            if (
                (isset($shiplist_data[$ship->id]) && array_sum($shiplist_data[$ship->id]) > 0)
                || (isset($queue_data[$ship->id]) && array_sum($queue_data[$ship->id]) > 0)
                || (isset($fleet_data[$ship->id]) && $fleet_data[$ship->id] > 0)
                || (isset($shiplist_bunkered[$ship->id]) && array_sum($shiplist_bunkered[$ship->id]) > 0)
            ) {
                $s_img = IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $ship->id . "_small." . IMAGE_EXT;
                echo '<tr>
                                  <td style="background:#000" style="width:40px;height:40px;">';

                if ($ship->special) {
                    echo '<a href="?page=ship_upgrade&amp;id=' . $ship->id . '" title="Zum Upgrademenu"><img src="' . $s_img . '" style="width:40px;height:40px;"/></a>';
                } else {
                    echo '<a href="' . HELP_URL . '" title="Info zu diesem Schiff anzeigen"><img src="' . $s_img . '" style="width:40px;height:40px;"/></a>';
                }
                echo '</td>
                                  <td>
                                      ' . $ship->name . '
                                  </td>';
                //Spalte gebauter Schiffe
                if (isset($shiplist_data[$ship->id])) {
                    // Summiert die Anzahl Schiffe von allen Planeten
                    $total = array_sum($shiplist_data[$ship->id]);

                    // Listet die Anzahl Schiffe von jedem einzelen Planeten auf
                    $tm = "";
                    foreach ($planet_data as $planetId => $planetName) {
                        if (($shiplist_data[$ship->id][$planetId] ?? 0) > 0) {
                            $tm .= "<b>" . $planetName . "</b>: " . StringUtils::formatNumber($shiplist_data[$ship->id][$planetId]) . "<br>";
                        }
                    }

                    echo '
                                  <td ' . tm("Anzahl", $tm) . '>
                                      ' . StringUtils::formatNumber($total) . '
                                  </td>';
                } else {
                    echo '
                                  <td>
                                      &nbsp;
                                  </td>';
                }

                //Spalte eingebunkerter Schiffe
                if (isset($shiplist_bunkered[$ship->id])) {
                    // Summiert die Anzahl Schiffe von allen Planeten
                    $total = array_sum($shiplist_bunkered[$ship->id]);

                    // Listet die Anzahl Schiffe von jedem einzelen Planeten auf
                    $tm = "";
                    foreach ($planet_data as $planetId => $planetName) {
                        if (($shiplist_bunkered[$ship->id][$planetId] ?? 0) > 0) {
                            $tm .= "<b>" . $planetName . "</b>: " . StringUtils::formatNumber($shiplist_bunkered[$ship->id][$planetId]) . "<br>";
                        }
                    }

                    if ($tm != "") {
                        echo '
                                      <td ' . tm("Anzahl", $tm) . '>
                                          ' . StringUtils::formatNumber($total) . '
                                      </td>';
                    } else
                        echo '
                                    <td>
                                        &nbsp;
                                    </td>';
                } else {
                    echo '
                                  <td>
                                      &nbsp;
                                  </td>';
                }

                //Spalte bauender Schiffe
                if (isset($queue_data[$ship->id])) {
                    // Summiert die Anzahl Schiffe von allen Planeten
                    $total = array_sum($queue_data[$ship->id]);

                    // Listet die Anzahl Schiffe von jedem einzelen Planeten auf
                    $tm = "";
                    foreach ($planet_data as $planetId => $planetName) {
                        if (($queue_data[$ship->id][$planetId] ?? 0) > 0) {
                            $tm .= "<b>" . $planetName . "</b>: " . StringUtils::formatNumber($queue_data[$ship->id][$planetId]) . "<br>";
                        }
                    }

                    echo '
                                  <td ' . tm("Anzahl", $tm) . '>
                                      ' . StringUtils::formatNumber($total) . '
                                  </td>';
                } else {
                    echo '
                                  <td>
                                      &nbsp;
                                  </td>';
                }


                //Spalte fliegender Schiffe
                if (isset($fleet_data[$ship->id])) {
                    // Summiert die Anzahl Schiffe von allen Planeten
                    $total = $fleet_data[$ship->id];
                    echo '
                                  <td>
                                      ' . StringUtils::formatNumber($total) . '
                                  </td>';
                } else {
                    echo '
                                  <td>
                                      &nbsp;
                                  </td>';
                }
                echo '</tr>';
            }
        }
    }

    tableEnd();

    unset($planet_data);
    unset($shiplist_data);
    unset($queue_data);
    unset($fleet_data);
} else {
    error_msg("Es sind noch keine Schiffe vorhanden!");
}

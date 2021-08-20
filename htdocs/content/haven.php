<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipTransformRepository;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var ResourceBoxDrawer $resourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];

/** @var ShipRepository $shipRepository */
$shipRepository = $app[ShipRepository::class];
/** @var DefenseRepository $defenseRepository */
$defenseRepository = $app[DefenseRepository::class];
/** @var ShipTransformRepository $shipTransformRepository */
$shipTransformRepository = $app[ShipTransformRepository::class];
/** @var DefenseDataRepository $defenseDataRepository */
$defenseDataRepository = $app[DefenseDataRepository::class];
/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];

if ($cp) {
    $planet = $planetRepo->find($cp->id);

    echo '<h1>Raumschiffhafen des Planeten ' . $planet->name . '</h1>';
    echo $resourceBoxDrawer->getHTML($planet);

    if (!$cu->isVerified) {
        iBoxStart("Funktion gesperrt");
        echo "Solange deine E-Mail Adresse nicht bestätigt ist, kannst du keine Flotten versenden!";
        iBoxEnd();
    } else {
        $hasMobileObjects = $shipTransformRepository->hasUserTransformableObjects($cu->getId(), $planet->id);

        $mode = isset($_GET['mode']) && ($_GET['mode'] != "") && ctype_alpha($_GET['mode']) ? $_GET['mode'] : 'launch';
        if ($hasMobileObjects) {
            show_tab_menu("mode", array(
                "launch" => "Flotten versenden",
                "transship" => "Mobile Anlagen umladen",
            ));
            echo "<br/>";
        }

        //
        // Launch fleet
        //
        if ($mode == "launch") {

            //
            // Kampfsperre prüfen
            //
            if ($config->getBoolean("battleban") && $config->param1Int("battleban_time") <= time() && $config->param2Int("battleban_time") > time()) {
                iBoxStart("Kampfsperre");
                echo 'Es ist momentan nicht m&ouml;glich andere Spieler anzugreifen. Grund: ' . text2html($config->param1("battleban")) . '<br />Die Sperre dauert vom ' . date("d.m.Y", $config->param1Int("battleban_time")) . ' um ' . date("H:i", $config->param1Int("battleban_time")) . ' Uhr bis am ' . date("d.m.Y", $config->param2Int("battleban_time")) . " um " . date("H:i", $config->param2Int("battleban_time")) . ' Uhr!';
                iBoxEnd();
            }

            if (isset($_GET['target']) && intval($_GET['target']) > 0) {
                $_SESSION['haven']['targetId'] = intval($_GET['target']);
            } elseif (isset($_GET['cellTarget']) && intval($_GET['cellTarget']) > 0) {
                $_SESSION['haven']['cellTargetId'] = intval($_GET['cellTarget']);
            }

            // Fleet object
            $fleet = new FleetLaunch($cp, $cu);

            $fleet->checkHaven();

            // Set vars for xajax
            $_SESSION['haven'] = Null;
            $_SESSION['haven']['fleetObj'] = serialize($fleet);

            echo '<div id="havenContent">
            <div id="havenContentShips" style="">
            <div style="padding:20px"><img src="images/loading.gif" alt="Loading" /> Lade Daten...</div>
            </div>
            <div id="havenContentTarget" style="display:none;"></div>
            <div id="havenContentWormhole" style="display:none;"></div>
            <div id="havenContentAction" style="display:none;"></div>
            </div>';
            echo '<script type="text/javascript">xajax_havenShowShips();</script>';
        }

        //
        // Mobile defenses
        //
        else if ($mode == "transship") {
            if ($hasMobileObjects) {
                if (isset($_POST['dtransform_submit'])) {
                    $transformed_counter = 0;
                    if (isset($_POST['dtransform']) && count($_POST['dtransform']) > 0) {
                        foreach ($_POST['dtransform'] as $def_id => $v) {
                            $mobileDefense = $shipTransformRepository->getDefense($cu->getId(), $planet->id, $def_id);
                            if ($mobileDefense !== null) {
                                $packcount = (int) min(max(0, $v), $mobileDefense->availableDefense);

                                if ($packcount > 0) {
                                    $shipRepository->addShip(
                                        $mobileDefense->shipId,
                                        $defenseRepository->removeDefense($mobileDefense->defenseId, $packcount, $cu->getId(), $planet->id),
                                        $cu->getId(),
                                        $planet->id
                                    );
                                    $transformed_counter += $packcount;
                                }
                            }
                        }
                    }

                    if ($transformed_counter > 0) {
                        success_msg("$transformed_counter Verteidigungsanlagen wurden verladen!");
                    }
                }

                if (isset($_POST['stransform_submit'])) {
                    $transformed_counter = 0;
                    if (isset($_POST['stransform']) && count($_POST['stransform']) > 0) {
                        foreach ($_POST['stransform'] as $ship_id => $v) {
                            $ship_id = intval($ship_id);
                            $mobileDefense = $shipTransformRepository->getShip($cu->getId(), $planet->id, $ship_id);
                            if ($mobileDefense !== null) {
                                $packcount = (int) min(max(0, $v), $mobileDefense->availableShips);
                                if ($packcount > 0) {
                                    $defenseRepository->addDefense(
                                        $mobileDefense->defenseId,
                                        $shipRepository->removeShips($mobileDefense->shipId, $packcount, $cu->getId(), $planet->id),
                                        $cu->getId(),
                                        $planet->id
                                    );
                                    $transformed_counter += $packcount;
                                }
                            }
                        }
                    }

                    if ($transformed_counter > 0) {
                        success_msg("$transformed_counter Verteidigungsanlagen wurden installiert!");
                    }
                }

                $mobileDefenses = $shipTransformRepository->getDefenses($cu->getId(), $planet->id);
                if (count($mobileDefenses) > 0) {
                    $defenseNames = $defenseDataRepository->getDefenseNames(true);
                    echo "<form action=\"?page=$page&mode=$mode\" method=\"post\">";
                    tableStart("Verteidigungsanlagen auf Träger verladen");
                    echo "<tr><th>Typ</th><th>Anzahl</th></tr>";
                    foreach ($mobileDefenses as $mobileDefense) {
                        echo "<tr><td>" . $defenseNames[$mobileDefense->defenseId] . "</td>
                            <td><input type=\"text\" name=\"dtransform[" . $mobileDefense->defenseId . "]\" value=\"" . $mobileDefense->availableDefense . "\" size=\"7\" /></td></tr>";
                    }

                    tableEnd();
                    echo "<input type=\"submit\" name=\"dtransform_submit\" value=\"Verladen\" /></form><br/>";
                }

                $mobileDefenses = $shipTransformRepository->getShips($cu->getId(), $planet->id);
                if (count($mobileDefenses) > 0) {
                    $shipNames = $shipDataRepository->getShipNames(true);
                    echo "<form action=\"?page=$page&mode=$mode\" method=\"post\">";
                    tableStart("Mobile Verteidigung installieren");
                    echo "<tr><th>Typ</th><th>Anzahl</th></tr>";
                    foreach ($mobileDefenses as $mobileDefense) {
                        echo "<tr><td>" . $shipNames[$mobileDefense->shipId] . "</td>
                            <td><input type=\"text\" name=\"stransform[" . $mobileDefense->shipId . "]\" value=\"" . $mobileDefense->availableShips . "\" size=\"7\" /></td></tr>";
                    }

                    tableEnd();
                    echo "<input type=\"submit\" name=\"stransform_submit\" value=\"Ausladen und installieren\" /></form><br/>";
                }
            } else {
                info_msg("Keine mobilen Anlagen vorhanden!");
            }
        }
    }
}

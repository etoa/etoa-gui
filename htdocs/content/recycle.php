<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Defense\DefenseSearch;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyRepository;
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
/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];
/** @var DefenseRepository $defenseRepository */
$defenseRepository = $app[DefenseRepository::class];
/** @var DefenseDataRepository $defenseDataRepository */
$defenseDataRepository = $app[DefenseDataRepository::class];
/** @var LogRepository $logRepository */
$logRepository = $app[LogRepository::class];

define('HELP_URL_DEF', "?page=help&site=defense");
define('HELP_URL_SHIP', "?page=help&site=shipyard");

// Maxmimale Recyclingtech effizient
define("RECYC_MAX_PAYBACK", $config->getFloat('recyc_max_payback'));

$planet = $planetRepo->find($cp->id);

echo "<h1>Recyclingstation des Planeten " . $planet->name . "</h1>";
echo $resourceBoxDrawer->getHTML($planet);

//Recycling Level laden
/** @var TechnologyRepository $technologyRepository */
$technologyRepository = $app[TechnologyRepository::class];
$tech_level = $technologyRepository->getTechnologyLevel($cu->getId(), RECYC_TECH_ID);

if ($tech_level > 0) {
    $payback_max = RECYC_MAX_PAYBACK;
    $payback = ($payback_max) - ($payback_max / $tech_level);
    $pb_percent = round($payback * 100, 2);
    $pb = [];
    $pb[0] = 0;
    $pb[1] = 0;
    $pb[2] = 0;
    $pb[3] = 0;
    $pb[4] = 0;
    $cnt = 0;
    $log_ships = "";
    $log_def = "";

    tableStart("Recycling");
    echo "<tr><td>Deine Recyclingtechnologie ist auf Stufe " . $tech_level . " entwickelt. Es werden " . $pb_percent . " % der Kosten zur&uuml;ckerstattet.<br/>Der Recyclingvorgang kann nicht r&uuml;ckg&auml;ngig gemacht werden, die Objekte werden sofort verschrottet!</td></tr>";
    tableEnd();

    //Schiffe recyceln
    if (isset($_POST['submit_recycle_ships']) && $_POST['submit_recycle_ships'] != "") {
        $recycled = [];
        //Anzahl muss grösser als 0 sein
        if (count($_POST['ship_count']) > 0) {
            $ships = $shipDataRepository->searchShips(ShipSearch::create()->buildable()->special(false));
            $shipCounts = $shipRepository->getEntityShipCounts($cu->getId(), $planet->id);
            foreach ($_POST['ship_count'] as $id => $num) {
                $id = intval($id);

                $num = abs($num);
                if ($num > 0) {
                    if (isset($ships[$id], $shipCounts[$id])) {
                        $ship = $ships[$id];
                        //Anzahl anpassen, wenn angegebene Anzahl grösser ist, als die effektive Anzahl auf dem Planeten
                        if ($num > $shipCounts[$ship->id]) {
                            $num = $shipCounts[$ship->id];
                        }

                        //Schiffe vom Planeten abziehen
                        $shipRepository->removeShips($ship->id, $num, $cu->getId(), $planet->id);

                        //Rohstoffe summieren
                        $pb[0] += ceil($payback * $ship->costsMetal * $num);
                        $pb[1] += ceil($payback * $ship->costsCrystal * $num);
                        $pb[2] += ceil($payback * $ship->costsPlastic * $num);
                        $pb[3] += ceil($payback * $ship->costsFuel * $num);
                        $pb[4] += ceil($payback * $ship->costsFood * $num);
                        $cnt += $num;

                        $log_ships .= "[B]" . $ship->name . ":[/B] " . $num . "\n";
                        $recycled[$id] = $num;
                    }
                }
            }

            //Rohstoffe Updaten
            $planetRepo->addResources($planet->id, $pb[0], $pb[1], $pb[2], $pb[3], $pb[4]);


            //Rohstoffe auf dem Planeten aktualisieren
            $planet->resMetal += $pb[0];
            $planet->resCrystal += $pb[1];
            $planet->resPlastic += $pb[2];
            $planet->resFuel += $pb[3];
            $planet->resFood += $pb[4];


            //Log schreiben
            $log = "Der User [page user sub=edit user_id=" . $cu->id . "] [B]" . $cu . "[/B] [/page] hat auf dem Planeten [page galaxy sub=edit id=" . $planet->id . "][B]" . $planet->name . "[/B][/page] folgende Schiffe mit dem r&uuml;ckgabewert von " . ($payback * 100) . "% recycelt:\n\n" . $log_ships . "\nDies hat ihm folgende Rohstoffe gegeben:\n" . RES_METAL . ": " . StringUtils::formatNumber($pb[0]) . "\n" . RES_CRYSTAL . ": " . StringUtils::formatNumber($pb[1]) . "\n" . RES_PLASTIC . ": " . StringUtils::formatNumber($pb[2]) . "\n" . RES_FUEL . ": " . StringUtils::formatNumber($pb[3]) . "\n" . RES_FOOD . ": " . StringUtils::formatNumber($pb[4]) . "\n";

            $logRepository->add(LogFacility::RECYCLING, LogSeverity::INFO, $log);
        }
        success_msg(StringUtils::formatNumber($cnt) . " Schiffe erfolgreich recycelt!");
        foreach ($recycled as $id => $num) {
            $app['dispatcher']->dispatch(new \EtoA\Ship\Event\ShipRecycle($id, $num), \EtoA\Ship\Event\ShipRecycle::RECYCLE_SUCCESS);
        }
    }


    //Verteidigungsanlagen recyceln
    if (isset($_POST['submit_recycle_def']) && $_POST['submit_recycle_def'] != "") {
        $recycled = [];
        //Anzahl muss grösser als 0 sein
        if (count($_POST['def_count']) > 0) {
            $fields = 0;
            $defenseCounts = $defenseRepository->getEntityDefenseCounts($cu->getId(), $planet->id);
            $defenses = $defenseDataRepository->searchDefense(DefenseSearch::create()->buildable());
            foreach ($_POST['def_count'] as $id => $num) {
                $num = abs($num);
                if ($num > 0) {
                    if (isset($defenses[$id], $defenseCounts[$id])) {
                        $defense = $defenses[$id];

                        //Anzahl anpassen, wenn angegebene Anzahl grösser ist, als die effektive Anzahl auf dem Planeten
                        if ($num > $defenseCounts[$defense->id]) {
                            $num = $defenseCounts[$defense->id];
                        }

                        //Defese vom Planeten Abziehen
                        $defenseRepository->removeDefense($defense->id, $num, $cu->getId(), $planet->id);

                        //Rohstoffe summieren
                        $pb[0] += ceil($payback * $defense->costsMetal * $num);
                        $pb[1] += ceil($payback * $defense->costsCrystal * $num);
                        $pb[2] += ceil($payback * $defense->costsPlastic * $num);
                        $pb[3] += ceil($payback * $defense->costsFuel * $num);
                        $pb[4] += ceil($payback * $defense->costsFood * $num);
                        $fields += $defense->fields * $num;
                        $cnt += $num;

                        $log_def .= "[B]" . $defense->name . ":[/B] " . $num . "\n";
                        $recycled[$id] = $num;
                    }
                }
            }

            //Rohstoffe und Felder updaten
            $planetRepo->addResources($planet->id, $pb[0], $pb[1], $pb[2], $pb[3], $pb[4], 0, -$fields);

            //Rohstoffe auf dem Planeten aktualisieren
            $planet->resMetal += $pb[0];
            $planet->resCrystal += $pb[1];
            $planet->resPlastic += $pb[2];
            $planet->resFuel += $pb[3];
            $planet->resFood += $pb[4];

            //Log schreiben
            $log = "Der User [page user sub=edit user_id=" . $cu->id . "] [B]" . $cu . "[/B] [/page] hat auf dem Planeten [page galaxy sub=edit id=" . $planet->id . "][B]" . $planet->name . "[/B][/page] folgende Verteidigungsanlagen mit dem r&uuml;ckgabewert von " . ($payback * 100) . "% recycelt:\n\n" . $log_def . "\nDies hat ihm folgende Rohstoffe gegeben:\n" . RES_METAL . ": " . StringUtils::formatNumber($pb[0]) . "\n" . RES_CRYSTAL . ": " . StringUtils::formatNumber($pb[1]) . "\n" . RES_PLASTIC . ": " . StringUtils::formatNumber($pb[2]) . "\n" . RES_FUEL . ": " . StringUtils::formatNumber($pb[3]) . "\n" . RES_FOOD . ": " . StringUtils::formatNumber($pb[4]) . "\n";

            $logRepository->add(LogFacility::RECYCLING, LogSeverity::INFO, $log);
        }
        success_msg("" . StringUtils::formatNumber($cnt) . " Verteidigungsanlagen erfolgreich recycelt!");
        foreach ($recycled as $id => $num) {
            $app['dispatcher']->dispatch(new \EtoA\Defense\Event\DefenseRecycle($id, $num), \EtoA\Defense\Event\DefenseRecycle::RECYCLE_SUCCESS);
        }
    }


    //
    //Schiffe
    //
    $shipNames = $shipDataRepository->searchShipNames(ShipSearch::create()->buildable()->special(false));
    $shipCounts = $shipRepository->getEntityShipCounts($cu->getId(), $planet->id);
    if (count($shipCounts) > 0) {
        echo "<form action=\"?page=$page\" method=\"POST\">";
        tableStart("Schiffe");
        echo "<tr>
                        <th width=\"390\" colspan=\"2\" valign=\"top\">Typ</th>
                        <th valign=\"top\" width=\"110\">Anzahl</th>
                        <th valign=\"top\" width=\"110\">Auswahl</th>
                    </tr>\n";

        $tabulator = 1;
        foreach ($shipNames as $shipId => $shipName) {
            if (!isset($shipCounts[$shipId])) {
                continue;
            }

            $s_img = IMAGE_PATH . "/" . IMAGE_SHIP_DIR . "/ship" . $shipId . "_small." . IMAGE_EXT;
            echo "<tr>
                            <td width=\"40\">
                                <a href=\"" . HELP_URL_SHIP . "&amp;id=" . $shipId . "\"><img src=\"$s_img\" width=\"40\"  height=\"40\" border=\"0\"/></a>
                            </td>";
            echo "<td width=\"66%\" valign=\"middle\">" . $shipName . "</td>";
            echo "<td width=\"22%\" valign=\"middle\">" . StringUtils::formatNumber($shipCounts[$shipId]) . "</td>";
            echo "<td width=\"12%\" valign=\"middle\"><input type=\"text\" name=\"ship_count[" . $shipId . "]\" size=\"8\" maxlength=\"" . strlen((string) $shipCounts[$shipId]) . "\" value=\"0\" title=\"Anzahl welche recyclet werden sollen\" tabindex=\"" . $tabulator . "\" onKeyPress=\"return nurZahlen(event)\">
                            </td>
                    </tr>\n";
        }

        tableEnd();
        echo "<input type=\"submit\" class=\"button\" name=\"submit_recycle_ships\" value=\"Ausgew&auml;hlte Schiffe recyceln\"><br/></form>";
    } else {
        info_msg("Es sind keine Schiffe auf diesem Planeten vorhanden!");
    }


    //
    //Verteidigung
    //
    $defenseNames = $defenseDataRepository->searchDefenseNames(DefenseSearch::create()->buildable());
    $defenseCounts = $defenseRepository->getEntityDefenseCounts($cu->getId(), $planet->id);
    if (count($defenseCounts) > 0) {
        echo "<form action=\"?page=$page\" method=\"POST\">";
        tableStart("Verteidigungsanlagen");
        echo "<tr>
                        <th colspan=\"2\">Typ</th>
                        <th valign=\"top\" width=\"110\">Anzahl</th>
                        <th valign=\"top\" width=\"110\">Auswahl</th>
                    </tr>\n";
        $tabulator = 1;
        foreach ($defenseNames as $defenseId => $defenseName) {
            if (!isset($defenseCounts[$defenseId])) {
                continue;
            }

            $s_img = IMAGE_PATH . "/" . IMAGE_DEF_DIR . "/def" . $defenseId . "_small." . IMAGE_EXT; //image angepasst by Lamborghini
            echo "<tr>
                            <td width=\"40\">
                                <a href=\"" . HELP_URL_DEF . "&amp;id=" . $defenseId . "\"><img src=\"$s_img\" width=\"40\"  height=\"40\" border=\"0\"/></a>
                            </td>";
            echo "<td width=\"66%\" valign=\"middle\">" . $defenseName . "</td>";
            echo "<td width=\"22%\" valign=\"middle\">" . StringUtils::formatNumber($defenseCounts[$defenseId]) . "</td>";
            echo "<td width=\"12%\" valign=\"middle\"><input type=\"text\" name=\"def_count[" . $defenseId . "]\" size=\"8\" maxlength=\"" . strlen((string) $defenseCounts[$defenseId]) . "\" value=\"0\" tabindex=\"" . $tabulator . "\" onKeyPress=\"return nurZahlen(event)\"></td>
                    </tr>\n";
            $tabulator++;
        }
        tableEnd();
        echo "<input type=\"submit\" class=\"button\" name=\"submit_recycle_def\" value=\"Ausgew&auml;hlte Anlagen recyceln\"></form>";
    } else
        info_msg("Es sind keine Verteidigungsanlagen auf diesem Planeten vorhanden!");
} else {
    info_msg("Es können keine Schiffe oder Verteidigungsanlagen recycelt werden, da die Recyclingtechnologie noch nicht erforscht wurde!");
}

<?PHP

// Main dialogs

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Bookmark\BookmarkRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\FleetRepository;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Ship\ShipSort;
use EtoA\Support\StringUtils;
use EtoA\Specialist\SpecialistService;
use EtoA\Support\BBCodeUtils;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResIcons;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use EtoA\User\UserUniverseDiscoveryService;

$xajax->register(XAJAX_FUNCTION, "havenShowShips");
$xajax->register(XAJAX_FUNCTION, "havenShowTarget");
$xajax->register(XAJAX_FUNCTION, "havenShowWormhole");
$xajax->register(XAJAX_FUNCTION, "havenShowAction");
$xajax->register(XAJAX_FUNCTION, "havenShowLaunch");

// Helpers
$xajax->register(XAJAX_FUNCTION, "havenReset");
$xajax->register(XAJAX_FUNCTION, "havenTargetInfo");
$xajax->register(XAJAX_FUNCTION, "havenBookmark");
$xajax->register(XAJAX_FUNCTION, "havenCheckRes");
$xajax->register(XAJAX_FUNCTION, "havenSetResAll");
$xajax->register(XAJAX_FUNCTION, "havenSetFetchAll");
$xajax->register(XAJAX_FUNCTION, "havenCheckPeople");
$xajax->register(XAJAX_FUNCTION, "havenCheckAction");
$xajax->register(XAJAX_FUNCTION, "havenAllianceAttack");
$xajax->register(XAJAX_FUNCTION, "havenCheckSupport");
$xajax->register(XAJAX_FUNCTION, "havenWormholeReset");

/**
 * Show a list of all ships on the planet
 */
function havenShowShips()
{
    global $app;

    /** @var ShipRepository $shipRepository */
    $shipRepository = $app[ShipRepository::class];

    /** @var ShipDataRepository $shipDataRepository */
    $shipDataRepository = $app[ShipDataRepository::class];

    /** @var ShipRequirementRepository $shipRequirementRepository */
    $shipRequirementRepository = $app[ShipRequirementRepository::class];

    /** @var TechnologyRepository $technologyRepository */
    $technologyRepository = $app[TechnologyRepository::class];

    /** @var AllianceBuildingRepository $allianceBuildingRepository */
    $allianceBuildingRepository = $app[AllianceBuildingRepository::class];

    /** @var SpecialistService $specialistService */
    $specialistService = $app[SpecialistService::class];

    $response = new xajaxResponse();
    ob_start();

    /** @var FleetLaunch $fleet */
    $fleet = unserialize($_SESSION['haven']['fleetObj']);

    $specialist = $specialistService->getSpecialistOfUser($fleet->ownerId());

    //Schiffsinfo
    echo "<div id=\"ship_info\"></div>";

    // Infobox
    tableStart("Hafen-Infos");

    // Flotten unterwegs
    echo "<tr><th>Aktive Flotten:</th><td>";
    if ($fleet->fleetSlotsUsed() > 1)
        echo "<b>" . $fleet->fleetSlotsUsed() . "</b> Flotten dieses Planeten sind <a href=\"?page=fleets\">unterwegs</a>.";
    elseif ($fleet->fleetSlotsUsed() == 1)
        echo "<b>Eine</b> Flotte dieses Planeten ist <a href=\"?page=fleets\">unterwegs</a>.";
    else
        echo "Es sind <b>keine</b> Flotten dieses Planeten unterwegs.";
    echo "</td></tr>";

    // Flotten startbar?
    echo "<tr><th>Flottenstart:</th><td>";
    if ($fleet->possibleFleetStarts() > 1)
        echo "<b>" . $fleet->possibleFleetStarts() . "</b> Flotten k&ouml;nnen von diesem Planeten starten!";
    elseif ($fleet->possibleFleetStarts() == 1)
        echo "<b>Eine</b> Flotte kann von diesem Planeten starten!";
    else
        echo "Es k&ouml;nnen <b>keine</b> Flotten von diesem Planeten starten!";
    echo " (Flottenkontrolle Stufe " . $fleet->fleetControlLevel();
    if ($specialist !== null && $specialist->fleetMax > 0)
        echo " +" . $specialist->fleetMax . " Flotten durch " . $specialist->name;
    echo ")</td></tr>";
    if ($fleet->owner->allianceId() > 0 && $allianceBuildingRepository->getLevel($fleet->owner->allianceId(), AllianceBuildingId::MAIN) > 0) {
        $flvl = $allianceBuildingRepository->getLevel($fleet->owner->allianceId(), AllianceBuildingId::FLEET_CONTROL);
        $fleet->setAllianceSlots($flvl);
        $afleets = $fleet->getAllianceSlots();
        $pfleets = $flvl + 2;
        echo "<th>Allianzflotten:</th><td><b>$afleets</b> Allianzflotten können mit <b>$pfleets</b> Teilflotten pro Flotte starten! (Allianzflottenkontrolle Stufe $flvl)</td>";
    }

    // Piloten
    echo "<tr><th>Piloten:</th><td>";
    if ($fleet->pilotsAvailable() > 1)
        echo "<b>" . StringUtils::formatNumber($fleet->pilotsAvailable()) . "</b> Piloten k&ouml;nnen eingesetzt werden.";
    elseif ($fleet->pilotsAvailable() == 1)
        echo "<b>Ein</b> Pilot kann eingesetzt werden.";
    else
        echo "Es sind <b>keine</b> Piloten verf&uuml;gbar.";
    echo "</td></tr>";

    // Rasse
    if ($fleet->raceSpeedFactor() != 1) {
        echo "<tr><th>Rassenbonus:</th><td>";
        echo "Die Schiffe fliegen aufgrund deiner Rasse <b>" . $fleet->ownerRaceName . "</b> mit " . StringUtils::formatPercentString($fleet->raceSpeedFactor, true) . " Geschwindigkeit!";
        echo "</td></tr>";
    }

    // Specialist
    if ($specialist !== null && $specialist->fleetSpeed != 1) {
        echo "<tr><th>Spezialistenbonus:</th><td>";
        echo "Die Schiffe fliegen aufgrund des <b>" . $specialist->name . "</b> mit " . StringUtils::formatPercentString($specialist->fleetSpeed, true) . " Geschwindigkeit!";
        echo "</td></tr>";
    }
    tableEnd();

    // Schiffe auflisten
    $shipList = $shipRepository->getEntityShipCounts($fleet->ownerId(), $fleet->sourceEntity->id());
    $ships = array_intersect_key($shipDataRepository->searchShips(null, ShipSort::haven()), $shipList);
    if (count($ships) != 0) {
        $fleetShips = $fleet->getShips();
        $technologyLevels = $technologyRepository->getTechnologyLevels($fleet->ownerId());

        $tabulator = 1;
        echo "<form id=\"shipForm\" onsubmit=\"xajax_havenShowTarget(xajax.getFormValues('shipForm')); return false;\">";
        tableStart("Vorhandene Raumschiffe");
        echo "<tr>
                <th colspan=\"6\">Schiffe wählen</th>
            </tr>";
        echo "<tr>
                <th colspan=\"2\">Typ</th>
                <th width=\"110\">Speed</th>
                <th width=\"110\">Piloten</th>
                <th width=\"110\">Anzahl</th>
                <th width=\"110\">Auswahl</th>
            </tr>\n";

        $jsAllShips = array();    // Array for selectable ships
        $launchable = 0;    // Counter for launchable ships
        foreach ($ships as $ship) {
            $count = $shipList[$ship->id];
            if (isset($fleetShips[$ship->id])) {
                $val = max(0, $fleetShips[$ship->id]['count']);
            } else {
                $val = 0;
            }

            if ($ship->special) {
                echo "<tr>
                    <td style=\"width:40px;background:#000;\">
                        <a href=\"?page=ship_upgrade&amp;id=" . $ship->id . "\">
                            <img src=\"" . $ship->getImagePath('small') . "\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
                        </a>
                    </td>";
            } else {
                echo "<tr>
                    <td style=\"width:40px;background:#000;\">
                        <a href=\"?page=help&amp;site=shipyard&amp;id=" . $ship->id . "\">
                            <img src=\"" . $ship->getImagePath('small') . "\" align=\"top\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/>
                        </a>
                    </td>";
            }

            // TODO: Rewrite this!
            //Geschwindigkeitsbohni der entsprechenden Antriebstechnologien laden und zusammenrechnen
            if ($fleet->raceSpeedFactor() != 1)
                $speedtechstring = "Rasse: " . StringUtils::formatPercentString($fleet->raceSpeedFactor(), true) . "<br>";
            else
                $speedtechstring = "";

            if ($specialist !== null && $specialist->fleetSpeed != 1)
                $speedtechstring .= "Spezialist: " . StringUtils::formatPercentString($specialist->fleetSpeed, true) . "<br>";
            else
                $speedtechstring .= "";

            $timefactor = $fleet->raceSpeedFactor() + ($specialist !== null ? $specialist->fleetSpeed : 1) - 1;

            $speedRequirements = $shipRequirementRepository->getRequiredSpeedTechnologies($ship->id);
            if (count($speedRequirements) > 0) {
                foreach ($speedRequirements as $requirement) {
                    $currentLevel = $technologyLevels[$requirement->id] ?? 0;
                    if ($currentLevel - $requirement->requiredLevel <= 0) {
                        $timefactor += 0;
                    } else {
                        $timefactor += ($currentLevel - $requirement->requiredLevel) * 0.1;
                        $speedtechstring .= $requirement->name . " " . $currentLevel . ": " . StringUtils::formatPercentString((($currentLevel - $requirement->requiredLevel) / 10) + 1, true) . "<br>";
                    }
                }
            }

            $ship->speed /= FLEET_FACTOR_F;


            $actions = array_filter(explode(",", $ship->actions));
            $accnt = count($actions);
            $acstr = '';
            if ($accnt > 0) {
                $acstr = "<br/><b>Fähigkeiten:</b> ";
                $x = 0;
                foreach ($actions as $i) {
                    if ($ac = FleetAction::createFactory($i)) {
                        $acstr .= $ac;
                        if ($x < $accnt - 1)
                            $acstr .= ", ";
                    }
                    $x++;
                }
                $acstr .= "";
            }


            echo "<td " . tm($ship->name, "<img src=\"" . $ship->getImagePath('medium') . "\" style=\"float:left;margin-right:5px;\">" . BBCodeUtils::toHTML($ship->shortComment) . "<br/>" . $acstr . "<br style=\"clear:both;\"/>") . ">" . $ship->name . "</td>";
            echo "<td width=\"190\" " . tm("Geschwindigkeit", "Grundgeschwindigkeit: " . $ship->speed . " AE/h<br>$speedtechstring") . ">" . StringUtils::formatNumber($ship->speed * $timefactor) . " AE/h</td>";
            echo "<td width=\"110\">" . StringUtils::formatNumber($ship->pilots) . "</td>";
            echo "<td width=\"110\">" . StringUtils::formatNumber($count) . "<br/>";

            echo "</td>";
            echo "<td width=\"110\">";
            if ($ship->launchable && $fleet->pilotsAvailable() >= $ship->pilots) {
                echo "<input type=\"text\"
                  id=\"ship_count_" . $ship->id . "\"
                  name=\"ship_count[" . $ship->id . "]\"
                  size=\"10\" value=\"$val\"
                  title=\"Anzahl Schiffe eingeben, die mitfliegen sollen\"
                  onclick=\"this.select();\" tabindex=\"" . $tabulator . "\"
                  onkeyup=\"FormatNumber(this.id,this.value," . $count . ",'','');\"/>
              <br/>
              <a href=\"javascript:;\" onclick=\"document.getElementById('ship_count_" . $ship->id . "').value=" . $count . ";document.getElementById('ship_count_" . $ship->id . "').select()\">Alle</a> &nbsp;
              <a href=\"javascript:;\" onclick=\"document.getElementById('ship_count_" . $ship->id . "').value=0;document.getElementById('ship_count_" . $ship->id . "').select()\">Keine</a>";
                $jsAllShips["ship_count_" . $ship->id] = $count;
                $launchable++;
            } else {
                echo "-";
            }
            echo "</td></tr>\n";
            $tabulator++;
        }
        echo "<tr><td colspan=\"5\"></td>
            <td>";

        // Select all ships button
        echo "<a href=\"javascript:;\" onclick=\"";
        foreach ($jsAllShips as $k => $v) {
            echo "document.getElementById('" . $k . "').value=" . $v . ";";
        }
        echo "\">Alle wählen</a>";
        echo "</td></tr>";
        tableEnd();

        // Show buttons if possible
        if ($fleet->error() == '') {
            if ($launchable > 0) {
                echo "<input type=\"submit\" value=\"Weiter zur Zielauswahl &gt;&gt;&gt;\" title=\"Wenn du die Schiffe ausgew&auml;hlt hast, klicke hier um das Ziel auszuw&auml;hlen\" tabindex=\"" . ($tabulator + 1) . "\" />";
                if (count($fleetShips) > 0) {
                    echo " &nbsp; <input type=\"button\" onclick=\"xajax_havenReset()\" value=\"Reset\" />";
                }
            }
        } else {
            echo error_msg($fleet->error());
        }
        echo "</form>";
    } else {
        error_msg("Es sind keine Schiffe auf diesem Planeten vorhanden!", 1);
    }


    $response->assign("havenContentShips", "innerHTML", ob_get_contents());

    $response->assign("havenContentTarget", "innerHTML", "");
    $response->assign("havenContentTarget", "style.display", 'none');

    $response->assign("havenContentAction", "innerHTML", "");
    $response->assign("havenContentAction", "style.display", 'none');

    $response->script("document.forms[0].elements[0].select();");
    ob_end_clean();

    $_SESSION['haven']['fleetObj'] = serialize($fleet);


    return $response;
}

/**
 * Verify ships and show target selector
 */
function havenShowTarget($form)
{
    global $app;

    /** @var PlanetRepository $planetRepository */
    $planetRepository = $app[PlanetRepository::class];
    /** @var BookmarkRepository $bookmarkRepository */
    $bookmarkRepository = $app[BookmarkRepository::class];

    $response = new xajaxResponse();

    // Get fleet object
    /** @var FleetLaunch $fleet */
    $fleet = unserialize($_SESSION['haven']['fleetObj']);
    ob_start();

    // Do some checks
    if (($form && count($form) > 0) || $fleet->getShipCount() > 0) {

        // Add ships
        if (isset($form['ship_count'])) {
            $fleet->resetShips();
            foreach ($form['ship_count'] as $sid => $cnt) {
                if (intval($cnt) > 0) {
                    $fleet->addShip($sid, $cnt);
                }
            }
        }

        // Check if there are enough people
        if ($fleet->fixShips()) {
            //
            // Show ships in fleet
            //
            //ob_start();

            tableStart("Schiffe");
            echo "<tr>
                        <th>Anzahl</th>
                        <th>Typ</th>
                        <th>Piloten</th>
                        <th>Speed</th>
                        <th>Kosten / 100 AE</th>
                    </tr>\n";
            $shipCount = 0;
            foreach ($fleet->getShips() as $sid => $sd) {
                echo "<tr>
                        <td>" . StringUtils::formatNumber($sd['count']) . "</td>
                        <td>" . $sd['name'] . "</td>
                        <td>" . StringUtils::formatNumber($sd['pilots']) . "</td>
                        <td>" . round($fleet->getSpeed() / $sd['speed'] * 100 / $fleet->sBonusSpeed) . "%</td>
                        <td>" . StringUtils::formatNumber($sd['costs_per_ae']) . " " . ResourceNames::FUEL . "</td></tr>";
                $shipCount++;
            }
            if ($shipCount > 1) {
                echo "<tr><td colspan=\"5\">Schnellere Schiffe nehmen im Flottenverband automatisch die Geschwindigkeit des langsamsten Schiffes an, sie brauchen daf&uuml;r aber auch entsprechend weniger Treibstoff!</td></tr>";
            }


            echo "<tr><td colspan=\"5\">Mögliche Aktionen: ";
            $cnt = 0;
            $shipAcCnt = count($fleet->shipActions);
            foreach ($fleet->shipActions as $ac) {
                $action = FleetAction::createFactory($ac);
                echo $action;
                if ($cnt < $shipAcCnt - 1)
                    echo ", ";
                $cnt++;
            }
            echo "</td></tr>";

            tableEnd();
            $response->assign("havenContentShips", "innerHTML", ob_get_contents());
            ob_end_clean();

            //
            // Show Target form
            //
            ob_start();
            echo "<form id=\"targetForm\" onsubmit=\"xajax_havenShowAction(xajax.getFormValues('targetForm'));return false;\" >";

            tableStart("Zielwahl");

            if (isset($fleet->targetEntity)) {
                $csx = $fleet->targetEntity->sx();
                $csy = $fleet->targetEntity->sy();
                $ccx = $fleet->targetEntity->cx();
                $ccy = $fleet->targetEntity->cy();
                $psp = $fleet->targetEntity->pos();
            } else {
                $csx = $fleet->sourceEntity->sx();
                $csy = $fleet->sourceEntity->sy();
                $ccx = $fleet->sourceEntity->cx();
                $ccy = $fleet->sourceEntity->cy();
                $psp = $fleet->sourceEntity->pos();
            }

            //Startplanet
            echo "<tr><th width=\"25%\"><b>Startplanet:</b></th>
                        <td style=\"padding:2px 2px 3px 6px;background:#000;color:#fff;height:47px;\">
                            <img src=\"" . $fleet->sourceEntity->imagePath() . "\" style=\"float:left;\" >
                            <br/>&nbsp;&nbsp; " . $fleet->sourceEntity . " (" . $fleet->sourceEntity->entityCodeString() . ", Besitzer: " . $fleet->sourceEntity->owner() . ")
                        </td></tr>";
            // Manuelle Auswahl
            echo "<tr id=\"manuelselect\"><th width=\"25%\">Manuelle Eingabe:</th><td width=\"75%\">";
            echo "<input type=\"text\"
                                                id=\"man_sx\"
                                                name=\"man_sx\"
                                                size=\"1\"
                                                maxlength=\"1\"
                                                value=\"$csx\"
                                                title=\"Sektor X-Koordinate\"
                                                tabindex=\"1\"
                                                autocomplete=\"off\"
                                                onfocus=\"this.select()\"
                                                onclick=\"this.select()\"
                                                onkeydown=\"detectChangeRegister(this,'t1');\"
                                                onkeyup=\"if (detectChangeTest(this,'t1')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                onkeypress=\"return nurZahlen(event)\"
                    />&nbsp;/&nbsp;";
            echo "<input type=\"text\"
                                                id=\"man_sy\"
                                                name=\"man_sy\"
                                                size=\"1\"
                                                maxlength=\"1\"
                                                value=\"$csy\"
                                                title=\"Sektor Y-Koordinate\"
                                                tabindex=\"2\"
                                                autocomplete=\"off\"
                                                onfocus=\"this.select()\"
                                                onclick=\"this.select()\"
                                                onkeydown=\"detectChangeRegister(this,'t2');\"
                                                onkeyup=\"if (detectChangeTest(this,'t2')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                onkeypress=\"return nurZahlen(event)\"
                    />&nbsp;&nbsp;:&nbsp;&nbsp;";
            echo "<input type=\"text\"
                                                id=\"man_cx\"
                                                name=\"man_cx\"
                                                size=\"2\"
                                                maxlength=\"2\"
                                                value=\"$ccx\"
                                                title=\"Zelle X-Koordinate\"
                                                tabindex=\"3\"
                                                autocomplete=\"off\"
                                                onfocus=\"this.select()\"
                                                onclick=\"this.select()\"
                                                onkeydown=\"detectChangeRegister(this,'t3');\"
                                                onkeyup=\"if (detectChangeTest(this,'t3')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                onkeypress=\"return nurZahlen(event)\"
                    />&nbsp;/&nbsp;";
            echo "<input type=\"text\"
                                                id=\"man_cy\"
                                                name=\"man_cy\"
                                                size=\"2\"
                                                maxlength=\"2\"
                                                value=\"$ccy\"
                                                tabindex=\"4\"
                                                autocomplete=\"off\"
                                                onfocus=\"this.select()\"
                                                onclick=\"this.select()\"
                                                onkeydown=\"detectChangeRegister(this,'t4');\"
                                                onkeyup=\"if (detectChangeTest(this,'t4')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                onkeypress=\"return nurZahlen(event)\"
                    />&nbsp;&nbsp;:&nbsp;&nbsp;";
            echo "<input type=\"text\"
                                                id=\"man_p\"
                                                name=\"man_p\"
                                                size=\"2\"
                                                maxlength=\"2\"
                                                value=\"$psp\"
                                                title=\"Position des Planeten im Sonnensystem\"
                                                tabindex=\"5\"
                                                autocomplete=\"off\"
                                                onfocus=\"this.select()\"
                                                onclick=\"this.select()\"
                                                onkeydown=\"detectChangeRegister(this,'t5');\"
                                                onkeyup=\"if (detectChangeTest(this,'t5')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                onkeypress=\"return nurZahlen(event)\"
                    /></td></tr>";

            echo "<tr id=\"bookmarkselect\"><th width=\"25%\">Zielfavoriten:</th><td width=\"75%\" align=\"left\">";
            echo "<select name=\"bookmarks\"
                                            id=\"bookmarks\"
                                            onchange=\"showLoader('submitbutton');xajax_havenBookmark(xajax.getFormValues('targetForm'));\"
                                            tabindex=\"6\"
                            >\n";
            echo "<option value=\"0\"";
            echo ">Wählen...</option>";

            $userPlanets = $planetRepository->getUserPlanetsWithCoordinates($fleet->ownerId());
            foreach ($userPlanets as $userPlanet) {
                echo "<option value=\"" . $userPlanet->id . "\"";
                echo ">Eigener Planet: " . $userPlanet->toString() . "</option>\n";
            }

            $bookmarkedEntities = $bookmarkRepository->getBookmarkedEntities($fleet->ownerId());
            if (count($bookmarkedEntities) > 0) {
                echo "<option value=\"0\"";
                echo ">-------------------------------</option>\n";

                foreach ($bookmarkedEntities as $bookmarkedEntity) {
                    echo "<option value=\"" . $bookmarkedEntity->id . "\"";
                    echo ">" . $bookmarkedEntity->toString() . "</option>\n";
                }
            }
            echo "</select>";

            echo "</td></tr>";

            // Speedfaktor
            echo "<tr id=\"speedselect\">
                        <th width=\"25%\">Speedfaktor:</th>
                        <td width=\"75%\" align=\"left\">";
            echo "<select name=\"speed_percent\"
                                            id=\"duration_percent\"
                                            onchange=\"showLoader('submitbutton');showLoader('duration');xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
                                            tabindex=\"6\"
                            >\n";
            for ($x = 100; $x > 0; $x -= 1) {
                echo "<option value=\"$x\"";
                if ($fleet->getSpeedPercent() == $x) echo " selected=\"selected\"";
                echo ">" . $x . "</option>\n";
            }
            echo "</select> %";

            echo "</td></tr>";

            // Daten anzeigen
            echo "<tr><th id=\"targettitle\" width=\"25%\"><b>Ziel-Informationen:</b></th>
                        <td id=\"targetinfo\" style=\"padding:2px 2px 3px 6px;background:#000;color:#fff;height:47px;\">
                            <img src=\"images/loading.gif\" alt=\"Loading\" /> Lade Daten...
                        </td></tr>";
            echo "<tr><th>Entfernung:</th>
                        <td id=\"distance\">-</td></tr>";
            echo "<tr><th width=\"25%\">Kosten/100 AE:</th>
                        <td id=\"costae\">" . StringUtils::formatNumber($fleet->getCostsPerHundredAE()) . " t " . ResourceNames::FUEL . "</td></tr>";
            echo "<tr><th>Geschwindigkeit:</th>
                        <td id=\"speed\">" . StringUtils::formatNumber($fleet->getSpeed()) . " AE/h";
            if ($fleet->sBonusSpeed > 1)
                echo " (inkl. " . StringUtils::formatPercentString($fleet->sBonusSpeed, true) . " Mysticum-Bonus)";
            echo "</td></tr>";
            echo "<tr><th>Dauer:</th>
                        <td><span id=\"duration\" style=\"font-weight:bold;\">-</span> (inkl. Start- und Landezeit von " . StringUtils::formatTimespan($fleet->getTimeLaunchLand()) . ")</td></tr>";
            echo "<tr><th>Treibstoff:</th>
                        <td><span id=\"costs\" style=\"font-weight:bold;\">-</span> (inkl. Start- und Landeverbrauch von " . StringUtils::formatNumber($fleet->getCostsLaunchLand()) . " " . ResourceNames::FUEL . ")</td></tr>";
            echo "<tr><th>Nahrung:</th>
                        <td><span id=\"food\"  style=\"font-weight:bold;\">-</span></td></tr>";
            echo "<tr><th>Piloten:</th>
                        <td>" . StringUtils::formatNumber($fleet->getPilots());
            if ($fleet->sBonusPilots != 1)
                echo " (inkl. " . StringUtils::formatPercentString($fleet->sBonusPilots, true, true) . " Mysticum-Bonus)";
            echo "</td></tr>";
            echo "<tr><th>Bemerkungen:</th>
                        <td id=\"comment\">-</td></tr>";
            echo "<tr id=\"allianceAttacks\" style=\"display: none;\"><th>Allianzangriffe:</th><td id=\"alliance\">-</td></tr>";
            tableEnd();

            echo "<div id=\"submitbutton\"></div>";

            echo "</form>";

            $response->assign("havenContentTarget", "innerHTML", ob_get_contents());
            $response->assign("havenContentTarget", "style.display", '');

            $response->assign("havenContentAction", "innerHTML", "");
            $response->assign("havenContentAction", "style.display", 'none');

            $response->script("document.getElementById('man_sx').focus();");
            $response->script("xajax_havenTargetInfo(xajax.getFormValues('targetForm'))");



            ob_end_clean();
        } else {
            $response->alert($fleet->error());
        }
    } else {
        $response->alert("Fehler! Es wurden keine Schiffe gewählt oder es sind keine vorhanden!");
    }


    $_SESSION['haven']['fleetObj'] = serialize($fleet);
    return $response;
}

/**
 * Verify wormhole and show target selector
 */
function havenShowWormhole($form)
{
    // TODO
    global $app;

    /** @var ConfigurationService $config */
    $config = $app[ConfigurationService::class];
    /** @var EntityRepository $entityRepository */
    $entityRepository = $app[EntityRepository::class];
    /** @var PlanetRepository $planetRepository */
    $planetRepository = $app[PlanetRepository::class];
    /** @var UserUniverseDiscoveryService $userUniverseDiscoveryService */
    $userUniverseDiscoveryService = $app[UserUniverseDiscoveryService::class];
    /** @var LogRepository $logRepository */
    $logRepository = $app[LogRepository::class];
    /** @var BookmarkRepository $bookmarkRepository */
    $bookmarkRepository = $app[BookmarkRepository::class];

    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];

    $response = new xajaxResponse();

    // Do some checks
    if (count($form) > 0) {
        // Get fleet object
        /** @var FleetLaunch $fleet */
        $fleet = unserialize($_SESSION['haven']['fleetObj']);

        if ($fleet->wormholeEntryEntity == null) {
            $owner = $userRepository->getUser(intval($fleet->owner->id));
            $absX = (($form['man_sx'] - 1) * $config->param1Int('num_of_cells')) + $form['man_cx'];
            $absY = (($form['man_sy'] - 1) * $config->param2Int('num_of_cells')) + $form['man_cy'];
            $code = $userUniverseDiscoveryService->discovered($owner, $absX, $absY) == 0 ? 'u' : '';

            $entity = $entityRepository->findByCoordinates(new EntityCoordinates($form['man_sx'], $form['man_sy'], $form['man_cx'], $form['man_cy'], $form['man_p']));
            if ($entity !== null) {
                if ($code == '')
                    $ent = Entity::createFactory($entity->code, $entity->id);
                else
                    $ent = Entity::createFactory($code, $entity->id);

                //Info Feld des ersten Teiles des Fluges, Tabelle muss vor setWormhole stehen!!
                ob_start();
                tableStart("Flug bis zum Wurmloch");
                echo "<tr><th width=\"25%\"><b>Startplanet:</b></th>
                            <td style=\"padding:2px 2px 3px 6px;background:#000;color:#fff;height:47px;\">
                                <img src=\"" . $fleet->sourceEntity->imagePath() . "\" style=\"float:left;\" >
                                <br/>&nbsp;&nbsp; " . $fleet->sourceEntity . " (" . $fleet->sourceEntity->entityCodeString() . ", Besitzer: " . $fleet->sourceEntity->owner() . ")
                            </td></tr>
                        <tr><th width=\"25%\"><b>Wurmloch-Eintrittspunkt:</b></th>
                            <td style=\"padding:2px 2px 3px 6px;background:#000;color:#fff;height:47px;\">
                                <img src=\"" . $fleet->targetEntity->imagePath() . "\" style=\"float:left;\" >
                                <br/>&nbsp;&nbsp; " . $fleet->targetEntity . " (" . $fleet->targetEntity->entityCodeString() . ", Besitzer: " . $fleet->targetEntity->owner() . ")
                            </td></tr>
                        <tr><th width=\"25%\"><b>Entfernung:</b></th><td>" . StringUtils::formatNumber($fleet->getDistance()) . " AE" . "</td>
                        <tr><th width=\"25%\"><b>Kosten/100 AE:</b></th><td>" . StringUtils::formatNumber($fleet->getCostsPerHundredAE()) . " t " . ResourceNames::FUEL . "</td>";
                $speedString = StringUtils::formatNumber($fleet->getSpeed()) . " AE/h";
                if ($fleet->sBonusSpeed > 1)
                    $speedString .= " (inkl. " . StringUtils::formatPercentString($fleet->sBonusSpeed, true) . " Mysticum-Bonus)";
                echo "<tr><th width=\"25%\"><b>Geschwindigkeit:</b></th><td>" . $speedString . "</td>
                        <tr><th width=\"25%\"><b>Dauer:</b></th><td>" . StringUtils::formatTimespan($fleet->getDuration()) . " (inkl. Start- und Landezeit von " . StringUtils::formatTimespan($fleet->getTimeLaunchLand()) . ")</td>
                        <tr><th width=\"25%\"><b>Treibstoff:</b></th><td>" . StringUtils::formatNumber($fleet->getCosts()) . " t " . ResourceNames::FUEL . "  (inkl. Start- und Landeverbrauch von " . StringUtils::formatNumber($fleet->getCostsLaunchLand()) . " " . ResourceNames::FUEL . ")</td>
                        <tr><th width=\"25%\"><b>Nahrung:</b></th><td>" . StringUtils::formatNumber($fleet->getCostsFood()) . " t " . ResourceNames::FOOD . "</td>
                        <tr><th width=\"25%\"><b>Piloten:</b></th><td>" . StringUtils::formatNumber($fleet->getPilots()) . "</td>";

                $response->assign("havenContentTarget", "innerHTML", ob_get_contents());

                ob_end_clean();

                if ($fleet->setWormhole($ent, $form['speed_percent'])) {
                    ob_start();
                    echo "<form id=\"targetForm\" onsubmit=\"xajax_havenShowAction(xajax.getFormValues('targetForm'));return false;\" >";
                    tableStart("Zielwahl nach dem Wurmlochsprung wählen");

                    $csx = $fleet->sourceEntity->sx();
                    $csy = $fleet->sourceEntity->sy();
                    $ccx = $fleet->sourceEntity->cx();
                    $ccy = $fleet->sourceEntity->cy();
                    $psp = $fleet->sourceEntity->pos();

                    //Wurmlochaustritt
                    echo "<tr><th width=\"25%\"><b>Wurmloch-Austrittspunkt:</b></th>
                                <td style=\"padding:2px 2px 3px 6px;background:#000;color:#fff;height:47px;\">
                                    <img src=\"" . $fleet->wormholeExitEntity->imagePath() . "\" style=\"float:left;\" >
                                    <br/>&nbsp;&nbsp; " . $fleet->wormholeExitEntity . " (" . $fleet->wormholeExitEntity->entityCodeString() . ", Besitzer: " . $fleet->wormholeExitEntity->owner() . ")
                                </td></tr>";
                    // Manuelle Auswahl
                    echo "<tr><th width=\"25%\">Manuelle Eingabe:</th><td width=\"75%\">";
                    echo "<input type=\"text\"
                                                    id=\"man_sx\"
                                                    name=\"man_sx\"
                                                    size=\"1\"
                                                    maxlength=\"1\"
                                                    value=\"$csx\"
                                                    title=\"Sektor X-Koordinate\"
                                                    tabindex=\"1\"
                                                    autocomplete=\"off\"
                                                    onfocus=\"this.select()\"
                                                    onclick=\"this.select()\"
                                                    onkeydown=\"detectChangeRegister(this,'t1');\"
                                                    onkeyup=\"if (detectChangeTest(this,'t1')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                    onkeypress=\"return nurZahlen(event)\"
                        />&nbsp;/&nbsp;";
                    echo "<input type=\"text\"
                                                    id=\"man_sy\"
                                                    name=\"man_sy\"
                                                    size=\"1\"
                                                    maxlength=\"1\"
                                                    value=\"$csy\"
                                                    title=\"Sektor Y-Koordinate\"
                                                    tabindex=\"2\"
                                                    autocomplete=\"off\"
                                                    onfocus=\"this.select()\"
                                                    onclick=\"this.select()\"
                                                    onkeydown=\"detectChangeRegister(this,'t2');\"
                                                    onkeyup=\"if (detectChangeTest(this,'t2')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                    onkeypress=\"return nurZahlen(event)\"
                        />&nbsp;&nbsp;:&nbsp;&nbsp;";
                    echo "<input type=\"text\"
                                                    id=\"man_cx\"
                                                    name=\"man_cx\"
                                                    size=\"2\"
                                                    maxlength=\"2\"
                                                    value=\"$ccx\"
                                                    title=\"Zelle X-Koordinate\"
                                                    tabindex=\"3\"
                                                    autocomplete=\"off\"
                                                    onfocus=\"this.select()\"
                                                    onclick=\"this.select()\"
                                                    onkeydown=\"detectChangeRegister(this,'t3');\"
                                                    onkeyup=\"if (detectChangeTest(this,'t3')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                    onkeypress=\"return nurZahlen(event)\"
                        />&nbsp;/&nbsp;";
                    echo "<input type=\"text\"
                                                    id=\"man_cy\"
                                                    name=\"man_cy\"
                                                    size=\"2\"
                                                    maxlength=\"2\"
                                                    value=\"$ccy\"
                                                    tabindex=\"4\"
                                                    autocomplete=\"off\"
                                                    onfocus=\"this.select()\"
                                                    onclick=\"this.select()\"
                                                    onkeydown=\"detectChangeRegister(this,'t4');\"
                                                    onkeyup=\"if (detectChangeTest(this,'t4')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                    onkeypress=\"return nurZahlen(event)\"
                        />&nbsp;&nbsp;:&nbsp;&nbsp;";
                    echo "<input type=\"text\"
                                                    id=\"man_p\"
                                                    name=\"man_p\"
                                                    size=\"2\"
                                                    maxlength=\"2\"
                                                    value=\"$psp\"
                                                    title=\"Position des Planeten im Sonnensystem\"
                                                    tabindex=\"5\"
                                                    autocomplete=\"off\"
                                                    onfocus=\"this.select()\"
                                                    onclick=\"this.select()\"
                                                    onkeydown=\"detectChangeRegister(this,'t5');\"
                                                    onkeyup=\"if (detectChangeTest(this,'t5')) { showLoader('submitbutton');showLoader('targetinfo');xajax_havenTargetInfo(xajax.getFormValues('targetForm')); }\"
                                                    onkeypress=\"return nurZahlen(event)\"
                        /></td></tr>";

                    echo "<tr id=\"bookmarkselect\"><th width=\"25%\">Zielfavoriten:</th><td width=\"75%\" align=\"left\">";
                    echo "<select name=\"bookmarks\"
                                                id=\"bookmarks\"
                                                onchange=\"showLoader('submitbutton');xajax_havenBookmark(xajax.getFormValues('targetForm'));\"
                                                tabindex=\"6\"
                                >\n";
                    echo "<option value=\"0\"";
                    echo ">Wählen...</option>";

                    $userPlanets = $planetRepository->getUserPlanetsWithCoordinates($fleet->ownerId());
                    foreach ($userPlanets as $userPlanet) {
                        echo "<option value=\"" . $userPlanet->id . "\"";
                        echo ">Eigener Planet: " . $userPlanet->toString() . "</option>\n";
                    }

                    $bookmarkedEntities = $bookmarkRepository->getBookmarkedEntities($fleet->ownerId());
                    if (count($bookmarkedEntities) > 0) {
                        echo "<option value=\"0\"";
                        echo ">-------------------------------</option>\n";

                        foreach ($bookmarkedEntities as $bookmarkedEntity) {
                            echo "<option value=\"" . $bookmarkedEntity->id . "\"";
                            echo ">" . $bookmarkedEntity->toString() . "</option>\n";
                        }
                    }
                    echo "</select>";

                    echo "</td></tr>";

                    // Speedfaktor
                    echo "<tr id=\"speedselect\">
                            <th width=\"25%\">Speedfaktor:</th>
                            <td width=\"75%\" align=\"left\">";
                    echo "<select name=\"speed_percent\"
                                                id=\"duration_percent\"
                                                onchange=\"showLoader('submitbutton');showLoader('duration');xajax_havenTargetInfo(xajax.getFormValues('targetForm'))\"
                                                tabindex=\"6\"
                                >\n";
                    for ($x = 100; $x > 0; $x -= 1) {
                        echo "<option value=\"$x\"";
                        if ($fleet->getSpeedPercent() == $x) echo " selected=\"selected\"";
                        echo ">" . $x . "</option>\n";
                    }
                    echo "</select> %";

                    echo "</td></tr>";

                    // Daten anzeigen
                    echo "<tr><th id=\"targettitle\" width=\"25%\"><b>Ziel-Informationen:</b></th>
                            <td id=\"targetinfo\" style=\"padding:2px 2px 3px 6px;background:#000;color:#fff;height:47px;\">
                                <img src=\"images/loading.gif\" alt=\"Loading\" /> Lade Daten...
                            </td></tr>";
                    echo "<tr><th>Entfernung:</th>
                            <td id=\"distance\">-</td></tr>";
                    echo "<tr><th width=\"25%\">Kosten/100 AE:</th>
                            <td id=\"costae\">" . StringUtils::formatNumber($fleet->getCostsPerHundredAE()) . " t " . ResourceNames::FUEL . "</td></tr>";
                    echo "<tr><th>Geschwindigkeit:</th>
                            <td id=\"speed\">" . StringUtils::formatNumber($fleet->getSpeed()) . " AE/h";
                    if ($fleet->sBonusSpeed > 1)
                        echo " (inkl. " . StringUtils::formatPercentString($fleet->sBonusSpeed, true) . " Mysticum-Bonus)";
                    echo "</td></tr>";
                    echo "<tr><th>Dauer:</th>
                            <td><span id=\"duration\" style=\"font-weight:bold;\">-</span> (inkl. Start- und Landezeit von " . StringUtils::formatTimespan($fleet->getTimeLaunchLand()) . ")</td></tr>";
                    echo "<tr><th>Treibstoff:</th>
                            <td><span id=\"costs\" style=\"font-weight:bold;\">-</span> (inkl. Start- und Landeverbrauch von " . StringUtils::formatNumber($fleet->getCostsLaunchLand()) . " " . ResourceNames::FUEL . ")</td></tr>";
                    echo "<tr><th>Nahrung:</th>
                            <td><span id=\"food\"  style=\"font-weight:bold;\">-</span></td></tr>";
                    echo "<tr><th>Piloten:</th>
                            <td>" . StringUtils::formatNumber($fleet->getPilots());
                    if ($fleet->sBonusPilots != 1)
                        echo " (inkl. " . StringUtils::formatPercentString($fleet->sBonusPilots, true, true) . " Mysticum-Bonus)";
                    echo "</td></tr>";
                    echo "<tr><th>Bemerkungen:</th>
                            <td id=\"comment\">-</td></tr>";
                    echo "<tr id=\"allianceAttacks\" style=\"display: none;\"><th>Allianzangriffe:</th><td id=\"alliance\">-</td></tr>";
                    tableEnd();

                    echo "<div id=\"submitbutton\"></div>
                                </form>";


                    $response->assign("havenContentWormhole", "innerHTML", ob_get_contents());
                    $response->assign("havenContentWormhole", "style.display", '');

                    $response->script("document.getElementById('man_sx').focus();");
                    $response->script("xajax_havenTargetInfo(xajax.getFormValues('targetForm'))");

                    ob_end_clean();
                } else {
                    $response->alert($fleet->error());
                }
            } else {
                $response->alert("Ungültiges Ziel!");
            }
        } else {
            include_once(getcwd() . '/inc/bootstrap.inc.php');
            $logRepository->add(
                LogFacility::ILLEGALACTION,
                LogSeverity::INFO,
                'Der User ' . $_SESSION['user_nick'] . ' versuchte, ein zweites Wurmloch zu &ouml;ffnen' . "\n"
                    . 'Bereits gesetztes Wurmloch: ' . $fleet->wormholeEntryEntity . ' mit Austrittspunkt ' . $fleet->wormholeExitEntity . "\n"
                    . 'Zweites Wumloch: ' . $form['man_sx'] . ' / ' . $form['man_sy'] . ' : ' . $form['man_cx'] . ' / ' . $form['man_cy'] . ' : ' . $form['man_p'] . '.'
            );
            $response->alert("Wurmloch wurde bereits gesetzt!");
        }


        $_SESSION['haven']['fleetObj'] = serialize($fleet);
    } else {
        $response->alert("Fehler! Es wurden keine Ziel gewählt!");
    }
    return $response;
}

/**
 * Verify target and show action selector
 */
function havenShowAction($form)
{
    // TODO
    global $app;

    /** @var ConfigurationService $config */
    $config = $app[ConfigurationService::class];

    /** @var UserUniverseDiscoveryService $userUniverseDiscoveryService */
    $userUniverseDiscoveryService = $app[UserUniverseDiscoveryService::class];
    /** @var EntityRepository $entityRepository */
    $entityRepository = $app[EntityRepository::class];
    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];

    $response = new xajaxResponse();

    // Do some checks
    if (count($form) > 0) {
        // Get fleet object
        /** @var FleetLaunch $fleet */
        $fleet = unserialize($_SESSION['haven']['fleetObj']);

        $owner = $userRepository->getUser(intval($fleet->owner->id));
        $absX = (($form['man_sx'] - 1) * $config->param1Int('num_of_cells')) + $form['man_cx'];
        $absY = (($form['man_sy'] - 1) * $config->param2Int('num_of_cells')) + $form['man_cy'];
        $code = $userUniverseDiscoveryService->discovered($owner, $absX, $absY) == 0 ? 'u' : '';

        $entity = $entityRepository->findByCoordinates(new EntityCoordinates($form['man_sx'], $form['man_sy'], $form['man_cx'], $form['man_cy'], $form['man_p']));
        if ($entity !== null) {
            if ($code == '')
                $ent = Entity::createFactory($entity->code, $entity->id);
            else
                $ent = Entity::createFactory($code, $entity->id);

            if ($fleet->setTarget($ent, $form['speed_percent'])) {
                if ($fleet->checkTarget()) {


                    // Target infos
                    //
                    ob_start();
                    tableStart("Zielinfos");
                    if ($fleet->wormholeEntryEntity != null) {
                        echo "<tr><th width=\"25%\"><b>Wurmloch-Eintrittspunkt:</b></th>
                                <td id=\"targetinfo\" style=\"padding:16px 2px 2px 60px;color:#fff;height:47px;background:#000 url('" . $fleet->wormholeEntryEntity->imagePath() . "') no-repeat 3px 3px;\">
                                    " . $fleet->wormholeEntryEntity . " (" . $fleet->wormholeEntryEntity->entityCodeString() . ", Besitzer: " . $fleet->wormholeEntryEntity->owner() . ")
                                </td></tr>";
                        echo "<tr><th width=\"25%\"><b>Wurmloch-Austrittspunkt:</b></th>
                                <td id=\"targetinfo\" style=\"padding:16px 2px 2px 60px;color:#fff;height:47px;background:#000 url('" . $fleet->wormholeExitEntity->imagePath() . "') no-repeat 3px 3px;\">
                                    " . $fleet->wormholeExitEntity . " (" . $fleet->wormholeExitEntity->entityCodeString() . ", Besitzer: " . $fleet->wormholeExitEntity->owner() . ")
                                </td></tr>";
                    }
                    echo "<tr><th width=\"25%\"><b>Ziel-Informationen:</b></th>
                            <td id=\"targetinfo\" style=\"padding:16px 2px 2px 60px;color:#fff;height:47px;background:#000 url('" . $ent->imagePath() . "') no-repeat 3px 3px;\">
                                " . $ent . " (" . $ent->entityCodeString() . ", Besitzer: " . $ent->owner() . ")
                            </td></tr>";
                    echo "<tr>
                            <th width=\"25%\">Speedfaktor:</th>
                            <td width=\"75%\" align=\"left\">";
                    echo $fleet->getSpeedPercent();
                    echo "%</td></tr>";
                    echo "<tr><th>Entfernung:</th>
                            <td id=\"distance\">" . StringUtils::formatNumber($fleet->getDistance()) . " AE</td></tr>";
                    echo "<tr><th>Dauer:</th>
                            <td><span id=\"duration\" style=\"font-weight:bold;\">" . StringUtils::formatTimespan($fleet->getDuration()) . "</span></td></tr>";
                    echo "<tr><th>Treibstoff:</th>
                            <td><span id=\"costs\" style=\"font-weight:bold;\">" . StringUtils::formatNumber($fleet->getCosts()) . " t " . ResourceNames::FUEL . "</span></td></tr>";
                    echo "<tr><th>Nahrung:</th>
                            <td><span id=\"costsFood\" style=\"font-weight:bold;\">" . StringUtils::formatNumber($fleet->getCostsFood()) . " t " . ResourceNames::FOOD . "</span></td></tr>";
                    echo "<tr id=\"supportTime\" style=\"display: none;\"><th>Supportzeit:</th><td id=\"support\"></td></tr>";
                    tableEnd();

                    $response->assign("havenContentTarget", "innerHTML", ob_get_contents());
                    $response->assign("havenContentTarget", "style.display", '');
                    $response->assign("havenContentWormhole", "innerHTML", '');
                    $response->assign("havenContentWormhole", "style.display", 'none');
                    ob_end_clean();

                    //
                    // Action chooser
                    //
                    ob_start();
                    echo "<form id=\"actionForm\">";
                    tableStart();
                    echo "<tr>
                            <th>Aktionswahl</th>
                            <th colspan=\"2\">Ladung</th>
                        </tr>";
                    echo "<tr><td rowspan=\"9\">";
                    $actionsAvailable = 0;
                    foreach ($fleet->getAllowedActions() as $ac) {
                        if ($fleet->getLeader() > 0) {
                            if ($ac->code() == "alliance") {
                                echo "<input type=\"radio\" onchange=\"xajax_havenCheckAction('" . $ac->code() . "');\" name=\"fleet_action\" value=\"" . $ac->code() . "\" id=\"action_" . $ac->code() . "\"";

                                echo " checked=\"checked\"";
                                echo " /><label for=\"action_" . $ac->code() . "\" " . tm($ac->name(), $ac->desc()) . "> " . $ac . " (unterstützen)</label><br/>";
                                $actionsAvailable++;
                            }
                        } else {
                            echo "<input type=\"radio\" onchange=\"xajax_havenCheckAction('" . $ac->code() . "');\" name=\"fleet_action\" value=\"" . $ac->code() . "\" id=\"action_" . $ac->code() . "\"";

                            if ($actionsAvailable == 0)
                                echo " checked=\"checked\"";
                            echo " /><label for=\"action_" . $ac->code() . "\" " . tm($ac->name(), $ac->desc()) . "> " . $ac . "</label><br/>";
                            $actionsAvailable++;
                        }
                    }
                    if ($actionsAvailable == 0) {
                        echo "<i>Keine Aktion auf dieses Ziel verfügbar!</i><br/>";
                    }
                    echo "<br/>" . $fleet->error();

                    $tabindex = 1;

                    echo "</td>
                        <th style=\"width:170px;\">Freie Kapazität:</th>
                        <td style=\"width:150px;\" id=\"resfree\">" . StringUtils::formatNumber($fleet->getCapacity()) . "</td></tr>
                        <tr><th>Freie Passagierplätze:</th>
                        <td style=\"width:150px;\" id=\"peoplefree\">" . StringUtils::formatNumber($fleet->getPeopleCapacity()) . "</td>
                        </td></tr>
                        <tr id=\"resbox1\" style=\"display:;\"><th>" . ResIcons::METAL . "" . ResourceNames::METAL . "</th>
                        <td><input type=\"text\" name=\"res1\" id=\"res1\" value=\"" . $fleet->getLoadedRes(1) . "\" size=\"12\" tabindex=\"" . ($tabindex++) . "\" onblur=\"xajax_havenCheckRes(1,this.value)\" />
                        <a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(1," . floor($fleet->sourceEntity->getRes(1)) . ");\">max</a></td></tr>
                        <tr id=\"resbox2\" style=\"display:;\"><th>" . ResIcons::CRYSTAL . "" . ResourceNames::CRYSTAL . "</th>
                        <td><input type=\"text\" name=\"res2\" id=\"res2\" value=\"" . $fleet->getLoadedRes(2) . "\" size=\"12\" tabindex=\"" . ($tabindex++) . "\" onblur=\"xajax_havenCheckRes(2,this.value)\" />
                        <a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(2," . floor($fleet->sourceEntity->getRes(2)) . ");\">max</a></td></tr>
                        <tr id=\"resbox3\" style=\"display:;\"><th>" . ResIcons::PLASTIC . "" . ResourceNames::PLASTIC . "</th>
                        <td><input type=\"text\" name=\"res3\" id=\"res3\" value=\"" . $fleet->getLoadedRes(3) . "\" size=\"12\" tabindex=\"" . ($tabindex++) . "\" onblur=\"xajax_havenCheckRes(3,this.value)\" />
                        <a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(3," . floor($fleet->sourceEntity->getRes(3)) . ");\">max</a></td></tr>
                        <tr id=\"resbox4\" style=\"display:;\"><th>" . ResIcons::FUEL . "" . ResourceNames::FUEL . "</th>
                        <td><input type=\"text\" name=\"res4\" id=\"res4\" value=\"" . $fleet->getLoadedRes(4) . "\" size=\"12\" tabindex=\"" . ($tabindex++) . "\" onblur=\"xajax_havenCheckRes(4,this.value)\" />
                        <a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(4," . floor($fleet->sourceEntity->getRes(4)) . ");\">max</a></td></tr>
                        <tr id=\"resbox5\" style=\"display:;\"><th>" . ResIcons::FOOD . "" . ResourceNames::FOOD . "</th>
                        <td><input type=\"text\" name=\"res5\" id=\"res5\" value=\"" . $fleet->getLoadedRes(5) . "\" size=\"12\" tabindex=\"" . ($tabindex++) . "\" onblur=\"xajax_havenCheckRes(5,this.value)\" />
                        <a href=\"javascript:;\" onclick=\"xajax_havenCheckRes(5," . floor($fleet->sourceEntity->getRes(5)) . ");\">max</a></td></tr>
                        <tr id=\"resbox6\" style=\"display:;\"><th>" . ResIcons::PEOPLE . "Passagiere</th>
                        <td><input type=\"text\" name=\"resp\" id=\"resp\" value=\"" . $fleet->capacityPeopleLoaded . "\" size=\"12\" tabindex=\"" . ($tabindex++) . "\" onblur=\"xajax_havenCheckPeople(this.value)\" />
                        <a href=\"javascript:;\" onclick=\"xajax_havenCheckPeople(" . floor($fleet->sourceEntity->people()) . ");\">max</a></td></tr>
                        <tr id=\"resbox7\" style=\"display:;\"><th id=\"respercent\">&nbsp;</th>
                        <td>&nbsp;
                        <a href=\"javascript:;\" onclick=\"xajax_havenSetResAll();\">Alles einladen</a></td></tr>

                        <tr id=\"fetchbox1\" style=\"display:none;\"><th>" . ResIcons::METAL . "" . ResourceNames::METAL . "</th>
                        <td><input type=\"text\" name=\"fetch1\" id=\"fres1\" value=\"0\" size=\"12\" onkeyup=\"FormatNumber(this.id,this.value, '" . $fleet->getTotalCapacity() . "', '', '');\"/>
                        <a href=\"javascript:;\" onclick=\"document.getElementById('fres1').value=" . $fleet->getTotalCapacity() . "\">max</a></td></tr>
                        <tr id=\"fetchbox2\" style=\"display:none;\"><th>" . ResIcons::CRYSTAL . "" . ResourceNames::CRYSTAL . "</th>
                        <td><input type=\"text\" name=\"fetch2\" id=\"fres2\" value=\"0\" size=\"12\" onkeyup=\"FormatNumber(this.id,this.value, '" . $fleet->getTotalCapacity() . "', '', '');\"/>
                        <a href=\"javascript:;\" onclick=\"document.getElementById('fres2').value=" . $fleet->getTotalCapacity() . "\">max</a></td></tr>
                        <tr id=\"fetchbox3\" style=\"display:none;\"><th>" . ResIcons::PLASTIC . "" . ResourceNames::PLASTIC . "</th>
                        <td><input type=\"text\" name=\"fetch3\" id=\"fres3\" value=\"0\" size=\"12\" onkeyup=\"FormatNumber(this.id,this.value, '" . $fleet->getTotalCapacity() . "', '', '');\"/>
                        <a href=\"javascript:;\" onclick=\"document.getElementById('fres3').value=" . $fleet->getTotalCapacity() . "\">max</a></td></tr>
                        <tr id=\"fetchbox4\" style=\"display:none;\"><th>" . ResIcons::FUEL . "" . ResourceNames::FUEL . "</th>
                        <td><input type=\"text\" name=\"fetch4\" id=\"fres4\" value=\"0\" size=\"12\" onkeyup=\"FormatNumber(this.id,this.value, '" . $fleet->getTotalCapacity() . "', '', '');\"/>
                        <a href=\"javascript:;\" onclick=\"document.getElementById('fres4').value=" . $fleet->getTotalCapacity() . "\">max</a></td></tr>
                        <tr id=\"fetchbox5\" style=\"display:none;\"><th>" . ResIcons::FOOD . "" . ResourceNames::FOOD . "</th>
                        <td><input type=\"text\" name=\"fetch5\" id=\"fres5\" value=\"0\" size=\"12\" onkeyup=\"FormatNumber(this.id,this.value, '" . $fleet->getTotalCapacity() . "', '', '');\"/>
                        <a href=\"javascript:;\" onclick=\"document.getElementById('fres5').value=" . $fleet->getTotalCapacity() . "\">max</a></td></tr>
                        <tr id=\"fetchbox6\" style=\"display:none;\"><th>" . ResIcons::PEOPLE . "Passagiere</th>
                        <td><input type=\"text\" name=\"fetchp\" id=\"fresp\" value=\"0\" size=\"12\" onkeyup=\"FormatNumber(this.id,this.value, '" . $fleet->getTotalPeopleCapacity() . "', '', '');\"/>
                        <a href=\"javascript:;\" onclick=\"document.getElementById('fresp').value=" . $fleet->getTotalPeopleCapacity() . "\">max</a></td></tr>
                        <tr id=\"fetchbox7\" style=\"display:none;\"><th>&nbsp;</th>
                        <td>&nbsp;
                        <a href=\"javascript:;\" onclick=\"xajax_havenSetFetchAll();\">Alles einladen</a></td></tr>

                        <tr id=\"msgHeader\" style=\"display:none;\"><th colspan=\"2\">Nachricht</th><th>Empfänger</th></tr>
                        <tr id=\"msg\" style=\"display:none;\"></tr>
                        <tr id=\"fakeheader\" style=\"display:none;\"><th colspan=\"3\">Die Schiffe sollen als welche Schiffe getarnt werden?</th></tr>
                        <tr id=\"fakebox\" style=\"display:none;\"></tr>";

                    tableEnd();

                    echo "<div id=\"submitbutton\"><input type=\"button\" onclick=\"xajax_havenWormholeReset();xajax_havenShowTarget(null)\" value=\"&lt;&lt; Zurück zur Zielwahl\" /> &nbsp; ";
                    echo "<input type=\"button\" onclick=\"xajax_havenReset()\" value=\"Reset\" /> &nbsp; ";
                    if ($actionsAvailable > 0) {
                        echo "<input type=\"button\" onclick=\"showLoader('submitbutton');xajax_havenShowLaunch(xajax.getFormValues('actionForm'))\" value=\"Start! &gt;&gt;&gt;\"  /></div>";
                    }
                    echo "</form>";

                    $response->assign("havenContentAction", "innerHTML", ob_get_contents());
                    $response->assign("havenContentAction", "style.display", '');

                    ob_end_clean();
                } else {
                    $response->alert($fleet->error());
                }
            } else {
                $response->alert($fleet->error());
            }
        } else {
            $response->alert("Ungültiges Ziel!");
        }

        $_SESSION['haven']['fleetObj'] = serialize($fleet);
    } else {
        $response->alert("Fehler! Es wurden keine Ziel gewählt!");
    }

    return $response;
}

/**
 * Launch fleet
 */
function havenShowLaunch($form)
{
    global $app;
    $response = new xajaxResponse();

    // Do some checks
    if (count($form) > 0) {
        // Get fleet object
        /** @var FleetLaunch $fleet */
        $fleet = unserialize($_SESSION['haven']['fleetObj']);

        if ($fleet->setAction($form['fleet_action'])) {
            if ($form['fleet_action'] == "fetch") {
                $fetch1 = $fleet->fetchResource(1, StringUtils::parseFormattedNumber($form['fetch1']));
                $fetch2 = $fleet->fetchResource(2, StringUtils::parseFormattedNumber($form['fetch2']));
                $fetch3 = $fleet->fetchResource(3, StringUtils::parseFormattedNumber($form['fetch3']));
                $fetch4 = $fleet->fetchResource(4, StringUtils::parseFormattedNumber($form['fetch4']));
                $fetch5 = $fleet->fetchResource(5, StringUtils::parseFormattedNumber($form['fetch5']));
                $fetch6 = $fleet->fetchResource(6, StringUtils::parseFormattedNumber($form['fetchp']));
                $load1 = $fleet->loadResource(1, 0);
                $load2 = $fleet->loadResource(2, 0);
                $load3 = $fleet->loadResource(3, 0);
                $load4 = $fleet->loadResource(4, 0);
                $load5 = $fleet->loadResource(5, 0);
                $load6 = $fleet->loadPeople(0);
            } else {
                $load1 = $fleet->loadResource(1, StringUtils::parseFormattedNumber($form['res1']));
                $load2 = $fleet->loadResource(2, StringUtils::parseFormattedNumber($form['res2']));
                $load3 = $fleet->loadResource(3, StringUtils::parseFormattedNumber($form['res3']));
                $load4 = $fleet->loadResource(4, StringUtils::parseFormattedNumber($form['res4']));
                $load5 = $fleet->loadResource(5, StringUtils::parseFormattedNumber($form['res5']));
            }

            if ($form['fleet_action'] == "fakeattack") {
                $fleet->setFakeId($form['fakeShip']);
            }

            $duration = $fleet->distance / $fleet->getSpeed();    // Calculate duration
            $duration *= 3600;    // Convert to seconds
            $duration = ceil($duration);
            $maxTime = 0;
            if (count($fleet->aFleets) > 0)  {
                $maxTime = $fleet->aFleets[0]->landTime - time() - $fleet->getTimeLaunchLand() - $fleet->duration1;
            }

            //check for alliance+time to join
            if (($duration < $maxTime) || $form['fleet_action'] != "alliance" || $maxTime < 0)
                {if ($fid = $fleet->launch()) {
                    ob_start();
                    $ac = FleetAction::createFactory($form['fleet_action']);

                    // bugfix - check for alliance added by river
                    if ($form['fleet_action'] == "alliance" && $fleet->getLeader() == 0 && $fleet->owner->allianceid() > 0 && count($form['msgUser']) > 0) {

                        /** @var \EtoA\Message\MessageRepository $messageRepository */
                        $messageRepository = $app[\EtoA\Message\MessageRepository::class];
                        /** @var AllianceRepository $allianceRepository */
                        $allianceRepository = $app[AllianceRepository::class];

                        $fleetOwnerAlliance = $allianceRepository->getAlliance($fleet->owner->allianceId());
                        $subject = "Allianzangriff (" . $fleet->targetEntity . ")";
                        $text = "[b]Angriffsdaten:[/b][table][tr][td]Flottenkennzeichen:[/td][td]" . $fleetOwnerAlliance->tag . "-" . $fid . "[/td][/tr][tr][td]Flottenleader:[/td][td]" . $fleet->owner->nick . "[/td][/tr][tr][td]Zielplanet:[/td][td]" . $fleet->targetEntity . "[/td][/tr][tr][td]Ankunftszeit:[/td][td]" . date("d.m.y, H:i:s", $fleet->landTime) . "[/td][/tr][/table]" . $form['message_text'];
                        foreach ($form['msgUser'] as $uid) {
                            $messageRepository->sendFromUserToUser(
                                (int) $fleet->ownerId(),
                                (int) $uid,
                                $subject,
                                $text,
                                6,
                                $fid
                            );
                        }
                    }

                    tableStart();
                    echo "<tr>
                            <th colspan=\"2\" style=\"color:#0f0\">Flotte gestartet!</th>
                        </tr>";
                    echo "<tr>
                            <td style=\"width:50%\"><b>Aktion:</b></td>
                            <td style=\"color:" . FleetAction::$attitudeColor[$ac->attitude()] . "\">" . $ac->name() . "</td>
                        </tr>";
                    echo "<tr>
                            <td><b>Ladung: " . ResourceNames::METAL . "</b></td>
                            <td>" . StringUtils::formatNumber($fleet->getLoadedRes(1)) . "</td>
                        </tr>";
                    echo "<tr>
                            <td><b>Ladung: " . ResourceNames::CRYSTAL . "</b></td>
                            <td>" . StringUtils::formatNumber($fleet->getLoadedRes(2)) . "</td>
                        </tr>";
                    echo "<tr>
                            <td><b>Ladung: " . ResourceNames::PLASTIC . "</b></td>
                            <td>" . StringUtils::formatNumber($fleet->getLoadedRes(3)) . "</td>
                        </tr>";
                    echo "<tr>
                            <td><b>Ladung: " . ResourceNames::FUEL . "</b></td>
                            <td>" . StringUtils::formatNumber($fleet->getLoadedRes(4)) . "</td>
                        </tr>";
                    echo "<tr>
                            <td><b>Ladung: " . ResourceNames::FOOD . "</b></td>
                            <td>" . StringUtils::formatNumber($fleet->getLoadedRes(5)) . "</td>
                        </tr>";
                    tableEnd();
                    echo "<input type=\"button\" onclick=\"xajax_havenReset()\" value=\"Weitere Flotte starten\" />
                        &nbsp; <input type=\"button\" onclick=\"document.location='?page=fleetinfo&amp;id=" . $fid . "'\" value=\"Flotte beobachten\" />";

                    $response->assign("havenContentAction", "innerHTML", ob_get_contents());
                    $response->assign("havenContentAction", "style.display", '');
                    $response->assign('support', 'innerHTML', StringUtils::formatTimespan($fleet->getSupportTime()));
                    ob_end_clean();
                    $_SESSION['haven']['fleetObj'] = serialize($fleet);

                    if ($app['etoa.quests.enabled']) {
                        $app['cubicle.quests.initializer']->initialize($fleet->ownerId());
                    }
                    $app['dispatcher']->dispatch(new \EtoA\Fleet\Event\FleetLaunch(), \EtoA\Fleet\Event\FleetLaunch::LAUNCH_SUCCESS);
                } else {
                    $response->assign("submitbutton", "innerHTML", "<input type=\"button\" onclick=\"xajax_havenReset()\" value=\"Zurück zur Flottenübersicht\" />");
                    $response->alert("Fehler! Start nicht möglich! \n" . $fleet->error());
                }
            } else {
                $response->assign("submitbutton", "innerHTML", "<input type=\"button\" onclick=\"xajax_havenReset()\" value=\"Zurück zur Flottenübersicht\" />");
                $response->alert("Fehler! Angriff kann nicht mehr erreicht werden! \n" . $fleet->error());
            }
        } else {
            $response->alert("Fehler! Ungültige Aktion! \n" . $fleet->error());
        }
    } else {
        $response->alert("Fehler! Es wurde keine Aktion gewählt!");
    }
    return $response;
}

/**
 * Reset everything
 */
function havenReset()
{
    $response = new xajaxResponse();
    $_SESSION['haven']['fleetObj'] = null;
    $response->script("document.location='?page=haven'");
    return $response;
}

/**
 * Shows information about the target
 */
function havenTargetInfo($form)
{
    // TODO
    global $app;

    /** @var ConfigurationService $config */
    $config = $app[ConfigurationService::class];
    /** @var EntityRepository $entityRepository */
    $entityRepository = $app[EntityRepository::class];
    /** @var UserUniverseDiscoveryService $userUniverseDiscoveryService */
    $userUniverseDiscoveryService = $app[UserUniverseDiscoveryService::class];
    /** @var AllianceRepository $allianceRepository */
    $allianceRepository = $app[AllianceRepository::class];
    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];

    $response = new xajaxResponse();
    $alliance = "";
    $target = false;
    $allianceStyle = 'none';
    $comment = "-";

    ob_start();
    $sx = intval($form['man_sx']);
    $sy = intval($form['man_sy']);
    $cx = intval($form['man_cx']);
    $cy = intval($form['man_cy']);
    $pos = intval($form['man_p']);
    if ($sx > 0 && $sy > 0 && $cx > 0 && $cy > 0 && $pos >= 0) {
        $absX = (($sx - 1) * $config->param1Int('num_of_cells')) + $cx;
        $absY = (($sy - 1) * $config->param2Int('num_of_cells')) + $cy;
        /** @var FleetLaunch $fleet */
        $fleet = unserialize($_SESSION['haven']['fleetObj']);

        $owner = $userRepository->getUser(intval($fleet->owner->id));
        $code = $userUniverseDiscoveryService->discovered($owner, $absX, $absY) == 0 ? 'u' : '';

        $entity = $entityRepository->findByCoordinates(new EntityCoordinates($sx, $sy, $cx, $cy, $pos));
        if ($entity !== null && !($code == 'u' && $pos > 0)) {
            if ($code == '')
                $ent = Entity::createFactory($entity->code, $entity->id);
            else
                $ent = Entity::createFactory($code, $entity->id);

            $fleet->setTarget($ent);
            $fleet->setSpeedPercent($form['speed_percent']);
            $fleet->setLeader(0);
            $allianceAttack = "";

            $speedString = StringUtils::formatNumber($fleet->getSpeed()) . " AE/h";
            if ($fleet->sBonusSpeed > 1)
                $speedString .= " (inkl. " . StringUtils::formatPercentString($fleet->sBonusSpeed, true) . " Mysticum-Bonus)";

            echo "<img src=\"" . $ent->imagePath() . "\" style=\"float:left;\" >";

            echo "<br/>&nbsp;&nbsp; " . $ent . " (" . $ent->entityCodeString() . ", Besitzer: " . $ent->owner() . ")";
            $response->assign('distance', 'innerHTML', StringUtils::formatNumber($fleet->getDistance()) . " AE");
            $response->assign('duration', 'innerHTML', StringUtils::formatTimespan($fleet->getDuration()) . "");
            $response->assign('speed', 'innerHTML', $speedString);
            $response->assign('costae', 'innerHTML', StringUtils::formatNumber($fleet->getCostsPerHundredAE()) . " t " . ResourceNames::FUEL . "");
            $response->assign('costs', 'innerHTML', StringUtils::formatNumber($fleet->getCosts()) . " t " . ResourceNames::FUEL . "");
            $response->assign('food', 'innerHTML', StringUtils::formatNumber($fleet->getCostsFood()) . " t " . ResourceNames::FOOD . "");
            $response->assign('targetinfo', 'style.color', '#fff');

            $target = true;

            if ($ent->entityCode() == 'w' && $fleet->wormholeEntryEntity == NULL && $fleet->wormholeEnable) {
                $action = '<input id="setWormhole" tabindex="9" type="button" onclick="xajax_havenShowWormhole(xajax.getFormValues(\'targetForm\'))" value="Wurmloch auswählen">';
            } else {
                $action = "<input id=\"cooseAction\" tabindex=\"9\" type=\"submit\" value=\"Weiter zur Aktionsauswahl &gt;&gt;&gt;\"  /> &nbsp;";
            }

            if ($ent->ownerId() > 0 && count($fleet->aFleets) > 0) {
                $alliance .= "<table style=\"width:100%;\">";
                $counter = 0;
                $fleetOwnerAlliance = $allianceRepository->getAlliance($fleet->owner->allianceId());
                foreach ($fleet->aFleets as $f) {
                    if ($f->entityTo == $ent->id()) {
                        $counter++;
                        $alliance .= "<tr><input type=\"button\" style=\"width:100%;\" onclick=\"xajax_havenAllianceAttack(" . $f->id . ")\" name=\"" . $fleetOwnerAlliance->tag . "-" . $f->id . "\" value=\"Flottenleader: " . get_user_nick($f->userId) . " Ankunftszeit: " . date("d.m.y, H:i:s", $f->landTime) . "\"/></tr>";
                    }
                }
                $alliance .= "</table>";
                if ($counter > 0)
                    $allianceStyle = '';
            }
        } else {
            echo "<div style=\"color:#f00\">Ziel nicht vorhanden!</div>";
            $response->assign('distance', 'innerHTML', "Unbekannt");
            $response->assign('targetinfo', 'style.color', "#f00");
            $action = "&nbsp; ";
        }

        if ($target)
            $submitButton = '&nbsp;<input tabindex="7" type="button" onclick="xajax_havenShowShips()" value="&lt;&lt; Zurück zur Schiffauswahl" />&nbsp;<input tabindex="8" type="button" onclick="xajax_havenReset()" value="Reset" />&nbsp;' . $action;
        else
            $submitButton = '&nbsp;<input tabindex="8" type="button" onclick="xajax_havenReset()" value="Reset" />&nbsp;';

        $response->assign('submitbutton', 'innerHTML', $submitButton);
        $response->assign('targetinfo', 'innerHTML', ob_get_contents());
        $response->assign('chooseAction', 'innerHTML', $action);
        $response->assign('alliance', 'innerHTML', $alliance);
        $response->assign('allianceAttacks', "style.display", $allianceStyle);
        ob_end_clean();

        $_SESSION['haven']['fleetObj'] = serialize($fleet);

        /*
            ob_start();
            echo "Erlaubte Aktionen: ";
            $cnt=0;
            $entAcCnt = count($ent->allowedFleetActions());
            foreach ($ent->allowedFleetActions() as $ac)
            {
                $action = FleetAction::createFactory($ac);
                echo $action;
                if ($cnt < $entAcCnt -1)
                    echo ", ";
                $cnt++;
            }
            $response->assign('comment','innerHTML',ob_get_clean());
            ob_end_clean();
            */
    }
    return $response;
}

function havenBookmark($form)
{
    // TODO
    global $app;

    /** @var ConfigurationService $config */
    $config = $app[ConfigurationService::class];
    /** @var EntityRepository $entityRepositroy */
    $entityRepositroy = $app[EntityRepository::class];
    /** @var UserUniverseDiscoveryService $userUniverseDiscoveryService */
    $userUniverseDiscoveryService = $app[UserUniverseDiscoveryService::class];
    /** @var AllianceRepository $allianceRepository */
    $allianceRepository = $app[AllianceRepository::class];
    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];

    $response = new xajaxResponse();

    /** @var FleetLaunch $fleet */
    $fleet = unserialize($_SESSION['haven']['fleetObj']);

    if ($form["bookmarks"]) {
        $ent = Entity::createFactoryById($form["bookmarks"]);
        $csx = $ent->sx();
        $csy = $ent->sy();
        $ccx = $ent->cx();
        $ccy = $ent->cy();
        $psp = $ent->pos();
    } else {
        $ent = $fleet->sourceEntity;
        $csx = $fleet->sourceEntity->sx();
        $csy = $fleet->sourceEntity->sy();
        $ccx = $fleet->sourceEntity->cx();
        $ccy = $fleet->sourceEntity->cy();
        $psp = $fleet->sourceEntity->pos();
    }

    $response->assign('man_sx', 'value', $csx);
    $response->assign('man_sy', 'value', $csy);
    $response->assign('man_cx', 'value', $ccx);
    $response->assign('man_cy', 'value', $ccy);
    $response->assign('man_p', 'value', $psp);


    $alliance = "";
    $allianceStyle = 'none';
    ob_start();

    $fleet->setTarget($ent);
    $fleet->setSpeedPercent($form['speed_percent']);
    $fleet->setLeader(0);
    $allianceAttack = "";

    $speedString = StringUtils::formatNumber($fleet->getSpeed()) . " AE/h";
    if ($fleet->sBonusSpeed > 1)
        $speedString .= " (inkl. " . StringUtils::formatPercentString($fleet->sBonusSpeed, true) . " Mysticum-Bonus)";

    $absX = (($csx - 1) * $config->param1Int('num_of_cells')) + $ccx;
    $absY = (($csy - 1) * $config->param2Int('num_of_cells')) + $ccy;

    $owner = $userRepository->getUser(intval($fleet->owner->id));
    $code = $userUniverseDiscoveryService->discovered($owner, $absX, $absY) == 0 ? 'u' : '';

    $entity = $entityRepositroy->findByCoordinates(new EntityCoordinates($csx, $csy, $ccx, $ccy, $psp));
    if ($entity !== null && !($code == 'u' && $psp)) {
        if ($code == '')
            $ent = Entity::createFactory($entity->code, $entity->id);
        else
            $ent = Entity::createFactory($code, $entity->id);
    }
    echo "<img src=\"" . $ent->imagePath() . "\" style=\"float:left;\" >";

    echo "<br/>&nbsp;&nbsp; " . $ent . " (" . $ent->entityCodeString() . ", Besitzer: " . $ent->owner() . ")";
    $response->assign('distance', 'innerHTML', StringUtils::formatNumber($fleet->getDistance()) . " AE");
    $response->assign('duration', 'innerHTML', StringUtils::formatTimespan($fleet->getDuration()) . "");
    $response->assign('speed', 'innerHTML', $speedString);
    $response->assign('costae', 'innerHTML', StringUtils::formatNumber($fleet->getCostsPerHundredAE()) . " t " . ResourceNames::FUEL . "");
    $response->assign('costs', 'innerHTML', StringUtils::formatNumber($fleet->getCosts()) . " t " . ResourceNames::FUEL . "");
    $response->assign('food', 'innerHTML', StringUtils::formatNumber($fleet->getCostsFood()) . " t " . ResourceNames::FOOD . "");
    $response->assign('targetinfo', 'style.color', "#fff");

    $target = true;

    if ($ent->entityCode() == 'w' && $fleet->wormholeEntryEntity == NULL && $fleet->wormholeEnable)
        $action = '<input id="setWormhole" tabindex="9" type="button" onclick="xajax_havenShowWormhole(xajax.getFormValues(\'targetForm\'))" value="Wurmloch auswählen">';
    else {
        if (isset($entity) && $entity->code !== 'w')
            $action = "<input id=\"cooseAction\" tabindex=\"9\" type=\"submit\" value=\"Weiter zur Aktionsauswahl &gt;&gt;&gt;\"  /> &nbsp;";
        else {
            $action = '';
            $target = false;
        }
    }
    // $action = "<input id=\"cooseAction\" tabindex=\"9\" type=\"submit\" value=\"Weiter zur Aktionsauswahl &gt;&gt;&gt;\"  /> &nbsp;";

    if ($ent->ownerId() > 0 && count($fleet->aFleets) > 0) {
        $alliance .= "<table style=\"width:100%;\">";
        $counter = 0;
        $fleetOwnerAlliance = $allianceRepository->getAlliance($fleet->owner->allianceId());
        foreach ($fleet->aFleets as $f) {
            if ($f->entityTo == $ent->id()) {
                $counter++;
                $alliance .= "<tr><input type=\"button\" style=\"width:100%;\" onclick=\"xajax_havenAllianceAttack(" . $f->id . ")\" name=\"" . $fleetOwnerAlliance->tag . "-" . $f->id . "\" value=\"Flottenleader: " . get_user_nick($f->userId) . " Ankunftszeit: " . date("d.m.y, H:i:s", $f->landTime) . "\"/></tr>";
            }
        }
        $alliance .= "</table>";
        if ($counter > 0)
            $allianceStyle = '';
    }

    if ($target == true)
        $submitButton = '&nbsp;<input tabindex="7" type="button" onclick="xajax_havenShowShips()" value="&lt;&lt; Zurück zur Schiffauswahl" />&nbsp;<input tabindex="8" type="button" onclick="xajax_havenReset()" value="Reset" />&nbsp;' . $action;
    else
        $submitButton = '&nbsp;<input tabindex="8" type="button" onclick="xajax_havenReset()" value="Reset" />&nbsp;';

    $response->assign('submitbutton', 'innerHTML', $submitButton);
    $response->assign('targetinfo', 'innerHTML', ob_get_contents());
    $response->assign('alliance', 'innerHTML', $alliance);
    $response->assign('allianceAttacks', "style.display", $allianceStyle);

    ob_end_clean();

    $_SESSION['haven']['fleetObj'] = serialize($fleet);
    return $response;
}

function havenCheckRes($id, $val)
{
    $response = new xajaxResponse();
    $val = max(0, intval(StringUtils::parseFormattedNumber($val)));

    /** @var FleetLaunch $fleet */
    $fleet = unserialize($_SESSION['haven']['fleetObj']);

    $erg = $fleet->loadResource($id, $val);

    $response->assign('res' . $id, 'value', StringUtils::formatNumber($erg));

    $response->assign('resfree', 'innerHTML', StringUtils::formatNumber($fleet->getCapacity()) . " / " . StringUtils::formatNumber($fleet->getTotalCapacity()));
    $response->assign('resfree', 'style.color', "#0f0");
    $response->assign('respercent', 'innerHTML', '');

    $_SESSION['haven']['fleetObj'] = serialize($fleet);

    return $response;
}

function havenSetResAll()
{
    $response = new xajaxResponse();

    $val = 0;
    $erg = 0;
    $sum = 0;
    $loadPerc = 0;

    /** @var FleetLaunch $fleet */
    $fleet = unserialize($_SESSION['haven']['fleetObj']);

    for ($id = 1; $id < 6; $id++) {
        $erg = $fleet->loadResource($id, 0);
        $sum += floor($fleet->sourceEntity->getRes($id));
    }

    $loadPerc = min(1.00, $fleet->getCapacity() / $sum);
    $response->assign('respercent', 'innerHTML', '&nbsp;Ladung: ' . round($loadPerc * 100, 2) . '%');
    if ($loadPerc < 1.00)
        $response->assign('respercent', 'style.color', "#f00");
    else
        $response->assign('respercent', 'style.color', "#0f0");

    for ($id = 1; $id < 6; $id++) {
        if ($id < 5)
            $val = floor(($fleet->sourceEntity->getRes($id)) * $loadPerc);
        else
            $val = floor($fleet->sourceEntity->getRes($id));

        $val = max(0, intval($val));
        $erg = $fleet->loadResource($id, $val);
        $response->assign('res' . $id, 'value', StringUtils::formatNumber($erg));
    }
    $response->assign('resfree', 'innerHTML', StringUtils::formatNumber($fleet->getCapacity()) . " / " . StringUtils::formatNumber($fleet->getTotalCapacity()));
    $response->assign('resfree', 'style.color', "#0f0");

    // max. People
    $val = floor($fleet->sourceEntity->people());
    $val = max(0, intval($val));
    $erg = $fleet->loadPeople($val);
    $response->assign('resp', 'value', StringUtils::formatNumber($erg));
    $response->assign('peoplefree', 'innerHTML', StringUtils::formatNumber($fleet->getPeopleCapacity()) . " / " . StringUtils::formatNumber($fleet->getTotalPeopleCapacity()));
    $response->assign('peoplefree', 'style.color', "#0f0");

    $_SESSION['haven']['fleetObj'] = serialize($fleet);

    return $response;
}

function havenSetFetchAll()
{
    $response = new xajaxResponse();

    $val = 0;
    $erg = 0;
    $sum = 0;
    $loadPerc = 0;

    /** @var FleetLaunch $fleet */
    $fleet = unserialize($_SESSION['haven']['fleetObj']);

    for ($id = 1; $id < 6; $id++) {
        $erg = $fleet->loadResource($id, 0);
        $sum += floor($fleet->sourceEntity->getRes($id));
    }

    for ($id = 1; $id < 6; $id++) {
        $val = floor($fleet->getTotalCapacity());
        $response->assign('fres' . $id, 'value', StringUtils::formatNumber($val));
    }
    $response->assign('resfree', 'innerHTML', StringUtils::formatNumber($fleet->getCapacity()) . " / " . StringUtils::formatNumber($fleet->getTotalCapacity()));
    $response->assign('resfree', 'style.color', "#0f0");

    // max. People
    $val = floor($fleet->getTotalPeopleCapacity());
    $response->assign('fresp', 'value', StringUtils::formatNumber($val));
    $response->assign('peoplefree', 'innerHTML', StringUtils::formatNumber($fleet->getPeopleCapacity()) . " / " . StringUtils::formatNumber($fleet->getTotalPeopleCapacity()));
    $response->assign('peoplefree', 'style.color', "#0f0");

    $_SESSION['haven']['fleetObj'] = serialize($fleet);

    return $response;
}

function havenCheckPeople($val)
{
    $response = new xajaxResponse();
    $val = max(0, intval(StringUtils::parseFormattedNumber($val)));

    /** @var FleetLaunch $fleet */
    $fleet = unserialize($_SESSION['haven']['fleetObj']);

    $erg = $fleet->loadPeople($val);

    $response->assign('resp', 'value', StringUtils::formatNumber($erg));

    $response->assign('peoplefree', 'innerHTML', StringUtils::formatNumber($fleet->getPeopleCapacity()) . " / " . StringUtils::formatNumber($fleet->getTotalPeopleCapacity()));
    $response->assign('peoplefree', 'style.color', "#0f0");

    $_SESSION['haven']['fleetObj'] = serialize($fleet);

    return $response;
}

function havenCheckAction($code)
{
    global $app;

    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];

    $response = new xajaxResponse();
    /** @var FleetLaunch $fleet */
    $fleet = unserialize($_SESSION['haven']['fleetObj']);
    ob_start();
    $fleet->resetSupport();

    if ($code == "support") {
        echo "<form id=\"supportForm\">";
        echo "<input type=\"text\"
                                id=\"hour\"
                                name=\"hour\"
                                size=\"1\"
                                maxlength=\"2\"
                                value=\"0\"
                                title=\"Stunden\"
                                tabindex=\"7\"
                                autocomplete=\"off\"
                                onfocus=\"this.select()\"
                                onclick=\"this.select()\"
                                onkeydown=\"detectChangeRegister(this,'t1');\"
                                onkeyup=\"if (detectChangeTest(this,'t1')) { xajax_havenCheckSupport(xajax.getFormValues('supportForm')); }\"
                                onkeypress=\"return nurZahlen(event)\"
    /> h&nbsp;";
        echo "<input type=\"text\"
                                id=\"min\"
                                name=\"min\"
                                size=\"1\"
                                maxlength=\"2\"
                                value=\"0\"
                                title=\"Minuten\"
                                tabindex=\"8\"
                                autocomplete=\"off\"
                                onfocus=\"this.select()\"
                                onclick=\"this.select()\"
                                onkeydown=\"detectChangeRegister(this,'t2');\"
                                onkeyup=\"if (detectChangeTest(this,'t2')) { xajax_havenCheckSupport(xajax.getFormValues('supportForm')); }\"
                                onkeypress=\"return nurZahlen(event)\"
    /> min&nbsp;&nbsp;";
        echo "<input type=\"text\"
                                id=\"second\"
                                name=\"second\"
                                size=\"1\"
                                maxlength=\"2\"
                                value=\"0\"
                                title=\"Sekunden\"
                                tabindex=\"9\"
                                autocomplete=\"off\"
                                onfocus=\"this.select()\"
                                onclick=\"this.select()\"
                                onkeydown=\"detectChangeRegister(this,'t3');\"
                                onkeyup=\"if (detectChangeTest(this,'t3')) { xajax_havenCheckSupport(xajax.getFormValues('supportForm')); }\"
                                onkeypress=\"return nurZahlen(event)\"
    /> s</form>"; //</span>";*/
        $response->assign('supportTime', "style.display", '');
        $response->assign('support', 'innerHTML', ob_get_contents());
        ob_end_clean();
    } else {
        $fleet->setSupportTime(0);
        $response->assign("supportTime", "style.display", 'none');
        $response->assign("support", "innerHTML", "");
        $response->assign('costs', 'innerHTML', StringUtils::formatNumber($fleet->getCosts()) . " t " . ResourceNames::FUEL);
        $response->assign('costsFood', 'innerHTML', "" . StringUtils::formatNumber($fleet->getCostsFood()) . " t " . ResourceNames::FOOD . "");
        $response->assign('resfree', 'innerHTML', StringUtils::formatNumber($fleet->getCapacity()) . " / " . StringUtils::formatNumber($fleet->getTotalCapacity()));
        $response->assign('resfree', 'style.color', "#0f0");
    }

    if ($code == "fetch") {
        $response->assign("fetchbox1", "style.display", '');
        $response->assign("fetchbox2", "style.display", '');
        $response->assign("fetchbox3", "style.display", '');
        $response->assign("fetchbox4", "style.display", '');
        $response->assign("fetchbox5", "style.display", '');
        $response->assign("fetchbox6", "style.display", '');
        $response->assign("fetchbox7", "style.display", '');
        $response->assign("resbox1", "style.display", 'none');
        $response->assign("resbox2", "style.display", 'none');
        $response->assign("resbox3", "style.display", 'none');
        $response->assign("resbox4", "style.display", 'none');
        $response->assign("resbox5", "style.display", 'none');
        $response->assign("resbox6", "style.display", 'none');
        $response->assign("resbox7", "style.display", 'none');
        $response->assign("peoplefree", "innerHTML", StringUtils::formatNumber($fleet->getTotalPeopleCapacity()));
        $response->assign("resfree", "innerHTML", StringUtils::formatNumber($fleet->getTotalCapacity()));
    } else {
        $response->assign("fetchbox1", "style.display", 'none');
        $response->assign("fetchbox2", "style.display", 'none');
        $response->assign("fetchbox3", "style.display", 'none');
        $response->assign("fetchbox4", "style.display", 'none');
        $response->assign("fetchbox5", "style.display", 'none');
        $response->assign("fetchbox6", "style.display", 'none');
        $response->assign("fetchbox7", "style.display", 'none');
        $response->assign("resbox1", "style.display", '');
        $response->assign("resbox2", "style.display", '');
        $response->assign("resbox3", "style.display", '');
        $response->assign("resbox4", "style.display", '');
        $response->assign("resbox5", "style.display", '');
        $response->assign("resbox6", "style.display", '');
        $response->assign("resbox7", "style.display", '');
        $response->assign('peoplefree', 'innerHTML', StringUtils::formatNumber($fleet->getPeopleCapacity()) . " / " . StringUtils::formatNumber($fleet->getTotalPeopleCapacity()));
        $response->assign('resfree', 'innerHTML', StringUtils::formatNumber($fleet->getCapacity()) . " / " . StringUtils::formatNumber($fleet->getTotalCapacity()));
    }

    if ($code == "fakeattack") {
        ob_start();
        /** @var ShipDataRepository $shipDataRepository */
        $shipDataRepository = $app[ShipDataRepository::class];
        $shipNames = $shipDataRepository->getFakeableShipNames();
        echo "<td colspan=\"3\">";
        foreach ($shipNames as $shipId => $shipName) {
            echo "<input type=\"radio\" name=\"fakeShip\" name=\"fakeShip\" value=\"" . $shipId . "\">&nbsp;" . $shipName . "<br>";
        }
        echo "</td>";
        $response->assign("fakebox", "innerHTML", ob_get_contents());
        $response->assign("fakebox", "style.display", '');
        $response->assign("fakeheader", "style.display", '');
        ob_end_clean();
    } else {
        $response->assign("fakeheader", "style.display", 'none');
        $response->assign("fakebox", "style.display", 'none');
    }
    // bugfix - check for alliance added by river
    if ($code == "alliance" && $fleet->getLeader() == 0 && $fleet->owner->alliance) {
        ob_start();
        echo "<td colspan=\"2\"><textarea name=\"message_text\" id=\"message\" rows=\"10\" cols=\"55\"></textarea></td>
            <td>";
        $allianceMemberNames = $userRepository->searchUserNicknames(UserSearch::create()->allianceId($fleet->owner->allianceId()));
        foreach ($allianceMemberNames as $userId => $userNick) {
            echo "<input type=\"checkbox\" name=\"msgUser[]\" name=\"msgUser[]\" value=\"$userId\" checked=\"checked\">&nbsp;$userNick<br>";
        }
        echo "</td>";
        $response->assign('msg', 'innerHTML', ob_get_contents());
        ob_end_clean();
        $response->assign("msgHeader", "style.display", '');
        $response->assign("msg", "style.display", '');
    } else {
        $response->assign("msgHeader", "style.display", 'none');
        $response->assign("msg", "style.display", 'none');
    }

    $response->assign('resfree', 'style.color', "#0f0");
    $response->assign('peoplefree', 'style.color', "#0f0");
    $_SESSION['haven']['fleetObj'] = serialize($fleet);

    return $response;
}

function havenAllianceAttack($id)
{
    global $app;

    /** @var FleetRepository $fleetRepository */
    $fleetRepository = $app[FleetRepository::class];

    $response = new xajaxResponse();
    /** @var FleetLaunch $fleet */
    $fleet = unserialize($_SESSION['haven']['fleetObj']);

    $percentageSpeed = 100;
    $comment = "-";
    $fleet->setSpeedPercent($percentageSpeed);

    if ($id > 0 && $fleet->getLeader() != $id) {
        $fleetObj = $fleetRepository->find($id);
        if ($fleetObj !== null) {
            if ($fleetObj->nextId == $fleet->sourceEntity->ownerAlliance()) {
                if ($fleet->checkAttNum($id)) {
                    $leaderCount = $fleetRepository->countLeaderFleets($id);
                    if ($leaderCount <= $fleet->allianceSlots) {
                        $duration = $fleet->distance / $fleet->getSpeed();    // Calculate duration
                        $duration *= 3600;    // Convert to seconds
                        $duration = ceil($duration);
                        $maxTime = $fleetObj->landTime - time() - $fleet->getTimeLaunchLand() - $fleet->duration1 - 120;

                        if ($duration < $maxTime) {
                            $percentageSpeed =  ceil(100 * $duration / $maxTime);
                            $fleet->setSpeedPercent($percentageSpeed);
                            $fleet->setLeader($id);
                            $comment = "Unterstützung des Allianzangriffes mit  Ankunft: " . date("d.m.y, H:i:s", $fleetObj->landTime);
                        } else $comment = "Der gewählte Angriff kann nicht mehr erreicht werden.";
                    } else $comment = "Am gewählten Angriff kann nicht teilgenommen werden, da die Flottenkontrolle keine weiteren Teilflotten unterstützt.";
                } else $comment = "Am gewählten Angriff kann nicht teilgenommen werden, da die Anzahl Angreifer limitiert ist.";
            } else $comment = "Der gewählte Angriff gehört nicht zu unserem Imperium";
        }
    } elseif ($fleet->getLeader() == $id) $fleet->setLeader(0);
    ob_start();
    for ($x = 100; $x > 0; $x -= 1) {
        echo "<option value=\"$x\"";
        if ($percentageSpeed == $x) echo " selected=\"selected\"";
        echo ">" . $x . "</option>\n";
    }
    $response->assign('duration_percent', 'innerHTML', ob_get_contents());
    $response->assign('speed', 'innerHTML', StringUtils::formatNumber($fleet->getSpeed()) . " AE/h");
    $response->assign('costae', 'innerHTML', StringUtils::formatNumber($fleet->getCostsPerHundredAE()) . " t " . ResourceNames::FUEL . "");
    $response->assign('duration', 'innerHTML', StringUtils::formatTimespan($fleet->getDuration()) . "");
    $response->assign('costs', 'innerHTML', StringUtils::formatNumber($fleet->getCosts()) . " t " . ResourceNames::FUEL . "");
    $response->assign('food', 'innerHTML', StringUtils::formatNumber($fleet->getCostsFood()) . " t " . ResourceNames::FOOD . "");
    $response->assign('comment', 'innerHTML', $comment);

    ob_end_clean();
    $_SESSION['haven']['fleetObj'] = serialize($fleet);

    return $response;
}

function havenCheckSupport($form)
{

    $response = new xajaxResponse();
    /** @var FleetLaunch $fleet */
    $fleet = unserialize($_SESSION['haven']['fleetObj']);
    ob_start();

    $supportTime = $form["second"] + $form["min"] * 60 + $form["hour"] * 3600;
    $maxTime = $fleet->getSupportMaxTime();

    if ($maxTime < $supportTime) {
        $supportTime = $maxTime;
        $hour = floor($maxTime / 3600);
        $temp = $maxTime - $hour * 3600;
        $minute = floor($temp / 60);
        $second = $temp - $minute * 60;

        $response->assign('hour', 'value', $hour);
        $response->assign('min', 'value', $minute);
        $response->assign('second', 'value', $second);
    }

    $fleet->setSupportTime($supportTime);

    $fuel = StringUtils::formatNumber($fleet->getCosts()) . " t " . ResourceNames::FUEL;
    $food = StringUtils::formatNumber($fleet->getCostsFood()) . " t " . ResourceNames::FOOD;

    if ($supportTime) {
        $fuel .= " (+ " . StringUtils::formatNumber($fleet->getSupportFuel()) . " t " . ResourceNames::FUEL . " Supportkosten)";
        if ($fleet->getSupportFood()) {
            $food .= " (+ " . StringUtils::formatNumber($fleet->getSupportFood()) . " t " . ResourceNames::FOOD . " Supportkosten)";
        }
    }

    $response->assign('costs', 'innerHTML', $fuel);
    $response->assign('costsFood', 'innerHTML', $food);
    $response->assign('resfree', 'innerHTML', StringUtils::formatNumber($fleet->getCapacity()) . " / " . StringUtils::formatNumber($fleet->getTotalCapacity()));
    $response->assign('resfree', 'style.color', "#0f0");

    ob_end_clean();
    $_SESSION['haven']['fleetObj'] = serialize($fleet);

    return $response;
}

function havenWormholeReset()
{
    $fleet = unserialize($_SESSION['haven']['fleetObj']);
    $fleet->unsetWormhole();
    $_SESSION['haven']['fleetObj'] = serialize($fleet);
}

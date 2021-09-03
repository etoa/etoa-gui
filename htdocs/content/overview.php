<?PHP

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceNewsRepository;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseQueueSearch;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Fleet\FleetStatus;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipQueueSearch;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemSearch;
use EtoA\Technology\TechnologyRepository;
use EtoA\Text\TextRepository;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserPropertiesRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];
/** @var PlanetRepository $planetRepository */
$planetRepository = $app[PlanetRepository::class];
/** @var TechnologyDataRepository $technologyDataRepository */
$technologyDataRepository = $app[TechnologyDataRepository::class];
/** @var TechnologyRepository $technologyRepository */
$technologyRepository = $app[TechnologyRepository::class];
/** @var BuildingDataRepository $buildingDataRepository */
$buildingDataRepository = $app[BuildingDataRepository::class];
/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];
/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];
/** @var ShipQueueRepository $shipQueueRepository */
$shipQueueRepository = $app[ShipQueueRepository::class];
/** @var DefenseDataRepository $defenseDataRepository */
$defenseDataRepository = $app[DefenseDataRepository::class];
/** @var DefenseQueueRepository $defenseQueRepository */
$defenseQueRepository = $app[DefenseQueueRepository::class];
/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];
/** @var AllianceBuildingRepository $allianceBuildingRepository */
$allianceBuildingRepository = $app[AllianceBuildingRepository::class];
/** @var AllianceTechnologyRepository $allianceTechnologyRepository */
$allianceTechnologyRepository = $app[AllianceTechnologyRepository::class];

$properties = $userPropertiesRepository->getOrCreateProperties($cu->id);

// BEGIN SKRIPT //
echo "<h1>&Uuml;bersicht</h1>";

if ($s->firstView) {
    if ($config->getBoolean("round_end")) {
        iBoxStart("Ende der Runde");
        echo "<div style=\"width:100%;text-align:center;\">Die Runde endet am <strong>" . StringUtils::formatDate($config->param1Int("round_end")) . "</strong>!";
        if ($config->param2("round_end")) {
            echo " " . $config->param2("round_end");
        }
        echo "</div>";
        iBoxEnd();
    }

    /** @var UserLoginFailureRepository $userLoginFailureRepository */
    $userLoginFailureRepository = $app[UserLoginFailureRepository::class];
    $failureCount = $userLoginFailureRepository->countLoginFailuresSince($cu->getId(), $cu->lastOnline);
    if ($failureCount > 0) {
        iBoxStart("Fehlerhafte Logins");
        echo "<div style=\"color:red;\"><b>Seit deinem letzten Login gab es " . $failureCount . " <a href=\"?page=userconfig&amp;mode=logins\">fehlerhafte Loginversuche</a>!</b></div>";
        iBoxEnd();
    }
}

if ($s->sittingActive) {
    iBoxStart("Sitting-Modus aktiv");
    echo "Du sittest diesen Account im Auftrag von " . $cu->nick . " bis " . StringUtils::formatDate($s->sittingUntil) . ".<br> Bitte beachte die speziellen <a href= '#' onclick=\"window.open('http://www.etoa.ch/regeln','rules','width=auto,height=auto,scrollbars=yes');\">Sittingregeln</a> unter §3 - (5):<br>Während eines aktiven Sittings sind Handeln und Angreifen zwischen Sitter und Gesittetem ohne Ausnahme verboten. Gemeinsame Allianzangriffe/-verteidigungen sind hingegen gestattet.";
    iBoxEnd();
}

//
// Admin-Infos
//

/** @var TextRepository $textRepo */
$textRepo = $app[TextRepository::class];

$infoText = $textRepo->find('info');
if ($infoText->isEnabled()) {
    echo '<div class="overviewInfoTextContainer">';
    iBoxStart(": Wichtige Information :");
    echo BBCodeUtils::toHTML($infoText->content);
    iBoxEnd();
    echo '</div>';
}

tableStart("Status");
echo "<tr>
                    <th style=\"width:30%;\">Rathaus</th>
                    <th style=\"width:20%;\">Eigene Flotten</th>
                    <th style=\"width:20%;\">Fremde Flotten</th>
                    <th style=\"width:30%;\">Forschung</th>
                </tr>";

//
// Rathaus
//

/** @var AllianceNewsRepository $allianceNewsRepository */
$allianceNewsRepository = $app[AllianceNewsRepository::class];
$newsCounts = $allianceNewsRepository->countNewEntriesSince($cu->allianceId(), $cu->lastOnline);
if ($newsCounts > 0) {
    echo "<tr><td><a href=\"?page=townhall\" style=\"color:#ff0\"><b>" . $newsCounts . "</b> neue Nachrichten</a></td>";
} else {
    echo "<tr><td>Keine neuen Nachrichten</td>";
}

//
// Flotten
//

//
// Eigene Flotten
//
/** @var FleetRepository $fleetRepository */
$fleetRepository = $app[FleetRepository::class];
$ownFleets = $fleetRepository->count(FleetSearch::create()->user($cu->getId()));
//Mehrere Flotten
if ($ownFleets > 1) {
    echo "<td><a href=\"?page=fleets\" style=\"color:#0f0\"><b>" . $ownFleets . "</b> eigene Flotten</a></td>";
}
//Eine Flotte
elseif ($ownFleets === 1) {
    echo "<td><a href=\"?page=fleets\" style=\"color:#0f0\"><b>" . $ownFleets . "</b> eigene Flotte</a></td>";
}
//Keine Flotten
else {
    echo "<td>Keine eigenen Flotten</td>";
}


//
// Fremde Flotten
//
$fm = new FleetManager($cu->id, $cu->allianceId);
$fm->loadForeign();
//Mehrere Flotten
if ($fm->count() > 1) {
    echo "<td><a href=\"?page=fleets\" style=\"" . $fm->attitude() . "\"><b>" . $fm->count() . "</b> fremde Flotten</a></td>";
}
//Eine Flotte
elseif ($fm->count() == 1) {
    echo "<td><a href=\"?page=fleets\" style=\"" . $fm->attitude() . "\"><b>" . $fm->count() . "</b> fremde Flotte</a></td>";
}
//Keine Flotten
else {
    echo "<td>Keine fremden Flotten</td>";
}

//
// Technologien
//
$technologyNames = $technologyDataRepository->getTechnologyNames(true);
//Lädt forschende Tech
$technologyInProgress = $technologyRepository->searchEntry(TechnologyListItemSearch::create()->userId($cu->getId())->notTechnologyId(TechnologyId::GEN)->underConstruction());
if ($technologyInProgress !== null) {
    echo "<td><a href=\"?page=research&amp;change_entity=" . $technologyInProgress->entityId . "\" id=\"tech_counter\">";
    //Forschung ist fertig
    if ($technologyInProgress->endTime - time() <= 0) {
        echo "" . $technologyNames[$technologyInProgress->technologyId] . " Fertig";
    }
    //Noch am forschen
    else {
        echo startTime($technologyInProgress->endTime - time(), 'tech_counter', 0, '' . $technologyNames[$technologyInProgress->technologyId] . ' TIME');
    }

    echo "</a></td></tr>";
} else {
    echo "<td>Es wird nirgendwo geforscht!</td></tr>";
}

//
//Gentech
//
$genTechnologyInProgress = $technologyRepository->searchEntry(TechnologyListItemSearch::create()->userId($cu->getId())->technologyId(TechnologyId::GEN)->underConstruction());
if ($genTechnologyInProgress !== null) {
    echo "
        <tr >
          <th colspan =3></th>
          <th>Gentechnik</th>
               </tr>
              <tr>";

    echo "<td colspan =3>";
    echo "<td><a href=\"?page=research&amp;change_entity=" . $genTechnologyInProgress->entityId . "\" id=\"tech_gen\">";
    //Forschung ist fertig
    if ($genTechnologyInProgress->endTime - time() <= 0) {
        echo "" . $technologyNames[$genTechnologyInProgress->technologyId] . " Fertig";
    }
    //Noch am forschen
    else {
        echo startTime($genTechnologyInProgress->endTime - time(), 'tech_gen', 0, '' . $technologyNames[$genTechnologyInProgress->technologyId] . ' TIME');
    }

    echo "</a></td></tr>";
}

//
// Allianzegebäude
//

if ($cu->allianceId != 0) {

    echo "<tr>
                            <th>Allianzgebäude</th>
                            <th>Supportflotten</th>
                            <th>Allianzangriffe</th>
                            <th>Allianzforschungen</th>
                        </tr>
                        <tr>";

    // Lädt bauende Allianzgebäude
    $allianceBuildingInProgress = $allianceBuildingRepository->getInProgress($cu->allianceId());
    if ($allianceBuildingInProgress !== null) {
        echo "<td>
                                <a href=\"?page=alliance&amp;action=base&amp;action2=buildings\" id=\"alliance_building_counter\">";

        //Forschung ist fertig
        if ($allianceBuildingInProgress['endTime'] - time() <= 0) {
            echo "" . $allianceBuildingInProgress['name'] . " Fertig";
        }
        //Noch am forschen
        else {
            echo startTime($allianceBuildingInProgress['endTime'] - time(), 'alliance_building_counter', 0, '' . $allianceBuildingInProgress['name'] . ' TIME');
        }

        echo "</a>
                            </td>";
    } else {
        echo "<td>Es wird nichts gebaut!</td>";
    }

    //
    // Supportflotten Flotten
    //
    $allianceSupportFleetCount = $fleetRepository->count(FleetSearch::create()->actionIn([\EtoA\Fleet\FleetAction::SUPPORT])->allianceId($cu->allianceId()));
    //Mehrere Flotten
    if ($allianceSupportFleetCount > 1) {
        echo "<td><a href=\"?page=fleets&mode=alliance\"><b>" . $allianceSupportFleetCount . "</b> Supportflotten</a></td>";
    }
    //Eine Flotte
    elseif ($allianceSupportFleetCount == 1) {
        echo "<td><a href=\"?page=fleets&mode=alliance\"><b>" . $allianceSupportFleetCount . "</b> Supportflotte</a></td>";
    }
    //Keine Flotten
    else {
        echo "<td>Keine Supportflotten</td>";
    }

    //
    // Allianzangriffs
    //
    $allianceAttackFleetCount = $fleetRepository->count(FleetSearch::create()->actionIn([\EtoA\Fleet\FleetAction::ALLIANCE])->nextId($cu->allianceId())->status(FleetStatus::DEPARTURE)->isLeader());
    //Mehrere Flotten
    if ($allianceAttackFleetCount > 1) {
        echo "<td><a href=\"?page=fleets&mode=alliance\"><b>" . $allianceAttackFleetCount . "</b> Allianzangriffe</a></td>";
    }
    //Eine Flotte
    elseif ($allianceAttackFleetCount === 1) {
        echo "<td><a href=\"?page=fleets&mode=alliance\"><b>" . $allianceAttackFleetCount . "</b> Allianzangriff</a></td>";
    }
    //Keine Flotten
    else {
        echo "<td>Keine Allianzangriffe</td>";
    }


    // Lädt forschende Allianztech
    $allianceTechnologyInProgress = $allianceTechnologyRepository->getInProgress($cu->allianceId());
    if ($allianceTechnologyInProgress !== null) {
        echo "<td>
                                <a href=\"?page=alliance&amp;action=base&amp;action2=research\" id=\"alliance_tech_counter\">";

        //Forschung ist fertig
        if ($allianceTechnologyInProgress['endTime'] - time() <= 0) {
            echo "" . $allianceTechnologyInProgress['name'] . " Fertig";
        }
        //Noch am forschen
        else {
            echo startTime($allianceTechnologyInProgress['endTime'] - time(), 'alliance_tech_counter', 0, '' . $allianceTechnologyInProgress['name'] . ' TIME');
        }

        echo "</a>
                            </td>";
    } else {
        echo "<td>Es wird nichts geforscht!</td>";
    }
}

echo "</tr>";

tableEnd();




//
// Javascript für dynamischen Planetkreis
//


?>
<script type="text/javascript">
    function show_info(
        planet_id,
        planet_name,
        building_name,
        building_time,
        shipyard_name,
        shipyard_time,
        defense_name,
        defense_time,
        people,
        res_metal,
        res_crystal,
        res_plastic,
        res_fuel,
        res_food,
        use_power,
        prod_power,
        store_metal,
        store_crystal,
        store_plastic,
        store_fuel,
        store_food,
        people_place) {

        //Planetinfo Anzeigen
        document.getElementById("planet_info_name").firstChild.nodeValue = planet_name;

        document.getElementById("planet_info_building_name").firstChild.nodeValue = building_name;
        document.getElementById("planet_info_building_time").firstChild.nodeValue = building_time;

        document.getElementById("planet_info_shipyard_name").firstChild.nodeValue = shipyard_name;
        document.getElementById("planet_info_shipyard_time").firstChild.nodeValue = shipyard_time;

        document.getElementById("planet_info_defense_name").firstChild.nodeValue = defense_name;
        document.getElementById("planet_info_defense_time").firstChild.nodeValue = defense_time;

        //Überprüfen ob Speicher voll ist
        var check_metal = store_metal - res_metal;
        var check_crystal = store_crystal - res_crystal;
        var check_plastic = store_plastic - res_plastic;
        var check_fuel = store_fuel - res_fuel;
        var check_food = store_food - res_food;
        var check_people = people_place - people;

        var rest_power = prod_power - use_power;

        //Wenn Speicher voll, anders darstellen als normal
        if (check_metal <= 0) {
            document.getElementById("planet_info_res_metal").className = 'resfullcolor';
        } else {
            document.getElementById("planet_info_res_metal").className = 'resmetalcolor';
        }

        if (check_crystal <= 0) {
            document.getElementById("planet_info_res_crystal").className = 'resfullcolor';
        } else {
            document.getElementById("planet_info_res_crystal").className = 'rescrystalcolor';
        }

        if (check_plastic <= 0) {
            document.getElementById("planet_info_res_plastic").className = 'resfullcolor';
        } else {
            document.getElementById("planet_info_res_plastic").className = 'resplasticcolor';
        }

        if (check_fuel <= 0) {
            document.getElementById("planet_info_res_fuel").className = 'resfullcolor';
        } else {
            document.getElementById("planet_info_res_fuel").className = 'resfuelcolor';
        }

        if (check_food <= 0) {
            document.getElementById("planet_info_res_food").className = 'resfullcolor';
        } else {
            document.getElementById("planet_info_res_food").className = 'resfoodcolor';
        }

        if (check_people <= 0) {
            document.getElementById("planet_info_people").className = 'resfullcolor';
        } else {
            document.getElementById("planet_info_people").className = 'respeoplecolor';
        }

        if (rest_power <= 0) {
            document.getElementById("planet_info_power").className = 'resfullcolor';
        } else {
            document.getElementById("planet_info_power").className = 'respowercolor';
        }


        var res_metal = format(res_metal);
        var res_crystal = format(res_crystal);
        var res_plastic = format(res_plastic);
        var res_fuel = format(res_fuel);
        var res_food = format(res_food);
        var people = format(people);
        var use_power = format(use_power);

        var store_metal = format(store_metal);
        var store_crystal = format(store_crystal);
        var store_plastic = format(store_plastic);
        var store_fuel = format(store_fuel);
        var store_food = format(store_food);
        var people_place = format(people_place);
        var prod_power = format(prod_power);

        if (rest_power >= 0) {
            var rest_power = format(rest_power);
        } else {
            var rest_power = '-' + format(Math.abs(rest_power));
        }


        //Roshtoff Anzeigen
        document.getElementById("planet_info_res_metal").firstChild.nodeValue = '' + res_metal + ' t';
        document.getElementById("planet_info_res_crystal").firstChild.nodeValue = '' + res_crystal + ' t';
        document.getElementById("planet_info_res_plastic").firstChild.nodeValue = '' + res_plastic + ' t';
        document.getElementById("planet_info_res_fuel").firstChild.nodeValue = '' + res_fuel + ' t';
        document.getElementById("planet_info_res_food").firstChild.nodeValue = '' + res_food + ' t';
        document.getElementById("planet_info_power").firstChild.nodeValue = rest_power;
        document.getElementById("planet_info_people").firstChild.nodeValue = people;


        //Alle Beschriftungen anzeigen
        document.getElementById("planet_info_text_building").innerHTML = '<a href=\"?page=buildings&change_entity=' + planet_id + '\">Bauhof:</a>';
        document.getElementById("planet_info_text_shipyard").innerHTML = '<a href=\"?page=shipyard&change_entity=' + planet_id + '\">Schiffswerft:</a>';
        document.getElementById("planet_info_text_defense").innerHTML = '<a href=\"?page=defense&change_entity=' + planet_id + '\">Waffenfabrik:</a>';
        document.getElementById("planet_info_text_res").firstChild.nodeValue = 'Ressourcen';
        document.getElementById("planet_info_text_res_metal").className = 'resmetalcolor';
        document.getElementById("planet_info_text_res_crystal").className = 'rescrystalcolor';
        document.getElementById("planet_info_text_res_plastic").className = 'resplasticcolor';
        document.getElementById("planet_info_text_res_fuel").className = 'resfuelcolor';
        document.getElementById("planet_info_text_res_food").className = 'resfoodcolor';
        document.getElementById("planet_info_text_people").className = 'respeoplecolor';
        document.getElementById("planet_info_text_power").className = 'respowercolor';
        document.getElementById("planet_info_text_res_metal").firstChild.nodeValue = '<?php echo ResourceNames::METAL . ":"; ?>';
        document.getElementById("planet_info_text_res_crystal").firstChild.nodeValue = '<?php echo ResourceNames::CRYSTAL . ":"; ?>';
        document.getElementById("planet_info_text_res_plastic").firstChild.nodeValue = '<?php echo ResourceNames::PLASTIC . ":"; ?>';
        document.getElementById("planet_info_text_res_fuel").firstChild.nodeValue = '<?php echo ResourceNames::FUEL . ":"; ?>';
        document.getElementById("planet_info_text_res_food").firstChild.nodeValue = '<?php echo ResourceNames::FOOD . ":"; ?>';
        document.getElementById("planet_info_text_people").firstChild.nodeValue = 'Bewohner:';
        document.getElementById("planet_info_text_power").firstChild.nodeValue = 'Energie:';
    }

    //Formatiert Zahlen (der PHP Skript will nicht gehen)
    function format(nummer) {
        var nummer = '' + nummer;
        var laenge = nummer.length;
        if (laenge > 3) {
            var mod = laenge % 3;
            var output = (mod > 0 ?
                (nummer.substring(0, mod)) : '');
            for (i = 0; i < Math.floor(laenge / 3); i++) {
                if ((mod == 0) && (i == 0))
                    output += nummer.substring(mod + 3 * i,
                        mod + 3 * i + 3);
                else
                    output += '`' + nummer.substring(mod + 3 * i,
                        mod + 3 * i + 3);
            }
            return (output);
        } else return nummer;
    }
</script>
<?PHP


//
// Planetkreis
//

//Kreis Definitionen
$division = 15;            //Kreis Teilung: So hoch wie die maximale Anzahl Planeten
$d_planets = $properties->planetCircleWidth;    //Durchmesser der Bilder (in Pixel)
$d_infos = $properties->planetCircleWidth;        //Durchmesser der Infos (in Pixel)
$pic_height = 75;            //Planet Bildhöhe
$pic_width = 75;            //Planet Bildbreite
$info_box_height = 50;    //Info Höhe
$info_box_width = 150;    //Info Breite
$degree = 0;                //Winkel des Startplanetes (0=Senkrecht (Oben))

$middle_left = $d_planets / 2 - $pic_height / 2;
$middle_top = $d_planets / 2 - $pic_width / 2;
$absolute_width = $d_infos + $info_box_width + $pic_width;
$absolute_height = $d_infos + $info_box_height + $pic_height;

//Abstand
echo "<br><br><br><br><br><br><br><br>";

echo "<div align=\"center\" style=\"position:relative; left:0px; top:0px; width:" . $absolute_width . "px; height:" . $absolute_height . "px; vertical-align:middle; margin: 0 auto\">
    ";

echo "<div align=\"center\" style=\"position:relative; left:0px; top:0px; width:" . $d_planets . "px; height:" . $d_planets . "px; text-align:center; vertical-align:middle;\" id=\"planet_circle_inner_container\">
    ";

//Liest alle Planeten des Besitzers aus und gibt benötigte infos
$userPlanets = $planetRepository->getUserPlanets($cu->getId());
$buildingNames = $buildingDataRepository->getBuildingNames(true);
$shipNames = $shipDataRepository->getShipNames(true);
$defenseNames = $defenseDataRepository->getDefenseNames(true);

$shipyard_rest_time = [];
$shipyard_name = [];
$shipyard_zeit = [];
$shipyard_time = [];
$defense_rest_time = [];
$defense_name = [];
$defense_zeit = [];
$defense_time = [];
foreach ($userPlanets as $userPlanet) {
    // Bauhof infos
    $buildingEntries = $buildingRepository->findForUser($cu->getId(), $userPlanet->id, time());
    if (count($buildingEntries) > 0) {
        $entry = $buildingEntries[0];

        //infos über den Bauhof
        $building_rest_time = $entry->endTime - time();
        $building_h = floor($building_rest_time / 3600);
        $building_m = floor(($building_rest_time - $building_h * 3600) / 60);
        $building_s = $building_rest_time - $building_h * 3600 - $building_m * 60;
        $building_zeit = "(" . $building_h . "h " . $building_m . "m " . $building_s . "s)";

        $building_time = $building_zeit;
        $building_name =  $buildingNames[$entry->buildingId];

        // Zeigt Ausbaulevel bei Abriss
        if ($entry->buildType == 4) {
            $building_level =  $entry->currentLevel - 1;
        }
        // Bei Ausbau
        else {
            $building_level =  $entry->currentLevel + 1;
        }

        if ($building_rest_time <= 0) {
            $building_time = "Fertig";
        }
    } else {
        $building_time = "";
        $building_rest_time = "";
        $building_name = "";
        $building_level = "";
    }


    // Schiffswerft infos
    $queueEntries = $shipQueueRepository->searchQueueItems(ShipQueueSearch::create()->entityId($userPlanet->id)->endAfter(time()), 1);
    if (count($queueEntries) > 0) {
        $queueItem = $queueEntries[0];

        //Verbleibende Zeit bis zur fertigstellung des aktuellen Auftrages
        $shipyard_rest_time[$userPlanet->id] = $queueItem->endTime - time();
        //Schiffsname
        $shipyard_name[$userPlanet->id] =  $shipNames[$queueItem->shipId];

        //infos über den raumschiffswerft
        $shipyard_h = floor($shipyard_rest_time[$userPlanet->id] / 3600);
        $shipyard_m = floor(($shipyard_rest_time[$userPlanet->id] - $shipyard_h * 3600) / 60);
        $shipyard_s = $shipyard_rest_time[$userPlanet->id] - $shipyard_h * 3600 - $shipyard_m * 60;
        $shipyard_zeit[$userPlanet->id] = "(" . $shipyard_h . "h " . $shipyard_m . "m " . $shipyard_s . "s)";

        $shipyard_time[$userPlanet->id] = $shipyard_zeit[$userPlanet->id];
        if ($shipyard_rest_time[$userPlanet->id] <= 0) {
            $shipyard_time[$userPlanet->id] = "Fertig";
        }
    } else {
        $shipyard_time[$userPlanet->id] = "";
        $shipyard_name[$userPlanet->id] = "";
    }

    // waffenfabrik infos
    $queueEntries = $defenseQueRepository->searchQueueItems(DefenseQueueSearch::create()->entityId($userPlanet->id)->endAfter(time()), 1);
    if (count($queueEntries) > 0) {
        $queueItem = $queueEntries[0];

        //Verbleibende Zeit bis zur fertigstellung des aktuellen Auftrages
        $defense_rest_time[$userPlanet->id] = $queueItem->endTime - time();
        //Defname
        $defense_name[$userPlanet->id] = $defenseNames[$queueItem->defenseId];

        // Infos über die Waffenfabrik
        $defense_h = floor($defense_rest_time[$userPlanet->id] / 3600);
        $defense_m = floor(($defense_rest_time[$userPlanet->id] - $defense_h * 3600) / 60);
        $defense_s = $defense_rest_time[$userPlanet->id] - $defense_h * 3600 - $defense_m * 60;
        $defense_zeit[$userPlanet->id] = "(" . $defense_h . "h " . $defense_m . "m " . $defense_s . "s)";

        $defense_time[$userPlanet->id] = $defense_zeit[$userPlanet->id];
        if ($defense_rest_time[$userPlanet->id] <= 0) {
            $defense_time[$userPlanet->id] = "Fertig";
        }
    } else {
        $defense_time[$userPlanet->id] = "";
        $defense_name[$userPlanet->id] = "";
    }

    $planet_info = "<b class=\"planet_name\">" . StringUtils::encodeDBStringToPlaintext($userPlanet->displayName()) . "</b><br>
            " . $building_name . " " . $building_level . "
            ";
    $planet_image_path = "" . IMAGE_PATH . "/" . IMAGE_PLANET_DIR . "/planet" . $userPlanet->image . "_middle.gif";

    // Planet bild mit link zum bauhof und der informationen übergabe beim mouseover
    $planet_link = "<a href=\"?page=buildings&change_entity=" . $userPlanet->id . "\"><img id=\"Planet\" src=\"" . $planet_image_path . "\" width=\"" . $pic_width . "\" height=\"" . $pic_height . "\" border=\"0\"
        onMouseOver=\"show_info(
            '" . $userPlanet->id . "',
            '" . StringUtils::encodeDBStringToJS($userPlanet->displayName()) . "',
            '" . $building_name . "',
            '" . $building_time . "',
            '" . $shipyard_name[$userPlanet->id] . "',
            '" . $shipyard_time[$userPlanet->id] . "',
            '" . $defense_name[$userPlanet->id] . "',
            '" . $defense_time[$userPlanet->id] . "',
            '" . floor($userPlanet->people) . "',
            '" . floor($userPlanet->resMetal) . "',
            '" . floor($userPlanet->resCrystal) . "',
            '" . floor($userPlanet->resPlastic) . "',
            '" . floor($userPlanet->resFuel) . "',
            '" . floor($userPlanet->resFood) . "',
            '" . floor($userPlanet->usePower) . "',
            '" . floor($userPlanet->prodPower) . "',
            '" . floor($userPlanet->storeMetal) . "',
            '" . floor($userPlanet->storeCrystal) . "',
            '" . floor($userPlanet->storePlastic) . "',
            '" . floor($userPlanet->storeFuel) . "',
            '" . floor($userPlanet->storeFood) . "',
            '" . floor($userPlanet->peoplePlace) . "'
            );\"/></a>
            ";


    if ($degree == 0)
        $text = "center";
    elseif ($degree > 0 && $degree <= 180)
        $text = "left";
    else
        $text = "right";

    $left2 = $middle_left + (($d_planets / 2) * cos(deg2rad($degree + 270)));
    $top2 = $middle_top + (($d_planets / 2) * sin(deg2rad($degree + 270)));

    echo "<div style=\"position:absolute; left:" . $left2 . "px; top:" . $top2 . "px; text-align:center; vertical-align:middle;\">" . $planet_link . "</div>
            ";

    if ($degree == 0) {
        $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) - ($info_box_width - $pic_width) / 2;
        $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270))) - $info_box_height;
    } elseif ($degree > 0 && $degree <= 45) {
        $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) + $pic_width;
        $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270))) - $pic_height / 2;
    } elseif ($degree > 45 && $degree < 135) {
        $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) + $pic_width;
        $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270)));
    } elseif ($degree >= 135 && $degree < 160) {
        $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) + $pic_width;
        $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270))) + $pic_height / 2;
    } elseif ($degree >= 160 && $degree < 180) {
        $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) + 15;
        $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270))) + $pic_height;
    } elseif ($degree >= 180 && $degree <= 210) {
        $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) - ($info_box_width + 15 - $pic_width);
        $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270))) + $pic_height;
    } elseif ($degree > 210 && $degree <= 225) {
        $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) - $pic_width - ($info_box_width - $pic_width);
        $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270))) + $pic_height / 2;
    } elseif ($degree > 225 && $degree < 315) {
        $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) - $pic_width - ($info_box_width - $pic_width);
        $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270)));
    } else //315<$degree<360
    {
        $left = $middle_left + (($d_infos / 2) * cos(deg2rad($degree + 270))) - $pic_width - ($info_box_width - $pic_width);
        $top = $middle_top + (($d_infos / 2) * sin(deg2rad($degree + 270))) - $pic_height / 2;
    }

    echo "<div id=\"planet_info_" . $userPlanet->id . "\" style=\"position:absolute; left:" . $left . "px; top:" . $top . "px; width:" . $info_box_width . "px; height:" . $info_box_height . "px; text-align:" . $text . "; vertical-align:middle;\">
            ";

    echo $planet_info;
    echo '<span id="planet_timer_' . $userPlanet->id . '">';

    // Stellt Zeit Counter dar, wenn ein Gebäude in bau ist
    if ($building_rest_time > 0) {
        echo startTime($building_rest_time, "planet_timer_" . $userPlanet->id . "", 0, "<br>(TIME)") . "";
    }

    echo "</span></div>
            ";
    $degree = $degree + (360 / $division);
}


$top_table = $middle_top + (($d_planets / 2) * sin(deg2rad(55 + 270)));
echo "<table border=\"0\" width=\"65%\" style=\"text-align:center; vertical-align:middle;margin: 0 auto\">";
echo "
            <tr height=\"" . $top_table . "\">
                <td colspan=\"3\">&nbsp;</td>
            </tr>
            <tr>
                <td class=\"PlaniTextCenterPlanetname\" id=\"planet_info_name\" colspan=\"3\" style=\"text-align:center;\">&nbsp;</td>
            </tr>

            <tr>
                <td colspan=\"3\">&nbsp;</td>
            </tr>

            <tr>
                <td width=\"38%\" class=\"PlaniTextCenterBeschreibung\"><div id=\"planet_info_text_building\">&nbsp;</div></td>
                <td width=\"2%\">&nbsp;</td>
                <td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_building_name\">&nbsp;</td>
            </tr>
            <tr>
                <td width=\"38%\">&nbsp;</td>
                <td width=\"2%\">&nbsp;</td>
                <td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_building_time\">&nbsp;</td></tr>

            <tr>
                <td width=\"38%\" class=\"PlaniTextCenterBeschreibung\"><div id=\"planet_info_text_shipyard\">&nbsp;</div></td>
                <td width=\"2%\">&nbsp;</td>
                <td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_shipyard_name\">&nbsp;</td>
            </tr>
            <tr>
                <td width=\"38%\">&nbsp;</td>
                <td width=\"2%\">&nbsp;</td>
                <td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_shipyard_time\">&nbsp;</td>
            </tr>

            <tr>
                <td width=\"38%\" class=\"PlaniTextCenterBeschreibung\"><div id=\"planet_info_text_defense\">&nbsp;</div></td>
                <td width=\"2%\">&nbsp;</td>
                <td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_defense_name\">&nbsp;</td>
            </tr>
            <tr>
                <td width=\"38%\">&nbsp;</td>
                <td width=\"2%\">&nbsp;</td>
                <td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_defense_time\">&nbsp;</td>
            </tr>

            <tr height=\"10\">
                <td colspan=\"3\">&nbsp;</td>
            </tr>
            <tr>
                <td colspan=\"3\" class=\"PlaniTextCenterRessourcen\" id=\"planet_info_text_res\">&nbsp;</td>
            </tr>
            <tr>
                <td width=\"28%\" id=\"planet_info_text_res_metal\" >&nbsp;</td>
                <td width=\"2%\">&nbsp;</td>
                <td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_metal\">&nbsp;</td>
            </tr>
            <tr>
                <td width=\"38%\" id=\"planet_info_text_res_crystal\" >&nbsp;</td>
                <td width=\"2%\">&nbsp;</td>
                <td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_crystal\">&nbsp;</td>
            </tr>
            <tr>
                <td width=\"38%\" id=\"planet_info_text_res_plastic\" >&nbsp;</td>
                <td width=\"2%\">&nbsp;</td>
                <td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_plastic\">&nbsp;</td>
            </tr>
            <tr>
                <td width=\"38%\" id=\"planet_info_text_res_fuel\" >&nbsp;</td>
                <td width=\"2%\">&nbsp;</td>
                <td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_fuel\">&nbsp;</td>
            </tr>
            <tr>
                <td width=\"38%\" id=\"planet_info_text_res_food\" >&nbsp;</td>
                <td width=\"2%\">&nbsp;</td>
                <td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_food\">&nbsp;</td>
            </tr>

            <tr>
                <td width=\"38%\" id=\"planet_info_text_people\" >&nbsp;</td>
                <td width=\"2%\">&nbsp;</td>
                <td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_people\">&nbsp;</td>
            </tr>
            <tr>
                <td width=\"38%\" id=\"planet_info_text_power\" >&nbsp;</td>
                <td width=\"2%\">&nbsp;</td>
                <td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_power\">&nbsp;</td>
            </tr>

    </table>";

echo "</div></div>
";

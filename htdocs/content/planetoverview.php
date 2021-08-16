<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Fleet\FleetRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use Symfony\Component\HttpFoundation\Request;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var PlanetService $planetService */
$planetService = $app[PlanetService::class];

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var EntityRepository $entityRepository */
$entityRepository = $app[EntityRepository::class];

/** @var ShipRepository $shipRepo */
$shipRepo = $app[ShipRepository::class];

/** @var FleetRepository $fleetRepo */
$fleetRepo = $app[FleetRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

/** @var ResourceBoxDrawer $resourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];

/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];
/** @var DefenseRepository $defenseRepository */
$defenseRepository = $app[DefenseRepository::class];
/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];
/** @var BuildingDataRepository $buildingDataRepository */
$buildingDataRepository = $app[BuildingDataRepository::class];
/** @var TechnologyDataRepository $techDataRepository */
$techDataRepository = $app[TechnologyDataRepository::class];
/** @var TechnologyRepository $techRepository */
$techRepository = $app[TechnologyRepository::class];
/** @var DefenseDataRepository $defenseDataRepository */
$defenseDataRepository = $app[DefenseDataRepository::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var ?Planet $cp - The current Planet */
/** @var User $cu - The current User */

if (isset($cp)) {
    $planet = $planetRepo->find($cp->id);
    $techNames = $techDataRepository->getTechnologyNames(true);
    $techlist = $techRepository->getTechnologyLevels($cu->getId());

    // Kolonie aufgeben
    if ($request->query->has('action') && $request->query->get('action') == "remove") {
        if (!$planet->mainPlanet) {
            echo "<h2>:: Kolonie auf diesem Planeten aufheben ::</h2>";

            $threshold = $planet->userChanged + COLONY_DELETE_THRESHOLD;
            if ($threshold < time()) {
                echo "<form action=\"?page=$page\" method=\"POST\">";
                iBoxStart("Sicherheitsabfrage");
                echo "Willst du die Kolonie auf dem Planeten <b>" . $planet->name . "</b> wirklich löschen?";
                iBoxEnd();
                echo "<input type=\"submit\" name=\"submit_noremove\" value=\"Nein, Vorgang abbrechen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit_remove\" value=\"Ja, die Kolonie soll aufgehoben werden\">";
                echo "</form>";
            } else {
                echo "Die Kolonie kann wegen eines kürzlich stattgefundenen Besitzerwechsels<br/>
                erst ab <b>" . df($threshold) . "</b> gelöscht werden!<br/><br/>
                <input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=$page'\" />";
            }
        } else {
            error_msg("Dies ist ein Hauptplanet! Hauptplaneten können nicht aufgegeben werden!");
        }
    }

    // Kolonie aufheben ausführen
    elseif ($request->request->get('submit_remove', '') != '') {
        if (!$planet->mainPlanet) {
            $threshold = $planet->userChanged + COLONY_DELETE_THRESHOLD;
            if ($threshold < time()) {
                if (!$shipRepo->hasShipsOnEntity($planet->id)) {
                    if (!$fleetRepo->hasFleetsRelatedToEntity($planet->id)) {
                        if ($cu->id == $planet->userId) {
                            $planetService->reset($planet->id);

                            $mainPlanetId = $planetRepo->getUserMainId($cu->id);

                            echo "<br>Die Kolonie wurde aufgehoben!<br>";
                            echo "<a href=\"?page=overview&planet_id=" . $mainPlanetId . "\">Zur Übersicht</a>";

                            $planet = null;
                        } else {
                            error_msg("Der Planet ist aktuell nicht ausgewählt oder er gehört nicht dir!");
                        }
                    } else {
                        error_msg("Kolonie kann nicht gelöscht werden da Schiffe von/zu diesem Planeten unterwegs sind!");
                    }
                } else {
                    error_msg("Kolonie kann nicht gelöscht werden da noch Schiffe auf dem Planeten stationiert sind oder Schiffe noch im Bau sind!");
                }
            } else {
                echo "Die Kolonie kann wegen eines kürzlich stattgefundenen Besitzerwechsels<br/>
                erst ab <b>" . df($threshold) . "</b> gelöscht werden!<br/><br/>
                <input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=$page'\" />";
            }
        } else {
            error_msg("Dies ist ein Hauptplanet! Hauptplaneten können nicht aufgegeben werden!");
        }
    }
    // Kolonie zum Hauptplaneten machen
    if ($request->query->has('action') && $request->query->get('action') == "change_main") {
        if (!$planet->mainPlanet) {
            if (!$cu->changedMainPlanet()) {
                echo "<h2>:: Kolonie zum Hauptplaneten machen ::</h2>";

                $threshold = $planet->userChanged + COLONY_DELETE_THRESHOLD;
                if ($threshold < time()) {
                    echo "<form action=\"?page=$page\" method=\"POST\">";
                    iBoxStart("Sicherheitsabfrage");
                    echo "Willst du die Kolonie auf dem Planeten <b>" . $planet->name . "</b> wirklich zu deinem Hauptplaneten machen?<br>"
                        . "Du kannst deinen Hauptplaneten nur ein einziges Mal ändern.";
                    iBoxEnd();
                    echo "<input type=\"submit\" name=\"submit_nochange_main\" value=\"Nein, Vorgang abbrechen\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"submit_change_main\" value=\"Ja, Hauptplanet wechseln\">";
                    echo "</form>";
                } else {
                    echo "Die Kolonie kann wegen eines kürzlich stattgefundenen Besitzerwechsels<br/>
                    erst ab <b>" . df($threshold) . "</b> zu deinem Hauptplaneten gemacht werden!<br/><br/>
                    <input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=$page'\" />";
                }
            } else {
                error_msg("Du kannst deinen Hauptplaneten nur ein Mal ändern!");
            }
        } else {
            error_msg("Dies ist bereits dein Hauptplanet!");
        }
    }

    // Kolonie zum Hauptplaneten machen ausführen
    elseif ($request->request->get('submit_change_main', '') != '') {
        if (!$planet->mainPlanet) {
            $threshold = $planet->userChanged + COLONY_DELETE_THRESHOLD;
            if ($threshold < time()) {
                if (!$cu->changedMainPlanet()) {
                    if ($planetRepo->setMain($planet->id, $cu->id)) {
                        $entity = $entityRepository->findIncludeCell($planet->id);
                        $userRepository->markMainPlanetChanged($cu->getId());
                        $cu->changedMainPlanet = true;

                        /** @var UserService $userService */
                        $userService = $app[UserService::class];
                        $userService->addToUserLog($cu->id, "planets", "{nick} wählt [b]" . $entity->toString() . "[/b] als neuen Hauptplanet aus.", false);

                        echo "<br><b>" . $planet->name . "</b> ist nun dein Hauptplanet!<br/><br/>
                        <input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=$page'\" />";
                    } else {
                        error_msg("Beim Aufheben der Kolonie trat ein Fehler auf! Bitte wende dich an einen Game-Admin!");
                    }
                } else {
                    error_msg("Du kannst deinen Hauptplaneten nur ein Mal ändern!");
                }
            } else {
                echo "Die Kolonie kann wegen eines kürzlich stattgefundenen Besitzerwechsels<br/>
                erst ab <b>" . df($threshold) . "</b> zu deinem Hauptplaneten gemacht werden!<br/><br/>
                <input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=$page'\" />";
            }
        } else {
            error_msg("Dies ist bereits ein Hauptplanet!");
        }
    }

    //
    // Planeteninfo anzeigen
    //
    elseif (isset($planet)) {
        if ($request->request->get('submit_change', '') != '') {
            if ($request->request->get('planet_name', '') != '') {
                $planetRepo->setNameAndComment(
                    $planet->id,
                    stripBBCode($request->request->get('planet_name')),
                    $request->request->get('planet_desc')
                );
                if ($request->request->get('planet_name') !== $planet->name) {
                    $app['dispatcher']->dispatch(new \EtoA\Planet\Event\PlanetRename(), \EtoA\Planet\Event\PlanetRename::RENAME_SUCCESS);
                }
                $planet = $planetRepo->find($planet->id);
            }
        }

        echo "<h1>Übersicht über den Planeten " . $planet->name . "</h1>";
        echo $resourceBoxDrawer->getHTML($planet);

        if ($request->query->has('sub') && $request->query->get('sub') == "ships") {
            $sub = "ships";
        } elseif ($request->query->has('sub') && $request->query->get('sub') == "defense") {
            $sub = "defense";
        } elseif ($request->query->has('sub') && $request->query->get('sub') == "fields") {
            $sub = "fields";
        } elseif ($request->query->has('sub') && $request->query->get('sub') == "name") {
            $sub = "name";
        } else {
            $sub = "";
        }
        echo "<script type=\"text/javascript\">
        // Wechselt zwischen den Verschiedenen Tabs
        function showTab(idx)
        {
            document.getElementById('tabOverview').style.display='none';
            document.getElementById('tabName').style.display='none';
            document.getElementById('tabFields').style.display='none';
            document.getElementById('tabShips').style.display='none';
            document.getElementById('tabDefense').style.display='none';
            if (document.getElementById(idx))
                document.getElementById(idx).style.display='';
        }
        </script>";
        $ddm = new DropdownMenu(1);
        $ddm->add('b', 'Übersicht', "showTab('tabOverview');");
        $ddm->add('n', 'Name &amp; Beschreibung', "showTab('tabName');");
        $ddm->add('r', 'Felder', "showTab('tabFields');");
        $ddm->add('f', 'Schiffe', "showTab('tabShips');");
        $ddm->add('d', 'Verteidigung', "showTab('tabDefense');");
        echo $ddm;

        echo "<div id=\"tabOverview\" style=\"" . ($sub == "" ? '' : 'display:none') . "\">";

        iBoxStart("Übersicht");
        echo "<div style=\"position:relative;height:320px;padding:0px;background:#000 url('images/stars_middle.jpg');\">
        <div style=\"position:absolute;right:20px;top:20px;\">
        <img src=\"" . $planetService->imagePath($planet, 'b') . "\" style=\"width:220px;height:220px;\" alt=\"Planet\" /></div>";
        echo "<div class=\"planetOverviewName\"><a href=\"javascript:;\" onclick=\"showTab('tabName')\">" . $planet->name . "</a></div>";
        echo "<div class=\"planetOverviewList\">
        <div class=\"planetOverviewItem\">Grösse</div> " . nf($config->getInt('field_squarekm') * $planet->fields) . " km&sup2;<br style=\"clear:left;\"/>
        <div class=\"planetOverviewItem\">Temperatur</div>	" . $planet->tempFrom . " &deg;C bis " . $planet->tempTo . " &deg;C <br style=\"clear:left;\"/>
        <div class=\"planetOverviewItem\">System</div> <a href=\"?page=cell&amp;id=" . $cp->cellId() . "&amp;hl=" . $planet->id . "\">" . $cp->getSectorSolsys() . "</a> (Position " . $cp->pos . ")<br style=\"clear:left;\"/>
        <div class=\"planetOverviewItem\">Kennung</div> <a href=\"?page=entity&amp;id=" . $planet->id . "\">" . $planet->id . "</a><br style=\"clear:left;\"/>
        <div class=\"planetOverviewItem\">Stern</div> " . helpLink("stars", $cp->starTypeName) . "<br style=\"clear:left;\"/>
        <div class=\"planetOverviewItem\">Planetentyp</div> " . helpLink("planets", $cp->type()) . "<br style=\"clear:left;\"/>
        <div class=\"planetOverviewItem\">Felder</div> <a href=\"javascript:;\" onclick=\"showTab('tabFields')\">" . nf($planet->fieldsUsed) . " von " . (nf($planet->fields)) . " benutzt</a> (" . round($planet->fieldsUsed / $planet->fields * 100) . "%)<br style=\"clear:left;\"/>";
        if ($planet->hasDebrisField()) {
            echo "<div class=\"planetOverviewItem\">Trümmerfeld</div>
            <span class=\"resmetal\">" . nf($planet->wfMetal, 0, 1) . "</span>
            <span class=\"rescrystal\">" . nf($planet->wfCrystal, 0, 1) . "</span>
            <span class=\"resplastic\">" . nf($planet->wfPlastic, 0, 1) . "</span>
            <br style=\"clear:left;\"/>";
        }
        if (filled($planet->description)) {
            if (strlen($planet->description) > 90) {
                echo "<div class=\"planetOverviewItem\">Beschreibung</div><span " . mTT('Beschreibung', $planet->description) . "> " . substr($planet->description, 0, 90) . " ...</span><br style=\"clear:left;\"/>";
            } else {
                echo "<div class=\"planetOverviewItem\">Beschreibung</div> " . $planet->description . "<br style=\"clear:left;\"/>";
            }
        }
        if ($planet->mainPlanet) {
            echo "<div class=\"planetOverviewItem\">Hauptplanet</div> Dies ist dein Hauptplanet. Hauptplaneten können nicht invasiert oder aufgegeben werden!<br style=\"clear:left;\"/>";
        }
        echo "</div>";
        echo "</div>";
        iBoxEnd();
        echo "</div>";


        echo "<div id=\"tabName\" style=\"" . ($sub == "name" ? '' : 'display:none;') . "\">";
        echo "<form action=\"?page=$page\" method=\"POST\" style=\"text-align:center;\">";
        tableStart("Name und Beschreibung ändern:");
        echo "<tr><th>Name:</th><td>
        <input type=\"text\" name=\"planet_name\" id=\"planet_name\" value=\"" . ($planet->name) . "\" length=\"25\" maxlength=\"15\" />
        &nbsp; <a href=\"javascript:;\" onclick=\"generatePlanetName('planet_name');\">Name generieren</a></td></tr>";
        echo "<tr><th>Beschreibung:</th><td><textarea name=\"planet_desc\" rows=\"2\" cols=\"30\">" . ($cp->getNoBrDesc()) . "</textarea></td></tr>";
        tableEnd();
        echo "<input type=\"submit\" name=\"submit_change\" value=\"Speichern\" /> &nbsp; ";
        echo "</form>";
        echo "</div>";


        //
        // Felder
        //

        echo "<div id=\"tabFields\" style=\"" . ($sub == "fields" ? '' : 'display:none;') . "\">";
        tableStart("Felderbelegung");
        echo "<tr>
        <tr><td colspan=\"2\">
        <img src=\"misc/progress.image.php?r=1&w=650&p=" . round($planet->fieldsUsed / $planet->fields * 100) . "\" alt=\"progress\" style=\"width:100%;\"/>
        <br/>Benutzt: " . $planet->fieldsUsed . ", Total: " . nf($planet->fields) . " = " . nf($cp->fieldsBase) . " Basisfelder + " . nf($planet->fieldsExtra) . " zusätzliche Felder<br/></td></tr>
        <tr><td style=\"width:50%;vertical-align:top;padding:5px;\">";
        tableStart("Gebäude", '100%');

        $buildingLevels = $buildingRepository->getBuildingLevels($planet->id);
        if (count($buildingLevels) > 0) {
            $buildings = $buildingDataRepository->getBuildings();
            $fcnt = 0;
            echo "<tr>
                <th>Name</th>
                <th>Stufe</th>
                <th>Felder</th></tr>";
            foreach ($buildingLevels as $buildingId => $buildingLevel) {
                $building = $buildings[$buildingId];
                echo "<tr><th>" . $building->name . "</th>";
                echo "<td>" . $buildingLevel . "</td>";
                echo "<td>" . nf($buildingLevel * $building->fields) . "</td></tr>";
                $fcnt += $buildingLevel * $building->fields;
            }
            unset($v);
            echo "<tr><th colspan=\"2\">Total</th><td>" . nf($fcnt) . "</td></tr>";
        } else
            echo "<tr><td><i>Keine Gebäude vorhanden!</i></td></tr>";
        tableEnd();

        echo "</td><td style=\"width:50%;vertical-align:top;padding:5px;\">";
        tableStart("Verteidigungsanlagen", '100%');
        $defenseCounts = $defenseRepository->getEntityDefenseCounts($cu->getId(), $planet->id);
        $defenses = $defenseDataRepository->getAllDefenses();
        if (count($defenseCounts) > 0) {
            $dfcnt = 0;
            echo "<tr><th>Name</th><th>Anzahl</th><th>Felder</th></tr>";
            foreach ($defenseCounts as $defenseId => $count) {
                echo "<tr><th>" . $defenses[$defenseId]->name . "</th>";
                echo "<td>" . $count . "</td>";
                echo "<td>" . nf($count * $defenses[$defenseId]->fields) . "</td></tr>";
                $dfcnt += $count * $defenses[$defenseId]->fields;
            }

            echo "<tr><th colspan=\"2\">Total</th><td>" . nf($dfcnt) . "</td></tr>";
        } else
            echo "<tr><td><i>Keine Verteidigungsanlagen vorhanden!</i></td></tr>";
        tableEnd();
        echo "</table>";
        echo "</div>";

        //
        // Schiffe
        //

        $bonusStructure = 0;
        $bonusShield = 0;
        $bonusWeapon = 0;
        $bonusHeal = 0;

        echo "<div id=\"tabShips\" style=\"" . ($sub == "ships" ? '' : 'display:none;') . "\">";
        tableStart("Kampfstärke");
        $shipCounts = $shipRepo->getEntityShipCounts($cu->getId(), $planet->id);
        if (count($shipCounts) > 0) {
            $ships = $shipDataRepository->getAllShips(true);
            $shield_tech_level = $techlist[SHIELD_TECH_ID] ?? 0;
            $shield_tech_a = 1 + ($shield_tech_level / 10);

            $structure_tech_level = $techlist[STRUCTURE_TECH_ID] ?? 0;
            $structure_tech_a = 1 + ($structure_tech_level / 10);

            $weapon_tech_level = $techlist[WEAPON_TECH_ID] ?? 0;
            $weapon_tech_a = 1 + ($weapon_tech_level / 10);

            $heal_tech_level = $techlist[REGENA_TECH_ID] ?? 0;
            $heal_tech_a = 1 + ($heal_tech_level / 10);

            $totalStructure = 0;
            $totalShield = 0;
            $totalWeapon = 0;
            $totalHeal = 0;
            foreach ($shipCounts as $shipId => $shipCount) {
                $totalStructure += $shipCount + $ships[$shipId]->structure;
                $bonusStructure += $shipCount + $ships[$shipId]->specialBonusStructure;
                $totalShield += $shipCount + $ships[$shipId]->shield;
                $bonusShield += $shipCount + $ships[$shipId]->specialBonusShield;
                $totalWeapon += $shipCount + $ships[$shipId]->weapon;
                $bonusWeapon += $shipCount + $ships[$shipId]->specialBonusWeapon;
                $totalWeapon += $shipCount + $ships[$shipId]->weapon;
                $bonusWeapon += $shipCount + $ships[$shipId]->specialBonusWeapon;
                $totalHeal += $shipCount + $ships[$shipId]->heal;
                $bonusHeal += $shipCount + $ships[$shipId]->specialBonusHeal;
            }

            echo "<tr><th><b>Einheit</b></th><th>Grundwerte</th><th>Aktuelle Werte</th></tr>";
            echo "<tr>
                    <td><b>Struktur:</b></td>
                    <td>" . nf($totalStructure) . "</td>
                    <td>" . nf($totalStructure * ($structure_tech_a + $bonusStructure));
            if ($structure_tech_a > 1) {
                echo " (" . get_percent_string($structure_tech_a, 1) . " durch " . $techNames[STRUCTURE_TECH_ID] . " " . $structure_tech_level;
                if ($bonusStructure > 0)
                    echo ", " . get_percent_string((1 + $bonusStructure), 1) . " durch Spezialschiffe";
                echo ")";
            }
            echo "</td></tr>";
            echo "<tr><td><b>Schilder:</b></td>
                    <td>" . nf($totalShield) . "</td>
                    <td>" . nf($totalShield * ($shield_tech_a + $bonusShield));
            if ($shield_tech_a > 1) {
                echo " (" . get_percent_string($shield_tech_a, 1) . " durch " . $techNames[SHIELD_TECH_ID] . " " . $shield_tech_level;
                if ($bonusShield > 0)
                    echo ", " . get_percent_string((1 + $bonusShield), 1) . " durch Spezialschiffe";
                echo ")";
            }
            echo "</td></tr>";
            echo "<tr><td><b>Waffen:</b></td>
                    <td>" . nf($totalWeapon) . "</td>
                    <td>" . nf($totalWeapon * ($weapon_tech_a + $bonusWeapon));
            if ($weapon_tech_a > 1) {
                echo " (" . get_percent_string($weapon_tech_a, 1) . " durch " . $techNames[WEAPON_TECH_ID] . " " . $weapon_tech_level;
                if ($bonusWeapon > 0)
                    echo ", " . get_percent_string((1 + $bonusWeapon), 1) . " durch Spezialschiffe";
                echo ")";
            }
            echo "</td></tr>";
            echo "<tr><td><b>Reparatur:</b></td>
                    <td>" . nf($totalHeal) . "</td>
                    <td>" . nf($totalHeal * ($heal_tech_a + $bonusHeal));
            if ($heal_tech_a > 1) {
                echo " (" . get_percent_string($heal_tech_a, 1) . " durch " . $techNames[REGENA_TECH_ID] . " " . $heal_tech_level;
                if ($bonusHeal > 0)
                    echo ", " . get_percent_string((1 + $bonusHeal), 1) . " durch Spezialschiffe";
                echo ")";
            }
            echo "</td></tr>";
            echo "<tr><td><b>Anzahl Schiffe:</b></td>
            <td colspan=\"2\">" . nf(array_sum($shipCounts)) . "</td></tr>";
        } else {
            echo "<tr><td><i>Keine Schiffe vorhanden!</i></td></tr>";
        }
        tableEnd();

        tableStart("Details");
        echo "<tr><th>Typ</th><th>Anzahl</th><th>Eingebunkert</th></tr>";
        $shipNames = $shipDataRepository->getShipNames(true);
        $bunkerCounts = $shipRepo->getBunkeredCount($cu->getId(), $planet->id);
        foreach (array_unique(array_merge(array_keys($bunkerCounts), array_keys($shipCounts))) as $shipId) {
            echo "<tr>
                <td>" . $shipNames[$shipId] . "</td>
                <td>" . nf($shipCounts[$shipId] ?? 0) . "</td>
                <td>" . nf($bunkerCounts[$shipId] ?? 0) . "</td>
                </tr>";
        }
        unset($v);
        tableEnd();
        echo "</div>";

        //
        // Defense overview
        //

        echo "<div id=\"tabDefense\" style=\"" . ($sub == "defense" ? '' : 'display:none;') . "\">";
        tableStart("Kampfstärke");
        if (count($defenseCounts) > 0) {
            $shield_tech_level = $techlist[SHIELD_TECH_ID] ?? 0;
            $shield_tech_a = 1 + ($shield_tech_level / 10);

            $structure_tech_level = $techlist[STRUCTURE_TECH_ID] ?? 0;
            $structure_tech_a = 1 + ($structure_tech_level / 10);

            $weapon_tech_level = $techlist[WEAPON_TECH_ID] ?? 0;
            $weapon_tech_a = 1 + ($weapon_tech_level / 10);

            $heal_tech_level = $techlist[REGENA_TECH_ID] ?? 0;
            $heal_tech_a = 1 + ($heal_tech_level / 10);

            $totalStructure = 0;
            $totalShield = 0;
            $totalWeapon = 0;
            $totalHeal = 0;
            foreach ($defenseCounts as $defenseId => $defenseCount) {
                $totalStructure += $defenseCount + $defenses[$defenseId]->structure;
                $totalShield += $defenseCount + $defenses[$defenseId]->shield;
                $totalWeapon += $defenseCount + $defenses[$defenseId]->weapon;
                $totalWeapon += $defenseCount + $defenses[$defenseId]->weapon;
                $totalHeal += $defenseCount + $defenses[$defenseId]->heal;
            }

            echo "<tr><th><b>Einheit</b></th><th>Grundwerte</th><th>Aktuelle Werte</th></tr>";
            echo "<tr>
                    <td><b>Struktur:</b></td>
                    <td>" . nf($totalStructure) . "</td>
                    <td>" . nf($totalStructure * ($structure_tech_a + $bonusStructure));
            if ($structure_tech_a > 1) {
                echo " (" . get_percent_string($structure_tech_a, 1) . " durch " . $techNames[STRUCTURE_TECH_ID] . " " . $structure_tech_level;
                if ($bonusStructure > 0)
                    echo ", " . get_percent_string((1 + $bonusStructure), 1) . " durch Spezialschiffe";
                echo ")";
            }
            echo "</td></tr>";
            echo "<tr><td><b>Schilder:</b></td>
                    <td>" . nf($totalShield) . "</td>
                    <td>" . nf($totalShield * ($shield_tech_a + $bonusShield));
            if ($shield_tech_a > 1) {
                echo " (" . get_percent_string($shield_tech_a, 1) . " durch " . $techNames[SHIELD_TECH_ID] . " " . $shield_tech_level;
                if ($bonusShield > 0)
                    echo ", " . get_percent_string((1 + $bonusShield), 1) . " durch Spezialschiffe";
                echo ")";
            }
            echo "</td></tr>";
            echo "<tr><td><b>Waffen:</b></td>
                    <td>" . nf($totalWeapon) . "</td>
                    <td>" . nf($totalWeapon * ($weapon_tech_a + $bonusWeapon));
            if ($weapon_tech_a > 1) {
                echo " (" . get_percent_string($weapon_tech_a, 1) . " durch " . $techNames[WEAPON_TECH_ID] . " " . $weapon_tech_level;
                if ($bonusWeapon > 0)
                    echo ", " . get_percent_string((1 + $bonusWeapon), 1) . " durch Spezialschiffe";
                echo ")";
            }
            echo "</td></tr>";
            echo "<tr><td><b>Reparatur:</b></td>
                    <td>" . nf($totalHeal) . "</td>
                    <td>" . nf($totalHeal * ($heal_tech_a + $bonusHeal));
            if ($heal_tech_a > 1) {
                echo " (" . get_percent_string($heal_tech_a, 1) . " durch " . $techNames[REGENA_TECH_ID] . " " . $heal_tech_level;
                if ($bonusHeal > 0)
                    echo ", " . get_percent_string((1 + $bonusHeal), 1) . " durch Spezialschiffe";
                echo ")";
            }
            echo "</td></tr>";
            echo "<tr><td><b>Anzahl Anlagen:</b></td>
            <td colspan=\"2\">" . nf(array_sum($defenseCounts)) . "</td></tr>";
        } else {
            echo "<tr><td><i>Keine Verteidigung vorhanden!</i></td></tr>";
        }
        tableEnd();

        tableStart("Details");
        echo "<tr><th>Typ</th><th>Anzahl</th><th>Felder</th></tr>";
        foreach ($defenseCounts as $defenseId => $defenseCount) {
            echo "<tr>
                <td>" . $defenses[$defenseId]->name . "</td>
                <td>" . nf($defenseCount) . "</td>
                <td>" . nf($defenseCount * $defenses[$defenseId]->fields) . "</td>
                </tr>";
        }
        unset($v);
        tableEnd();

        echo "</div>";



        if (!$planet->mainPlanet) {
            echo "&nbsp;<input type=\"button\" value=\"Kolonie aufheben\" onclick=\"document.location='?page=$page&action=remove'\" />";
            if (!$cu->changedMainPlanet()) {
                echo "&nbsp;<input type=\"button\" value=\"Zum Hauptplaneten machen\" onclick=\"document.location='?page=$page&action=change_main'\" />";
            }
        }
    }
} else {
    error_msg("Dieser Planet existiert nicht oder er gehlört nicht dir!");
}

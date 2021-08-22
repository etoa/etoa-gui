<?PHP

use EtoA\Backend\BackendMessageService;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\StringUtils;
use EtoA\Universe\Asteroid\AsteroidRepository;
use EtoA\Universe\EmptySpace\EmptySpaceRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Nebula\NebulaRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Star\SolarTypeRepository;
use EtoA\Universe\Star\StarRepository;
use EtoA\Universe\Wormhole\WormholeRepository;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var EntityRepository $entityRepo */
$entityRepo = $app[EntityRepository::class];

/** @var StarRepository $starRepo */
$starRepo = $app[StarRepository::class];

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var AsteroidRepository $asteroidRepo */
$asteroidRepo = $app[AsteroidRepository::class];

/** @var NebulaRepository $nebulaRepo */
$nebulaRepo = $app[NebulaRepository::class];

/** @var WormholeRepository $wormholeRepo */
$wormholeRepo = $app[WormholeRepository::class];

/** @var EmptySpaceRepository $emptySpaceRepo */
$emptySpaceRepo = $app[EmptySpaceRepository::class];

/** @var SolarTypeRepository $solarTypeRepository */
$solarTypeRepository = $app[SolarTypeRepository::class];

/** @var PlanetTypeRepository $planetTypeRepository */
$planetTypeRepository = $app[PlanetTypeRepository::class];

/** @var PlanetService $planetService */
$planetService = $app[PlanetService::class];

/** @var BackendMessageService $backendMessageService */
$backendMessageService = $app[BackendMessageService::class];
/** @var LogRepository $logRepository */
$logRepository = $app[LogRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

$id = $request->query->getInt('id');
if ($id > 0) {
    $entity = $entityRepo->findIncludeCell($id);
    if ($entity !== null) {
        echo "<h2>Raumobjekt " . $entity->toString() . " bearbeiten</h2>";
        if ($id > 1) {
            echo button("&lt;&lt; Vorheriges Objekt", "?page=$page&amp;sub=$sub&id=" . ($id - 1) . "");
        }
        echo " &nbsp; Objekt " . $entity->id . " &nbsp; ";
        echo button("Nächstes Objekt &gt;&gt;", "?page=$page&amp;sub=$sub&id=" . ($id + 1) . "") . "<br/><br/>
        " . button("Alle Objekte dieser Zelle/dieses Systems anzeigen", "?page=$page" . searchQueryUrl("cell_s:=:" . $entity->sx . "_" . $entity->sy . ";cell_c:=:" . $entity->cx . "_" . $entity->cy)) . "
        " . button("System dieses Objekts auf der Karte anzeigen", "?page=$page&amp;sub=map&amp;cell=" . $entity->cellId);
        echo "<br/><br/>";

        if ($entity->code == EntityType::PLANET) {
            if ($request->request->has('save')) {
                $planet = $planetRepo->find($id);

                if ($request->request->has('planet_user_main')) {
                    if (!$planet->mainPlanet && $planetRepo->setMain($id, $planet->userId)) {
                        success_msg("Hauptplanet gesetzt; ursprüngliche Hautpplanet-Zuordnung entfernt!");
                    }
                } else {
                    if ($planet->mainPlanet && $planetRepo->unsetMain($id)) {
                        success_msg("Hauptplanet-Zuordnung entfernt. Denke daran, einen neuen Hautplanet festzulegen!");
                    }
                }

                if ($request->request->has('rst_user_changed')) {
                    $planetRepo->resetUserChanged($id);
                }

                $image = $planet->typeId != $request->request->getInt('planet_type_id')
                    ? $request->request->getInt('planet_type_id') . "_1"
                    : $request->request->get('planet_image');

                $affected = $planetRepo->update(
                    $id,
                    $request->request->getInt('planet_type_id'),
                    $request->request->get('planet_name'),
                    $request->request->getInt('planet_fields'),
                    $request->request->getInt('planet_fields_extra'),
                    $image,
                    $request->request->getInt('planet_temp_from'),
                    $request->request->getInt('planet_temp_to'),
                    $request->request->getInt('planet_res_metal'),
                    $request->request->getInt('planet_res_crystal'),
                    $request->request->getInt('planet_res_plastic'),
                    $request->request->getInt('planet_res_fuel'),
                    $request->request->getInt('planet_res_food'),
                    $request->request->getInt('planet_wf_metal'),
                    $request->request->getInt('planet_wf_crystal'),
                    $request->request->getInt('planet_wf_plastic'),
                    $request->request->getInt('planet_people'),
                    $request->request->get('planet_desc')
                );
                $affectedAdd = $planetRepo->addResources(
                    $id,
                    $request->request->getInt('planet_res_metal_add'),
                    $request->request->getInt('planet_res_crystal_add'),
                    $request->request->getInt('planet_res_plastic_add'),
                    $request->request->getInt('planet_res_fuel_add'),
                    $request->request->getInt('planet_res_food_add'),
                    $request->request->getInt('planet_people_add')
                );
                if ($affected || $affectedAdd) {
                    success_msg("Änderungen übernommen");
                }
            }
            if ($request->request->has('calcres')) {
                $backendMessageService->updatePlanet($id);
                sleep(2);
                success_msg("Resourcen neu berechnet");
            } else {
                if (count($request->request->all()) > 0 && !$request->request->has('save')) {
                    // Wenn der Besitzer wechseln soll
                    if ($request->request->get('planet_user_id') != $request->request->get('planet_user_id_old')) {
                        //Planet dem neuen User übergeben (Schiffe und Verteidigung werden vom Planeten gelöscht!)
                        $planetService->changeOwner($id, $request->request->getInt('planet_user_id'));

                        if ($request->request->getInt('planet_user_id') == 0) {
                            $planetRepo->reset($id);
                        }

                        //Log Schreiben
                        $logRepository->add(LogFacility::GALAXY, LogSeverity::INFO, $cu->nick . " wechselt den Besitzer vom Planeten: [page galaxy sub=edit id=" . $id . "][B]" . $id . "[/B][/page]
Alter Besitzer: [page user sub=edit user_id=" . $request->request->getInt('planet_user_id_old') . "][B]" . $request->request->getInt('planet_user_id_old') . "[/B][/page]
Neuer Besitzer: [page user sub=edit user_id=" . $request->request->getInt('planet_user_id') . "][B]" . $request->request->getInt('planet_user_id') . "[/B][/page]");

                        success_msg("Der Planet wurde dem User mit der ID: [b]" . $request->request->getInt('planet_user_id') . "[/b] übergeben!");
                    } else {
                        error_msg("Es wurde kein neuer Besitzer gewählt!");
                    }
                }
            }

            $planet = $planetRepo->find($id);

            echo "<form action=\"?page=$page&sub=edit&id=" . $id . "\" method=\"post\" id=\"editform\">";
            tableStart("<span style=\"color:" . Entity::$entityColors[$entity->code] . "\">Planet</span>", "auto");


            echo "<tr><th>Name</t>
            <td><input type=\"text\" name=\"planet_name\" value=\"" . $planet->name . "\" size=\"20\" maxlength=\"250\" /></td>";
            echo "<th>Typ</th>
            <td>
            <select name=\"planet_type_id\">";
            $planetTypeNames = $planetTypeRepository->getPlanetTypeNames(true);
            $selectedPlanetTypeName = null;
            foreach ($planetTypeNames as $planetTypeId => $planetTypeName) {
                echo "<option value=\"" . $planetTypeId . "\"";
                if ($planet->typeId == $planetTypeId) {
                    echo " selected=\"selected\"";
                    $selectedPlanetTypeName = $planetTypeName;
                }
                echo ">" . $planetTypeName . "</option>\n";
            }
            echo "</select></td></tr>";

            echo "<tr><td style=\"height:2px;\" colspan=\"4\"></td></tr>";

            /** @var UserRepository $userRepo */
            $userRepo = $app[UserRepository::class];

            //Listet alle User der Spiels auf
            echo "<tr><th>Besitzer</th><td colspan=\"3\"><select name=\"planet_user_id\">";
            echo "<option value=\"0\">(niemand)</option>";
            foreach ($userRepo->searchUserNicknames() as $userId => $userNick) {
                echo "<option value=\"$userId\"";
                if ($planet->userId == $userId) {
                    echo " selected=\"selected\"";
                }
                echo ">" . $userNick . "</option>";
            }
            echo "</select> ";
            if ($planet->userId > 0) {
                $allianceId = $userRepo->getAllianceId($planet->userId);
                if ($allianceId > 0) {
                    $ally = new Alliance($allianceId);
                    echo $ally . " &nbsp; ";
                    unset($ally);
                }
            }
            echo "<input type=\"hidden\" name=\"planet_user_id_old\" value=\"" . $planet->userId . "\">";
            echo "<input tabindex=\"29\" type=\"button\" name=\"change_owner\" value=\"Planet übergeben\" class=\"button\" onclick=\"if( confirm('Dieser Planet soll einem neuen Besitzer gehören. Alle Schiffs- und Verteidigungsdaten vom alten Besitzer werden komplett gelöscht.')) document.getElementById('editform').submit()\"/>&nbsp;";
            echo "</td></tr>";

            echo "<tr>
            <th>Hauptplanet</th>
            <td>";
            if ($planet->userId > 0) {
                echo "<input type=\"checkbox\" name=\"planet_user_main\" " . ($planet->mainPlanet ? " checked=\"checked\"" : "") . " value=\"1\"/> Ist Hauptplanet";
            } else
                echo "-";
            echo "</td>
            <th>Letzer Besitzerwechsel</th>
            <td>
            " . ($planet->userChanged > 0 ? StringUtils::formatDate($planet->userChanged) . " <input type=\"checkbox\" name=\"rst_user_changed\" value=\"1\" /> Reset" : '-') . "
            </td>
            </tr>";

            echo "<tr><td style=\"height:2px;\" colspan=\"4\"></td></tr>";

            echo "<tr><th>Felder / Extra-Felder</th>
            <td><input type=\"text\" name=\"planet_fields\" value=\"" . $planet->fields . "\" size=\"10\" maxlength=\"250\" />
            <input type=\"text\" name=\"planet_fields_extra\" value=\"" . $planet->fieldsExtra . "\" size=\"10\" maxlength=\"250\" /></td>";
            echo "<th>Felder benutzt</th>
            <td>" . StringUtils::formatNumber($planet->fieldsUsed) . "</td></tr>";

            echo "<tr><th>Temperatur</th>
            <td>
                <input type=\"text\" name=\"planet_temp_from\" value=\"" . $planet->tempFrom . "\" size=\"4\" maxlength=\"5\" />
                bis <input type=\"text\" name=\"planet_temp_to\" value=\"" . $planet->tempTo . "\" size=\"4\" maxlength=\"5\" /> &deg;C
            </td>";
            $imPath = IMAGE_PATH . "/planets/planet";
            $imPathPost = "_small." . IMAGE_EXT;
            echo "<th>Bild</th>
            <td>
            <img id=\"pimg\" src=\"" . $imPath . $planet->image . $imPathPost . "\" style=\"float:left;\" />
            <select name=\"planet_image\" onchange=\"document.getElementById('pimg').src='$imPath'+this.value+'$imPathPost'\">";
            echo "<option value=\"\">Undefiniert</option>";

            for ($x = 1; $x <= $config->getInt('num_planet_images'); $x++) {
                echo "<option value=\"" . $planet->typeId . "_" . $x . "\"";
                if ($planet->image == $planet->typeId . "_" . $x)
                    echo " selected=\"selected\"";
                echo ">" . $selectedPlanetTypeName . " $x</option>\n";
            }
            echo "</select>

            </td>";

            echo "</tr>";

            echo "<td style=\"height:2px;\" colspan=\"4\"></td></tr>";

            echo "<tr><th class=\"resmetalcolor\">Titan</th>
            <td><input type=\"text\" name=\"planet_res_metal\" value=\"" . intval($planet->resMetal) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"planet_res_metal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
            echo "<th class=\"rescrystalcolor\">Silizium</th>
            <td><input type=\"text\" name=\"planet_res_crystal\" value=\"" . intval($planet->resCrystal) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"planet_res_crystal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

            echo "<tr><th class=\"resplasticcolor\">PVC</th>
            <td><input type=\"text\" name=\"planet_res_plastic\" value=\"" . intval($planet->resPlastic) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"planet_res_plastic_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
            echo "<th class=\"resfuelcolor\">Tritium</th>
            <td><input type=\"text\" name=\"planet_res_fuel\" value=\"" . intval($planet->resFuel) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"planet_res_fuel_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

            echo "<tr><th class=\"resfoodcolor\">Nahrung</th>
            <td><input type=\"text\" name=\"planet_res_food\" value=\"" . intval($planet->resFood) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"planet_res_food_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
            echo "<th class=\"respeoplecolor\">Bevölkerung</th>
            <td><input type=\"text\" name=\"planet_people\" value=\"" . intval($planet->people) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"planet_people_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

            echo "<td style=\"height:2px;\" colspan=\"4\"></td></tr>";

            echo "<tr><th>Produktion " . RES_METAL . "</th>
            <td>" . StringUtils::formatNumber($planet->prodMetal) . "</td>";
            echo "<th>Speicher " . RES_METAL . ":</th>
            <td>" . StringUtils::formatNumber($planet->storeMetal) . "</td></tr>";

            echo "<tr><th>Produktion " . RES_CRYSTAL . "</th>
            <td>" . StringUtils::formatNumber($planet->prodCrystal) . "</td>";
            echo "<th>Speicher " . RES_CRYSTAL . ":</th>
            <td>" . StringUtils::formatNumber($planet->storeCrystal) . "</td></tr>";

            echo "<tr><th>Produktion " . RES_PLASTIC . "</th>
            <td>" . StringUtils::formatNumber($planet->prodPlastic) . "</td>";
            echo "<th>Speicher " . RES_PLASTIC . ":</th>
            <td>" . StringUtils::formatNumber($planet->storePlastic) . "</td></tr>";

            echo "<tr><th>Produktion " . RES_FUEL . "</th>
            <td>" . StringUtils::formatNumber($planet->prodFuel) . "</td>";
            echo "<th>Speicher " . RES_FUEL . ":</th>
            <td>" . StringUtils::formatNumber($planet->storeFuel) . "</td></tr>";

            echo "<tr><th>Produktion " . RES_FOOD . "</th>
            <td>" . StringUtils::formatNumber($planet->prodFood) . "</td>";
            echo "<th>Speicher " . RES_FOOD . ":</th>
            <td>" . StringUtils::formatNumber($planet->storeFood) . "</td></tr>";

            echo "<tr><th>Verbrauch Energie:</th>
            <td>" . StringUtils::formatNumber($planet->usePower) . "</td>";
            echo "<th>Produktion Energie:</th>
            <td>" . StringUtils::formatNumber($planet->prodPower) . "</td></tr>";

            echo "<tr><th>Wohnraum</th>
            <td>" . StringUtils::formatNumber($planet->peoplePlace) . "</td>";
            echo "<th>Bevölkerungswachstum</th>
            <td>" . StringUtils::formatNumber($planet->prodPeople) . "</td></tr>";

            echo "<td style=\"height:2px;\" colspan=\"4\"></td></tr>";

            echo "<tr><th>Trümmerfeld Titan</th>
            <td><input type=\"text\" name=\"planet_wf_metal\" value=\"" . $planet->wfMetal . "\" size=\"20\" maxlength=\"250\" /></td>";
            echo "<th>Trümmerfeld Silizium</th>
            <td><input type=\"text\" name=\"planet_wf_crystal\" value=\"" . $planet->wfCrystal . "\" size=\"20\" maxlength=\"250\" /></td></tr>";

            echo "<tr><th>Trümmerfeld PVC</th>
            <td><input type=\"text\" name=\"planet_wf_plastic\" value=\"" . $planet->wfPlastic . "\" size=\"20\" maxlength=\"250\" /></td>";
            echo "<th>Updated</th>
            <td>" . date("d.m.Y H:i", $planet->lastUpdated) . "</th></tr>";


            echo "<tr><th>Beschreibung</td>
            <td colspan=\"3\"><textarea name=\"planet_desc\" rows=\"2\" cols=\"50\" >" . stripslashes($planet->description) . "</textarea></td></tr>";
            echo "</table>";
            echo "<br/>";
            echo "<p>";
            echo "<input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"Übernehmen\" class=\"button\" />&nbsp;";
            echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page&amp;newsearch'\" value=\"Neue Suche\" /> ";
            echo "<input tabindex=\"28\" type=\"button\" value=\"Zurück zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
            echo "</p><hr/><p>";
            echo "<input type=\"submit\" name=\"calcres\" value=\"Neu berechnen\" class=\"button\" />&nbsp;";
            echo "<input type=\"button\" value=\"Gebäude\" onclick=\"document.location='?page=buildings&action=search&query=" . searchQuery(array("entity_id" => $planet->id)) . "'\" /> &nbsp;";
            echo "</p>";
            echo "</form>";
        } elseif ($entity->code == EntityType::STAR) {
            if ($request->request->has('save')) {
                if ($starRepo->update($id, $request->request->get('name'), $request->request->getInt('type_id'))) {
                    success_msg("Änderungen übernommen");
                }
            }

            $star = $starRepo->find($id);

            echo "<form action=\"?page=$page&sub=edit&id=" . $id . "\" method=\"post\" id=\"editform\">";
            tableStart("<span style=\"color:" . Entity::$entityColors[$entity->code] . "\">Stern</span>", "auto");
            echo "<tr><th>Name</th>
            <td><input type=\"text\" name=\"name\" value=\"" . $star->name . "\" size=\"20\" maxlength=\"250\" /></td>";
            echo "<th>Typ</th>
            <td>
            <img src=\"" . IMAGE_PATH . "/stars/star" . $star->typeId . "_small." . IMAGE_EXT . "\" style=\"float:left;\" />
            <select name=\"type_id\">";
            $solarTypeNames = $solarTypeRepository->getSolarTypeNames(true);
            foreach ($solarTypeNames as $solarTypeId => $solarTypeName) {
                echo "<option value=\"" . $solarTypeId . "\"";
                if ($star->typeId == $solarTypeId) {
                    echo " selected=\"selected\"";
                }
                echo ">" . $solarTypeName . "</option>\n";
            }
            echo "</select></td></tr>";
            echo "</table>";
            echo "<br/>
                        <input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"Übernehmen\" class=\"button\" />&nbsp;";
            echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
            echo "<input tabindex=\"28\" type=\"button\" value=\"Zurück zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
            echo "</form>";
        } elseif ($entity->code == EntityType::ASTEROID) {
            if ($request->request->has('save')) {
                //Daten Speichern
                $affected = $asteroidRepo->update(
                    $id,
                    $request->request->getInt('res_metal'),
                    $request->request->getInt('res_crystal'),
                    $request->request->getInt('res_plastic'),
                    $request->request->getInt('res_fuel'),
                    $request->request->getInt('res_food'),
                    $request->request->getInt('res_power')
                );
                $affectedAdd = $asteroidRepo->addResources(
                    $id,
                    $request->request->getInt('res_metal_add'),
                    $request->request->getInt('res_crystal_add'),
                    $request->request->getInt('res_plastic_add'),
                    $request->request->getInt('res_fuel_add'),
                    $request->request->getInt('res_food_add'),
                    $request->request->getInt('res_power_add')
                );
                if ($affected || $affectedAdd) {
                    success_msg("Änderungen übernommen");
                }
            }

            $asteroid = $asteroidRepo->find($id);

            echo "<form action=\"?page=$page&sub=edit&id=" . $id . "\" method=\"post\" id=\"editform\">";
            tableStart("<span style=\"color:" . Entity::$entityColors[$entity->code] . "\">Asteroidenfeld</span>", "auto");

            echo "<tr><th>" . RES_METAL . "</th>
            <td><input type=\"text\" name=\"res_metal\" value=\"" . intval($asteroid->resMetal) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"res_metal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
            echo "<th>" . RES_CRYSTAL . "</th>
            <td><input type=\"text\" name=\"res_crystal\" value=\"" . intval($asteroid->resCrystal) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"res_crystal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

            echo "<tr><th>" . RES_PLASTIC . "</th>
            <td><input type=\"text\" name=\"res_plastic\" value=\"" . intval($asteroid->resPlastic) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"res_plastic_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
            echo "<th>" . RES_FUEL . "</th>
            <td><input type=\"text\" name=\"res_fuel\" value=\"" . intval($asteroid->resFuel) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"res_fuel_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

            echo "<tr><th>" . RES_FOOD . "</th>
            <td><input type=\"text\" name=\"res_food\" value=\"" . intval($asteroid->resFood) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"res_food_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
            echo "<th>" . RES_POWER . "</th>
            <td><input type=\"text\" name=\"res_power\" value=\"" . intval($asteroid->resPower) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"res_power_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

            echo "</table>";
            echo "<br/>
                        <input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"Übernehmen\" class=\"button\" />&nbsp;";
            echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
            echo "<input tabindex=\"28\" type=\"button\" value=\"Zurück zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
            echo "</form>";
        } elseif ($entity->code == EntityType::NEBULA) {
            if ($request->request->has('save')) {
                //Daten Speichern
                $affected = $nebulaRepo->update(
                    $id,
                    $request->request->getInt('res_metal'),
                    $request->request->getInt('res_crystal'),
                    $request->request->getInt('res_plastic'),
                    $request->request->getInt('res_fuel'),
                    $request->request->getInt('res_food'),
                    $request->request->getInt('res_power')
                );
                $affectedAdd = $nebulaRepo->addResources(
                    $id,
                    $request->request->getInt('res_metal_add'),
                    $request->request->getInt('res_crystal_add'),
                    $request->request->getInt('res_plastic_add'),
                    $request->request->getInt('res_fuel_add'),
                    $request->request->getInt('res_food_add'),
                    $request->request->getInt('res_power_add')
                );
                if ($affected || $affectedAdd) {
                    success_msg("Änderungen übernommen");
                }
            }

            $nebula = $nebulaRepo->find($id);

            echo "<form action=\"?page=$page&sub=edit&id=" . $id . "\" method=\"post\" id=\"editform\">";
            tableStart("<span style=\"color:" . Entity::$entityColors[$entity->code] . "\">Interstellarer Nebel</span>", "auto");

            echo "<tr><th>" . RES_METAL . "</th>
            <td><input type=\"text\" name=\"res_metal\" value=\"" . intval($nebula->resMetal) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"res_metal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
            echo "<th>" . RES_CRYSTAL . "</th>
            <td><input type=\"text\" name=\"res_crystal\" value=\"" . intval($nebula->resCrystal) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"res_crystal_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

            echo "<tr><th>" . RES_PLASTIC . "</th>
            <td><input type=\"text\" name=\"res_plastic\" value=\"" . intval($nebula->resPlastic) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"res_plastic_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
            echo "<th>" . RES_FUEL . "</th>
            <td><input type=\"text\" name=\"res_fuel\" value=\"" . intval($nebula->resFuel) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"res_fuel_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

            echo "<tr><th>" . RES_FOOD . "</th>
            <td><input type=\"text\" name=\"res_food\" value=\"" . intval($nebula->resFood) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"res_food_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td>";
            echo "<th>" . RES_POWER . "</th>
            <td><input type=\"text\" name=\"res_power\" value=\"" . intval($nebula->resPower) . "\" size=\"12\" maxlength=\"20\" /><br/>
            +/-: <input type=\"text\" name=\"res_power_add\" value=\"0\" size=\"8\" maxlength=\"20\" /></td></tr>";

            echo "</table>";
            echo "<br/>
                        <input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"Übernehmen\" class=\"button\" />&nbsp;";
            echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
            echo "<input tabindex=\"28\" type=\"button\" value=\"Zurück zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
            echo "</form>";
        } elseif ($entity->code == EntityType::WORMHOLE) {
            //Daten Speichern
            if ($request->request->has('save')) {
                $persistent = $request->request->getBoolean('wormhole_persistent');

                $wormhole = $wormholeRepo->find($id);

                $wormholeRepo->setPersistent($wormhole->id, $persistent);
                $wormholeRepo->setPersistent($wormhole->targetId, $persistent);

                success_msg("Änderungen übernommen");
            }

            $wormhole = $wormholeRepo->find($id);

            echo "<form action=\"?page=$page&sub=edit&id=" . $id . "\" method=\"post\" id=\"editform\">";
            tableStart("<span style=\"color:" . Entity::$entityColors[$entity->code] . "\">Wurmloch</span>", "auto");
            echo "<tr><th>Entstanden</th><td>" . StringUtils::formatDate($wormhole->changed) . "</td><tr/>";
            echo "<tr><th>Ziel</th>
            <td>";
            $ent = Entity::createFactoryById($wormhole->targetId);
            echo "<a href=\"?page=$page&amp;sub=$sub&amp;id=" . $ent->id() . "\">" . $ent . "</a>";
            echo "</td></tr>";
            echo "<tr><th>Persistent</th><td>";
            echo "<input type=\"radio\" name=\"wormhole_persistent\" id=\"wormhole_persistent_0\" value=\"0\" " . (!$wormhole->persistent ? " checked=\"checked\"" : "") . "> <label for=\"wormhole_persistent_0\">Nein</label> ";
            echo "<input type=\"radio\" name=\"wormhole_persistent\" id=\"wormhole_persistent_1\" value=\"1\" " . ($wormhole->persistent ? " checked=\"checked\"" : "") . "> <label for=\"wormhole_persistent_1\">Ja</label> ";
            echo "</td><tr/>";
            echo "</table>";
            echo "<br/>
                        <input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"Übernehmen\" class=\"button\" />&nbsp;";
            echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
            echo "<input tabindex=\"28\" type=\"button\" value=\"Zurück zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
            echo "</form>";
        } elseif ($entity->code == EntityType::EMPTY_SPACE) {
            $space = $emptySpaceRepo->find($id);

            echo "<form action=\"?page=$page&sub=edit&id=" . $id . "\" method=\"post\" id=\"editform\">";
            tableStart("<span style=\"color:" . Entity::$entityColors[$entity->code] . "\">Leerer Raum</span>", "auto");
            echo "<tr><th>Zuletzt besucht</th>
            <td>";
            echo ($space->lastVisited > 0) ? StringUtils::formatDate($space->lastVisited) : "Nie";
            echo "</td></tr>";
            echo "</table>";
            echo "<br/>
                        <input tabindex=\"26\" type=\"submit\" name=\"save\" value=\"Übernehmen\" class=\"button\" />&nbsp;";
            echo "<input tabindex=\"27\" type=\"button\" class=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
            echo "<input tabindex=\"28\" type=\"button\" value=\"Zurück zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
            echo "</form>";
        } else {
            error_msg("Für diesen Entitätstyp (" . $entity->code . ") existiert noch kein Bearbeitungsformular!");
            echo "<br/><br/><input type=\"button\" value=\"Zurück zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
        }
    } else {
        echo "Entität nicht vorhanden!";
    }
} else {
    echo "Ungültige ID!";
}

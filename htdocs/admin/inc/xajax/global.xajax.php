<?PHP

use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceSearch;
use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Missile\MissileDataRepository;
use EtoA\Missile\MissileRepository;
use EtoA\Requirement\RequirementRepositoryProvider;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipXpCalculator;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetSearch;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;

$xajax->register(XAJAX_FUNCTION, "planetSelectorByCell");
$xajax->register(XAJAX_FUNCTION, "planetSelectorByUser");

$xajax->register(XAJAX_FUNCTION, "showShipsOnPlanet");
$xajax->register(XAJAX_FUNCTION, "addShipToPlanet");
$xajax->register(XAJAX_FUNCTION, "removeShipFromPlanet");
$xajax->register(XAJAX_FUNCTION, "editShipByListId");
$xajax->register(XAJAX_FUNCTION, "editShipByShipId");
$xajax->register(XAJAX_FUNCTION, "submitEditShip");
$xajax->register(XAJAX_FUNCTION, "calcShipLevel");

$xajax->register(XAJAX_FUNCTION, "showMissilesOnPlanet");
$xajax->register(XAJAX_FUNCTION, "addMissileToPlanet");
$xajax->register(XAJAX_FUNCTION, "removeMissileFromPlanet");
$xajax->register(XAJAX_FUNCTION, "editMissile");
$xajax->register(XAJAX_FUNCTION, "submitEditMissile");

$xajax->register(XAJAX_FUNCTION, "showDefenseOnPlanet");
$xajax->register(XAJAX_FUNCTION, "addDefenseToPlanet");
$xajax->register(XAJAX_FUNCTION, "removeDefenseFromPlanet");
$xajax->register(XAJAX_FUNCTION, "editDefense");
$xajax->register(XAJAX_FUNCTION, "submitEditDefense");

$xajax->register(XAJAX_FUNCTION, "showBuildingsOnPlanet");
$xajax->register(XAJAX_FUNCTION, "addBuildingToPlanet");
$xajax->register(XAJAX_FUNCTION, "addAllBuildingToPlanet");
$xajax->register(XAJAX_FUNCTION, "removeBuildingFromPlanet");
$xajax->register(XAJAX_FUNCTION, "editBuilding");
$xajax->register(XAJAX_FUNCTION, "submitEditBuilding");

$xajax->register(XAJAX_FUNCTION, "searchUser");
$xajax->register(XAJAX_FUNCTION, "searchUserList");
$xajax->register(XAJAX_FUNCTION, "searchAlliance");
$xajax->register(XAJAX_FUNCTION, "lockUser");

$xajax->register(XAJAX_FUNCTION, "buildingPrices");
$xajax->register(XAJAX_FUNCTION, "totalBuildingPrices");

function planetSelectorByCell($form, $function, $show_user_id = 0)
{
    global $app;

    /** @var EntityRepository $entityRepository */
    $entityRepository = $app[EntityRepository::class];
    /** @var PlanetRepository $planetRepository */
    $planetRepository = $app[PlanetRepository::class];

    $objResponse = new xajaxResponse();
    $out = '';
    if ($form['cell_sx'] != 0 && $form['cell_sy'] != 0 && $form['cell_cx'] != 0 && $form['cell_cy'] != 0) {
        $entitiesInCell = $entityRepository->searchEntities(EntitySearch::create()->sx($form['cell_sx'])->sy($form['cell_sy'])->cx($form['cell_cx'])->cy($form['cell_cy']));
        $nr = count($entitiesInCell);
        if ($nr > 0) {
            if ($nr > 1) {
                $entities = [];
                $planetIds = [];
                foreach ($entitiesInCell as $entity) {
                    if ($entity->code === EntityType::PLANET) {
                        $planetIds[] = $entity->id;
                        $entities[$entity->id] = $entity;
                    }
                }

                $planetNames = $planetRepository->searchPlanetNamesWithUserNick(PlanetSearch::create()->idIn($planetIds));
                $nr = count($planetNames);
                if ($nr > 0) {
                    $out = "<select name=\"entity_id\" size=\"" . ($nr) . "\" onchange=\"showLoader('shipsOnPlanet');xajax_" . $function . "(this.options[this.selectedIndex].value);\">\n";
                    foreach ($planetNames as $planetName) {
                        if ($show_user_id == 1) {
                            $val = $planetName->id . ":" . $planetName->userId;
                        } else {
                            $val = $planetName->id;
                        }
                        if ($planetName->userId > 0)
                            $out .= "<option value=\"$val\">" . $entities[$planetName->id]->pos . " " . $planetName->displayName() . " (" . $planetName->userNick . ")</option>";
                        else
                            $out .= "<option value=\"$val\" style=\"font-style:italic\">" . $entities[$planetName->id]->pos . " " . $planetName->displayName() . " Unbewohnt</option>";
                    }
                    $out .= "</select>";
                }
            } else {
                $out = "Dies ist kein Sonnensystem!";
            }
        } else {
            $out = "Zelle nicht gefunden!";
        }
    } else {
        $out = "Sonnensystem w&auml;hlen...";
    }
    $objResponse->assign("planetSelector", "innerHTML", $out);
    $objResponse->assign("user_nick", "value", "");
    return $objResponse;
}

function planetSelectorByUser($userNick, $function, $show_user_id = 1)
{
    global $app;

    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];
    /** @var PlanetRepository $planetRepository */
    $planetRepository = $app[PlanetRepository::class];

    $objResponse = new xajaxResponse();
    if ($userNick != "") {
        $userId = $userRepository->getUserIdByNick($userNick);
        $planets = $planetRepository->getUserPlanetsWithCoordinates($userId);
        $nr = count($planets);
        if ($nr > 0) {
            $out = "<select name=\"entity_id\" size=\"" . ($nr + 1) . "\" onchange=\"showLoader('shipsOnPlanet');xajax_" . $function . "(this.options[this.selectedIndex].value);\">\n";
            $cnt = 0;
            $val = '';
            foreach ($planets as $planet) {
                if ($cnt === 0) {
                    $out .= "<option value=\"0:" . $planet->userId . "\">Alle</option>";
                    $cnt++;
                }

                if ($show_user_id == 1) {
                    $val = $planet->id . ":" . $planet->userId;
                } else {
                    $val = $planet->id . ":" . "0";
                }
                $out .= "<option value=\"$val\">" . $planet->toString() . "</option>\n";
            }
            $out .= "</select>\n";

            if ($nr == 1) {
                $objResponse->script("showLoader('shipsOnPlanet');xajax_" . $function . "('" . $val . "');");
            }
        } else {
            $out = "Keine Planeten gefunden!";
        }
    } else {
        $out = "Korrekten Usernamen w&auml;hlen...";
    }
    $objResponse->assign("planetSelector", "innerHTML", $out);
    return $objResponse;
}

function showShipsOnPlanet($form)
{
    global $app;

    /** @var ShipRepository $shipRepository */
    $shipRepository = $app[ShipRepository::class];
    /** @var ShipDataRepository $shipDataRepository */
    $shipDataRepository = $app[ShipDataRepository::class];

    $objResponse = new xajaxResponse();

    $updata = explode(":", $form);
    $eid = $updata[0];
    $uid = $updata[1];

    $style = 'none';

    ob_start();

    if ($eid != 0) {
        $shipList = $shipRepository->findForUser((int) $uid, (int) $eid);
        if (count($shipList) > 0) {
            $out = "<table class=\"tb\">
            <tr><th>Anzahl</th>
            <th>Bunker</th>
            <th>Typ</th>
            <th>Punkte</th>
            <th>Spezielles</th>
            <th>Aktionen</th></tr>";
            $points = 0;
            $ships = $shipDataRepository->getAllShips(true);
            foreach ($shipList as $item) {
                $ship = $ships[$item->shipId];
                $itemPoints = $ship->points * ($item->count + $item->bunkered);
                $points += $itemPoints;
                $out .= "<tr><td style=\"width:80px\" id=\"cnt_" . $item->id . "\">" . $item->count . "</td>
                <td style=\"width:80px\" id=\"bunkered_" . $item->id . "\">" . $item->count . "</td>
                <td>" . $ship->name . "</td>
                <td>" . ($itemPoints) . "</td>
                <td id=\"special_" . $item->id . "\">";
                if ($ship->specialNeedExp > 0) {
                    $out .= StringUtils::formatNumber($item->specialShipExp) . " XP, Level " . ShipXpCalculator::levelByXp($ship->specialNeedExp, $ship->specialExpFactor, $item->specialShipExp);
                }
                $out .= "
                <td style=\"width:180px\" id=\"actions_" . $item->id . "\" id=\"actions_" . $item->id . "\">
                <input type=\"button\" value=\"Bearbeiten\" onclick=\"xajax_editShipByListId(xajax.getFormValues('selector')," . $item->id . ")\" />
                <input type=\"button\" value=\"Löschen\" onclick=\"if (confirm('Sollen " . $item->count . " " . $ship->name . " von diesem Planeten gel&ouml;scht werden?')) {showLoaderPrepend('shipsOnPlanet');xajax_removeShipFromPlanet(xajax.getFormValues('selector')," . $item->id . ")}\" />
                </td>
                </tr>";
            }
            $out .= "<tr><td colspan=\"3\"></td><td><b>" . StringUtils::formatNumber($points) . "</b></td><td colspan=\"2\"></td></tr>";
            $out .= "</table>";
        } else {
            $out = "Keine Schiffe vorhanden!<br/>";
        }
        $out .= "<br/><input type=\"Button\" value=\"Neu laden\" onclick=\"showLoader('shipsOnPlanet');xajax_showShipsOnPlanet('" . $form . "');\">";
        $style = '';
    } elseif ($uid != 0) {
        $counts = $shipRepository->getUserShipCounts((int) $uid);
        $ships = array_intersect_key($shipDataRepository->getAllShips(true), $counts);
        if (count($ships) > 0) {
            $out = "<table class=\"tb\">
            <tr><th>Anzahl</th>
            <th>Bunker</th>
            <th>Typ</th>
            <th>Punkte</th>
            <th>Spezielles</th>
            <th>Aktionen</th></tr>";
            $points = 0;
            foreach ($ships as $ship) {
                $count = $counts[$ship->id];
                $points += $ship->points * $count->sum();
                $out .= "<tr id=\"data_" . $ship->id . "\"><td style=\"width:80px\" id=\"cnt_" . $ship->id . "\">" . $count->count . "</td>
                <td style=\"width:80px\" id=\"bunkered_" . $ship->id . "\">" . $count->bunkered . "</td>
                <td>" . $ship->name . "</td>
                <td>" . ($ship->points * $count->count) . "</td>
                <td id=\"special_" . $ship->id . "\">";
                if ($ship->specialNeedExp > 0) {
                    $out .= StringUtils::formatNumber($count->exp) . " XP, Level " . ShipXpCalculator::levelByXp($ship->specialNeedExp, $ship->specialExpFactor, $count->exp);
                }
                $out .= "
                <td style=\"width:180px\" id=\"actions_" . $ship->id . "\" id=\"actions_" . $ship->id . "\">
                <input type=\"button\" value=\"Bearbeiten\" onclick=\"xajax_editShipByShipId(xajax.getFormValues('selector')," . $ship->id . ")\" />
                </td></tr>
                <tr><td colspan=\"6\" id=\"edit_" . $ship->id . "\" style=\"display:none;\"></td></tr>";
            }
            $out .= "<tr><td colspan=\"3\"></td><td><b>" . StringUtils::formatNumber($points) . "</b></td><td colspan=\"2\"></td></tr>";
            $out .= "</table>";
        } else {
            $out = "Keine Schiffe vorhanden!<br/>";
        }

        $out .= "<br/><input type=\"Button\" value=\"Neu laden\" onclick=\"showLoader('shipsOnPlanet');xajax_showShipsOnPlanet('" . $form . "');\">";
    } else {
        $out = "Planet w&auml;hlen...";
    }
    echo $out;
    $out = ob_get_clean();
    $objResponse->assign("addObject", "style.display", $style);
    $objResponse->assign("shipsOnPlanet", "innerHTML", $out);
    return $objResponse;
}

function addShipToPlanet($form)
{
    global $app;

    $objResponse = new xajaxResponse();

    $updata = explode(":", $form['entity_id']);

    if ($updata[1] > 0) {
        /** @var ShipRepository $shipRepository */
        $shipRepository = $app[ShipRepository::class];

        $shipRepository->addShip((int) $form['ship_id'], (int) $form['shiplist_count'], (int) $updata[1], (int) $updata[0]);
        $objResponse->script("xajax_showShipsOnPlanet('" . $form['entity_id'] . "')");
    } else {
        $out = "Planet unbewohnt. Kann keine Schiffe hier bauen!";
        $objResponse->assign("shipsOnPlanet", "innerHTML", $out);
    }
    return $objResponse;
}

function removeShipFromPlanet($form, $listId)
{
    global $app;

    /** @var ShipRepository $shipRepository */
    $shipRepository = $app[ShipRepository::class];

    $shipRepository->removeEntry($listId);

    $objResponse = new xajaxResponse();
    $objResponse->script("xajax_showShipsOnPlanet('" . $form['entity_id'] . "');");
    return $objResponse;
}

function editShipByListId($form, $listId)
{
    global $app;

    /** @var ShipRepository $shipRepository */
    $shipRepository = $app[ShipRepository::class];
    /** @var ShipDataRepository $shipDataRepository */
    $shipDataRepository = $app[ShipDataRepository::class];

    $objResponse = new xajaxResponse();

    $updata = explode(":", $form['entity_id']);

    $shipList = $shipRepository->findForUser((int) $updata[1], (int) $updata[0]);
    if (count($shipList) > 0) {
        $ships = $shipDataRepository->getAllShips(true);
        foreach ($shipList as $item) {
            $ship = $ships[$item->shipId];

            if ($item->id == $listId) {
                $out = "<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editcnt_" . $item->id . "\" value=\"" . $item->count . "\" />";
                $objResponse->assign("cnt_" . $listId, "innerHTML", $out);
                $out = "<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editbunkered_" . $item->id . "\" value=\"" . $item->bunkered . "\" />";
                $objResponse->assign("bunkered_" . $item->id, "innerHTML", $out);
                if ($ship->specialNeedExp > 0) {
                    $out = "<input type=\"hidden\" name=\"ship_special_" . $item->id . "\" value=\"1\"><input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editxp_" . $item->id . "\" value=\"" . $item->specialShipExp . "\" onkeyup=\"xajax_calcShipLevel(" . $item->id . "," . $ship->specialNeedExp . "," . $ship->specialExpFactor . ",this.value);\" /> XP,
                    Level <b><span id=\"editlevel_" . $item->id . "\">" . ShipXpCalculator::levelByXp($ship->specialNeedExp, $ship->specialExpFactor, $item->specialShipExp) . "</span></b><br/>

                    <b>Waffenlevel:</b> <input type=\"text\" name=\"edit_bonus_weapon_" . $item->id . "\" value=\"" . $item->specialShipBonusWeapon . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Strukturlevel:</b> <input type=\"text\" name=\"edit_bonus_structure_" . $item->id . "\" value=\"" . $item->specialShipBonusStructure . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Schildlevel:</b> <input type=\"text\" name=\"edit_bonus_shield_" . $item->id . "\" value=\"" . $item->specialShipBonusShield . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Heallevel:</b> <input type=\"text\" name=\"edit_bonus_heal_" . $item->id . "\" value=\"" . $item->specialShipBonusHeal . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Kapazit&auml;tlevel:</b> <input type=\"text\" name=\"edit_bonus_capacity_" . $item->id . "\" value=\"" . $item->specialShipBonusCapacity . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Speedlevel:</b> <input type=\"text\" name=\"edit_bonus_speed_" . $item->id . "\" value=\"" . $item->specialShipBonusSpeed . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Bereitschaftslevel:</b> <input type=\"text\" name=\"edit_bonus_readiness_" . $item->id . "\" value=\"" . $item->specialShipBonusReadiness . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Besatzungslevel:</b> <input type=\"text\" name=\"edit_bonus_pilots_" . $item->id . "\" value=\"" . $item->specialShipBonusPilots . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Tarnungslevel:</b> <input type=\"text\" name=\"edit_bonus_tarn_" . $item->id . "\" value=\"" . $item->specialShipBonusTarn . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Giftgaslevel:</b> <input type=\"text\" name=\"edit_bonus_antrax_" . $item->id . "\" value=\"" . $item->specialShipBonusAnthrax . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Techklaulevel:</b> <input type=\"text\" name=\"edit_bonus_forsteal_" . $item->id . "\" value=\"" . $item->specialShipBonusForSteal . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Bombardierlevel:</b> <input type=\"text\" name=\"edit_bonus_build_destroy_" . $item->id . "\" value=\"" . $item->specialShipBonusBuildDestroy . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Antraxlevel:</b> <input type=\"text\" name=\"edit_bonus_antrax_food_" . $item->id . "\" value=\"" . $item->specialShipBonusAnthraxFood . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Deaktivierlevel:</b> <input type=\"text\" name=\"edit_bonus_deactivade_" . $item->id . "\" value=\"" . $item->specialShipBonusDeactivate . "\" size=\"5\" maxlength=\"20\" />";
                } else
                    $out = "";
                $objResponse->assign("special_" . $listId, "innerHTML", $out);
                $out = "<input type=\"button\" value=\"Speichern\" onclick=\"showLoader('actions_" . $item->id . "');xajax_submitEditShip(xajax.getFormValues('selector')," . $item->id . ");\" /> ";
                $out .= "<input type=\"button\" value=\"Abbrechen\" onclick=\"showLoader('shipsOnPlanet');xajax_showShipsOnPlanet('" . $form['entity_id'] . "');\" />";
                $objResponse->assign("actions_" . $listId, "innerHTML", $out);
            } else {
                $objResponse->assign("actions_" . $item->id, "innerHTML", "");
            }
        }
    }

    return $objResponse;
}

function editShipByShipId($form, $shipId)
{
    global $app;

    /** @var ShipRepository $shipRepository */
    $shipRepository = $app[ShipRepository::class];
    /** @var ShipDataRepository $shipDataRepostiroy */
    $shipDataRepostiroy = $app[ShipDataRepository::class];

    $objResponse = new xajaxResponse();

    $updata = explode(":", $form['entity_id']);

    $shipList = $shipRepository->findForUser((int) $updata[1]);
    if (count($shipList) > 0) {
        $ships = $shipDataRepostiroy->getAllShips(true);
        ob_start();
        tableStart();
        $out = '';
        foreach ($shipList as $item) {
            if ($item->shipId == $shipId) {
                $objResponse->assign("edit_" . $item->shipId, "style.display", '');
                $p = Planet::getById($item->entityId);
                $ship = $ships[$item->shipId];

                echo "<tr><th colspan=\"6\">" . $p . "</th></tr><tr>
                <td style=\"width:80px\" id=\"cnt_" . $item->id . "\"><input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editcnt_" . $item->id . "\" value=\"" . $item->count . "\" /></td>
                <td style=\"width:80px\" id=\"bunkered_" . $item->id . "\"><input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editbunkered_" . $item->id . "\" value=\"" . $item->bunkered . "\" /></td>
                <td>" . $ship->name . "</td>
                <td>" . ($ship->points * ($item->count + $item->bunkered)) . "</td>
                <td id=\"special_" . $item->id . "\">";
                if ($ship->specialNeedExp > 0) {
                    echo "<input type=\"hidden\" name=\"ship_special_" . $item->id . "\" value=\"1\"><input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editxp_" . $item->id . "\" value=\"" . $item->specialShipExp . "\" onkeyup=\"xajax_calcShipLevel(" . $item->id . "," . $ship->specialNeedExp . "," . $ship->specialExpFactor . ",this.value);\" /> XP,
                    Level <b><span id=\"editlevel_" . $item->id . "\">" . ShipXpCalculator::levelByXp($ship->specialNeedExp, $ship->specialExpFactor, $item->specialShipExp) . "</span></b><br/>

                    <b>Waffenlevel:</b> <input type=\"text\" name=\"edit_bonus_weapon_" . $item->id . "\" value=\"" . $item->specialShipBonusWeapon . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Strukturlevel:</b> <input type=\"text\" name=\"edit_bonus_structure_" . $item->id . "\" value=\"" . $item->specialShipBonusStructure . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Schildlevel:</b> <input type=\"text\" name=\"edit_bonus_shield_" . $item->id . "\" value=\"" . $item->specialShipBonusShield . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Heallevel:</b> <input type=\"text\" name=\"edit_bonus_heal_" . $item->id . "\" value=\"" . $item->specialShipBonusHeal . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Kapazit&auml;tlevel:</b> <input type=\"text\" name=\"edit_bonus_capacity_" . $item->id . "\" value=\"" . $item->specialShipBonusCapacity . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Speedlevel:</b> <input type=\"text\" name=\"edit_bonus_speed_" . $item->id . "\" value=\"" . $item->specialShipBonusSpeed . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Bereitschaftslevel:</b> <input type=\"text\" name=\"edit_bonus_readiness_" . $item->id . "\" value=\"" . $item->specialShipBonusReadiness . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Besatzungslevel:</b> <input type=\"text\" name=\"edit_bonus_pilots_" . $item->id . "\" value=\"" . $item->specialShipBonusPilots . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Tarnungslevel:</b> <input type=\"text\" name=\"edit_bonus_tarn_" . $item->id . "\" value=\"" . $item->specialShipBonusTarn . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Giftgaslevel:</b> <input type=\"text\" name=\"edit_bonus_antrax_" . $item->id . "\" value=\"" . $item->specialShipBonusAnthrax . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Techklaulevel:</b> <input type=\"text\" name=\"edit_bonus_forsteal_" . $item->id . "\" value=\"" . $item->specialShipBonusForSteal . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Bombardierlevel:</b> <input type=\"text\" name=\"edit_bonus_build_destroy_" . $item->id . "\" value=\"" . $item->specialShipBonusBuildDestroy . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Antraxlevel:</b> <input type=\"text\" name=\"edit_bonus_antrax_food_" . $item->id . "\" value=\"" . $item->specialShipBonusAnthraxFood . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Deaktivierlevel:</b> <input type=\"text\" name=\"edit_bonus_deactivade_" . $item->id . "\" value=\"" . $item->specialShipBonusDeactivate . "\" size=\"5\" maxlength=\"20\" />";
                }
                echo "</td><td style=\"width:180px\" id=\"actions_" . $item->id . "\" id=\"actions_" . $item->id . "\"><input type=\"button\" value=\"Speichern\" onclick=\"showLoader('actions_" . $item->shipId . "');xajax_submitEditShip(xajax.getFormValues('selector')," . $item->id . ");\" /><input type=\"button\" value=\"Löschen\" onclick=\"if (confirm('Sollen " . $item->count . " " . $ship->name . " von diesem Planeten gel&ouml;scht werden?')) {showLoaderPrepend('shipsOnPlanet');xajax_removeShipFromPlanet(xajax.getFormValues('selector')," . $item->id . ")}\" /></td></tr>";

                $out = "<input type=\"button\" value=\"Abbrechen\" onclick=\"showLoader('shipsOnPlanet');xajax_showShipsOnPlanet('" . $form['entity_id'] . "');\" />";
            } else {
                $objResponse->assign("actions_" . $item->shipId, "innerHTML", "");
            }
        }
        tableEnd();
        $objResponse->assign("actions_" . $shipId, "innerHTML", $out);
        $objResponse->assign("edit_" . $shipId, "innerHTML", ob_get_contents());
        ob_clean();
    }

    return $objResponse;
}

function calcShipLevel($slid, $base, $factor, $xp)
{
    $objResponse = new xajaxResponse();

    $objResponse->assign("editlevel_" . $slid, "innerHTML", ShipXpCalculator::levelByXp($base, $factor, $xp));
    return $objResponse;
}

function submitEditShip($form, $listId)
{
    global $app;

    /** @var ShipRepository $shipRepository */
    $shipRepository = $app[ShipRepository::class];

    $objResponse = new xajaxResponse();

    $entry = $shipRepository->find($listId);
    if ($entry !== null) {
        $entry->count = (int)$form['editcnt_' . $listId];
        $entry->bunkered = (int)$form['editbunkered_' . $listId];

        if (isset($form['ship_special_' . $listId])) {
            $entry->specialShipExp = (int)$form['editxp_' . $listId];
            $entry->specialShipBonusWeapon = (int)$form['edit_bonus_weapon_' . $listId];
            $entry->specialShipBonusStructure = (int)$form['edit_bonus_structure_' . $listId];
            $entry->specialShipBonusShield = (int)$form['edit_bonus_shield_' . $listId];
            $entry->specialShipBonusHeal = (int)$form['edit_bonus_heal_' . $listId];
            $entry->specialShipBonusCapacity = (int)$form['edit_bonus_capacity_' . $listId];
            $entry->specialShipBonusSpeed = (int)$form['edit_bonus_speed_' . $listId];
            $entry->specialShipBonusReadiness = (int)$form['edit_bonus_readiness_' . $listId];
            $entry->specialShipBonusPilots = (int)$form['edit_bonus_pilots_' . $listId];
            $entry->specialShipBonusTarn = (int)$form['edit_bonus_tarn_' . $listId];
            $entry->specialShipBonusAnthrax = (int)$form['edit_bonus_antrax_' . $listId];
            $entry->specialShipBonusForSteal = (int)$form['edit_bonus_forsteal_' . $listId];
            $entry->specialShipBonusBuildDestroy = (int)$form['edit_bonus_build_destroy_' . $listId];
            $entry->specialShipBonusAnthraxFood = (int)$form['edit_bonus_antrax_food_' . $listId];
            $entry->specialShipBonusDeactivate = (int)$form['edit_bonus_deactivade_' . $listId];
        }

        $shipRepository->saveItem($entry);
    }

    $objResponse->script("xajax_showShipsOnPlanet('" . $form['entity_id'] . "');");
    return $objResponse;
}

// Missiles

function showMissilesOnPlanet($pid)
{
    global $app;

    /** @var MissileDataRepository $missileDataRepository */
    $missileDataRepository = $app[MissileDataRepository::class];
    /** @var MissileRepository $missileRepository */
    $missileRepository = $app[MissileRepository::class];

    $objResponse = new xajaxResponse();

    if ($pid != 0) {
        [$entityId, $userId] = explode(":", $pid);
        $missileList = $missileRepository->findForUser((int) $userId, (int) $entityId);
        if (count($missileList) > 0) {
            $missileNames = $missileDataRepository->getMissileNames(true);
            $out = "<table class=\"tb\">";
            foreach ($missileList as $entry) {
                $out .= "<tr><td style=\"width:80px\" id=\"cnt_" . $entry->id . "\">" . $entry->count . "</td>
                <th>" . $missileNames[$entry->id] . "</th>
                <td style=\"width:150px\" id=\"actions_" . $entry->id . "\"><a href=\"javascript:;\" onclick=\"xajax_editMissile(xajax.getFormValues('selector')," . $entry->id . ")\">Bearbeiten</a>
                <a href=\"javascript:;\" onclick=\"if (confirm('Sollen " . $entry->count . " " . $missileNames[$entry->id] . " von diesem Planeten gel&ouml;scht werden?')) {xajax_removeMissileFromPlanet(xajax.getFormValues('selector')," . $entry->id . ")}\">L&ouml;schen</td>
                </tr>";
            }
            $out .= "</table>";
        } else {
            $out = "Keine Raketen vorhanden!";
        }
    } else {
        $out = "Planet w&auml;hlen...";
    }
    $objResponse->assign("shipsOnPlanet", "innerHTML", $out);
    return $objResponse;
}

function addMissileToPlanet($form)
{
    global $app;
    /** @var MissileRepository $missileRepository */
    $missileRepository = $app[MissileRepository::class];
    $objResponse = new xajaxResponse();

    [$entityId, $userId] = explode(":", $form['entity_id']);
    if ($userId > 0) {
        $missileRepository->addMissile((int) $form['ship_id'], (int) $form['shiplist_count'], (int) $userId, (int) $entityId);
        $objResponse->script("xajax_showMissilesOnPlanet('" . $form['entity_id'] . "')");
    } else {
        $out = "Planet unbewohnt. Kann keine Schiffe hier bauen!";
        $objResponse->assign("shipsOnPlanet", "innerHTML", $out);
    }
    return $objResponse;
}

function removeMissileFromPlanet($form, $listId)
{
    global $app;

    /** @var MissileRepository $missileRepository */
    $missileRepository = $app[MissileRepository::class];

    $objResponse = new xajaxResponse();

    $missileRepository->remove((int) $listId);
    $objResponse->script("xajax_showMissilesOnPlanet('" . $form['entity_id'] . "');");

    return $objResponse;
}

function editMissile($form, $listId)
{
    global $app;

    /** @var MissileRepository $missileRepository */
    $missileRepository = $app[MissileRepository::class];

    $objResponse = new xajaxResponse();

    [$entityId, $userId] = explode(":", $form['entity_id']);
    $missileList = $missileRepository->findForUser((int) $userId, (int) $entityId);
    if (count($missileList) > 0) {
        foreach ($missileList as $entry) {
            if ($entry->id == $listId) {
                $out = "<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editcnt_" . $entry->id . "\" value=\"" . $entry->count . "\" />";
                $objResponse->assign("cnt_" . $entry->id, "innerHTML", $out);
                $out = "<a href=\"javaScript:;\" onclick=\"xajax_submitEditMissile(xajax.getFormValues('selector')," . $entry->id . ");\">Speichern</a> ";
                $out .= "<a href=\"javaScript:;\" onclick=\"xajax_showMissilesOnPlanet('" . $form['entity_id'] . "');\">Abbrechen</a>";
                $objResponse->assign("actions_" . $entry->id, "innerHTML", $out);
            } else {
                $objResponse->assign("actions_" . $entry->id, "innerHTML", "");
            }
        }
    }

    return $objResponse;
}

function submitEditMissile($form, $listId)
{
    global $app;

    /** @var MissileRepository $missileRepository */
    $missileRepository = $app[MissileRepository::class];

    $objResponse = new xajaxResponse();

    $missileRepository->setMissileCount((int) $listId, (int) $form['editcnt_' . $listId]);
    $objResponse->script("xajax_showMissilesOnPlanet('" . $form['entity_id'] . "');");

    return $objResponse;
}


// Defense

function showDefenseOnPlanet($form)
{
    global $app;

    /** @var DefenseDataRepository $defenseDataRepository */
    $defenseDataRepository = $app[DefenseDataRepository::class];
    /** @var DefenseRepository $defenseRepository */
    $defenseRepository = $app[DefenseRepository::class];

    $objResponse = new xajaxResponse();

    [$entityId, $userId] = explode(":", $form);

    if ($entityId > 0) {
        $defenseNames = $defenseDataRepository->getDefenseNames(true);
        $defenseList = $defenseRepository->findForUser((int) $userId, (int) $entityId);
        if (count($defenseList) > 0) {
            $out = "<table class=\"tb\">";
            foreach ($defenseList as $entry) {
                $out .= "<tr><td style=\"width:80px\" id=\"cnt_" . $entry->id . "\">" . $entry->count . "</td>
                <th>" . $defenseNames[$entry->defenseId] . "</th>
                <td style=\"width:150px\" id=\"actions_" . $entry->id . "\"><a href=\"javascript:;\" onclick=\"xajax_editDefense(xajax.getFormValues('selector')," . $entry->id . ")\">Bearbeiten</a>
                <a href=\"javascript:;\" onclick=\"if (confirm('Sollen " . $entry->count . " " . $defenseNames[$entry->defenseId] . " von diesem Planeten gel&ouml;scht werden?')) {xajax_removeDefenseFromPlanet(xajax.getFormValues('selector')," . $entry->id . ")}\">L&ouml;schen</td>
                </tr>";
            }
            $out .= "</table>";
        } else {
            $out = "Keine Verteidigung vorhanden!";
        }
    } else {
        $out = "Planet w&auml;hlen...";
    }
    $objResponse->assign("shipsOnPlanet", "innerHTML", $out);
    return $objResponse;
}

function addDefenseToPlanet($form)
{
    global $app;

    /** @var DefenseRepository $defenseRepository */
    $defenseRepository = $app[DefenseRepository::class];

    $objResponse = new xajaxResponse();

    [$entityId, $userId] = explode(":", $form['entity_id']);
    if ($userId > 0) {
        $defenseRepository->addDefense((int) $form['def_id'], (int) $form['deflist_count'], (int) $userId, (int) $entityId);
        $objResponse->script("xajax_showDefenseOnPlanet('" . $form['entity_id'] . "')");
    } else {
        $out = "Planet unbewohnt. Kann keine Schiffe hier bauen!";
        $objResponse->assign("shipsOnPlanet", "innerHTML", $out);
    }
    return $objResponse;
}

function removeDefenseFromPlanet($form, $listId)
{
    global $app;

    /** @var DefenseRepository $defenseRepository */
    $defenseRepository = $app[DefenseRepository::class];

    $objResponse = new xajaxResponse();

    [$entityId] = explode(":", $form['entity_id']);
    $defenseRepository->removeEntry((int) $listId);

    $objResponse->script("xajax_showDefenseOnPlanet('" . $form['entity_id'] . "');");
    return $objResponse;
}

function editDefense($form, $listId)
{
    global $app;

    /** @var DefenseRepository $defenseRepository */
    $defenseRepository = $app[DefenseRepository::class];

    $objResponse = new xajaxResponse();
    [$entityId, $userId] = explode(":", $form['entity_id']);
    $defenseList = $defenseRepository->findForUser((int) $userId, (int) $entityId);
    foreach ($defenseList as $entry) {
        if ($entry->id == $listId) {
            $out = "<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editcnt_" . $entry->id . "\" value=\"" . $entry->count . "\" />";
            $objResponse->assign("cnt_" . $entry->id, "innerHTML", $out);
            $out = "<a href=\"javaScript:;\" onclick=\"xajax_submitEditDefense(xajax.getFormValues('selector')," . $listId . ");\">Speichern</a> ";
            $out .= "<a href=\"javaScript:;\" onclick=\"xajax_showDefenseOnPlanet('" . $form['entity_id'] . "');\">Abbrechen</a>";
            $objResponse->assign("actions_" . $entry->id, "innerHTML", $out);
        } else {
            $objResponse->assign("actions_" . $entry->id, "innerHTML", "");
        }
    }

    return $objResponse;
}

function submitEditDefense($form, $listId)
{
    global $app;

    /** @var DefenseRepository $defenseRepository */
    $defenseRepository = $app[DefenseRepository::class];

    $objResponse = new xajaxResponse();

    $defenseRepository->setDefenseCount((int) $listId, (int) $form['editcnt_' . $listId]);
    $objResponse->script("xajax_showDefenseOnPlanet('" . $form['entity_id'] . "');");

    return $objResponse;
}

// Buildings

function showBuildingsOnPlanet($form)
{
    global $app;

    /** @var BuildingRepository $buildingRepository */
    $buildingRepository = $app[BuildingRepository::class];
    /** @var BuildingDataRepository $buildingDataRepository */
    $buildingDataRepository = $app[BuildingDataRepository::class];

    $objResponse = new xajaxResponse();

    [$entityId, $userId] = explode(":", $form);
    $out =     "<script type=\"text/javascript\">document.getElementById('entity_id').selectedindex=" . $entityId . ";</script>";

    $buildTypes = Building::getBuildTypes();

    if ($entityId != 0) {
        $buildingList = $buildingRepository->findForUser((int) $userId, (int) $entityId);
        if (count($buildingList) > 0) {
            $buildingNames = $buildingDataRepository->getBuildingNames(true);

            $out .= "<table class=\"tb\" id =\"tb\">";
            foreach ($buildingList as $entry) {
                $out .= "<tr><td style=\"width:80px\" id=\"cnt_" . $entry->id . "\">" . $entry->currentLevel . "</td>
                <td style=\"width:100px\" id=\"type_" . $entry->id . "\">" . $buildTypes[$entry->buildType] . "</td>
                <td style=\"width:300px\" id=\"time_" . $entry->id . "\">";
                $out .= ($entry->endTime > 0) ? "Start: " . StringUtils::formatDate($entry->startTime) . "<br />Ende: " . StringUtils::formatDate($entry->endTime) : "";
                $out .= "</td>
                <th>" . $buildingNames[$entry->buildingId] . "</th>
                <td style=\"width:150px\" id=\"actions_" . $entry->id . "\"><a href=\"javascript:;\" onclick=\"xajax_editBuilding(xajax.getFormValues('selector')," . $entry->id . ")\">Bearbeiten</a>
                <a href=\"javascript:;\" onclick=\"if (confirm('Soll " . $buildingNames[$entry->buildingId] . " " . $entry->currentLevel . " von diesem Planeten gel&ouml;scht werden?')) {xajax_removeBuildingFromPlanet(xajax.getFormValues('selector')," . $entry->id . ")}\">L&ouml;schen</td>
                </tr>";
            }
            $out .= "</table>";
        } else {
            $out = "Keine Gebäude vorhanden!";
        }
    } else {
        $out = "Planet w&auml;hlen...";
    }
    $objResponse->assign("shipsOnPlanet", "innerHTML", $out);
    return $objResponse;
}

function addBuildingToPlanet($form)
{
    global $app;
    $objResponse = new xajaxResponse();

    [$entityId, $userId] = explode(":", $form['entity_id']);
    if ($userId > 0) {
        /** @var BuildingRepository $buildingRepository */
        $buildingRepository = $app[BuildingRepository::class];
        $buildingRepository->addBuilding((int) $form['building_id'], (int) $form['buildlist_current_level'], (int) $userId, (int) $entityId);
        $objResponse->script("xajax_showBuildingsOnPlanet('" . $form['entity_id'] . "')");
    } else {
        $out = "Planet unbewohnt. Kann keine Gebäude hier bauen!";
        $objResponse->assign("shipsOnPlanet", "innerHTML", $out);
    }
    return $objResponse;
}

function addAllBuildingToPlanet($form, $num)
{
    global $app;

    $objResponse = new xajaxResponse();

    [$entityId, $userId] = explode(":", $form['entity_id']);
    if ($userId > 0) {
        /** @var BuildingRepository $buildingRepository */
        $buildingRepository = $app[BuildingRepository::class];
        for ($i = 1; $i <= $num; $i++) {
            $buildingRepository->addBuilding($i, (int) $form['buildlist_current_level'], (int) $userId, (int) $entityId);
        }

        $objResponse->script("xajax_showBuildingsOnPlanet('" . $form['entity_id'] . "')");
    } else {
        $out = "Planet unbewohnt. Kann keine Gebäude hier bauen!";
        $objResponse->assign("shipsOnPlanet", "innerHTML", $out);
    }
    return $objResponse;
}

function removeBuildingFromPlanet($form, $listId)
{
    global $app;

    /** @var BuildingRepository $buildingRepository */
    $buildingRepository = $app[BuildingRepository::class];

    $objResponse = new xajaxResponse();

    $buildingRepository->removeEntry((int) $listId);
    $objResponse->script("xajax_showBuildingsOnPlanet('" . $form['entity_id'] . "');");

    return $objResponse;
}

function editBuilding($form, $listId)
{
    global $app;

    /** @var BuildingRepository $buildingRepository */
    $buildingRepository = $app[BuildingRepository::class];

    $objResponse = new xajaxResponse();

    [$entityId, $userId] = explode(":", $form['entity_id']);
    if ($entityId !== '') {
        $buildingList = $buildingRepository->findForUser((int) $userId, (int) $entityId);
        if (count($buildingList) > 0) {
            $buildTypes = Building::getBuildTypes();
            foreach ($buildingList as $entry) {
                if ($entry->id == $listId) {
                    ob_start();
                    echo "Start: ";
                    echo '<input type="datetime-local" value="' . ($entry->startTime > 0 ? date("Y-m-d\TH:i:s", $entry->startTime) : '') . '" step="1" name="' . "editstart_" . $entry->id . '">';
                    echo "<br />Ende: ";
                    echo '<input type="datetime-local" value="' . ($entry->endTime > 0 ? date("Y-m-d\TH:i:s", $entry->endTime) : '') . '" step="1" name="' . "editend_" . $entry->id . '">';
                    $objResponse->assign("time_" . $entry->id, "innerHTML", ob_get_clean());
                    ob_start();
                    echo '<select name="editbuildtype_' . $entry->id . '">';
                    foreach ($buildTypes as $id => $type) {
                        echo '<option value="' . $id . '"';
                        if ($id == $entry->buildType) echo ' selected';
                        echo '>' . $type . '</option>';
                    }
                    echo '</select>';
                    $objResponse->assign("type_" . $entry->id, "innerHTML", ob_get_clean());
                    $out = "<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editcnt_" . $entry->id . "\" value=\"" . $entry->currentLevel . "\" />";
                    $objResponse->assign("cnt_" . $entry->id, "innerHTML", $out);
                    $out = "<a href=\"javaScript:;\" onclick=\"xajax_submitEditBuilding(xajax.getFormValues('selector')," . $entry->id . ");\">Speichern</a> ";
                    $out .= "<a href=\"javaScript:;\" onclick=\"xajax_showBuildingsOnPlanet('" . $form['entity_id'] . "');\">Abbrechen</a>";
                    $objResponse->assign("actions_" . $entry->id, "innerHTML", $out);
                } else {
                    $objResponse->assign("actions_" . $entry->id, "innerHTML", "");
                }
            }
        }
    } else {
        $objResponse->assign("tb", "innerHTML", "Fehlerhafte Plantenid!");
    }

    return $objResponse;
}

function submitEditBuilding($form, $listId)
{
    global $app;

    /** @var BuildingRepository $buildingRepository */
    $buildingRepository = $app[BuildingRepository::class];

    $objResponse = new xajaxResponse();

    $status = intval($form['editbuildtype_' . $listId]);
    $endtime = $status > 0 ? strtotime($form['editend_' . $listId]) : '0';
    $starttime = $status > 0 ? strtotime($form['editstart_' . $listId]) : '0';

    $buildingRepository->updateBuildingListEntry((int) $listId, (int) $form['editcnt_' . $listId], $status, $starttime, $endtime);
    $objResponse->script("xajax_showBuildingsOnPlanet('" . $form['entity_id'] . "');");

    return $objResponse;
}


//Listet gefundene User auf
function searchUser($val, $field_id = 'user_nick', $box_id = 'citybox')
{
    global $app;

    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];

    $sOut = "";
    $sLastHit = null;

    $userNicks = $userRepository->searchUserNicknames(UserSearch::create()->nickLike($val), 20);
    $nCount = count($userNicks);
    foreach ($userNicks as $nick) {
        $sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('" . $field_id . "').value='" . htmlentities($nick) . "';document.getElementById('" . $box_id . "').style.display = 'none';\">" . htmlentities($nick) . "</a>";
        $sLastHit = $nick;
    }

    if ($nCount > 20) {
        $sOut = "";
    }

    $objResponse = new xajaxResponse();

    if (strlen($sOut) > 0) {
        $sOut = "" . $sOut . "";
        $objResponse->script("document.getElementById('" . $box_id . "').style.display = \"block\"");
    } else {
        $objResponse->script("document.getElementById('" . $box_id . "').style.display = \"none\"");
    }

    //Wenn nur noch ein User in frage kommt, diesen Anzeigen
    if ($nCount == 1) {
        $objResponse->script("document.getElementById('" . $box_id . "').style.display = \"none\"");
        $objResponse->script("document.getElementById('" . $field_id . "').value = \"" . $sLastHit . "\"");
    }

    $objResponse->assign($box_id, "innerHTML", $sOut);

    return $objResponse;
}


//Listet gefundene User auf (Speziel für Schiffs-, Def-, und Raketenformular)
function searchUserList($val, $function)
{
    global $app;

    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];

    $targetId = 'userlist';
    $inputId = 'userlist_nick';

    $sOut = "";
    $sLastHit = null;

    $userNicks = $userRepository->searchUserNicknames(UserSearch::create()->nickLike($val), 20);
    $nCount = count($userNicks);
    foreach ($userNicks as $nick) {
        $sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('$inputId').value='" . htmlentities($nick) . "';xajax_planetSelectorByUser('" . $nick . "','" . $function . "');document.getElementById('$targetId').style.display = 'none';\">" . htmlentities($nick) . "</a>";
        $sLastHit = $nick;
    }

    if ($nCount > 20) {
        $sOut = "";
    }

    $objResponse = new xajaxResponse();

    if (strlen($sOut) > 0) {
        $sOut = "" . $sOut . "";
        $objResponse->script("document.getElementById('$targetId').style.display = \"block\"");
    } else {
        $objResponse->script("document.getElementById('$targetId').style.display = \"none\"");
    }

    //Wenn nur noch ein User in frage kommt, diesen Anzeigen
    if ($nCount == 1) {
        $objResponse->script("document.getElementById('$targetId').style.display = \"none\"");
        $objResponse->script("document.getElementById('$inputId').value = \"" . $sLastHit . "\"");
        $objResponse->script("xajax_planetSelectorByUser('$sLastHit','" . $function . "')");
    }

    $objResponse->assign("$targetId", "innerHTML", $sOut);

    return $objResponse;
}


//Listet gefundene Allianzen auf
function searchAlliance($val, $field_id = 'alliance_name', $box_id = 'citybox')
{
    global $app;

    /** @var AllianceRepository $allianceRepository */
    $allianceRepository = $app[AllianceRepository::class];

    $sOut = "";
    $nCount = 0;
    $sLastHit = null;

    $allianceNames = $allianceRepository->getAllianceNames(AllianceSearch::create()->nameLike($val), 20);
    foreach ($allianceNames as $allianceName) {
        $nCount++;
        $sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('" . $field_id . "').value='" . htmlentities($allianceName) . "';document.getElementById('" . $box_id . "').style.display = 'none';\">" . htmlentities($allianceName) . "</a>";
        $sLastHit = $allianceName;
    }

    if ($nCount > 20) {
        $sOut = "";
    }

    $objResponse = new xajaxResponse();

    if (strlen($sOut) > 0) {
        $sOut = "" . $sOut . "";
        $objResponse->script("document.getElementById('" . $box_id . "').style.display = \"block\"");
    } else {
        $objResponse->script("document.getElementById('" . $box_id . "').style.display = \"none\"");
    }

    //Wenn nur noch ein User in frage kommt, diesen Anzeigen
    if ($nCount == 1) {
        $objResponse->script("document.getElementById('" . $box_id . "').style.display = \"none\"");
        $objResponse->script("document.getElementById('" . $field_id . "').value = \"" . $sLastHit . "\"");
    }

    $objResponse->assign($box_id, "innerHTML", $sOut);

    return $objResponse;
}

function lockUser($uid, $time, $reason)
{
    global $app;

    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];

    $t1 = time();
    $t2 = $t1 + $time;
    $userRepository->blockUser($uid, $t1, $t2, $reason, $_SESSION[SESSION_NAME]['user_id']);
    $objResponse = new xajaxResponse();
    $objResponse->alert("Der Benutzer wurde gesperrt!");
    return $objResponse;
}

/***********/

function buildingPrices($id, $lvl)
{
    global $app;

    /** @var BuildingDataRepository $buildingRepository */
    $buildingRepository = $app[BuildingDataRepository::class];

    $objResponse = new xajaxResponse();
    $building = $buildingRepository->getBuilding($id);
    $bc = calcBuildingCosts($building, $lvl);
    $objResponse->assign("c1_metal", "innerHTML", StringUtils::formatNumber($bc['metal']));
    $objResponse->assign("c1_crystal", "innerHTML", StringUtils::formatNumber($bc['crystal']));
    $objResponse->assign("c1_plastic", "innerHTML", StringUtils::formatNumber($bc['plastic']));
    $objResponse->assign("c1_fuel", "innerHTML", StringUtils::formatNumber($bc['fuel']));
    $objResponse->assign("c1_food", "innerHTML", StringUtils::formatNumber($bc['food']));
    $objResponse->assign("c1_power", "innerHTML", StringUtils::formatNumber($bc['power']));

    return $objResponse;
}

function totalBuildingPrices($form)
{
    global $app;

    /** @var BuildingDataRepository $buildingRepository */
    $buildingRepository = $app[BuildingDataRepository::class];

    $objResponse = new xajaxResponse();
    $bctt = array();
    foreach ($form['b_lvl'] as $id => $lvl) {
        $building = $buildingRepository->getBuilding($id);
        $bct = array();
        for ($x = 0; $x < $lvl; $x++) {
            $bc = calcBuildingCosts($building, $x);
            $bct['metal'] += $bc['metal'];
            $bct['crystal'] += $bc['crystal'];
            $bct['plastic'] += $bc['plastic'];
            $bct['fuel'] += $bc['fuel'];
            $bct['food'] += $bc['food'];
        }
        $bctt['metal'] += $bct['metal'];
        $bctt['crystal'] += $bct['crystal'];
        $bctt['plastic'] += $bct['plastic'];
        $bctt['fuel'] += $bct['fuel'];
        $bctt['food'] += $bct['food'];
        $objResponse->assign("b_metal_" . $id, "innerHTML", StringUtils::formatNumber($bct['metal']));
        $objResponse->assign("b_crystal_" . $id, "innerHTML", StringUtils::formatNumber($bct['crystal']));
        $objResponse->assign("b_plastic_" . $id, "innerHTML", StringUtils::formatNumber($bct['plastic']));
        $objResponse->assign("b_fuel_" . $id, "innerHTML", StringUtils::formatNumber($bct['fuel']));
        $objResponse->assign("b_food_" . $id, "innerHTML", StringUtils::formatNumber($bct['food']));
    }
    $objResponse->assign("t_metal", "innerHTML", StringUtils::formatNumber($bctt['metal']));
    $objResponse->assign("t_crystal", "innerHTML", StringUtils::formatNumber($bctt['crystal']));
    $objResponse->assign("t_plastic", "innerHTML", StringUtils::formatNumber($bctt['plastic']));
    $objResponse->assign("t_fuel", "innerHTML", StringUtils::formatNumber($bctt['fuel']));
    $objResponse->assign("t_food", "innerHTML", StringUtils::formatNumber($bctt['food']));


    return $objResponse;
}

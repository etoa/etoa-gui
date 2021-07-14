<?PHP

use EtoA\Building\BuildingRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Missile\MissileRepository;
use EtoA\Ship\ShipRepository;

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
$xajax->register(XAJAX_FUNCTION, "searchPlanet");
$xajax->register(XAJAX_FUNCTION, "lockUser");

$xajax->register(XAJAX_FUNCTION, "buildingPrices");
$xajax->register(XAJAX_FUNCTION, "totalBuildingPrices");

$xajax->register(XAJAX_FUNCTION, "reqInfo");

function planetSelectorByCell($form, $function, $show_user_id = 0)
{
    $objResponse = new xajaxResponse();
    $out = '';
    if ($form['cell_sx'] != 0 && $form['cell_sy'] != 0 && $form['cell_cx'] != 0 && $form['cell_cy'] != 0) {
        $res = dbquery("
        SELECT
            entities.id,
            cells.sx,
            cells.sy,
            cells.cx,
            cells.cy,
            entities.pos,
            entities.code
        FROM
            entities
        INNER JOIN
            cells
        ON
            entities.cell_id=cells.id
        AND
            cells.sx='" . $form['cell_sx'] . "'
        AND
            cells.sy='" . $form['cell_sy'] . "'
        AND
            cells.cx='" . $form['cell_cx'] . "'
        AND
            cells.cy='" . $form['cell_cy'] . "'
        ;");

        $nr = mysql_num_rows($res);
        if ($nr > 0) {
            if ($nr > 1) {
                $cnt = 0;
                $entities = array();
                $ids = "";
                while ($arr = mysql_fetch_row($res)) {
                    if ($arr[6] == 'p') {
                        if ($cnt != 0) $ids .= ",";
                        $ids .= $arr[0];
                        $cnt++;
                        $entities[$arr[0]] = $arr[5];
                    }
                }

                $pres = dbquery("
                SELECT
                    id,
                    planet_name,
                    planet_user_id,
                    user_nick
                FROM
                    planets
                LEFT JOIN
                    users
                    ON planet_user_id=user_id
                WHERE
                    planets.id IN (" . $ids . ");
                ");
                $nr = mysql_num_rows($pres);
                if ($nr > 0) {
                    $out = "<select name=\"entity_id\" size=\"" . ($nr) . "\" onchange=\"showLoader('shipsOnPlanet');xajax_" . $function . "(this.options[this.selectedIndex].value);\">\n";
                    while ($parr = mysql_fetch_array($pres)) {
                        $name = ($parr['planet_name'] == "") ? "Unbennant" : $parr['planet_name'];
                        if ($show_user_id == 1) {
                            $val = $parr['id'] . ":" . $parr['planet_user_id'];
                        } else {
                            $val = $parr['id'];
                        }
                        if ($parr['planet_user_id'] > 0)
                            $out .= "<option value=\"$val\">" . $entities[$parr['id']] . " " . $name . " (" . $parr['user_nick'] . ")</option>";
                        else
                            $out .= "<option value=\"$val\" style=\"font-style:italic\">" . $entities[$parr['id']] . " " . $name . " Unbewohnt</option>";
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
    $objResponse = new xajaxResponse();
    if ($userNick != "") {
        $pres = dbquery("
        SELECT
            id,
            planet_user_id
        FROM
            planets
        INNER JOIN
            users
        ON planet_user_id=user_id
            AND user_nick='$userNick'
        ORDER BY
            planets.planet_user_main DESC,
            planets.id ASC
            ;
        ");
        $nr = mysql_num_rows($pres);
        if ($nr > 0) {
            $out = "<select name=\"entity_id\" size=\"" . ($nr + 1) . "\" onchange=\"showLoader('shipsOnPlanet');xajax_" . $function . "(this.options[this.selectedIndex].value);\">\n";
            $cnt = 0;
            $val = '';
            while ($parr = mysql_fetch_row($pres)) {
                if ($cnt === 0) {
                    $out .= "<option value=\"0:" . $parr[1] . "\">Alle</option>";
                    $cnt++;
                }
                $p = Planet::getById($parr[0]);

                if ($show_user_id == 1) {
                    $val = $parr[0] . ":" . $parr[1];
                } else {
                    $val = $parr[0] . ":" . "0";
                }
                $out .= "<option value=\"$val\">" . $p . "</option>\n";
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
    $objResponse = new xajaxResponse();

    $updata = explode(":", $form);
    $eid = $updata[0];
    $uid = $updata[1];

    $style = 'none';

    ob_start();

    if ($eid != 0) {
        $res = dbquery("
        SELECT
            ship_points,
            ship_name,
            shiplist_count,
            shiplist_bunkered,
            shiplist_id,
            special_ship_need_exp as ship_xp_base,
            special_ship_exp_factor as ship_xp_factor,
            shiplist_special_ship_exp as shiplist_xp
        FROM
            shiplist
        INNER JOIN
            ships
            ON shiplist_ship_id=ship_id
            AND shiplist_entity_id='" . $eid . "'
            AND (shiplist_count+shiplist_bunkered)>0
        ORDER BY
            ship_name
        ;");

        if (mysql_num_rows($res) > 0) {
            $out = "<table class=\"tb\">
            <tr><th>Anzahl</th>
            <th>Bunker</th>
            <th>Typ</th>
            <th>Punkte</th>
            <th>Spezielles</th>
            <th>Aktionen</th></tr>";
            $points = 0;
            while ($arr = mysql_fetch_array($res)) {
                $points += $arr['ship_points'] * ($arr['shiplist_count'] + $arr['shiplist_bunkered']);
                $out .= "<tr><td style=\"width:80px\" id=\"cnt_" . $arr['shiplist_id'] . "\">" . $arr['shiplist_count'] . "</td>
                <td style=\"width:80px\" id=\"bunkered_" . $arr['shiplist_id'] . "\">" . $arr['shiplist_bunkered'] . "</td>
                <td>" . $arr['ship_name'] . "</td>
                <td>" . ($arr['ship_points'] * ($arr['shiplist_count'] + $arr['shiplist_bunkered'])) . "</td>
                <td id=\"special_" . $arr['shiplist_id'] . "\">";
                if ($arr['ship_xp_base'] > 0) {
                    $out .= nf($arr['shiplist_xp']) . " XP, Level " . Ship::levelByXp($arr['ship_xp_base'], $arr['ship_xp_factor'], $arr['shiplist_xp']);
                }
                $out .= "
                <td style=\"width:180px\" id=\"actions_" . $arr['shiplist_id'] . "\" id=\"actions_" . $arr['shiplist_id'] . "\">
                <input type=\"button\" value=\"Bearbeiten\" onclick=\"xajax_editShipByListId(xajax.getFormValues('selector')," . $arr['shiplist_id'] . ")\" />
                <input type=\"button\" value=\"Löschen\" onclick=\"if (confirm('Sollen " . $arr['shiplist_count'] . " " . $arr['ship_name'] . " von diesem Planeten gel&ouml;scht werden?')) {showLoaderPrepend('shipsOnPlanet');xajax_removeShipFromPlanet(xajax.getFormValues('selector')," . $arr['shiplist_id'] . ")}\" />
                </td>
                </tr>";
            }
            $out .= "<tr><td colspan=\"3\"></td><td><b>" . nf($points) . "</b></td><td colspan=\"2\"></td></tr>";
            $out .= "</table>";
        } else {
            $out = "Keine Schiffe vorhanden!<br/>";
        }
        $out .= "<br/><input type=\"Button\" value=\"Neu laden\" onclick=\"showLoader('shipsOnPlanet');xajax_showShipsOnPlanet('" . $form . "');\">";
        $style = '';
    } elseif ($uid != 0) {
        $res = dbquery("
        SELECT
            ship_points,
            ship_name,
            SUM(shiplist_count) AS cnt,
            SUM(shiplist_bunkered) AS bunkered,
            ship_id,
            special_ship_need_exp as ship_xp_base,
            special_ship_exp_factor as ship_xp_factor,
            shiplist_special_ship_exp as shiplist_xp
        FROM
            shiplist
        INNER JOIN
            ships
            ON shiplist_ship_id=ship_id
            AND shiplist_user_id='" . $uid . "'
            AND shiplist_count>0
        GROUP BY
            ship_id
        ORDER BY
            ship_name
        ;");

        if (mysql_num_rows($res) > 0) {
            $out = "<table class=\"tb\">
            <tr><th>Anzahl</th>
            <th>Bunker</th>
            <th>Typ</th>
            <th>Punkte</th>
            <th>Spezielles</th>
            <th>Aktionen</th></tr>";
            $points = 0;
            while ($arr = mysql_fetch_array($res)) {
                $points += $arr['ship_points'] * ($arr['cnt'] + $arr['bunkered']);
                $out .= "<tr id=\"data_" . $arr['ship_id'] . "\"><td style=\"width:80px\" id=\"cnt_" . $arr['ship_id'] . "\">" . $arr['cnt'] . "</td>
                <td style=\"width:80px\" id=\"bunkered_" . $arr['ship_id'] . "\">" . $arr['bunkered'] . "</td>
                <td>" . $arr['ship_name'] . "</td>
                <td>" . ($arr['ship_points'] * $arr['cnt']) . "</td>
                <td id=\"special_" . $arr['ship_id'] . "\">";
                if ($arr['ship_xp_base'] > 0) {
                    $out .= nf($arr['shiplist_xp']) . " XP, Level " . Ship::levelByXp($arr['ship_xp_base'], $arr['ship_xp_factor'], $arr['shiplist_xp']);
                }
                $out .= "
                <td style=\"width:180px\" id=\"actions_" . $arr['ship_id'] . "\" id=\"actions_" . $arr['ship_id'] . "\">
                <input type=\"button\" value=\"Bearbeiten\" onclick=\"xajax_editShipByShipId(xajax.getFormValues('selector')," . $arr['ship_id'] . ")\" />
                </td></tr>
                <tr><td colspan=\"6\" id=\"edit_" . $arr['ship_id'] . "\" style=\"display:none;\"></td></tr>";
            }
            $out .= "<tr><td colspan=\"3\"></td><td><b>" . nf($points) . "</b></td><td colspan=\"2\"></td></tr>";
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
        /** @var ShipRepository */
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
    $objResponse = new xajaxResponse();

    dbquery("
    DELETE FROM
        shiplist
    WHERE
        shiplist_id=" . intval($listId) . "
    ;");
    $objResponse->script("xajax_showShipsOnPlanet('" . $form['entity_id'] . "');");
    return $objResponse;
}

function editShipByListId($form, $listId)
{
    $objResponse = new xajaxResponse();

    $updata = explode(":", $form['entity_id']);

    $res = dbquery("
        SELECT
            shiplist_count,
            shiplist_bunkered,
            shiplist_id,
            special_ship_need_exp as ship_xp_base,
            special_ship_exp_factor as ship_xp_factor,
            shiplist_special_ship_exp as shiplist_xp,
            shiplist_special_ship_bonus_weapon,
            shiplist_special_ship_bonus_structure,
            shiplist_special_ship_bonus_shield,
            shiplist_special_ship_bonus_heal,
            shiplist_special_ship_bonus_capacity,
            shiplist_special_ship_bonus_speed,
            shiplist_special_ship_bonus_readiness,
            shiplist_special_ship_bonus_pilots,
            shiplist_special_ship_bonus_tarn,
            shiplist_special_ship_bonus_antrax,
            shiplist_special_ship_bonus_forsteal,
            shiplist_special_ship_bonus_build_destroy,
            shiplist_special_ship_bonus_antrax_food,
            shiplist_special_ship_bonus_deactivade
        FROM
            shiplist
        INNER JOIN
            ships
            ON shiplist_ship_id=ship_id
        AND
            shiplist_entity_id=" . $updata[0] . "
        ;");

    if (mysql_num_rows($res)) {
        while ($arr = mysql_fetch_array($res)) {
            if ($arr['shiplist_id'] == $listId) {
                $out = "<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editcnt_" . $listId . "\" value=\"" . $arr['shiplist_count'] . "\" />";
                $objResponse->assign("cnt_" . $listId, "innerHTML", $out);
                $out = "<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editbunkered_" . $listId . "\" value=\"" . $arr['shiplist_bunkered'] . "\" />";
                $objResponse->assign("bunkered_" . $listId, "innerHTML", $out);
                if ($arr['ship_xp_base'] > 0) {
                    $out = "<input type=\"hidden\" name=\"ship_special_" . $listId . "\" value=\"1\"><input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editxp_" . $listId . "\" value=\"" . $arr['shiplist_xp'] . "\" onkeyup=\"xajax_calcShipLevel(" . $listId . "," . $arr['ship_xp_base'] . "," . $arr['ship_xp_factor'] . ",this.value);\" /> XP,
                    Level <b><span id=\"editlevel_" . $listId . "\">" . Ship::levelByXp($arr['ship_xp_base'], $arr['ship_xp_factor'], $arr['shiplist_xp']) . "</span></b><br/>

                    <b>Waffenlevel:</b> <input type=\"text\" name=\"edit_bonus_weapon_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_weapon'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Strukturlevel:</b> <input type=\"text\" name=\"edit_bonus_structure_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_structure'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Schildlevel:</b> <input type=\"text\" name=\"edit_bonus_shield_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_shield'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Heallevel:</b> <input type=\"text\" name=\"edit_bonus_heal_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_heal'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Kapazit&auml;tlevel:</b> <input type=\"text\" name=\"edit_bonus_capacity_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_capacity'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Speedlevel:</b> <input type=\"text\" name=\"edit_bonus_speed_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_speed'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Bereitschaftslevel:</b> <input type=\"text\" name=\"edit_bonus_readiness_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_readiness'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Besatzungslevel:</b> <input type=\"text\" name=\"edit_bonus_pilots_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_pilots'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Tarnungslevel:</b> <input type=\"text\" name=\"edit_bonus_tarn_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_tarn'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Giftgaslevel:</b> <input type=\"text\" name=\"edit_bonus_antrax_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_antrax'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Techklaulevel:</b> <input type=\"text\" name=\"edit_bonus_forsteal_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_forsteal'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Bombardierlevel:</b> <input type=\"text\" name=\"edit_bonus_build_destroy_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_build_destroy'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Antraxlevel:</b> <input type=\"text\" name=\"edit_bonus_antrax_food_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_antrax_food'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Deaktivierlevel:</b> <input type=\"text\" name=\"edit_bonus_deactivade_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_deactivade'] . "\" size=\"5\" maxlength=\"20\" />";
                } else
                    $out = "";
                $objResponse->assign("special_" . $listId, "innerHTML", $out);
                $out = "<input type=\"button\" value=\"Speichern\" onclick=\"showLoader('actions_" . $listId . "');xajax_submitEditShip(xajax.getFormValues('selector')," . $listId . ");\" /> ";
                $out .= "<input type=\"button\" value=\"Abbrechen\" onclick=\"showLoader('shipsOnPlanet');xajax_showShipsOnPlanet('" . $form['entity_id'] . "');\" />";
                $objResponse->assign("actions_" . $listId, "innerHTML", $out);
            } else {
                $objResponse->assign("actions_" . $arr['shiplist_id'], "innerHTML", "");
            }
        }
    }

    return $objResponse;
}

function editShipByShipId($form, $shipId)
{
    $objResponse = new xajaxResponse();

    $updata = explode(":", $form['entity_id']);

    $res = dbquery("
        SELECT
            shiplist_count,
            shiplist_bunkered,
            shiplist_id,
            ship_id,
            ship_name,
            ship_points,
            shiplist_entity_id,
            special_ship_need_exp as ship_xp_base,
            special_ship_exp_factor as ship_xp_factor,
            shiplist_special_ship_exp as shiplist_xp,
            shiplist_special_ship_bonus_weapon,
            shiplist_special_ship_bonus_structure,
            shiplist_special_ship_bonus_shield,
            shiplist_special_ship_bonus_heal,
            shiplist_special_ship_bonus_capacity,
            shiplist_special_ship_bonus_speed,
            shiplist_special_ship_bonus_readiness,
            shiplist_special_ship_bonus_pilots,
            shiplist_special_ship_bonus_tarn,
            shiplist_special_ship_bonus_antrax,
            shiplist_special_ship_bonus_forsteal,
            shiplist_special_ship_bonus_build_destroy,
            shiplist_special_ship_bonus_antrax_food,
            shiplist_special_ship_bonus_deactivade
        FROM
            shiplist
        INNER JOIN
            ships
            ON shiplist_ship_id=ship_id
        AND
            shiplist_user_id=" . $updata[1] . "
        AND
            shiplist_count!=0
        ;");

    if (mysql_num_rows($res)) {
        ob_start();
        tableStart();
        $out = '';
        while ($arr = mysql_fetch_array($res)) {
            if ($arr['ship_id'] == $shipId) {
                $objResponse->assign("edit_" . $shipId, "style.display", '');

                $p = Planet::getById($arr['shiplist_entity_id']);
                $listId = $arr['shiplist_id'];

                echo "<tr><th colspan=\"6\">" . $p . "</th></tr><tr>
                <td style=\"width:80px\" id=\"cnt_" . $arr['shiplist_id'] . "\"><input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editcnt_" . $listId . "\" value=\"" . $arr['shiplist_count'] . "\" /></td>
                <td style=\"width:80px\" id=\"bunkered_" . $arr['shiplist_id'] . "\"><input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editbunkered_" . $listId . "\" value=\"" . $arr['shiplist_bunkered'] . "\" /></td>
                <td>" . $arr['ship_name'] . "</td>
                <td>" . ($arr['ship_points'] * ($arr['shiplist_count'] + $arr['shiplist_bunkered'])) . "</td>
                <td id=\"special_" . $listId . "\">";
                if ($arr['ship_xp_base'] > 0) {
                    echo "<input type=\"hidden\" name=\"ship_special_" . $listId . "\" value=\"1\"><input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editxp_" . $listId . "\" value=\"" . $arr['shiplist_xp'] . "\" onkeyup=\"xajax_calcShipLevel(" . $listId . "," . $arr['ship_xp_base'] . "," . $arr['ship_xp_factor'] . ",this.value);\" /> XP,
                    Level <b><span id=\"editlevel_" . $listId . "\">" . Ship::levelByXp($arr['ship_xp_base'], $arr['ship_xp_factor'], $arr['shiplist_xp']) . "</span></b><br/>

                    <b>Waffenlevel:</b> <input type=\"text\" name=\"edit_bonus_weapon_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_weapon'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Strukturlevel:</b> <input type=\"text\" name=\"edit_bonus_structure_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_structure'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Schildlevel:</b> <input type=\"text\" name=\"edit_bonus_shield_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_shield'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Heallevel:</b> <input type=\"text\" name=\"edit_bonus_heal_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_heal'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Kapazit&auml;tlevel:</b> <input type=\"text\" name=\"edit_bonus_capacity_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_capacity'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Speedlevel:</b> <input type=\"text\" name=\"edit_bonus_speed_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_speed'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Bereitschaftslevel:</b> <input type=\"text\" name=\"edit_bonus_readiness_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_readiness'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Besatzungslevel:</b> <input type=\"text\" name=\"edit_bonus_pilots_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_pilots'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Tarnungslevel:</b> <input type=\"text\" name=\"edit_bonus_tarn_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_tarn'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Giftgaslevel:</b> <input type=\"text\" name=\"edit_bonus_antrax_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_antrax'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Techklaulevel:</b> <input type=\"text\" name=\"edit_bonus_forsteal_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_forsteal'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Bombardierlevel:</b> <input type=\"text\" name=\"edit_bonus_build_destroy_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_build_destroy'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Antraxlevel:</b> <input type=\"text\" name=\"edit_bonus_antrax_food_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_antrax_food'] . "\" size=\"5\" maxlength=\"20\" /><br/>
                    <b>Deaktivierlevel:</b> <input type=\"text\" name=\"edit_bonus_deactivade_" . $listId . "\" value=\"" . $arr['shiplist_special_ship_bonus_deactivade'] . "\" size=\"5\" maxlength=\"20\" />";
                }
                echo "</td><td style=\"width:180px\" id=\"actions_" . $arr['shiplist_id'] . "\" id=\"actions_" . $arr['shiplist_id'] . "\"><input type=\"button\" value=\"Speichern\" onclick=\"showLoader('actions_" . $shipId . "');xajax_submitEditShip(xajax.getFormValues('selector')," . $listId . ");\" /><input type=\"button\" value=\"Löschen\" onclick=\"if (confirm('Sollen " . $arr['shiplist_count'] . " " . $arr['ship_name'] . " von diesem Planeten gel&ouml;scht werden?')) {showLoaderPrepend('shipsOnPlanet');xajax_removeShipFromPlanet(xajax.getFormValues('selector')," . $listId . ")}\" /></td></tr>";

                $out = "<input type=\"button\" value=\"Abbrechen\" onclick=\"showLoader('shipsOnPlanet');xajax_showShipsOnPlanet('" . $form['entity_id'] . "');\" />";
            } else {
                $objResponse->assign("actions_" . $arr['ship_id'], "innerHTML", "");
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

    $objResponse->assign("editlevel_" . $slid, "innerHTML", Ship::levelByXp($base, $factor, $xp));
    return $objResponse;
}

function submitEditShip($form, $listId)
{
    $objResponse = new xajaxResponse();

    if (isset($form['ship_special_' . $listId])) {
        dbquery("
        UPDATE
            shiplist
        SET
            shiplist_count=" . intval($form['editcnt_' . $listId]) . ",
            shiplist_bunkered=" . intval($form['editbunkered_' . $listId]) . ",
            shiplist_special_ship_exp=" . intval($form['editxp_' . $listId]) . ",
            shiplist_special_ship_bonus_weapon='" . intval($form['edit_bonus_weapon_' . $listId]) . "',
            shiplist_special_ship_bonus_structure='" . intval($form['edit_bonus_structure_' . $listId]) . "',
            shiplist_special_ship_bonus_shield='" . intval($form['edit_bonus_shield_' . $listId]) . "',
            shiplist_special_ship_bonus_heal='" . intval($form['edit_bonus_heal_' . $listId]) . "',
            shiplist_special_ship_bonus_capacity='" . intval($form['edit_bonus_capacity_' . $listId]) . "',
            shiplist_special_ship_bonus_speed='" . intval($form['edit_bonus_speed_' . $listId]) . "',
            shiplist_special_ship_bonus_readiness='" . intval($form['edit_bonus_readiness_' . $listId]) . "',
            shiplist_special_ship_bonus_pilots='" . intval($form['edit_bonus_pilots_' . $listId]) . "',
            shiplist_special_ship_bonus_tarn='" . intval($form['edit_bonus_tarn_' . $listId]) . "',
            shiplist_special_ship_bonus_antrax='" . intval($form['edit_bonus_antrax_' . $listId]) . "',
            shiplist_special_ship_bonus_forsteal='" . intval($form['edit_bonus_forsteal_' . $listId]) . "',
            shiplist_special_ship_bonus_build_destroy='" . intval($form['edit_bonus_build_destroy_' . $listId]) . "',
            shiplist_special_ship_bonus_antrax_food='" . intval($form['edit_bonus_antrax_food_' . $listId]) . "',
            shiplist_special_ship_bonus_deactivade='" . intval($form['edit_bonus_deactivade_' . $listId]) . "'
        WHERE
            shiplist_id=" . intval($listId) . "
        ;");
    } else {
        dbquery("
        UPDATE
            shiplist
        SET
            shiplist_count=" . intval($form['editcnt_' . $listId]) . ",
            shiplist_bunkered=" . intval($form['editbunkered_' . $listId]) . "
        WHERE
            shiplist_id=" . intval($listId) . "
        ;");
    }

    $objResponse->script("xajax_showShipsOnPlanet('" . $form['entity_id'] . "');");
    return $objResponse;
}

// Missiles

function showMissilesOnPlanet($pid)
{
    $objResponse = new xajaxResponse();

    if ($pid != 0) {
        $updata = explode(":", $pid);
        $pid = $updata[0];
        $res = dbquery("
        SELECT
            missile_name,
            missilelist_count,
            missilelist_id
        FROM
            missilelist
        INNER JOIN
            missiles
            ON missilelist_missile_id=missile_id
            AND missilelist_entity_id='" . $pid . "'
        ORDER BY
            missile_name
        ;");
        if (mysql_num_rows($res) > 0) {
            $out = "<table class=\"tb\">";
            while ($arr = mysql_fetch_array($res)) {
                $out .= "<tr><td style=\"width:80px\" id=\"cnt_" . $arr['missilelist_id'] . "\">" . $arr['missilelist_count'] . "</td>
                <th>" . $arr['missile_name'] . "</th>
                <td style=\"width:150px\" id=\"actions_" . $arr['missilelist_id'] . "\"><a href=\"javascript:;\" onclick=\"xajax_editMissile(xajax.getFormValues('selector')," . $arr['missilelist_id'] . ")\">Bearbeiten</a>
                <a href=\"javascript:;\" onclick=\"if (confirm('Sollen " . $arr['missilelist_count'] . " " . $arr['missile_name'] . " von diesem Planeten gel&ouml;scht werden?')) {xajax_removeMissileFromPlanet(xajax.getFormValues('selector')," . $arr['missilelist_id'] . ")}\">L&ouml;schen</td>
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
    /** @var MissileRepository */
    $missileRepository = $app[MissileRepository::class];
    $objResponse = new xajaxResponse();

    $updata = explode(":", $form['entity_id']);
    if ($updata[1] > 0) {
        $missileRepository->addMissile((int) $form['ship_id'], (int) $form['shiplist_count'], (int) $updata[1], (int) $updata[0]);
        $objResponse->script("xajax_showMissilesOnPlanet(" . $updata[0] . ")");
    } else {
        $out = "Planet unbewohnt. Kann keine Schiffe hier bauen!";
        $objResponse->assign("shipsOnPlanet", "innerHTML", $out);
    }
    return $objResponse;
}

function removeMissileFromPlanet($form, $listId)
{
    $objResponse = new xajaxResponse();

    $updata = explode(":", $form['entity_id']);
    dbquery("
    DELETE FROM
        missilelist
    WHERE
        missilelist_id=" . intval($listId) . "
    ;");
    $objResponse->script("xajax_showMissilesOnPlanet(" . $updata[0] . ");");
    return $objResponse;
}

function editMissile($form, $listId)
{
    $objResponse = new xajaxResponse();

    $updata = explode(":", $form['entity_id']);
    $res = dbquery("
    SELECT
        missilelist_count,
        missilelist_id
    FROM
        missilelist
    WHERE
        missilelist_entity_id=" . $updata[0] . "
    ;");
    if (mysql_num_rows($res)) {
        while ($arr = mysql_fetch_array($res)) {
            if ($arr['missilelist_id'] == $listId) {
                $out = "<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editcnt_" . $listId . "\" value=\"" . $arr['missilelist_count'] . "\" />";
                $objResponse->assign("cnt_" . $listId, "innerHTML", $out);
                $out = "<a href=\"javaScript:;\" onclick=\"xajax_submitEditMissile(xajax.getFormValues('selector')," . $listId . ");\">Speichern</a> ";
                $out .= "<a href=\"javaScript:;\" onclick=\"xajax_showMissilesOnPlanet(" . $updata[0] . ");\">Abbrechen</a>";
                $objResponse->assign("actions_" . $listId, "innerHTML", $out);
            } else {
                $objResponse->assign("actions_" . $arr['missilelist_id'], "innerHTML", "");
            }
        }
    }

    return $objResponse;
}

function submitEditMissile($form, $listId)
{
    $objResponse = new xajaxResponse();

    $updata = explode(":", $form['entity_id']);
    dbquery("
    UPDATE
        missilelist
    SET
        missilelist_count=" . intval($form['editcnt_' . $listId]) . "
    WHERE
        missilelist_id=" . intval($listId) . "
    ;");
    $objResponse->script("xajax_showMissilesOnPlanet(" . $updata[0] . ");");
    return $objResponse;
}


// Defense

function showDefenseOnPlanet($form)
{
    $objResponse = new xajaxResponse();

    $updata = explode(":", $form);
    $eid = $updata[0];


    if ($eid != 0) {
        $res = dbquery("
        SELECT
            def_name,
            deflist_count,
            deflist_id
        FROM
            deflist
        INNER JOIN
            defense
            ON deflist_def_id=def_id
            AND deflist_entity_id='" . $eid . "'
        ORDER BY
            def_name
        ;");
        if (mysql_num_rows($res) > 0) {
            $out = "<table class=\"tb\">";
            while ($arr = mysql_fetch_array($res)) {
                $out .= "<tr><td style=\"width:80px\" id=\"cnt_" . $arr['deflist_id'] . "\">" . $arr['deflist_count'] . "</td>
                <th>" . $arr['def_name'] . "</th>
                <td style=\"width:150px\" id=\"actions_" . $arr['deflist_id'] . "\"><a href=\"javascript:;\" onclick=\"xajax_editDefense(xajax.getFormValues('selector')," . $arr['deflist_id'] . ")\">Bearbeiten</a>
                <a href=\"javascript:;\" onclick=\"if (confirm('Sollen " . $arr['deflist_count'] . " " . $arr['def_name'] . " von diesem Planeten gel&ouml;scht werden?')) {xajax_removeDefenseFromPlanet(xajax.getFormValues('selector')," . $arr['deflist_id'] . ")}\">L&ouml;schen</td>
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

    /** @var DefenseRepository */
    $defenseRepository = $app[DefenseRepository::class];

    $objResponse = new xajaxResponse();

    $updata = explode(":", $form['entity_id']);
    if ($updata[1] > 0) {
        $defenseRepository->addDefense((int) $form['def_id'], (int) $form['deflist_count'], (int) $updata[1], (int) $updata[0]);
        $objResponse->script("xajax_showDefenseOnPlanet('" . $form['entity_id'] . "')");
    } else {
        $out = "Planet unbewohnt. Kann keine Schiffe hier bauen!";
        $objResponse->assign("shipsOnPlanet", "innerHTML", $out);
    }
    return $objResponse;
}

function removeDefenseFromPlanet($form, $listId)
{
    $objResponse = new xajaxResponse();

    $updata = explode(":", $form['entity_id']);
    dbquery("
    DELETE FROM
        deflist
    WHERE
        deflist_id=" . intval($listId) . "
    ;");
    $objResponse->script("xajax_showDefenseOnPlanet(" . $updata[0] . ");");
    return $objResponse;
}

function editDefense($form, $listId)
{
    $objResponse = new xajaxResponse();

    $updata = explode(":", $form['entity_id']);
    $res = dbquery("
    SELECT
        deflist_count,
        deflist_id
    FROM
        deflist
    WHERE
        deflist_entity_id=" . $updata[0] . "
    ;");
    if (mysql_num_rows($res)) {
        while ($arr = mysql_fetch_array($res)) {
            if ($arr['deflist_id'] == $listId) {
                $out = "<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editcnt_" . $listId . "\" value=\"" . $arr['deflist_count'] . "\" />";
                $objResponse->assign("cnt_" . $listId, "innerHTML", $out);
                $out = "<a href=\"javaScript:;\" onclick=\"xajax_submitEditDefense(xajax.getFormValues('selector')," . $listId . ");\">Speichern</a> ";
                $out .= "<a href=\"javaScript:;\" onclick=\"xajax_showDefenseOnPlanet(" . $updata[0] . ");\">Abbrechen</a>";
                $objResponse->assign("actions_" . $listId, "innerHTML", $out);
            } else {
                $objResponse->assign("actions_" . $arr['deflist_id'], "innerHTML", "");
            }
        }
    }

    return $objResponse;
}

function submitEditDefense($form, $listId)
{
    $objResponse = new xajaxResponse();

    $updata = explode(":", $form['entity_id']);
    dbquery("
    UPDATE
        deflist
    SET
        deflist_count=" . intval($form['editcnt_' . $listId]) . "
    WHERE
        deflist_id=" . intval($listId) . "
    ;");
    $objResponse->script("xajax_showDefenseOnPlanet(" . $updata[0] . ");");
    return $objResponse;
}

// Buildings

function showBuildingsOnPlanet($form)
{
    $objResponse = new xajaxResponse();

    $updata = explode(":", $form);
    $eid = $updata[0];
    $out =     "<script type=\"text/javascript\">document.getElementById('entity_id').selectedindex=" . $eid . ";</script>";

    $buildTypes = Building::getBuildTypes();

    if ($eid != 0) {
        $res = dbquery("
        SELECT
            building_name,
            buildlist_current_level,
            buildlist_id,
            buildlist_build_type,
            buildlist_build_start_time,
            buildlist_build_end_time
        FROM
            buildlist
        INNER JOIN
            buildings
            ON buildlist_building_id=building_id
            AND buildlist_entity_id='" . $eid . "'
        ORDER BY
            building_name
        ;");
        if (mysql_num_rows($res) > 0) {
            $out .= "<table class=\"tb\" id =\"tb\">";
            while ($arr = mysql_fetch_array($res)) {
                $out .= "<tr><td style=\"width:80px\" id=\"cnt_" . $arr['buildlist_id'] . "\">" . $arr['buildlist_current_level'] . "</td>
                <td style=\"width:100px\" id=\"type_" . $arr['buildlist_id'] . "\">" . $buildTypes[$arr['buildlist_build_type']] . "</td>
                <td style=\"width:300px\" id=\"time_" . $arr['buildlist_id'] . "\">";
                $out .= ($arr['buildlist_build_end_time'] > 0) ? "Start: " . df($arr['buildlist_build_start_time']) . "<br />Ende: " . df($arr['buildlist_build_end_time']) : "";
                $out .= "</td>
                <th>" . $arr['building_name'] . "</th>
                <td style=\"width:150px\" id=\"actions_" . $arr['buildlist_id'] . "\"><a href=\"javascript:;\" onclick=\"xajax_editBuilding(xajax.getFormValues('selector')," . $arr['buildlist_id'] . ")\">Bearbeiten</a>
                <a href=\"javascript:;\" onclick=\"if (confirm('Soll " . $arr['building_name'] . " " . $arr['buildlist_current_level'] . " von diesem Planeten gel&ouml;scht werden?')) {xajax_removeBuildingFromPlanet(xajax.getFormValues('selector')," . $arr['buildlist_id'] . ")}\">L&ouml;schen</td>
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

    $updata = explode(":", $form['entity_id']);
    if ($updata[1] > 0) {
        /** @var BuildingRepository */
        $buildingRepository = $app[BuildingRepository::class];
        $buildingRepository->addBuilding((int) $form['building_id'], (int) $form['buildlist_current_level'], (int) $updata[1], (int) $updata[0]);
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

    $updata = explode(":", $form['entity_id']);
    if ($updata[1] > 0) {
        /** @var \EtoA\Building\BuildingRepository $buildingRepository */
        $buildingRepository = $app[BuildingRepository::class];
        for ($i = 1; $i <= $num; $i++) {
            $buildingRepository->addBuilding($i, (int) $form['buildlist_current_level'], (int) $updata[1], (int) $updata[0]);
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
    $objResponse = new xajaxResponse();
    $updata = explode(":", $form['entity_id']);

    dbquery("
    DELETE FROM
        buildlist
    WHERE
        buildlist_id=" . intval($listId) . "
    ;");
    $objResponse->script("xajax_showBuildingsOnPlanet('" . $updata[0] . "');");

    return $objResponse;
}

function editBuilding($form, $listId)
{
    $objResponse = new xajaxResponse();

    $updata = explode(":", $form['entity_id']);
    if ($updata[0] !== '') {
        $res = dbquery("
        SELECT
            buildlist_current_level,
            buildlist_build_start_time,
            buildlist_build_end_time,
            buildlist_build_type,
            buildlist_id
        FROM
            buildlist
        WHERE
            buildlist_entity_id=" . $updata[0] . "
        ;");
        if (mysql_num_rows($res)) {
            $buildTypes = Building::getBuildTypes();
            while ($arr = mysql_fetch_array($res)) {
                if ($arr['buildlist_id'] == $listId) {
                    ob_start();
                    echo "Start: ";
                    show_timebox("editstart_" . $listId, $arr['buildlist_build_start_time']);
                    echo "<br />Ende: ";
                    show_timebox("editend_" . $listId, $arr['buildlist_build_end_time']);
                    $objResponse->assign("time_" . $listId, "innerHTML", ob_get_clean());
                    ob_start();
                    echo '<select name="editbuildtype_' . $listId . '">';
                    foreach ($buildTypes as $id => $type) {
                        echo '<option value="' . $id . '"';
                        if ($id == $arr['buildlist_build_type']) echo ' selected';
                        echo '>' . $type . '</option>';
                    }
                    echo '</select>';
                    $objResponse->assign("type_" . $listId, "innerHTML", ob_get_clean());
                    $out = "<input type=\"text\" size=\"9\" maxlength=\"12\" name=\"editcnt_" . $listId . "\" value=\"" . $arr['buildlist_current_level'] . "\" />";
                    $objResponse->assign("cnt_" . $listId, "innerHTML", $out);
                    $out = "<a href=\"javaScript:;\" onclick=\"xajax_submitEditBuilding(xajax.getFormValues('selector')," . $listId . ");\">Speichern</a> ";
                    $out .= "<a href=\"javaScript:;\" onclick=\"xajax_showBuildingsOnPlanet('" . $form['entity_id'] . "');\">Abbrechen</a>";
                    $objResponse->assign("actions_" . $listId, "innerHTML", $out);
                } else {
                    $objResponse->assign("actions_" . $arr['buildlist_id'], "innerHTML", "");
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
    $objResponse = new xajaxResponse();

    $status = intval($form['editbuildtype_' . $listId]);
    $endtime = $status > 0 ? mktime($form['editend_' . $listId . '_h'], $form['editend_' . $listId . '_i'], $form['editend_' . $listId . '_s'], $form['editend_' . $listId . '_m'], $form['editend_' . $listId . '_d'], $form['editend_' . $listId . '_y']) : '0';
    $starttime = $status > 0 ? mktime($form['editstart_' . $listId . '_h'], $form['editstart_' . $listId . '_i'], $form['editstart_' . $listId . '_s'], $form['editstart_' . $listId . '_m'], $form['editstart_' . $listId . '_d'], $form['editstart_' . $listId . '_y']) : '0';

    $updata = explode(":", $form['entity_id']);
    dbquery("
    UPDATE
        buildlist
    SET
        buildlist_current_level=" . intval($form['editcnt_' . $listId]) . ",
        buildlist_build_type=" . $status . ",
        buildlist_build_start_time=" . $starttime . ",
        buildlist_build_end_time=" . $endtime . "
    WHERE
        buildlist_id=" . intval($listId) . "
    ;");
    $objResponse->script("xajax_showBuildingsOnPlanet('" . $form['entity_id'] . "');");
    return $objResponse;
}


//Listet gefundene User auf
function searchUser($val, $field_id = 'user_nick', $box_id = 'citybox')
{

    $sOut = "";
    $nCount = 0;
    $sLastHit = null;

    $res = dbquery("SELECT user_nick FROM users WHERE user_nick LIKE '" . $val . "%' LIMIT 20;");
    if (mysql_num_rows($res) > 0) {
        while ($arr = mysql_fetch_row($res)) {
            $nCount++;
            $sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('" . $field_id . "').value='" . htmlentities($arr[0]) . "';document.getElementById('" . $box_id . "').style.display = 'none';\">" . htmlentities($arr[0]) . "</a>";
            $sLastHit = $arr[0];
        }
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
    $targetId = 'userlist';
    $inputId = 'userlist_nick';

    $sOut = "";
    $nCount = 0;
    $sLastHit = null;
    $res = dbquery("SELECT
        user_nick
    FROM
        users
    WHERE
        user_nick LIKE '" . $val . "%'
    LIMIT 20;");
    if (mysql_num_rows($res) > 0) {
        while ($arr = mysql_fetch_row($res)) {
            $nCount++;
            $sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('$inputId').value='" . htmlentities($arr[0]) . "';xajax_planetSelectorByUser('" . $arr[0] . "','" . $function . "');document.getElementById('$targetId').style.display = 'none';\">" . htmlentities($arr[0]) . "</a>";
            $sLastHit = $arr[0];
        }
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

    $sOut = "";
    $nCount = 0;
    $sLastHit = null;

    $res = dbquery("SELECT alliance_name FROM alliances WHERE alliance_name LIKE '%" . $val . "%' LIMIT 20;");
    if (mysql_num_rows($res) > 0) {
        while ($arr = mysql_fetch_row($res)) {
            $nCount++;
            $sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('" . $field_id . "').value='" . htmlentities($arr[0]) . "';document.getElementById('" . $box_id . "').style.display = 'none';\">" . htmlentities($arr[0]) . "</a>";
            $sLastHit = $arr[0];
        }
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


//Listet gefundene Planeten auf
function searchPlanet($val, $field_id = 'planet_name', $box_id = 'citybox')
{

    $sOut = "";
    $nCount = 0;
    $sLastHit = null;

    $res = dbquery("SELECT planet_name FROM planets WHERE planet_name LIKE '" . $val . "%' LIMIT 20;");
    if (mysql_num_rows($res) > 0) {
        while ($arr = mysql_fetch_row($res)) {
            $nCount++;
            $sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('" . $field_id . "').value='" . htmlentities($arr[0]) . "';document.getElementById('" . $box_id . "').style.display = 'none';\">" . htmlentities($arr[0]) . "</a>";
            $sLastHit = $arr[0];
        }
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
    $t1 = time();
    $t2 = $t1 + $time;
    dbquery("
    UPDATE
        users
    SET
        user_blocked_from=" . $t1 . ",
        user_blocked_to=" . $t2 . ",
        user_ban_reason='" . addslashes($reason) . "',
        user_ban_admin_id='" . $_SESSION[SESSION_NAME]['user_id'] . "'
    WHERE
        user_id='" . $uid . "'
    ;");
    $objResponse = new xajaxResponse();
    $objResponse->alert("Der Benutzer wurde gesperrt!");
    return $objResponse;
}

/***********/

function buildingPrices($id, $lvl)
{
    $objResponse = new xajaxResponse();
    $res = dbquery("
    SELECT
        building_costs_metal,
        building_costs_crystal,
        building_costs_plastic,
        building_costs_fuel,
        building_costs_food,
        building_costs_power,
        building_build_costs_factor
    FROM
        buildings
    WHERE
        building_id=" . $id . "
    ;");
    $arr = mysql_fetch_array($res);
    $bc = calcBuildingCosts($arr, $lvl);
    $objResponse->assign("c1_metal", "innerHTML", nf($bc['metal']));
    $objResponse->assign("c1_crystal", "innerHTML", nf($bc['crystal']));
    $objResponse->assign("c1_plastic", "innerHTML", nf($bc['plastic']));
    $objResponse->assign("c1_fuel", "innerHTML", nf($bc['fuel']));
    $objResponse->assign("c1_food", "innerHTML", nf($bc['food']));
    $objResponse->assign("c1_power", "innerHTML", nf($bc['power']));

    return $objResponse;
}

function totalBuildingPrices($form)
{
    $objResponse = new xajaxResponse();
    $bctt = array();
    foreach ($form['b_lvl'] as $id => $lvl) {
        $res = dbquery("
        SELECT
            building_costs_metal,
            building_costs_crystal,
            building_costs_plastic,
            building_costs_fuel,
            building_costs_food,
            building_costs_power,
            building_build_costs_factor
        FROM
            buildings
        WHERE
            building_id=" . $id . "
        ;");
        $arr = mysql_fetch_array($res);
        $bct = array();
        for ($x = 0; $x < $lvl; $x++) {
            $bc = calcBuildingCosts($arr, $x);
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
        $objResponse->assign("b_metal_" . $id, "innerHTML", nf($bct['metal']));
        $objResponse->assign("b_crystal_" . $id, "innerHTML", nf($bct['crystal']));
        $objResponse->assign("b_plastic_" . $id, "innerHTML", nf($bct['plastic']));
        $objResponse->assign("b_fuel_" . $id, "innerHTML", nf($bct['fuel']));
        $objResponse->assign("b_food_" . $id, "innerHTML", nf($bct['food']));
    }
    $objResponse->assign("t_metal", "innerHTML", nf($bctt['metal']));
    $objResponse->assign("t_crystal", "innerHTML", nf($bctt['crystal']));
    $objResponse->assign("t_plastic", "innerHTML", nf($bctt['plastic']));
    $objResponse->assign("t_fuel", "innerHTML", nf($bctt['fuel']));
    $objResponse->assign("t_food", "innerHTML", nf($bctt['food']));


    return $objResponse;
}


function reqInfo($id, $cat = 'b')
{
    global $app;
    $or = new xajaxResponse();
    ob_start();

    defineImagePaths();

    /** @var \EtoA\Building\BuildingDataRepository $buildingRepository */
    $buildingRepository = $app[\EtoA\Building\BuildingDataRepository::class];
    $buildingNames = $buildingRepository->getBuildingNames();

    /** @var \EtoA\Technology\TechnologyDataRepository $technologyRepository */
    $technologyRepository = $app[\EtoA\Technology\TechnologyDataRepository::class];
    $technologyNames = $technologyRepository->getTechnologyNames();

    /** @var \EtoA\Ship\ShipDataRepository $shipRepository */
    $shipRepository = $app[\EtoA\Ship\ShipDataRepository::class];
    $shipNames = $shipRepository->getShipNames();

    /** @var \EtoA\Defense\DefenseDataRepository $defenseRepository */
    $defenseRepository = $app[\EtoA\Defense\DefenseDataRepository::class];
    $defenseNames = $defenseRepository->getDefenseNames();

    /** @var \EtoA\Missile\MissileDataRepository $missileRepository */
    $missileRepository = $app[\EtoA\Missile\MissileDataRepository::class];
    $missileNames = $missileRepository->getMissileNames();

    //
    // Required objects
    //

    if ($cat == 'b') {
        $req_tbl = "building_requirements";
        $req_field = "obj_id";
    } elseif ($cat == 't') {
        $req_tbl = "tech_requirements";
        $req_field = "obj_id";
    } elseif ($cat == 's') {
        $req_tbl = "ship_requirements";
        $req_field = "obj_id";
    } elseif ($cat == 'd') {
        $req_tbl = "def_requirements";
        $req_field = "obj_id";
    } elseif ($cat == 'm') {
        $req_tbl = "missile_requirements";
        $req_field = "obj_id";
    } else {
        throw new \InvalidArgumentException('Unknown cateogry: ' . $cat);
    }

    $items = array();
    $res = dbquery("SELECT * FROM $req_tbl WHERE obj_id=" . $id . " AND req_building_id>0 AND req_level>0 ORDER BY req_level;");
    $nr = mysql_num_rows($res);
    if ($nr > 0) {
        while ($arr = mysql_fetch_assoc($res)) {
            $items[] = array($arr['req_building_id'], $buildingNames[$arr['req_building_id']], $arr['req_level'], IMAGE_PATH . "/buildings/building" . $arr['req_building_id'] . "_middle." . IMAGE_EXT, "xajax_reqInfo(" . $arr['req_building_id'] . ",'b')");
        }
    }
    $res = dbquery("SELECT * FROM $req_tbl WHERE $req_field=" . $id . " AND req_tech_id>0 AND req_level>0 ORDER BY req_level;");
    $nr2 = mysql_num_rows($res);
    if ($nr2 > 0) {
        while ($arr = mysql_fetch_assoc($res)) {
            $items[] = array($arr['req_tech_id'], $technologyNames[$arr['req_tech_id']], $arr['req_level'], IMAGE_PATH . "/technologies/technology" . $arr['req_tech_id'] . "_middle." . IMAGE_EXT, "xajax_reqInfo(" . $arr['req_tech_id'] . ",'t')");
        }
    }

    if (count($items) > 0) {
        echo "<div class=\"techtreeItemContainer\">";
        foreach ($items as $i) {
            echo "<div class=\"techtreeItem\" style=\"background:url('" . $i[3] . "');\">
            <div class=\"techtreeItemLevel\">Lvl <b>" . $i[2] . "</b></div>
            <a href=\"javascript:;\" onclick=\"" . $i[4] . "\" style=\"height:100%;display:block;\"></a>
            <div class=\"techtreeItemName\">" . $i[1] . "</div>
            </div>";
        }
        echo "<br style=\"clear:both;\"";
        echo "</div>";

        echo "<div style=\"margin:0px auto;\">wird benötigt für</div>";
    }

    //
    // Current object
    //

    if ($cat == 'b') {
        $img = IMAGE_PATH . "/buildings/building" . $id . "_middle." . IMAGE_EXT;
        $name = $buildingNames[$id];
    } elseif ($cat == 't') {
        $img = IMAGE_PATH . "/technologies/technology" . $id . "_middle." . IMAGE_EXT;
        $name = $technologyNames[$id];
    } elseif ($cat == 's') {
        $img = IMAGE_PATH . "/ships/ship" . $id . "_middle." . IMAGE_EXT;
        $name = $shipNames[$id];
    } elseif ($cat == 'd') {
        $img = IMAGE_PATH . "/defense/def" . $id . "_middle." . IMAGE_EXT;
        $name = $defenseNames[$id];
    } elseif ($cat == 'm') {
        $img = IMAGE_PATH . "/missiles/missile" . $id . "_middle." . IMAGE_EXT;
        $name = $missileNames[$id];
    } else {
        throw new \InvalidArgumentException('Unknown category: ' . $cat);
    }
    echo "<div class=\"techtreeMainItem\" style=\"background:url('" . $img . "');\">";
    echo "<div class=\"techtreeItemName\">" . $name . "</div>";
    echo "</div>";

    //
    // Allowed objects
    //

    if ($cat == 'b' || $cat == 't') {
        if ($cat == 'b') {
            $req_field = "req_building_id";
            $req_level_field = "req_level";
        } elseif ($cat == 't') {
            $req_field = "req_tech_id";
            $req_level_field = "req_level";
        } else {
            throw new \InvalidArgumentException('Unknown category: ' . $cat);
        }


        $items = array();
        $res = dbquery("SELECT * FROM building_requirements WHERE " . $req_field . "=" . $id . " ORDER BY " . $req_level_field . ";");
        $nr = mysql_num_rows($res);
        if ($nr > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                if (isset($buildingNames[$arr['obj_id']])) {
                    $items[] = array($arr['obj_id'], $buildingNames[$arr['obj_id']], $arr[$req_level_field], IMAGE_PATH . "/buildings/building" . $arr['obj_id'] . "_middle." . IMAGE_EXT, "xajax_reqInfo(" . $arr['obj_id'] . ",'b')");
                }
            }
        }
        $res = dbquery("SELECT * FROM tech_requirements WHERE " . $req_field . "=" . $id . " ORDER BY " . $req_level_field . ";");
        $nr = mysql_num_rows($res);
        if ($nr > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                if (isset($technologyNames[$arr['obj_id']])) {
                    $items[] = array($arr['obj_id'], $technologyNames[$arr['obj_id']], $arr[$req_level_field], IMAGE_PATH . "/technologies/technology" . $arr['obj_id'] . "_middle." . IMAGE_EXT, "xajax_reqInfo(" . $arr['obj_id'] . ",'t')");
                }
            }
        }
        $res = dbquery("SELECT * FROM ship_requirements WHERE " . $req_field . "=" . $id . " ORDER BY " . $req_level_field . ";");
        $nr = mysql_num_rows($res);
        if ($nr > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                if (isset($shipNames[$arr['obj_id']])) {
                    $items[] = array($arr['obj_id'], $shipNames[$arr['obj_id']], $arr[$req_level_field], IMAGE_PATH . "/ships/ship" . $arr['obj_id'] . "_middle." . IMAGE_EXT, "xajax_reqInfo(" . $arr['obj_id'] . ",'s')");
                }
            }
        }
        $res = dbquery("SELECT * FROM def_requirements WHERE " . $req_field . "=" . $id . " ORDER BY " . $req_level_field . ";");
        $nr = mysql_num_rows($res);
        if ($nr > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                if (isset($defenseNames[$arr['obj_id']])) {
                    $items[] = array($arr['obj_id'], $defenseNames[$arr['obj_id']], $arr[$req_level_field], IMAGE_PATH . "/defense/def" . $arr['obj_id'] . "_middle." . IMAGE_EXT, "xajax_reqInfo(" . $arr['obj_id'] . ",'d')");
                }
            }
        }
        $res = dbquery("SELECT * FROM missile_requirements WHERE " . $req_field . "=" . $id . " ORDER BY " . $req_level_field . ";");
        $nr = mysql_num_rows($res);
        if ($nr > 0) {
            while ($arr = mysql_fetch_assoc($res)) {
                if (isset($missileNames[$arr['obj_id']])) {
                    $items[] = array($arr['obj_id'], $missileNames[$arr['obj_id']], $arr[$req_level_field], IMAGE_PATH . "/missiles/missile" . $arr['obj_id'] . "_middle." . IMAGE_EXT, "xajax_reqInfo(" . $arr['obj_id'] . ",'m')");
                }
            }
        }

        if (count($items) > 0) {
            echo "<div style=\"margin:10px auto;\">ermöglicht</div>";

            echo "<div class=\"techtreeItemContainer\">";
            $cnt = 0;
            foreach ($items as $i) {
                echo "<div class=\"techtreeItem\" style=\"background:url('" . $i[3] . "');\">
                <div class=\"techtreeItemLevel\">Ab Lvl <b>" . $i[2] . "</b></div>
                <a href=\"javascript:;\" onclick=\"" . $i[4] . "\" style=\"height:100%;display:block;\"></a>
                <div class=\"techtreeItemName\">" . $i[1] . "</div>
                </div>";
                $cnt++;
            }
            echo "<br style=\"clear:both;\"";
            echo "</div>";
        }
    }


    $out = ob_get_clean();
    $or->assign('reqInfo', 'innerHTML', $out);
    return $or;
}

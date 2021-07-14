<?php

use EtoA\Building\BuildingRepository;
use EtoA\Ship\ShipRepository;

/** @var ShipRepository $shipRepository */
$shipRepository = $app[ShipRepository::class];

/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];

// Schiffangebot löschen
// <editor-fold>
if (isset($_POST['ship_cancel'])) {
    if (isset($_POST['ship_market_id'])) {
        $smid = intval($_POST['ship_market_id']);

        $scres = dbquery("
            SELECT
                *
            FROM
                market_ship
            WHERE
                id='" . $smid . "'
                AND user_id='" . $cu->id . "'");

        if (mysql_num_rows($scres) > 0) {
            $scrow = mysql_fetch_array($scres);
            $marketLevel = $buildingRepository->getBuildingLevel($cu->getId(), MARKTPLATZ_ID, (int) $scrow['entity_id']);
            $return_factor = floor((1 - 1 / ($marketLevel + 1)) * 100) / 100;
            $marr = array('factor' => $return_factor, "ship_id" => $scrow['ship_id'], "ship_count" => $scrow['count']);
            foreach ($resNames as $rk => $rn) {
                // todo: when non on the planet where the deal belongs to, the return_factor
                // is based on the local marketplace, for better or worse... change that so that the
                // origin marketplace return factor will be taken
                $marr['buy_' . $rk] = $scrow['costs_' . $rk];
            }

            $returnCount = (int) floor($scrow['count'] * $return_factor);
            if ($returnCount > 0) {
                $shipRepository->addShip((int) $scrow['ship_id'], $returnCount, (int) $scrow['user_id'], (int) $scrow['entity_id']);
            }

            dbquery("
                DELETE FROM
                    market_ship
                WHERE
                    id='" . $smid . "'");

            MarketReport::addMarketReport(array(
                'user_id' => $cu->id,
                'entity1_id' => $cp->id,
            ), "shipcancel", $smid, $marr);

            success_msg("Angebot wurde gel&ouml;scht und du hast $returnCount (" . ($return_factor * 100) . "%) der angebotenen Schiffe zur&uuml;ck erhalten (es wird abgerundet)");
        } else {
            error_msg("Es wurde kein entsprechendes Angebot ausgew&auml;hlt!");
            return_btn();
        }
    } else {
        error_msg("Es wurde kein entsprechendes Angebot ausgew&auml;hlt!");
        return_btn();
    }
}
// </editor-fold>

// Rohstoffangebot löschen
// <editor-fold>
elseif (isset($_POST['ressource_cancel']) && isset($_POST['ressource_market_id'])) {
    $rmid = intval($_POST['ressource_market_id']);

    $rcres = dbquery("
        SELECT
            *
        FROM
            market_ressource
        WHERE
            id='" . $rmid . "'
            AND user_id='" . $cu->id . "'");

    if (mysql_num_rows($rcres) > 0) {
        $rcrow = mysql_fetch_assoc($rcres);

        $rarr = array();
        $marketLevel = $buildingRepository->getBuildingLevel($cu->getId(), MARKTPLATZ_ID, (int) $rcrow['entity_id']);
        $return_factor = floor((1 - 1 / ($marketLevel + 1)) * 100) / 100;
        $marr = array('factor' => $return_factor);
        foreach ($resNames as $rk => $rn) {
            if ($rcrow['sell_' . $rk] > 0) {
                // todo: when non on the planet where the deal belongs to, the return_factor
                // is based on the local marketplace, for better or worse... change that so that the
                // origin marketplace return factor will be taken
                $rarr[$rk] = $rcrow['sell_' . $rk] * $return_factor;
                $marr['sell_' . $rk] = $rcrow['sell_' . $rk];
            }
        }

        $tp = Entity::createFactoryById($rcrow['entity_id']);
        $tp->addRes($rarr);
        unset($tp);

        MarketReport::addMarketReport(array(
            'user_id' => $cu->id,
            'entity1_id' => $rcrow['entity_id'],
        ), "rescancel", $rmid, $marr);

        dbquery("
            DELETE FROM
                market_ressource
            WHERE
                id='" . $rmid . "'");

        success_msg("Angebot wurde gel&ouml;scht und du hast " . ($return_factor * 100) . "% der angebotenen Rohstoffe zur&uuml;ck erhalten!");
    } else {
        error_msg("Es wurde kein entsprechendes Angebot ausgew&auml;hlt!");
        return_btn();
    }
}
// </editor-fold>

//Auktionen löschen
// <editor-fold>
elseif (isset($_POST['auction_cancel']) && isset($_POST['auction_cancel_id'])) {
    $acid = intval($_POST['auction_cancel_id']);

    $acres = dbquery("
        SELECT
            *
        FROM
            market_auction
        WHERE
            id='" . $acid . "'
            AND user_id='" . $cu->id . "'");
    if (mysql_num_rows($acres) > 0) {
        // Rohstoffe zurückgeben
        $acrow = mysql_fetch_array($acres);

        $rarr = array();
        $marketLevel = $buildingRepository->getBuildingLevel($cu->getId(), MARKTPLATZ_ID, (int) $acrow['entity_id']);
        $return_factor = floor((1 - 1 / ($marketLevel + 1)) * 100) / 100;
        $marr = array('factor' => $return_factor);
        foreach ($resNames as $rk => $rn) {
            if ($acrow['sell_' . $rk] > 0) {
                // todo: when non on the planet where the deal belongs to, the return_factor
                // is based on the local marketplace, for better or worse... change that so that the
                // origin marketplace return factor will be taken
                $rarr[$rk] = $acrow['sell_' . $rk] * $return_factor;
                $marr['sell_' . $rk] = $acrow['sell_' . $rk];
            }
        }
        $cp->addRes($rarr);

        //Auktion löschen
        dbquery("DELETE FROM market_auction WHERE id='" . $acid . "'");

        MarketReport::addMarketReport(array(
            'user_id' => $cu->id,
            'entity1_id' => $acrow['entity_id'],
        ), "auctioncancel", $acid, $marr);
        //			Log::add(7, Log::INFO, "Der Spieler ".$cu->nick." zieht folgende Auktion zur&uuml;ck:\nRohstoffe:\n".RES_METAL.": ".$acrow['sell_metal']."\n".RES_CRYSTAL.": ".$acrow['sell_crystal']."\n".RES_PLASTIC.": ".$acrow['sell_plastic']."\n".RES_FUEL.": ".$acrow['sell_fuel']."\n".RES_FOOD.": ".$acrow['sell_food']."\n\nEr erh&auml;lt ".(round($return_factor,2)*100)."% der Waren erstattet!",time());

        success_msg("Auktion wurde gel&ouml;scht und du hast " . ($return_factor * 100) . "% der angebotenen Waren zur&uuml;ck erhalten (es wird abgerundet)!");
    } else {
        error_msg("Es wurde kein entsprechendes Angebot ausgew&auml;hlt!");
        return_btn();
    }
}
// </editor-fold>

// Eigene Angebote zeigen
else {
    $cstr = checker_init();

    //
    // Rohstoffe
    // <editor-fold>
    $res = dbquery("
        SELECT
            *
        FROM
            market_ressource
        WHERE
            user_id='" . $cu->id . "'
            AND buyable='1'
        ORDER BY
            datum ASC");
    if (mysql_num_rows($res) > 0) {
        echo "<form action=\"?page=$page&amp;mode=user_sell\" method=\"post\">\n";
        echo $cstr;
        tableStart("Rohstoffe");
        echo "<tr>
                <th>Rohstoffe:</th>
                <th>Angebot:</th>
                <th>Preis:</th>
                <th>Marktplatz:</th>
                <th>Datum/Text:</th>
                <th>Zur&uuml;ckziehen:</th></tr>";
        $cnt = 0;
        while ($row = mysql_fetch_array($res)) {
            $reservation = '';
            if ($row['for_user'] != 0) {
                $reservedUser = new User($row['for_user']);
                if ($reservedUser->isValid) {
                    $reservation = "<span class=\"userAllianceMemberColor\">F&uuml;r Spieler " . $reservedUser->nick . " reserviert</span>";
                }
            } else if ($row['for_alliance'] != 0) {
                $reservation = "<span class=\"userAllianceMemberColor\">F&uuml;r Allianzmitglied Reserviert</span>";
            } else {
                $reservation = "";
            }

            $i = 0;

            $te = Entity::createFactoryById($row['entity_id']);
            $marketLevel = $buildingRepository->getBuildingLevel($cu->getId(), MARKTPLATZ_ID, (int) $row['entity_id']);
            $return_factor = floor((1 - 1 / ($marketLevel + 1)) * 100) / 100;
            $info_string = "Wenn du das Angebot zur&uuml;ckziehst erh&auml;lst du " . ($return_factor * 100) . "% des Angebotes zur&uuml;ck (abgerundet).";
            if ($te != null) {

                foreach ($resNames as $rk => $rn) {
                    echo "<tr>
                    <td class=\"rescolor" . $rk . "\">" . $resIcons[$rk] . " <b>" . $rn . "</b>:</td>
                    <td class=\"rescolor" . $rk . "\">" . ($row['sell_' . $rk] > 0 ? nf($row['sell_' . $rk]) : '-') . "</td>
                    <td class=\"rescolor" . $rk . "\">" . ($row['buy_' . $rk] > 0 ? nf($row['buy_' . $rk]) : '-') . "</td>";
                    if ($i++ == 0) {

                        echo "<td rowspan=\"5\">" . ($te->detailLink()) . "</td>";
                        echo "<td rowspan=\"5\">" . date("d.m.Y  G:i:s", $row['datum']) . "<br/><br/>" . stripslashes($row['text']) . "</td>";
                        echo "<td rowspan=\"5\" " . tt($info_string) . "><input type=\"radio\" name=\"ressource_market_id\" value=\"" . $row['id'] . "\"><br/><br/>" . $reservation . "</td></tr>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan=\"6\">Angebot von ungültigem Ziel</td></tr>";
            }

            $cnt++;
            if ($cnt < mysql_num_rows($res))
                echo "<tr><td colspan=\"7\" style=\"height:10px;background:#000\"></td></tr>";
        }
        tableEnd();
        echo "<input type=\"submit\" class=\"button\" name=\"ressource_cancel\" value=\"Angebot zur&uuml;ckziehen\"/>";
        echo "</form><br/><br/>";
    } else {
        iBoxStart("Rohstoffe");
        echo "Keine Angebote vorhanden!";
        iBoxEnd();
    }
    // </editor-fold>

    //
    // Schiffe
    // <editor-fold>
    $res = dbquery("
        SELECT
            *
        FROM
            market_ship
        WHERE
            user_id='" . $cu->id . "'
            AND buyable='1'
        ORDER BY
            datum ASC");
    if (mysql_num_rows($res) > 0) {
        echo "<form action=\"?page=$page&amp;mode=user_sell\" method=\"post\">\n";
        echo $cstr;
        tableStart("Schiffe");

        echo "<tr>
            <th>Angebot:</th>
            <th colspan=\"2\">Preis:</th>
            <th>Datum/Text:</th>
            <th>Zur&uuml;ckziehen:</th></tr>";

        $cnt = 0;
        while ($arr = mysql_fetch_array($res)) {
            $reservation = '';
            if ($arr['for_user'] != 0) {
                $reservedUser = new User($arr['for_user']);
                if ($reservedUser->isValid) {
                    $reservation = "<span class=\"userAllianceMemberColor\">F&uuml;r Spieler " . $reservedUser->nick . " reserviert</span>";
                }
            } else if ($arr['for_alliance'] != 0) {
                $reservation = "<span class=\"userAllianceMemberColor\">F&uuml;r Allianzmitglied Reserviert</span>";
            } else {
                $reservation = "";
            }

            $i = 0;
            $resCnt = count($resNames);
            $marketLevel = $buildingRepository->getBuildingLevel($cu->getId(), MARKTPLATZ_ID, (int) $arr['entity_id']);
            $return_factor = floor((1 - 1 / ($marketLevel + 1)) * 100) / 100;
            $info_string = "Wenn du das Angebot zur&uuml;ckziehst erh&auml;lst du " . ($return_factor * 100) . "% des Angebotes zur&uuml;ck (abgerundet).";
            foreach ($resNames as $rk => $rn) {
                echo "<tr>";
                if ($i == 0) {
                    $ship = new Ship($arr['ship_id']);
                    echo "<td rowspan=\"$resCnt\">" . $arr['count'] . " <a href=\"?page=help&site=shipyard&id=" . $arr['ship_id'] . "\">" . $ship->toolTip() . "</a></td>";
                }
                echo "<td class=\"rescolor" . $rk . "\">" . $resIcons[$rk] . "<b>" . $rn . "</b>:</td>
                    <td class=\"rescolor" . $rk . "\">" . nf($arr['costs_' . $rk]) . "</td>";
                if ($i++ == 0) {
                    echo "<td rowspan=\"$resCnt\">" . date("d.m.Y  G:i:s", $arr['datum']) . "<br/><br/>" . stripslashes($arr['text']) . "</td>";
                    echo "<td rowspan=\"$resCnt\" " . tt($info_string) . "><input type=\"radio\" name=\"ship_market_id\" value=\"" . $arr['id'] . "\"><br/><br/>" . $reservation . "</td>";
                }
                echo "</tr>";
            }

            $cnt++;
            if ($cnt < mysql_num_rows($res))
                echo "<tr><td colspan=\"6\" style=\"height:10px;background:#000\"></td></tr>";
        }
        tableEnd();
        echo "<input type=\"submit\" class=\"button\" name=\"ship_cancel\" value=\"Angebot zur&uuml;ckziehen\" />";
        echo "</form><br/><br/>";
    } else {
        iBoxStart("Schiffe");
        echo "Keine Angebote vorhanden!";
        iBoxEnd();
    }
    // </editor-fold>


    //
    // Auktionen
    //
    // <editor-fold>
    $res = dbquery("
        SELECT
            *
        FROM
            market_auction
        WHERE
            user_id='" . $cu->id . "'
        ORDER BY
            date_end ASC;");

    if (mysql_num_rows($res) > 0) {
        echo "<form action=\"?page=$page&amp;mode=user_sell\" method=\"post\">\n";
        tableStart("Offene Auktionen");
        // Header
        echo "<tr>
            <th>Angebot</th>
            <th>Beschreibung</th>
            <th style=\"width:100px;\">Angebotsende</th>
            <th style=\"width:50px;\">Gebote</th>
            <th>Aktuelles Gebot</th>
            <th>Zurückziehen</th>
            </tr>";

        $cnt = 0;
        $acnts = array();
        $acnt = 0;
        while ($arr = mysql_fetch_array($res)) {
            //restliche zeit bis zum ende
            $rest_time = $arr['date_end'] - time();

            // Gibt Nachricht aus, wenn die Auktion beendet ist, aber noch kein Löschtermin festgelegt ist
            if ($rest_time <= 0) {
                $rest_time = "Auktion beendet!";
            }
            // und sonst Zeit bis zum Ende anzeigen
            else {
                $rest_time = tf($rest_time);
            }

            echo "<tr>
                <td>";
            foreach ($resNames as $rk => $rn) {
                if ($arr['sell_' . $rk] > 0) {
                    echo "<span class=\"rescolor" . $rk . "\">";
                    echo $resIcons[$rk] . $rn . ": " . nf($arr['sell_' . $rk]) . "</span><br style=\"clear:both;\" />";
                }
            }
            echo "</td>
                <td>" . $arr['text'] . "</td>
                <td>$rest_time</td>
                <td>" . $arr['bidcount'] . "</td>
                <td>";
            foreach ($resNames as $rk => $rn) {
                if ($arr['currency_' . $rk] > 0) {
                    echo "<span class=\"rescolor" . $rk . "\">";
                    echo $resIcons[$rk] . $rn . ": " . nf($arr['buy_' . $rk]);
                    echo "</span><br style=\"clear:both;\" />";
                }
            }
            echo "</td>";
            $marketLevel = $buildingRepository->getBuildingLevel($cu->getId(), MARKTPLATZ_ID, (int) $arr['entity_id']);
            $return_factor = floor((1 - 1 / ($marketLevel + 1)) * 100) / 100;
            $info_string = "Wenn du das Angebot zur&uuml;ckziehst erh&auml;lst du " . ($return_factor * 100) . "% des Angebotes zur&uuml;ck (abgerundet).";
            echo "<td " . tt($info_string) . " style=\"width:100px;\">";
            if ($arr['date_end'] - time() > 0 && $arr['bidcount'] == 0 && $arr['buyable'] == 1)
                echo "<input type=\"radio\" name=\"auction_cancel_id\"  value=\"" . $arr['id'] . "\" />";
            echo "</td>";
            echo "</td></tr>";
        }
        tableEnd();
        echo "<input type=\"submit\" class=\"button\" name=\"auction_cancel\" value=\"Angebot zur&uuml;ckziehen\"/>";
        echo "</form>";
    } else {
        iBoxStart("Auktionen");
        echo "Keine Auktionen vorhanden!";
        iBoxEnd();
    }
    // </editor-fold>
}

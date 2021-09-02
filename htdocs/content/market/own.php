<?php

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingRepository;
use EtoA\Market\MarketAuctionRepository;
use EtoA\Market\MarketResourceRepository;
use EtoA\Market\MarketShipRepository;
use EtoA\Message\MarketReportRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\PreciseResources;

/** @var ShipRepository $shipRepository */
$shipRepository = $app[ShipRepository::class];
/** @var PlanetRepository $planetRepository */
$planetRepository = $app[PlanetRepository::class];
/** @var BuildingRepository $buildingRepository */
$buildingRepository = $app[BuildingRepository::class];

/** @var MarketAuctionRepository $marketAuctionRepository */
$marketAuctionRepository = $app[MarketAuctionRepository::class];
/** @var MarketResourceRepository $marketResourceRepository */
$marketResourceRepository = $app[MarketResourceRepository::class];
/** @var MarketShipRepository $marketShipRepository */
$marketShipRepository = $app[MarketShipRepository::class];
/** @var MarketReportRepository $marketReportRepository */
$marketReportRepository = $app[MarketReportRepository::class];

// Schiffangebot löschen
// <editor-fold>
if (isset($_POST['ship_cancel'])) {
    if (isset($_POST['ship_market_id'])) {
        $smid = intval($_POST['ship_market_id']);

        $offer = $marketShipRepository->getUserOffer($smid, $cu->getId());
        if ($offer !== null) {
            $marketLevel = $buildingRepository->getBuildingLevel($cu->getId(), BuildingId::MARKET, $offer->entityId);
            $return_factor = floor((1 - 1 / ($marketLevel + 1)) * 100) / 100;
            $costs = $offer->getCosts();
            $returnCount = (int) floor($offer->count * $return_factor);
            if ($returnCount > 0) {
                $shipRepository->addShip($offer->shipId, $returnCount, $offer->userId, $offer->entityId);
            }

            $marketShipRepository->delete($smid);

            $marketReportRepository->addShipReport($offer->id, $cu->getId(), $cp->id, 0, $offer->shipId, $offer->count, "shipcancel", $costs, $return_factor);

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

    $offer = $marketResourceRepository->getUserOffer($rmid, $cu->getId());
    if ($offer !== null) {
        $rarr = array();
        $marketLevel = $buildingRepository->getBuildingLevel($cu->getId(), BuildingId::MARKET, $offer->entityId);
        $return_factor = floor((1 - 1 / ($marketLevel + 1)) * 100) / 100;
        $sellResources = $offer->getSellResources();
        $returnResources = new PreciseResources();
        foreach ($resNames as $rk => $rn) {
            if ($sellResources->get($rk) > 0) {
                // todo: when non on the planet where the deal belongs to, the return_factor
                // is based on the local marketplace, for better or worse... change that so that the
                // origin marketplace return factor will be taken
                $returnResources->set($rk, $sellResources->get($rk) * $return_factor);
            }
        }

        $planetRepository->addResources($offer->entityId, $returnResources->metal, $returnResources->crystal, $returnResources->plastic, $returnResources->fuel, $returnResources->food);

        $marketReportRepository->addResourceReport($rmid, $cu->id, $offer->entityId, 0, $sellResources, "rescancel", new BaseResources(), $return_factor);

        $marketResourceRepository->delete($rmid);
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

    $auction = $marketAuctionRepository->getUserAuction($acid, $cu->getId());
    if ($auction !== null) {
        // Rohstoffe zurückgeben
        $rarr = array();
        $marketLevel = $buildingRepository->getBuildingLevel($cu->getId(), BuildingId::MARKET, $auction->entityId);
        $return_factor = floor((1 - 1 / ($marketLevel + 1)) * 100) / 100;
        $sellResources = $auction->getSellResources();
        $returnResources = new PreciseResources();
        foreach ($resNames as $rk => $rn) {
            if ($sellResources->get($rk) > 0) {
                // todo: when non on the planet where the deal belongs to, the return_factor
                // is based on the local marketplace, for better or worse... change that so that the
                // origin marketplace return factor will be taken
                $returnResources->set($rk, $sellResources->get($rk) * $return_factor);
            }
        }
        $planetRepository->addResources($auction->entityId, $returnResources->metal, $returnResources->crystal, $returnResources->plastic, $returnResources->fuel, $returnResources->food);

        //Auktion löschen
        $marketAuctionRepository->deleteAuction($auction->id);
        $marketReportRepository->addAuctionReport($auction->id, $cu->getId(), $auction->entityId, 0, $sellResources, 'auctioncancel', new BaseResources(), null, $return_factor);
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
    $offers = $marketResourceRepository->getUserOffers($cu->getId());
    if (count($offers) > 0) {
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
        foreach ($offers as $offer) {
            $reservation = '';
            if ($offer->forUserId !== 0) {
                $reservedUser = new User($offer->forUserId);
                if ($reservedUser->isValid) {
                    $reservation = "<span class=\"userAllianceMemberColor\">F&uuml;r Spieler " . $reservedUser->nick . " reserviert</span>";
                }
            } else if ($offer->forAllianceId !== 0) {
                $reservation = "<span class=\"userAllianceMemberColor\">F&uuml;r Allianzmitglied Reserviert</span>";
            } else {
                $reservation = "";
            }

            $i = 0;

            $te = Entity::createFactoryById($offer->entityId);
            $marketLevel = $buildingRepository->getBuildingLevel($cu->getId(), BuildingId::MARKET, $offer->entityId);
            $return_factor = floor((1 - 1 / ($marketLevel + 1)) * 100) / 100;
            $info_string = "Wenn du das Angebot zur&uuml;ckziehst erh&auml;lst du " . ($return_factor * 100) . "% des Angebotes zur&uuml;ck (abgerundet).";
            if ($te != null) {
                $buyResources = $offer->getBuyResources();
                $sellResources = $offer->getSellResources();
                foreach ($resNames as $rk => $rn) {
                    echo "<tr>
                    <td class=\"rescolor" . $rk . "\">" . $resIcons[$rk] . " <b>" . $rn . "</b>:</td>
                    <td class=\"rescolor" . $rk . "\">" . ($sellResources->get($rk) > 0 ? StringUtils::formatNumber($sellResources->get($rk)) : '-') . "</td>
                    <td class=\"rescolor" . $rk . "\">" . ($buyResources->get($rk) > 0 ? StringUtils::formatNumber($buyResources->get($rk)) : '-') . "</td>";
                    if ($i++ == 0) {

                        echo "<td rowspan=\"5\">" . ($te->detailLink()) . "</td>";
                        echo "<td rowspan=\"5\">" . date("d.m.Y  G:i:s", $offer->date) . "<br/><br/>" . stripslashes($offer->text) . "</td>";
                        echo "<td rowspan=\"5\" " . tt($info_string) . "><input type=\"radio\" name=\"ressource_market_id\" value=\"" . $offer->id . "\"><br/><br/>" . $reservation . "</td></tr>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan=\"6\">Angebot von ungültigem Ziel</td></tr>";
            }

            $cnt++;
            if ($cnt < count($offers))
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
    $offers = $marketShipRepository->getUserOffers($cu->getId());
    if (count($offers) > 0) {
        echo "<form action=\"?page=$page&amp;mode=user_sell\" method=\"post\">\n";
        echo $cstr;
        tableStart("Schiffe");

        echo "<tr>
            <th>Angebot:</th>
            <th colspan=\"2\">Preis:</th>
            <th>Datum/Text:</th>
            <th>Zur&uuml;ckziehen:</th></tr>";

        $cnt = 0;
        foreach ($offers as $offer) {
            $reservation = '';
            if ($offer->forUserId !== 0) {
                $reservedUser = new User($offer->forUserId);
                if ($reservedUser->isValid) {
                    $reservation = "<span class=\"userAllianceMemberColor\">F&uuml;r Spieler " . $reservedUser->nick . " reserviert</span>";
                }
            } else if ($offer->forAllianceId !== 0) {
                $reservation = "<span class=\"userAllianceMemberColor\">F&uuml;r Allianzmitglied Reserviert</span>";
            } else {
                $reservation = "";
            }

            $i = 0;
            $resCnt = count($resNames);
            $marketLevel = $buildingRepository->getBuildingLevel($cu->getId(), BuildingId::MARKET, $offer->entityId);
            $return_factor = floor((1 - 1 / ($marketLevel + 1)) * 100) / 100;
            $info_string = "Wenn du das Angebot zur&uuml;ckziehst erh&auml;lst du " . ($return_factor * 100) . "% des Angebotes zur&uuml;ck (abgerundet).";

            $costs = $offer->getCosts();
            foreach ($resNames as $rk => $rn) {
                echo "<tr>";
                if ($i == 0) {
                    $ship = new Ship($offer->shipId);
                    echo "<td rowspan=\"$resCnt\">" . $offer->count . " <a href=\"?page=help&site=shipyard&id=" . $offer->shipId . "\">" . $ship->toolTip() . "</a></td>";
                }
                echo "<td class=\"rescolor" . $rk . "\">" . $resIcons[$rk] . "<b>" . $rn . "</b>:</td>
                    <td class=\"rescolor" . $rk . "\">" . StringUtils::formatNumber($costs->get($rk)) . "</td>";
                if ($i++ == 0) {
                    echo "<td rowspan=\"$resCnt\">" . date("d.m.Y  G:i:s", $offer->date) . "<br/><br/>" . stripslashes($offer->text) . "</td>";
                    echo "<td rowspan=\"$resCnt\" " . tt($info_string) . "><input type=\"radio\" name=\"ship_market_id\" value=\"" . $offer->id . "\"><br/><br/>" . $reservation . "</td>";
                }
                echo "</tr>";
            }

            $cnt++;
            if ($cnt < count($offers))
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
    $userAuctions = $marketAuctionRepository->getUserAuctions($cu->getId());
    if (count($userAuctions) > 0) {
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
        foreach ($userAuctions as $auction) {
            //restliche zeit bis zum ende
            $rest_time = $auction->dateEnd - time();

            // Gibt Nachricht aus, wenn die Auktion beendet ist, aber noch kein Löschtermin festgelegt ist
            if ($rest_time <= 0) {
                $rest_time = "Auktion beendet!";
            }
            // und sonst Zeit bis zum Ende anzeigen
            else {
                $rest_time = StringUtils::formatTimespan($rest_time);
            }

            echo "<tr>
                <td>";
            $sellResources = $auction->getSellResources();
            foreach ($resNames as $rk => $rn) {
                if ($sellResources->get($rk) > 0) {
                    echo "<span class=\"rescolor" . $rk . "\">";
                    echo $resIcons[$rk] . $rn . ": " . StringUtils::formatNumber($sellResources->get($rk)) . "</span><br style=\"clear:both;\" />";
                }
            }
            echo "</td>
                <td>" . $auction->text . "</td>
                <td>$rest_time</td>
                <td>" . $auction->bidCount . "</td>
                <td>";
            $currencyResources = $auction->getCurrencyResources();
            $buyResources = $auction->getBuyResources();
            foreach ($resNames as $rk => $rn) {
                if ($currencyResources->get($rk) > 0) {
                    echo "<span class=\"rescolor" . $rk . "\">";
                    echo $resIcons[$rk] . $rn . ": " . StringUtils::formatNumber($buyResources->get($rk));
                    echo "</span><br style=\"clear:both;\" />";
                }
            }
            echo "</td>";
            $marketLevel = $buildingRepository->getBuildingLevel($cu->getId(), BuildingId::MARKET, $auction->entityId);
            $return_factor = floor((1 - 1 / ($marketLevel + 1)) * 100) / 100;
            $info_string = "Wenn du das Angebot zur&uuml;ckziehst erh&auml;lst du " . ($return_factor * 100) . "% des Angebotes zur&uuml;ck (abgerundet).";
            echo "<td " . tt($info_string) . " style=\"width:100px;\">";
            if ($auction->dateEnd - time() > 0 && $auction->bidCount == 0 && $auction->buyable)
                echo "<input type=\"radio\" name=\"auction_cancel_id\"  value=\"" . $auction->id . "\" />";
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

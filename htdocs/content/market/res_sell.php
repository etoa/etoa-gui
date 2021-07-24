<?php

use EtoA\Market\MarketHandler;
use EtoA\Market\MarketResourceRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;

/** @var MarketResourceRepository $marketResourceRepository */
$marketResourceRepository = $app[MarketResourceRepository::class];
/** @var EntityService $entityService */
$entityService = $app[EntityService::class];
/** @var EntityRepository $entityRepository */
$entityRepository = $app[EntityRepository::class];

$cnt = 0;
$cnt_error = 0;

$supplyTotal = array_fill(0, count($resNames), 0);
$demandTotal = array_fill(0, count($resNames), 0);

if (isset($_POST['ressource_market_id'])) {
    foreach ($_POST['ressource_market_id'] as $num => $id) {
        // Lädt Angebotsdaten
        $offer = $marketResourceRepository->getBuyableOffer((int) $id, $cu->getId(), (int) $cu->allianceId());

        // Prüft, ob Angebot noch vorhanden ist
        if ($offer !== null) {
            // Prüft, ob genug Rohstoffe vorhanden sind
            $ok = true;
            $buyarr = array();
            $sellarr = array();
            $buyResource = $offer->getBuyResources();
            $sellResources = $offer->getSellResources();
            foreach ($resNames as $rk => $rn) {
                $buyarr[$rk] = $buyResource->get($rk);
                $sellarr[$rk] = $sellResources->get($rk);
            }

            if ($cp->checkRes($buyarr)) {
                $cp->subRes($buyarr);

                $seller = new User($offer->userId);
                $sellerEntity = $entityRepository->getEntity($offer->entityId);
                $ownEntity = $entityRepository->getEntity((int) $cp->id);

                $id = $sellerEntity !== null ? $sellerEntity->id : 0;
                $tradeShip = new Ship(MARKET_SHIP_ID);
                $numSellerShip = ($tradeShip->capacity > 0) ? ceil(array_sum($sellarr) / $tradeShip->capacity) : 1;

                $dist = $entityService->distance($sellerEntity, $ownEntity);
                $sellerFlighttime = ceil($dist / ($seller->specialist->tradeTime * $tradeShip->speed / 3600) + $tradeShip->time2start + $tradeShip->time2land);
                $buyerFlighttime = ceil($dist / ($cu->specialist->tradeTime * $tradeShip->speed / 3600) + $tradeShip->time2start + $tradeShip->time2land);

                $launchtime = time();
                $sellerLandtime = $launchtime + $sellerFlighttime;
                $buyerLandtime = $launchtime + $buyerFlighttime;


                // Fleet Seller -> Buyer
                dbquery("
                            INSERT INTO
                                fleet
                            (
                                user_id,
                                entity_from,
                                entity_to,
                                launchtime,
                                landtime,
                                action,
                                res_metal,
                                res_crystal,
                                res_plastic,
                                res_fuel,
                                res_food,
                                status
                            )
                            VALUES
                            (
                                " . $cu->id . ",
                                " . $id . ",
                                " . $cp->id . ",
                                " . $launchtime . ",
                                " . $buyerLandtime . ",
                                'market',
                                " . $sellarr[0] . ",
                                " . $sellarr[1] . ",
                                " . $sellarr[2] . ",
                                " . $sellarr[3] . ",
                                " . $sellarr[4] . ",
                                0
                            );");
                $sellerFid = mysql_insert_id();
                dbquery("
                            INSERT INTO
                                fleet_ships
                            (
                                fs_fleet_id,
                                fs_ship_id,
                                fs_ship_cnt
                            )
                            VALUES
                            (
                                " . $sellerFid . ",
                                " . MARKET_SHIP_ID . ",
                                " . $numSellerShip . "
                            );");

                $numBuyerShip = ($tradeShip->capacity > 0) ? ceil(array_sum($buyarr) / $tradeShip->capacity) : 1;

                // Fleet Buyer->Seller
                dbquery("
                                INSERT INTO
                                    fleet
                                (
                                    user_id,
                                    entity_from,
                                    entity_to,
                                    launchtime,
                                    landtime,
                                    action,
                                    res_metal,
                                    res_crystal,
                                    res_plastic,
                                    res_fuel,
                                    res_food,
                                    status
                                )
                                VALUES
                                (
                                    " . $seller->id . ",
                                    " . $cp->id . ",
                                    " . $sellerEntity->id . ",
                                    " . $launchtime . ",
                                    " . $sellerLandtime . ",
                                    'market',
                                " . $buyarr[0] . ",
                                " . $buyarr[1] . ",
                                " . $buyarr[2] . ",
                                " . $buyarr[3] . ",
                                " . $buyarr[4] . ",
                                    0
                                );");
                $buyerFid = mysql_insert_id();
                dbquery("
                                INSERT INTO
                                    fleet_ships
                                (
                                    fs_fleet_id,
                                    fs_ship_id,
                                    fs_ship_cnt
                                )
                                VALUES
                                (
                                    " . $buyerFid . ",
                                    " . MARKET_SHIP_ID . ",
                                    " . $numBuyerShip . "
                                );");


                // Angebot löschen
                $marketResourceRepository->delete($offer->id);

                // Add values for market rate calculation and
                // fill array for the market report ($mr)
                $mr = array();
                foreach ($resNames as $rk => $rn) {
                    $supplyTotal[$rk] += $sellarr[$rk];
                    $demandTotal[$rk] += $buyarr[$rk];

                    $mr['sell_' . $rk] = $sellarr[$rk];
                    $mr['buy_' . $rk] = $buyarr[$rk];
                }

                // Send report to seller
                MarketReport::addMarketReport(array(
                    'user_id' => $offer->userId,
                    'entity1_id' => $offer->entityId,
                    'entity2_id' => $cp->id,
                    'opponent1_id' => $cu->id,
                ), "ressold", $offer->id, array_merge($mr, array("fleet1_id" => $sellerFid, "fleet2_id" => $buyerFid)));

                // Send report to buyer (the current user)
                MarketReport::addMarketReport(array(
                    'user_id' => $cu->id,
                    'entity1_id' => $cp->id,
                    'entity2_id' => $offer->entityId,
                    'opponent1_id' => $offer->userId,
                ), "resbought", $offer->id, array_merge($mr, array("fleet1_id" => $buyerFid, "fleet2_id" => $sellerFid)));

                // Add market ratings
                $cu->rating->addTradeRating(TRADE_POINTS_PER_TRADE, false, 'Handel #' . $offer->id . ' mit ' . $offer->userId);
                if (strlen($offer->text) > TRADE_POINTS_TRADETEXT_MIN_LENGTH)
                    $seller->rating->addTradeRating(TRADE_POINTS_PER_TRADE + TRADE_POINTS_PER_TRADETEXT, true, 'Handel #' . $offer->id . ' mit ' . $cu->id);
                else
                    $seller->rating->addTradeRating(TRADE_POINTS_PER_TRADE, true, 'Handel #' . $offer->id . ' mit ' . $cu->id);

                // Log schreiben, falls dieser Handel regelwidrig ist
                // TODO: Think of an implementation using the user class...
                $multi_res1 = dbquery("
                                    SELECT
                                        multi_id
                                    FROM
                                        user_multi
                                    WHERE
                                        multi_id='" . $cu->id . "'
                                        AND user_id='" . $offer->userId . "';");

                $multi_res2 = dbquery("
                                    SELECT
                                        user_id
                                    FROM
                                        user_multi
                                    WHERE
                                        multi_id='" . $offer->userId . "'
                                        AND user_id='" . $cu->id . "';");

                if (mysql_num_rows($multi_res1) != 0 || mysql_num_rows($multi_res2) != 0) {
                    Log::add(Log::F_MULTITRADE, Log::INFO, "[page user sub=edit user_id=" . $cu->id . "][B]" . $cu->nick . "[/B][/page] hat von [page user sub=edit user_id=" . $offer->userId . "][B]" . $seller . "[/B][/page] Rohstoffe gekauft:\n\n" . RES_METAL . ": " . nf($offer->sell0) . "\n" . RES_CRYSTAL . ": " . nf($offer->sell1) . "\n" . RES_PLASTIC . ": " . nf($offer->sell2) . "\n" . RES_FUEL . ": " . nf($offer->sell3) . "\n" . RES_FOOD . ": " . nf($offer->sell4) . "\n\nDies hat ihn folgende Rohstoffe gekostet:\n" . RES_METAL . ": " . nf($offer->buy0) . "\n" . RES_CRYSTAL . ": " . nf($offer->buy1) . "\n" . RES_PLASTIC . ": " . nf($offer->buy2) . "\n" . RES_FUEL . ": " . nf($offer->buy3) . "\n" . RES_FOOD . ": " . nf($offer->buy4));
                }

                // Zählt die erfolgreich abgewickelten Angebote
                $cnt++;
            } else {
                // Zählt die gescheiterten Angebote
                $cnt_error++;
            }
        } else {
            // Zählt die gescheiterten Angebote
            $cnt_error++;
        }
    }
} else {
    error_msg("Kein(e) Angebot(e) ausgewählt!");
}

if ($cnt > 0) {
    success_msg("" . $cnt . " Angebot(e) erfolgreich gekauft!");
}
if ($cnt_error > 0) {
    error_msg("" . $cnt_error . " Angebot(e) sind nicht mehr vorhanden, oder die benötigten Rohstoffe sind nicht mehr verfügbar!");
}

// Gekaufte/Verkaufte Rohstoffe in Config-DB speichern für Kursberechnung
/** @var MarketHandler */
$marketHandler = $app[MarketHandler::class];
$marketHandler->addResToRate($supplyTotal, $demandTotal);

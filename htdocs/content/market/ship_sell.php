<?php

use EtoA\Fleet\FleetRepository;
use EtoA\Market\MarketShipRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Resources\BaseResources;
use EtoA\User\UserMultiRepository;

$cnt = 0;
$cnt_error = 0;

/** @var MarketShipRepository $marketShipRepository */
$marketShipRepository = $app[MarketShipRepository::class];
/** @var EntityService $entityService */
$entityService = $app[EntityService::class];
/** @var EntityRepository $entityRepository */
$entityRepository = $app[EntityRepository::class];
/** @var FleetRepository $fleetRepository */
$fleetRepository = $app[FleetRepository::class];

foreach ($_POST['ship_market_id'] as $num => $id) {
    // Lädt Angebotsdaten
    $offer = $marketShipRepository->getBuyableOffer((int) $id, $cu->getId(), (int) $cu->allianceId());
    // Prüft, ob Angebot noch vorhanden ist
    if ($offer !== null) {
        $buyarr = array();
        $mr = array("ship_id" => $offer->shipId, "ship_count" => $offer->count);
        $costs = $offer->getCosts();
        foreach ($resNames as $rk => $rn) {
            $buyarr[$rk] = $costs->get($rk);
            $mr['buy_' . $rk] = $costs->get($rk);
        }

        // Prüft, ob genug Rohstoffe vorhanden sind
        if ($cp->checkRes($buyarr)) {
            $seller_user_nick = get_user_nick($offer->userId);

            // Rohstoffe vom Käuferplanet abziehen
            $cp->subRes($buyarr);

            $seller = new User($offer->userId);
            $sellerEntity = $entityRepository->getEntity($offer->entityId);
            $ownEntity = $entityRepository->getEntity((int) $cp->id);

            $tradeShip = new Ship(MARKET_SHIP_ID);

            $dist = $entityService->distance($sellerEntity, $ownEntity);
            $sellerFlighttime = ceil($dist / ($seller->specialist->tradeTime * $tradeShip->speed / 3600) + $tradeShip->time2start + $tradeShip->time2land);
            $buyerFlighttime = ceil($dist / ($cu->specialist->tradeTime * $tradeShip->speed / 3600) + $tradeShip->time2start + $tradeShip->time2land);

            $launchtime = time();
            $sellerLandtime = $launchtime + $sellerFlighttime;
            $buyerLandtime = $launchtime + $buyerFlighttime;

            // Fleet Seller -> Buyer
            $sellerFid = $fleetRepository->add($cu->getId(), $launchtime, (int) $buyerLandtime, $sellerEntity->id, $cp->id, \EtoA\Fleet\FleetAction::MARKET, \EtoA\Fleet\FleetStatus::DEPARTURE, new BaseResources());
            $fleetRepository->addShipsToFleet($sellerFid, $offer->shipId, $offer->count);

            $numBuyerShip = ($tradeShip->capacity > 0) ? ceil(array_sum($buyarr) / $tradeShip->capacity) : 1;

            // Fleet Buyer->Seller
            $buyerFid = $fleetRepository->add($seller->getId(), $launchtime, (int) $sellerLandtime, $cp->id, $sellerEntity->id, \EtoA\Fleet\FleetAction::MARKET, \EtoA\Fleet\FleetStatus::DEPARTURE, $costs);
            $fleetRepository->addShipsToFleet($buyerFid, MARKET_SHIP_ID, $numBuyerShip);

            $marketShipRepository->delete($offer->id);
            $cnt++;


            // Send report to seller
            MarketReport::addMarketReport(array(
                'user_id' => $offer->userId,
                'entity1_id' => $offer->entityId,
                'entity2_id' => $cp->id,
                'opponent1_id' => $cu->id,
            ), "shipsold", $offer->id, array_merge($mr, array("fleet1_id" => $sellerFid, "fleet2_id" => $buyerFid)));

            // Send report to buyer (the current user)
            MarketReport::addMarketReport(array(
                'user_id' => $cu->id,
                'entity1_id' => $cp->id,
                'entity2_id' => $offer->entityId,
                'opponent1_id' => $offer->userId,
            ), "shipbought", $offer->id, array_merge($mr, array("fleet1_id" => $buyerFid, "fleet2_id" => $sellerFid)));

            // Add market ratings
            $cu->rating->addTradeRating(TRADE_POINTS_PER_TRADE, false, 'Handel #' . $offer->id . ' mit ' . $offer->userId);
            if (strlen($offer->text) > TRADE_POINTS_TRADETEXT_MIN_LENGTH)
                $seller->rating->addTradeRating(TRADE_POINTS_PER_TRADE + TRADE_POINTS_PER_TRADETEXT, true, 'Handel #' . $offer->id . ' mit ' . $cu->id);
            else
                $seller->rating->addTradeRating(TRADE_POINTS_PER_TRADE, true, 'Handel #' . $offer->id . ' mit ' . $cu->id);



            //Log schreiben, falls dieser Handel regelwidrig ist
            /** @var UserMultiRepository $userMultiRepository */
            $userMultiRepository = $app[UserMultiRepository::class];
            $isMultiWith = $userMultiRepository->existsEntryWith($cu->getId(), $offer->userId);
            if ($isMultiWith) {
                /** @var ShipDataRepository $shipRepository */
                $shipRepository = $app[ShipDataRepository::class];
                $shipNames = $shipRepository->getShipNames(true);

                Log::add(Log::F_MULTITRADE, Log::INFO, "[page user sub=edit user_id=" . $cu->id . "][B]" . $cu->nick . "[/B][/page] hat von [page user sub=edit user_id=" . $offer->userId . "][B]" . $seller . "[/B][/page] Schiffe gekauft:\n\n" . $offer->count . " " . $shipNames[$offer->shipId] . "\n\nund das zu folgendem Preis:\n\n" . RES_METAL . ": " . nf($offer->costs0) . "\n" . RES_CRYSTAL . ": " . nf($offer->costs1) . "\n" . RES_PLASTIC . ": " . nf($offer->costs2) . "\n" . RES_FUEL . ": " . nf($offer->costs3) . "\n" . RES_FOOD . ": " . nf($offer->costs4));
            }

            //Marktlog schreiben
            //						Log::add(7,Log::INFO, "Der Spieler ".$cu->nick." hat folgende Schiffe von ".$seller_user_nick." gekauft:\n\n".$arr['ship_count']." ".$arr['ship_name']."\n\nund das zu folgendem Preis:\n\n".RES_METAL.": ".nf($arr['ship_costs_metal'])."\n".RES_CRYSTAL.": ".nf($arr['ship_costs_crystal'])."\n".RES_PLASTIC.": ".nf($arr['ship_costs_plastic'])."\n".RES_FUEL.": ".nf($arr['ship_costs_fuel'])."\n".RES_FOOD.": ".nf($arr['ship_costs_food']),time());

            // Zählt die erfolgreich abgewickelten Angebote

        } else {
            // Zählt die gescheiterten Angebote
            $cnt_error++;
        }
    } else {
        // Zählt die gescheiterten Angebote
        $cnt_error++;
    }
}

if ($cnt > 0) {
    success_msg("" . $cnt . " Angebot(e) erfolgreich gekauft!");
}
if ($cnt_error > 0) {
    error_msg("" . $cnt_error . " Angebot(e) sind nicht mehr vorhanden, oder die benötigten Rohstoffe sind nicht mehr verfügbar!");
}

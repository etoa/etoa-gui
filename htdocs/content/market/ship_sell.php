<?php

use EtoA\Fleet\FleetRepository;
use EtoA\Log\LogFacility;
use EtoA\Market\MarketShipRepository;
use EtoA\Message\MarketReportRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Resources\BaseResources;
use EtoA\User\UserMultiRepository;
use EtoA\User\UserRatingRepository;

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
/** @var MarketReportRepository $marketReportRepository */
$marketReportRepository = $app[MarketReportRepository::class];

foreach ($_POST['ship_market_id'] as $num => $id) {
    // Lädt Angebotsdaten
    $offer = $marketShipRepository->getBuyableOffer((int) $id, $cu->getId(), (int) $cu->allianceId());
    // Prüft, ob Angebot noch vorhanden ist
    if ($offer !== null) {
        $buyarr = array();
        $costs = $offer->getCosts();
        foreach ($resNames as $rk => $rn) {
            $buyarr[$rk] = $costs->get($rk);
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
            $marketReportRepository->addShipReport($offer->id, $offer->userId, $offer->entityId, $cu->getId(), $offer->shipId, $offer->count, "shipsold", $costs, 1.0, null, 0, $cp->id, $sellerFid, $buyerFid);

            // Send report to buyer (the current user)
            $marketReportRepository->addShipReport($offer->id, $cu->getId(), $cp->id, $offer->userId, $offer->shipId, $offer->count, "shipbought", $costs, 1.0, null, 0, $offer->entityId, $buyerFid, $sellerFid);

            // Add market ratings
            /** @var UserRatingRepository $userRatingRepository */
            $userRatingRepository = $app[UserRatingRepository::class];

            $userRatingRepository->addTradeRating(
                $cu->id,
                TRADE_POINTS_PER_TRADE,
                false,
                'Handel #' . $offer->id . ' mit ' . $offer->userId
            );
            if (strlen($offer->text) > TRADE_POINTS_TRADETEXT_MIN_LENGTH) {
                $userRatingRepository->addTradeRating(
                    $seller->id,
                    TRADE_POINTS_PER_TRADE + TRADE_POINTS_PER_TRADETEXT,
                    true,
                    'Handel #' . $offer->id . ' mit ' . $cu->id
                );
            } else {
                $userRatingRepository->addTradeRating(
                    $seller->id,
                    TRADE_POINTS_PER_TRADE,
                    true,
                    'Handel #' . $offer->id . ' mit ' . $cu->id
                );
            }

            //Log schreiben, falls dieser Handel regelwidrig ist
            /** @var UserMultiRepository $userMultiRepository */
            $userMultiRepository = $app[UserMultiRepository::class];
            $isMultiWith = $userMultiRepository->existsEntryWith($cu->getId(), $offer->userId);
            if ($isMultiWith) {
                /** @var ShipDataRepository $shipRepository */
                $shipRepository = $app[ShipDataRepository::class];
                $shipNames = $shipRepository->getShipNames(true);

                Log::add(LogFacility::MULTITRADE, Log::INFO, "[page user sub=edit user_id=" . $cu->id . "][B]" . $cu->nick . "[/B][/page] hat von [page user sub=edit user_id=" . $offer->userId . "][B]" . $seller . "[/B][/page] Schiffe gekauft:\n\n" . $offer->count . " " . $shipNames[$offer->shipId] . "\n\nund das zu folgendem Preis:\n\n" . RES_METAL . ": " . nf($offer->costs0) . "\n" . RES_CRYSTAL . ": " . nf($offer->costs1) . "\n" . RES_PLASTIC . ": " . nf($offer->costs2) . "\n" . RES_FUEL . ": " . nf($offer->costs3) . "\n" . RES_FOOD . ": " . nf($offer->costs4));
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

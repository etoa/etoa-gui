<?php

use EtoA\Fleet\FleetRepository;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Market\MarketHandler;
use EtoA\Market\MarketResourceRepository;
use EtoA\Message\MarketReportRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserMultiRepository;
use EtoA\User\UserRatingService;

/** @var MarketResourceRepository $marketResourceRepository */
$marketResourceRepository = $app[MarketResourceRepository::class];
/** @var EntityService $entityService */
$entityService = $app[EntityService::class];
/** @var EntityRepository $entityRepository */
$entityRepository = $app[EntityRepository::class];
/** @var FleetRepository $fleetRepository */
$fleetRepository = $app[FleetRepository::class];
/** @var MarketReportRepository $marketReportRepository */
$marketReportRepository = $app[MarketReportRepository::class];
/** @var LogRepository $logRepository */
$logRepository = $app[LogRepository::class];
/** @var PlanetRepository $planetRepository */
$planetRepository = $app[PlanetRepository::class];

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
                $planetRepository->removeResources($cp->id(), $buyResource);
                $cp->reloadRes();

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
                $sellerFid = $fleetRepository->add($cu->getId(), $launchtime, (int) $buyerLandtime, $id, $cp->id, \EtoA\Fleet\FleetAction::MARKET, \EtoA\Fleet\FleetStatus::DEPARTURE, $sellResources);
                $fleetRepository->addShipsToFleet($sellerFid, MARKET_SHIP_ID, $numSellerShip);

                $numBuyerShip = ($tradeShip->capacity > 0) ? ceil(array_sum($buyarr) / $tradeShip->capacity) : 1;

                // Fleet Buyer->Seller
                $buyerFid = $fleetRepository->add($seller->getId(), $launchtime, (int) $sellerLandtime, $cp->id, $sellerEntity->id, \EtoA\Fleet\FleetAction::MARKET, \EtoA\Fleet\FleetStatus::DEPARTURE, $buyResource);
                $fleetRepository->addShipsToFleet($buyerFid, MARKET_SHIP_ID, $numBuyerShip);

                // Angebot löschen
                $marketResourceRepository->delete($offer->id);

                // Add values for market rate calculation and
                foreach ($resNames as $rk => $rn) {
                    $supplyTotal[$rk] += $sellarr[$rk];
                    $demandTotal[$rk] += $buyarr[$rk];
                }

                // Send report to seller
                $marketReportRepository->addResourceReport($offer->id, $offer->userId, $offer->entityId, $cu->getId(), $sellResources, "ressold", $buyResource, 1.0, null, 0, $cp->id, $sellerFid, $buyerFid);

                // Send report to buyer (the current user)
                $marketReportRepository->addResourceReport($offer->id, $cu->getId(), $cp->id, $offer->userId, $sellResources, "resbought", $buyResource, 1.0, null, 0, $offer->entityId, $buyerFid, $sellerFid);

                // Add market ratings
                /** @var UserRatingService $userRatingService */
                $userRatingService = $app[UserRatingService::class];

                $userRatingService->addTradeRating(
                    $cu->id,
                    TRADE_POINTS_PER_TRADE,
                    false,
                    'Handel #' . $offer->id . ' mit ' . $offer->userId
                );
                if (strlen($offer->text) > TRADE_POINTS_TRADETEXT_MIN_LENGTH) {
                    $userRatingService->addTradeRating(
                        $seller->id,
                        TRADE_POINTS_PER_TRADE + TRADE_POINTS_PER_TRADETEXT,
                        true,
                        'Handel #' . $offer->id . ' mit ' . $cu->id
                    );
                } else {
                    $userRatingService->addTradeRating(
                        $seller->id,
                        TRADE_POINTS_PER_TRADE,
                        true,
                        'Handel #' . $offer->id . ' mit ' . $cu->id
                    );
                }

                // Log schreiben, falls dieser Handel regelwidrig ist
                /** @var UserMultiRepository $userMultiRepository */
                $userMultiRepository = $app[UserMultiRepository::class];
                $isMultiWith = $userMultiRepository->existsEntryWith($cu->getId(), $offer->userId);
                if ($isMultiWith) {
                    $logRepository->add(LogFacility::MULTITRADE, LogSeverity::INFO, "[page user sub=edit user_id=" . $cu->id . "][B]" . $cu->nick . "[/B][/page] hat von [page user sub=edit user_id=" . $offer->userId . "][B]" . $seller . "[/B][/page] Rohstoffe gekauft:\n\n" . RES_METAL . ": " . nf($offer->sell0) . "\n" . RES_CRYSTAL . ": " . nf($offer->sell1) . "\n" . RES_PLASTIC . ": " . nf($offer->sell2) . "\n" . RES_FUEL . ": " . nf($offer->sell3) . "\n" . RES_FOOD . ": " . nf($offer->sell4) . "\n\nDies hat ihn folgende Rohstoffe gekostet:\n" . RES_METAL . ": " . nf($offer->buy0) . "\n" . RES_CRYSTAL . ": " . nf($offer->buy1) . "\n" . RES_PLASTIC . ": " . nf($offer->buy2) . "\n" . RES_FUEL . ": " . nf($offer->buy3) . "\n" . RES_FOOD . ": " . nf($offer->buy4));
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
/** @var MarketHandler $marketHandler */
$marketHandler = $app[MarketHandler::class];
$marketHandler->addResToRate($supplyTotal, $demandTotal);

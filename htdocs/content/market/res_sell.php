<?php

use EtoA\Fleet\FleetRepository;
use EtoA\Legacy\User;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Market\MarketHandler;
use EtoA\Market\MarketResourceRepository;
use EtoA\Market\TradePoints;
use EtoA\Message\MarketReportRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipId;
use EtoA\Specialist\SpecialistService;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;
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
/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];
/** @var SpecialistService $specialistService */
$specialistService = $app[SpecialistService::class];

$cnt = 0;
$cnt_error = 0;

$supplyTotal = array_fill(0, count(ResourceNames::NAMES), 0);
$demandTotal = array_fill(0, count(ResourceNames::NAMES), 0);

if (isset($_POST['ressource_market_id'])) {
    foreach ($_POST['ressource_market_id'] as $num => $id) {
        // Lädt Angebotsdaten
        $offer = $marketResourceRepository->getBuyableOffer((int)$id, $cu->getId(), (int)$cu->allianceId());

        // Prüft, ob Angebot noch vorhanden ist
        if ($offer !== null) {
            // Prüft, ob genug Rohstoffe vorhanden sind
            $ok = true;
            $buyarr = array();
            $sellarr = array();
            $buyResource = $offer->getBuyResources();
            $sellResources = $offer->getSellResources();
            foreach (ResourceNames::NAMES as $rk => $rn) {
                $buyarr[$rk] = $buyResource->get($rk);
                $sellarr[$rk] = $sellResources->get($rk);
            }

            if ($cp->checkRes($buyarr)) {
                $planetRepository->removeResources($cp->id(), $buyResource);
                $cp->reloadRes();

                $sellerEntity = $entityRepository->getEntity($offer->entityId);
                $ownEntity = $entityRepository->getEntity((int)$cp->id);

                $id = $sellerEntity !== null ? $sellerEntity->id : 0;
                $tradeShip = $shipDataRepository->getShip(ShipId::MARKET, false);
                $numSellerShip = ($tradeShip->capacity > 0) ? ceil(array_sum($sellarr) / $tradeShip->capacity) : 1;

                $sellerSpecialist = $specialistService->getSpecialistOfUser($offer->userId);
                $specialist = $specialistService->getSpecialistOfUser($cu->id);

                $dist = $entityService->distance($sellerEntity, $ownEntity);
                $sellerFlighttime = ceil($dist / (($sellerSpecialist !== null ? $sellerSpecialist->tradeTime : 1) * $tradeShip->speed / 3600) + $tradeShip->timeToStart + $tradeShip->timeToLand);
                $buyerFlighttime = ceil($dist / (($specialist !== null ? $specialist->tradeTime : 1) * $tradeShip->speed / 3600) + $tradeShip->timeToStart + $tradeShip->timeToLand);

                $launchtime = time();
                $sellerLandtime = $launchtime + $sellerFlighttime;
                $buyerLandtime = $launchtime + $buyerFlighttime;


                // Fleet Seller -> Buyer
                $sellerFid = $fleetRepository->add($cu->getId(), $launchtime, (int)$buyerLandtime, $id, $cp->id, \EtoA\Fleet\FleetAction::MARKET, \EtoA\Fleet\FleetStatus::DEPARTURE, $sellResources);
                $fleetRepository->addShipsToFleet($sellerFid, ShipId::MARKET, $numSellerShip);

                $numBuyerShip = ($tradeShip->capacity > 0) ? ceil(array_sum($buyarr) / $tradeShip->capacity) : 1;

                // Fleet Buyer->Seller
                $buyerFid = $fleetRepository->add($offer->userId, $launchtime, (int)$sellerLandtime, $cp->id, $sellerEntity->id, \EtoA\Fleet\FleetAction::MARKET, \EtoA\Fleet\FleetStatus::DEPARTURE, $buyResource);
                $fleetRepository->addShipsToFleet($buyerFid, ShipId::MARKET, $numBuyerShip);

                // Angebot löschen
                $marketResourceRepository->delete($offer->id);

                // Add values for market rate calculation and
                foreach (ResourceNames::NAMES as $rk => $rn) {
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
                    TradePoints::POINTS_PER_TRADE,
                    false,
                    'Handel #' . $offer->id . ' mit ' . $offer->userId
                );
                if (strlen($offer->text) > TradePoints::POINTS_TRADE_TEXT_MIN_LENGTH) {
                    $userRatingService->addTradeRating(
                        $offer->userId,
                        TradePoints::POINTS_PER_TRADE + TradePoints::POINTS_PER_TRADE_TEXT,
                        true,
                        'Handel #' . $offer->id . ' mit ' . $cu->id
                    );
                } else {
                    $userRatingService->addTradeRating(
                        $offer->userId,
                        TradePoints::POINTS_PER_TRADE,
                        true,
                        'Handel #' . $offer->id . ' mit ' . $cu->id
                    );
                }

                // Log schreiben, falls dieser Handel regelwidrig ist
                /** @var UserMultiRepository $userMultiRepository */
                $userMultiRepository = $app[UserMultiRepository::class];
                $isMultiWith = $userMultiRepository->existsEntryWith($cu->getId(), $offer->userId);
                if ($isMultiWith) {
                    $seller = new User($offer->userId);
                    $logRepository->add(LogFacility::MULTITRADE, LogSeverity::INFO, "[page user sub=edit user_id=" . $cu->id . "][B]" . $cu->nick . "[/B][/page] hat von [page user sub=edit user_id=" . $offer->userId . "][B]" . $seller . "[/B][/page] Rohstoffe gekauft:\n\n" . ResourceNames::METAL . ": " . StringUtils::formatNumber($offer->sell0) . "\n" . ResourceNames::CRYSTAL . ": " . StringUtils::formatNumber($offer->sell1) . "\n" . ResourceNames::PLASTIC . ": " . StringUtils::formatNumber($offer->sell2) . "\n" . ResourceNames::FUEL . ": " . StringUtils::formatNumber($offer->sell3) . "\n" . ResourceNames::FOOD . ": " . StringUtils::formatNumber($offer->sell4) . "\n\nDies hat ihn folgende Rohstoffe gekostet:\n" . ResourceNames::METAL . ": " . StringUtils::formatNumber($offer->buy0) . "\n" . ResourceNames::CRYSTAL . ": " . StringUtils::formatNumber($offer->buy1) . "\n" . ResourceNames::PLASTIC . ": " . StringUtils::formatNumber($offer->buy2) . "\n" . ResourceNames::FUEL . ": " . StringUtils::formatNumber($offer->buy3) . "\n" . ResourceNames::FOOD . ": " . StringUtils::formatNumber($offer->buy4));
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

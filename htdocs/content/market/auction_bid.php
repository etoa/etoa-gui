<?php

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Market\MarketAuctionRepository;
use EtoA\Message\MarketReportRepository;
use EtoA\Support\RuntimeDataStore;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\User\UserMultiRepository;
use EtoA\User\UserRatingService;

/** @var PlanetRepository $planetRepository */
$planetRepository = $app[PlanetRepository::class];
/** @var RuntimeDataStore $runtimeDataStore */
$runtimeDataStore = $app[RuntimeDataStore::class];
/** @var MarketAuctionRepository $marketAuctionRepository */
$marketAuctionRepository = $app[MarketAuctionRepository::class];
/** @var MarketReportRepository $marketReportRepository */
$marketReportRepository = $app[MarketReportRepository::class];
/** @var LogRepository $logRepository */
$logRepository = $app[LogRepository::class];

// Speichert Bieterangebot in Array
$buyRes = array();
$newBuyResource = new BaseResources();
foreach ($resNames as $rk => $rn) {
    if (isset($_POST['new_buy_' . $rk])) {
        $newBuyResource->set($rk, nf_back($_POST['new_buy_' . $rk]));
        $buyRes[$rk] = nf_back($_POST['new_buy_' . $rk]);
    } else
        $buyRes[$rk] = 0;
}

$auction = $marketAuctionRepository->getNonUserAuction($_POST['auction_market_id'], $cu->getId());

// Prüft, ob Angebot noch vorhaden ist
if ($auction !== null && $auction->dateEnd > time()) {
    // Prüft, ob noch genug Rohstoffe vorhanden sind (eventueller Verlust durch Kampf?)
    if ($cp->checkRes($buyRes)) {
        $sell_price = 0;
        $current_price = 0;
        $new_price = 0;

        $sellResources = $auction->getSellResources();
        $currentBuyResources = $auction->getBuyResources();
        foreach ($resNames as $rk => $rn) {
            $rate = (float) $runtimeDataStore->get('market_rate_' . $rk, (string) 1);

            // Errechnet Rohstoffwert vom Angebot
            $sell_price += $sellResources->get($rk) * $rate;
            // Errechnet Rohstoffwert vom Höchstbietenden
            $current_price += $currentBuyResources->get($rk) * $rate;
            // Errechnet Rohstoffwert vom abgegebenen Gebot
            $new_price += $buyRes[$rk] * $rate;
        }

        // Prüft, ob Gebot höher ist als das vom Höchstbietenden
        if ($current_price * (1 + AUCTION_OVERBID) < $new_price) {


            // wenn der bietende das höchst mögliche (oder mehr) bietet...
            if (AUCTION_PRICE_FACTOR_MAX <= (ceil($new_price) / floor($sell_price))) {
                if ($auction->currentBuyerId !== 0) {
                    // Rohstoffe dem überbotenen User wieder zurückgeben
                    $planetRepository->addResources($auction->currentBuyerEntityId, $currentBuyResources->metal, $currentBuyResources->crystal, $currentBuyResources->plastic, $currentBuyResources->fuel, $currentBuyResources->food);

                    // Nachricht dem überbotenen User schicken
                    $marketReportRepository->addAuctionReport($auction->id, $auction->currentBuyerId, $cp->id, $cu->getId(), $sellResources, "auctionoverbid", $newBuyResource);
                }

                $bid = new BaseResources();
                foreach ($resNames as $rk => $rn) {
                    $bid->set($rk, $buyRes[$rk]);
                }

                // Rohstoffe dem Gewinner abziehen
                $planetRepository->removeResources($cp->id(), $bid);
                $cp->reloadRes();

                // Nachricht an Verkäufer
                $marketReportRepository->addAuctionReport($auction->id, $auction->userId, $cp->id, $cu->getId(), $sellResources, "auctionfinished", $newBuyResource);
                $marketReportRepository->addAuctionReport($auction->id, $cu->getId(), $cp->id, $auction->userId, $sellResources, "auctionwon", $newBuyResource);

                // Add market ratings
                /** @var UserRatingService $userRatingService */
                $userRatingService = $app[UserRatingService::class];

                $userRatingService->addTradeRating(
                    $cu->id,
                    TRADE_POINTS_PER_TRADE,
                    false,
                    'Handel #' . $auction->id . ' mit ' . $auction->userId
                );
                if (strlen($auction->text) > TRADE_POINTS_TRADETEXT_MIN_LENGTH) {
                    $userRatingService->addTradeRating(
                        $auction->userId,
                        TRADE_POINTS_PER_TRADE + TRADE_POINTS_PER_TRADETEXT,
                        true,
                        'Handel #' . $auction->id . ' mit ' . $cu->id
                    );
                } else {
                    $userRatingService->addTradeRating(
                        $auction->userId,
                        TRADE_POINTS_PER_TRADE,
                        true,
                        'Handel #' . $auction->id . ' mit ' . $cu->id
                    );
                }

                // Auktion Speichern und "Stoppen" so dass nicht mehr geboten werden kann
                $delete_date = time() + (AUCTION_DELAY_TIME * 3600);
                $marketAuctionRepository->addBid($auction->id, $cu->getId(), $cp->id(), $bid, true, $delete_date);

                //Log schreiben, falls dieser Handel regelwidrig ist
                /** @var UserMultiRepository $userMultiRepository */
                $userMultiRepository = $app[UserMultiRepository::class];
                $isMultiWith = $userMultiRepository->existsEntryWith($cu->getId(), $auction->userId);
                if ($isMultiWith) {
                    // TODO
                    $seller = new User($auction->userId);
                    $logRepository->add(LogFacility::MULTITRADE, LogSeverity::INFO, "[page user sub=edit user_id=" . $cu->id . "][B]" . $cu->nick . "[/B][/page] hat an einer Auktion von [page user sub=edit user_id=" . $auction->userId . "][B]" . $seller . "[/B][/page] gewonnen:\n\nRohstoffe:\n" . RES_METAL . ": " . StringUtils::formatNumber($auction->sell0) . "\n" . RES_CRYSTAL . ": " . StringUtils::formatNumber($auction->sell1) . "\n" . RES_PLASTIC . ": " . StringUtils::formatNumber($auction->sell2) . "\n" . RES_FUEL . ": " . StringUtils::formatNumber($auction->sell3) . "\n" . RES_FOOD . ": " . StringUtils::formatNumber($auction->sell4) . "\n\nDies hat ihn folgende Rohstoffe gekostet:\n" . RES_METAL . ": " . $newBuyResource->metal . "\n" . RES_CRYSTAL . ": " . $newBuyResource->crystal . "\n" . RES_PLASTIC . ": " . $newBuyResource->plastic . "\n" . RES_FUEL . ": " . $newBuyResource->fuel . "\n" . RES_FOOD . ": " . $newBuyResource->food . "");
                }

                // Log schreiben
                //// TODO
                //					Log::add(7, Log::INFO, "Es wurde folgende Auktion erfolgreich beendet: Der Spieler ".$cu->nick." hat vom Spieler ".$partner_user_nick."  folgende Waren ersteigert:\n\nRohstoffe:\n".RES_METAL.": ".StringUtils::formatNumber($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".StringUtils::formatNumber($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".StringUtils::formatNumber($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".StringUtils::formatNumber($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".StringUtils::formatNumber($arr['auction_sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:".RES_METAL.": ".StringUtils::formatNumber($_POST['auction_new_buy_metal'])."\n".RES_CRYSTAL.": ".StringUtils::formatNumber($_POST['auction_new_buy_crystal'])."\n".RES_PLASTIC.": ".StringUtils::formatNumber($_POST['auction_new_buy_plastic'])."\n".RES_FUEL.": ".StringUtils::formatNumber($_POST['auction_new_buy_fuel'])."\n".RES_FOOD.": ".StringUtils::formatNumber($_POST['auction_new_buy_food'])."\n\nDie Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gel&ouml;scht",time());


                success_msg("Gratulation, du hast die Auktion gewonnen, da du den maximal Betrag geboten hast!");

                // TODO: Market course update
            } else {
                if ($auction->currentBuyerId !== 0) {
                    // Rohstoffe dem überbotenen User wieder zurückgeben
                    $planetRepository->addResources($auction->currentBuyerEntityId, $currentBuyResources->metal, $currentBuyResources->crystal, $currentBuyResources->plastic, $currentBuyResources->fuel, $currentBuyResources->food);

                    // Nachricht dem überbotenen user schicken
                    $marketReportRepository->addAuctionReport($auction->id, $auction->currentBuyerId, $cp->id, $cu->getId(), $sellResources, "auctionoverbid", $newBuyResource, null, $auction->dateEnd);
                }

                $bid = new BaseResources();
                foreach ($resNames as $rk => $rn) {
                    $bid->set($rk, $buyRes[$rk]);
                }

                // Rohstoffe vom neuen Bieter abziehen
                $planetRepository->removeResources($cp->id(), $bid);
                $cp->reloadRes();

                //Das neue Angebot Speichern
                $marketAuctionRepository->addBid($auction->id, $cu->getId(), $cp->id(), $bid);
                success_msg("Gebot erfolgeich abgegeben!");
                echo "<p>" . button("Zurück zur Auktion", "?page=market&amp;mode=search&amp;searchcat=auctions&amp;auctionid=" . $auction->id . "") . "</p>";
            }
        } else {
            error_msg("Das Gebot muss mindestens " . AUCTION_OVERBID . "% höher sein als das Gebot des Höchstbietenden!");
            echo "<p>" . button("Zurück zur Auktion", "?page=market&amp;mode=search&amp;searchcat=auctions&amp;auctionid=" . $auction->id . "") . "</p>";
        }
    } else {
        error_msg("Die gebotenen Rohstoffe sind nicht mehr verfügbar!");
    }
} else {
    error_msg("Die Auktion ist nicht mehr vorhanden oder bereits abgelaufen!");
}

<?php

// Berechnet Endzeit
use EtoA\Log\LogFacility;
use EtoA\Market\MarketAuctionRepository;
use EtoA\Message\MarketReportRepository;
use EtoA\Universe\Resources\BaseResources;

/** @var MarketReportRepository $marketReportRepository */
$marketReportRepository = $app[MarketReportRepository::class];

$auction_min_time = AUCTION_MIN_DURATION * 24 * 3600;
$auction_time_days = $_POST['auction_time_days'];
$auction_time_hours = $_POST['auction_time_hours'];
$auction_end_time = time() + $auction_min_time + $auction_time_days * 24 * 3600 + $auction_time_hours * 3600;

$ok = true;
$subtracted = [];
$currency = new BaseResources();
$sell = new BaseResources();

foreach ($resNames as $rk => $rn) {
    // Convert formatted number back to integer
    $_POST['auction_sell_' . $rk] = nf_back($_POST['auction_sell_' . $rk]);

    $sell->set($rk, max((int) nf_back($_POST['auction_sell_' . $rk]), 0));
    $currency->set($rk, max(0, nf_back($_POST['auction_buy_' . $rk] ?? 0)));

    // Prüft ob noch immer genug Rohstoffe auf dem Planeten sind (eventueller verlust durch Kampf?)
    if ($sell->get($rk) > 0 && $sell->get($rk) * MARKET_TAX > $cp->resources[$rk]) {
        $ok = false;
        break;
    }

    // Save resource to be subtracted from the planet
    $subtracted[$rk] = $sell->get($rk) * MARKET_TAX;
}

$ship_update = 0;
$ress_update = 0;

// Prüft ob Rohstoffe noch vorhanden sind (eventueller verlust durch Kampf?)
if ($ok && $cp->checkRes($subtracted)) {
    // Rohstoffe + Taxe vom Planetenkonto abziehen
    $cp->subRes($subtracted);

    // Angebot speichern
    /** @var MarketAuctionRepository $marketAuctionRepository */
    $marketAuctionRepository = $app[MarketAuctionRepository::class];
    $auctionId = $marketAuctionRepository->add($cu->getId(), $cp->id(), $auction_end_time, $_POST['auction_text'], $sell, $currency);

    //Nachricht senden
    $marketReportRepository->addAuctionReport($auctionId, $cu->getId(), $cp->id, 0, $sell, "auctionadd", $currency, $_POST['auction_text'], MARKET_TAX, $auction_end_time);
    Log::add(LogFacility::MARKET, Log::INFO, "Der Spieler " . $cu->nick . " hat folgende Rohstoffe zur versteigerung angeboten:\n\n" . RES_METAL . ": " . $sell->metal . "\n" . RES_CRYSTAL . ": " . $sell->crystal . "\n" . RES_PLASTIC . ": " . $sell->plastic . "\n" . RES_FUEL . ": " . $sell->fuel . "\n" . RES_FOOD . ": " . $sell->food . "\n\nAuktionsende: " . date("d.m.Y H:i", $auction_end_time) . "");

    // todo: report

    success_msg("Auktion erfolgreich lanciert");
    return_btn();
} else {
    error_msg("Die angegebenen Rohstoffe sind nicht mehr verfügbar!");
    return_btn();
}

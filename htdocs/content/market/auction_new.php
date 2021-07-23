<?php

// Berechnet Endzeit
use EtoA\Market\MarketAuctionRepository;
use EtoA\Universe\Resources\BaseResources;

$auction_min_time = AUCTION_MIN_DURATION * 24 * 3600;
$auction_time_days = $_POST['auction_time_days'];
$auction_time_hours = $_POST['auction_time_hours'];
$auction_end_time = time() + $auction_min_time + $auction_time_days * 24 * 3600 + $auction_time_hours * 3600;
$marr = array('factor' => MARKET_TAX, 'timestamp2' => $auction_end_time);

$ok = true;
$sf = "";
$sv = "";
$subtracted = [];
$currency = new BaseResources();
$sell = new BaseResources();

foreach ($resNames as $rk => $rn) {
    // Convert formatted number back to integer
    $_POST['auction_sell_' . $rk] = nf_back($_POST['auction_sell_' . $rk]);

    // Prüft ob noch immer genug Rohstoffe auf dem Planeten sind (eventueller verlust durch Kampf?)
    if (isset($_POST['auction_sell_' . $rk]) && $_POST['auction_sell_' . $rk] * MARKET_TAX > $cp->resources[$rk]) {
        $ok = false;
        break;
    }

    // Save resource to be subtracted from the planet
    $subtracted[$rk] = $_POST['auction_sell_' . $rk] * MARKET_TAX;

    $sell->set($rk, (int) $_POST['auction_sell_' . $rk]);
    $currency->set($rk, $_POST['auction_buy_' . $rk] ?? 0);

    // Report data
    if ($_POST['auction_sell_' . $rk] > 0)
        $marr['sell_' . $rk] = $_POST['auction_sell_' . $rk];
    if (isset($_POST['res_buy_' . $rk]) && $_POST['res_buy_' . $rk] > 0)
        $marr['buy_' . $rk] = intval($_POST['auction_buy_' . $rk]);
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
    MarketReport::addMarketReport(array(
        'user_id' => $cu->id,
        'entity1_id' => $cp->id,
        'content' => $_POST['auction_text']
    ), "auctionadd", $auctionId, $marr);

    Log::add(MARKET_LOG_CAT, Log::INFO, "Der Spieler " . $cu->nick . " hat folgende Rohstoffe zur versteigerung angeboten:\n\n" . RES_METAL . ": " . nf($_POST['auction_sell_0']) . "\n" . RES_CRYSTAL . ": " . nf($_POST['auction_sell_1']) . "\n" . RES_PLASTIC . ": " . nf($_POST['auction_sell_2']) . "\n" . RES_FUEL . ": " . nf($_POST['auction_sell_3']) . "\n" . RES_FOOD . ": " . nf($_POST['auction_sell_4']) . "\n\nAuktionsende: " . date("d.m.Y H:i", $auction_end_time) . "");

    // todo: report

    success_msg("Auktion erfolgreich lanciert");
    return_btn();
} else {
    error_msg("Die angegebenen Rohstoffe sind nicht mehr verfügbar!");
    return_btn();
}

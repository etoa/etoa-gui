<?php

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Market\MarketShipRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\User\UserRepository;

/** @var int $alliance_market_level */
/** @var bool $cd_enabled */
/** @var int $cooldown */

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var ShipRepository $shipRepository */
$shipRepository = $app[ShipRepository::class];
/** @var MarketShipRepository $marketShipRepository */
$marketShipRepository = $app[MarketShipRepository::class];
/** @var AllianceBuildingRepository $allianceBuildingRepository */
$allianceBuildingRepository = $app[AllianceBuildingRepository::class];

$for_user = 0;
$for_alliance = 0;
$errMsg = null;

if ($_POST['ship_offer_reservation'] == 1) {
    $for_user = $userRepository->getUserIdByNick(trim($_POST['ship_offer_user_nick']));
    if ($for_user == 0) {
        $errMsg = "Reservation nicht möglich, Spieler nicht gefunden!";
    }
}
if ($_POST['ship_offer_reservation'] == 2) {
    if ($alliance_market_level > 0 && !$cd_enabled) {
        $for_alliance = $cu->allianceId;
    } else {
        $errMsg = "Reservation nicht möglich, Allianzmarkt nicht vorhanden oder nicht bereit!";
    }
}

if (!isset($errMsg)) {

    $ship_id = $_POST['ship_list'];
    $ship_count = nf_back($_POST['ship_count']);

    $marr = array("ship_id" => $ship_id, "ship_count" => $ship_count);
    $costs = new BaseResources();
    foreach ($resNames as $rk => $rn) {
        // Convert formatted number back to integer
        $_POST['ship_buy_' . $rk] = nf_back($_POST['ship_buy_' . $rk]);
        $costs->set($rk, (int) $_POST['ship_buy_' . $rk]);
        $marr['buy_' . $rk] = $_POST['ship_buy_' . $rk];
    }

    // Überprüft ob die angegebene Anzahl Schiffe noch vorhanden ist (eventuelle Zerstörung durch Kampf?)
    // Schiffe vom Planeten abziehen
    $removed_ships_count = $shipRepository->removeShips((int) $ship_id, (int) $ship_count, $cu->getId(), (int) $cp->id);

    // Falls alle Schiffe abgezogen werden konnten
    if ($ship_count == $removed_ships_count) {
        // Angebot speicherns
        $offerId = $marketShipRepository->add($cu->getId(), (int) $cp->id(), (int) $for_user, (int) $for_alliance, $_POST['ship_text'], $ship_id, $ship_count, $costs);

        MarketReport::addMarketReport(array(
            'user_id' => $cu->id,
            'entity1_id' => $cp->id,
            'content' => $_POST['ship_text']
        ), "shipadd", $offerId, $marr);


        if ($for_alliance > 0) {
            // Set cooldown
            $cd = time() + $cooldown;
            $allianceBuildingRepository->setCooldown($cu->allianceId(), AllianceBuildingId::MARKET, $cd);

            $cu->alliance->buildlist->setCooldown(AllianceBuildingId::MARKET, $cd);
        }

        success_msg("Angebot erfolgreich abgesendet!");
        return_btn();
    } else {
        // if only some ships have been removed, re-add the removed ships
        if ($removed_ships_count > 0) {
            $shipRepository->addShip($ship_id, $removed_ships_count, $cu->getId(), (int) $cp->id);
            // log action because this was a bug earlier
            Log::add(
                Log::F_ILLEGALACTION,
                Log::WARNING,
                'User ' . $cu->nick . ' hat versucht, auf dem Planeten' . $cp->name()
                    . ' mehr Schiffe der ID ' . $ship_id . ' zu verkaufen, als vorhanden sind.'
                    . ' Vorhanden: ' . $removed_ships_count . ', Versuchte Verkaufszahl: ' . $ship_count
            );
        }
        error_msg("Die angegebenen Schiffe sind nicht mehr vorhanden!");
        return_btn();
    }
} else {
    error_msg($errMsg);
}

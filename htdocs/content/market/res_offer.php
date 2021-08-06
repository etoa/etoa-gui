<?php

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Market\MarketResourceRepository;
use EtoA\Message\MarketReportRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\User\UserRepository;

/** @var int $alliance_market_level */
/** @var bool $cd_enabled */
/** @var int $cooldown */

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var MarketResourceRepository $marketResourceRepository */
$marketResourceRepository = $app[MarketResourceRepository::class];
/** @var AllianceBuildingRepository $allianceBuildingRepository */
$allianceBuildingRepository = $app[AllianceBuildingRepository::class];
/** @var MarketReportRepository $marketReportRepository */
$marketReportRepository = $app[MarketReportRepository::class];

$for_user = 0;
$for_alliance = 0;

if ($_POST['resource_offer_reservation'] == 1) {
    $for_user = $userRepository->getUserIdByNick(trim($_POST['resource_offer_user_nick']));
    if ($for_user == 0) {
        $errMsg = "Reservation nicht möglich, Spieler nicht gefunden!";
    }
}
if ($_POST['resource_offer_reservation'] == 2) {
    if ($alliance_market_level > 0 && !$cd_enabled) {
        $for_alliance = $cu->allianceId;
    } else {
        $errMsg = "Reservation nicht möglich, Allianzmarkt nicht vorhanden oder nicht bereit!";
    }
}

if (!isset($errMsg)) {
    $ok = true;    // Checker for valid resources
    $subtracted = array(); // Resource to be subtracted from planet
    $sf = "";
    $sv = "";

    $sellResources = new BaseResources();
    $costs = new BaseResources();
    foreach ($resNames as $rk => $rn) {
        // Convert formatted number back to integer
        $_POST['res_sell_' . $rk] = nf_back($_POST['res_sell_' . $rk]);
        if (isset($_POST['res_buy_' . $rk]))
            $_POST['res_buy_' . $rk] = nf_back($_POST['res_buy_' . $rk]);

        // Prüft ob noch immer genug Rohstoffe auf dem Planeten sind (eventueller verlust durch Kampf?)
        if (isset($_POST['res_sell_' . $rk]) && $_POST['res_sell_' . $rk] * MARKET_TAX > $cp->resources[$rk]) {
            $ok = false;
            break;
        }

        // Save resource to be subtracted from the planet
        $subtracted[$rk] = $_POST['res_sell_' . $rk] * MARKET_TAX;

        // Build query
        $sellResources->set($rk, (int) $_POST['res_sell_' . $rk]);

        if (isset($_POST['res_buy_' . $rk])) {
            $costs->set($rk, (int) $_POST['res_buy_' . $rk]);
        }
    }

    if ($ok) {
        // Rohstoffe vom Planet abziehen
        if ($cp->subRes($subtracted)) {

            // Angebot speichern
            $offerId = $marketResourceRepository->add($cu->getId(), (int) $cp->id, (int) $for_user, (int) $for_alliance, $_POST['ressource_text'], $sellResources, $costs);
            if ($for_alliance > 0) {
                // Set cooldown
                $cd = time() + $cooldown;
                $allianceBuildingRepository->setCooldown($cu->allianceId(), AllianceBuildingId::MARKET, $cd);
            }

            $marketReportRepository->addResourceReport($offerId, $cu->id, $cp->id, 0, $sellResources, "resadd", $costs, MARKET_TAX, $_POST['ressource_text']);

            success_msg("Angebot erfolgreich aufgegeben");
            return_btn();
        } else {
            error_msg("Es gab ein Problem beim Reservieren der Rohstoffe!");
            return_btn();
        }
    } else {
        error_msg("Es sind nicht mehr genügend Rohstoffe vorhanden!");
        return_btn();
    }
} else {
    error_msg($errMsg);
}

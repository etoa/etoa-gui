<?PHP

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Bookmark\BookmarkService;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Fleet\Exception\FleetScanFailedException;
use EtoA\Fleet\Exception\FleetScanPreconditionsNotMetException;
use EtoA\Fleet\FleetScanService;
use EtoA\Fleet\Exception\InvalidFleetScanParameterException;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var BookmarkService $bookmarkService */
$bookmarkService = $app[BookmarkService::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var PlanetRepository $planetRepository */
$planetRepository = $app[PlanetRepository::class];

/** @var EntityRepository $entityRepository */
$entityRepository = $app[EntityRepository::class];

/** @var FleetScanService $fleetScanService */
$fleetScanService = $app[FleetScanService::class];

/** @var AllianceBuildingRepository $allianceBuildingRepository */
$allianceBuildingRepository = $app[AllianceBuildingRepository::class];

/** @var ResourceBoxDrawer $resourceBoxDrawer */
$resourceBoxDrawer = $app[ResourceBoxDrawer::class];

/** @var Request */
$request = Request::createFromGlobals();

$planet = $planetRepository->find($cp->id);
$currentUser = $userRepository->getUser($cu->id);

// Gebäude Level und Arbeiter laden
$cryptoCenterLevel = $allianceBuildingRepository->getLevel($currentUser->allianceId, AllianceBuildingId::CRYPTO);

// Allg. deaktivierung
if ($config->getBoolean('crypto_enable')) {
    // Prüfen ob Gebäude gebaut ist
    if ($cryptoCenterLevel > 0) {
        echo "<h1>Allianzkryptocenter (Stufe " . $cryptoCenterLevel . ") der Allianz " . $cu->alliance . "</h1>";
        echo $resourceBoxDrawer->getHTML($planet);

        if ($request->request->has('scan') && checker_verify()) {
            if ($cu->alliance->checkActionRightsNA(AllianceRights::CRYPTO_MINISTER)) {
                $targetCoordinates = new EntityCoordinates(
                    $request->request->getInt('sx'),
                    $request->request->getInt('sy'),
                    $request->request->getInt('cx'),
                    $request->request->getInt('cy'),
                    $request->request->getInt('p')
                );
                $targetEntity = $entityRepository->findByCoordinates($targetCoordinates);
                try {
                    $out = $fleetScanService->scanFleets($currentUser, $planet, $cryptoCenterLevel, $targetEntity);

                    iBoxStart("Ergebnis der Analyse");
                    echo BBCodeUtils::toHTML($out);
                    iBoxEnd();
                } catch (FleetScanPreconditionsNotMetException | InvalidFleetScanParameterException | FleetScanFailedException $ex) {
                    error_msg($ex->getMessage());
                }
            } else {
                error_msg("Du besitzt nicht die notwendigen Rechte!");
            }
        }

        $userCooldownDiff = $fleetScanService->getUserCooldownDifference($currentUser->id);

        tableStart("Kryptocenter-Infos");
        echo "<tr><th>Aktuelle Reichweite:</th>
                <td>" . StringUtils::formatNumber($config->getInt('crypto_range_per_level') * $cryptoCenterLevel) . " AE ~" . floor($config->getInt('crypto_range_per_level') * $cryptoCenterLevel / $config->getInt('cell_length')) . " Systeme (+" . $config->getInt('crypto_range_per_level') . " pro Stufe) </td></tr>";
        if ($userCooldownDiff == 0) {
            echo '<tr><th>Zielinfo:</th><td id="targetinfo">
                                Wähle bitte ein Ziel...
                                </td></tr>';
            echo '<tr><th>Entfernung:</th><td id="distance">-
                    </td></tr>';
        }
        echo "<tr><th>Kosten pro Scan:</th>
                <td>" . StringUtils::formatNumber($config->getInt('crypto_fuel_costs_per_scan')) . " " . RES_FUEL . " und " . StringUtils::formatNumber($config->getInt('crypto_fuel_costs_per_scan')) . " " . RES_FUEL . " Allianzrohstoffe</td></tr>";
        echo "<tr><th>Abklingzeit:</th>
                <td>" . StringUtils::formatTimespan($fleetScanService->calculateCooldown($cryptoCenterLevel)) . " (-" . StringUtils::formatTimespan($config->getInt("crypto_cooldown_reduction_per_level")) . " pro Stufe, minimal " . StringUtils::formatTimespan($config->getInt("crypto_min_cooldown")) . ")</td></tr>";
        $statusText = $userCooldownDiff > 0 ? "Bereit in <span id=\"cdcd\">" . StringUtils::formatTimespan($userCooldownDiff) . "</span>" : "Bereit";
        echo "<tr><th>Status:</th>
                <td>" . $statusText . "</td></tr>";
        tableEnd();

        if ($fleetScanService->getUserCooldownDifference($currentUser->id) == 0) {
            $planetEntity = $entityRepository->findIncludeCell($planet->id);

            $coords = $planetEntity->getCoordinates();
            if ($request->query->has('target') && $request->query->getInt('target') > 0) {
                $targetEntity = $entityRepository->findIncludeCell($request->query->getInt('target'));
                if ($targetEntity !== null) {
                    $coords = $targetEntity->getCoordinates();
                }
            } elseif ($request->request->has('scan')) {
                $coords = new EntityCoordinates(
                    $request->request->getInt('sx'),
                    $request->request->getInt('sy'),
                    $request->request->getInt('cx'),
                    $request->request->getInt('cy'),
                    $request->request->getInt('p')
                );
            }

            $keyup_command = 'xajax_getCryptoDistance(xajax.getFormValues(\'targetForm\'),' . $planetEntity->sx . ',' . $planetEntity->sy . ',' . $planetEntity->cx . ',' . $planetEntity->cy . ',' . $planetEntity->pos . ');';
            echo '<body onload="' . $keyup_command . '">';
            echo '<form action="?page=' . $page . '" method="post" id="targetForm">';
            echo '<input type="hidden" value=' . $config->getInt('crypto_range_per_level') * $cryptoCenterLevel . ' name="range" />';
            checker_init();

            iBoxStart("Ziel für Flottenanalyse wahlen:");
            echo 'Koordinaten eingeben:
                            <input type="text" onkeyup="' . $keyup_command . '" name="sx" id="sx" value="' . $coords->sx . '" size="2" maxlength="2" /> /
                            <input type="text" onkeyup="' . $keyup_command . '" name="sy" id="sy" value="' . $coords->sy . '" size="2" maxlength="2" /> :
                            <input type="text" onkeyup="' . $keyup_command . '" name="cx" id="cx" value="' . $coords->cx . '" size="2" maxlength="2" /> /
                            <input type="text" onkeyup="' . $keyup_command . '" name="cy" id="cy" value="' . $coords->cy . '" size="2" maxlength="2" /> :
                            <input type="text" onkeyup="' . $keyup_command . '" name="p" id="p" value="' . $coords->pos . '" size="2" maxlength="2" /><br /><br />';
            echo '<i>oder</i> Favorit wählen: ';
            echo $bookmarkService->drawSelector($currentUser->id, "bookmark_select", "applyBookmark();");
            iBoxEnd();

            echo "<script type=\"text/javascript\">
                    function applyBookmark()
                    {
                        let select_id = document.getElementById('bookmark_select').selectedIndex;
                        let select_val = document.getElementById('bookmark_select').options[select_id];
                        if (select_val && select_val.dataset.sx)
                        {
                            document.getElementById('sx').value = select_val.dataset.sx;
                            document.getElementById('sy').value = select_val.dataset.sy;
                            document.getElementById('cx').value = select_val.dataset.cx;
                            document.getElementById('cy').value = select_val.dataset.cy;
                            document.getElementById('p').value = select_val.dataset.pos;
                            " . $keyup_command . "
                        } else {
                            document.getElementById('sx').value = '';
                            document.getElementById('sy').value = '';
                            document.getElementById('cx').value = '';
                            document.getElementById('cy').value = '';
                            document.getElementById('p').value = '';
                        }
                    }
                    </script>";

            if ($planet->resFuel >= $config->getInt('crypto_fuel_costs_per_scan')) {
                echo '<input type="submit" name="scan" value="Analyse für ' . StringUtils::formatNumber($config->getInt('crypto_fuel_costs_per_scan')) . ' ' . RES_FUEL . ' starten" />';
            } else {
                echo "Zuwenig Rohstoffe für eine Analyse vorhanden, " . StringUtils::formatNumber($config->getInt('crypto_fuel_costs_per_scan')) . " " . RES_FUEL . " benötigt, " . StringUtils::formatNumber($planet->resFuel) . " vorhanden!";
            }
            echo '</form>';
            echo '</body>';
        } else {
            $userCooldown = $allianceBuildingRepository->getUserCooldown($currentUser->id, AllianceBuildingId::CRYPTO);
            echo "<b>Diese Funktion wurde vor kurzem benutzt! <br/>";
            echo "Du musst bis " . StringUtils::formatDate($userCooldown) . " warten, um die Funktion wieder zu benutzen!</b>";
            countDown("cdcd", $userCooldown);
        }
    } else {
        echo "<h1>Kryptocenter des Planeten " . $planet->name . "</h1>";
        echo $resourceBoxDrawer->getHTML($planet);

        info_msg("Das Kryptocenter wurde noch nicht gebaut!");
    }
} else {
    echo "<h1>Kryptocenter des Planeten " . $planet->name . "</h1>";
    echo $resourceBoxDrawer->getHTML($planet);

    info_msg("Aufgrund eines intergalaktischen Moratoriums der Völkerföderation der Galaxie Andromeda
    sind sämtliche elektronischen Spionagetätigkeiten zurzeit nicht erlaubt!");
}

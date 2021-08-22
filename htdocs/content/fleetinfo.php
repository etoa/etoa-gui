<?PHP

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var AllianceBuildingRepository $allianceBuildingRepository */
$allianceBuildingRepository = $app[AllianceBuildingRepository::class];
/** @var AllianceRepository $allianceRepository */
$allianceRepository = $app[AllianceRepository::class];
/** @var UserUniverseDiscoveryService $userUniverseDiscoveryService */
$userUniverseDiscoveryService = $app[UserUniverseDiscoveryService::class];
/** @var LogRepository $logRepository */
$logRepository = $app[LogRepository::class];

$user = $userRepository->getUser($cu->id);

echo "<h1>Flotten</h1>";
echo "<h2>Details</h2>";

$fleet_id = (isset($_GET['id']) && intval($_GET['id']) > 0) ? intval($_GET['id']) : -1;
$lead_id = (isset($_GET['lead_id']) && intval($_GET['lead_id']) > 0) ? intval($_GET['lead_id']) : -1;

$valid = 0;

$fd = new Fleet($fleet_id, -1, $lead_id);
if ($fd->valid()) {
    if ($fd->ownerId() == $cu->id) {
        $valid = 10;
    } elseif ($cu->alliance->checkActionRightsNA(AllianceRights::FLEET_MINISTER)) {
        $allianceFleetControlLevel = $allianceBuildingRepository->getLevel($cu->allianceId(), AllianceBuildingId::FLEET_CONTROL);
        if ($fd->getAction()->code() == "support" && $fd->ownerAllianceId() == $cu->allianceId() && $cu->allianceId() > 0 && ($fd->status() == 0 || $fd->status() == 3)) {
            $valid = $allianceFleetControlLevel;
        } elseif ($fd->getAction()->code() == "alliance" && $fd->ownerAllianceId() == $cu->allianceId() && $cu->allianceId() > 0) {
            if ($fd->status() == 0) {
                if ($lead_id > 0 && ($allianceFleetControlLevel >= ALLIANCE_FLEET_SHOW)) {
                    $valid = $allianceFleetControlLevel;
                }
            } elseif ($fd->status() == 3) {
                if ($allianceFleetControlLevel >= ALLIANCE_FLEET_SHOW_PART) {
                    $valid = $allianceFleetControlLevel;
                }
            }
        }
    }
}

if ($valid > 0) {
    // Flugabbruch ausl�sen
    if (isset($_POST['cancel']) != "" && checker_verify()) {
        if ($valid >= ALLIANCE_FLEET_SEND_HOME_PART) {
            if ($fd->cancelFlight()) {
                success_msg("Flug erfolgreich abgebrochen!");
            } else {
                error_msg("Flug konnte nicht abgebrochen werden. " . $fd->getError());
            }
        } else {
            error_msg("Flug konnte nicht abgebrochen werden, da die Rechte nicht vorhanden sind!");
        }
    }

    if (isset($_POST['cancel_alliance']) != "" && checker_verify()) {
        if ($valid >= ALLIANCE_FLEET_SEND_HOME) {
            if ($fd->cancelFlight(true)) {
                success_msg("Flug erfolgreich abgebrochen!");
                $logRepository->add(LogFacility::FLEETACTION, LogSeverity::INFO, "Der Spieler [b]" . $cu->nick . "[/b] bricht den ganzen Allianzflug seiner Flotte [b]" . $fleet_id . "[/b] ab");
            } else {
                error_msg("Flug konnte nicht abgebrochen werden. " . $fd->getError());
            }
        } else {
            error_msg("Flug konnte nicht abgebrochen werden, da die Rechte nicht vorhanden sind!");
        }
    }


    echo "<table style=\"width:98%\">
        <tr><td colspan=\"3\">";

    iBoxStart("Flugdaten", "fleetInfoContainer");
    echo "<div class=\"fleetInfoWrap\">";
    echo "<div class=\"fleetInfoProgress\" id=\"fleetProgress\"></div>";
    echo "<div id=\"source\" class=\"fleetInfoSource\">";
    if ($fd->getAction()->visibleSource()) {
        if ($userUniverseDiscoveryService->discovered($user, $fd->getSource()->getCell()->absX(), $fd->getSource()->getCell()->absY())) {
            echo $fd->getSource()->smallImage() . "<br/>
                    <a href=\"?page=cell&amp;id=" . $fd->getSource()->cellId() . "&amp;hl=" . $fd->getSource()->id() . "\">" . $fd->getSource() . "</a><br/>";
        } else {
            $ent = Entity::createFactory('u', $fd->getSource()->id());
            echo $ent->smallImage() . "<br/>
                    <a href=\"?page=cell&amp;id=" . $ent->cellId() . "&amp;hl=" . $ent->id() . "\">" . $ent . "</a><br/>";
        }
    } else {
        echo $fd->getSource()->smallImage() . "<br />" . $fd->getSource()->entityCodeString() . "<br />";
    }
    echo "<b><span id=\"sourceLabel\">Start</span>:</b> " . date("d.m.Y H:i:s", $fd->launchTime()) . "
            </div>";
    echo "<div class=\"fleetInfoAction\">
                <div style=\"color:" . FleetAction::$attitudeColor[$fd->getAction()->attitude()] . "\">" . $fd->getAction()->name() . " [" . FleetAction::$statusCode[$fd->status()] . "]</div>
                <div><span id=\"flighttime\"></span> (<span id=\"flightPercent\"></span>)</div>
            </div>";
    echo "<div id=\"target\" class=\"fleetInfoTarget\">";
    if ($userUniverseDiscoveryService->discovered($user, $fd->getTarget()->getCell()->absX(), $fd->getTarget()->getCell()->absY())) {
        echo $fd->getTarget()->smallImage() . "<br/>
                <a href=\"?page=cell&amp;id=" . $fd->getTarget()->cellId() . "&amp;hl=" . $fd->getTarget()->id() . "\">" . $fd->getTarget() . "</a><br/>";
    } else {
        $ent = Entity::createFactory('u', $fd->getTarget()->id());
        echo $ent->smallImage() . "<br/>
                <a href=\"?page=cell&amp;id=" . $ent->cellId() . "&amp;hl=" . $ent->id() . "\">" . $ent . "</a><br/>";
    }
    echo "<b><span id=\"targetLabel\">Landung</span>:</b> " . date("d.m.Y H:i:s", $fd->landTime()) . "
            </div>";
    echo "</div>";
    iBoxEnd();

    echo "</td></tr><tr><td style=\"50%\">";

    //Allianzbox
    echo "<div id=\"allianceBox\" style=\"display:none;\"></div>";

    // Flugdaten
    tableStart("Piloten &amp; Verbrauch", "50%");
    echo "<tr>
            <th style=\"width:150px;\">" . RES_ICON_PEOPLE . "Piloten:</th>
            <td class=\"tbldata\">" . StringUtils::formatNumber($fd->pilots()) . "</td></tr>";
    echo "<tr>
            <th>" . RES_ICON_FUEL . "" . RES_FUEL . ":</th>
            <td class=\"tbldata\">" . StringUtils::formatNumber($fd->usageFuel()) . "</td></tr>";
    echo "<tr>
            <th>" . RES_ICON_FOOD . "" . RES_FOOD . ":</th>
            <td class=\"tbldata\">" . StringUtils::formatNumber($fd->usageFood()) . "</td></tr>";
    echo "<tr>
            <th>" . RES_ICON_POWER . " " . RES_POWER . ":</th>
            <td class=\"tbldata\">" . StringUtils::formatNumber($fd->usagePower()) . "</td></tr>";
    tableEnd();

    tableStart("Passagierraum", "50%");
    echo "<tr><th>" . RES_ICON_PEOPLE . "Passagiere</th><td class=\"tbldata\">" . StringUtils::formatNumber($fd->resPeople()) . "</td></tr>";
    echo "<tr><th style=\"width:150px;\">Freier Platz:</th><td class=\"tbldata\">" . StringUtils::formatNumber($fd->getFreePeopleCapacity()) . "</td></tr>";
    echo "<tr><th style=\"width:150px;\">Totaler Platz:</th><td class=\"tbldata\">" . StringUtils::formatNumber($fd->getPeopleCapacity()) . "</td></tr>";
    tableEnd();

    echo "</td><td style=\"width:5%;vertical-align:top;\"></td><td style=\"width:45%;vertical-align:top;\">";

    // Frachtraum
    tableStart("Frachtraum", "50%");
    echo "<tr><th>" . RES_ICON_METAL . "" . RES_METAL . "</th><td class=\"tbldata\">" . StringUtils::formatNumber($fd->resMetal()) . " t</td></tr>";
    echo "<tr><th>" . RES_ICON_CRYSTAL . "" . RES_CRYSTAL . "</th><td class=\"tbldata\" >" . StringUtils::formatNumber($fd->resCrystal()) . " t</td></tr>";
    echo "<tr><th>" . RES_ICON_PLASTIC . "" . RES_PLASTIC . "</th><td class=\"tbldata\">" . StringUtils::formatNumber($fd->resPlastic()) . " t</td></tr>";
    echo "<tr><th>" . RES_ICON_FUEL . "" . RES_FUEL . "</th><td class=\"tbldata\">" . StringUtils::formatNumber($fd->resFuel()) . " t</td></tr>";
    echo "<tr><th>" . RES_ICON_FOOD . "" . RES_FOOD . "</th><td class=\"tbldata\">" . StringUtils::formatNumber($fd->resFood()) . " t</td></tr>";
    echo "<tr><th>" . RES_ICON_POWER . "" . RES_POWER . "</th><td class=\"tbldata\">" . StringUtils::formatNumber($fd->resPower()) . " t</td></tr>";
    echo "<tr><th style=\"width:150px;\">Freier Frachtraum:</th><td class=\"tbldata\">" . StringUtils::formatNumber($fd->getFreeCapacity()) . " t</td></tr>";
    echo "<tr><th style=\"width:150px;\">Totaler Frachtraum:</th><td class=\"tbldata\">" . StringUtils::formatNumber($fd->getCapacity()) . " t</td></tr>";
    tableEnd();



    echo "</td></tr><tr><td colspan=\"3\">";


    // Schiffe laden
    if ($fd->countShips() > 0) {
        // Schiffe anzeigen
        tableStart("Schiffe");
        echo "<tr>
                <th colspan=\"2\">Schifftyp</th>
                <th width=\"50\">Anzahl</th></tr>";
        foreach ($fd->getShipIds() as $sid => $scnt) {
            $ship = new Ship($sid);
            echo "<tr>
                    <td class=\"tbldata\" style=\"width:40px;height: 40px;background:#000\">
                        " . $ship->img() . "</td>";
            echo "<td class=\"tbldata\">
                    <b>" . $ship->name() . "</b><br/>
                " . text2html($ship->shortComment()) . "</td>";
            echo "<td class=\"tbldata\" style=\"width:50px;\">" . StringUtils::formatNumber($scnt) . "</td></tr>";
        }
        tableEnd();
    }

    echo "</td></tr></table>";

    echo "<form action=\"?page=$page&amp;id=$fleet_id&amp;lead_id=$lead_id\" method=\"post\">";
    echo "<input type=\"button\" onClick=\"document.location='?page=fleets'\" value=\"Zur&uuml;ck zur Flotten&uuml;bersicht\"> &nbsp;";

    // Abbrechen-Button anzeigen
    if ($valid >= ALLIANCE_FLEET_SEND_HOME && ($fd->status() == 0 && $lead_id > 0) && $fd->landTime() > time()) {
        checker_init();
        echo "<input type=\"submit\" name=\"cancel_alliance\" value=\"Allianzangriff abbrechen und zum Heimatplanet zur&uuml;ckkehren\"  onclick=\"return confirm('Willst du diesen Allianzangriff wirklich abbrechen? Merke du brichst damit den ganzen Allianzangriff ab!');\">";
    } elseif (
        $valid >= ALLIANCE_FLEET_SEND_HOME_PART
        && (($fd->status() == 0
            && $lead_id < 0) || $fd->status() == 3) && $fd->landTime() > time() && $fd->getAction()->cancelable()
    ) {
        checker_init();
        echo "<input type=\"submit\" name=\"cancel\" value=\"Flug abbrechen und zum Heimatplanet zur&uuml;ckkehren\"  onclick=\"return confirm('Willst du diesen Flug wirklich abbrechen?');\">";
    }

    echo "</form>";

    countDown('flighttime', $fd->landTime());

    $totalFlightTime = $fd->landTime() - $fd->launchTime();
?>
    <script type="text/javascript;">
        function moveFleet(t) {
            var objectWidth = 40;
            var progrssWidth = $('.fleetInfoWrap').width() - (2 * objectWidth);
            perc = <?= $totalFlightTime ?> > 0 ? ((t - <?= $fd->launchTime() ?>) / (<?= $totalFlightTime ?>)) : 1;
            perc = Math.min(1, perc);
            pxl = objectWidth + Math.round(perc * progrssWidth);
            $('#fleetProgress').css('left', pxl + 'px');
            $('#flightPercent').html(Math.round(perc * 100) + "%");
            setTimeout(function() {
                moveFleet(t + 1);
            }, 1000);
        }
        $(function() {
            moveFleet(<?= time() ?>);
        });
    </script>
<?PHP

    //Some adjustements for special actions
    if ($fd->getAction()->code() == "support" && $fd->status() == 3) {
        echo "<script type=\"text/javascript;\">
                document.getElementById('targetLabel').innerHTML = 'Ende';
                </script>";
    } elseif ($fd->getAction()->code() == "alliance" && $lead_id > 0 && $fd->status() == 0) {
        echo "<script type=\"text/javascript;\">
                document.getElementById('allianceBox').style.display= '';
                document.getElementById('allianceBox').innerHTML = '";
        tableStart("Allianzangriff", "50%");
        echo "<tr>
                        <td class=\"tbltitle\">Leaderflotte:</td>
                        <td>Das ist der Gesammte Angriff!</td>
                    </tr>
                    <tr>
                        <td class=\"tbltitle\">Teilflotten:</td>
                        <td>";

        $alliance = $allianceRepository->getAlliance($cu->allianceId());
        $allianceTag = $alliance !== null ? $alliance->tag : null;
        foreach ($fd->fleets as $f) {
            echo "<a href=\"?page=fleetinfo&amp;id=" . $f->id() . "\">" . $allianceTag . "-" . $f->id() . "<br />Besitzer: " . get_user_nick($f->ownerId()) . "</a><br />";
        }
        echo "</td></tr>";
        tableEnd();
        echo "';
                </script>";
    } elseif ($fd->getAction()->code() == "alliance" && ($fd->status() == 3 || $fd->status() == 0)) {
    }
} else {
    echo "Diese Flotte existiert nicht mehr! Wahrscheinlich sind die Schiffe schon <br/>auf dem Zielplaneten gelandet oder der Flug wurde abgebrochen.<br/><br/>";
    echo "<input type=\"button\" onclick=\"document.location='?page=fleets'\" value=\"Zur&uuml;ck zur Flotten&uuml;bersicht\">";
}

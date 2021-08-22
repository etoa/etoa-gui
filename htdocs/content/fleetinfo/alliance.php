<?PHP

/** @var int $fleet_id */
/** @var Fleet $fd */

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;

/** @var AllianceBuildingRepository $allianceBuildingRepository */
$allianceBuildingRepository = $app[AllianceBuildingRepository::class];
/** @var AllianceRepository $allianceRepository */
$allianceRepository = $app[AllianceRepository::class];
/** @var LogRepository $logRepository */
$logRepository = $app[LogRepository::class];

$lead_id = (int) $_GET['lead_id'] > 0 ? (int) $_GET['lead_id'] : -1;

$rights = true;
$allianceFleetControlLevel = $allianceBuildingRepository->getLevel($cu->allianceId(), \EtoA\Alliance\AllianceBuildingId::FLEET_CONTROL);

if ($lead_id > 0)
    $fd = new Fleet($fleet_id, -1, $lead_id);
else
        if ($allianceFleetControlLevel < ALLIANCE_FLEET_SHOW_PART && $cu->id != $fd->ownerId())
    $rights = false;

if ($allianceFleetControlLevel >= ALLIANCE_FLEET_SHOW_DETAIL && $rights) {
    if ($cu->alliance->checkActionRightsNA(AllianceRights::FLEET_MINISTER) || $cu->id == $fd->ownerId()) {
        //
        // Flottendaten laden und überprüfen ob die Flotte existiert
        //

        if ($fd->valid()) {
            // Flugabbruch auslösen
            if (isset($_POST['cancel']) != "" && checker_verify()) {
                if ($fd->cancelFlight()) {
                    success_msg("Flug erfolgreich abgebrochen!");
                    $logRepository->add(LogFacility::FLEETACTION, LogSeverity::INFO, "Der Spieler [b]" . $cu->nick . "[/b] bricht den Flug seiner Flotte [b]" . $fleet_id . "[/b] ab");
                } else {
                    error_msg("Flug konnte nicht abgebrochen werden. " . $fd->getError());
                }
            }

            //ToDo
            if (isset($_POST['cancel_alliance']) != "" && checker_verify()) {
                if ($fd->cancelFlight(true)) {
                    success_msg("Flug erfolgreich abgebrochen!");
                    $logRepository->add(LogFacility::FLEETACTION, LogSeverity::INFO, "Der Spieler [b]" . $cu->nick . "[/b] bricht den ganzen Allianzflug seiner Flotte [b]" . $fleet_id . "[/b] ab");
                } else {
                    error_msg("Flug konnte nicht abgebrochen werden. " . $fd->getError());
                }
            }


            tableStart("", "", "double");

            // Flugdaten
            tableStart("Flugdaten", "50%");

            echo "<tr>
                        <th>Auftrag:</th>
                        <td " . tm($fd->getAction()->name(), $fd->getAction()->desc()) . " style=\"color:" . FleetAction::$attitudeColor[$fd->getAction()->attitude()] . "\">
                            " . $fd->getAction()->name() . " [" . FleetAction::$statusCode[$fd->status()] . "]
                        </td>
                    </tr>
                    <tr>
                        <th>Leaderflotte:</th>
                        <td>";

            if ($fd->id() == $fd->leaderId() && $lead_id > 0)
                echo "Das ist der Gesammte Angriff!</td></tr>";
            elseif ($fd->id() == $fd->leaderId()) {
                echo "Das ist die Leaderflotte!<br />
                        <a href=\"?page=fleetinfo&amp;id=" . $fd->leaderId() . "&lead_id=" . $fd->leaderId() . "\">Gesamten Angriff anzeigen</a></td></tr>";
            } else
                echo "<a href=\"?page=fleetinfo&amp;id=" . $fd->leaderId() . "&lead_id=" . $fd->leaderId() . "\">Gesamten Angriff anzeigen</a></td></tr>";
            echo "<tr>
                        <th>Startkoordinaten:</th>
                        <td>
                            <a href=\"?page=cell&amp;id=" . $fd->getSource()->cellId() . "&amp;hl=" . $fd->getSource()->id() . "\">" . $fd->getSource() . "</a>
                            (" . $fd->getSource()->entityCodeString() . ")
                        </td>
                    </tr>
                    <tr>
                        <th>Zielkoordinaten:</th>
                        <td>
                            <a href=\"?page=cell&amp;id=" . $fd->getTarget()->cellId() . "&amp;hl=" . $fd->getTarget()->id() . "\">" . $fd->getTarget() . "</a>
                            (" . $fd->getTarget()->entityCodeString() . ")
                        </td>
                    </tr>
                    <tr>
                        <th>Startzeit:</th>
                        <td>" . date("d.m.Y H:i:s", $fd->launchTime()) . "</td>
                    </tr>
                    <tr>
                        <th>Ende des Fluges:</th>
                        <td>" . date("d.m.Y H:i:s", $fd->landTime()) . "</td>
                    </tr>
                    <tr>
                        <th>Verbleibend:</th>
                        <td id=\"flighttime\" style=\"color:#ff0\">-</td>
                    </tr>";

            if ($fd->id() == $fd->leaderId() && $lead_id > 0 && $allianceFleetControlLevel >= ALLIANCE_FLEET_SHOW_PART) {
                $alliance = $allianceRepository->getAlliance($cu->allianceId());
                $allianceTag = $alliance !== null ? $alliance->tag : null;
                echo "<th>Teilflotten:</th>
                        <td>
                            <a href=\"?page=fleetinfo&amp;id=" . $fd->id() . "\">" . $allianceTag . "-" . $fd->id() . "<br />Besitzer: " . get_user_nick($fd->ownerId()) . "</a><br />";
                foreach ($fd->fleets as $f) {
                    echo "<a href=\"?page=fleetinfo&amp;id=" . $f->id() . "\">" . $allianceTag . "-" . $f->id() . "<br />Besitzer: " . get_user_nick($f->ownerId()) . "</a><br />";
                }
                echo "</td></tr>";
            }
            tableEnd();

            tableStart("Piloten &amp; Verbrauch", "50%");
            echo "<tr>
                        <th style=\"width:150px;\">" . RES_ICON_PEOPLE . "Piloten:</th>
                        <td>" . StringUtils::formatNumber($fd->pilots()) . "</td>
                    </tr>
                    <tr>
                        <th>" . RES_ICON_FUEL . "" . RES_FUEL . ":</th>
                        <td>" . StringUtils::formatNumber($fd->usageFuel()) . "</td>
                    </tr>
                    <tr>
                        <th>" . RES_ICON_FOOD . "" . RES_FOOD . ":</th>
                        <td>" . StringUtils::formatNumber($fd->usageFood()) . "</td>
                    </tr>
                    <tr>
                        <th>" . RES_ICON_POWER . " " . RES_POWER . ":</th>
                        <td>" . StringUtils::formatNumber($fd->usagePower()) . "</td>
                    </tr>";
            tableEnd();

            echo "</td><td style=\"width:5%;vertical-align:top;\"></td><td style=\"width:45%;vertical-align:top;\">";

            // Frachtraum
            tableStart("Frachtraum", "50%");
            echo "<tr>
                        <th>" . RES_ICON_METAL . "" . RES_METAL . "</th>
                        <td>" . StringUtils::formatNumber($fd->resMetal()) . " t</td>
                    </tr>
                    <tr>
                        <th>" . RES_ICON_CRYSTAL . "" . RES_CRYSTAL . "</th>
                        <td>" . StringUtils::formatNumber($fd->resCrystal()) . " t</td>
                    </tr>
                    <tr>
                        <td>" . RES_ICON_PLASTIC . "" . RES_PLASTIC . "</th>
                        <td>" . StringUtils::formatNumber($fd->resPlastic()) . " t</td>
                    </tr>
                    <tr>
                        <th>" . RES_ICON_FUEL . "" . RES_FUEL . "</th>
                        <td>" . StringUtils::formatNumber($fd->resFuel()) . " t</td>
                    </tr>
                    <tr>
                        <th>" . RES_ICON_FOOD . "" . RES_FOOD . "</th>
                        <td>" . StringUtils::formatNumber($fd->resFood()) . " t</td>
                    </tr>
                    <tr>
                        <th>" . RES_ICON_POWER . "" . RES_POWER . "</th>
                        <td>" . StringUtils::formatNumber($fd->resPower()) . " t</td>
                    </tr>
                    <tr>
                        <th style=\"width:150px;\">Freier Frachtraum:</th>
                        <td>" . StringUtils::formatNumber($fd->getFreeCapacity()) . " t</td>
                    </tr>
                    <tr>
                        <th style=\"width:150px;\">Totaler Frachtraum:</th>
                        <td>" . StringUtils::formatNumber($fd->getCapacity()) . " t</td>
                    </tr>";
            tableEnd();

            tableStart("Passagierraum", "50%");
            echo "<tr>
                        <th>" . RES_ICON_PEOPLE . "Passagiere</th>
                        <td>" . StringUtils::formatNumber($fd->resPeople()) . "</td>
                    </tr>
                    <tr>
                        <th style=\"width:150px;\">Freier Platz:</th>
                        <td>" . StringUtils::formatNumber($fd->getFreePeopleCapacity()) . "</td>
                    </tr>
                    <tr>
                        <th style=\"width:150px;\">Totaler Platz:</th>
                        <td>" . StringUtils::formatNumber($fd->getPeopleCapacity()) . "</td>
                    </tr>";
            tableEnd();

            echo "</td></tr>";
            tableEnd();

            // Schiffe laden
            if ($fd->countShips() > 0) {
                // Schiffe anzeigen
                tableStart("Schiffe");
                echo "<tr>
                            <th colspan=\"2\">Schifftyp</td>
                            <td width=\"50\">Anzahl</td>
                        </tr>";
                foreach ($fd->getShipIds() as $sid => $scnt) {
                    $ship = new Ship($sid);
                    echo "<tr>
                                <td style=\"width:40px;background:#000\">
                                    " . $ship->img() . "
                                </td>
                                <td>
                                    <b>" . $ship->name() . "</b><br/>
                                    " . BBCodeUtils::toHTML($ship->shortComment()) . "
                                </td>
                                <td style=\"width:50px;\">" . StringUtils::formatNumber($scnt) . "</td>
                            </tr>";
                }
                tableEnd();
            }

            echo "<form action=\"?page=$page&amp;id=$fleet_id\" method=\"post\">";
            echo "<input type=\"button\" onClick=\"document.location='?page=fleets'\" value=\"Zur&uuml;ck zur Flotten&uuml;bersicht\"> &nbsp;";

            // Abbrechen-Button anzeigen
            if (($fd->status() == 0 || $fd->status() == 3) && $fd->landTime() > time()) {
                checker_init();
                if ($fd->id() == $lead_id && $lead_id == $fd->leaderId())
                    echo " &nbsp;<input type=\"submit\" name=\"cancel_alliance\" value=\"Allianzangriff abbrechen\"  onclick=\"return confirm('Willst du diesen Allianzangriff wirklich abbrechen? Damit beendest du den ganzen Alianzangriff und alle Teilflotten kehren zu ihrem Heimatplaneten zurück!');\">";
                else
                    echo "<input type=\"submit\" name=\"cancel\" value=\"Flug abbrechen und zum Heimatplanet zur&uuml;ckkehren\"  onclick=\"return confirm('Willst du diesen Flug wirklich abbrechen? Alle weiteren Teilflotten des Angriffes werden immer noch weiterfliegen!');\">";
            }
            echo "</form>";

            countDown('flighttime', $fd->landTime());
        } else {
            echo "Diese Flotte existiert nicht mehr! Wahrscheinlich sind die Schiffe schon <br/>auf dem Zielplaneten gelandet oder der Flug wurde abgebrochen.<br/><br/>";
            echo "<input type=\"button\" onclick=\"document.location='?page=fleets'\" value=\"Zur&uuml;ck zur Flotten&uuml;bersicht\">";
        }
    } else {
        error_msg("Du besitzt nicht die notwendigen Rechte!");
    }
} else {
    error_msg("Die Allianzflottenkontrolle wurde noch nicht genug ausgebaut!");
}

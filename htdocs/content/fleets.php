<?PHP

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Alliance\AllianceService;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Fleet\FleetStatus;
use EtoA\Fleet\ForeignFleetLoader;
use EtoA\Ship\ShipDataRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;

/** @var FleetRepository $fleetRepository */
$fleetRepository = $app[FleetRepository::class];

/** @var UserUniverseDiscoveryService $userUniverseDiscoveryService */
$userUniverseDiscoveryService = $app[UserUniverseDiscoveryService::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var AllianceBuildingRepository $allianceBuildingRepository */
$allianceBuildingRepository = $app[AllianceBuildingRepository::class];
/** @var AllianceService $allianceService */
$allianceService = $app[AllianceService::class];
/** @var AllianceRepository $allianceRepository */
$allianceRepository = $app[AllianceRepository::class];
/** @var EntityRepository $entityRepository */
$entityRepository = $app[EntityRepository::class];
$user = $userRepository->getUser($cu->id);

echo "<h1>Flotten</h1>";

echo "<br/><input type=\"button\" onclick=\"document.location='?page=fleetstats'\" value=\"Schiffs&uuml;bersicht anzeigen\" /> &nbsp; ";

//
// Alliance fleets
//
if (isset($_GET['mode']) && $_GET['mode'] == "alliance" && $cu->allianceId > 0) {
    echo "<input type=\"button\" onclick=\"document.location='?page=fleets'\" value=\"Flotten anzeigen\" /><br/><br/>";

    if ($cu->allianceId() > 0) {
        if ($allianceBuildingRepository->getLevel($cu->allianceId(), AllianceBuildingId::FLEET_CONTROL) >= ALLIANCE_FLEET_SHOW) {
            $supportFleets = $fleetRepository->search(FleetSearch::create()->actionIn([\EtoA\Fleet\FleetAction::SUPPORT])->allianceId($cu->allianceId()));

            if (count($supportFleets) > 0) {
                $cdarr = array();

                echo "Klicke auf den Auftrag um die Details einer Flotte anzuzeigen<br/><br/>";

                tableStart("Allianz Supportflotten");
                echo "<tr>
                            <th>Auftrag</th>
                            <th>Start / Ziel</th>
                            <th>Start / Landung</th>
                        </tr>";

                $alliance = $allianceRepository->getAlliance($user->allianceId);
                $userAlliancePermission = $allianceService->getUserAlliancePermissions($alliance, $user);
                foreach ($supportFleets as $supportFleet) {
                    $cdarr["cd" . $supportFleet->id] = $supportFleet->landTime;

                    echo "<tr>";
                    echo "<td>";
                    if ($userAlliancePermission->hasRights(AllianceRights::FLEET_MINISTER))
                        echo "<a href=\"?page=fleetinfo&id=" . $supportFleet->id . "\">";

                    $action = FleetAction::createFactory($supportFleet->action);
                    echo "<span style=\"font-weight:bold;color:" . FleetAction::$attitudeColor[$action->attitude()] . "\">
                                        " . $action->name() . "
                                    </span> [" . FleetAction::$statusCode[$supportFleet->status] . "]
                                </a><br/>";
                    if ($supportFleet->landTime < time()) {
                        if ($supportFleet->status > 0) {
                            echo "Flotte landet...";
                        } else {
                            echo "Zielaktion wird durchgef&uuml;hrt...";
                        }
                    } else {
                        echo "Ankunft in <b><span id=\"cd" . $supportFleet->id . "\">-</span></b>";
                    }
                    echo "</td>";
                    $source = $entityRepository->searchEntityLabel(EntitySearch::create()->id($supportFleet->entityFrom));
                    $target = $entityRepository->searchEntityLabel(EntitySearch::create()->id($supportFleet->entityTo));
                    echo "<td><b>" . $source->codeString() . "</b>
                                <a href=\"?page=cell&amp;id=" . $source->cellId . "&amp;hl=" . $source->id . "\">" . $source->toString() . "</a><br/>
                                <b>" . $target->codeString() . "</b>
                                <a href=\"?page=cell&amp;id=" . $target->cellId . "&amp;hl=" . $target->id . "\">" . $target->toString() . "</a>
                            </td>
                            <td>" .
                        date("d.m.y, H:i:s", $supportFleet->launchTime) . "<br/>" .
                        date("d.m.y, H:i:s", $supportFleet->landTime) . "
                            </td>";

                    echo "</tr>";
                }
                tableEnd();

                foreach ($cdarr as $elem => $t) {
                    countDown($elem, $t);
                }
            } else {
                iBoxStart("Allianz Supportflotten");
                echo "Es sind keine Allianz Supportflotten unterwegs!";
                iBoxEnd();
            }

            $allianceAttackFleets = $fleetRepository->search(FleetSearch::create()->actionIn([\EtoA\Fleet\FleetAction::ALLIANCE])->nextId($cu->allianceId())->status(FleetStatus::DEPARTURE)->isLeader());
            if (count($allianceAttackFleets) > 0) {
                $cdarr = array();

                echo "Klicke auf den Auftrag um die Details einer Flotte anzuzeigen<br/><br/>";

                tableStart("Allianz Angriffe");
                echo "<tr>
                            <th>Start / Ziel</th>
                            <th>Start / Landung</th>
                            <th>Auftrag / Status</th>
                        </tr>";

                $alliance = $allianceRepository->getAlliance($user->allianceId);
                $userAlliancePermission = $allianceService->getUserAlliancePermissions($alliance, $user);
                foreach ($allianceAttackFleets as $allianceAttackFleet) {
                    $cdarr["cd" . $allianceAttackFleet->id] = $allianceAttackFleet->landTime;

                    $source = $entityRepository->searchEntityLabel(EntitySearch::create()->id($allianceAttackFleet->entityFrom));
                    $target = $entityRepository->searchEntityLabel(EntitySearch::create()->id($allianceAttackFleet->entityTo));
                    echo "<tr>
                                <td><b>" . $source->codeString() . "</b>
                                    <a href=\"?page=cell&amp;id=" . $source->cellId . "&amp;hl=" . $source->id . "\">" . $source->toString() . "</a><br/>
                                    <b>" . $target->codeString() . "</b>
                                    <a href=\"?page=cell&amp;id=" . $target->cellId . "&amp;hl=" . $target->id . "\">" . $target->toString() . "</a>
                                </td>
                                <td>" .
                        date("d.m.y, H:i:s", $allianceAttackFleet->launchTime) . "<br/>" .
                        date("d.m.y, H:i:s", $allianceAttackFleet->landTime) . "
                                </td>
                                <td>";
                    if ($userAlliancePermission->hasRights(AllianceRights::FLEET_MINISTER))
                        echo "<a href=\"?page=fleetinfo&id=" . $allianceAttackFleet->id . "&lead_id=" . $allianceAttackFleet->id . "\">";

                    $action = FleetAction::createFactory($allianceAttackFleet->action);
                    echo "<span style=\"color:" . FleetAction::$attitudeColor[$action->attitude()] . "\">
                                            " . $action->name() . "
                                        </span> [" . FleetAction::$statusCode[$allianceAttackFleet->status] . "]
                                    </a><br/>";
                    if ($allianceAttackFleet->landTime < time()) {
                        if ($allianceAttackFleet->status > 0) {
                            echo "Flotte landet...";
                        } else {
                            echo "Zielaktion wird durchgef&uuml;hrt...";
                        }
                    } else {
                        echo "Ankunft in <b><span id=\"cd" . $allianceAttackFleet->id . "\">-</span></b>";
                    }
                    echo "</td></tr>";
                }
                tableEnd();

                foreach ($cdarr as $elem => $t) {
                    countDown($elem, $t);
                }
            } else {
                iBoxStart("Allianz Angriffe");
                echo "Es sind keine Allianz Angriffe unterwegs!";
                iBoxEnd();
            }
        } else {
            error_msg("Allianzflottenkontrolle wurde noch nicht gebaut!");
        }
    } else {
        error_msg("Du gehörst noch keiner Allianz an.");
    }
}

//
// Personal fleets
//
else {
    echo "<input type=\"button\" onclick=\"document.location='?page=fleets&mode=alliance'\" value=\"Allianzflotten anzeigen\" /><br/><br/>";

    $ownFleets = $fleetRepository->search(FleetSearch::create()->user($cu->getId()));
    if (count($ownFleets) > 0) {
        $cdarr = array();

        echo "Klicke auf den Auftrag um die Details einer Flotte anzuzeigen<br/><br/>";
        tableStart("Eigene Flotten");
        echo "
            <tr>
                <th>Auftrag</th>
                <th>Start / Ziel</th>
                <th>Start / Landung</th>
            </tr>";
        foreach ($ownFleets as $fleet) {
            $cdarr["cd" . $fleet->id] = $fleet->landTime;
            $action = FleetAction::createFactory($fleet->action);
            echo "<tr>
                <td>
                    <a href=\"?page=fleetinfo&id=" . $fleet->id . "\">
                    <span style=\"font-weight:bold;color:" . FleetAction::$attitudeColor[$action->attitude()] . "\">
                    " . $action->name() . "
                    </span> [" . FleetAction::$statusCode[$fleet->status] . "]</a><br/>";
            if ($fleet->landTime < time()) {
                if ($fleet->status > 0) {
                    echo "Flotte landet...";
                } else {
                    echo "Zielaktion wird durchgef&uuml;hrt...";
                }
            } else {
                echo "Ankunft in <b><span id=\"cd" . $fleet->id . "\">-</span></b>";
            }
            echo "</td>";

            $source = $entityRepository->searchEntityLabel(EntitySearch::create()->id($fleet->entityFrom));
            $target = $entityRepository->searchEntityLabel(EntitySearch::create()->id($fleet->entityTo));
            echo "<td><b>" . $source->codeString() . "</b>
                <a href=\"?page=cell&amp;id=" . $source->cellId . "&amp;hl=" . $source->id . "\">" . $source->toString() . "</a><br/>";

            if ($userUniverseDiscoveryService->isEntityDiscovered($user, $target)) {
                echo "<b>" . $target->codeString() . "</b>
                    <a href=\"?page=cell&amp;id=" . $target->cellId . "&amp;hl=" . $target->id . "\">" . $target->toString() . "</a></td>";
            } else {
                $ent = Entity::createFactory('u', $fleet->entityTo);
                echo "<b>" . $ent->entityCodeString() . "</b>
                    <a href=\"?page=cell&amp;id=" . $ent->cellId() . "&amp;hl=" . $ent->id() . "\">" . $ent . "</a></td>";
            }
            echo "<td>
                " . date("d.m.y, H:i:s", $fleet->launchTime) . "<br/>";
            echo date("d.m.y, H:i:s", $fleet->landTime) . "</td>";
            echo "</tr>";
        }
        tableEnd();

        foreach ($cdarr as $elem => $t) {
            countDown($elem, $t);
        }
    } else {
        iBoxStart("Eigene Flotten");
        echo "Es sind keine eigenen Flotten unterwegs!";
        iBoxEnd();
    }


    //
    // Gegnerische Flotten
    //
    $header = 0;
    /** @var ForeignFleetLoader $foreignFleetLoader */
    $foreignFleetLoader = $app[ForeignFleetLoader::class];
    $foreignFleets = $foreignFleetLoader->getVisibleFleets($cu->getId());
    if (count($foreignFleets->visibleFleets) > 0) {
        $show_num = 0;
        tableStart("Fremde Flotten");
        foreach ($foreignFleets->visibleFleets as $foreignFleet) {
            // Is the attitude visible?
            /** @var \FleetAction $action */
            $action = \FleetAction::createFactory($foreignFleet->action);
            if (SPY_TECH_SHOW_ATTITUDE <= $foreignFleets->userSpyLevel) {
                $attitude = $action->attitude();
            } else {
                $attitude = 4;
            }
            $attitudeColor = FleetAction::$attitudeColor[$attitude];
            $attitudeString = FleetAction::$attitudeString[$attitude];

            // Is the number of ships visible?
            if (SPY_TECH_SHOW_NUM <= $foreignFleets->userSpyLevel) {
                $show_num = 1;

                $shipsCount = $fleetRepository->countShipsInFleet($foreignFleet->id);
            } else {
                $shipsCount = -1;
            }

            //Opfer sieht die einzelnen Schiffstypen in der Flotte
            $shipStr = array();
            $showShips = false;
            if (SPY_TECH_SHOW_SHIPS <= $foreignFleets->userSpyLevel) {
                $showShips = true; {

                    $ships = array();

                    if ($foreignFleet->leaderId > 0) {
                        $fleetShips = $fleetRepository->findAllShipsForLeader($foreignFleet->leaderId);
                    } else {
                        $fleetShips = $fleetRepository->findAllShipsInFleet($foreignFleet->id, null);
                    }

                    foreach ($fleetShips as $fleetShip) {
                        $shipId = $fleetShip->shipFaked > 0 ? $fleetShip->shipFaked : $fleetShip->shipId;
                        if (!isset($ships[$shipId])) {
                            $ships[$shipId] = 0;
                        }

                        $ships[$shipId] += $fleetShip->count;
                    }

                    /** @var ShipDataRepository $shipRepository */
                    $shipRepository = $app[ShipDataRepository::class];
                    $shipNames = $shipRepository->getShipNames(true);
                    foreach ($ships as $sid => $scnt) {
                        $str = "";

                        //Opfer sieht die genau Anzahl jedes Schifftypes in einer Flotte
                        if (SPY_TECH_SHOW_NUMSHIPS <= $foreignFleets->userSpyLevel) {
                            $str = "" . $scnt . " ";
                        }
                        $str .= "" . $shipNames[$sid];
                        $shipStr[] = $str;
                    }
                }
            }

            // Show action
            if (SPY_TECH_SHOW_ACTION <= $foreignFleets->userSpyLevel) {
                $shipAction = $action->displayName();
            } else {
                $shipAction = $attitudeString;
            }

            if ($header != 1) {
                echo "<tr>
                        <th>Start / Ziel</th>
                        <th>Startzeit / Landezeit</th>
                        <th>Gesinnung</th>
                        <th>Spieler</th>
                        </tr>";
                $header = 1;
            }

            $source = $entityRepository->searchEntityLabel(EntitySearch::create()->id($foreignFleet->entityFrom));
            $target = $entityRepository->searchEntityLabel(EntitySearch::create()->id($foreignFleet->entityTo));
            echo "<tr>
                    <td><b>" . $source->codeString() . "</b>
                    <a href=\"?page=cell&amp;id=" . $source->cellId . "&amp;hl=" . $source->id . "\">" . $source->toString() . "</a><br/>";
            echo "<b>" . $target->codeString() . "</b>
                    <a href=\"?page=cell&amp;id=" . $target->cellId . "&amp;hl=" . $target->id . "\">" . $target->toString() . "</a></td>";
            echo "<td>
                    " . date("d.m.y, H:i:s", $foreignFleet->launchTime) . "<br/>";
            echo date("d.m.y, H:i:s", $foreignFleet->landTime) . "</td>";
            echo "<td>
                    <span style=\"color:" . $attitudeColor . "\">
                    " . $shipAction . "
                    </span> [" . FleetAction::$statusCode[$foreignFleet->status] . "]<br/>";
            echo "<td>
                    <a href=\"?page=messages&mode=new&message_user_to=" . $foreignFleet->userId . "\">" . get_user_nick($foreignFleet->userId) . "</a>
                    </td>";
            echo "</tr>";
            if ($show_num == 1) {
                echo "<tr><td colspan=\"4\">";
                echo "<b>Anzahl:</b> " . $shipsCount . "";
                if ($showShips) {
                    echo ";<br><b>Schiffe:</b> ";
                    $count = false;
                    foreach ($shipStr as $value) {
                        if ($count) {
                            echo ", ";
                        } else {
                            $count = true;
                        }
                        echo $value;
                    }
                }
                echo "</td></tr>";
            }
        }
        tableEnd();
    } else {
        iBoxStart("Fremde Flotten");
        echo "Es sind keine fremden Flotten zu deinen Planeten unterwegs!";
        iBoxEnd();
    }
}

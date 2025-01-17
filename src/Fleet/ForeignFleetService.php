<?php declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Entity\User;
use EtoA\Ship\ShipDataRepository;
use EtoA\Technology\SpyTechFleetLevel;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use Symfony\Bundle\SecurityBundle\Security;

class ForeignFleetService
{
    public function __construct(
        private readonly FleetRepository $fleetRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly FleetShipRepository $fleetShipRepository,
        private readonly Security                 $security,
        private readonly ShipDataRepository $shipDataRepository
    )
    {}

    public function getVisibleFleets(User $user): ForeignFleets
    {
        $userSpyTechLevel = $this->technologyListItemRepository->findOneBy(['technology'=>TechnologyId::SPY,'user'=>$user])?->getCurrentLevel();

        $specialist = $user->getSpecialist();
        if ($specialist) {
            $userSpyTechLevel += $specialist->getSpyLevel();
        }

        if (SpyTechFleetLevel::SHOW_ATTITUDE > $userSpyTechLevel) {
            return new ForeignFleets;
        }

        //Lädt Flottendaten
        $foreignFleets = $this->fleetRepository->search(
            FleetSearch::create()
            ->planetUser($user)
            ->notUser($user)
            ->filterNonLeadingAllianceAttacks()
        );

        if (count($foreignFleets) === 0) {
            return new ForeignFleets;
        }

        $visibleFleets = [];
        $aggressiveCount = 0;
        foreach ($foreignFleets as $fleet) {
            /** @var FleetAction $action */
            $action = FleetAction::createFactory($fleet->getAction());
            if (!$action->visible()) {
                continue;
            }

            if ($action->attitude() !== 3) {
                $visibleFleets[] = $fleet;

                continue;
            }

            $opponentTarnTech = $this->technologyListItemRepository->findOneBy(['technology'=>TechnologyId::TARN,'user'=>$fleet->getUser()]);
            $opponentSpecialist = $fleet->getUser()->getSpecialist();
            if ($opponentSpecialist !== null) {
                $opponentTarnTech += $opponentSpecialist->getTarnLevel();
            }

            $diffTimeFactor = max($opponentTarnTech - $userSpyTechLevel, 0);
            $specialShipBonusTarn = 0;

            // Minbari fleet hide ability does not work with alliance attacks
            // TODO: Improvement would be differentiation between single fleets
            if ($fleet->getAction() !== FleetAction::ALLIANCE) {
                $specialShipBonusTarn = $this->fleetShipRepository->getFleetSpecialTarnBonus($fleet);
            }

            $diffTimeFactor = 0.1 * min(9, $diffTimeFactor + 10 * $specialShipBonusTarn);

            if ($fleet->getRemainingTime() < ($fleet->getLandTime() - $fleet->getLaunchTime()) * (1 - $diffTimeFactor)) {
                $visibleFleets[] = $fleet;
                $aggressiveCount++;
            }
        }

        $result = new ForeignFleets();
        $result->userSpyLevel = $userSpyTechLevel;
        $result->visibleFleets = $visibleFleets;
        $result->aggressiveCount = $aggressiveCount;

        return $result;
    }

    public function renderForeignFleets():string
    {
        //
        // Gegnerische Flotten
        //
        $header = 0;
        $foreignFleets = $this->getVisibleFleets($this->security->getUser()->getData());

        ob_start();
        if (count($foreignFleets->visibleFleets) > 0) {
            $show_num = 0;
            echo '<table class="tb"><caption>Fremde Flotten</caption>';
            foreach ($foreignFleets->visibleFleets as $foreignFleet) {
                // Is the attitude visible?
                $action = FleetAction::createFactory($foreignFleet->getAction());
                if (SpyTechFleetLevel::SHOW_ATTITUDE <= $foreignFleets->userSpyLevel) {
                    $attitude = $action->attitude();
                } else {
                    $attitude = 4;
                }
                $attitudeColor = FleetAction::$attitudeColor[$attitude];
                $attitudeString = FleetAction::$attitudeString[$attitude];

                // Is the number of ships visible?
                if (SpyTechFleetLevel::SHOW_NUMBER <= $foreignFleets->userSpyLevel) {
                    $show_num = 1;

                    $shipsCount = $this->fleetShipRepository->countShipsInFleet($foreignFleet);
                } else {
                    $shipsCount = -1;
                }

                //Opfer sieht die einzelnen Schiffstypen in der Flotte
                $shipStr = array();
                $showShips = false;

                if (SpyTechFleetLevel::SHOW_SHIPS <= $foreignFleets->userSpyLevel) {
                    $showShips = true;
                    $ships = array();

                    if ($foreignFleet->getLeader()) {
                        $fleetShips = $this->fleetShipRepository->findAllShipsForLeader($foreignFleet->getLeader());
                    } else {
                        $fleetShips = $this->fleetShipRepository->findAllShipsInFleet($foreignFleet);
                    }

                    foreach ($fleetShips as $fleetShip) {
                        $shipId = $fleetShip->getShipFaked() > 0 ? $fleetShip->getShipFaked() : $fleetShip->getShip()->getId();
                        if (!isset($ships[$shipId])) {
                            $ships[$shipId] = 0;
                        }

                        $ships[$shipId] += $fleetShip->getCount();
                    }

                    foreach ($ships as $sid => $scnt) {
                        $str = "";

                        //Opfer sieht die genau Anzahl jedes Schifftypes in einer Flotte
                        if (SpyTechFleetLevel::SHOW_NUMBER_OF_SHIPS <= $foreignFleets->userSpyLevel) {
                            $str = "" . $scnt . " ";
                        }
                        $str .= "" .  $this->shipDataRepository->find($sid)->getName();
                        $shipStr[] = $str;
                    }
                }

                // Show action
                if (SpyTechFleetLevel::SHOW_ACTION <= $foreignFleets->userSpyLevel) {
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

                $source = $foreignFleet->getEntityFrom();
                $target = $foreignFleet->getEntityTo();
                echo "<tr>
                    <td><b>" . $source->codeString() . "</b>
                    <a href=\"?page=cell&amp;id=" . $source->getCell()->getId() . "&amp;hl=" . $source->getId() . "\">" . $source->toString() . "</a><br/>";
                echo "<b>" . $target->codeString() . "</b>
                    <a href=\"?page=cell&amp;id=" . $target->getCell()->getId() . "&amp;hl=" . $target->getId() . "\">" . $target->toString() . "</a></td>";
                echo "<td>
                    " . date("d.m.y, H:i:s", $foreignFleet->getLaunchTime()) . "<br/>";
                echo date("d.m.y, H:i:s", $foreignFleet->getLandTime()) . "</td>";
                echo "<td>
                    <span style=\"color:" . $attitudeColor . "\">
                    " . $shipAction . "
                    </span> [" . FleetAction::$statusCode[$foreignFleet->getStatus()] . "]<br/>";
                echo "<td>
                    <a href=\"?page=messages&mode=new&message_user_to=" . $foreignFleet->getUser()->getId() . "\">" . $foreignFleet->getUser()->getNick() . "</a>
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
                echo '</table>';
            }
        }
        return ob_get_clean();
    }
}

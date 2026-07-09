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

    public function getForeignFleetsData(): ForeignFleets
    {
        $foreignFleets = $this->getVisibleFleets($this->security->getUser()->getData());

        if (count($foreignFleets->visibleFleets) === 0) {
            return $foreignFleets;
        }

        // Transform Fleet entities to ForeignFleetData DTOs with enriched information
        $enrichedFleets = [];
        foreach ($foreignFleets->visibleFleets as $fleet) {
            $action = FleetAction::createFactory($fleet->getAction());
            
            // Determine attitude based on spy level
            if (SpyTechFleetLevel::SHOW_ATTITUDE <= $foreignFleets->userSpyLevel) {
                $attitude = $action->attitude();
            } else {
                $attitude = 4;
            }
            
            $attitudeColor = FleetAction::$attitudeColor[$attitude];
            $attitudeString = FleetAction::$attitudeString[$attitude];
            $statusCode = FleetAction::$statusCode[$fleet->getStatus()];
            
            // Determine ship action display
            if (SpyTechFleetLevel::SHOW_ACTION <= $foreignFleets->userSpyLevel) {
                $shipAction = $action->displayName();
            } else {
                $shipAction = $attitudeString;
            }
            
            // Add ships count if visible
            $shipsCount = null;
            if (SpyTechFleetLevel::SHOW_NUMBER <= $foreignFleets->userSpyLevel) {
                $shipsCount = $this->fleetShipRepository->countShipsInFleet($fleet);
            }

            // Add ship details if visible
            $ships = null;
            $showShipNumbers = false;
            if (SpyTechFleetLevel::SHOW_SHIPS <= $foreignFleets->userSpyLevel) {
                $ships = [];

                if ($fleet->getLeader()) {
                    $fleetShips = $this->fleetShipRepository->findAllShipsForLeader($fleet->getLeader()->getUser());
                } else {
                    $fleetShips = $this->fleetShipRepository->findAllShipsInFleet($fleet);
                }

                foreach ($fleetShips as $fleetShip) {
                    $shipId = $fleetShip->getShipFaked() > 0 ? $fleetShip->getShipFaked() : $fleetShip->getShip()->getId();
                    if (!isset($ships[$shipId])) {
                        $ships[$shipId] = ['count' => 0, 'name' => ''];
                    }

                    $ships[$shipId]['count'] += $fleetShip->getCount();
                    $ships[$shipId]['name'] = $this->shipDataRepository->find($shipId)->getName();
                }

                $showShipNumbers = SpyTechFleetLevel::SHOW_NUMBER_OF_SHIPS <= $foreignFleets->userSpyLevel;
            }

            $enrichedFleets[] = new ForeignFleetData(
                fleet: $fleet,
                attitudeColor: $attitudeColor,
                attitudeString: $attitudeString,
                statusCode: $statusCode,
                shipAction: $shipAction,
                shipsCount: $shipsCount,
                ships: $ships,
                showShipNumbers: $showShipNumbers
            );
        }

        $foreignFleets->visibleFleets = $enrichedFleets;
        return $foreignFleets;
    }
}

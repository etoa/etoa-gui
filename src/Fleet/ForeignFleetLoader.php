<?php declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Specialist\SpecialistService;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyRepository;

class ForeignFleetLoader
{
    private FleetRepository $fleetRepository;
    private TechnologyRepository $technologyRepository;
    private SpecialistService $specialistService;

    public function __construct(FleetRepository $fleetRepository, TechnologyRepository $technologyRepository, SpecialistService $specialistService)
    {
        $this->fleetRepository = $fleetRepository;
        $this->technologyRepository = $technologyRepository;
        $this->specialistService = $specialistService;
    }

    public function getVisibleFleets(int $userId): ForeignFleets
    {
        $userSpyTechLevel = $this->technologyRepository->getTechnologyLevel($userId, TechnologyId::SPY);
        $specialist = $this->specialistService->getSpecialistOfUser($userId);
        if ($specialist !== null) {
            $userSpyTechLevel += $specialist->spyLevel;
        }

        if (SPY_TECH_SHOW_ATTITUDE > $userSpyTechLevel) {
            return new ForeignFleets;
        }

        //Lädt Flottendaten
        $foreignFleets = $this->fleetRepository->search(
            FleetSearch::create()
            ->planetUser($userId)
            ->notUser($userId)
            ->filterNonLeadingAllianceAttacks()
        );

        if (count($foreignFleets) === 0) {
            return new ForeignFleets;
        }

        $visibleFleets = [];
        $aggressiveCount = 0;
        foreach ($foreignFleets as $fleet) {
            /** @var \FleetAction $action */
            $action = \FleetAction::createFactory($fleet->action);
            if (!$action->visible()) {
                continue;
            }

            if ($action->attitude() !== 3) {
                $visibleFleets[] = $fleet;

                continue;
            }

            $opponentTarnTech = $this->technologyRepository->getTechnologyLevel($fleet->userId, TechnologyId::TARN);
            $opponentSpecialist = $this->specialistService->getSpecialistOfUser($fleet->userId);
            if ($opponentSpecialist !== null) {
                $opponentTarnTech += $opponentSpecialist->tarnLevel;
            }

            $diffTimeFactor = max($opponentTarnTech - $userSpyTechLevel, 0);
            $specialShipBonusTarn = 0;

            // Minbari fleet hide ability does not work with alliance attacks
            // TODO: Improvement would be differentiation between single fleets
            if ($fleet->action !== FleetAction::ALLIANCE) {
                $specialShipBonusTarn = $this->fleetRepository->getFleetSpecialTarnBonus($fleet->id);
            }

            $diffTimeFactor = 0.1 * min(9, $diffTimeFactor + 10 * $specialShipBonusTarn);

            if ($fleet->getRemainingTime() < ($fleet->landTime - $fleet->launchTime) * (1 - $diffTimeFactor)) {
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
}

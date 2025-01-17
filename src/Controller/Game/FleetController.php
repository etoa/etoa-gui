<?php

namespace EtoA\Controller\Game;

use EtoA\Alliance\AllianceBuildingId;
use EtoA\Alliance\AllianceBuildListRepository;
use EtoA\Alliance\AllianceFleetControlLevel;
use EtoA\Alliance\AllianceRights;
use EtoA\Alliance\AllianceService;
use EtoA\Entity\Fleet;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Fleet\FleetStatus;
use EtoA\Fleet\ForeignFleetService;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FleetController extends AbstractGameController
{
    public function __construct(
        private readonly FleetRepository              $fleetRepository,
        private readonly ForeignFleetService          $foreignFleetService,
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService,
        private readonly UserRepository               $userRepository,
        private readonly AllianceService              $allianceService,
        private readonly AllianceBuildListRepository  $allianceBuildListRepository
    )
    {
    }

    #[Route('/game/fleets', name: 'game.fleets')]
    public function fleets(): Response {
        $cu = $this->getUser()->getData();
        $ownFleets = $this->fleetRepository->search(FleetSearch::create()->user($cu));

        return $this->render('game/fleets/fleets_fleets.html.twig',[
            'ownFleets' => $ownFleets,
            'foreignFleets' => $this->foreignFleetService->renderForeignFleets(),
            'userUniverseDiscoveryService' => $this->userUniverseDiscoveryService
        ]);
    }

    #[Route('/game/fleets/alliance', name: 'game.fleets.alliance')]
    public function fleetsAlliance(): Response {
        $cu = $this->getUser()->getData();
        $alliance = $cu->getAlliance();

        if($alliance) {
            if ($this->allianceBuildListRepository->getLevel($alliance, AllianceBuildingId::FLEET_CONTROL) >= AllianceFleetControlLevel::SHOW) {
                $userAlliancePermission = $this->allianceService->getUserAlliancePermissions($alliance, $cu);
                $supportFleets = $this->fleetRepository->search(FleetSearch::create()->actionIn([FleetAction::SUPPORT])->userIn($this->userRepository->findBy(['alliance'=>$alliance])));
                $allianceAttackFleets = $this->fleetRepository->search(FleetSearch::create()->actionIn([FleetAction::ALLIANCE])->nextId($cu->getAlliance()->getId())->status(FleetStatus::DEPARTURE)->isLeader());

                return $this->render('game/fleets/fleets_alliance.html.twig',[
                    'supportFleets' => $supportFleets,
                    'allianceAttackFleets' => $allianceAttackFleets,
                    'hasRights' => $userAlliancePermission->hasRights(AllianceRights::FLEET_MINISTER)
                ]);
            }

            return $this->render('game/error.html.twig',[
                'msg' => 'Allianzflottenkontrolle wurde noch nicht gebaut!',
                'path' => $this->generateUrl('game.alliance.base.buildings'),
                'headline' => 'Flotten'
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Du gehörst noch keiner Allianz an.',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Flotten'
        ]);
    }

    #[Route('/game/fleets/info/{id}', name: 'game.fleets.info')]
    public function info(?Fleet $fleet = null): Response {
        $cu = $this->getUser()->getData();
        $alliance = $cu->getAlliance();

        if ($fleet) {
            if($fleet->getUser() === $cu) {
                $valid = 10;
            } else {
                $userAlliancePermission = $this->allianceService->getUserAlliancePermissions($alliance, $cu);
                if ($userAlliancePermission->hasRights(AllianceRights::FLEET_MINISTER)) {
                    $allianceFleetControlLevel = $this->allianceBuildListRepository->getLevel($alliance, AllianceBuildingId::FLEET_CONTROL);
                    if ($fleet->getAction() === FleetAction::SUPPORT && $fleet->getUser()->getAlliance() === $alliance && ($fleet->getStatus() == 0 || $fleet->getStatus() == 3)) {
                        $valid = $allianceFleetControlLevel;
                    } elseif ($fleet->getAction() === FleetAction::ALLIANCE && $fleet->getUser()->getAlliance() === $alliance) {
                        if ($fleet->getStatus() === 0) {
                            if ($fleet->getLeader() && ($allianceFleetControlLevel >= AllianceFleetControlLevel::SHOW)) {
                                $valid = $allianceFleetControlLevel;
                            }
                        } elseif ($fleet->getStatus() == 3) {
                            if ($allianceFleetControlLevel >= AllianceFleetControlLevel::SHOW_PART) {
                                $valid = $allianceFleetControlLevel;
                            }
                        }
                    }
                }
            }
        }
    }
}
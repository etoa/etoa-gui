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
use EtoA\Fleet\FleetService;
use EtoA\Fleet\FleetStatus;
use EtoA\Fleet\ForeignFleetService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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
        private readonly AllianceBuildListRepository  $allianceBuildListRepository,
        private readonly FleetService                 $fleetService,
        private readonly LogRepository                $logRepository
    )
    {
    }

    #[Route('/game/fleets', name: 'game.fleets')]
    public function fleets(): Response {
        $cu = $this->getUser()->getData();
        $ownFleets = $this->fleetRepository->search(FleetSearch::create()->user($cu));

        return $this->render('game/fleets/fleets_fleets.html.twig',[
            'ownFleets' => $ownFleets,
            'foreignFleets' => $this->foreignFleetService->getForeignFleetsData(),
            'userUniverseDiscoveryService' => $this->userUniverseDiscoveryService
        ]);
    }

    #[Route('/game/fleets/alliance', name: 'game.fleets.alliance')]
    public function fleetsAlliance(): Response {
        $cu = $this->getUser()->getData();
        $alliance = $cu->getAlliance();

        if($alliance) {
            if ($this->allianceBuildListRepository->getLevel($alliance, AllianceBuildingId::FLEET_CONTROL->value) >= AllianceFleetControlLevel::SHOW) {
                $userAlliancePermission = $this->allianceService->getUserAlliancePermissions($alliance, $cu);
                $supportFleets = $this->fleetRepository->search(FleetSearch::create()->actionIn([FleetAction::SUPPORT])->userIn($this->userRepository->findBy(['alliance'=>$alliance])));
                $allianceAttackFleets = $this->fleetRepository->search(FleetSearch::create()->actionIn([FleetAction::ALLIANCE])->nextId($cu->getAlliance()->getId())->status(FleetStatus::DEPARTURE->value)->isLeader());

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
    public function info(Request $request, ?Fleet $fleet = null): Response {
        $cu = $this->getUser()->getData();
        $alliance = $cu->getAlliance();
        $valid = 0;

        if ($fleet) {
            if($fleet->getUser() === $cu) {
                $valid = 10;
            } else {
                $userAlliancePermission = $this->allianceService->getUserAlliancePermissions($alliance, $cu);
                if ($userAlliancePermission->hasRights(AllianceRights::FLEET_MINISTER)) {
                    $allianceFleetControlLevel = $this->allianceBuildListRepository->getLevel($alliance, AllianceBuildingId::FLEET_CONTROL->value);
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

        if($valid) {
            $form = $this->createFormBuilder()
                ->add('cancel', SubmitType::class, [
                    'label' => 'Flug abbrechen und zum Heimatplanet zurückkehren',
                    'attr' => [
                        'onclick' => "return confirm('Willst du diesen Flug wirklich abbrechen?')"
                    ]
                ])
                ->add('cancelAlliance', SubmitType::class, [
                    'label' => 'Allianzangriff abbrechen und zum Heimatplanet zurückkehren',
                    'attr' => [
                        'onclick' => "return confirm('Willst du diesen Allianzangriff wirklich abbrechen? Merke du brichst damit den ganzen Allianzangriff ab!')"
                    ]
                ])
                ->getForm()
                ->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                if ($form->has('cancel') && $form->get('cancel')->isClicked()) {
                    if ($valid >= AllianceFleetControlLevel::SEND_HOME_PART) {
                        $cancel = $this->fleetService->cancelFlight($fleet);
                        if (is_bool($cancel)) {
                            $msg['success'] = "Flug erfolgreich abgebrochen!";
                        } else {
                            return $this->render('game/error.html.twig',[
                                'msg' => "Flug konnte nicht abgebrochen werden. " . $cancel,
                                'path' => $this->generateUrl('game.fleets'),
                                'headline' => 'Flotten'
                            ]);
                        }
                    } else {
                        return $this->render('game/error.html.twig',[
                            'msg' => "Flug konnte nicht abgebrochen werden, da die Rechte nicht vorhanden sind!",
                            'path' => $this->generateUrl('game.fleets'),
                            'headline' => 'Flotten'
                        ]);
                    }
                }

                if ($form->has('cancelAlliance') && $form->get('cancelAlliance')->isClicked()) {
                    if ($valid >= AllianceFleetControlLevel::SEND_HOME) {
                        $cancel = $this->fleetService->cancelFlight($fleet,true);
                        if (is_bool($cancel)) {
                            $msg['success'] = "Flug erfolgreich abgebrochen!";
                            $this->logRepository->add(LogFacility::FLEETACTION, LogSeverity::INFO, "Der Spieler [b]" . $cu->getNick() . "[/b] bricht den ganzen Allianzflug seiner Flotte [b]" . $fleet->getId() . "[/b] ab");
                        } else {
                            return $this->render('game/error.html.twig',[
                                'msg' => "Flug konnte nicht abgebrochen werden. " . $cancel,
                                'path' => $this->generateUrl('game.fleets'),
                                'headline' => 'Flotten'
                            ]);
                        }
                    } else {
                        return $this->render('game/error.html.twig',[
                            'msg' => "Flug konnte nicht abgebrochen werden, da die Rechte nicht vorhanden sind!",
                            'path' => $this->generateUrl('game.fleets'),
                            'headline' => 'Flotten'
                        ]);
                    }
                }
            }

            return $this->render('game/fleets/fleets_info.html.twig',[
                'fleet' => $fleet,
                'userUniverseDiscoveryService' => $this->userUniverseDiscoveryService,
                'form' => $form,
                'valid' => $valid,
                'msg' => $msg??null
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Diese Flotte existiert nicht mehr! Wahrscheinlich sind die Schiffe schon <br/>auf dem Zielplaneten gelandet oder der Flug wurde abgebrochen.',
            'path' => $this->generateUrl('game.fleets'),
            'headline' => 'Flotten'
        ]);
    }
}
<?php

namespace EtoA\Controller\Game;

use EtoA\Backend\BackendMessageService;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Economy\EconomyService;
use EtoA\Entity\Planet;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetStatus;
use EtoA\Specialist\SpecialistDataRepository;
use EtoA\Specialist\SpecialistService;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotNull;

class EconomyController extends AbstractGameController
{
    public function __construct(
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly UserRepository $userRepository,
        private readonly SpecialistService $specialistService,
        private readonly PlanetRepository $planetRepository,
        private readonly SpecialistDataRepository $specialistDataRepository,
        private readonly BackendMessageService $backendMessageService,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly DefenseQueueRepository $defenseQueueRepository,
        private readonly FleetRepository $fleetRepository,
        private readonly EconomyService $economyService
    )
    {
    }

    #[Route('/game/economy/{id}', name: 'game.economy')]
    public function economy(Request $request, ?Planet $entity = null): Response {
        $id = $request->getSession()->get('cpid');

        if(!$entity || $entity->getUser() !== $this->getUser()->getData()) {
            return $this->render('game/error.html.twig',[
                'msg' => 'Dieser Planet existiert nicht oder er gehört nicht dir!',
                'path' => $this->generateUrl('game.overview'),
                'headline' => 'Wirtschaftsübersicht'
            ]);
        }

        $data = $this->economyService->getPlanetEconomyData();

        $form = $this->createFormBuilder();

        foreach ($data['producingBuildings'] as $item) {
            if($item['productionPercentOptions']) {
                $options = [];
                $selected = null;
                foreach ($item['productionPercentOptions'] as $option) {
                    $options[(string)$option['value']] = $option['label'];
                    if($option['selected'])
                        $selected = $option['value'];
                }
                $form = $form->add($item['id'], ChoiceType::class, [
                    'choices' => array_flip($options),
                    'data' => $selected
                ]);
            }
        }
        $form = $form
            ->add('calc', SubmitType::class, ['label' => 'Neu Berechnen'])
            ->add('save', SubmitType::class, [
                'label' => 'Speichern',
                'attr' => [
                    'class' => 'button textSmall'
                ]
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('calc')->isClicked()) {
                $this->backendMessageService->updatePlanet($id);
                $this->addFlash('success',"Planet wird neu berechnet!");
            }
            if($form->get('save')->isClicked()) {
                $updated = false;

                foreach ($form->all() as $buildingId => $child) {
                    if(is_int($id)) {
                        $item = $this->buildingListItemRepository->findOneBy(['building'=>$buildingId,'entity'=>$id]);
                        $val = $child->getViewData();

                        if($item && $item->getProdPercent() !== $val) {
                            $val = floatval($val);
                            if ($val > 1) $val = 1;
                            if ($val < 0) $val = 0;
                            $item->setProdPercent($val);
                            $this->buildingListItemRepository->save();
                            $updated = true;
                        }
                    }
                }

                if($updated) {
                    $this->addFlash('success',"Änderungen gespeichert!");
                    // Send
                    $this->backendMessageService->updatePlanet($id);
                    return $this->redirectToRoute('game.economy',['id'=>$id]);
                }
            }
        }

        return $this->render('game/economy/economy.html.twig', array_merge($data, [
            'id' => $id,
            'form' => $form
        ]));
    }

    #[Route('/game/specialists', name: 'game.specialists')]
    public function specialists(Request $request): Response {
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));
        $cu = $this->getUser()->getData();
        $specialist = $cu->getSpecialist();

        $formDischarge = $this->createFormBuilder()
              ->add('discharge', SubmitType::class, [
                'label' => 'Entlassen',
                'attr' => [
                    'onclick' => "confirm('Willst du den Spezialisten wirklich entlassen? Es werden keine Ressourcen zurückerstattet, da der Spezialist diese als Abgangsentschädigung behält!')"
                ]
            ])
            ->getForm()
            ->handleRequest($request);

        // Discharge specialist
        if ($formDischarge->isSubmitted() && $formDischarge->isValid()) {
            if ($specialist && $cu->getSpecialistTime() > time()) {
                $inUse = false;
                $inittime = $cu->getSpecialistTime() - (86400 * $specialist->getDays());

                // check if research is in progress if using the professor
                if ($specialist->getTimeTechnologies() !== 1.0) {
                    $technologyEntries = $this->technologyListItemRepository->findForUser($cu, time());
                    foreach ($technologyEntries as $entry) {
                        if ($entry->getStartTime() > $inittime) {
                            $inUse = true;
                            break;
                        }
                    }
                }

                //Ingenieur
                if ($specialist->getTimeDefense() !== 1.0) {
                    $entries = $this->defenseQueueRepository->findBy(['user'=>$cu]);
                    foreach ($entries as $entry) {
                        if ($entry->getEndTime() > time() && $entry->getUserClickTime() > $inittime) {
                            $inUse = true;
                            break;
                        }
                    }
                }

                //Architekt
                if ($specialist->getTimeBuildings() !== 1.0) {
                    $buildingEntries = $this->buildingListItemRepository->findForUser($cu, null, time());
                    foreach ($buildingEntries as $entry) {
                        if ($entry->getStartTime() > $inittime) {
                            $inUse = true;
                            break;
                        }
                    }
                }

                //Admiral
                if ($specialist->getFleetSpeed() !== 1.0) {
                    /** @var FleetRepository $fleetRepository */
                    $fleets = $this->fleetRepository->findBy(['user'=>$cu]);
                    foreach ($fleets as $fleet) {
                        if ($fleet->getLaunchTime() > $inittime) {
                            if ($fleet->getStatus() == FleetStatus::DEPARTURE->value) {
                                $inUse = true;
                                break;
                            } else {
                                $duration = $fleet->getLandTime() - $fleet->getLaunchTime();
                                $org_launchtime = $fleet->getLaunchTime() - $duration;

                                if ($org_launchtime >= $inittime) {
                                    $inUse = true;
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($inUse) {
                    return $this->render('game/error.html.twig',[
                        'msg' => 'Der Spezialist wird gerade verwendet!',
                        'path' => $this->generateUrl('game.overview'),
                        'headline' => 'Spezialisten'
                    ]);
                } else {
                    $cu->setSpecialist(null);
                    $cu->setSpecialistTime(0);

                    $this->userRepository->save();

                    $msg['success'] = 'Der Spezialist wurde entlassen!';
                }
            } else {
                return $this->render('game/error.html.twig',[
                    'msg' => 'Du kannst niemanden entlassen, da kein Spezialist angestellt ist!',
                    'path' => $this->generateUrl('game.specialists'),
                    'headline' => 'Spezialisten'
                ]);
            }
        }

        $formSpecialists = $this->createFormBuilder($this->getUser()->getData())
            ->add('engage', SubmitType::class, [
                'label' => 'Gewählten Spezialisten einstellen',
            ])
            ->add('specialist', ChoiceType::class, [
                'label' => 'Gewählten Spezialisten einstellen',
                'choices' => $this->specialistDataRepository->findBy(['enabled'=>true]),
                'expanded' => true,
                'constraints' => [new NotNull()]
            ])
            ->getForm()
            ->handleRequest($request);

        if ($formSpecialists->isSubmitted() && $formSpecialists->isValid()) {
            $specialist = $cu->getSpecialist();
            if ($cu->getSpecialistTime() < time()) {
                $factor = $this->specialistService->getFactor($this->getUser()->getData()->getSpecialist());

                if ($cu->getPoints() >= $specialist->getPointsRequirement()) {
                    if (
                        $planet->getResMetal() >= $specialist->getCostsMetal() * $factor &&
                        $planet->getResCrystal() >= $specialist->getCostsCrystal() * $factor &&
                        $planet->getResPlastic() >= $specialist->getCostsPlastic() * $factor &&
                        $planet->getResFuel() >= $specialist->getCostsFuel() * $factor &&
                        $planet->getResFood() >= $specialist->getCostsFood() * $factor
                    ) {
                        $st = time() + (86400 * $specialist->getDays());
                        $cu->setSpecialistTime($st);

                        $this->planetRepository->addResources(
                            $planet,
                            -$specialist->getCostsMetal() * $factor,
                            -$specialist->getCostsCrystal() * $factor,
                            -$specialist->getCostsPlastic() * $factor,
                            -$specialist->getCostsFuel() * $factor,
                            -$specialist->getCostsFood() * $factor
                        );

                        $this->userRepository->save();

                        //Update every planet
                        foreach ($cu->getPlanets() as $userPlanet) {
                            $this->backendMessageService->updatePlanet($userPlanet->getId());
                        }
                        $msg['success'] = 'Der gewählte Spezialist wurde eingestellt!';
                    } else {
                        return $this->render('game/error.html.twig',[
                            'msg' => 'Zuwenig Rohstoffe vorhanden!',
                            'path' => $this->generateUrl('game.specialists'),
                            'headline' => 'Spezialisten'
                        ]);
                    }
                } else {
                    return $this->render('game/error.html.twig',[
                        'msg' => 'Zuwenig Punkte!',
                        'path' => $this->generateUrl('game.overview'),
                        'headline' => 'Spezialisten'
                    ]);
                }
            } else {
                return $this->render('game/error.html.twig',[
                    'msg' => 'Es ist bereits ein Spezialist eingestellt.
                            Seine Anstellung dauert noch bis ' . StringUtils::formatDate($cu->getSpecialistTime()) . '.
                            Du musst warten bis seine Anstellung beendet ist!',
                    'path' => $this->generateUrl('game.overview'),
                    'headline' => 'Spezialisten'
                ]);
            }
        }

        return $this->render('game/economy/specialist.html.twig', [
            'form_specialists' => $formSpecialists,
            'form_discharge' => $formDischarge,
            'specialistService' => $this->specialistService,
            'planet' => $planet,
            'msg' => $msg??null
        ]);
    }

    #[Route('/game/planetstats', name: 'game.planetstats')]
    public function planetStats(Request $request): Response {
        return $this->render('game/economy/planetstats.html.twig', [
            'planetResourcesData' => $this->economyService->getPlanetResourcesData(),
            'id' => $request->getSession()->get('cpid')
        ]);
    }
}
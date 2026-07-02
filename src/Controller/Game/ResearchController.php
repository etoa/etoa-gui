<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Entity\Technology;
use EtoA\Entity\TechnologyListItem;
use EtoA\Log\GameLogFacility;
use EtoA\Log\GameLogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Technology\TechnologyService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResearchController extends AbstractGameController
{
    public function __construct(
        private readonly PlanetRepository             $planetRepository,
        private readonly BuildingListItemRepository   $buildingListItemRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly TechnologyService            $technologyService,
        private readonly GameLogRepository            $gameLogRepository
    )
    {
    }

    #[Route('/game/research', name: 'game.research')]
    public function list(Request $request): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $researchBuilding = $this->buildingListItemRepository->findOneBy(['entity' => $cp, 'building' => BuildingId::TECHNOLOGY]);

        if ($researchBuilding) {
            return $this->render('game/research/list.html.twig', [
                'planet' => $cp,
                'researchBuilding' => $researchBuilding,
                'render' => $this->technologyService->renderResearch()
            ]);
        }

        return $this->render('game/error.html.twig', [
            'msg' => 'Das Forschungslabor wurde noch nicht gebaut!',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Forschungslabor des Planeten ' . $cp->getName()
        ]);
    }

    #[Route('/game/research/{id}', name: 'game.research.detail')]
    public function detail(Request $request, ?Technology $technology): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $researchBuilding = $this->buildingListItemRepository->findOneBy(['entity' => $cp, 'building' => BuildingId::TECHNOLOGY]);
        $data = $this->technologyService->getTechnologyData($technology);
        $technologyListItem = $this->technologyListItemRepository->findOneBy(['user' => $this->getUser()->getData(), 'technology' => $technology]);

        if (!$technology || !$technology->isShow()) {
            return $this->render('game/error.html.twig', [
                'msg' => 'Technik nicht vorhanden!',
                'path' => $this->generateUrl('game.research'),
                'headline' => 'Forschungslabor des Planeten ' . $cp->getName()
            ]);
        }


        if (!$researchBuilding) {
            return $this->render('game/error.html.twig', [
                'msg' => 'Das Forschungslabor wurde noch nicht gebaut!',
                'path' => $this->generateUrl('game.overview'),
                'headline' => 'Forschungslabor des Planeten ' . $cp->getName()
            ]);
        }

        $form = $this->container->get('form.factory')->createNamed('research_form', FormType::class)
            ->add('research', SubmitType::class, [
                'label' => 'Erforschen'
            ])
            ->add('cancelResearch', SubmitType::class, [
                'label' => 'Forschung abbrechen',
                'attr' => [
                    'onclick' => "if (this.value=='Forschung abbrechen'){return confirm('Wirklich abbrechen?');}"
                ]
            ])
            ->add('refresh', SubmitType::class, [
                'label' => 'Aktualisieren'
            ])
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('research')->isClicked()) {
                if (!$technologyListItem) {
                    $technologyListItem = new TechnologyListItem();
                    $technologyListItem->setUser($this->getUser()->getData());
                    $technologyListItem->setTechnology($technology);
                }

                if ($technologyListItem->getBuildType() === 0) {
                    $researching = false;

                    if ($technologyListItem->getTechnology()->getId() !== TechnologyId::GEN) {
                        $researching = $this->technologyListItemRepository->isTechInProgress($this->getUser()->getData(), $technology);
                    }

                    if (!$researching) {
                        $sufficient = true;

                        foreach ($data['options']['researchCosts']['resources'] as $resource) {
                            if (!$resource['sufficient'])
                                $sufficient = false;
                        }

                        if ($sufficient) {
                            $start_time = time();
                            $end_time = time() + $data['options']['researchCosts']['time'];
                            $this->technologyListItemRepository->updateBuildStatus($technologyListItem, 3, $start_time, (int)$end_time);
                            $buildingId = $technology->getId() === TechnologyId::GEN ? BuildingId::PEOPLE->value : BuildingId::TECHNOLOGY->value;
                            $this->buildingListItemRepository->markBuildingWorkingStatus($technologyListItem->getUser(), $cp, $buildingId, true);

                            //Rohstoffe vom Planeten abziehen und aktualisieren
                            $this->planetRepository->addResources($cp,
                                -$data['options']['researchCosts']['resources']['metal']['cost'],
                                -$data['options']['researchCosts']['resources']['crystal']['cost'],
                                -$data['options']['researchCosts']['resources']['plastic']['cost'],
                                -$data['options']['researchCosts']['resources']['fuel']['cost'],
                                -$data['options']['researchCosts']['resources']['food']['cost']);

                            $b_status = 3;

                            if ($technology->getId() === TechnologyId::GEN)
                                $peopleWorking = $this->technologyService->getPeopleWorkingGen();
                            else
                                $peopleWorking = $this->technologyService->getPeopleWorking();
                            //Log schreiben
                            $log_text = "[b]Forschung Ausbau[/b]

                            [b]Erforschungsdauer:[/b] " . StringUtils::formatTimespan($data['options']['researchCosts']['time']) . "
                            [b]Ende:[/b] " . date("d.m.Y H:i:s", (int)$end_time) . "
                            [b]Forschungslabor Level:[/b] " . $researchBuilding->getCurrentLevel() . "
                            [b]Eingesetzte Bewohner:[/b] " . StringUtils::formatNumber($peopleWorking) . "
                            [b]Gen-Tech Level:[/b] " . $this->technologyListItemRepository->getTechnologyLevel($this->getUser()->getData(), TechnologyId::GEN) ?? 0 . "
                            [b]Eingesetzter Spezialist:[/b] " . ($this->getUser()->getData()->getSpecialist() ? $this->getUser()->getData()->getSpecialist()->getName() : "Kein Spezialist") . "

                            [b]Kosten[/b]
                            [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($data['options']['researchCosts']['resources']['metal']['cost']) . "
                            [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($data['options']['researchCosts']['resources']['crystal']['cost']) . "
                            [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($data['options']['researchCosts']['resources']['plastic']['cost']) . "
                            [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($data['options']['researchCosts']['resources']['fuel']['cost']) . "
                            [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($data['options']['researchCosts']['resources']['food']['cost']) . "

                            [b]Restliche Rohstoffe auf dem Planeten[/b]
                            [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($cp->getResMetal()) . "
                            [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($cp->getResCrystal()) . "
                            [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($cp->getResPlastic()) . "
                            [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($cp->getResFuel()). "
                            [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($cp->getResFood());

                            $this->gameLogRepository->add(GameLogFacility::TECH, LogSeverity::INFO, $log_text, $this->getUser()->getData(), $this->getUser()->getData()->getAlliance(), $cp->getEntity(), $technology->getId(), $b_status, $data['technology']['currentLevel']);
                        } else
                            $this->addFlash('error', 'Forschung kann nicht gestartet werden, zuwenig Rohstoffe vorhanden!');
                    } else
                        $this->addFlash('error', 'Forschung kann nicht gestartet werden, es wird bereits an einer Technologie geforscht!');
                }
            }

            if ($form->get('cancelResearch')->isClicked()) {
                $endTime = $data['options']['researchEndTime'];
                if ($endTime > time()) {
                    $startTime = $data['options']['researchStartTime'];
                    $fac = ($endTime - time()) / ($endTime - $startTime);
                    $this->technologyListItemRepository->updateBuildStatus($technologyListItem, 0, 0, 0);

                    $buildingId = $technology->getId() === TechnologyId::GEN ? BuildingId::PEOPLE->value : BuildingId::TECHNOLOGY->value;
                    $this->buildingListItemRepository->markBuildingWorkingStatus($this->getUser()->getData(), $cp, $buildingId, false);

                    //Rohstoffe zurückgeben und aktualisieren
                    $this->planetRepository->addResources($cp,
                        $data['options']['researchCosts']['resources']['metal']['cost'] * $fac,
                        $data['options']['researchCosts']['resources']['crystal']['cost'] * $fac,
                        $data['options']['researchCosts']['resources']['plastic']['cost'] * $fac,
                        $data['options']['researchCosts']['resources']['fuel']['cost'] * $fac,
                        $data['options']['researchCosts']['resources']['food']['cost'] * $fac
                    );

                    $b_status = 0;

                    //Log schreiben
                    $log_text = "[b]Forschung Abbruch[/b]

                        [b]Start der Forschung:[/b] " . date("d.m.Y H:i:s", $startTime) . "
                        [b]Ende der Forschung:[/b] " . date("d.m.Y H:i:s", $endTime) . "

                        [b]Erhaltene Rohstoffe[/b]
                        [b]Faktor:[/b] " . $fac . "
                        [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($data['options']['researchCosts']['resources']['metal']['cost'] * $fac) . "
                        [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($data['options']['researchCosts']['resources']['crystal']['cost'] * $fac) . "
                        [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($data['options']['researchCosts']['resources']['plastic']['cost'] * $fac) . "
                        [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($data['options']['researchCosts']['resources']['fuel']['cost'] * $fac) . "
                        [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($data['options']['researchCosts']['resources']['food']['cost'] * $fac) . "

                        [b]Rohstoffe auf dem Planeten[/b]
                        [b]" . ResourceNames::METAL . ":[/b] " . StringUtils::formatNumber($cp->getResMetal()). "
                        [b]" . ResourceNames::CRYSTAL . ":[/b] " . StringUtils::formatNumber($cp->getResCrystal()) . "
                        [b]" . ResourceNames::PLASTIC . ":[/b] " . StringUtils::formatNumber($cp->getResPlastic()) . "
                        [b]" . ResourceNames::FUEL . ":[/b] " . StringUtils::formatNumber($cp->getResFuel()) . "
                        [b]" . ResourceNames::FOOD . ":[/b] " . StringUtils::formatNumber($cp->getResFood());

                    //Log Speichern
                    $this->gameLogRepository->add(GameLogFacility::TECH, LogSeverity::INFO, $log_text, $this->getUser()->getData(), $this->getUser()->getData()->getAlliance(), $cp->getEntity(), $technology->getId(), $b_status, $data['technology']['currentLevel']);

                } else {
                    $this->addFlash('error', 'Bauauftrag kann nicht mehr abgebrochen werden, die Arbeit ist bereits fertiggestellt!');
                }
            }

            return $this->redirectToRoute('game.research.detail', ['id' => $technology->getId()]);
        }

        return $this->render('game/research/detail.html.twig', [
            'planet' => $cp,
            'researchBuilding' => $researchBuilding,
            'render' => $data,
            'form' => $form
        ]);
    }
}
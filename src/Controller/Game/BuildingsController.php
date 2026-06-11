<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingService;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Building;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\Routing\Annotation\Route;

class BuildingsController extends AbstractGameController
{
    public function __construct(
        private readonly BuildingService              $buildingService,
        private readonly PlanetRepository             $planetRepository,
        private readonly BuildingListItemRepository   $buildingListItemRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly ConfigurationService         $configurationService
    )
    {
    }

    #[Route('/game/buildings', name: 'game.buildings')]
    public function list(Request $request): Response
    {
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));

        //only allow own planets
        if ($planet->getUser() !== $this->getUser()->getData()) {
            return $this->redirectToRoute('game.overview');
        }

        $buildingData = $this->buildingService->getBuildingsData();
        $base = $this->buildingListItemRepository->findOneBy(['entity' => $planet, 'building' => BuildingId::BUILDING]);
        $peopleWorking = $base?->getPeopleWorking() ?? 0;
        $genTechLevel = $this->technologyListItemRepository->getTechnologyLevel($this->getUser()->getData(), TechnologyId::GEN) ?? 0;
        $currentlyBuilding = ($base?->getPeopleWorkingStatus() === 1) || !$base;

        //TODO: could be done with live component
        $form = $this->createFormBuilder()
            ->add('peopleOptimized', HiddenType::class, [
                'data' => 0
            ])
            ->add('peopleFree', HiddenType::class, [
                'data' => floor($planet->getPeople()) - $this->buildingListItemRepository->getTotalPeopleWorking($planet) + $peopleWorking
            ])
            ->add('foodAvailable', HiddenType::class, [
                'data' => $planet->getResFood()
            ])
            ->add('foodRequired', HiddenType::class, [
                'data' => $this->configurationService->getInt('people_food_require')
            ])
            ->add('workDone', HiddenType::class, [
                'data' => $this->configurationService->getInt('people_work_done')
            ])
            ->add('peopleWorking', TextType::class, [
                'attr' => [
                    'onKeyUp' => "updatePeopleWorkingBox(this.value,'-1','-1')"
                ],
                'data' => StringUtils::formatNumber($peopleWorking)
            ])
            ->add('timeReduction', TextType::class, [
                'attr' => [
                    'onKeyUp' => "updatePeopleWorkingBox('-1',this.value,'-1')"
                ],
                'data' => StringUtils::formatTimespan($this->configurationService->getInt('people_work_done') * $peopleWorking)
            ])
            ->add('foodUsing', TextType::class, [
                'attr' => [
                    'onKeyUp' => "updatePeopleWorkingBox('-1','-1',this.value);"
                ],
                'data' => StringUtils::formatNumber($this->configurationService->getInt('people_food_require') * $peopleWorking)
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Speichern'
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$currentlyBuilding) {
                // Free: Total people on planet minus total working people on planet
                // PLUS people working in this building (these can be set again)
                $free = floor($planet->getPeople()) - $this->buildingListItemRepository->getTotalPeopleWorking($planet) + $peopleWorking;
                $people = $form->get('peopleWorking')->getData();
                if ($free >= $people) {
                    $base->setPeopleWorking($people);
                    $peopleWorking = $people;
                    $this->buildingListItemRepository->save();
                    $this->addFlash('success',"Arbeiter zugeteilt!");
                }
            }
            else
                $this->addFlash('error',"Arbeiter konnten nicht zugeteilt werden!");
        }

        return $this->render('game/buildings/list.html.twig', [
            'planet' => $planet,
            'buildingData' => $buildingData,
            'working' => $peopleWorking,
            'genTechLevel' => $genTechLevel,
            'currentlyBuilding' => $currentlyBuilding,
            'form' => $form
        ]);
    }

    #[Route('/game/buildings/{id}', name: 'game.buildings.show')]
    public function show(?Building $building, Request $request): Response
    {
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));

        $this->buildingService->getBuildingData($building);

        //check building and only allow own planets
        if ($building && $building->isShow() && $planet->getUser() !== $this->getUser()->getData()) {
            return $this->redirectToRoute('game.buildings');
        }

        $buildingData = $this->buildingService->getBuildingData($building);
        $base = $this->buildingListItemRepository->findOneBy(['entity' => $planet, 'building' => BuildingId::BUILDING]);
        $peopleWorking = $base?->getPeopleWorking() ?? 0;
        $genTechLevel = $this->technologyListItemRepository->getTechnologyLevel($this->getUser()->getData(), TechnologyId::GEN) ?? 0;
        $currentlyBuilding = ($base?->getPeopleWorkingStatus() === 1) || !$base;

        //TODO: could be done with live component
        $form = $this->createFormBuilder()
            ->add('peopleOptimized', HiddenType::class, [
                'data' => 0
            ])
            ->add('peopleFree', HiddenType::class, [
                'data' => floor($planet->getPeople()) - $this->buildingListItemRepository->getTotalPeopleWorking($planet) + $peopleWorking
            ])
            ->add('foodAvailable', HiddenType::class, [
                'data' => $planet->getResFood()
            ])
            ->add('foodRequired', HiddenType::class, [
                'data' => $this->configurationService->getInt('people_food_require')
            ])
            ->add('workDone', HiddenType::class, [
                'data' => $this->configurationService->getInt('people_work_done')
            ])
            ->add('peopleWorking', TextType::class, [
                'attr' => [
                    'onKeyUp' => "updatePeopleWorkingBox(this.value,'-1','-1')"
                ],
                'data' => StringUtils::formatNumber($peopleWorking)
            ])
            ->add('timeReduction', TextType::class, [
                'attr' => [
                    'onKeyUp' => "updatePeopleWorkingBox('-1',this.value,'-1')"
                ],
                'data' => StringUtils::formatTimespan($this->configurationService->getInt('people_work_done') * $peopleWorking)
            ])
            ->add('foodUsing', TextType::class, [
                'attr' => [
                    'onKeyUp' => "updatePeopleWorkingBox('-1','-1',this.value);"
                ],
                'data' => StringUtils::formatNumber($this->configurationService->getInt('people_food_require') * $peopleWorking)
            ])
            ->add('send', SubmitType::class, [
                'label' => 'Speichern'
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$currentlyBuilding) {
                // Free: Total people on planet minus total working people on planet
                // PLUS people working in this building (these can be set again)
                $free = floor($planet->getPeople()) - $this->buildingListItemRepository->getTotalPeopleWorking($planet) + $peopleWorking;
                $people = $form->get('peopleWorking')->getData();
                if ($free >= $people) {
                    $base->setPeopleWorking($people);
                    $peopleWorking = $people;
                    $this->buildingListItemRepository->save();
                    $this->addFlash('success',"Arbeiter zugeteilt!");
                }
            }
            else
                $this->addFlash('error',"Arbeiter konnten nicht zugeteilt werden!");
        }

        return $this->render('game/buildings/show.html.twig', [
            'planet' => $planet,
            'building' => $building,
            'form' => $form,
            'working' => $peopleWorking,
            'genTechLevel' => $genTechLevel,
            'currentlyBuilding' => $currentlyBuilding,
        ]);
    }
}
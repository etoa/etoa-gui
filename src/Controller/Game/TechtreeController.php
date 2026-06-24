<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingSearch;
use EtoA\Building\BuildingSort;
use EtoA\Building\BuildingTypeDataRepository;
use EtoA\Defense\DefenseCategoryRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseSearch;
use EtoA\Defense\DefenseSort;
use EtoA\Missile\MissileCategory;
use EtoA\Missile\MissileDataRepository;
use EtoA\Ship\ShipCategoryRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Ship\ShipSort;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyTypeRepository;
use EtoA\Techtree\TechtreeService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TechtreeController extends AbstractGameController
{


    public function __construct(
        private readonly BuildingDataRepository     $buildingDataRepository,
        private readonly BuildingTypeDataRepository $buildingTypeDataRepository,
        private readonly TechtreeService            $techtreeService,
        private readonly TechnologyDataRepository   $technologyDataRepository,
        private readonly TechnologyTypeRepository   $technologyTypeRepository,
        private readonly ShipCategoryRepository     $shipCategoryRepository,
        private readonly ShipDataRepository         $shipDataRepository,
        private readonly DefenseCategoryRepository  $defenseCategoryRepository,
        private readonly DefenseDataRepository      $defenseDataRepository,
        private readonly MissileDataRepository      $missileDataRepository
    )
    {
    }

    #[
        Route('/game/techtree/general', name: 'game.techtree.general')]
    public function general(): Response
    {
        return $this->render('game/techtree/general.html.twig', [
            'planet' => $this->techtreeService->getCurrentPlanet()
        ]);
    }

    #[Route('/game/techtree/building', name: 'game.techtree.building')]
    public function building(): Response
    {
        $buildingCategories = $this->buildingTypeDataRepository->findBy([], ['typeOrder' => 'ASC']);
        $buildingsByCategory = [];

        $buildings = $this->buildingDataRepository->searchBuildings(BuildingSearch::create()->show(), BuildingSort::type());
        foreach ($buildings as $building) {
            $buildingsByCategory[$building->getType()->getId()][] = $building;
        }

        $categoriesData = $this->techtreeService->buildCategoriesData(
            $buildingCategories,
            $buildingsByCategory,
        );

        return $this->render('game/techtree/info.html.twig', [
            'planet' => $this->techtreeService->getCurrentPlanet(),
            'data' => $categoriesData
        ]);
    }

    #[Route('/game/techtree/tech', name: 'game.techtree.tech')]
    public function tech(): Response
    {
        $techCategories = $this->technologyTypeRepository->findBy([], ['order' => 'ASC']);
        $techsByCategory = [];

        $techs = $this->technologyDataRepository->findBy(['show' => true], ['type' => 'ASC', 'order' => 'ASC', 'name' => 'ASC']);
        foreach ($techs as $tech) {
            $techsByCategory[$tech->getType()->getId()][] = $tech;
        }

        $categoriesData = $this->techtreeService->buildCategoriesData(
            $techCategories,
            $techsByCategory,
        );

        return $this->render('game/techtree/info.html.twig', [
            'planet' => $this->techtreeService->getCurrentPlanet(),
            'data' => $categoriesData
        ]);
    }

    #[Route('/game/techtree/ships', name: 'game.techtree.ships')]
    public function ships(): Response
    {
        $shipCategories = $this->shipCategoryRepository->findBy([], ['order' => 'ASC']);
        $shipsByCategory = [];

        $shipSearch = ShipSearch::create()->buildable()->raceOrNull($this->getUser()->getData()->getRace());
        $shipOrder = ShipSort::name();
        $ships = $this->shipDataRepository->searchShips($shipSearch, $shipOrder);

        foreach ($ships as $ship) {
            $shipsByCategory[$ship->getCat()->getId()][] = $ship;
        }

        $categoriesData = $this->techtreeService->buildCategoriesData(
            $shipCategories,
            $shipsByCategory,
        );

        return $this->render('game/techtree/info.html.twig', [
            'planet' => $this->techtreeService->getCurrentPlanet(),
            'data' => $categoriesData
        ]);
    }

    #[Route('/game/techtree/defense', name: 'game.techtree.defense')]
    public function defense(): Response
    {
        $defCategories = $this->defenseCategoryRepository->findBy([], ['order' => 'ASC']);
        $defsByCategory = [];

        $defSearch = DefenseSearch::create()->buildable()->raceOrNull($this->getUser()->getData()->getRace());
        $defOrder = DefenseSort::category();
        $defs = $this->defenseDataRepository->searchDefense($defSearch, $defOrder);

        foreach ($defs as $def) {
            $defsByCategory[$def->getCat()->getId()][] = $def;
        }

        $categoriesData = $this->techtreeService->buildCategoriesData(
            $defCategories,
            $defsByCategory,
        );

        return $this->render('game/techtree/info.html.twig', [
            'planet' => $this->techtreeService->getCurrentPlanet(),
            'data' => $categoriesData
        ]);
    }

    #[Route('/game/techtree/missile', name: 'game.techtree.missile')]
    public function missile(): Response
    {
        $missilesByCategory = [];
        $missileCategory = new MissileCategory();
        $missileCategory->setId(1);
        $missileCategories = [$missileCategory];

        $missiles = $this->missileDataRepository->findBy(['show'=>true],['name'=>'ASC']);

        foreach ($missiles as $missile) {
            $missilesByCategory[1][] = $missile;
        }

        $categoriesData = $this->techtreeService->buildCategoriesData(
            $missileCategories,
            $missilesByCategory,
        );

        return $this->render('game/techtree/info.html.twig', [
            'planet' => $this->techtreeService->getCurrentPlanet(),
            'data' => $categoriesData
        ]);
    }
}
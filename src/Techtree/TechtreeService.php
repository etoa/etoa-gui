<?php

namespace EtoA\Techtree;

use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\AbstractRequirements;
use EtoA\Core\ObjectWithRequirements;
use EtoA\Entity\Planet;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class TechtreeService
{
    public function __construct(
        private readonly PlanetRepository             $planetRepository,
        private readonly RequestStack                 $requestStack,
        private readonly BuildingListItemRepository   $buildingListItemRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository
    )
    {
    }

    public function getCurrentPlanet(): Planet
    {
        $request = $this->requestStack->getCurrentRequest();
        return $this->planetRepository->find($request->getSession()->get('cpid'));
    }

    public function buildCategoriesData(
        array $categories,
        array $itemsByCategory,
    ): array
    {
        $categoriesData = [];

        foreach ($categories as $category) {
            if (!isset($itemsByCategory[$category->getId()])) {
                continue;
            }

            $categoryItems = [];

            foreach ($itemsByCategory[$category->getId()] as $data) {
                $dataInfo = $this->buildInfoData(
                    $data,
                );

                if ($dataInfo !== null) {
                    $categoryItems[] = $dataInfo;
                }
            }

            if (count($categoryItems) > 0) {
                $categoriesData[] = [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'items' => $categoryItems,
                ];
            }
        }

        return $categoriesData;
    }

    private function buildInfoData(
        ObjectWithRequirements $data,
    ): ?array
    {
        $requirements = [];
        foreach ($data->getObjectRequirements()->getValues() as $requirement) {
            $passed = $this->requirementsPassed($requirement);
            $requirements[] = ['passed' => $passed, 'level'=>$requirement->getLevel(),'item' => $requirement->getBuilding() ?? $requirement->getTech()];
        }

        return ['id'=>$data->getId(),'name'=>$data->getName(),'requirements'=>$requirements];
    }

    private function requirementsPassed(AbstractRequirements $requirement): bool
    {
        $cp = $this->getCurrentPlanet();

        if ($requirement->getBuilding() && $requirement->getLevel() > $this->buildingListItemRepository->findOneBy(['building' => $requirement->getBuilding(), 'entity' => $cp])?->getCurrentLevel()) {
            return false;
        }

        if ($requirement->getTech() && $requirement->getLevel() > $this->technologyListItemRepository->findOneBy(['user' => $cp->getUser(), 'technology' => $requirement->getTech()])?->getCurrentLevel()) {
            return false;
        }
        return true;
    }
}
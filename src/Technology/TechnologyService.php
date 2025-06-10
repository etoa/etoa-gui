<?php

namespace EtoA\Technology;

use EtoA\Building\BuildingListItemRepository;
use EtoA\Entity\Planet;
use EtoA\Entity\Technology;
use EtoA\Entity\User;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class TechnologyService
{
    public function __construct(
        private readonly TechnologyRequirementRepository $technologyRequirementRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly Security                 $security,
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly RequestStack $requestStack,
        private readonly PlanetRepository $planetRepository
    )
    {
    }

    public function requirementsPassed(Technology $technology, ?Planet $planet = null, ?User $user = null):bool
    {
        $requirements = $this->technologyRequirementRepository->findBy(['object'=>$technology],['requiredLevel'=>'DESC']);
        $requirements_passed = true;
        $user = $user??$this->security->getUser()->getData();
        $request = $this->requestStack->getCurrentRequest();
        $planet = $planet??$this->planetRepository->find($request->getSession()->get('cpid'));

        foreach ($requirements as $requirement) {
            if ($requirement->getRequiredTechnology()) {
                if ($requirement->getRequiredLevel() > ($this->technologyListItemRepository->findOneBy(['user'=>$user,'technology'=>$requirement->getRequiredTechnology()])?->getCurrentLevel() ?? 0)) {
                    $requirements_passed = false;
                }
            }
            if ($requirement->getRequiredBuilding()) {
                if ($requirement->getRequiredLevel() > ($this->buildingListItemRepository->findOneBy(['user'=>$user,'entity'=>$planet,'building'=>$requirement->getRequiredBuilding()])?->getCurrentLevel() ?? 0)) {
                    $requirements_passed = false;
                }
            }
        }

        return $requirements_passed;
    }
}
<?php

namespace EtoA\Missile;

use EtoA\Building\BuildingListItemRepository;
use EtoA\Entity\Missile;
use EtoA\Entity\Planet;
use EtoA\Entity\User;
use EtoA\Technology\TechnologyListItemRepository;

class MissileService
{


    public function __construct(
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository
    )
    {
    }

    public function checkRequirements(Missile $missile, User $user, Planet $planet):bool
    {
        if(!$missile->isShow()) {
            return false;
        }

        $requirements_passed = true;

        foreach ($missile->getObjectRequirements() as $requirement) {
            if($requirement->getBuilding()) {
                if($this->buildingListItemRepository->findOneBy(['entity'=>$planet,'building'=>$requirement->getBuilding()])?->getCurrentLevel()<$requirement->getLevel()) {
                    $requirements_passed = false;
                }
            } else {
                if($this->technologyListItemRepository->findOneBy(['user'=>$user,'technology'=>$requirement->getTech()])?->getCurrentLevel()<$requirement->getLevel()) {
                    $requirements_passed = false;
                }
            }
        }

        return $requirements_passed;
    }
}
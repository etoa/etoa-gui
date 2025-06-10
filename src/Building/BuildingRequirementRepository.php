<?php declare(strict_types=1);

namespace EtoA\Building;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Entity\BuildingRequirements;
use EtoA\Requirement\AbstractRequirementRepository;

class BuildingRequirementRepository extends AbstractRequirementRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuildingRequirements::class);
    }
}

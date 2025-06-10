<?php declare(strict_types=1);

namespace EtoA\Technology;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Entity\TechnologyRequirement;
use EtoA\Requirement\AbstractRequirementRepository;

class TechnologyRequirementRepository extends AbstractRequirementRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TechnologyRequirement::class);
    }
}

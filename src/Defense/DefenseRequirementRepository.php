<?php declare(strict_types=1);

namespace EtoA\Defense;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Entity\DefenseRequirements;
use EtoA\Requirement\AbstractRequirementRepository;

class DefenseRequirementRepository extends AbstractRequirementRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DefenseRequirements::class);
    }
}

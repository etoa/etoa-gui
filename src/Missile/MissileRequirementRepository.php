<?php declare(strict_types=1);

namespace EtoA\Missile;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Entity\MissileRequirements;
use EtoA\Requirement\AbstractRequirementRepository;

class MissileRequirementRepository extends AbstractRequirementRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MissileRequirements::class);
    }
}

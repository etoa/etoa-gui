<?php declare(strict_types=1);

namespace EtoA\Missile;

use Doctrine\DBAL\Connection;
use EtoA\Requirement\AbstractRequirementRepository;

class MissileRequirementRepository extends AbstractRequirementRepository
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection, 'missile_requirements');
    }
}

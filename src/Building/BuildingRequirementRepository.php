<?php declare(strict_types=1);

namespace EtoA\Building;

use Doctrine\DBAL\Connection;
use EtoA\Requirement\AbstractRequirementRepository;

class BuildingRequirementRepository extends AbstractRequirementRepository
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection, 'building_requirements');
    }
}

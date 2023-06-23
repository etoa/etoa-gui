<?php declare(strict_types=1);

namespace EtoA\Technology;

use Doctrine\DBAL\Connection;
use EtoA\Requirement\AbstractRequirementRepository;

class TechnologyRequirementRepository extends AbstractRequirementRepository
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection, 'tech_requirements');
    }
}

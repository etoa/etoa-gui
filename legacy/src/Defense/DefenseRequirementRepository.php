<?php declare(strict_types=1);

namespace EtoA\Defense;

use Doctrine\DBAL\Connection;
use EtoA\Requirement\AbstractRequirementRepository;

class DefenseRequirementRepository extends AbstractRequirementRepository
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection, 'def_requirements');
    }
}

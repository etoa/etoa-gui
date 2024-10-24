<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AllianceTechnology;
use EtoA\Entity\AllianceTechnologyListItem;

class AllianceTechnologyRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceTechnology::class);
    }

    /**
     * @return array<int, string>
     */
    public function getNames(bool $orderById = false): array
    {
        return $this->fetchIdsWithNames('alliance_technologies', 'alliance_tech_id', 'alliance_tech_name', $orderById);
    }
}

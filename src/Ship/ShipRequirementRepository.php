<?php declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Entity\Ship;
use EtoA\Entity\ShipRequirements;
use EtoA\Requirement\AbstractRequirementRepository;
use EtoA\Technology\TechnologyTypeId;

class ShipRequirementRepository extends AbstractRequirementRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipRequirements::class);
    }

    /**
     * @return ShipRequirements[]
     */
    public function getRequiredSpeedTechnologies(Ship $ship): array
    {
        return $this->createQueryBuilder('q')
            ->innerJoin('App:Technology', 't', 'WITH', 'q.tech = t.id')
            ->where('q.obj = :ship')
            ->andWhere('t.type = :speedCat')
            ->setParameters([
                'ship' => $ship,
                'speedCat' => TechnologyTypeId::SPEED,
            ])
            ->getQuery()
            ->execute();
    }

    /**
     * @return ShipRequiredTechnology[]
     */
    public function getShipsWithRequiredTechnology(int $techId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('s.ship_id, s.ship_name, r.req_level')
            ->from('ship_requirements', 'r')
            ->innerJoin('r', 'ships', 's', 'r.obj_id = s.ship_id')
            ->where('r.req_tech_id = :techId')
            ->andWhere('s.special_ship = 0')
            ->setParameters([
                'techId' => $techId,
            ])
            ->fetchAllAssociative();

        return array_map(fn ($row) => ShipRequiredTechnology::createFromShip($row), $data);
    }
}

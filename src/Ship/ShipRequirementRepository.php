<?php declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\DBAL\Connection;
use EtoA\Requirement\AbstractRequirementRepository;

class ShipRequirementRepository extends AbstractRequirementRepository
{
    public function __construct(Connection $connection)
    {
        parent::__construct($connection, 'ship_requirements');
    }

    /**
     * @return ShipRequiredTechnology[]
     */
    public function getRequiredSpeedTechnologies(int $shipId): array
    {
        $data = $this->createQueryBuilder()
            ->select('t.tech_id, t.tech_name, r.req_level')
            ->from('ship_requirements', 'r')
            ->innerJoin('r', 'technologies', 't', 'req_tech_id = tech_id')
            ->where('r.obj_id = :shipId')
            ->andWhere('t.tech_type_id = :speedCat')
            ->setParameters([
                'shipId' => $shipId,
                'speedCat' => TECH_SPEED_CAT,
            ])
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => ShipRequiredTechnology::createFromTech($row), $data);
    }

    /**
     * @return ShipRequiredTechnology[]
     */
    public function getShipsWithRequiredTechnology(int $techId): array
    {
        $data = $this->createQueryBuilder()
            ->select('s.ship_id, s.ship_name, r.req_level')
            ->from('ship_requirements', 'r')
            ->innerJoin('r', 'ships', 's', 'r.obj_id = s.ship_id')
            ->where('r.req_tech_id = :techId')
            ->andWhere('s.special_ship = 0')
            ->setParameters([
                'techId' => $techId,
            ])
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => ShipRequiredTechnology::createFromShip($row), $data);
    }
}

<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\Core\AbstractRepository;

class ShipRequirementRepository extends AbstractRepository
{
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

    public function getDuplicateTechRequirements(): array
    {
        return $this->getDuplicateRequirements('req_building_id');
    }

    public function getDuplicateBuildingRequirements(): array
    {
        return $this->getDuplicateRequirements('req_tech_id');
    }

    private function getDuplicateRequirements(string $requirement): array
    {
        $data = $this->createQueryBuilder()
            ->select('obj_id', $requirement)
            ->from('ship_requirements')
            ->where($requirement . ' > 0')
            ->groupBy('obj_id')
            ->addGroupBy($requirement)
            ->having('COUNT(*) > 1')
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }
}

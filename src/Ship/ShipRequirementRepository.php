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

        return array_map(fn($row) => new ShipRequiredTechnology($row), $data);
    }
}

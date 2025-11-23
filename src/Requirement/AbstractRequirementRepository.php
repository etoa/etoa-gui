<?php declare(strict_types=1);

namespace EtoA\Requirement;

use EtoA\Core\AbstractRepository;
use EtoA\Entity\Building;
use EtoA\Entity\BuildingRequirements;
use EtoA\Entity\Technology;
use EtoA\Entity\TechnologyRequirement;

abstract class AbstractRequirementRepository extends AbstractRepository
{
    private string $table;


    public function getAll(): RequirementsCollection
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from($this->table)
            ->fetchAllAssociative();

        return new RequirementsCollection(array_map(fn (array $row) => ObjectRequirement::createFromData($row), $data));
    }

    public function getRequirements(int $objectId): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.obj = :objectId')
            ->orderBy('q.level')
            ->setParameter('objectId', $objectId)
            ->getQuery()
            ->execute();
    }

    /**
     * @return BuildingRequirements[]
     */
    public function getRequiredByBuilding(int|Building $buildingId): array
    {
        return $this->findBy(['building'=>$buildingId],['level'=>'DESC']);
    }

    /**
     * @return TechnologyRequirement[]
     */
    public function getRequiredByTechnology(int|Technology $technologyId): array
    {
        return $this->findBy(['tech'=>$technologyId],['level'=>'DESC']);
    }

    public function add(int $objId, int $level, ?int $techId, ?int $buildingId): void
    {
        $this->getConnection()->executeQuery('
            INSERT INTO ' . $this->table . '(obj_id, req_level, req_tech_id, req_building_id)
            VALUES (:objId, :level, :techId, :buildingId)
            ON DUPLICATE KEY UPDATE req_level = :level
        ', [
            'objId' => $objId,
            'level' => $level,
            'techId' => $techId === 0 ? null : $techId,
            'buildingId' => $buildingId === 0 ? null : $buildingId,
        ]);
    }
}

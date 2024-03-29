<?php declare(strict_types=1);

namespace EtoA\Requirement;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

abstract class AbstractRequirementRepository extends AbstractRepository
{
    private string $table;

    public function __construct(Connection $connection, string $table)
    {
        parent::__construct($connection);
        $this->table = $table;
    }

    public function getAll(): RequirementsCollection
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from($this->table)
            ->fetchAllAssociative();

        return new RequirementsCollection(array_map(fn (array $row) => ObjectRequirement::createFromData($row), $data));
    }

    public function getRequirements(int $objectId): RequirementsCollection
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from($this->table)
            ->where('obj_id = :objectId')
            ->orderBy('req_level')
            ->setParameter('objectId', $objectId)
            ->fetchAllAssociative();

        return new RequirementsCollection(array_map(fn (array $row) => ObjectRequirement::createFromData($row), $data));
    }

    /**
     * @return ObjectRequirement[]
     */
    public function getRequiredByBuilding(int $buildingId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from($this->table)
            ->where('req_building_id = :buildingId')
            ->orderBy('req_level')
            ->setParameter('buildingId', $buildingId)
            ->fetchAllAssociative();

        return array_map(fn (array $row) => ObjectRequirement::createFromData($row), $data);
    }

    /**
     * @return ObjectRequirement[]
     */
    public function getRequiredByTechnology(int $technologyId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from($this->table)
            ->where('req_tech_id = :technologyId')
            ->orderBy('req_level')
            ->setParameter('technologyId', $technologyId)
            ->fetchAllAssociative();

        return array_map(fn (array $row) => ObjectRequirement::createFromData($row), $data);
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

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete($this->table)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }
}

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
            ->execute()
            ->fetchAllAssociative();

        return new RequirementsCollection(array_map(fn (array $row) => new ObjectRequirement($row), $data));
    }

    public function getRequirements(int $objectId): RequirementsCollection
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from($this->table)
            ->where('obj_id = :objectId')
            ->orderBy('req_level')
            ->setParameter('objectId', $objectId)
            ->execute()
            ->fetchAllAssociative();

        return new RequirementsCollection(array_map(fn (array $row) => new ObjectRequirement($row), $data));
    }

    /**
     * @return array<int, int>
     */
    public function getDuplicateTechRequirements(): array
    {
        return $this->getDuplicateRequirements('req_building_id');
    }

    /**
     * @return array<int, int>
     */
    public function getDuplicateBuildingRequirements(): array
    {
        return $this->getDuplicateRequirements('req_tech_id');
    }

    /**
     * @return array<int, int>
     */
    private function getDuplicateRequirements(string $requirement): array
    {
        $data = $this->createQueryBuilder()
            ->select('obj_id', $requirement)
            ->from($this->table)
            ->where($requirement . ' > 0')
            ->groupBy('obj_id')
            ->addGroupBy($requirement)
            ->having('COUNT(*) > 1')
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
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
            'techId' => $techId,
            'buildingId' => $buildingId,
        ]);
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete($this->table)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }
}

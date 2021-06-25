<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Core\AbstractRepository;

class TechnologyDataRepository extends AbstractRepository
{
    /**
     * @return array<int, string>
     */
    public function getTechnologyNames(): array
    {
        return $this->createQueryBuilder()
            ->select('t.tech_id, t.tech_name')
            ->from('technologies','t')
            ->innerJoin('t', 'tech_types', 'tt', 't.tech_type_id = tt.type_id')
            ->where('t.tech_show = 1')
            ->orderBy('tt.type_order')
            ->addOrderBy('t.tech_order')
            ->addOrderBy('t.tech_name')
            ->execute()
            ->fetchAllKeyValue();
    }

    /**
     * @return Technology[]
     */
    public function getTechnologies(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('technologies')
            ->where('tech_show = 1')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Technology($row), $data);
    }

    public function getTechnology(int $techId): ?Technology
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('technologies')
            ->where('tech_show = 1')
            ->andWhere('tech_id = :techId')
            ->setParameter('techId', $techId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Technology($data) : null;
    }

    /**
     * @return Technology[]
     */
    public function getTechnologiesByType(int $typeId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('technologies')
            ->where('tech_show = 1')
            ->andWhere('tech_type_id = :typeId')
            ->setParameter('typeId', $typeId)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Technology($row), $data);
    }
}

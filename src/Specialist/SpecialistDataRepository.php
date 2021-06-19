<?php declare(strict_types=1);

namespace EtoA\Specialist;

use EtoA\Core\AbstractRepository;

class SpecialistDataRepository extends AbstractRepository
{
    /**
     * @return array<int, string>
     */
    public function getSpecialistNames(): array
    {
        return $this->createQueryBuilder()
            ->select('s.specialist_id, s.specialist_name')
            ->from('specialists', 's')
            ->andWhere('s.specialist_enabled = 1')
            ->orderBy('s.specialist_name')
            ->execute()
            ->fetchAllKeyValue();
    }

    public function getSpecialist(int $specialistId): ?Specialist
    {
        $data = $this->createQueryBuilder()
            ->select('s.*')
            ->from('specialists', 's')
            ->where('s.specialist_enabled = 1')
            ->andWhere('s.specialist_id = :id')
            ->setParameter('id', $specialistId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Specialist($data) : null;
    }

    /**
     * @return Specialist[]
     */
    public function getActiveSpecialists(): array
    {
        $data = $this->createQueryBuilder()
            ->select('s.*')
            ->from('specialists', 's')
            ->where('s.specialist_enabled = 1')
            ->orderBy('s.specialist_name')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Specialist($row), $data);
    }
}

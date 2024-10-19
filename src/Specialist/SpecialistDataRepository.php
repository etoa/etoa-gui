<?php declare(strict_types=1);

namespace EtoA\Specialist;

use EtoA\Core\AbstractRepository;
use EtoA\Entity\Specialist;

class SpecialistDataRepository extends AbstractRepository
{
    /**
     * @return array<int, string>
     */
    public function getSpecialistNames(): array
    {
        return $this->createQueryBuilder('q')
            ->select('s.specialist_id, s.specialist_name')
            ->from('specialists', 's')
            ->andWhere('s.specialist_enabled = 1')
            ->orderBy('s.specialist_name')
            ->fetchAllKeyValue();
    }

    public function getSpecialist(int $specialistId): ?Specialist
    {
        $data = $this->createQueryBuilder('q')
            ->select('s.*')
            ->from('specialists', 's')
            ->where('s.specialist_enabled = 1')
            ->andWhere('s.specialist_id = :id')
            ->setParameter('id', $specialistId)
            ->fetchAssociative();

        return $data !== false ? new Specialist($data) : null;
    }

    /**
     * @return Specialist[]
     */
    public function getActiveSpecialists(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('s.*')
            ->from('specialists', 's')
            ->where('s.specialist_enabled = 1')
            ->orderBy('s.specialist_name')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Specialist($row), $data);
    }
}

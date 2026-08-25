<?php declare(strict_types=1);

namespace EtoA\Specialist;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Specialist;

class SpecialistDataRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Specialist::class);
    }

    /**
     * Despite the name this returns specialist entities, not a name map. The admin
     * user form relies on that (choice_value: 'id', choice_label: 'name').
     *
     * @return Specialist[]
     */
    public function getSpecialistNames(): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.enabled = 1')
            ->orderBy('q.name')
            ->getQuery()
            ->execute();
    }

    public function getSpecialist(int $specialistId): ?Specialist
    {
        return $this->findOneBy(['id' => $specialistId, 'enabled' => true]);
    }

    /**
     * @return Specialist[]
     */
    public function getActiveSpecialists(): array
    {
        return $this->findBy(['enabled'=>true],['name'=>'ASC']);
    }
}

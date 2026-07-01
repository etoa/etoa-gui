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
     * @return array<int, string>
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
        return $this->findBy(['enabled'=>true],['name'=>'ASC']);
    }
}

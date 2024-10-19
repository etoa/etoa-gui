<?php declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;

class AllianceRightRepository extends AbstractRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceRight::class);
    }

    /**
     * @return AllianceRight[]
     */
    public function getRights(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('alliance_rights')
            ->orderBy('right_id', 'ASC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceRight($row), $data);
    }
}

<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceRightRepository extends AbstractRepository
{
    /**
     * @return AllianceRight[]
     */
    public function getRights(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('alliance_rights')
            ->orderBy('right_id', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceRight($row), $data);
    }
}

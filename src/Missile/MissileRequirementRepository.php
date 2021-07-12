<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\Core\AbstractRepository;

class MissileRequirementRepository extends AbstractRepository
{
    /**
     * @return MissileRequirement[]
     */
    public function getAll(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('missile_requirements')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new MissileRequirement($row), $data);
    }
}

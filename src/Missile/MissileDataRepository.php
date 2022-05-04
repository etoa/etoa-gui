<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\Core\AbstractRepository;

class MissileDataRepository extends AbstractRepository
{
    /**
     * @return array<int, string>
     */
    public function getMissileNames(bool $showAll = false, bool $orderById = false): array
    {
        $qb = $this->createQueryBuilder()
            ->select('missile_id', 'missile_name')
            ->from('missiles');

        if (!$showAll) {
            $qb->where('missile_show = 1');
        }

        return $qb
            ->orderBy($orderById ? 'missile_id' : 'missile_name')
            ->fetchAllKeyValue();
    }

    public function getMissile(int $missileId): ?Missile
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('missiles')
            ->where('missile_show=1')
            ->andWhere('missile_id = :missileId')
            ->setParameter('missileId', $missileId)
            ->fetchAssociative();

        return $data !== false ? new Missile($data) : null;
    }

    /**
     * @return Missile[]
     */
    public function getMissiles(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('missiles')
            ->where('missile_show=1')
            ->fetchAllAssociative();

        $result = [];
        foreach ($data as $row) {
            $missile = new Missile($row);
            $result[$missile->id] = $missile;
        }

        return $result;
    }
}

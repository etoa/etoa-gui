<?php

declare(strict_types=1);

namespace EtoA\Bookmark;

use EtoA\Core\AbstractRepository;
use EtoA\Universe\Resources\BaseResources;

class FleetBookmarkRepository extends AbstractRepository
{
    /**
     * @return FleetBookmark[]
     */
    public function getForUser(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('fleet_bookmarks')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->orderBy('name')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new FleetBookmark($row), $data);
    }

    public function get(int $id, int $userId): ?FleetBookmark
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('fleet_bookmarks')
            ->where('id = :id')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new FleetBookmark($data) : null;
    }

    public function add(int $userId, string $name, int $targetId, string $ships, BaseResources $freight, BaseResources $fetch, string $action, int $speed): int
    {
        $this->createQueryBuilder()
            ->insert('fleet_bookmarks')
            ->values([
                'user_id' => ':userId',
                'name' => ':name',
                'target_id' => ':targetId',
                'ships' => ':ships',
                'res' => ':res',
                'resfetch' => ':fetch',
                'action' => ':action',
                'speed' => ':speed',
            ])
            ->setParameters([
                'userId' => $userId,
                'name' => $name,
                'targetId' => $targetId,
                'ships' => $ships,
                'res' => implode(',', [$freight->metal, $freight->crystal, $freight->plastic, $freight->fuel, $freight->food, $freight->people]),
                'fetch' => implode(',', [$fetch->metal, $fetch->crystal, $fetch->plastic, $fetch->fuel, $fetch->food, $fetch->people]),
                'action' => $action,
                'speed' => $speed,
            ])
            ->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function update(int $id, int $userId, string $name, int $targetId, string $ships, BaseResources $freight, BaseResources $fetch, string $action, int $speed): void
    {
        $this->createQueryBuilder()
            ->update('fleet_bookmarks')
            ->set('name', ':name')
            ->set('target_id', ':targetId')
            ->set('ships', ':ships')
            ->set('res', ':res')
            ->set('resfetch', ':fetch')
            ->set('action', ':action')
            ->set('speed', ':speed')
            ->where('id = :id')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
                'name' => $name,
                'targetId' => $targetId,
                'ships' => $ships,
                'res' => implode(',', [$freight->metal, $freight->crystal, $freight->plastic, $freight->fuel, $freight->food, $freight->people]),
                'fetch' => implode(',', [$fetch->metal, $fetch->crystal, $fetch->plastic, $fetch->fuel, $fetch->food, $fetch->people]),
                'action' => $action,
                'speed' => $speed,
            ])
            ->execute();
    }

    public function remove(int $id, int $userId): bool
    {
        return (bool) $this->createQueryBuilder()
            ->delete('fleet_bookmarks')
            ->where('user_id = :userId')
            ->andWhere('id = :id')
            ->setParameters([
                'userId' => $userId,
                'id' => $id,
            ])
            ->execute();
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder()
            ->delete('fleet_bookmarks')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }
}

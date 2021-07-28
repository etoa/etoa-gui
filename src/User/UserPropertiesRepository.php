<?php

declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class UserPropertiesRepository extends AbstractRepository
{
    public function addBlank(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('user_properties')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute();

        $this->createQueryBuilder()
            ->insert('user_properties')
            ->values([
                'id' => ':id',
            ])
            ->setParameters([
                'id' => $id,
            ])
            ->execute();
    }

    /**
     * @return array<string, int>
     */
    public function getDesignStats(int $limit): array
    {
        $data = $this->getConnection()
            ->executeQuery(
                "SELECT
                    css_style,
                    COUNT(id) cnt
                FROM
                    user_properties
                GROUP BY
                    css_style
                ORDER BY
                    cnt DESC
                LIMIT $limit;"
            )
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return array<int, array{name: string, cnt: int}>
     */
    public function getImagePackStats(int $limit): array
    {
        $data = $this->getConnection()
            ->executeQuery(
                "SELECT
                    image_url as name,
                    COUNT(id) as cnt
                FROM
                    user_properties
                GROUP BY
                    image_url
                ORDER BY
                    cnt DESC
                LIMIT $limit;"
            )
            ->fetchAllAssociative();

        return array_map(fn ($arr) => [
            'name' => (string) $arr['name'],
            'cnt' => (int) $arr['cnt'],
        ], $data);
    }

    /**
     * @return array<int, array{name: string, cnt: int}>
     */
    public function getImageExtensionStats(int $limit): array
    {
        $data = $this->getConnection()
            ->executeQuery(
                "SELECT
                    image_ext as name,
                    COUNT(id) as cnt
                FROM
                    user_properties
                GROUP BY
                    image_ext
                ORDER BY
                    cnt DESC
                LIMIT $limit;"
            )
            ->fetchAllAssociative();

        return array_map(fn ($arr) => [
            'name' => (string) $arr['name'],
            'cnt' => (int) $arr['cnt'],
        ], $data);
    }

    /**
     * @param int[] $availableUserIds
     */
    public function getOrphanedCount(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->select('count(id)')
            ->from('user_properties')
            ->where($qb->expr()->notIn('id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchOne();
    }

    /**
     * @param int[] $availableUserIds
     */
    public function deleteOrphaned(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->delete('user_properties')
            ->where($qb->expr()->notIn('id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder()
            ->delete('user_properties')
            ->where('id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }
}

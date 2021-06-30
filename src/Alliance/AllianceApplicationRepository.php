<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceApplicationRepository extends AbstractRepository
{
    public function hasApplications(int $allianceId): bool
    {
        $data = (int) $this->createQueryBuilder()
            ->select("COUNT(user_id)")
            ->from('alliance_applications')
            ->where('alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute()
            ->fetchOne();

        return $data > 0;
    }

    public function findOneForUser(int $userId): ?AllianceApplication
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('alliance_applications')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new AllianceApplication($data) : null;
    }

    /**
     * @return array<AllianceApplication>
     */
    public function findForAlliance(int $allianceId): array
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('alliance_applications')
            ->where('alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->orderBy('timestamp', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new AllianceApplication($row), $data);
    }

    public function add(int $allianceId, int $userId, string $text): int
    {
        $this->createQueryBuilder()
            ->insert('alliance_applications')
            ->values([
                'user_id' => ':userId',
                'alliance_id' => ':allianceId',
                'text' => ':text',
                'timestamp' => ':timestamp',
            ])
            ->setParameters([
                'userId' => $userId,
                'allianceId' => $allianceId,
                'text' => $text,
                'timestamp' => time(),
            ])
            ->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function removeForAlliance(int $allianceId): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_applications')
            ->where('alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute();
    }

    public function removeForAllianceAndUser(int $allianceId, int $userId): bool
    {
        $affected = (int) $this->createQueryBuilder()
            ->delete('alliance_applications')
            ->where('alliance_id = :allianceId')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'allianceId' => $allianceId,
                'userId' => $userId,
            ])
            ->execute();

        return $affected > 0;
    }
}

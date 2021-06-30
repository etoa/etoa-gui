<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceApplicationRepository extends AbstractRepository
{
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

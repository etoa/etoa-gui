<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceApplicationRepository extends AbstractRepository
{
    public function countApplications(int $allianceId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(user_id)')
            ->from('alliance_applications')
            ->where('alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute()
            ->fetchOne();
    }

    public function getUserApplication(int $userId): ?UserAllianceApplication
    {
        $data = $this->createQueryBuilder()
            ->select('alliance_id, timestamp')
            ->from('alliance_applications')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new UserAllianceApplication($data) : null;
    }

    /**
     * @return AllianceApplication[]
     */
    public function getAllianceApplications(int $allianceId): array
    {
        $data = $this->createQueryBuilder()
            ->select('a.timestamp, a.text, u.user_id, u.user_nick, u.user_points, u.user_rank, u.user_registered')
            ->from('alliance_applications', 'a')
            ->innerJoin('a', 'users', 'u', 'a.user_id = u.user_id')
            ->where('a.alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceApplication($row), $data);
    }

    public function addApplication(int $userId, int $allianceId, string $application): void
    {
        $this->createQueryBuilder()
            ->insert('alliance_applications')
            ->values([
                'user_id' => ':userId',
                'alliance_id' => ':allianceId',
                'text' => ':application',
                'timestamp' => ':now',
            ])
            ->setParameters([
                'userId' => $userId,
                'allianceId' => $allianceId,
                'application' => $application,
                'now' => time(),
            ])
            ->execute();
    }

    public function deleteApplication(int $userId, int $allianceId): bool
    {
        return (bool) $this->createQueryBuilder()
            ->delete('alliance_applications')
            ->where('alliance_id = :allianceId')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'allianceId' => $allianceId,
                'userId' => $userId,
            ])
            ->execute();
    }

    public function deleteAllianceApplication(int $allianceId): int
    {
        return (int) $this->createQueryBuilder()
            ->delete('alliance_applications')
            ->where('alliance_id = :allianceId')
            ->setParameters([
                'allianceId' => $allianceId,
            ])
            ->execute();
    }

    public function deleteUserApplication(int $userId): bool
    {
        return (bool) $this->createQueryBuilder()
            ->delete('alliance_applications')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->execute();
    }
}

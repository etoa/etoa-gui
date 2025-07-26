<?php declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceApplication;
use EtoA\Entity\User;

class AllianceApplicationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceApplication::class);
    }

    public function countApplications(int $allianceId): int
    {
        return $this->count(['alliance'=>$allianceId]);
    }

    public function getUserApplication(int $userId): ?UserAllianceApplication
    {
        $data = $this->createQueryBuilder('q')
            ->select('alliance_id, timestamp')
            ->from('alliance_applications')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->fetchAssociative();

        return $data !== false ? new UserAllianceApplication($data) : null;
    }

    /**
     * @return AllianceApplication[]
     */
    public function getAllianceApplications(int $allianceId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('a.timestamp, a.text, u.user_id, u.user_nick, u.user_points, u.user_rank, u.user_registered')
            ->from('alliance_applications', 'a')
            ->innerJoin('a', 'users', 'u', 'a.user_id = u.user_id')
            ->where('a.alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceApplication($row), $data);
    }

    public function addApplication(int $userId, int $allianceId, string $application): void
    {
        $this->createQueryBuilder('q')
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
            ->executeQuery();
    }

    public function deleteApplication(int $userId, int $allianceId): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->delete('alliance_applications')
            ->where('alliance_id = :allianceId')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'allianceId' => $allianceId,
                'userId' => $userId,
            ])
            ->executeQuery()
            ->rowCount();
    }

    public function deleteAllianceApplication(Alliance $alliance): void
    {
        $applications = $this->findBy(['alliance'=>$alliance]);

        foreach ($applications as $application) {
            $this->remove($application);
        }

        $this->save();
    }

    public function deleteUserApplication(User $user): void
    {
         $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->setParameters([
                'user' => $user,
            ])
            ->getQuery()
            ->execute();
    }
}

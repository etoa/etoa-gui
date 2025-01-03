<?php declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceSpend;
use EtoA\Entity\User;
use EtoA\Universe\Resources\BaseResources;

class AllianceSpendRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceSpend::class);
    }

    public function addEntry(Alliance $alliance, User $user, BaseResources $resources): void
    {
        $entry = new AllianceSpend();
        $entry->setAlliance($alliance);
        $entry->setUser($user);
        $entry->setMetal($resources->metal);
        $entry->setCrystal($resources->crystal);
        $entry->setPlastic($resources->plastic);
        $entry->setFuel($resources->fuel);
        $entry->setFood($resources->food);
        $entry->setTime(time());

        $this->persist($entry);
        $this->save();
    }

    public function getTotalSpent(Alliance $alliance, User $user = null): BaseResources
    {
        $qb = $this->createQueryBuilder('q')
            ->select('SUM(q.metal) AS metal, SUM(q.crystal) AS crystal, SUM(q.plastic) AS plastic, SUM(q.fuel) AS fuel, SUM(q.food) AS food')
            ->where('q.alliance = :alliance')
            ->setParameter('alliance', $alliance);

        if ($user) {
            $qb
                ->andWhere('q.user = :user')
                ->setParameter('user', $user);
        }

        $data = $qb
            ->getQuery()
            ->getOneOrNullResult();

        $resources = new BaseResources();
        if ($data) {
            $resources->metal = (int) $data['metal'];
            $resources->crystal = (int) $data['crystal'];
            $resources->plastic = (int) $data['plastic'];
            $resources->fuel = (int) $data['fuel'];
            $resources->food = (int) $data['food'];
        }

        return $resources;
    }

    /**
     * @return AllianceSpend[]
     */
    public function getSpent(int $alliance, ?int $userId, int $limit): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('*')
            ->from('alliance_spends')
            ->where('alliance_spend_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->orderBy('alliance_spend_time', 'DESC');

        if ($userId > 0) {
            $qb
                ->andWhere('alliance_spend_user_id = :userId')
                ->setParameter('userId', $userId);
        }

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        $data = $qb
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceSpend($row), $data);
    }

    public function deleteAllianceEntries(int $allianceId): void
    {
        $this->createQueryBuilder('q')
            ->delete('alliance_spends')
            ->where('alliance_spend_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->executeQuery();
    }
}

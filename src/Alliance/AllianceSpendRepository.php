<?php declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Universe\Resources\BaseResources;

class AllianceSpendRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceSpend::class);
    }

    public function addEntry(int $allianceId, int $userId, BaseResources $resources): void
    {
        $this->createQueryBuilder('q')
            ->insert('alliance_spends')
            ->values([
                'alliance_spend_alliance_id' => ':allianceId',
                'alliance_spend_user_id' => ':userId',
                'alliance_spend_metal' => ':metal',
                'alliance_spend_crystal' => ':crystal',
                'alliance_spend_plastic' => ':plastic',
                'alliance_spend_fuel' => ':fuel',
                'alliance_spend_food' => ':food',
                'alliance_spend_time' => ':now',
            ])
            ->setParameters([
                'allianceId' => $allianceId,
                'userId' => $userId,
                'now' => time(),
                'metal' => $resources->metal,
                'crystal' => $resources->crystal,
                'plastic' => $resources->plastic,
                'fuel' => $resources->fuel,
                'food' => $resources->food,
            ])
            ->executeQuery();
    }

    public function getTotalSpent(int $allianceId, int $userId = null): BaseResources
    {
        $qb = $this->createQueryBuilder('q')
            ->select('SUM(alliance_spend_metal) AS metal, SUM(alliance_spend_crystal) AS crystal, SUM(alliance_spend_plastic) AS plastic, SUM(alliance_spend_fuel) AS fuel, SUM(alliance_spend_food) AS food')
            ->from('alliance_spends')
            ->where('alliance_spend_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId);

        if ($userId > 0) {
            $qb
                ->andWhere('alliance_spend_user_id = :userId')
                ->setParameter('userId', $userId);
        }

        $data = $qb
            ->fetchAssociative();

        $resources = new BaseResources();
        if ($data !== false) {
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
    public function getSpent(int $allianceId, ?int $userId, int $limit): array
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

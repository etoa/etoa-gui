<?php declare(strict_types=1);

namespace EtoA\Market;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\MarketResource;
use EtoA\Entity\Planet;
use EtoA\Entity\User;
use EtoA\Universe\Resources\BaseResources;
use EtoA\Universe\Resources\ResourceNames;

class MarketResourceRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketResource::class);
    }

    public function add(User $user, Planet $entity, ?User $forUser, ?Alliance $forAlliance, string $text, BaseResources $sell, BaseResources $buy): MarketResource
    {
        $offer = (new MarketResource())
            ->setUser($user)
            ->setEntity($entity)
            ->setForUser($forUser)
            ->setForAlliance($forAlliance)
            ->setText($text)
            ->setDate(time())
            ->setSell0($sell->metal)
            ->setSell1($sell->crystal)
            ->setSell2($sell->plastic)
            ->setSell3($sell->fuel)
            ->setSell4($sell->food)
            ->setBuy0($buy->metal)
            ->setBuy1($buy->crystal)
            ->setBuy2($buy->plastic)
            ->setBuy3($buy->fuel)
            ->setBuy4($buy->food);

        $this->persist($offer);
        $this->save();

        return $offer;
    }

    /**
     * @return MarketResource[]
     */
    public function getAll(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('market_ressource')
            ->orderBy('datum', 'ASC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new MarketResource($row), $data);
    }

    /**
     * @return MarketResource[]
     */
    public function getBuyableOffers(User $user, ?BaseResources $sellFilter = null, ?BaseResources $buyFilter = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.user <> :userId')
            ->andWhere('q.forUser IS NULL OR q.forUser = :userId')
            ->andWhere('q.forAlliance IS NULL OR q.forAlliance = :allianceId')
            ->setParameters([
                'userId' => $user,
                'allianceId' => $user->getAlliance(),
            ]);


        if ($sellFilter && $sellFilter->getSum() > 0) {
            $filter = [];
            foreach (array_keys(ResourceNames::NAMES) as $index) {
                if ($sellFilter->get($index) > 0) {
                    $filter[] = 'q.sell' . $index . ' > 0';
                }
            }
            $qb->andWhere(implode(' OR ', $filter));
        }

        if ($buyFilter && $buyFilter->getSum() > 0) {
            $filter = [];
            foreach (array_keys(ResourceNames::NAMES) as $index) {
                if ($buyFilter->get($index) > 0) {
                    $filter[] = 'q.buy' . $index . ' > 0';
                }
            }
            $qb->andWhere(implode(' OR ', $filter));
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function countBuyableOffers(int $userId, int $allianceId): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('count(*)')
            ->from('market_ressource')
            ->where('user_id <> :userId')
            ->andWhere('for_user = 0 OR for_user = :userId')
            ->andWhere('for_alliance = 0 OR for_alliance = :allianceId')
            ->setParameters([
                'userId' => $userId,
                'allianceId' => $allianceId,
            ])
            ->fetchOne();
    }

    public function getBuyableOffer(int $id, int|User $user, ?Alliance $alliance): ?MarketResource
    {
        return $this->createQueryBuilder('q')
            ->where('q.id = :id')
            ->andWhere('q.user <> :userId')
            ->andWhere('q.forUser IS NULL OR q.forUser = :userId')
            ->andWhere('q.forAlliance IS NULL OR q.forAlliance = :allianceId')
            ->andWhere('q.buyable = 1')
            ->setParameters([
                'id' => $id,
                'userId' => $user,
                'allianceId' => $alliance,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return MarketResource[]
     */
    public function getUserOffers(User $user): array
    {
        return $this->findBy(['user'=>$user,'buyable'=>true],['date'=>'ASC']);
    }

    public function getUserOffer(int $id, int $userId): ?MarketResource
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('market_ressource')
            ->where('id = :id')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
            ])
            ->fetchAssociative();

        return $data !== false ? new MarketResource($data) : null;
    }

    public function deleteUserOffers(User $user) : void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function delete(MarketResource $offer) : void
    {
        $this->remove($offer);
        $this->save();
    }
}

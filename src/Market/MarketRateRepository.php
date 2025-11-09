<?php declare(strict_types=1);

namespace EtoA\Market;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\MarketRate;
use EtoA\Entity\User;

class MarketRateRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MarketRate::class);
    }

    /**
     * @return MarketRate[]
     */
    public function getRates(int $amount, int $offset = 0): array
    {
        return $this->createQueryBuilder('q')
            ->orderBy('q.id', 'DESC')
            ->setMaxResults($amount)
            ->setFirstResult($offset)
            ->getQuery()
            ->execute();
    }

    public function removeWhereIdLowerThan(int $id): void
    {
        $this->createQueryBuilder('q')
        ->delete('market_rates')
        ->where('id < :id')
        ->setParameters([
            'id' => $id,
        ])
        ->executeQuery();
    }
}

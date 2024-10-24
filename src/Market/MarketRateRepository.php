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
        $data = $this->createQueryBuilder('q')
            ->select("*")
            ->from('market_rates')
            ->orderBy('id', 'DESC')
            ->setMaxResults($amount)
            ->setFirstResult($offset)
            ->fetchAllAssociative();

        return array_map(fn ($row) => MarketRate::createFromArray($row), $data);
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

<?php declare(strict_types=1);

namespace EtoA\Market;

use EtoA\Core\AbstractRepository;

class MarketRateRepository extends AbstractRepository
{
    /**
     * @return MarketRate[]
     */
    public function getRates(int $amount): array
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('market_rates')
            ->orderBy('id', 'DESC')
            ->setMaxResults($amount)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new MarketRate($row), $data);
    }
}

<?php declare(strict_types=1);

namespace EtoA\Market;

use EtoA\Core\AbstractRepository;

class MarketRateRepository extends AbstractRepository
{
    /**
     * @return MarketRate[]
     */
    public function getRates(int $amount, int $offset = 0): array
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('market_rates')
            ->orderBy('id', 'DESC')
            ->setMaxResults($amount)
            ->setFirstResult($offset)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => MarketRate::createFromArray($row), $data);
    }

    public function save(MarketRate $rate): void
    {
        if ($rate->id !== null) {
            $this->createQueryBuilder()
                ->update('market_rates')
                ->set('timestamp', ':timestamp')
                ->set('supply_0', ':supply0')
                ->set('supply_1', ':supply1')
                ->set('supply_2', ':supply2')
                ->set('supply_3', ':supply3')
                ->set('supply_4', ':supply4')
                ->set('supply_5', ':supply5')
                ->set('demand_0', ':demand0')
                ->set('demand_1', ':demand1')
                ->set('demand_2', ':demand2')
                ->set('demand_3', ':demand3')
                ->set('demand_4', ':demand4')
                ->set('demand_5', ':demand5')
                ->set('rate_0', ':rate0')
                ->set('rate_1', ':rate1')
                ->set('rate_2', ':rate2')
                ->set('rate_3', ':rate3')
                ->set('rate_4', ':rate4')
                ->set('rate_5', ':rate5')
                ->where('id = :id')
                ->setParameters([
                    'id' => $rate->id,
                    'timestamp' => $rate->timestamp,
                    'supply0' => $rate->supply0,
                    'supply1' => $rate->supply1,
                    'supply2' => $rate->supply2,
                    'supply3' => $rate->supply3,
                    'supply4' => $rate->supply4,
                    'supply5' => $rate->supply5,
                    'demand0' => $rate->demand0,
                    'demand1' => $rate->demand1,
                    'demand2' => $rate->demand2,
                    'demand3' => $rate->demand3,
                    'demand4' => $rate->demand4,
                    'demand5' => $rate->demand5,
                    'rate0' => $rate->rate0,
                    'rate1' => $rate->rate1,
                    'rate2' => $rate->rate2,
                    'rate3' => $rate->rate3,
                    'rate4' => $rate->rate4,
                    'rate5' => $rate->rate5,
                ])
                ->execute();

            return;
        }

        $this->createQueryBuilder()
            ->insert('market_rates')
            ->values([
                'timestamp' => ':timestamp',
                'supply_0' => ':supply0',
                'supply_1' => ':supply1',
                'supply_2' => ':supply2',
                'supply_3' => ':supply3',
                'supply_4' => ':supply4',
                'supply_5' => ':supply5',
                'demand_0' => ':demand0',
                'demand_1' => ':demand1',
                'demand_2' => ':demand2',
                'demand_3' => ':demand3',
                'demand_4' => ':demand4',
                'demand_5' => ':demand5',
                'rate_0' => ':rate0',
                'rate_1' => ':rate1',
                'rate_2' => ':rate2',
                'rate_3' => ':rate3',
                'rate_4' => ':rate4',
                'rate_5' => ':rate5',
            ])
            ->setParameters([
                'timestamp' => $rate->timestamp,
                'supply0' => $rate->supply0,
                'supply1' => $rate->supply1,
                'supply2' => $rate->supply2,
                'supply3' => $rate->supply3,
                'supply4' => $rate->supply4,
                'supply5' => $rate->supply5,
                'demand0' => $rate->demand0,
                'demand1' => $rate->demand1,
                'demand2' => $rate->demand2,
                'demand3' => $rate->demand3,
                'demand4' => $rate->demand4,
                'demand5' => $rate->demand5,
                'rate0' => $rate->rate0,
                'rate1' => $rate->rate1,
                'rate2' => $rate->rate2,
                'rate3' => $rate->rate3,
                'rate4' => $rate->rate4,
                'rate5' => $rate->rate5,
            ])
            ->execute();

        $rate->id = (int) $this->getConnection()->lastInsertId();
    }

    public function removeWhereIdLowerThan(int $id): void
    {
        $this->createQueryBuilder()
        ->delete('market_rates')
        ->where('id < :id')
        ->setParameters([
            'id' => $id,
        ])
        ->execute();
    }
}

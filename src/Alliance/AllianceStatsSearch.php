<?php declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\DBAL\Query\QueryBuilder;

class AllianceStatsSearch
{
    private string $sort = 'points';
    private string $order = 'DESC';

    public static function create(): AllianceStatsSearch
    {
        return new AllianceStatsSearch();
    }

    public static function createAllianceBase(): AllianceStatsSearch
    {
        return self::create()->withSort('apoints', 'DESC');
    }

    public function withSort(string $sort, string $order): self
    {
        if (!in_array($order, ['ASC', 'DESC'], true)) {
            throw new \InvalidArgumentException('Invalid value for $order: ' . $order);
        }

        if (!in_array($sort, ['bpoints', 'tpoints', 'spoints', 'apoints', 'uavg', 'cnt', 'points'], true)) {
            throw new \InvalidArgumentException('Invalid value for $sort: ' . $order);
        }

        $this->sort = $sort;
        $this->order = $order;

        return $this;
    }

    public function apply(QueryBuilder $qb): QueryBuilder
    {
        return $qb->orderBy($this->sort, $this->order);
    }
}

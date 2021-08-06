<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\Database\AbstractSort;

class AllianceStatsSort extends AbstractSort
{
    private function __construct()
    {
        $this->sorts = ['points' => 'DESC'];
    }

    public static function create(): AllianceStatsSort
    {
        return new AllianceStatsSort();
    }

    public static function createAllianceBase(): AllianceStatsSort
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

        $this->sorts = [$sort => $order];

        return $this;
    }
}

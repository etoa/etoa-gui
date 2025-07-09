<?php

declare(strict_types=1);

namespace EtoA\Support;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\RuntimeData;

class RuntimeDataStore extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RuntimeData::class);
    }

    public function get(string $key, string $default = null): ?string
    {
        return $this->findOneBy(['dataKey'=>$key])??$default;
    }

    public function set(string $key, string $value): void
    {
        $this->getConnection()
            ->executeStatement("
				REPLACE INTO
					runtime_data
				(
					data_key,
					data_value
				)
				VALUES
				(
					:key,
					:value
				)
				;", [
                'key' => $key,
                'value' => $value,
            ]);
    }
}

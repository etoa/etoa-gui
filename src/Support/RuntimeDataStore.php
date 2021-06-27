<?php

declare(strict_types=1);

namespace EtoA\Support;

use EtoA\Core\AbstractRepository;

class RuntimeDataStore extends AbstractRepository
{
    public function get(string $key, string $default = null): ?string
    {
        $value = $this->createQueryBuilder()
            ->select('data_value')
            ->from('runtime_data')
            ->where('data_key = :key')
            ->setParameter('key', $key)
            ->execute()
            ->fetchOne();

        return $value !== false ? $value : $default;
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

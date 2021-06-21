<?php

declare(strict_types=1);

namespace EtoA\Core\Configuration;

use EtoA\Core\AbstractRepository;

class ConfigurationRepository extends AbstractRepository
{
    /**
     * @return array<string,ConfigItem>
     */
    public function findAll(): array
    {
        return collect(
            $this->createQueryBuilder()
                ->select(
                    'config_name',
                    'config_value',
                    'config_param1',
                    'config_param2'
                )
                ->from('config')
                ->execute()
                ->fetchAllAssociativeIndexed()
            )
            ->map(fn ($arr) => new ConfigItem(
                $arr['config_value'],
                $arr['config_param1'],
                $arr['config_param2']
            ))
            ->toArray();
    }

    public function save(string $name, ConfigItem $item): void
    {
        $this->getConnection()
            ->executeStatement(
                'INSERT INTO
                    config
                (
                    config_name,
                    config_value,
                    config_param1,
                    config_param2
                )
                VALUES
                (
                    :name,
                    :value,
                    :param1,
                    :param2
                )
                ON DUPLICATE KEY UPDATE
                    config_value = :value,
                    config_param1 = :param1,
                    config_param2 = :param2
                ;',
                [
                    'name' => $name,
                    'value' => $item->value,
                    'param1' => $item->param1,
                    'param2' => $item->param2,
                ]
            );
    }

    public function remove(string $name): void
    {
        $this->createQueryBuilder()
            ->delete('config')
            ->where('config_name = :name')
            ->setParameter('name', $name)
            ->execute();
    }

    public function truncate(): void
    {
        $this->getConnection()
            ->executeStatement("TRUNCATE TABLE config;");
    }
}

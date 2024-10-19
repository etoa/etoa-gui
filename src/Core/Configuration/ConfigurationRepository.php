<?php

declare(strict_types=1);

namespace EtoA\Core\Configuration;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Building\Building;
use EtoA\Core\AbstractRepository;

class ConfigurationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfigItem::class);
    }

    /**
     * @return array<string,ConfigItem>
     */
    public function findAll(): array
    {
        $data = $this->createQueryBuilder('q')
                ->select(
                    'config_name',
                    'config_value',
                    'config_param1',
                    'config_param2'
                )
                ->from('config')
                ->fetchAllAssociativeIndexed();

        return array_map(fn ($arr) => new ConfigItem(
            $arr['config_value'],
            $arr['config_param1'],
            $arr['config_param2']
        ), $data);
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
        $this->createQueryBuilder('q')
            ->delete('config')
            ->where('config_name = :name')
            ->setParameter('name', $name)
            ->executeQuery();
    }

    public function truncate(): void
    {
        $this->getConnection()
            ->executeStatement("TRUNCATE TABLE config;");
    }
}

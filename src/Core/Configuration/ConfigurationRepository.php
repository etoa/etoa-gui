<?php

declare(strict_types=1);

namespace EtoA\Core\Configuration;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Config;

class ConfigurationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Config::class);
    }

    /**
     * @return array<string,ConfigItem>
     */
    public function findAll(): array
    {
        $data = parent::findAll();
        return array_map(fn ($value) => new ConfigItem(
            $value->getValue(),
            $value->getParam1(),
            $value->getParam2()
        ), $data);
    }

    public function truncate(): void
    {
        $this->getConnection()
            ->executeStatement("TRUNCATE TABLE config;");
    }
}

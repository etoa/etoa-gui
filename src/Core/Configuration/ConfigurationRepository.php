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

    public function truncate(): void
    {
        $this->createQueryBuilder('q')
            ->delete();
    }
}

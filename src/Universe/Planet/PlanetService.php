<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Entity\EntityRepository;

class PlanetService
{
    private PlanetRepository $repository;
    private EntityRepository $entityRepo;
    private ConfigurationService $config;

    public function __construct(
        PlanetRepository $repository,
        EntityRepository $entityRepository,
        ConfigurationService $config
    ) {
        $this->repository = $repository;
        $this->entityRepository = $entityRepository;
        $this->config = $config;
    }
}

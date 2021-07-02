<?php

declare(strict_types=1);

namespace EtoA\Universe\Wormhole;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\EmptySpace\EmptySpaceRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;

class WormholeService
{
    private WormholeRepository $repository;
    private EntityRepository $entityRepository;
    private EmptySpaceRepository $emptySpaceRepo;
    private ConfigurationService $config;

    public function __construct(
        WormholeRepository $repository,
        EntityRepository $entityRepository,
        EmptySpaceRepository $emptySpaceRepo,
        ConfigurationService $config
    ) {
        $this->repository = $repository;
        $this->entityRepository = $entityRepository;
        $this->emptySpaceRepo = $emptySpaceRepo;
        $this->config = $config;
    }

    public function randomize(): void
    {
        $changedBefore = time() - $this->config->getInt('wh_update');
        $numberOfWormholesToChange = $this->config->param1Int('wh_update');

        /** @var int[] */
        $toBeDeleted = [];

        $wormholes = $this->repository->findNonPersistentInRandomOrder($changedBefore, $numberOfWormholesToChange);
        foreach ($wormholes as $wormhole) {
            if (!in_array($wormhole->id, $toBeDeleted, true)) {
                array_push($toBeDeleted, $wormhole->id, $wormhole->targetId);
            }
        }

        if (count($toBeDeleted) % 2 !== 0) {
            array_pop($toBeDeleted);
        }

        foreach ($toBeDeleted as $id) {
            $this->entityRepository->updateCode($id, EntityType::EMPTY_SPACE);
            $this->repository->remove($id);
            $this->emptySpaceRepo->add($id);
        }

        $emptySpaceEntities = $this->entityRepository->findRandomByCodes([EntityType::EMPTY_SPACE], count($toBeDeleted));
        for ($x = 0; $x < count($emptySpaceEntities); $x += 2) {
            $space1 = $emptySpaceEntities[$x];
            $space2 = $emptySpaceEntities[$x + 1];
            $this->entityRepository->updateCode($space1->id, EntityType::WORMHOLE);
            $this->entityRepository->updateCode($space2->id, EntityType::WORMHOLE);
            $this->emptySpaceRepo->remove($space1->id);
            $this->emptySpaceRepo->remove($space2->id);
            $this->repository->add($space1->id, false, $space2->id);
        }
    }
}

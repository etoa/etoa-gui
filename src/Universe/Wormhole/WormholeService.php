<?php

declare(strict_types=1);

namespace EtoA\Universe\Wormhole;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\EmptySpace\EmptySpaceRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;

class WormholeService
{
    public function __construct(
        private readonly WormholeRepository $repository,
        private readonly EntityRepository $entityRepository,
        private readonly EmptySpaceRepository $emptySpaceRepo,
        private readonly ConfigurationService $config
    ) {}

    public function randomize(): void
    {
        $changedBefore = time() - $this->config->getInt('wh_update');
        $numberOfWormholesToChange = $this->config->param1Int('wh_update');

        /** @var int[] */
        $toBeDeleted = [];

        $wormholes = $this->repository->findNonPersistentInRandomOrder($changedBefore, $numberOfWormholesToChange);
        foreach ($wormholes as $wormhole) {
            if (!in_array($wormhole->getEntity(), $toBeDeleted, true)) {
                array_push($toBeDeleted, $wormhole->getEntity(), $wormhole->getTarget()->getEntity());
            }
        }

        if (count($toBeDeleted) % 2 !== 0) {
            array_pop($toBeDeleted);
        }

        foreach ($toBeDeleted as $entity) {
            $this->repository->remove($entity->getType());
            $this->entityRepository->updateCode($entity, EntityType::EMPTY_SPACE);

            $this->emptySpaceRepo->add($entity);
        }

        $emptySpaceEntities = $this->entityRepository->findRandomByCodes([EntityType::EMPTY_SPACE], count($toBeDeleted));
        for ($x = 0; $x < count($emptySpaceEntities); $x += 2) {
            $space1 = $emptySpaceEntities[$x];
            $space2 = $emptySpaceEntities[$x + 1];
            $this->emptySpaceRepo->remove($space1->getEmptySpace());
            $this->emptySpaceRepo->remove($space2->getEmptySpace());

            $this->entityRepository->updateCode($space1, EntityType::WORMHOLE);
            $this->entityRepository->updateCode($space2, EntityType::WORMHOLE);
            $this->repository->add($space1, false, $space2->getType());
        }
    }
}

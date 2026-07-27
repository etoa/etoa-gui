<?php

namespace EtoA\Components\Core;

use EtoA\Bookmark\BookmarkRepository;
use EtoA\Controller\Game\AbstractGameController;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Entity;
use EtoA\Fleet\FleetLaunch;
use EtoA\Fleet\FleetLaunchService;
use EtoA\Fleet\FleetRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipTransformRepository;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\AbstractEntity;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\ResourceNames;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

/**
 * Target selection step of the fleet launch wizard (replaces the legacy xajax
 * havenShowTarget / havenTargetInfo / havenBookmark / havenAllianceAttack functions).
 * The coordinate/speed/favorite inputs are writable live props bound via data-model.
 */
#[AsLiveComponent(template: 'components/haven_target.html.twig', route: 'live_component_game')]
class HavenTarget extends AbstractGameController
{
    use DefaultActionTrait;

    // ----- user input (writable, bound via data-model) -----
    #[LiveProp(writable: true, onUpdated: 'updateByField')]
    public string $csx = '';
    #[LiveProp(writable: true, onUpdated: 'updateByField')]
    public string $csy = '';
    #[LiveProp(writable: true, onUpdated: 'updateByField')]
    public string $ccx = '';
    #[LiveProp(writable: true, onUpdated: 'updateByField')]
    public string $ccy = '';
    #[LiveProp(writable: true, onUpdated: 'updateByField')]
    public string $psp = '';

    #[LiveProp(writable: true, onUpdated: 'updateSpeedPercent')]
    public int $speedPercent = 100;

    #[LiveProp(writable: true, onUpdated: 'updateByFavorite')]
    public ?int $bookmark = null;

    #[LiveProp(writable: true, onUpdated: 'onAllianceAttackSelected')]
    public ?int $selectedAllianceAttackId = null;

    // ----- computed display values -----
    #[LiveProp]
    public string $costs = '';
    #[LiveProp]
    public string $food = '';
    #[LiveProp]
    public string $speed = '';
    #[LiveProp]
    public string $duration = '';
    #[LiveProp]
    public string $distance = '';
    #[LiveProp]
    public string $costsPerHundredAE = '';
    #[LiveProp]
    public bool $wormhole = false;
    #[LiveProp]
    public string $allianceComment = '';
    #[LiveProp]
    public ?Entity $targetEntity = null;

    public ?FleetLaunch $fleetLaunch = null;

    private bool $hydrated = false;

    public function __construct(
        private readonly ShipTransformRepository $shipTransformRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly ShipListRepository $shipListRepository,
        private readonly FleetLaunchService $fleetLaunchService,
        private readonly EntityRepository $entityRepository,
        private readonly BookmarkRepository $bookmarkRepository,
        private readonly RequestStack $requestStack,
        private readonly ConfigurationService $configurationService,
        private readonly UserUniverseDiscoveryService $userUniverseDiscoveryService,
        private readonly EntityService $entityService,
        private readonly SerializerInterface $serializer,
        private readonly FleetRepository $fleetRepository
    )
    {
    }

    #[PostMount]
    public function postMount(): void
    {
        $this->hydrate();

        // Restore the last chosen speed factor so the slider isn't reset to 100%
        $this->speedPercent = $this->fleetLaunch->getSpeedPercent();

        // A target which was requested by another page (e.g. a favourite) wins once
        if ($this->preselectRequestedTarget()) {
            return;
        }

        // Derive the initial target from the fleet. The deserialized target entity has no
        // usable id, so rebuild it from its (reliably serialized) cell coordinates.
        $target = $this->fleetLaunch->getTargetEntity() ?? $this->fleetLaunch->getSourceEntity()->getEntity();
        if ($target !== null && $target->getCell() !== null) {
            $entity = $this->entityRepository->findByCoordinates(new EntityCoordinates(
                $target->getCell()->getSx(),
                $target->getCell()->getSy(),
                $target->getCell()->getCx(),
                $target->getCell()->getCy(),
                $target->getPos()
            ));
            if ($entity !== null) {
                $this->fillCoordinates($entity);
                $this->updateValues($entity);
            }
        }
    }

    /**
     * Takes over a target which was requested by another page (link with ?target=...,
     * e.g. the "send fleet" action of a favourite). The request is consumed so a later
     * target selection is not overruled.
     */
    private function preselectRequestedTarget(): bool
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        $requestedTargetId = (int) $session->get('havenTarget', 0);
        if ($requestedTargetId <= 0) {
            return false;
        }

        $session->remove('havenTarget');

        $entity = $this->entityRepository->find($requestedTargetId);
        if ($entity === null || $entity->getCell() === null) {
            return false;
        }

        $this->fillCoordinates($entity);
        $this->updateValues($entity);

        return true;
    }

    #[LiveAction]
    public function updateByField(): void
    {
        $this->hydrate();

        if ((int) $this->csx > 0 && (int) $this->csy > 0 && (int) $this->ccx > 0 && (int) $this->ccy > 0 && (int) $this->psp >= 0) {
            $entity = $this->entityRepository->findByCoordinates(new EntityCoordinates(
                (int) $this->csx,
                (int) $this->csy,
                (int) $this->ccx,
                (int) $this->ccy,
                (int) $this->psp
            ));
            if ($entity !== null) {
                $this->updateValues($entity);
            }
        }
    }

    #[LiveAction]
    public function updateByFavorite(): void
    {
        $this->hydrate();

        if ($this->bookmark) {
            $entity = $this->entityRepository->find($this->bookmark);
            if ($entity !== null) {
                // Move the coordinate inputs to the selected favorite
                $this->fillCoordinates($entity);
                $this->updateValues($entity);
            }
        }
    }

    #[LiveAction]
    public function updateSpeedPercent(): void
    {
        $this->hydrate();

        if ($this->targetEntity !== null) {
            $this->updateValues($this->targetEntity);
        }
    }

    private function fillCoordinates(Entity $entity): void
    {
        $this->csx = (string) $entity->getCell()->getSx();
        $this->csy = (string) $entity->getCell()->getSy();
        $this->ccx = (string) $entity->getCell()->getCx();
        $this->ccy = (string) $entity->getCell()->getCy();
        $this->psp = (string) $entity->getPos();
    }

    private function updateValues(Entity $entity): void
    {
        $this->hydrate();

        $this->wormhole = false;

        $absX = (($entity->getCell()->getSx() - 1) * $this->configurationService->param1Int('num_of_cells')) + $entity->getCell()->getCx();
        $absY = (($entity->getCell()->getSy() - 1) * $this->configurationService->param2Int('num_of_cells')) + $entity->getCell()->getCy();

        $owner = $this->fleetLaunch->getOwner();
        $code = $this->userUniverseDiscoveryService->discovered($owner, $absX, $absY) === false ? 'u' : '';

        if (!($code == 'u' && $entity->getPos() > 0)) {
            $this->setTarget($entity, $this->speedPercent);
            $this->fleetLaunch->setLeader(null);
            $this->targetEntity = $entity;
            $this->refreshDisplay();

            // Offer the wormhole jump when the target is a wormhole that has not been set yet
            if ($entity->getCode() == 'w' && !$this->fleetLaunch->getWormholeEntryEntity() && $this->fleetLaunch->isWormholeEnable()) {
                $this->wormhole = true;
            }
        } else {
            $this->fleetLaunch->setTargetEntity(null);
            $this->targetEntity = null;
            $this->distance = 'Unbekannt';
        }
    }

    #[LiveAction]
    public function chooseAction(): RedirectResponse
    {
        $this->hydrate();

        // Use the actually selected target (a managed live prop), not the stale default.
        if ($this->targetEntity !== null) {
            $this->fleetLaunchService->setFleetLaunch($this->fleetLaunch);

            // Keep the alliance leader and its computed speed if one was joined,
            // otherwise use the speed from the dropdown.
            $speedPercent = $this->fleetLaunch->getLeader() !== null
                ? $this->fleetLaunch->getSpeedPercent()
                : $this->speedPercent;

            $this->setTarget($this->targetEntity, $speedPercent);
            $this->saveToSession();

            return $this->redirectToRoute('game.haven.action');
        }

        return $this->redirectToRoute('game.haven.target');
    }

    /**
     * Sets the wormhole entry/exit for a two-legged flight and keeps the user on the
     * target selection so the final destination (after the jump) can be chosen.
     */
    #[LiveAction]
    public function chooseWormhole(): void
    {
        $this->hydrate();

        if ($this->targetEntity !== null) {
            $this->fleetLaunchService->setFleetLaunch($this->fleetLaunch);
            if ($this->fleetLaunchService->setWormhole($this->targetEntity, $this->speedPercent)) {
                $this->saveToSession();
                $this->allianceComment = 'Wurmloch gesetzt. Wähle nun das Ziel nach dem Wurmlochsprung aus.';
            } else {
                $this->allianceComment = $this->fleetLaunch->getError() ?: 'Wurmloch konnte nicht gesetzt werden.';
            }
        }

        // Refresh the target info for the new (wormhole exit -> target) leg
        $this->updateByField();
    }

    #[LiveAction]
    public function reset(): RedirectResponse
    {
        $this->requestStack->getCurrentRequest()->getSession()->remove('fleetLaunch');

        return $this->redirectToRoute('game.haven.show');
    }

    /**
     * @return array<int, array{id: int, label: string}>
     */
    public function getBookmarkChoices(): array
    {
        $this->hydrate();

        $choices = [];
        foreach ($this->planetRepository->findBy(['user' => $this->fleetLaunch->getOwner()]) as $planet) {
            if ($planet->getEntity() !== null) {
                $choices[] = ['id' => $planet->getEntity()->getId(), 'label' => 'Eigener Planet: ' . $planet->getEntity()->toString()];
            }
        }
        foreach ($this->bookmarkRepository->findBy(['user' => $this->fleetLaunch->getOwner()]) as $bookmark) {
            if ($bookmark->getEntity() !== null) {
                $choices[] = ['id' => $bookmark->getEntity()->getId(), 'label' => $bookmark->getEntity()->toString()];
            }
        }

        return $choices;
    }

    /**
     * The pending alliance attacks (leader fleets) that target the currently selected entity.
     * The serialized aFleets are not real Fleet entities, so they are reloaded fresh from the DB.
     *
     * @return \EtoA\Entity\Fleet[]
     */
    public function getAllianceAttacks(): array
    {
        if ($this->targetEntity === null || $this->targetEntity->getOwner() === null) {
            return [];
        }

        $this->hydrate();
        $this->fleetLaunchService->setFleetLaunch($this->fleetLaunch);
        $this->fleetLaunchService->loadAllianceFleets();

        $attacks = [];
        foreach ($this->fleetLaunch->getAFleets() as $f) {
            if ($f->getEntityTo() !== null && $f->getEntityTo()->getId() === $this->targetEntity->getId()) {
                $attacks[] = $f;
            }
        }

        return $attacks;
    }

    /**
     * Join (or leave) the selected alliance attack and adjust the speed so the fleet
     * arrives together with the leader (migrated from the legacy havenAllianceAttack).
     */
    public function onAllianceAttackSelected(): void
    {
        // Resolve the current target first (also puts the fleet into the service)
        $this->updateByField();

        if ($this->fleetLaunch === null) {
            return;
        }

        $id = $this->selectedAllianceAttackId;
        $this->allianceComment = '-';
        $this->fleetLaunch->setSpeedPercent($this->speedPercent);

        $this->fleetLaunchService->setFleetLaunch($this->fleetLaunch);
        $this->fleetLaunchService->loadAllianceFleets();

        if ($id !== null && $id > 0 && $this->fleetLaunch->getLeader() !== $id) {
            $leaderFleet = $this->fleetRepository->find($id);
            if ($leaderFleet !== null) {
                $sourceAllianceId = $this->fleetLaunch->getSourceEntity()->getUser()?->getAlliance()?->getId();
                if ($leaderFleet->getNextId() === $sourceAllianceId) {
                    if ($this->fleetLaunchService->checkAttNum($id)) {
                        $leaderCount = $this->fleetRepository->countLeaderFleets($id);
                        if ($leaderCount <= $this->fleetLaunch->getAllianceSlots()) {
                            $duration = $this->fleetLaunch->getSpeed() > 0
                                ? ceil($this->fleetLaunch->getDistance() / $this->fleetLaunch->getSpeed() * 3600)
                                : 0;
                            $maxTime = $leaderFleet->getLandTime() - time() - $this->fleetLaunch->getTimeLaunchLand() - $this->fleetLaunch->getDuration1() - 120;
                            if ($maxTime > 0 && $duration < $maxTime) {
                                $this->fleetLaunch->setSpeedPercent((int) ceil(100 * $duration / $maxTime));
                                $this->fleetLaunch->setLeader($id);
                                $this->allianceComment = "Unterstützung des Allianzangriffes mit Ankunft: " . date("d.m.y, H:i:s", $leaderFleet->getLandTime());
                            } else {
                                $this->allianceComment = "Der gewählte Angriff kann nicht mehr erreicht werden.";
                            }
                        } else {
                            $this->allianceComment = "Am gewählten Angriff kann nicht teilgenommen werden, da die Flottenkontrolle keine weiteren Teilflotten unterstützt.";
                        }
                    } else {
                        $this->allianceComment = "Am gewählten Angriff kann nicht teilgenommen werden, da die Anzahl Angreifer limitiert ist.";
                    }
                } else {
                    $this->allianceComment = "Der gewählte Angriff gehört nicht zu unserem Imperium.";
                }
            }
        } elseif ($this->fleetLaunch->getLeader() === $id) {
            $this->fleetLaunch->setLeader(null);
        }

        $this->refreshDisplay();
        $this->saveToSession();
    }

    /**
     * Loads the fleet from the session (once per request) and replaces the deserialized
     * source planet + owner with managed entities, so nothing reads uninitialized fields
     * or queries with a detached user (e.g. the bookmark dropdown / discovery check).
     */
    private function hydrate(): void
    {
        if ($this->hydrated) {
            return;
        }

        if (!$this->fleetLaunch) {
            $request = $this->requestStack->getCurrentRequest();
            $this->fleetLaunch = $this->serializer->deserialize($request->getSession()->get('fleetLaunch'), FleetLaunch::class, 'json', [
                'allow_extra_attributes' => true,
            ]);
        }

        $this->fleetLaunch->setSourceEntity(
            $this->planetRepository->find($this->requestStack->getCurrentRequest()->getSession()->get('cpid'))
        );
        if ($this->fleetLaunch->getSourceEntity()?->getUser() !== null) {
            $this->fleetLaunch->setOwner($this->fleetLaunch->getSourceEntity()->getUser());
        }

        // getSpeed()/getCostsPerHundredAE() are computed getters but their setters store raw
        // values, so the serializer re-applies the speed factor on every round-trip. Recompute
        // the raw values from the ships array (which survives serialization) to undo that.
        $ships = $this->fleetLaunch->getShips();
        if (!empty($ships)) {
            $this->fleetLaunch->setSpeed(min(array_map(static fn ($s) => $s['speed'], $ships)));
            $this->fleetLaunch->setCostsPerHundredAE(array_sum(array_map(static fn ($s) => $s['costs_per_ae'] ?? 0, $ships)));
        }

        $this->hydrated = true;
    }

    private function refreshDisplay(): void
    {
        $this->costs = StringUtils::formatNumber($this->fleetLaunch->getCosts()) . " t " . ResourceNames::FUEL;
        $this->distance = StringUtils::formatNumber($this->fleetLaunch->getDistance()) . " AE";
        $this->duration = StringUtils::formatTimespan($this->fleetLaunch->getDuration());
        $this->speed = StringUtils::formatNumber($this->fleetLaunch->getSpeed()) . " AE/h";
        $this->costsPerHundredAE = StringUtils::formatNumber($this->fleetLaunch->getCostsPerHundredAE()) . " t " . ResourceNames::FUEL;
        $this->food = StringUtils::formatNumber($this->fleetLaunchService->getCostsFood()) . " t " . ResourceNames::FOOD;
    }

    private function setTarget(Entity $ent, $speedPercent = 100): bool
    {
        if ($this->fleetLaunch->isShipsFixed()) {
            $this->fleetLaunch->setTargetEntity($ent);
            if ($this->fleetLaunch->getWormholeEntryEntity()) {
                $this->fleetLaunch->setDistance($this->entityService->distanceByCoords($this->fleetLaunch->getWormholeExitEntity()->getCoordinates(), $this->fleetLaunch->getTargetEntity()->getCoordinates()));
                $this->fleetLaunch->setDistance1($this->entityService->distanceByCoords($this->fleetLaunch->getSourceEntity()->getEntity()->getCoordinates(), $this->fleetLaunch->getWormholeEntryEntity()->getCoordinates()));
            } else {
                $this->fleetLaunch->setDistance($this->entityService->distanceByCoords($this->fleetLaunch->getSourceEntity()->getEntity()->getCoordinates(), $this->fleetLaunch->getTargetEntity()->getCoordinates()));
                $this->fleetLaunch->setDistance1(0);
            }

            $this->fleetLaunch->setSpeedPercent($speedPercent);

            return true;
        }
        return false;
    }

    private function saveToSession(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $request->getSession()->set('fleetLaunch', $this->serializer->serialize($this->fleetLaunch, 'json', [
            'circular_reference_handler' => function ($object) {
                if (is_a($object, AbstractEntity::class)) {
                    return $object->getEntity()->getId();
                }
                return $object->getId();
            },
            'ignored_attributes' => ['__initializer__', '__cloner__', '__isInitialized__', 'lazyObjectState', 'lazyObjectInitialized', 'lazyObjectAsInitialized'],
            'skip_null_values' => true,
        ]));
    }
}
